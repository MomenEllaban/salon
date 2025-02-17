<?php

namespace Modules\Product\Http\Controllers\Backend;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Location\Models\Location;
use Modules\Product\Models\Cart;
use Modules\Product\Models\Order;
use Modules\Product\Models\OrderGroup;
use Modules\Product\Models\OrderItem;
use Modules\Product\Models\OrderUpdate;
use Modules\Product\Trait\OrderTrait;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Modules\Tax\Models\Tax;

class OrdersController extends Controller
{
    use OrderTrait;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'orders.title';
        // module name
        $this->module_name = 'orders';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $export_import = false;

        $locations = Location::where('status', 1)->latest()->get();

        return view('product::backend.order.index_datatable', compact('export_import', 'locations'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $orders = Order::with('orderGroup');

        $filter = $request->filter;

        $posOrder = [];

        if (isset($filter)) {
            if (isset($filter['code'])) {
                $orders = $orders->where(function ($q) use ($filter) {
                    $orderGroup = OrderGroup::where('order_code', $filter['code'])->pluck('id');
                    $q->orWhereIn('order_group_id', $orderGroup);
                });
            }

            if (isset($filter['delivery_status'])) {
                $orders = $orders->where('delivery_status', $filter['delivery_status']);
            }

            if (isset($filter['payment_status'])) {
                $orders = $orders->where('payment_status', $filter['payment_status']);
            }

            if (isset($filter['location_id'])) {
                $orders = $orders->where('location_id', $filter['location_id']);
            }
        }

        $orders = $orders->where(function ($q) {
            $orderGroup = OrderGroup::pluck('id');
            $q->orWhereIn('order_group_id', $orderGroup);
        });

        return $datatable->eloquent($orders)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('product::backend.order.columns.action_column', compact('data'));
            })
            ->editColumn('order_code', function ($data) {
                return setting('inv_prefix').$data->orderGroup->order_code;
            })
            ->editColumn('customer_name', function ($data) {
                $user = optional($data->user);
                $Profile_image = $user->profile_image ?? default_user_avatar();
                $name = $user->full_name ?? default_user_name();
                $email = $user->email ?? '--';
                return view('booking::backend.bookings.datatable.user_id', compact('Profile_image','name','email'));
                // return view('product::backend.order.columns.customer_column', compact('data'));
            })
            ->addColumn('phone', function ($data) {
                return optional($data->user)->mobile ?? '-';
            })
            ->editColumn('placed_on', function ($data) {
                return customDate($data->created_at);
            })
            ->editColumn('items', function ($data) {
                return $data->orderItems()->count();
            })
            ->editColumn('type', function ($data) {
                return view('product::backend.order.columns.type_column', compact('data'));
            })
            ->editColumn('payment', function ($data) {
                return view('product::backend.order.columns.payment_column', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return view('product::backend.order.columns.status_column', compact('data'));
            })
            ->editColumn('location', function ($data) {
                return $data->location ? $data->location->name : 'N/A';
            })
            ->filterColumn('customer_name', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%'.$keyword.'%');
                        $q->orWhere('last_name', 'like', '%'.$keyword.'%');
                        $q->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->filterColumn('phone', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('mobile', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('updated_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->updated_at);
                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action', 'check','phone'])
            ->toJson();
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show(Request $request)
    {
        $order = Order::find($request->id);
        if ($order == null) {
            return abort(500);
        }

        return view('product::backend.order.show', compact('order'));
    }

    // update payment status
    public function updatePaymentStatus(Request $request)
    {
        $order = Order::findOrFail((int) $request->order_id);
        $order->payment_status = $request->status;
        $order->save();

        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'note' => 'Payment status updated to '.ucwords(str_replace('_', ' ', $request->status)).'.',
        ]);

        // todo::['mail notification']
        return response()->json(['status' => true, 'message' => 'Payment Status Has Been Updated']);
    }

    // update delivery status
    public function updateDeliveryStatus(Request $request)
    {
        $order = Order::findOrFail((int) $request->order_id);

        if ($order->delivery_status != 'cancelled' && $request->status == 'cancelled') {
            $this->addQtyToStock($order);
        }

        if ($order->delivery_status == 'cancelled' && $request->status != 'cancelled') {
            $this->removeQtyFromStock($order);
        }

        $order->delivery_status = $request->status;
        $order->save();

        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => auth()->user()->id,
            'note' => 'Delivery status updated to '.ucwords(str_replace('_', ' ', $request->status)).'.',
        ]);

        $order_prefix_data = Setting::where('name', 'inv_prefix')->first();
        $order_prefix = $order_prefix_data ? $order_prefix_data->val : '';

        $notify_type = null;

        $status = $request->status;

        switch ($status) {
            case 'processing':
                $notify_type = 'order_proccessing';
                $messageTemplate = 'Order #[[order_id]] is now being processed.';
                $notify_message = str_replace('[[order_id]]', $order->id, $messageTemplate);
                break;
            case 'delivered':
                $notify_type = 'order_delivered';
                $messageTemplate = 'Order #[[order_id]] has been delivered.';
                $notify_message = str_replace('[[order_id]]', $order->id, $messageTemplate);
                break;
            case 'cancelled':
                $notify_type = 'order_cancelled';
                $messageTemplate = 'Order #[[order_id]] has been cancelled.';
                $notify_message = str_replace('[[order_id]]', $order->id, $messageTemplate);
                break;
        }

        try {
            $notification_data = [

                'id' => $order->id,
                'order_code' => $order_prefix.optional($order->orderGroup)->order_code,
                'user_id' => $order->user_id,
                'user_name' => optional($order->user)->first_name.' '.optional($order->user)->last_name ?? default_user_name(),
                'order_date' => $order->updated_at->format('d/m/Y'),
                'order_time' => $order->updated_at->format('h:i A'),
            ];

            $this->sendNotificationOnOrderUpdate($notify_type,$notify_message,$notification_data);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        // todo::['mail notification']
        return response()->json(['status' => true, 'message' => 'Order delivery status has been updated']);
    }

    // add qty to stock
    private function addQtyToStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->product_variation->product_variation_stock;
            $stock->stock_qty += $orderItem->qty;
            $stock->save();

            $product = $orderItem->product_variation->product;
            $product->total_sale_count += $orderItem->qty;
            $product->save();

            if ($product->categories()->count() > 0) {
                foreach ($product->categories as $category) {
                    $category->total_sale_count += $orderItem->qty;
                    $category->save();
                }
            }
        }
    }

