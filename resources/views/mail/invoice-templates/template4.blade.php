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

    .invoice {
      width: 190mm;
      height: auto;
      box-sizing: border-box;
    }

    .invoice-header,
    .invoice-footer {
      text-align: center;
    }

    .invoice-header h1 {
      margin: 0;
    }

    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .invoice-table th, .invoice-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    .invoice-table th {
      background-color: #f2f2f2;
    }

    .total {
      margin-top: 20px;
      text-align: right;
    }

    .thank-you {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="invoice">
    <div class="invoice-header">
      <h1>Invoice Template 4</h1>
    </div>

    <div class="invoice-details">
      <p><strong>Invoice Number:</strong> ORDER{{$data['id']}}</p>
      <p><strong>Date: </strong>{{$data['booking_date']}}</p>
    </div>

    <table class="invoice-table">
      <thead>
        <tr>
          <th>Item Description</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th>Total</th>
        </tr>
      </thead>
      @php
            $productPrice = 0;
            $package_price = 0;
          @endphp
     <tbody>
        @foreach($data['extra']['services'] as $key => $value)
        <tr>
          <td>{{$value['service_name']}}</td>
          <td>1</td>
          <td>{{$value['service_price']}}</td>
          <td>{{$value['service_price']}}</td>
        </tr>
        @endforeach

        <!-- @if (!empty($data['product_name'])) -->

        @foreach($data['extra']['products'] as $key => $value)
        <tr>
        <td>{{$value['product_name']}}</td>
        <td>{{$value['product_qty']}}</td>

          @php
                $price = $value['product_price'];
                $delPrice = false;
                $discountType = $value['discount_type'];
                $discountValue = $value['discount_value'] . ($discountType == 'percent' ? '%' : '');
                if($price != $value['discounted_price']) {
                    $delPrice = $price;
                    $price = $value['discounted_price'];
                }
                $productPrice = $price * $value['product_qty'] +$productPrice
          @endphp

        <td>{{$price}}</td>
        <td>{{ $price * $value['product_qty'] }}</td>
        </tr>

      @endforeach
        <!-- @endif -->
        @foreach($data['extra']['packages'] as $key => $value)

        <tr>
          <td>{{$value['name']}}</td>
          <td>1</td>
          <td>{{$value['package_price']}}</td>
          <td>{{$value['package_price']}}</td>
        </tr>

@endforeach
      </tbody>
    </table>

    <div class="total">
        <p><strong>Total:</strong>{{ \Currency::format($data['serviceAmount'] + $productPrice + $data['package_price']) }}

</p>
    </div>

    <div class="thank-you">
      <p>{{ setting('spacial_note') }}</p>
    </div>
  </div>

</body>
</html>
