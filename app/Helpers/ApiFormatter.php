<?php

namespace App\Helpers;

class ApiFormatter
{
    protected static $response = [
        "status" => NULL,
        "massage" => NULL,
        "data" => NULL,
    ];

    public static function sendResponse($status = NULL, $massage = NULL, $data = [])
  {
    self::$response['status'] = $status;
    self::$response['massage'] = $massage;
    self::$response['data'] = $data;
    return response()->json(self::$response, self::$response['status']);
  }
}

 



?>