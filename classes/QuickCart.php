<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Mobile POS Cart Business
 */
class QuickCart extends QuickObject
{
    /**
     * Tạo Cart mới
     *
     * @return QuickResult
     */
    public static function create()
    {
        $context = self::context();

        $cart = new Cart();

        $cart->id_lang       = (int)$context->language->id;
        $cart->id_currency   = (int)$context->currency->id;
        $cart->id_shop       = (int)$context->shop->id;
        $cart->id_shop_group = (int)$context->shop->id_shop_group;
        //$cart->secure_key    = md5(uniqid(mt_rand(), true));

        if (!$cart->add()) {
            return QuickResult::error('Không thể tạo giỏ hàng.');
        }

        return QuickResult::success($cart);
    }

   /**
	 * Thêm sản phẩm
	 *
	 * @param int $idCart
	 * @param int $idProduct
	 * @param int $qty
	 *
	 * @return QuickResult
	 */
	public static function add($idCart, $idProduct, $qty)
	{
		$cart = self::load($idCart);
		
		$product = new Product((int)$idProduct);

		if (!Validate::isLoadedObject($product)) {
			return QuickResult::error('Sản phẩm không tồn tại.');
		}

		if (!$cart) {
			return QuickResult::error('Giỏ hàng không tồn tại.');
		}

		if (!$cart->updateQty(
			(int)$qty,
			(int)$idProduct
		)) {
			return QuickResult::error('Không thể thêm sản phẩm.');
		}

		return QuickResult::success($cart);
	}

	/**
	 * Cập nhật số lượng
	 *
	 * @param int $idCart
	 * @param int $idProduct
	 * @param int $qty
	 *
	 * @return QuickResult
	 */
	public static function update($idCart, $idProduct, $qty)
	{
		$cart = self::load($idCart);

		if (!$cart) {
			return QuickResult::error('Giỏ hàng không tồn tại.');
		}

		$products = $cart->getProducts();

		$currentQty = 0;

		foreach ($products as $product) {

			if ((int)$product['id_product'] == (int)$idProduct) {

				$currentQty = (int)$product['cart_quantity'];

				break;
			}
		}

		$qty = (int)$qty;

		if ($qty <= 0) {
			return self::remove($idCart, $idProduct);
		}

		if ($qty == $currentQty) {
			return QuickResult::success($cart);
		}

		$delta = abs($qty - $currentQty);

		$operator = ($qty > $currentQty)
			? 'up'
			: 'down';

		if (!$cart->updateQty(
			$delta,
			(int)$idProduct,
			null,
			false,
			$operator
		)) {
			return QuickResult::error('Không thể cập nhật số lượng.');
		}

		return QuickResult::success($cart);
	}

   /**
	 * Nạp Cart
	 *
	 * @param int $idCart
	 * @return Cart|false
	 */
	protected static function load($idCart)
	{
		$cart = new Cart((int)$idCart);

		if (!Validate::isLoadedObject($cart)) {
			return false;
		}

		return $cart;
	}
	
	/**
	 * Xóa sản phẩm khỏi Cart
	 *
	 * @param int $idCart
	 * @param int $idProduct
	 *
	 * @return QuickResult
	 */
	public static function remove($idCart, $idProduct)
	{
		$cart = self::load($idCart);

		if (!$cart) {
			return QuickResult::error('Giỏ hàng không tồn tại.');
		}

		$products = $cart->getProducts();

		foreach ($products as $product) {

			if ((int)$product['id_product'] != (int)$idProduct) {
				continue;
			}

			if (!$cart->updateQty(
				(int)$product['cart_quantity'],
				(int)$idProduct,
				null,
				false,
				'down'
			)) {
				return QuickResult::error('Không thể xóa sản phẩm.');
			}

			break;
		}

		return QuickResult::success($cart);
	}
	
	/**
	 * Xóa toàn bộ sản phẩm khỏi Cart
	 *
	 * @param int $idCart
	 * @return QuickResult
	 */
	public static function clear($idCart)
	{
		$cart = self::load($idCart);

		if (!$cart) {
			return QuickResult::error('Giỏ hàng không tồn tại.');
		}

		foreach ($cart->getProducts() as $product) {

			if (!$cart->updateQty(
				(int)$product['cart_quantity'],
				(int)$product['id_product'],
				null,
				false,
				'down'
			)) {
				return QuickResult::error(
					'Không thể xóa giỏ hàng.'
				);
			}
		}

		return QuickResult::success($cart);
	}
	
