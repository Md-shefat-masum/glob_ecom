<?php

namespace App\Http\Controllers;

use App\Models\BillingAddress;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderPayment;
use App\Models\OrderProgress;
use App\Models\Product;
use App\Models\ShippingInfo;
use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function viewAllOrders(Request $request)
    {
        if ($request->ajax()) {

            // $data = DB::table('orders')
            //     ->leftJoin('shipping_infos', 'shipping_infos.order_id', '=', 'orders.id')
            //     ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            //     ->select('orders.*', 'shipping_infos.full_name as customer_name', 'shipping_infos.email as customer_email', 'shipping_infos.phone as customer_phone', 'order_details.qty as quantity')
            //     ->orderBy('id', 'desc')
            //     ->whereNull('orders.deleted_at')
            //     ->get();

            $qtySub = DB::table('order_details')
                ->select('order_id', DB::raw('SUM(qty) as total_qty'))
                ->groupBy('order_id');

            // Subquery to get latest shipping_info per order
            $shippingSub = DB::table('shipping_infos as si1')
                ->select('si1.*')
                ->whereRaw('si1.id = (
                    SELECT MAX(si2.id)
                    FROM shipping_infos si2
                    WHERE si2.order_id = si1.order_id
                )');

            $data = DB::table('orders')
                ->leftJoinSub($shippingSub, 'shipping_infos', function ($join) {
                    $join->on('shipping_infos.order_id', '=', 'orders.id');
                })
                ->leftJoinSub($qtySub, 'order_qty', function ($join) {
                    $join->on('order_qty.order_id', '=', 'orders.id');
                })
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.email as customer_email',
                    'shipping_infos.phone as customer_phone',
                    'order_qty.total_qty as quantity'
                )
                ->whereNull('orders.deleted_at')
                ->orderByDesc('orders.id')
                ->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';
                    // $btn .= ' <a href="'.url('edit/place/order').'/'.$data->slug.'" title="Edit" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-pencil-alt"></i></a>';

                    if ($data->order_status == 0) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Cancel" data-id="' . $data->slug . '" data-original-title="Cancel" class="d-inline-block btn-sm btn-danger rounded cancelBtn"><i class="fa fa-times"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve" data-id="' . $data->slug . '" data-original-title="Check" class="d-inline-block btn-sm btn-success rounded approveBtn"><i class="fas fa-check"></i></a>';
                    }

                    if ($data->order_status == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Cancel" data-id="' . $data->slug . '" data-original-title="Cancel" class="d-inline-block btn-sm btn-danger rounded cancelBtn"><i class="fa fa-times"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve" data-id="'.$data->slug.'" data-original-title="Check" class="d-inline-block btn-sm btn-success rounded intransitBtn"><i class="fas fa-check"></i></a>';
                    }

                    if ($data->order_status == 2) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Deliver" data-id="' . $data->slug . '" data-original-title="Delivery" class="d-inline-block btn-sm btn-success rounded deliveryBtn"><i class="fas fa-truck"></i></a>';
                    }

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.all');
    }

    public function viewPendigOrders(Request $request)
    {
        if ($request->ajax()) {

            // $data = Order::where('order_status', 0)->orderBy('id', 'desc')->get();
            // $data = DB::table('orders')
            //     ->leftJoin('shipping_infos', 'shipping_infos.order_id', '=', 'orders.id')
            //     ->select('orders.*', 'shipping_infos.full_name as customer_name', 'shipping_infos.email as customer_email', 'shipping_infos.phone as customer_phone')
            //     ->where('order_status', 0)
            //     ->orderBy('id', 'desc')
            //     ->get();

            $qtySub = DB::table('order_details')
                ->select('order_id', DB::raw('SUM(qty) as total_qty'))
                ->groupBy('order_id');

            // Subquery to get latest shipping_info per order
            $shippingSub = DB::table('shipping_infos as si1')
                ->select('si1.*')
                ->whereRaw('si1.id = (
                SELECT MAX(si2.id)
                FROM shipping_infos si2
                WHERE si2.order_id = si1.order_id
            )');

            $data = DB::table('orders')
                ->leftJoinSub($shippingSub, 'shipping_infos', function ($join) {
                    $join->on('shipping_infos.order_id', '=', 'orders.id');
                })
                ->leftJoinSub($qtySub, 'order_qty', function ($join) {
                    $join->on('order_qty.order_id', '=', 'orders.id');
                })
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.email as customer_email',
                    'shipping_infos.phone as customer_phone',
                    'order_qty.total_qty as quantity'
                )
                ->where('order_status', 0)
                ->whereNull('orders.deleted_at')
                ->orderByDesc('orders.id')
                ->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/edit') . '/' . $data->slug . '" onclick="return orderEditWarning()" title="Order Edit" class="mb-1 d-inline-block btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="mb-1 d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Cancel" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded cancelBtn"><i class="fa fa-times"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve" data-id="' . $data->slug . '" data-original-title="Check" class="d-inline-block btn-sm btn-success rounded approveBtn"><i class="fas fa-check"></i></a>';
                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.pending');
    }

    public function viewAllTrashedOrders(Request $request)
    {
        if ($request->ajax()) {

            $data = Order::onlyTrashed()
                ->leftJoin('shipping_infos', 'shipping_infos.order_id', '=', 'orders.id')
                ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.email as customer_email',
                    'shipping_infos.phone as customer_phone',
                    'order_details.qty as quantity'
                )
                ->orderByDesc('orders.id')
                ->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $btn = '';

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-undo"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.trashed');
    }
    public function viewAllDispatchOrders(Request $request)
    {
        if ($request->ajax()) {

            // $data = Order::where('order_status', 0)->orderBy('id', 'desc')->get();
            // $data = DB::table('orders')
            //     ->leftJoin('shipping_infos', 'shipping_infos.order_id', '=', 'orders.id')
            //     ->select('orders.*', 'shipping_infos.full_name as customer_name', 'shipping_infos.email as customer_email', 'shipping_infos.phone as customer_phone')
            //     ->where('order_status', 2)
            //     ->orderBy('id', 'desc')
            //     ->get();

             $qtySub = DB::table('order_details')
                ->select('order_id', DB::raw('SUM(qty) as total_qty'))
                ->groupBy('order_id');

            // Subquery to get latest shipping_info per order
            $shippingSub = DB::table('shipping_infos as si1')
                ->select('si1.*')
                ->whereRaw('si1.id = (
                SELECT MAX(si2.id)
                FROM shipping_infos si2
                WHERE si2.order_id = si1.order_id
            )');

            $data = DB::table('orders')
                ->leftJoinSub($shippingSub, 'shipping_infos', function ($join) {
                    $join->on('shipping_infos.order_id', '=', 'orders.id');
                })
                ->leftJoinSub($qtySub, 'order_qty', function ($join) {
                    $join->on('order_qty.order_id', '=', 'orders.id');
                })
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.email as customer_email',
                    'shipping_infos.phone as customer_phone',
                    'order_qty.total_qty as quantity'
                )
                ->where('order_status', 2)
                ->whereNull('orders.deleted_at')
                ->orderByDesc('orders.id')
                ->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '';
                    $btn .= ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="mb-1 d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';
                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.dispatch');
    }

    public function viewApprovedOrders(Request $request)
    {
        if ($request->ajax()) {

            $data = Order::where('order_status', 1)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('sub_total', function ($data) {
                    return "৳ " . number_format($data->sub_total, 2);
                })
                ->editColumn('discount', function ($data) {
                    return "৳ " . number_format($data->discount, 2);
                })
                ->editColumn('delivery_fee', function ($data) {
                    return "৳ " . number_format($data->delivery_fee, 2);
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';

                    if ($data->order_status == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Cancel" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded cancelBtn"><i class="fa fa-times"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-success rounded intransitBtn"><i class="fas fa-check"></i></a>';
                    }
                    if ($data->order_status == 2) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Deliver" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-success rounded deliveryBtn"><i class="fas fa-truck"></i></a>';
                    }

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.approved');
    }

    public function viewIntransitOrders(Request $request)
    {
        if ($request->ajax()) {

            $data = Order::where('order_status', 3)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('sub_total', function ($data) {
                    return "৳ " . number_format($data->sub_total, 2);
                })
                ->editColumn('discount', function ($data) {
                    return "৳ " . number_format($data->discount, 2);
                })
                ->editColumn('delivery_fee', function ($data) {
                    return "৳ " . number_format($data->delivery_fee, 2);
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';

                    if ($data->order_status == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Cancel" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded cancelBtn"><i class="fa fa-times"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-success rounded intransitBtn"><i class="fas fa-check"></i></a>';
                    }
                    if ($data->order_status == 2) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Deliver" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-success rounded deliveryBtn"><i class="fas fa-truck"></i></a>';
                    }

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.intransit');
    }

    public function viewDeliveredOrders(Request $request)
    {
        if ($request->ajax()) {

            // $data = Order::where('order_status', 4)->orderBy('id', 'desc')->get();

             $qtySub = DB::table('order_details')
                ->select('order_id', DB::raw('SUM(qty) as total_qty'))
                ->groupBy('order_id');

            // Subquery to get latest shipping_info per order
            $shippingSub = DB::table('shipping_infos as si1')
                ->select('si1.*')
                ->whereRaw('si1.id = (
                SELECT MAX(si2.id)
                FROM shipping_infos si2
                WHERE si2.order_id = si1.order_id
            )');

            $data = DB::table('orders')
                ->leftJoinSub($shippingSub, 'shipping_infos', function ($join) {
                    $join->on('shipping_infos.order_id', '=', 'orders.id');
                })
                ->leftJoinSub($qtySub, 'order_qty', function ($join) {
                    $join->on('order_qty.order_id', '=', 'orders.id');
                })
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.email as customer_email',
                    'shipping_infos.phone as customer_phone',
                    'order_qty.total_qty as quantity'
                )
                ->where('order_status', 4)
                ->whereNull('orders.deleted_at')
                ->orderByDesc('orders.id')
                ->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('sub_total', function ($data) {
                    return "৳ " . number_format($data->sub_total, 2);
                })
                ->editColumn('discount', function ($data) {
                    return "৳ " . number_format($data->discount, 2);
                })
                ->editColumn('delivery_fee', function ($data) {
                    return "৳ " . number_format($data->delivery_fee, 2);
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.delivered');
    }

    public function viewCancelledOrders(Request $request)
    {
        if ($request->ajax()) {

            $data = Order::where('order_status', 6)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('sub_total', function ($data) {
                    return "৳ " . number_format($data->sub_total, 2);
                })
                ->editColumn('discount', function ($data) {
                    return "৳ " . number_format($data->discount, 2);
                })
                ->editColumn('delivery_fee', function ($data) {
                    return "৳ " . number_format($data->delivery_fee, 2);
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.cancelled');
    }

    public function viewPickedOrders(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::where('order_status', 5)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('order_status', function ($data) {
                    if ($data->order_status == 0) {
                        return '<span class="alert alert-warning" style="padding: 2px 10px !important;">Pending</span>';
                    } elseif ($data->order_status == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">Approved</span>';
                    } elseif ($data->order_status == 2) {
                        return '<span class="alert alert-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                    } elseif ($data->order_status == 3) {
                        return '<span class="alert alert-secondary" style="padding: 2px 10px !important;">Intransit</span>';
                    } elseif ($data->order_status == 4) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Delivered</span>';
                    } elseif ($data->order_status == 5) {
                        return '<span class="alert alert-dark" style="padding: 2px 10px !important;">Return</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                    }
                })
                ->editColumn('payment_method', function ($data) {
                    if ($data->payment_method == NULL) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_method == 1) {
                        return '<span class="alert alert-info" style="padding: 2px 10px !important;">COD</span>';
                    } elseif ($data->payment_method == 2) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">bKash</span>';
                    } elseif ($data->payment_method == 3) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Nagad</span>';
                    } else {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Card</span>';
                    }
                })
                ->editColumn('payment_status', function ($data) {
                    if ($data->payment_status == 0) {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                    } elseif ($data->payment_status == 1) {
                        return '<span class="alert alert-success" style="padding: 2px 10px !important;">Paid</span>';
                    } else {
                        return '<span class="alert alert-danger" style="padding: 2px 10px !important;">Failed</span>';
                    }
                })
                ->editColumn('sub_total', function ($data) {
                    return "৳ " . number_format($data->sub_total, 2);
                })
                ->editColumn('discount', function ($data) {
                    return "৳ " . number_format($data->discount, 2);
                })
                ->editColumn('delivery_fee', function ($data) {
                    return "৳ " . number_format($data->delivery_fee, 2);
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('order/details') . '/' . $data->slug . '" title="Order Details" class="d-inline-block btn-sm btn-info rounded"><i class="fas fa-list-ul"></i></a>';

                    if (Auth::user()->user_type == 1) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="d-inline-block btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'order_status', 'payment_method', 'payment_status'])
                ->make(true);
        }
        return view('backend.orders.picked');
    }

    public function orderDetails($slug)
    {
        $order = Order::where(function($q) use($slug){
                $q->where('slug', $slug);
                $q->orWhere('id', $slug);
            })
            ->with('orderDeliveryMen')
            ->first();
            
        if(!$order) {
            return response()->json('no order found');
        }
        

        $userInfo = User::where('id', $order->user_id)->first();
        $shippingInfo = ShippingInfo::where('order_id', $order->id)->first();
        $billingAddress = BillingAddress::where('order_id', $order->id)->first();
        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('units', 'order_details.unit_id', '=', 'units.id')
            ->leftJoin('product_warehouses', 'order_details.warehouse_id', '=', 'product_warehouses.id')
            ->leftJoin('product_warehouse_rooms', 'order_details.warehouse_room_id', '=', 'product_warehouse_rooms.id')
            ->leftJoin('product_warehouse_room_cartoons', 'order_details.warehouse_room_cartoon_id', '=', 'product_warehouse_room_cartoons.id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                'products.is_package',
                'units.name as unit_name',
                'categories.name as category_name',
                'product_warehouses.title as warehouse_title',
                'product_warehouse_rooms.title as warehouse_room_title',
                'product_warehouse_room_cartoons.title as warehouse_room_cartoon_title'
            )
            ->where('order_details.order_id', $order->id)
            ->get();
        $generalInfo = DB::table('general_infos')->select('logo', 'logo_dark', 'company_name')->first();
        $delivery_man = User::where('user_type', 4)->get();

        return view('backend.orders.details', compact('order', 'shippingInfo', 'billingAddress', 'orderDetails', 'userInfo', 'generalInfo', 'delivery_man'));
    }

    public function cancelOrder($slug)
    {
        $data = Order::where('slug', $slug)->first();
        $data->order_status = 6;
        $data->payment_status = 2;
        $data->updated_at = Carbon::now();
        $data->save();

        $order_details = DB::table('order_details')->where('order_id', $data->id)->select('product_id', 'qty')->get();

        foreach ($order_details as $order_detail) {
            $product = Product::find($order_detail->product_id);

            if ($product) {
                if ($product->is_package) {
                    // If it's a package, increment stock for each item in the package
                    $packageItems = DB::table('package_product_items')
                        ->where('package_product_id', $product->id)
                        ->get();

                    foreach ($packageItems as $item) {
                        $itemProduct = Product::find($item->product_id);
                        if (!$itemProduct) continue;

                        // Check if the package item has color_id or size_id (variant)
                        if ($item->color_id || $item->size_id) {
                            // It's a variant, increment variant stock
                            $variantQuery = DB::table('product_variants')
                                ->where('product_id', $item->product_id);

                            if ($item->color_id) {
                                $variantQuery->where('color_id', $item->color_id);
                            }

                            if ($item->size_id) {
                                $variantQuery->where('size_id', $item->size_id);
                            }

                            $variantQuery->increment('stock', $order_detail->qty * $item->quantity);
                        } else {
                            // Normal product without variant
                            $itemProduct->increment('stock', $order_detail->qty * $item->quantity);
                        }
                    }
                } else {
                    // Normal product
                    $product->increment('stock', $order_detail->qty);
                }
            }
        }

        OrderProgress::insert([
            'order_id' => $data->id,
            'order_status' => 6,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Order Cancelled successfully.']);
    }

    public function approveOrder($slug)
    {

        $data = Order::where('slug', $slug)->first();
        $data->order_status = 1;
        $data->updated_at = Carbon::now();
        $data->save();

        OrderProgress::insert([
            'order_id' => $data->id,
            'order_status' => 1,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Order Approved successfully.']);
    }

    public function intransitOrder($slug)
    {

        $data = Order::where('slug', $slug)->first();
        $data->order_status = 3;
        $data->updated_at = Carbon::now();
        $data->save();

        OrderProgress::insert([
            'order_id' => $data->id,
            'order_status' => 3,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Order In Transit successfully.']);
    }

    public function deliverOrder($slug)
    {

        $data = Order::where('slug', $slug)->first();
        $data->order_status = 4;
        $data->payment_status = 1;
        $data->updated_at = Carbon::now();
        $data->save();

        OrderProgress::insert([
            'order_id' => $data->id,
            'order_status' => 4,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Order Delivered successfully.']);
    }

    public function orderInfoUpdate(Request $request)
    {

        $data = Order::where('id', $request->order_id)->first();

        if ($data->order_status == 2 && $request->order_status == 2) {
            Toastr::error('Delivery Man Already Assigned', 'Error');
            return back();
        }

        if ($request->order_status) {

            if ($request->order_status == 4 && $data->payment_method == 1) {
                $data->payment_status = 1;
            }

            $data->order_remarks = $request->order_remarks;

            if ($request->order_status == 6) {
                $order_details = DB::table('order_details')->where('order_id', $data->id)->select('product_id', 'qty')->get();

                foreach ($order_details as $order_detail) {
                    $product = Product::find($order_detail->product_id);
                    if ($product) {
                        $product->increment('stock', $order_detail->qty);
                    }
                }
            }

            $data->order_status = $request->order_status;

            $data->estimated_dd = $request->estimated_dd;
            $data->updated_at = Carbon::now();
            $data->save();


            OrderProgress::insert([
                'order_id' => $request->order_id,
                'order_status' => $request->order_status ?? $data->order_status,
                'created_at' => Carbon::now()
            ]);

            $existingDeliveryMan = DB::table('order_delivey_men')->where('order_id', $request->order_id)->first();

            if ($existingDeliveryMan) {
                DB::table('order_delivey_men')
                    ->where('order_id', $request->order_id)
                    ->update([
                        'delivery_man_id' => $request->delivery_man_id,
                        'status' => 'pending',
                    ]);
            } else {
                DB::table('order_delivey_men')->insert([
                    'order_id' => $request->order_id,
                    'delivery_man_id' => $request->delivery_man_id,
                    'status' => 'pending',
                ]);
            }
        } else {
            $data->order_remarks = $request->order_remarks;
            $data->estimated_dd = $request->estimated_dd;
            $data->updated_at = Carbon::now();
            $data->save();
        }

        Toastr::success('Order Information Updated', 'Success');
        return back();
    }

    public function orderEdit($slug)
    {
        $order = Order::where('slug', $slug)->first();
        $userInfo = User::where('id', $order->user_id)->first();
        $shippingInfo = ShippingInfo::where('order_id', $order->id)->first();
        $billingAddress = BillingAddress::where('order_id', $order->id)->first();

        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', 'products.id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('units', 'order_details.unit_id', 'units.id')
            ->select('order_details.*', 'products.name as product_name', 'units.name as unit_name', 'categories.name as category_name')
            ->where('order_id', $order->id)
            ->get();

        $generalInfo = DB::table('general_infos')->select('logo', 'logo_dark', 'company_name')->first();
        $districts = DB::table('districts')->get();
        $countries = DB::table('country')->get();
        $upazilas = DB::table('upazilas')->get();

        return view(
            'backend.orders.edit',
            compact('order', 'shippingInfo', 'billingAddress', 'orderDetails', 'userInfo', 'generalInfo', 'districts', 'countries', 'upazilas')
        );
    }

    public function orderUpdate(Request $request)
    {
        $orderInfo = Order::where('id', $request->order_id)->first();

        // order items
        if (isset($request->product_id)) {
            OrderDetails::where('order_id', $request->order_id)->delete();
            $index = 0;
            $totalOrderAmount = 0;
            foreach ($request->product_id as $product_id) {

                $color_id = isset($request->color_id[$index]) ? $request->color_id[$index] : '';
                $size_id = isset($request->size_id[$index]) ? $request->size_id[$index] : '';
                $region_id = isset($request->region_id[$index]) ? $request->region_id[$index] : '';
                $sim_id = isset($request->sim_id[$index]) ? $request->sim_id[$index] : '';
                $storage_id = isset($request->storage_id[$index]) ? $request->storage_id[$index] : '';
                $warrenty_id = isset($request->warrenty_id[$index]) ? $request->warrenty_id[$index] : '';
                $device_condition_id = isset($request->device_condition_id[$index]) ? $request->device_condition_id[$index] : '';

                OrderDetails::insert([
                    'order_id' => $request->order_id,
                    'product_id' => $product_id,
                    'color_id' => $color_id,
                    'size_id' => $size_id,
                    'region_id' => $region_id,
                    'sim_id' => $sim_id,
                    'storage_id' => $storage_id,
                    'warrenty_id' => $warrenty_id,
                    'device_condition_id' => $device_condition_id,
                    'unit_id' => $request->unit_id[$index],
                    'qty' => $request->qty[$index],
                    'unit_price' => $request->unit_price[$index],
                    'total_price' => $request->qty[$index] * $request->unit_price[$index],
                    'updated_at' => Carbon::now()
                ]);
                $totalOrderAmount = $totalOrderAmount + ($request->qty[$index] * $request->unit_price[$index]);
                $index++;
            }

            $orderInfo->sub_total = $totalOrderAmount;
            $orderInfo->total = $totalOrderAmount - $orderInfo->discount;
            $orderInfo->save();
        } else {
            Toastr::error('Sorry No Item Exists', 'Failed');
            return back();
        }

        // shipping info
        $shippingInfo = ShippingInfo::where('order_id', $request->order_id)->first();
        if ($shippingInfo) {

            $deliveryCharge = 100;
            $districtWiseDeliveryCharge = DB::table('districts')->select('delivery_charge')->where('name', strtolower(trim($request->shipping_city)))->first();
            if ($districtWiseDeliveryCharge) {
                $deliveryCharge = $districtWiseDeliveryCharge->delivery_charge;
            }

            $orderInfo->delivery_fee = $deliveryCharge;
            $orderInfo->total = $orderInfo->total + $deliveryCharge;
            $orderInfo->save();


            $shippingInfo->full_name = $request->shipping_name;
            $shippingInfo->phone = $request->shipping_phone;
            $shippingInfo->email = $request->shipping_email;
            $shippingInfo->address = $request->shipping_address;
            $shippingInfo->post_code = $request->shipping_post_code;
            $shippingInfo->city = $request->shipping_city;
            $shippingInfo->country = $request->shipping_country;
            $shippingInfo->thana = $request->shipping_thana;
            $shippingInfo->updated_at = Carbon::now();
            $shippingInfo->save();
        } else {

            $deliveryCharge = 100;
            $districtWiseDeliveryCharge = DB::table('districts')->select('delivery_charge')->where('name', strtolower(trim($request->shipping_city)))->first();
            if ($districtWiseDeliveryCharge) {
                $deliveryCharge = $districtWiseDeliveryCharge->delivery_charge;
            }

            $orderInfo->delivery_fee = $deliveryCharge;
            $orderInfo->total = $orderInfo->total + $deliveryCharge;
            $orderInfo->save();

            ShippingInfo::insert([
                'order_id' => $orderInfo->id,
                'full_name' => $request->shipping_name,
                'phone' => $request->shipping_phone,
                'email' => $request->shipping_email,
                'address' => $request->shipping_address,
                'post_code' => $request->shipping_post_code,
                'city' => $request->shipping_city,
                'country' => $request->shipping_country,
                'created_at' => Carbon::now()
            ]);
        }

        // billing info
        $billingInfo = BillingAddress::where('order_id', $request->order_id)->first();
        if ($billingInfo) {
            $billingInfo->address = $request->billing_address;
            $billingInfo->post_code = $request->billing_post_code;
            $billingInfo->city = $request->billing_city;
            $billingInfo->country = $request->billing_country;
            $billingInfo->thana = $request->billing_thana;
            $billingInfo->updated_at = Carbon::now();
            $billingInfo->save();
        } else {
            BillingAddress::insert([
                'order_id' => $orderInfo->id,
                'address' => $request->billing_address,
                'post_code' => $request->billing_post_code,
                'city' => $request->billing_city,
                'country' => $request->billing_country,
                'created_at' => Carbon::now()
            ]);
        }

        if (isset($request->payment_method) && $request->payment_method == 1) {
            $orderInfo->bank_tran_id = "Not Available (COD)";
            $orderInfo->payment_method = 1;
            $orderInfo->payment_status = 1; //success
            $orderInfo->save();

            OrderPayment::insert([
                'order_id' => $orderInfo->id,
                'payment_through' => "COD",
                'tran_id' => $orderInfo->tran_id,
                'val_id' => NULL,
                'amount' => $orderInfo->total,
                'card_type' => NULL,
                'store_amount' => $orderInfo->total,
                'card_no' => NULL,
                'status' => "VALID",
                'tran_date' => date("Y-m-d H:i:s"),
                'currency' => "BDT",
                'card_issuer' => NULL,
                'card_brand' => NULL,
                'card_sub_brand' => NULL,
                'card_issuer_country' => NULL,
                'created_at' => Carbon::now()
            ]);
        }

        Toastr::success('Order Information Updated', 'Success');
        return back();
    }

    public function addMoreProduct(Request $request)
    {
        $rowNo = $request->rowno;
        $returnHTML = view('backend.orders.add_more', compact('rowNo'))->render();
        return response()->json(['more' => $returnHTML]);
    }

    public function getProductVariants(Request $request)
    {

        $productInfo = Product::where('id', $request->product_id)->first();
        if ($productInfo->has_variant == 1) {
            $data = DB::table('product_variants')
                ->leftJoin('colors', 'product_variants.color_id', '=', 'colors.id')
                ->leftJoin('product_sizes', 'product_variants.size_id', '=', 'product_sizes.id')
                ->leftJoin('country', 'product_variants.region_id', '=', 'country.id')
                ->leftJoin('sims', 'product_variants.sim_id', '=', 'sims.id')
                ->leftJoin('storage_types', 'product_variants.storage_type_id', '=', 'storage_types.id')
                ->leftJoin('product_warrenties', 'product_variants.warrenty_id', '=', 'product_warrenties.id')
                ->leftJoin('device_conditions', 'product_variants.device_condition_id', '=', 'device_conditions.id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')

                ->select('product_variants.id', 'product_variants.color_id', 'product_variants.size_id', 'product_variants.storage_type_id', 'product_variants.region_id', 'product_variants.sim_id', 'product_variants.warrenty_id', 'product_variants.device_condition_id', 'product_variants.discounted_price', 'product_variants.price', 'product_variants.stock as variant_stock', 'colors.name as color_name', 'product_sizes.name as size_name', 'country.name as region_name', 'sims.name as sim_name', 'storage_types.ram', 'storage_types.rom', 'product_warrenties.name as warrrenty', 'device_conditions.name as device_condition', 'units.name as unit_name', 'units.id as unit_id')

                ->where('product_variants.product_id', $request->product_id)
                ->where('product_variants.stock', '>', 0)
                ->orderBy('product_variants.id', 'asc')
                ->get();

            return response()->json($data);
        } else {

            $productInfo = DB::table('products')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->select('products.*', 'units.name as unit_name')
                ->where('products.id', $request->product_id)
                ->first();

            return response()->json($productInfo);
        }
    }

    public function deleteOrder($slug)
    {

        $orderInfo = Order::where('slug', $slug)->first();
        // OrderDetails::where('order_id', $orderInfo->id)->delete();
        // ShippingInfo::where('order_id', $orderInfo->id)->delete();
        // BillingAddress::where('order_id', $orderInfo->id)->delete();
        // OrderPayment::where('order_id', $orderInfo->id)->delete();
        // OrderProgress::where('order_id', $orderInfo->id)->delete();
        $orderInfo->delete();

        return response()->json(['success' => 'Order Deleted Successfully.']);
    }
    public function RestoreOrder($slug)
    {

        $orderInfo = Order::withTrashed()->where('slug', $slug)->first();
        $orderInfo->restore();

        return response()->json(['success' => 'Order Restored Successfully.']);
    }
}
