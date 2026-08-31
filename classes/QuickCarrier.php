<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Mobile POS Carrier Business
 */
class QuickCarrier extends QuickObject
{
    /**
     * Lấy Carrier mặc định
     *
     * @return QuickResult
     */
    public static function getDefault()
    {
        $context = self::context();

       $carrier = new Carrier(
			(int)Configuration::get('PS_CARRIER_DEFAULT')
		);

		if (Validate::isLoadedObject($carrier)) {
			return QuickResult::success($carrier);
		}

        if (empty($carriers)) {
            return QuickResult::error(
                'Không có Carrier khả dụng.'
            );
        }

        foreach ($carriers as $row) {

            $carrier = new Carrier(
                (int)$row['id_carrier']
            );

            if (Validate::isLoadedObject($carrier)) {
                return QuickResult::success($carrier);
            }
        }

        return QuickResult::error(
            'Không tải được Carrier.'
        );
    }
}