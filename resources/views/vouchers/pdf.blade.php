<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voucher PDF</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      color: #333;
    }

    .container {
      width: 90%;
      margin: auto;
      padding: 20px;
      border: 1px solid #ddd;
    }

    .header,
    .sub-header {
      text-align: center;
      background-color: #504dff;
      color: white;
      padding: 10px;
      font-weight: bold;
    }

    .sub-header {
      margin-top: 5px;
    }

    .hotel-name {
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      margin-top: 10px;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .info-table td {
      padding: 10px;

    }

    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Company Header -->
    <div class="header">
      {{ $voucher->company->name }}
    </div>

    <!-- Booking Details -->
    <table class="info-table">
      <tr>
        <td><strong>Date:</strong> {{ now()->format('M d, Y h:i A') }}</td>
        <td><strong>Internal Confirm:</strong> {{ $voucher->internal_confirmation }}</td>
      </tr>
    </table>

    <div class="sub-header">
      No. {{ str_pad($voucher->id, 6, '0', STR_PAD_LEFT) }}
    </div>

    <!-- Hotel Name -->
    <div class="hotel-name">
      {{ strtoupper($voucher->hotel->name) }} HOTEL
    </div>

    <!-- Voucher Details -->
    <table class="info-table">
      <tr>
        <td><strong>Confirmation No.:</strong></td>
        <td>{{ $voucher->hcn }}</td>
      </tr>
      <tr>
        <td><strong>Holder Name:</strong></td>
        <td>{{ $voucher->client_name }}</td>
      </tr>
      <tr>
        <td><strong>Arrival Date:</strong></td>
        <td>{{ date('Y-m-d', strtotime($voucher->arrival_date)) }}</td>
      </tr>
      <tr>
        <td><strong>Departure Date:</strong></td>
        <td>{{ date('Y-m-d', strtotime($voucher->departure_date)) }}</td>
      </tr>
      <tr>
        <td><strong>Nights:</strong></td>
        <td>{{ $voucher->nights }}</td>
      </tr>
      <tr>
        <td><strong>Meal:</strong></td>
        <td>{{ $voucher->meal->meal_type }}</td>
      </tr>
      <tr>
        <td><strong>Room:</strong></td>
        <td>{{ ucfirst($voucher->room->room_type) }}</td>
      </tr>
      <tr>
        <td><strong>Rooms Count:</strong></td>
        <td>{{ $voucher->rooms_count }}</td>
      </tr>
      <tr>
        <td><strong>View:</strong></td>
        <td>{{ ucfirst($voucher->view) }}</td>
      </tr>
      <tr>
        <td><strong>Pax:</strong></td>
        <td>{{ $voucher->pax }}</td>
      </tr>
      <tr>
        <td><strong>Adults:</strong></td>
        <td>{{ $voucher->adults }}</td>
      </tr>
      <tr>
        <td><strong>Children:</strong></td>
        <td>{{ $voucher->children }}</td>
      </tr>
    </table>
    <!-- Footer -->
    <div class="footer">
      Thank You.
    </div>
  </div>
</body>

</html>