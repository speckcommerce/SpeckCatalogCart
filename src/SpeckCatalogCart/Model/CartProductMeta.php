<?php

namespace SpeckCatalogCart\Model;

class CartProductMeta
{
    protected $parentOptionId;
    protected $productId;
    protected $itemNumber;
    protected $parentOptionName;
    protected $flatOptions = array();
    protected $image;
    protected $uom;

    public function __construct(array $config = array())
    {
        if (count($config)) {
           $this->parentOptionId   = isset($config['parent_option_id'])   ? $config['parent_option_id']   : null;
           $this->productId        = isset($config['product_id'])         ? $config['product_id']         : null;
           $this->itemNumber       = isset($config['item_number'])        ? $config['item_number']        : null;
           $this->parentOptionName = isset($config['parent_option_name']) ? $config['parent_option_name'] : null;
           $this->flatOptions      = isset($config['flat_options'])       ? $config['flat_options']       : array();
           $this->image            = isset($config['image'])              ? $config['image']              : null;
           $this->uom              = isset($config['uom'])                ? $config['uom']                : null;
        }
    }

    function getParentOptionId()
    {
        return $this->parentOptionId;
    }

    function setParentOptionId($parentOptionId)
    {
        $this->parentOptionId = $parentOptionId;
        return $this;
    }

    function getParentOptionName()
    {
        return $this->parentOptionName;
    }

    function setParentOptionName($parentOptionName)
    {
        $this->parentOptionName = $parentOptionName;
        return $this;
    }

    function getImage()
    {
        return $this->image;
    }

    function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    function getProductId()
    {
        return $this->productId;
    }

    function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    function getFlatOptions()
    {
        return $this->flatOptions;
    }

    function setFlatOptions($flatOptions)
    {
        $this->flatOptions = $flatOptions;
        return $this;
    }

    public function getItemNumber()
    {
        return $this->itemNumber;
    }

    public function setItemNumber($itemNumber)
    {
        $this->itemNumber = $itemNumber;
        return $this;
    }

    public function getUom()
    {
        return $this->uom;
    }

    public function setUom($uom)
    {
        $this->uom = $uom;
        return $this;
    }
}
