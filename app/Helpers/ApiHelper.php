<?php

namespace App\Helpers;

class ApiHelper
{
    public static function success($message = null, $data = null, $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message, $code = 400)
    {return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }

    public static function saveMedia($model, $file, $collection)
    {
        if ($file) {
            $model->addMedia($file)->toMediaCollection($collection);
        }
    }
}
