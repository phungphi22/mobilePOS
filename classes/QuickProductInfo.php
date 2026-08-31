<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * DT Mobile POS
 *
 * Immutable Value Object
 */
final class QuickProductInfo
{
    /**
     * @var int
     */
    private $idProduct;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $ean13;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $price;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $image;

    /**
     * Constructor
     */
    public function __construct(
        $idProduct,
        $reference,
        $ean13,
        $name,
        $price,
        $quantity,
        $image
    ) {
        $this->idProduct = (int)$idProduct;
        $this->reference = (string)$reference;
        $this->ean13 = (string)$ean13;
        $this->name = (string)$name;
        $this->price = (float)$price;
        $this->quantity = (int)$quantity;
        $this->image = (string)$image;
    }

    public function getIdProduct()
    {
        return $this->idProduct;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getEan13()
    {
        return $this->ean13;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getImage()
    {
        return $this->image;
    }
	
	public function toArray()
	{
		return array(
			'id_product' => $this->idProduct,
			'reference'  => $this->reference,
			'ean13'      => $this->ean13,
			'name'       => $this->name,
			'price'      => $this->price,
			'quantity'   => $this->quantity,
			'image'      => $this->image,
		);
	}
}