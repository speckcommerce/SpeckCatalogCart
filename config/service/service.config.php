<?php

use SpeckCatalogCart\Service\CartService;

return array(
    'factories' => array(
        'catalog_cart_service' => function($sm) {
            $service = new CartService();
            $service->setCartItemPrototype($sm->get('speckcart_entity_cartitem'));
            return $service;
        }
    ),
);
