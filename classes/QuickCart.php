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
}