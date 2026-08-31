<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Ajax Response Helper
 */
final class QuickAjax
{
    /**
     * Không cho phép khởi tạo
     */
    private function __construct()
    {
    }

    /**
     * Trả JSON từ QuickResult
     *
     * @param QuickResult $result
     */
   public static function response(QuickResult $result)
{
    header('Content-Type: application/json; charset=utf-8');

    die(json_encode(
        array(
            'success' => $result->isSuccess(),
            'message' => $result->getMessage(),
            'data'    => self::normalize($result->getData())
        ),
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    ));
}

    /**
     * Chuẩn hóa dữ liệu trước khi xuất JSON
     *
     * @param mixed $data
     * @return mixed
     */
    private static function normalize($data)
    {
        // Một object có toArray()
        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        // Mảng
        if (is_array($data)) {

            $result = array();

            foreach ($data as $key => $value) {
                $result[$key] = self::normalize($value);
            }

            return $result;
        }

        return $data;
    }
}