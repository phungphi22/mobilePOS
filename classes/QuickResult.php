<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Business Result Object
 */
class QuickResult
{
    /**
     * Thành công hay thất bại
     *
     * @var bool
     */
    private $success = false;

    /**
     * Thông báo
     *
     * @var string
     */
    private $message = '';

    /**
     * Dữ liệu trả về
     *
     * @var mixed
     */
    private $data = null;

    /**
     * Mã lỗi
     *
     * @var int
     */
    private $code = 0;

    /**
     * Constructor
     */
    public function __construct(
        $success = false,
        $message = '',
        $data = null,
        $code = 0
    ) {
        $this->success = (bool)$success;
        $this->message = $message;
        $this->data = $data;
        $this->code = (int)$code;
    }

    /**
     * Success
     */
    public static function success($data = null, $message = '')
    {
        return new self(
            true,
            $message,
            $data
        );
    }

    /**
     * Error
     */
    public static function error($message, $code = 0)
    {
        return new self(
            false,
            $message,
            null,
            $code
        );
    }

	/**
	 * Thành công?
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return $this->success;
	}

	/**
	 * Lấy thông báo
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Lấy dữ liệu
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Lấy mã lỗi
	 *
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}
}