	/**
	 * Chuẩn hóa dữ liệu Cart
	 *
	 * @param Cart $cart
	 * @return array
	 */
	protected static function buildSummary(Cart $cart)
	{
		$rows = array();
		$countQuantity = 0;
		foreach ($cart->getProducts() as $product) {
			$imageInfo = Image::getCover((int)$product['id_product']);

			$image = '';

			if ($imageInfo) {

				$image = self::link()->getImageLink(

					Tools::link_rewrite($product['name']),

					(int)$imageInfo['id_image'],

					'home_default'

				);
			}
			
			$rows[] = array(

				'id_product' => (int)$product['id_product'],

				'name' => $product['name'],

				'reference' => $product['reference'],
				'image' => $image,

				'quantity' => (int)$product['cart_quantity'],

				'unit_price_tax_excl' => (float)$product['price'],

				'unit_price_tax_incl' => (float)$product['price_wt'],

				'total_price_tax_excl' => (float)$product['total'],

				'total_price_tax_incl' => (float)$product['total_wt']

			);
			$countQuantity += (int)$product['cart_quantity'];
		}

		return array(

			'products' => $rows,

			'count_products' => count($rows),
			'count_quantity' => $countQuantity,

			'total_products' => (float)$cart->getOrderTotal(
				true,
				Cart::ONLY_PRODUCTS
			),

			'total_shipping' => (float)$cart->getOrderTotal(
				true,
				Cart::ONLY_SHIPPING
			),

			'total_discounts' => (float)$cart->getOrderTotal(
				true,
				Cart::ONLY_DISCOUNTS
			),

			'total_paid' => (float)$cart->getOrderTotal(true)

		);
	}
	
	/**
	 * Lấy thông tin Cart
	 *
	 * @param int $idCart
	 * @return QuickResult
	 */
	public static function summary($idCart)
	{
		$cart = self::load($idCart);

		if (!$cart) {
			return QuickResult::error(
				'Giỏ hàng không tồn tại.'
			);
		}

		return QuickResult::success(
			self::buildSummary($cart)
		);
	}
	
	//Begin upgrade
	    /**
     * Mã nhận diện CartRule do Mobile POS tạo.
     */
    const MOBILEPOS_DISCOUNT_PREFIX = 'DTPOS-';

    /**
     * Tìm CartRule giảm giá do Mobile POS tạo cho Cart.
     *
     * @param Cart $cart
     * @return int
     */
    public static function getMobilePosDiscountId(Cart $cart)
    {
        foreach ($cart->getCartRules() as $rule) {
            if (!empty($rule['code'])
                && strpos(
                    $rule['code'],
                    self::MOBILEPOS_DISCOUNT_PREFIX.(int)$cart->id
                ) === 0
            ) {
                return (int)$rule['id_cart_rule'];
            }
        }

        return 0;
    }

    /**
     * Xóa CartRule giảm giá của Mobile POS khỏi Cart.
     *
     * @param Cart $cart
     * @return bool
     */
    protected static function removeMobilePosDiscount(Cart $cart)
    {
        $idCartRule = self::getMobilePosDiscountId($cart);

        if (!$idCartRule) {
            return true;
        }

        if (!$cart->removeCartRule($idCartRule)) {
            return false;
        }

        $cartRule = new CartRule($idCartRule);

        if (Validate::isLoadedObject($cartRule)) {
            return (bool)$cartRule->delete();
        }

        return true;
    }

