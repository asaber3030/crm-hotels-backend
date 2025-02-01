<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 20px;
    }

    .container {
      background: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header {
      background: #007bff;
      color: #ffffff;
      padding: 10px;
      text-align: center;
      border-radius: 10px 10px 0 0;
    }

    .content {
      padding: 20px;
    }

    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
      color: #666;
    }

    .attachments {
      margin-top: 10px;
      padding: 10px;
      background: #f9f9f9;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h2>{{ $subject }}</h2>
    </div>
    <div class="content">
      <p><strong>Type:</strong> {{ $type }}</p>
      <p><strong>From:</strong> {{ $from }}</p>
      <p><strong>To:</strong> {{ $to }}</p>

      <p><strong>CC:</strong></p>
      <div style="display: flex; flex-wrap: wrap; gap: 5px;">
        @if (!empty($cc) && count($cc) > 0)
          @foreach ($cc as $item)
            <p style="margin-right: 5px; padding: 5px; border: 1px solid #ddd; border-radius: 5px;">
              {{ $item }}
            </p>
          @endforeach
        @endif
      </div>
      <hr>
      <p>{{ $body_content }}</p>
    </div>

    <div>
      @if (!empty($file))
        <h3>Attachment:</h3>
        <a href="{{ $file['url'] }}" download>
          {{ $file['name'] }}
        </a>
      @endif
    </div>

    <div class="footer">
      <p>Thanks, <br>{{ config('app.name') }}</p>
    </div>
  </div>
</body>

</html>
