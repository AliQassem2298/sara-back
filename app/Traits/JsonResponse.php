<?php

namespace App\Traits;

trait JsonResponse
{
    public function jsonResponse($data = null, $message = null, $status = null)
    {
        return response()->json(['data' => $data, 'message' => $message, 'status' => $status]);
    }
}
