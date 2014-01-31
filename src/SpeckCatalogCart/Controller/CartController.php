<?php

namespace SpeckCatalogCart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CartController extends AbstractActionController
{
    protected $cartService;
    protected $productService;
    protected $choiceService;
    protected $flatOptions = array();

    public function editItemAction()
    {
        $id   = $this->getParams('id');
        $item = $this->getCartService()->findItemById($id, true);
        $meta = $item->getMetadata();

        $resp = $this->getEventManager()->trigger(array('meta' => $meta));
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'cart' => $this->getCartService()->getSessionCart(),
        ));
    }

    public function addItemAction()
    {
        // @todo use Request
        $productId     = (isset($_POST['product_id'])     ? $_POST['product_id']     : $this->params('id') );
        $productBranch = (isset($_POST['product_branch']) ? $_POST['product_branch'] : array()             );
        $uom           = (isset($_POST['uom'])            ? $_POST['uom']            : $productId . ':EA:1');
        $quantity      = (isset($_POST['quantity'])       ? $_POST['quantity']       : 1                   );
        $this->getCartService()->addCartItem($productId, $productBranch, $uom, $quantity);
        return $this->_redirect()->toRoute('cart');
    }

    public function updateProductAction()
    {
        // @todo use Request
        $this->getCartService()->replaceCartItemsChildren($_POST['cart_item_id'], $_POST['product_branch'], $_POST['uom'], $_POST['quantity']);
        return $this->redirect()->toRoute('cart');
    }

    public function updateQuantitiesAction()
    {
        // @todo use Request
        $this->getCartService()->updateQuantities($_POST['quantities']);
        return $this->_redirect()->toRoute('cart');
    }

    public function removeItemAction()
    {
        $cartService = $this->getCartService();
        $cartService->removeItemFromCart($this->params('id'));
        return $this->_redirect()->toRoute('cart');
    }

    public function getCartService()
    {
        if (!isset($this->cartService)) {
            $this->cartService = $this->getServiceLocator()->get('catalog_cart_service');
        }

        return $this->cartService;
    }

    public function setCartService($service)
    {
        $this->cartService = $service;
        return $this;
    }
}
