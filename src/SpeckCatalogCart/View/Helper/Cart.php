<?php

namespace SpeckCatalogCart\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Cart extends AbstractHelper
{
    protected $indent = 0;

    public function __invoke()
    {
        return $this;
    }

    //takes flat array of child cart items
    //uses metadata to build nested array representation of configuration to be used for views/etc
    public function options($cartItems)
    {
        $options = array();
        foreach($cartItems as $i => $cartItem){
            $itemMeta = $cartItem->getMetaData();
            $parentOptionId = $itemMeta->getParentOptionId();
            $options[$parentOptionId]['choices'][] = $cartItem;
            if (!isset($options[$parentOptionId]['option'])) {
                $options[$parentOptionId]['option'] = $itemMeta->getParentOptionName();
            }
            unset($cartItems[$i]);
        }

        return $options;
    }
}
