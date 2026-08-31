<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuickOrder extends QuickObject
{
    /**
     * Tạo Order
     *
     * @param Cart $cart
     * @param Customer $customer
     *
     * @return QuickResult
     */
    public static function create(Cart $cart, Customer $customer)
    {
        if (!Validate::isLoadedObject($cart)) {
            return QuickResult::error('Cart không hợp lệ.');
        }

        if (!Validate::isLoadedObject($customer)) {
            return QuickResult::error('Customer không hợp lệ.');
        }

        // Chuẩn bị địa chỉ
        $result = QuickCustomer::prepare($customer);

        if (!$result->isSuccess()) {
            return $result;
        }

        /** @var Address $address */
        $address = $result->getData();

        // Carrier mặc định
        $result = QuickCarrier::getDefault();

        if (!$result->isSuccess()) {
            return $result;
        }

        /** @var Carrier $carrier */
        $carrier = $result->getData();

        // Cập nhật Cart
        $cart->id_customer = (int)$customer->id;
        $cart->secure_key = $customer->secure_key;
        $cart->id_address_delivery = (int)$address->id;
        $cart->id_address_invoice = (int)$address->id;
        $cart->id_carrier = (int)$carrier->id;

        if (!$cart->update()) {
            return QuickResult::error('Không cập nhật được Cart.');
        }

        // Payment Module
        $module = Module::getInstanceByName('dtmobilepos');

        if (!Validate::isLoadedObject($module)) {
            return QuickResult::error('Không tải được module dtmobilepos.');
        }

        // Tạo Order
        $module->validateOrder(
            (int)$cart->id,
            (int)Configuration::get('PS_OS_BANKWIRE'),
            (float)$cart->getOrderTotal(true),
            'Chuyển khoản',
            null,
            array(),
            (int)$cart->id_currency,
            false,
            $customer->secure_key
        );

        if (empty($module->currentOrder)) {
            return QuickResult::error('Không tạo được Order.');
        }

        $order = new Order((int)$module->currentOrder);

        if (!Validate::isLoadedObject($order)) {
            return QuickResult::error('Không tải được Order.');
        }

        return QuickResult::success($order);
    }
}