<?php

if (!function_exists('send_response')) {
  function send_response($message, int $status, $data = null)
  {
    $response = [
      'message' => $message,
      'status' => $status,
    ];
    if ($data) {
      $response['data'] = $data;
    }
    return response()->json($response, $status);
  }
}

if (!function_exists('format_date')) {
  function format_date($date, $format = 'Y-m-d')
  {
    return \Carbon\Carbon::parse($date)->format($format);
  }
}

if (!function_exists('generate_random_string')) {
  function generate_random_string($length = 10)
  {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
  }
}
