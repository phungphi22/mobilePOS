<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Mobile POS Customer Business
 */
class QuickCustomer extends QuickObject
{
	/**
	 * Chuẩn hóa mã căn hộ để dùng làm khóa tìm kiếm.
	 *
	 * Ví dụ:
	 * Vin.A2.101
	 * Vin-A2-101
	 * vin/a2_101
	 *
	 * =>
	 * VINA2101
	 */
	protected static function normalizeApartment($apartment)
	{
		$apartment = Tools::strtoupper(trim($apartment));

		// chỉ giữ A-Z và 0-9
		$apartment = preg_replace('/[^A-Z0-9]/', '', $apartment);

		return $apartment;
	}
	
	protected static function apartmentToCompany($apartment)
	{
		$parts = explode('.', trim($apartment));

		if (count($parts) < 2) {
			return '';
		}

		return $parts[0].'.'.$parts[1];
	}
    /**
     * Tìm khách hàng theo số căn hộ
     *
     * @param string $apartment
     * @return QuickResult
     */
    public static function find($apartment)
    {
        $apartment = trim($apartment);
		$result = QuickValidator::apartment($apartment);

		if (!$result->isSuccess()) {
			return $result;
		}

		$apartment = $result->getData();
        if ($apartment === '') {
            return QuickResult::error('Số căn hộ không được để trống.');
        }

       $query = new DbQuery();

		/* $key = self::normalizeApartment($apartment);

		$email = Tools::strtolower($key).'@dtmobilepos.local'; */
		$email = trim($apartment);

		$email = str_replace(' ', '_', $email);

		$email .= '@dtmobilepos.local';

		$query = new DbQuery();

		$query->select('id_customer');
		$query->from('customer');
		$query->where("email = '".pSQL($email)."'");

		$idCustomer = (int)Db::getInstance()->getValue($query);

        if (!$idCustomer) {
            return QuickResult::error('CUSTOMER_NOT_FOUND');
        }

        $customer = new Customer((int)$idCustomer);

        if (!Validate::isLoadedObject($customer)) {
            return QuickResult::error('Không thể tải khách hàng.');
        }

        return QuickResult::success($customer);
    }

    /**
     * Tạo khách hàng mới
     *
     * @param string $apartment
     * @return QuickResult
     */
    public  static function create($apartment)
    {
        $customer = new Customer();

		$key = self::normalizeApartment($apartment);

		
		
		$display = trim($apartment);

		$customer->firstname = 'KH';

		$customer->lastname = $display;
		
		$customer->company = self::apartmentToCompany($apartment);

		// Email chỉ dùng nội bộ
		$email = trim($apartment);

		$email = str_replace(' ', '_', $email);

		$customer->email = $email.'@dtmobilepos.local';
		
        $customer->passwd    = Tools::encrypt(Tools::passwdGen(10));
        $customer->active    = 1;

		
        if (!$customer->add()) {
            return QuickResult::error('Không thể tạo khách hàng.');
        }

        return QuickResult::success($customer);
    }

    /**
     * Tìm hoặc tạo khách hàng
     *
     * @param string $apartment
     * @return QuickResult
     */
    public static function findOrCreate($apartment)
    {
        $result = self::find($apartment);

        if ($result->isSuccess()) {
            return $result;
        }

        if ($result->getMessage() !== 'CUSTOMER_NOT_FOUND') {
            return $result;
        }

        return self::create($apartment);
    }
	
	/**
	 * Chuẩn bị Customer để tạo Order
	 *
	 * Nếu Customer chưa có địa chỉ thì tạo địa chỉ mặc định.
	 *
	 * @param Customer $customer
	 * @return QuickResult
	 */
	public static function prepare(Customer $customer)
	{
		// Đã có địa chỉ
		$addresses = $customer->getAddresses((int)self::context()->language->id);
		

		if (!empty($addresses)) {

			$address = new Address((int)$addresses[0]['id_address']);

			if (Validate::isLoadedObject($address)) {
				return QuickResult::success($address);
			}
		}

		// Chưa có -> tạo mới
		return self::createAddress($customer);
	}
	
	/**
	 * Tạo địa chỉ mặc định
	 *
	 * @param Customer $customer
	 * @return QuickResult
	 */
	protected static function createAddress(Customer $customer)
	{
		$address = new Address();

		$address->id_customer = (int)$customer->id;

		$address->alias = 'NR';

		$address->firstname = $customer->firstname;
		$address->lastname  = $customer->lastname;
		$address->company = $customer->company;
		$address->address1 = 'đang cập nhật';

		$address->city = 'Hà Nội';

		$address->phone = '000000000';

		$address->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');

		// Nếu quốc gia mặc định yêu cầu mã bưu điện
		$country = new Country($address->id_country);

		if (Validate::isLoadedObject($country) && $country->need_zip_code) {
			$address->postcode = str_replace(
				'N',
				'0',
				$country->zip_code_format
			);

			if (empty($address->postcode)) {
				$address->postcode = '100000';
			}
		}

		if (!$address->add()) {
			return QuickResult::error(
				'Không thể tạo địa chỉ.'
			);
		}

		return QuickResult::success($address);
	}
}