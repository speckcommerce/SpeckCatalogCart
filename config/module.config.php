<?php
return array(
    'router' => array(
        'routes' => array(
            'product' => array(
                'child_routes' => array(
                    'cartProduct' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/:id/:cartItemId',
                            'constraints' => array(
                                'id'         => '[0-9]+',
                                'cartItemId' => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
            'cart' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/cart',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'add-product' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/add-product[/:id]',
                            'defaults' => array(
                                'controller' => 'catalog_cart_controller',
                                'action' => 'addItem',
                            ),
                        ),
                    ),
                    'remove-item' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/remove-item/:id',
                            'defaults' => array(
                                'controller' => 'catalog_cart_controller',
                                'action' => 'remove-item',
                            ),
                        ),
                    ),
                    'update-product' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/update-product',
                            'defaults' => array(
                                'controller' => 'catalog_cart_controller',
                                'action' => 'update-product',
                            ),
                        ),
                    ),
                ),
            ),
        )
    )
);
