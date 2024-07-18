<?php

function _200($data = null, $status = true, $message = 'Success', $code = 200)
{
    return response()->json([
        'status'      => $status,
        'status_code' => $code,
        'message'     => $message,
        'data'        => $data,
    ], $code);
}
function _400($message = 'Bad Request', $code = 400)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _401($message = 'Unauthorized', $code = 401)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _403($message = 'Forbidden', $code = 403)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _404($message = 'Not Found', $code = 404)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _406($message = 'Not Acceptable', $code = 406)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _500($message = 'Something went wrong. Please try again later', $code = 500)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function _411($message = 'update your app', $code = 411)
{
    return response()->json([
        'status'      => false,
        'status_code' => $code,
        'message'     => $message,
        'data'        => null,
    ], $code);
}

function datatable_return($data)
{
    return response()->json($data, 200);
}

if (!function_exists('jsonSuccess')) {
    function jsonSuccess($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'result' => true,
            'msg'    => $message,
            'data'   => $data,
        ], $code);
    }
}

if (!function_exists('jsonError')) {
    function jsonError($message = '', $code = 500, $data = [])
    {
        return response()->json([
            'result' => false,
            'msg'    => $message,
            'data'   => $data,
        ], $code);
    }
}