    // remove qty from stock
    private function removeQtyFromStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->product_variation->product_variation_stock;
            $stock->stock_qty -= $orderItem->qty;
            $stock->save();

            $product = $orderItem->product_variation->product;
            $product->total_sale_count -= $orderItem->qty;
            $product->save();

            if ($product->categories()->count() > 0) {
                foreach ($product->categories as $category) {
                    $category->total_sale_count -= $orderItem->qty;
                    $category->save();
                }
            }
        }
    }

    // Order Creation
    public function complete(Request $request)
    {
        $user = auth()->user();

        $userId = $user->id;

        $location_id = $request->location_id;

        $carts = Cart::where('user_id', $userId)->where('location_id', $location_id)->get();

        if (count($carts) > 0) {
            // check carts available stock -- todo::[update version] -> run this check while storing OrderItems
            foreach ($carts as $cart) {
                $productVariationStock = $cart->product_variation->product_variation_stock ? $cart->product_variation->product_variation_stock->stock_qty : 0;
                if ($cart->qty > $productVariationStock) {
                    $message = $cart->product_variation->product->name.' is out of stock';

                    return response()->json(['message' => $message, 'status' => false]);
                }
            }

            // create new order group
            $orderGroup = new OrderGroup;
            $orderGroup->user_id = $userId;
            $orderGroup->shipping_address_id = $request->shipping_address_id;
            $orderGroup->billing_address_id = $request->billing_address_id;
            $orderGroup->location_id = $location_id;
            $orderGroup->phone_no = $request->phone;
            $orderGroup->alternative_phone_no = $request->alternative_phone;
            $orderGroup->sub_total_amount = getSubTotal($carts, false, '', false);
            $orderGroup->total_tax_amount = 0;
            $orderGroup->total_coupon_discount_amount = 0;
            $orderGroup->type = 'online';
            $logisticZone = LogisticZone::where('id', $request->chosen_logistic_zone_id)->first();
            // todo::[for eCommerce] handle exceptions for standard & express
            $orderGroup->total_shipping_cost = $logisticZone->standard_delivery_charge;
            $orderGroup->total_tips_amount = $request->tips;

            $orderGroup->grand_total_amount = $orderGroup->sub_total_amount + $orderGroup->total_tax_amount + $orderGroup->total_shipping_cost + $orderGroup->total_tips_amount - $orderGroup->total_coupon_discount_amount;
            $orderGroup->save();

            // order -> todo::[update version] make array for each vendor, create order in loop
            $order = new Order;
            $order->order_group_id = $orderGroup->id;
            $order->user_id = $userId;
            $order->location_id = $location_id;
            $order->total_admin_earnings = $orderGroup->grand_total_amount;
            $order->logistic_id = $logisticZone->logistic_id;
            $order->logistic_name = optional($logisticZone->logistic)->name;

            $order->shipping_cost = $orderGroup->total_shipping_cost; // todo::[update version] calculate for each vendors
            $order->tips_amount = $orderGroup->total_tips_amount; // todo::[update version] calculate for each vendors

            $order->save();

            // order items
            $total_points = 0;
            foreach ($carts as $cart) {
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_variation_id = $cart->product_variation_id;
                $orderItem->qty = $cart->qty;
                $orderItem->location_id = $location_id;
                $orderItem->unit_price = variationDiscountedPrice($cart->product_variation->product, $cart->product_variation);
                $orderItem->total_tax = 0;
                $orderItem->total_price = $orderItem->unit_price * $orderItem->qty;
                $orderItem->save();

                $product = $cart->product_variation->product;
                $product->total_sale_count += $orderItem->qty;

                // minus stock qty
                try {
                    $productVariationStock = $cart->product_variation->product_variation_stock;
                    $productVariationStock->stock_qty -= $orderItem->qty;
                    $productVariationStock->save();
                } catch (\Throwable $th) {
                    //throw $th;
                }

                $product->stock_qty -= $orderItem->qty;
                $product->save();

                // category sales count
                if ($product->categories()->count() > 0) {
                    foreach ($product->categories as $category) {
                        $category->total_sale_count += $orderItem->qty;
                        $category->save();
                    }
                }
                $cart->delete();
            }

            $order->save();
            // payment gateway integration & redirection
            $orderGroup->payment_method = $request->payment_method;
            $orderGroup->save();

            return true;
        }
    }

    // download invoice
    public function downloadInvoice($id)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', config('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        $font_family = "'Roboto','sans-serif'";

        $order = Order::findOrFail((int) $id);
        $app_name = Setting::where('name', 'app_name')->value('val');
        $inquriy_email = Setting::where('name', 'inquriy_email')->value('val');
        $helpline_number = Setting::where('name', 'helpline_number')->value('val');

        $taxDetails = json_decode($order->orderGroup->taxes); 
        // $imagePath = asset('public/img/logo/logo.png');
        $view = view('product::backend.order.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'app_name' => $app_name,          
            'inquriy_email' => $inquriy_email, 
            'helpline_number' => $helpline_number,
            'taxDetails' => $taxDetails, 

        ])->render();
        // dd($imagePath);
        $pdf = Pdf::loadHTML($view);
            return $pdf->download('invoice.pdf');
    }

    public function OrderInvoicedownload(Request $request )
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', config('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        $font_family = "'Roboto','sans-serif'";

        $order = Order::findOrFail((int)($request->id));

        $app_name = Setting::where('name', 'app_name')->value('val');
        $inquriy_email = Setting::where('name', 'inquriy_email')->value('val');
        $helpline_number = Setting::where('name', 'helpline_number')->value('val');

        $taxDetails = json_decode($order->orderGroup->taxes);
        // $imagePath = asset('public/img/logo/logo.png');
        $view = view('product::backend.order.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'app_name' => $app_name,          
            'inquriy_email' => $inquriy_email, 
            'helpline_number' => $helpline_number,
            'taxDetails' => $taxDetails, // Pass tax details to the view

        ])->render();
        // dd($imagePath);
        $pdf = Pdf::loadHTML($view);

        if ($request->is('api/*')) {
            // Handle API request
            $baseDirectory = storage_path('app/public');
            $highestDirectory = collect(File::directories($baseDirectory))->map(function ($directory) {
                return basename($directory);
            })->max() ?? 0;
            $nextDirectory = intval($highestDirectory) + 1;
            while (File::exists($baseDirectory . '/' . $nextDirectory)) {
                $nextDirectory++;
            }
            $newDirectory = $baseDirectory . '/' . $nextDirectory;
            File::makeDirectory($newDirectory, 0777, true);

            $filename = 'invoice_' . $request->id . '.pdf';
            $filePath = $newDirectory . '/' . $filename;

            $pdf->save($filePath);

            $url = url('storage/' . $nextDirectory . '/' . $filename);
            if (!empty($url)) {
                return response()->json(['status' => true, 'link' => $url], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Url Not Found'], 404);
            }
        } else {
            // Handle non-API request
            return $pdf->download('invoice.pdf');
        }
    }
}
