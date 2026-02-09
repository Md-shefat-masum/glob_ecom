<?php

return [
    'python_binary' => env('PYTHON_BINARY', 'python3'),
    'prediction_enabled' => env('ANALYTICS_PREDICTION_ENABLED', true),
    'low_stock_threshold' => env('ANALYTICS_LOW_STOCK_THRESHOLD', 5),
];

