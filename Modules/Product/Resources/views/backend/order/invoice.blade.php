<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,700');

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body, table {
        font-family: 'Open Sans', sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }

    h5, h6 {
        margin-bottom: 10px;
    }

    .text-end {
        text-align: right !important;
    }

    .text-muted {
        color: #6c757d;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .card-header,
    .card-footer {
        background-color: #f8f9fa;
        padding: 15px;
    }
    /* Responsive adjustments */
    .visibleMobile {
        display: none;
    }

    .hiddenMobile {
        display: block;
    }

    @media (max-width: 768px) {
        .text-end {
            text-align: flex-end !important;
        }

        .visibleMobile {
            display: block;
        }

        .hiddenMobile {
            display: none;
        }
    }
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .invoice-table th, .invoice-table td {
      border: 1px solid #f1f1f1;
      padding: 16px;
      font-size: 14px;
    }

    .invoice-table th {
      background-color: #f2f2f2;
    }
</style>


{{-- header start --}}
<div class="col-xl-9 order-2 order-md-2 order-lg-2 order-xl-1">
            <div class="card mb-4" id="section-1">
                <div class="card-body">
                    <div class="invoice-title">

                        <div class="mb-4">
                            <h2 class="mb-1 text-muted">{{ $app_name }}</h2>
                        </div>
                        <div class="text-muted">
                            <p class="mb-1"><i class="uil uil-envelope-alt me-1"></i>{{ $inquriy_email }}</p>
                            <p><i class="uil uil-phone me-1"></i> {{ $helpline_number }}</p>
                        </div>
                    </div>
                    <!--Order Detail-->
                    <div class="row justify-content-between align-items-center g-3 mb-4">
                        <!-- <div class="col-auto flex-grow-1">
                        <img src="{{ public_path(setting('logo')) }}" alt="logo" class="img-fluid" width="100">
                        </div> -->
                        <div class="col-auto text-end">
                            <h5 class="mb-0">Invoice
                                <span
                                    class="text-accent">{{ setting('inv_prefix') }}{{ $order->orderGroup->order_code }}
                                </span>
                            </h5>
                            <span class="text-muted">Order Date:
                                {{ date('d M, Y', strtotime($order->created_at)) }}
                            </span>
                            <br>
                            <span class="text-muted">Delivery Date:
                                {{ date('d M, Y', strtotime($order->updated_at)) }}
                            </span>
                            @if ($order->location_id != null)
                                <div>
                                    <span class="text-muted">
                                        <i class="las la-map-marker"></i> {{ optional($order->location)->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row d-flex justify-content-md-between justify-content-center g-3">
                        <div class="col-md-3">
                            <!--Customer Detail-->
                            <div class="welcome-message">
                                <h5 class="mb-2">Customer Info</h6>
                                    <p class="mb-0">Name: <strong>{{ optional($order->user)->full_name }}</strong></p>
                                    <p class="mb-0">Email: <strong>{{ optional($order->user)->email }}</strong></p>
                                    <p class="mb-0">Phone: <strong>{{ optional($order->user)->mobile }}</strong></p>
                            </div>
                            <br>
                            <div class="col-auto mt-3">
                                <h6 class="d-inline-block">Payment Method: <span>{{ ucwords(str_replace('_', ' ', $order->orderGroup->payment_method)) }}</span> </h6>
                            </div>
                            <h6 class="col-auto d-inline-block">Logistic: <span>{{ $order->logistic_name }}</span> </h6> 
                            <h6 class="col-auto d-inline-block">Status: <span>{{ Str::title(Str::replace('_', ' ', $order->delivery_status))  }}</span> </h6> 
                        </div>
                        <div class="col-auto text-end">
                             <div class="shipping-address d-flex justify-content-md-end gap-3 mb-3">
                                <div class="border-end w-25">
                                    <h5 class="mb-2">Shipping Address</h5>

                                        @php
                                            $shippingAddress = $order->orderGroup->shippingAddress;
                                        @endphp
                                        <p class="mb-0 text-wrap">
                                            {{ optional($shippingAddress)->address_line_1 }},
                                            {{ optional($shippingAddress->city_data)->name }},
                                            {{ optional($shippingAddress->state_data)->name }},
                                            {{ optional($shippingAddress->country_data)->name }}
                                        </p>
                                </div>
                                @if (!$order->orderGroup->is_pos_order)
                                    <div class="w-25">
                                        <h5 class="mb-2">Billing Address</h5>
                                        @php
                                            $billingAddress = $order->orderGroup->billingAddress;
                                        @endphp
                                        <p class="mb-0 text-wrap">
                                            {{ optional($billingAddress)->address_line_1 }},
                                            {{ optional($billingAddress->city_data)->name }},
                                            {{ optional($billingAddress->state_data)->name }},
                                            {{ optional($billingAddress->country_data)->name }}
                                        </p>
                                    </div>
                                @endif
                            </div> 
                            <!-- <div class="shipping-address d-flex justify-content-md-end gap-3">
                                <div class="w-25"></div>
                                <div class="w-25">

                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!--order details-->
                <table class="invoice-table" data-use-parent-width="true">
                    <thead>
                        <tr>
                            <th class="text-center" >S/L</th>
                            <th>Products</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">QTY</th>
                            <th class="text-end">Total Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($order->orderItems as $key => $item)
                            @php
                                $product = $item->product_variation->product;
                            @endphp
                            <tr>
                                <td class="text-center"width="7%">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- <div> <img src="{{ $product->feature_image }}" alt="{{ $product->name }}"
                                                class="avatar avatar-50 rounded-pill">
                                        </div> -->
                                        <div class="ms-2">
                                            <h4 style="max-width: 250px; white-space: normal;">
                                                {{ $product->name }}
                                            </h4>
                                            <div class="text-muted">
                                                @foreach (generateVariationOptions($item->product_variation->combinations) as $variation)
                                                    <span class="fs-6">
                                                        {{ $variation['name'] }}:
                                                        @foreach ($variation['values'] as $value)
                                                            {{ $value['name'] }}
                                                        @endforeach
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-end">
                                    <span class="fw-bold">{{ \Currency::format($item->unit_price) }}
                                    </span>
                                </td>
                                <td class="fw-bold text-end">{{ $item->qty }}</td>

                                <td class="text-end">
                                    @if ($item->refundRequest && $item->refundRequest->refund_status == 'refunded')
                                        <span
                                            class="badge bg-soft-info rounded-pill text-capitalize">{{ $item->refundRequest->refund_status }}</span>
                                    @endif
                                    <span class="text-accent fw-bold">{{ \Currency::format($item->total_price) }}
                                    </span>

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-end">
                        <tr>
                            <td colspan="4">
                                <h5 class="d-inline-block text-end">Sub Total: </h5>
                            </td>
                            <td width="10%">
                                <strong>{{ \Currency::format($order->orderGroup->sub_total_amount) }}</strong></td>
                        </tr>
                        @if ($order->orderGroup->total_tips_amount > 0)
                            <tr>
                                <td colspan="4">
                                    <h5 class="d-inline-block text-end">Tips: </h5>
                                </td>
                                <td width="10%" class="text-end">
                                    <strong>{{ \Currency::format($order->orderGroup->total_tips_amount) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4">
                                <h5 class="d-inline-block text-end">Tax: 
                                        @if (!empty($taxDetails))
                                            @foreach($taxDetails as $tax)
                                            @if($tax->tax_type === 'percent')
                                                <span>({{ $tax->tax_name }} {{  $tax->tax_value }}%)</span>                                       
                                            @else
                                                <span>({{ $tax->tax_name }} {{ \Currency::format($tax->tax_value )  }})</span>
                                            @endif
                                            @endforeach
                                        @endif
                                        
                                </h5>
                            </td>
                            <td width="10%" class="text-end">
                                <strong>{{ \Currency::format($order->orderGroup->total_tax_amount) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <h5 class="d-inline-block text-end">Delivery Charge: </h5>
                            </td>
                            <td width="10%" class="text-end">
                                <strong>{{ \Currency::format($order->orderGroup->total_shipping_cost) }}</strong></td>
                        </tr>
                        @if ($order->orderGroup->total_coupon_discount_amount > 0)
                            <tr>
                                <td colspan="4">
                                    <h5 class="d-inline-block text-end">Coupon Discount: </h5>
                                </td>
                                <td width="10%" class="text-end">
                                    <strong>{{ \Currency::format($order->orderGroup->total_coupon_discount_amount) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4">
                                <h5 class="d-inline-block text-end">Grand Total: </h5>
                            </td>
                            <td width="10%" class="text-end"><strong
                                    class="text-accent">{{ \Currency::format($order->orderGroup->grand_total_amount) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!--Note-->
                <div class="card-body">
                    <div class="card-footer border-top-0 px-4 py-4 rounded bg-soft-gray border border-2">
                        <p class="mb-0">{{ setting('spacial_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
{{-- footer end --}}
