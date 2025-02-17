@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
    <style>
        .alternate-list {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }
        .alternate-list li:not(:last-child){
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--bs-border-color);
        }
    </style>

<style type="text/css" media="print">
      @page :footer {
        display: none !important;
      }

      @page :header {
        display: none !important;
      }
      @page { size: landscape;
        margin: 0;
    }
      /* @page { margin: 0; } */

      .pr-hide {
        display: none;
        }

      button {
        display: none !important;
      }
      * {
        -webkit-print-color-adjust: none !important;   /* Chrome, Safari 6 – 15.3, Edge */
        color-adjust: none !important;                 /* Firefox 48 – 96 */
        print-color-adjust: none !important;           /* Firefox 97+, Safari 15.4+ */
      }
    </style>

    <div class="row pr-hide">
        <div class="col-12">
            <div class="card ">
                <div class="card-header border-bottom-0">
                    <div class="row pr-hide">
                        <div class="col-auto col-lg-4 mb-4">
                            <div class="input-group">
                                <select class="form-select select2" name="payment_status"
                                    data-minimum-results-for-search="Infinity" id="update_payment_status">
                                    <option value="" disabled>
                                        Payment Status
                                    </option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-auto col-lg-4 mb-4">
                            <div class="input-group">
                                <select name="delivery_status" class="form-control select2" name="delivery_status"
                                    data-ajax--url="{{ route('backend.get_search_data', ['type' => 'constant', 'sub_type' => 'ORDER_STATUS']) }}"
                                    data-ajax--cache="true">
                                    <option value="" disabled>Delivery Status</option>
                                    @if (isset($order->delivery_status))
                                        <option value="{{ $order->delivery_status }}" selected>
                                            {{ Str::title(Str::replace('_', ' ', $order->delivery_status)) }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-auto col-lg-4 mb-4 text-center text-lg-end">
                            <a class="btn btn-primary" href="{{route('backend.orders.downloadInvoice', ['id' => request()->id])}}">
                                <i class="fa-solid fa-download"></i>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!--Main Invoice-->
        <div class="col-xl-9 order-2 order-md-2 order-lg-2 order-xl-1">
            <div class="card mb-4" id="section-1">
                <div class="card-body">
                    <!--Order Detail-->
                    <div class="row justify-content-between align-items-center g-3 mb-4">
                        <div class="col-auto flex-grow-1">
                            <img src="{{ asset(setting('logo')) }}" alt="logo" class="img-fluid" width="200">
                        </div>
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
                            <div class="col-auto mt-3">
                                <h6 class="d-inline-block">Payment Method: </h6>
                                <span class="badge bg-primary">{{ ucwords(str_replace('_', ' ', $order->orderGroup->payment_method)) }}</span>
                            </div>
                            <h6 class="col-auto d-inline-block">Logistic: </h6> <span class="badge bg-primary">{{ $order->logistic_name }}</span><br>
                            <h6 class="col-auto d-inline-block">Status: </h6> <span>{{ Str::title(Str::replace('_', ' ', $order->delivery_status))  }}</span>
                        </div>
                        <div class="col">
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
                <table class="table table-bordered border-top" data-use-parent-width="true">
                    <thead>
                        <tr>
                            <th class="text-center" width="7%">S/L</th>
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
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div> <img src="{{ $product->feature_image }}" alt="{{ $product->name }}"
                                                class="avatar avatar-50 rounded-pill">
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="fs-lg mb-0" style="max-width: 280px; white-space: normal;">
                                                {{ $product->name }}
                                            </h6>
                                            <div class="text-muted">
                                                @foreach (generateVariationOptions($item->product_variation->combinations) as $variation)
                                                    <span class="fs-xs">
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

                                <td class=" text-end">
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
                                <h6 class="d-inline-block me-3">Sub Total: </h6>
                            </td>
                            <td width="10%">
                                <strong>{{ \Currency::format($order->orderGroup->sub_total_amount) }}</strong></td>
                        </tr>
                        @if ($order->orderGroup->total_tips_amount > 0)
                            <tr>
                                <td colspan="4">
                                    <h6 class="d-inline-block me-3">Tips: </h6>
                                </td>
                                <td width="10%" class="text-end">
                                    <strong>{{ \Currency::format($order->orderGroup->total_tips_amount) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4">
                                <h6 class="d-inline-block me-3">Tax: </h6>
                            </td>
                            <td width="10%" class="text-end">
                                <strong>{{ \Currency::format($order->orderGroup->total_tax_amount) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <h6 class="d-inline-block me-3">Delivery Charge: </h6>
                            </td>
                            <td width="10%" class="text-end">
                                <strong>{{ \Currency::format($order->orderGroup->total_shipping_cost) }}</strong></td>
                        </tr>
                        @if ($order->orderGroup->total_coupon_discount_amount > 0)
                            <tr>
                                <td colspan="4">
                                    <h6 class="d-inline-block me-3">Coupon Discount: </h6>
                                </td>
                                <td width="10%" class="text-end">
                                    <strong>{{ \Currency::format($order->orderGroup->total_coupon_discount_amount) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4">
                                <h6 class="d-inline-block me-3">Grand Total: </h6>
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

        <!--Order Status-->
        <div class="col-xl-3 order-1 order-md-1 order-lg-1 order-xl-2 pr-hide">
            <div class="sticky-sidebar">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="alternate-list list-unstyled">

                            @forelse ($order->orderUpdates as $orderUpdate)
                                <li>
                                    <a class="{{ $loop->first ? 'active' : '' }}">
                                        {{ $orderUpdate->note }} <br> By
                                        <span class="text-capitalize">{{ optional($orderUpdate->user)->name }}</span>
                                        at
                                        {{ date('d M, Y', strtotime($orderUpdate->created_at)) }}.</a>
                                </li>
                            @empty
                                <li>
                                    <a class="active">No logs found</a>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        function invoicePrint() {
            window.print()
        }

        function updateStatusAjax(__this, url) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    order_id: {{ $order->id }},
                    status: __this.val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.status) {
                        window.successSnackbar(res.message)
                        setTimeout(() => {
                            location.reload()
                        }, 100);
                    }
                }
            });
        }
        $('[name="payment_status"]').on('change', function() {
            if ($(this).val() !== '') {
                updateStatusAjax($(this), "{{ route('backend.orders.update_payment_status') }}")
            }
        })

        $('[name="delivery_status"]').on('change', function() {
            if ($(this).val() !== '') {
                updateStatusAjax($(this), "{{ route('backend.orders.update_delivery_status') }}")
            }
        })
    </script>
@endpush
