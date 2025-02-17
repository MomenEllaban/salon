<?php

namespace Modules\Product\Trait;

use App\Models\Branch;
use App\Models\User;
use Modules\Product\Models\Cart;
use Modules\Product\Models\Order;
use Modules\Product\Models\OrderGroup;
use Modules\Product\Models\OrderItem;

trait ProductTrait
{
    /**
     * Request $data
     * - location_id
     * - shipping_address_id
     * - billing_address_id
     * - phone
     * - type
     * - tips
     * - payment_method
     * - user_id
     */
    protected function createCart($booking_product, $booking_details)
    {
        foreach ($booking_product as $product) {
            $cart = new Cart();
            $cart->user_id = $booking_details['user_id'];
            $cart->product_id = $product['product_id'];
            $cart->location_id = 1;
            $cart->product_variation_id = $product['product_variation_id'];
            $cart->qty = $product['product_qty'];
            $cart->save();
        }

        $branch_data = Branch::with('address')->where('id', $booking_details['branch_id'])->first();

        $address_id = $branch_data ? $branch_data->address->id : null;

        $user_data = User::where('id', $booking_details['user_id'])->first();

        $mobile = $user_data ? $user_data->mobile : null;

        $data = [
            'user_id' => $booking_details['user_id'],
            'location_id' => 1,
            'shipping_address_id' => $address_id,
            'billing_address_id' => $address_id,
            'type' => 'booking',
            'tips' => $booking_details['payment']->tip_amount,
            'payment_method' => $booking_details['payment']->transaction_type,
            'alternative_phone' => $mobile,
            'phone' => $mobile,

        ];

        return $orderId = $this->createOrder($data);
    }

    protected function createOrder($data)
    {
        $userId = $data['user_id'];

        $location_id = $data['location_id'];

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
            $orderGroup->shipping_address_id = $data['shipping_address_id'];
            $orderGroup->billing_address_id = $data['billing_address_id'];
            $orderGroup->location_id = $location_id;
            $orderGroup->phone_no = $data['phone'];
            $orderGroup->alternative_phone_no = $data['alternative_phone'];
            $orderGroup->sub_total_amount = getSubTotal($carts, false, '', false);
            $orderGroup->total_tax_amount = 0;
            $orderGroup->total_coupon_discount_amount = 0;
            $orderGroup->type = $data['type'];
            $orderGroup->payment_status = 'paid';

            //  $logisticZone = LogisticZone::where('id', $data['chosen_logistic_zone_id'])->first();
            // todo::[for eCommerce] handle exceptions for standard & express
            $orderGroup->total_shipping_cost = 0;  //$logisticZone->standard_delivery_charge
            $orderGroup->total_tips_amount = $data['tips'];

            $orderGroup->grand_total_amount = $orderGroup->sub_total_amount + $orderGroup->total_tax_amount + $orderGroup->total_shipping_cost + $orderGroup->total_tips_amount - $orderGroup->total_coupon_discount_amount;
            $orderGroup->save();

            // order -> todo::[update version] make array for each vendor, create order in loop
            $order = new Order;
            $order->order_group_id = $orderGroup->id;
            $order->user_id = $userId;
            $order->location_id = $location_id;
            $order->total_admin_earnings = $orderGroup->grand_total_amount;
            $order->delivery_status = 'delivered';
            $order->payment_status = 'paid';

            // $order->logistic_id = $logisticZone->logistic_id;
            // $order->logistic_name = optional($logisticZone->logistic)->name;

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
            $orderGroup->payment_method = $data['payment_method'];
            $orderGroup->save();

            return $order->id;
        }
    }
}
