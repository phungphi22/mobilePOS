<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Base class của DT Mobile POS
 */
abstract class QuickObject
{
    /**
     * Db singleton
     *
     * @return Db
     */
    protected static function db()
    {
        return Db::getInstance();
    }

    /**
     * Context hiện tại
     *
     * @return Context
     */
    protected static function context()
    {
        return Context::getContext();
    }

    /**
     * Shop hiện tại
     *
     * @return Shop
     */
    protected static function shop()
    {
        return self::context()->shop;
    }

    /**
     * Language hiện tại
     *
     * @return Language
     */
    protected static function language()
    {
        return self::context()->language;
    }
	
	/**
	 * Link helper
	 *
	 * @return Link
	 */
	protected static function link()
	{
		return self::context()->link;
	}

    /**
     * Employee hiện tại
     *
     * @return Employee
     */
    protected static function employee()
    {
        return self::context()->employee;
    }

    /**
     * Thực thi DbQuery, trả về nhiều dòng
     *
     * @param DbQuery $query
     * @return array
     */
    protected static function executeQuery(DbQuery $query)
    {
        return self::db()->executeS($query);
    }

    /**
     * Thực thi DbQuery, trả về một dòng
     *
     * @param DbQuery $query
     * @return array|false
     */
    protected static function executeRow(DbQuery $query)
    {
        return self::db()->getRow($query);
    }

    /**
     * Escape chuỗi SQL
     *
     * @param string $value
     * @return string
     */
    protected static function escape($value)
    {
        return pSQL(trim($value));
    }

    /**
     * Ghi log
     *
     * @param string $message
     * @param int $severity
     */
    protected static function log($message, $severity = 1)
    {
        PrestaShopLogger::addLog(
            '[DTMobilePOS] '.$message,
            (int)$severity
        );
    }
}