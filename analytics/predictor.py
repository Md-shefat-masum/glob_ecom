import argparse
import json
import math
from datetime import datetime, timedelta
from pathlib import Path
from typing import Dict, List, Tuple

import numpy as np
import pandas as pd

try:
    from sklearn.ensemble import RandomForestRegressor
    from sklearn.model_selection import train_test_split
    from sklearn.metrics import r2_score, mean_absolute_error
    SKLEARN_AVAILABLE = True
except ImportError:
    SKLEARN_AVAILABLE = False

MODEL_VERSION = "forecast-v1"


def load_payload(path: Path) -> Dict:
    with path.open("r", encoding="utf-8") as fp:
        return json.load(fp)


def save_payload(path: Path, payload: Dict) -> None:
    with path.open("w", encoding="utf-8") as fp:
        json.dump(payload, fp, indent=2, default=str)


def build_dataframe(payload: Dict) -> Tuple[pd.DataFrame, pd.DataFrame]:
    records = []
    meta_rows = []
    for product in payload.get("products", []):
        product_id = product.get("product_id")
        name = product.get("name")
        current_stock = product.get("current_stock", 0)
        price = product.get("price", 0)
        discount_price = product.get("discount_price", 0) or price

        history = product.get("history", [])
        for entry in history:
            records.append(
                {
                    "product_id": product_id,
                    "date": entry["date"],
                    "sales_qty": entry.get("sales_qty", 0.0),
                    "sales_revenue": entry.get("sales_revenue", 0.0),
                    "orders_count": entry.get("orders_count", 0),
                    "returns_qty": entry.get("returns_qty", 0.0),
                    "site_visitors": entry.get("site_visitors", 0),
                    "conversion_rate": entry.get("conversion_rate", 0.0),
                }
            )

        meta_rows.append(
            {
                "product_id": product_id,
                "name": name,
                "current_stock": current_stock,
                "price": price,
                "discount_price": discount_price,
            }
        )

    if not records:
        raise ValueError("No historical records supplied.")

    df = pd.DataFrame(records)
    df["date"] = pd.to_datetime(df["date"])
    df = df.sort_values(["product_id", "date"])

    meta_df = pd.DataFrame(meta_rows).drop_duplicates("product_id")
    return df, meta_df


def engineer_features(df: pd.DataFrame) -> pd.DataFrame:
    df = df.copy()
    df["day_of_week"] = df["date"].dt.dayofweek
    df["week_of_year"] = df["date"].dt.isocalendar().week.astype(int)
    df["month"] = df["date"].dt.month
    df["trend_index"] = df.groupby("product_id").cumcount()

    for window in (3, 7, 14, 30):
        df[f"rolling_mean_{window}"] = (
            df.groupby("product_id")["sales_qty"].transform(lambda s: s.rolling(window, min_periods=1).mean())
        )
        df[f"rolling_std_{window}"] = (
            df.groupby("product_id")["sales_qty"].transform(lambda s: s.rolling(window, min_periods=1).std().fillna(0))
        )

    df["returns_ratio"] = df.apply(
        lambda row: row["returns_qty"] / row["sales_qty"] if row["sales_qty"] > 0 else 0, axis=1
    )

    for lag in (1, 2, 3, 7, 14):
        df[f"lag_{lag}"] = df.groupby("product_id")["sales_qty"].shift(lag).fillna(0)

    df["conversion_rate"] = df["conversion_rate"].fillna(0)
    df["site_visitors"] = df["site_visitors"].fillna(0)
    df["orders_count"] = df["orders_count"].fillna(0)

    return df


def train_model(dataset: pd.DataFrame):
    features = [
        col
        for col in dataset.columns
        if col
        not in (
            "sales_qty",
            "date",
            "product_id",
        )
    ]

    X = dataset[features].values
    y = dataset["sales_qty"].values

    if not SKLEARN_AVAILABLE or len(dataset) < 50:
        # Fallback heuristic model
        return {"type": "heuristic", "features": features, "mean": float(np.mean(y) if len(y) else 0)}

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, shuffle=True, random_state=42)

    model = RandomForestRegressor(
        n_estimators=200,
        random_state=42,
        n_jobs=-1,
        max_depth=10,
        min_samples_split=3,
    )
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    r2 = r2_score(y_test, y_pred) if len(y_test) > 0 else None
    mae = mean_absolute_error(y_test, y_pred) if len(y_test) > 0 else None

    feature_importance = []
    for name, importance in zip(features, model.feature_importances_):
        feature_importance.append({"feature": name, "importance": float(round(importance, 4))})

    return {
        "type": "random_forest",
        "model": model,
        "features": features,
        "feature_importance": feature_importance,
        "r2": float(round(r2, 4)) if r2 is not None else None,
        "mae": float(round(mae, 4)) if mae is not None else None,
    }


def _predict_with_model(model_payload, feature_row: np.ndarray) -> float:
    if model_payload["type"] == "heuristic":
        # simple moving average fallback
        return float(model_payload["mean"])
    return float(model_payload["model"].predict(feature_row.reshape(1, -1))[0])


