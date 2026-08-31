<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Business Validator
 */
final class QuickValidator
{
    /**
     * Không cho phép khởi tạo
     */
    private function __construct()
    {
    }

    /**
     * Kiểm tra bắt buộc
     *
     * @param mixed  $value
     * @param string $field
     *
     * @return QuickResult
     */
    public static function required($value, $field)
    {
        if (trim((string)$value) === '') {
            return QuickResult::error($field.' không được để trống.');
        }

        return QuickResult::success();
    }

    /**
     * Kiểm tra từ khóa tìm sản phẩm
     *
     * @param string $keyword
     *
     * @return QuickResult
     */
    public static function productKeyword($keyword)
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return QuickResult::error('Vui lòng nhập mã hoặc tên sản phẩm.');
        }

        if (Tools::strlen($keyword) > 128) {
            return QuickResult::error('Từ khóa quá dài.');
        }

        return QuickResult::success($keyword);
    }

    /**
     * Kiểm tra số lượng
     *
     * @param mixed $quantity
     *
     * @return QuickResult
     */
    public static function quantity($quantity)
    {
        if (!Validate::isUnsignedInt($quantity)) {
            return QuickResult::error('Số lượng không hợp lệ.');
        }

        if ((int)$quantity <= 0) {
            return QuickResult::error('Số lượng phải lớn hơn 0.');
        }

        return QuickResult::success((int)$quantity);
    }

    /**
     * Kiểm tra mã căn hộ
     *
     * @param string $apartment
     *
     * @return QuickResult
     */
    public static function apartment($apartment)
    {
        $apartment = trim($apartment);

        if ($apartment === '') {
            return QuickResult::error('Vui lòng nhập số căn hộ.');
        }

        if (Tools::strlen($apartment) > 32) {
            return QuickResult::error('Số căn hộ quá dài.');
        }

        return QuickResult::success($apartment);
    }
}