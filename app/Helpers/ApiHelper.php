<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ApiHelper
{
    public static function deleteMedia($model, $field): void
    {
        if (!$model->$field)
            return;


        if (Storage::disk('public')->exists($model->$field))
          Storage::disk('public')->delete($model->$field);

    $model->update([$field => null]);
    }

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