def predict_demand(model_payload, dataset: pd.DataFrame, meta_df: pd.DataFrame) -> List[Dict]:
    predictions = []
    features = model_payload["features"]

    future_horizon_days = 7  # forecast one week ahead

    for product_id, history in dataset.groupby("product_id"):
        history = history.sort_values("date")
        latest_row = history.iloc[-1].copy()
        future_date = latest_row["date"] + timedelta(days=future_horizon_days)

        # Shift temporal features for the prediction horizon
        latest_row["day_of_week"] = future_date.dayofweek
        latest_row["week_of_year"] = future_date.isocalendar().week
        latest_row["month"] = future_date.month
        latest_row["trend_index"] = latest_row["trend_index"] + future_horizon_days

        feature_vector = latest_row[features].to_numpy()
        predicted_value = max(_predict_with_model(model_payload, feature_vector), 0)

        trailing_window = history.tail(30)
        trailing_mean = trailing_window["sales_qty"].mean() if len(trailing_window) else 0

        growth_pct = 0.0
        if trailing_mean > 0:
            growth_pct = ((predicted_value - trailing_mean) / trailing_mean) * 100

        direction = "flat"
        if growth_pct > 5:
            direction = "up"
        elif growth_pct < -5:
            direction = "down"

        meta = meta_df[meta_df["product_id"] == product_id].iloc[0]
        current_stock = meta["current_stock"]

        restock_recommended = predicted_value > max(current_stock, 0)

        reason_parts = []
        if growth_pct > 15:
            reason_parts.append("Demand trending upward")
        if restock_recommended:
            reason_parts.append("Predicted demand exceeds stock")
        if latest_row.get("site_visitors", 0) > 0 and latest_row.get("conversion_rate", 0) > 0.02:
            reason_parts.append("Healthy conversion rate")
        if not reason_parts:
            reason_parts.append("Stable demand pattern")

        predictions.append(
            {
                "product_id": int(product_id),
                "predicted_demand": round(predicted_value, 3),
                "predicted_growth_pct": round(growth_pct, 2),
                "trend_direction": direction,
                "confidence": float(model_payload.get("r2") or 0.7),
                "restock_recommended": bool(restock_recommended),
                "reason": "; ".join(reason_parts),
                "meta": {
                    "current_stock": float(current_stock),
                    "trailing_mean": float(round(trailing_mean, 3)),
                    "latest_visitors": float(latest_row.get("site_visitors", 0)),
                    "latest_conversion": float(round(latest_row.get("conversion_rate", 0), 4)),
                },
            }
        )

    return predictions


def detect_anomalies(dataset: pd.DataFrame) -> List[Dict]:
    anomalies = []
    for product_id, history in dataset.groupby("product_id"):
        z_scores = history["sales_qty"].rolling(14, min_periods=10).apply(
            lambda x: (x.iloc[-1] - np.mean(x)) / (np.std(x) if np.std(x) else 1), raw=False
        )
        latest_z = z_scores.iloc[-1]
        if pd.notna(latest_z) and abs(latest_z) >= 3:
            anomalies.append(
                {
                    "product_id": int(product_id),
                    "date": history.iloc[-1]["date"].strftime("%Y-%m-%d"),
                    "z_score": float(round(latest_z, 2)),
                    "direction": "spike" if latest_z > 0 else "drop",
                }
            )
    return anomalies


def recommend_products(predictions: List[Dict]) -> List[Dict]:
    sorted_predictions = sorted(
        predictions,
        key=lambda x: (x["restock_recommended"], x["predicted_growth_pct"], x["predicted_demand"]),
        reverse=True,
    )
    return sorted_predictions


def main(input_path: Path, output_path: Path) -> None:
    payload = load_payload(input_path)
    df, meta_df = build_dataframe(payload)
    engineered = engineer_features(df)

    model_payload = train_model(engineered)

    predictions = predict_demand(model_payload, engineered, meta_df)
    predictions = recommend_products(predictions)

    feature_importance = model_payload.get("feature_importance", [])
    anomalies = detect_anomalies(engineered)

    output = {
        "generated_at": datetime.utcnow().isoformat(),
        "predicted_for": (datetime.utcnow() + timedelta(days=7)).date().isoformat(),
        "model_version": MODEL_VERSION,
        "model_summary": {
            "type": model_payload["type"],
            "r2": model_payload.get("r2"),
            "mae": model_payload.get("mae"),
            "training_rows": int(len(engineered)),
        },
        "predictions": predictions,
        "feature_importance": feature_importance,
        "anomalies": anomalies,
    }

    save_payload(output_path, output)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Predict product demand using historical sales data.")
    parser.add_argument("--input", required=True, help="Path to input JSON payload")
    parser.add_argument("--output", required=True, help="Path to write predictions JSON")
    args = parser.parse_args()

    main(Path(args.input), Path(args.output))

