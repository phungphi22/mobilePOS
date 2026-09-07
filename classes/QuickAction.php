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

       //upgrade
	           /*
         * Ghi nhớ CartRule giảm giá của Mobile POS
         * trước khi Cart được chuyển thành Order.
         */
        $idCartRule = QuickCart::getMobilePosDiscountId($cart);

        $result = QuickOrder::create($cart, $customer);

        if (!$result->isSuccess()) {
            return $result;
        }

        /** @var Order $order */
        $order = $result->getData();

      /*
		 * Bây giờ đã có id_order.
		 *
		 * Đổi tên giảm giá:
		 * Giam_#<id_order>
		 */
		if ($idCartRule > 0) {
			if (!QuickCart::finalizeDiscountName(
				$idCartRule,
				$order
			)) {
				return QuickResult::error(
					'Đơn đã tạo nhưng không thể cập nhật tên giảm giá.'
				);
			}
		}

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
	 * Upgraded
	 */
	public static function searchProduct($keyword, $idCart = 0)
	{
		// Validate từ khóa
		$result = QuickValidator::productKeyword($keyword);

		if (!$result->isSuccess()) {
			return $result;
		}

		$keyword = $result->getData();

		// Business Layer
		$result = QuickProduct::find($keyword);

		if (!$result->isSuccess()) {
			return $result;
		}

		$products = $result->getData();

		/*
		 * Mobile POS không dùng combination.
		 *
		 * Loại khỏi kết quả những id_product
		 * đã tồn tại trong Cart hiện tại.
		 */
		$idCart = (int)$idCart;

		if ($idCart > 0 && !empty($products)) {

			$cart = new Cart($idCart);

			if (Validate::isLoadedObject($cart)) {

				$cartProducts = $cart->getProducts();

				$inCart = array();

				foreach ($cartProducts as $cartProduct) {
					$inCart[(int)$cartProduct['id_product']] = true;
				}

				foreach ($products as $key => $product) {

					if (isset(
						$inCart[(int)$product->getIdProduct()]
					)) {
						unset($products[$key]);
					}
				}

				/*
				 * Đưa array về index liên tục.
				 */
				$products = array_values($products);
			}
		}

		return QuickResult::success($products);
	}
	
	    /**
     * Áp dụng / thay đổi / bỏ giảm giá.
     *
     * @param int $idCart
     * @param float $amount
     * @return QuickResult
     */
    public static function setDiscount($idCart, $amount)
    {
        return QuickCart::setDiscount(
            (int)$idCart,
            (float)$amount
        );
    }

    /**
     * Xóa Cart hiện tại.
     *
     * @param int $idCart
     * @return QuickResult
     */
	  public static function deleteCart($idCart)
	{
		$idCart = (int)$idCart;

		if ($idCart <= 0) {
			return QuickResult::error('Giỏ hàng không hợp lệ.');
		}

		$cart = new Cart($idCart);

		if (!Validate::isLoadedObject($cart)) {
			return QuickResult::error('Không tìm thấy giỏ hàng.');
		}

		if ($cart->OrderExists()) {
			return QuickResult::error(
				'Giỏ hàng đã được tạo đơn, không thể xóa.'
			);
		}

		if (!$cart->delete()) {
			return QuickResult::error(
				'Không thể xóa giỏ hàng.'
			);
		}

		return QuickResult::success(true);
	}
}