    /**
     * Đặt giảm giá bằng số tiền cố định cho Cart.
     *
     * Số tiền nhập vào được hiểu là số tiền giảm TAX INCLUDED,
     * phù hợp với tổng tiền đang hiển thị trên Mobile POS.
     *
     * @param int $idCart
     * @param float $amount
     * @return QuickResult
     */
    public static function setDiscount($idCart, $amount)
    {
        $cart = self::load($idCart);

        if (!$cart) {
            return QuickResult::error('Giỏ hàng không tồn tại.');
        }

        $amount = (float)$amount;

        /*
         * Bỏ giảm giá nếu nhập 0.
         */
        if ($amount <= 0) {
            if (!self::removeMobilePosDiscount($cart)) {
                return QuickResult::error(
                    'Không thể bỏ giảm giá.'
                );
            }

            return QuickResult::success(
                self::buildSummary($cart)
            );
        }

        /*
         * Không cho giảm giá khi Cart chưa có sản phẩm.
         */
        $totalProducts = (float)$cart->getOrderTotal(
            true,
            Cart::ONLY_PRODUCTS
        );

        if ($totalProducts <= 0) {
            return QuickResult::error(
                'Chưa có sản phẩm để áp dụng giảm giá.'
            );
        }

        /*
         * Không cho giảm lớn hơn tiền hàng.
         *
         * Làm vậy để tránh cơ chế partial_use của CartRule
         * tạo voucher dư sau khi Order được tạo.
         */
        if ($amount > $totalProducts) {
            return QuickResult::error(
                'Số tiền giảm không được lớn hơn tiền hàng.'
            );
        }

        /*
         * Nếu đã có giảm giá Mobile POS thì xóa trước.
         */
        if (!self::removeMobilePosDiscount($cart)) {
            return QuickResult::error(
                'Không thể cập nhật giảm giá hiện tại.'
            );
        }

        /*
         * Tạo CartRule theo đúng cơ chế native của PrestaShop.
         */
        $cartRule = new CartRule();

        $now = time();

        /*
         * Tên tạm.
         *
         * Tên chính thức sẽ được đổi thành:
         * giam_<so_can>_<id_order>
         * sau khi Order được tạo.
         */
        $temporaryName = 'DTPOS Discount Cart '.(int)$cart->id;

        $languages = Language::getLanguages(false);

        $cartRule->name = array();

        foreach ($languages as $language) {
            $cartRule->name[(int)$language['id_lang']] =
                $temporaryName;
        }

        $cartRule->id_customer = 0;

        $cartRule->date_from =
            date('Y-m-d H:i:s', $now);

        $cartRule->date_to =
            date('Y-m-d H:i:s', strtotime('+1 year', $now));

        $cartRule->description =
            'Discount created by DT Mobile POS';

        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->priority = 1;

        /*
         * Không cho tạo voucher dư.
         */
        $cartRule->partial_use = 0;

        /*
         * Không dùng mã voucher nhập từ khách.
         * Code này chỉ dùng nội bộ để nhận diện CartRule
         * của Mobile POS.
         */
        $cartRule->code =
            self::MOBILEPOS_DISCOUNT_PREFIX.(int)$cart->id;

        $cartRule->minimum_amount = 0;
        $cartRule->minimum_amount_tax = 0;
        $cartRule->minimum_amount_currency =
            (int)$cart->id_currency;
        $cartRule->minimum_amount_shipping = 0;

        $cartRule->country_restriction = 0;
        $cartRule->carrier_restriction = 0;
        $cartRule->group_restriction = 0;
        $cartRule->cart_rule_restriction = 0;
        $cartRule->product_restriction = 0;
        $cartRule->shop_restriction = 0;

        $cartRule->free_shipping = 0;

        /*
         * Giảm theo số tiền cố định.
         */
        $cartRule->reduction_percent = 0;
        $cartRule->reduction_amount = $amount;

        /*
         * Amount người dùng nhập được hiểu là giá đã gồm thuế,
         * vì Mobile POS đang hiển thị total_paid bằng getOrderTotal(true).
         */
        $cartRule->reduction_tax = 1;

        $cartRule->reduction_currency =
            (int)$cart->id_currency;

        $cartRule->reduction_product = 0;

        $cartRule->gift_product = 0;
        $cartRule->gift_product_attribute = 0;

        $cartRule->highlight = 0;
        $cartRule->active = 1;

        if (!$cartRule->add()) {
            return QuickResult::error(
                'Không thể tạo giảm giá.'
            );
        }

        /*
         * Gắn CartRule vào Cart bằng API native.
         */
        if (!$cart->addCartRule((int)$cartRule->id)) {

            $cartRule->delete();

            return QuickResult::error(
                'Không thể áp dụng giảm giá vào giỏ hàng.'
            );
        }

        return QuickResult::success(
            self::buildSummary($cart)
        );
    }

    /**
     * Đổi tên CartRule sau khi đã có Order.
     *
     * Tên:
     * giam_<so_can>_<id_order>
     *
     * Đồng thời cập nhật tên trong order_cart_rule.
     *
     * @param int $idCartRule
     * @param Order $order
     * @param string $apartment
     * @return bool
     */
    public static function finalizeDiscountName(
        $idCartRule,
        Order $order,
        $apartment
    ) {
        $idCartRule = (int)$idCartRule;

        if ($idCartRule <= 0) {
            return true;
        }

        $cartRule = new CartRule($idCartRule);

        if (!Validate::isLoadedObject($cartRule)) {
            return false;
        }

        $parts = explode('.', trim($apartment));

        /*
         * Với cấu trúc:
         * chungcu.toa.can
         *
         * phần cuối chính là số căn.
         */
        $apartmentNumber = trim(
            $parts[count($parts) - 1]
        );

        if ($apartmentNumber === '') {
            $apartmentNumber = trim($apartment);
        }

        $finalName =
            'giam_'
            .$apartmentNumber
            .'_'
            .(int)$order->id;

        /*
         * CartRule là multilang nên cập nhật tất cả ngôn ngữ.
         */
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $cartRule->name[(int)$language['id_lang']] =
                $finalName;
        }

        if (!$cartRule->update()) {
            return false;
        }

        /*
         * PaymentModule đã tạo OrderCartRule.
         * Đổi tên bản ghi OrderCartRule để trong đơn hàng
         * cũng hiện đúng tên cuối cùng.
         */
        foreach ($order->getCartRules() as $row) {

            if ((int)$row['id_cart_rule'] != $idCartRule) {
                continue;
            }

            if (empty($row['id_order_cart_rule'])) {
                continue;
            }

            $orderCartRule = new OrderCartRule(
                (int)$row['id_order_cart_rule']
            );

            if (!Validate::isLoadedObject($orderCartRule)) {
                return false;
            }

            $orderCartRule->name = $finalName;

            if (!$orderCartRule->update()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Xóa Cart chưa gắn với Order.
     *
     * @param int $idCart
     * @return QuickResult
     */
    public static function deleteCart($idCart)
    {
        $cart = self::load($idCart);

        if (!$cart) {
            /*
             * Cart đã không còn thì coi như đã xóa.
             */
            return QuickResult::success();
        }

        /*
         * Cart::delete() của PrestaShop tự kiểm tra
         * Cart đã gắn với Order hay chưa.
         */
        if (!$cart->delete()) {
            return QuickResult::error(
                'Không thể xóa giỏ hàng hiện tại.'
            );
        }

        return QuickResult::success();
    }
}