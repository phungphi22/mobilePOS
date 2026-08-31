<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Product DTO
 */
final class QuickProductItem
{
    /**
     * @var int
     */
    private $id;

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

    public function __construct(
        $id,
        $reference,
        $ean13,
        $name
    ) {
        $this->id        = (int)$id;
        $this->reference = (string)$reference;
        $this->ean13     = (string)$ean13;
        $this->name      = (string)$name;
    }

    public function getId()
    {
        return $this->id;
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
	public function toArray()
	{
		return array(
			'id_product' => $this->id,
			'reference'  => $this->reference,
			'ean13'      => $this->ean13,
			'name'       => $this->name,
		);
	}
}