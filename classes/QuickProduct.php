<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Product Business
 */
class QuickProduct extends QuickObject
{
    /**
     * Số lượng sản phẩm tối đa trả về
     */
    const SEARCH_LIMIT = 20;

    /**
     * Chỉ tìm sản phẩm Active
     */
    const ONLY_ACTIVE_PRODUCT = false;

    /**
     * Tìm sản phẩm
     *
     * @param string $keyword
     * @return QuickResult
     */
    public static function find($keyword)
    {
        $result = QuickValidator::productKeyword($keyword);

        if (!$result->isSuccess()) {
            return $result;
        }

        $keyword = $result->getData();

        // Ưu tiên tìm theo mã
        $products = self::findByCode($keyword);

        if (!empty($products)) {
            return QuickResult::success($products);
        }

        // Tìm theo tên nếu đủ 3 ký tự
        if (Tools::strlen($keyword) >= 3) {

            $products = self::findByName($keyword);

            if (!empty($products)) {
                return QuickResult::success($products);
            }
        }

        //return QuickResult::error('Không tìm thấy sản phẩm.');
		return QuickResult::success(array());
    }

    /**
	 * Tìm theo Reference hoặc EAN13
	 *
	 * @param string $keyword
	 * @return QuickProductInfo[]
	 */
	protected static function findByCode($keyword)
	{
		$query = new DbQuery();

		$query->select("
			p.id_product,
			p.reference,
			p.ean13,
			pl.name,
			IFNULL(sa.quantity,0) quantity,
			i.id_image
		");

		$query->from('product', 'p');

		$query->innerJoin(
			'product_lang',
			'pl',
			'pl.id_product = p.id_product
			AND pl.id_lang='.(int)self::language()->id.'
			AND pl.id_shop='.(int)self::shop()->id
		);

		$query->leftJoin(
			'stock_available',
			'sa',
			'sa.id_product = p.id_product
			AND sa.id_product_attribute = 0
			AND sa.id_shop='.(int)self::shop()->id
		);

		$query->leftJoin(
			'image',
			'i',
			'i.id_product = p.id_product
			AND i.cover = 1'
		);		

		$keyword = pSQL($keyword);

		$query->where('p.active = 1');
		$query->where(
			'(p.reference = "'.$keyword.'" OR p.ean13 = "'.$keyword.'")'
		);
		$rows = self::db()->executeS($query);

		return self::formatRows($rows);
	}

	/**
	 * Tìm theo tên
	 *
	 * @param string $keyword
	 * @return QuickProductInfo[]
	 */
	protected static function findByName($keyword)
	{
		$query = new DbQuery();

		$query->select("
			p.id_product,
			p.reference,
			p.ean13,
			pl.name,
			IFNULL(sa.quantity,0) quantity,
			i.id_image
		");

		$query->from('product', 'p');

		$query->innerJoin(
			'product_lang',
			'pl',
			'pl.id_product=p.id_product
			AND pl.id_lang='.(int)self::language()->id.'
			AND pl.id_shop='.(int)self::shop()->id
		);

		$query->leftJoin(
			'stock_available',
			'sa',
			'sa.id_product=p.id_product
			AND sa.id_product_attribute=0
			AND sa.id_shop='.(int)self::shop()->id
		);

		$query->leftJoin(
			'image',
			'i',
			'i.id_product=p.id_product
			AND i.cover=1'
		);

		$query->where('p.active=1');

		$query->where(
			'pl.name LIKE "%'.pSQL($keyword).'%"'
		);

		$query->orderBy('pl.name ASC');

		$query->limit(self::SEARCH_LIMIT);

		$rows=self::db()->executeS($query);

		return self::formatRows($rows);
	}

   /**
	 * SQL -> QuickProductInfo
	 *
	 * @param array $row
	 * @return QuickProductInfo
	 */
	protected static function formatRow(array $row)
	{
		$price = Product::getPriceStatic(
			(int)$row['id_product'],
			true
		);

		$image='';

		if (!empty($row['id_image']))
		{
			$image=self::link()->getImageLink(
				Tools::link_rewrite($row['name']),
				(int)$row['id_image'],
				'home_default'
			);
		}

		return new QuickProductInfo(

			(int)$row['id_product'],

			$row['reference'],

			$row['ean13'],

			$row['name'],

			(float)$price,

			(int)$row['quantity'],

			$image
		);
	}

    /**
     * Chuẩn hóa danh sách
     *
     * @param array $rows
     * @return QuickProductItem[]
     */
   protected static function formatRows(array $rows)
	{
		$products=array();

		foreach($rows as $row)
		{
			$products[]=self::formatRow($row);
		}

		return $products;
	}
}