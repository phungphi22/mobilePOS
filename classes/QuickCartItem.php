<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Cart Item
 */
final class QuickCartItem
{
    /**
     * @var QuickProductInfo
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * Constructor
     *
     * @param QuickProductInfo $product
     * @param int $quantity
     */
    public function __construct(
        QuickProductInfo $product,
        $quantity
    ) {
        $this->product = $product;
        $this->quantity = max(1, (int)$quantity);
    }

    /**
     * @return QuickProductInfo
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->product->getPrice();
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->getUnitPrice() * $this->quantity;
    }

    /**
     * Tạo CartItem mới với số lượng mới
     *
     * Immutable
     *
     * @param int $quantity
     * @return QuickCartItem
     */
    public function withQuantity($quantity)
    {
        return new self(
            $this->product,
            $quantity
        );
    }
}