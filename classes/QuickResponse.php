<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuickResponse
{
    /**
     * Xuất JSON thành công
     *
     * @param mixed $data
     */
    public static function success($data = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        die(Tools::jsonEncode(array(
            'success' => true,
            'data'    => $data
        )));
    }

    /**
     * Xuất JSON lỗi
     *
     * @param string $message
     */
    public static function error($message)
    {
        header('Content-Type: application/json; charset=utf-8');

        die(Tools::jsonEncode(array(
            'success' => false,
            'message' => $message
        )));
    }
}