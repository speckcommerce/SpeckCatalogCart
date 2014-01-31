<?php

namespace SpeckCatalogCart\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use SpeckCart\Entity\CartItem;
use SpeckCatalogCart\Model\CartProductMeta;
use SpeckCatalog\Model\Option;
use SpeckCatalog\Model\Product;
use SpeckCatalog\Model\Choice;

class CartService implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use EventManagerAwareTrait;

    protected $flatOptions = array();
    protected $productService;
    protected $cartService;
    protected $productUomService;

    public function persistItem($cartItem)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('cartItem' => $cartItem));
        //trigger event (tax?)
        return $this->getCartService()->persistItem($cartItem);
    }

    public function addItemToCart($cartItem)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('cartItem' => $cartItem));
        //trigger event (tax?)
        return $this->getCartService()->addItemToCart($cartItem);
    }

    public function getSessionCart()
    {
        return $this->getCartService()->getSessionCart();
    }

    public function removeItemFromCart($cartItemId)
    {
        return $this->getCartService()->removeItemFromCart($cartItemId);
    }

    public function findItemById($cartItemId, $childItems = false)
    {
        if (!$childItems) {
            return $this->getCartService()->findItemById($cartItemId);
        }

        //this is temporarly until speck cart service can return an item with its children populated
        $return = null;
        $cartItems = $this->getSessionCart()->getItems();
        foreach ($cartItems as $item) {
            if ($cartItemId === $item->getCartItemId()) {
                $return = $item;
                break;
            }
        }
        return $return;
    }

    public function addCartItem($productId, $flatOptions = array(), $uomString, $quantity)
    {
        $this->flatOptions = $flatOptions;
        $product = $this->getProductService()->getFullProduct($productId, true);
        $cartItem = $this->createCartItem($product, null, $uomString, $quantity);

        $this->addItemToCart($cartItem);

        return $cartItem;
    }

    protected function addOptions($options = array(), $parentCartItem, $quantity = 1)
    {
        if (!count($options)) {
            return $parentCartItem;
        }
        foreach($options as $option){
            if(array_key_exists($option->getOptionId(), $this->flatOptions)){

                $opt = $this->flatOptions[$option->getOptionId()];
                if(is_array($opt)){ // multiple choices allowed(checkboxes or multi-select)
                    foreach($option->getChoices() as $choice){
                        if(array_key_exists($choice->getChoiceId(), $opt)){
                            $childItem = $this->createCartItem($choice, $option, null, $quantity);
                            $parentCartItem->addItem($childItem);
                        }
                    }
                } else { // $opt is the choiceId
                    foreach($option->getChoices() as $choice){
                        if($opt == $choice->getChoiceId()){
                            $childItem = $this->createCartItem($choice, $option, null, $quantity);
                            $parentCartItem->addItem($childItem);
                        }
                    }
                }

            }
        }
        return $parentCartItem;
    }

    public function replaceCartItemsChildren($cartItemId, $flatOptions = array(), $uomString, $quantity)
    {
        $this->flatOptions = $flatOptions;

        $cartItem = $this->findItemById($cartItemId, true);

        $product = $this->getProductService()->getFullProduct($cartItem->getMetadata()->getProductId());

        //remove all children
        $children = $cartItem->getItems();
        if ($children) {
            foreach ($children as $child) {
                $this->removeItemFromCart($child->getCartItemId());
            }
        }

        //add the new child items
        $this->addOptions($product->getOptions(), $cartItem);
        $newItems = $cartItem->getItems();
        if ($newItems) {
            foreach ($newItems as $childItem) {
                $childItem->setParentItemId($cartItem->getCartItemId());
                $this->addItemToCart($childItem);
            }
        }

        $cartItem->setQuantity($quantity);
        $cartItem->getMetaData()->setUom($uomString);
        $cartItem->setPrice($this->getPriceForUom($uomString));

        //update and persist parent
        $cartItem->getMetaData()->setFlatOptions($this->flatOptions);
        $this->persistItem($cartItem);
    }

    public function createCartItem($item, Option $parentOption = null, $uomString = null, $quantity = 1)
    {
        $meta = array(
            'uom'                => $uomString,
            'item_number'        => $item->getItemNumber(),
            'image'              => $item->has('image') ? $item->getImage() : null,
            'parent_option_id'   => null,
            'parent_option_name' => null,
            'product_id'         => $item->getProductId(),
            'flat_options'       => $this->flatOptions,
        );

        if ($item instanceOf Product && $parentOption) {
            $meta['parent_option_id']   = $parentOption->getOptionId();
            $meta['parent_option_name'] = $parentOption->__toString();
        }

        if ($item instanceOf Product) {
            $meta['product_type_id'] = $item->getProductTypeId();
        }

        $cartProductMeta = new CartProductMeta($meta);
        $cartItem = new CartItem(array(
            'metadata'    => $cartProductMeta,
            'description' => $item->__toString(),
            'quantity'    => $quantity,
            'price'       => $parentOption ? $item->getAddPrice() : $this->getPriceForUom($uomString),
        ));

        $this->addOptions($item->getOptions(), $cartItem, $quantity);

        return $cartItem;
    }

    public function getPriceForUom($uomString)
    {
        $exp = explode(':', $uomString);
        $data = array(
            'product_id' => (int) $exp[0],
            'uom_code'   => $exp[1],
            'quantity'   => (int) $exp[2],
        );
        $uom = $this->getProductUomService()->find($data);

        if(!$uom instanceOf \SpeckCatalog\Model\ProductUom) {
            throw new \Exception('couldnt get that uom');
        }
        return $uom->getPrice();
    }

    public function getProductService()
    {
        if (null === $this->productService) {
            $this->productService = $this->getServiceLocator()->get('speckcatalog_product_service');
        }
        return $this->productService;
    }

    public function setProductService($productService)
    {
        $this->productService = $productService;
    }

    function getCartService()
    {
        if (null === $this->cartService) {
            $this->cartService = $this->getServiceLocator()->get('SpeckCart\Service\CartService');
        }
        return $this->cartService;
    }

    function setCartService($cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @return productUomService
     */
    public function getProductUomService()
    {
        if (null === $this->productUomService) {
            $this->productUomService = $this->getServiceLocator()->get('speckcatalog_product_uom_service');
        }
        return $this->productUomService;
    }

    /**
     * @param $productUomService
     * @return self
     */
    public function setProductUomService($productUomService)
    {
        $this->productUomService = $productUomService;
        return $this;
    }

    /**
     * @return flatOptions
     */
    public function getFlatOptions()
    {
        return $this->flatOptions;
    }

    /**
     * @param $flatOptions
     * @return self
     */
    public function setFlatOptions($flatOptions)
    {
        $this->flatOptions = $flatOptions;
        return $this;
    }
}
