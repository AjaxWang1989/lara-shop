<?php

namespace App\Services;

use Illuminate\Validation\ValidationException as ExceptionContract;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Log;

/**
 * 响应
 */
class ApiResponse
{
    /**
     * api异常返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  \Exception  $exception 异常
     * @param  integer     $code  状态
     * @return array
     */
    public static function exceptionApi(Exception $exception, $code = 200)
    {
        $error = '';
        if ($exception instanceof ExceptionContract) {
            $error = $exception->getMessage();
        }

        Log::info(
            "response",
            [
                'code'     => $exception->getCode(),
                'response' => $exception->getMessage(),
                'trace'    => $exception->getTraceAsString()
            ]
        );

        return [
            'hasError' => true,
            'success'  => false,
            'error'    => is_string($error)? ['message' =>$error] : $error,
            'code'     => $code,
            'data'     => [],
        ];
    }

    /**
     * Api错误返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  array  $error 异常
     * @param  integer     $code  状态
     * @return array
     */
    public static function errorApi($error, $code = 400)
    {
        return [
            'hasError' => true,
            'success'  => false,
            'error'    => is_string($error)? ['message' =>$error] : $error,
            'code'     => $code,
            'data'     => [],
        ];
    }

    /**
     * api返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  mixed     $content 返回内容
     * @param  integer   $status  状态
     * @return array
     */
    public static function api($content, $code = 200)
    {
        if ($content instanceof Arrayable) {
            $content = $content->toArray();
        }

        $result = [
            'hasError' => false,
            'success'  => true,
            'error'    => '',
            'code'     => $code,
        ];

        return $result + ['data' => $content];
    }
}
