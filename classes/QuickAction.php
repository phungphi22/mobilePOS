<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Application Layer
 *
 * Điều phối Business Layer.
 * Không chứa SQL.
 * Không xuất JSON.
 * Không thao tác trực tiếp với Db.
 */
class QuickAction extends QuickObject
{
    /**
     * Tạo Cart mới
     *
     * @return QuickResult
     */
	public static function createCart()
	{
		$result = QuickCart::create();

		if (!$result->isSuccess()) {
			return $result;
		}

		/** @var Cart $cart */
		$cart = $result->getData();

		return QuickResult::success(array(
			'id_cart' => (int)$cart->id
		));
	}

    /**
     * Thêm sản phẩm vào Cart
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $quantity
     *
     * @return QuickResult
     */
   public static function addProduct($idCart, $idProduct, $quantity)
	{
		$idCart = (int)$idCart;

		if ($idCart <= 0) {

			$result = QuickCart::create();

			if (!$result->isSuccess()) {
				return $result;
			}

			$cart = $result->getData();

			$idCart = (int)$cart->id;
		}

		$result = QuickCart::add(
			$idCart,
			(int)$idProduct,
			(int)$quantity
		);

		if (!$result->isSuccess()) {
			return $result;
		}

		return QuickResult::success(array(
			'id_cart' => $idCart
		));
	}

    /**
     * Cập nhật số lượng
     *
     * @param int $idCart
     * @param int $idProduct
     * @param int $quantity
     *
     * @return QuickResult
     */
    public static function updateQuantity($idCart, $idProduct, $quantity)
    {
        return QuickCart::update(
            (int)$idCart,
            (int)$idProduct,
            (int)$quantity
        );
    }

    /**
     * Xóa sản phẩm khỏi Cart
     *
     * @param int $idCart
     * @param int $idProduct
     *
     * @return QuickResult
     */
    public static function removeProduct($idCart, $idProduct)
    {
        return QuickCart::remove(
            (int)$idCart,
            (int)$idProduct
        );
    }

    /**
     * Tóm tắt Cart
     *
     * @param int $idCart
     *
     * @return QuickResult
     */
    public static function summary($idCart)
    {
        return QuickCart::summary(
            (int)$idCart
        );
    }

    /**
     * Tạo Order
     *
     * @param int $idCart
     * @param string $apartment
     *
     * @return QuickResult
     */
    public static function createOrder($idCart, $apartment)
    {
        // Tìm hoặc tạo Customer
        $result = QuickCustomer::findOrCreate($apartment);

        if (!$result->isSuccess()) {
            return $result;
        }

        /** @var Customer $customer */
        $customer = $result->getData();

        // Load Cart
        $cart = new Cart((int)$idCart);

        if (!Validate::isLoadedObject($cart)) {
            return QuickResult::error(
                'Giỏ hàng không tồn tại.'
            );
        }

        $result = QuickOrder::create($cart, $customer);

		if (!$result->isSuccess()) {
			return $result;
		}

		/** @var Order $order */
		$order = $result->getData();

		return QuickResult::success(array(
			'id_order'  => (int)$order->id,
			'reference' => $order->reference
		));
    }
	
	/**
	 * Tìm sản phẩm
	 *
	 * @param string $keyword
	 *
	 * @return QuickResult
	 */
	public static function searchProduct($keyword)
	{
		// Validate từ khóa
		$result = QuickValidator::productKeyword($keyword);

		if (!$result->isSuccess()) {
			return $result;
		}

		$keyword = $result->getData();

		// Business Layer
		return QuickProduct::find($keyword);
	}
}