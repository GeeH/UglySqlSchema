<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'my-first-route' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'uglysqlschema merge [<platform>]',
                        'defaults' => array(
                            'controller' => 'UglySqlSchema\Controller\ConsoleController',
                            'action' => 'merge'
                        ),
                    ),
                ),
            ),
        ),
    ),
);