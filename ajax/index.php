<?php

/**
 * Mobile POS Ajax Entry Point
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config/config.inc.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    QuickResponse::error('Method không được hỗ trợ.');
}
/**
 * Chỉ cho phép nhân viên Back Office sử dụng
 */
$context = Context::getContext();

if (!Validate::isLoadedObject($context->employee)) {
    QuickResponse::error('Bạn chưa đăng nhập Back Office.');
}

/* $token = Tools::getValue('token');

if ($token !== Tools::getAdminTokenLite('AdminModules')) {
    QuickResponse::error('Token không hợp lệ.');
} */

/**
 * Load Framework
 */
require_once dirname(__FILE__) . '/../classes/loader.php';

/**
 * Đọc Action
 */
$action = Tools::getValue('action');

if (!$action) {
    QuickResponse::error('Thiếu action.');
}

/**
 * Kết quả xử lý
 *
 * @var QuickResult
 */
$result = null;

/**
 * Router
 */
switch ($action) {

    /**
     * Tạo Cart
     */
    case 'createCart':

        $result = QuickAction::createCart();

        break;

    /**
     * Thêm sản phẩm
     */
    case 'addProduct':

        $idCart = (int)Tools::getValue('id_cart');

        $idProduct = (int)Tools::getValue('id_product');

        $quantity = (int)Tools::getValue('quantity', 1);

        $result = QuickAction::addProduct(
            $idCart,
            $idProduct,
            $quantity
        );

        break;

    /**
     * Cập nhật số lượng
     */
    case 'updateQuantity':

        $idCart = (int)Tools::getValue('id_cart');

        $idProduct = (int)Tools::getValue('id_product');

        $quantity = (int)Tools::getValue('quantity');

        $result = QuickAction::updateQuantity(
            $idCart,
            $idProduct,
            $quantity
        );

        break;

    /**
     * Xóa sản phẩm
     */
    case 'removeProduct':

        $idCart = (int)Tools::getValue('id_cart');

        $idProduct = (int)Tools::getValue('id_product');

        $result = QuickAction::removeProduct(
            $idCart,
            $idProduct
        );

        break;
	    /**
		 * Xem tóm tắt Cart
		 */
		case 'summary':

			$idCart = (int)Tools::getValue('id_cart');

			$result = QuickAction::summary(
				$idCart
			);

			break;

		/**
		 * Tìm sản phẩm
		 */
		case 'searchProduct':

			$keyword = Tools::getValue('keyword');

			$result = QuickAction::searchProduct(
				$keyword
			);

		break;

		/**
		 * Tạo Order
		 */
		case 'createOrder':

			$idCart = (int)Tools::getValue('id_cart');

			$apartment = trim(
				Tools::getValue('apartment')
			);

			$result = QuickAction::createOrder(
				$idCart,
				$apartment
			);

			break;

		/**
		 * Action không hợp lệ
		 */
		default:

			$result = QuickResult::error(
				'Action không hợp lệ.'
			);

			break;
	}

	/**
	 * Đảm bảo luôn có QuickResult
	 */
	if (!$result instanceof QuickResult) {

		QuickResponse::error(
			'Kết quả xử lý không hợp lệ.'
		);
	}

	/**
	 * Xuất JSON
	 */
	if ($result->isSuccess()) {

		QuickResponse::success(
			$result->getData()
		);
	}

	QuickResponse::error(
		$result->getMessage()
	);