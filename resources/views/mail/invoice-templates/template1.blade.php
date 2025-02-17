<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    h1, h2, h3, h4, h5, h6 {
      color: #000000;
    }

    p {
      margin: 0 0 8px;
    }

    .invoice {
      width: 190mm;
      height: auto;
      box-sizing: border-box;
    }

    .invoice-header {
      text-align: center;
    }

    .invoice-header h1 {
      margin: 0 0 10px;
    }

    .invoice-logo-section {
      text-align: center;
      margin: 0 0 20px;
      padding: 0 0 20px;
      border-bottom: 1px solid #f1f1f1;
    }

    .invoice-detail-part {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin: 16px 0;
    }

    .invoice-customer, .invoice-billing {
      width: 45%;
    }

    .invoice-branch {
      width: 100%;
      text-align: right;
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

    .text-end {
      text-align: right; 
    }

    strong {
      color: #000000;
    }

    table th {
      color: #000000;
    }

    .thank-you {
      margin-top: 20px;
      background: #f1f1f1;
      padding: 16px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="invoice">
    <div class="invoice-logo-section">
      <img src="{{ public_path(setting('logo')) }}" alt="logo" class="img-fluid" width="100">
    </div>
    <div class="text-end">
      <p><strong>Invoice No:</strong> Booking{{$data['id']}}</p>
    </div>

    <div class="invoice-detail-part">
      <div class="invoice-customer">
        <h3>Customer Info</h3>
        <p>{{$data['user_name']}}</p>
        <p>{{$data['email']}}</p>
        <p>{{$data['mobile']}}</p>
      </div>
      <div class="invoice-billing">
        <h3>Billing Address</h3>
        <p>{{$data['venue_address']}}</p>
      </div>
      <div class="invoice-branch">
        <h3>Branch Details</h3>
        <p>Branch name: {{ $data['branch_name'] }}</p>
        <p>Contact number: {{ $data['branch_number'] }}</p>
        <p>Email</p>
      </div>
    </div>

    <div class="invoice-info">
      <div>
        <p><strong>Booking Date:</strong></p>
        <p>{{$data['booking_date']}}</p>
      </div>
      <div>
        <p><strong>Payment Method:</strong></p>
        <p>{{ $data['transaction_type'] === 'upi' ? 'UPI' : ucwords($data['transaction_type']) }}</p>
      </div>
    </div>

    <table class="invoice-table">
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th class="text-end">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data['extra']['services'] as $key => $value)
          <tr>
            <td>{{$value['service_name']}}</td>
            <td>1</td>
            <td class="text-end">{{ \Currency::format($value['service_price']) }}</td>
            <td class="text-end">{{ \Currency::format($value['service_price']) }}</td>
          </tr>
        @endforeach
        @php
          $productPrice = 0; 
        @endphp
        @foreach($data['extra']['products'] as $key => $value)
          <tr>
            <td>{{$value['product_name']}}</td>
            <td>{{$value['product_qty']}}</td>
            @php
              $price = $value['discounted_price'] != $value['product_price'] ? $value['discounted_price'] : $value['product_price'];
              $productPrice += $price * $value['product_qty'];
            @endphp
            <td class="text-end">{{ \Currency::format($price) }}</td>
            <td class="text-end">{{ \Currency::format($price * $value['product_qty']) }}</td>
          </tr>
        @endforeach
        @foreach($data['extra']['packages'] as $key => $value)
          <tr>
            <td>{{$value['name']}}</td>
            <td>1</td>
            <td class="text-end">{{ \Currency::format($value['package_price']) }}</td>
            <td class="text-end">{{ \Currency::format($value['package_price']) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        @if($data['coupon_discount'])
        <tr>
          <td colspan="3" class="text-end"><strong>Coupon Discount:</strong></td>
          <td class="text-end">{{ \Currency::format($data['coupon_discount']) }}</td>
        </tr>
        @endif
        <tr>
          <td colspan="3" class="text-end"><strong>Sub Total:</strong></td>
          <td class="text-end">{{ \Currency::format($data['serviceAmount'] + $data['product_price'] + $data['package_price'] - $data['coupon_discount']) }}</td>
        </tr>
        <tr>
          <td colspan="3" class="text-end"><strong>Tips:</strong></td>
          <td class="text-end">{{ \Currency::format($data['tip_amount']) }}</td>
        </tr>
        <tr>
          <td colspan="3" class="text-end"><strong>TAX:</strong></td>
          <td class="text-end">{{ \Currency::format($data['tax_amount']) }}</td>
        </tr>
        
        <tr>
          <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
          <td class="text-end">{{ \Currency::format($data['grand_total']) }}</td>
        </tr>
      </tfoot>
    </table>

    <div class="thank-you">
      <p>{{ setting('spacial_note') }}</p>
    </div>
  </div>
</body>
</html>
