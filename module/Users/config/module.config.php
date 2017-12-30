<?php

return array(

        'doctrine' => array(
                'eventmanager' => array(
                        'orm_default' => array(
                                'subscribers' => array(
                                        // pick any listeners you need
                                        'Gedmo\Tree\TreeListener',
                                        'Gedmo\Timestampable\TimestampableListener',
                                        //                'Gedmo\Sluggable\SluggableListener',
                                        'Gedmo\Translatable\TranslatableListener',
                                        //                'Gedmo\Sortable\SortableListener'
                                ),
                        ),
                ),

                'driver' => array(
                        'users_entity' => array(
                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                'paths' => array(__DIR__ . '/../src/Application/Entity'),
                        ),
                        'translatable_metadata_driver' => array(
                                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                                'cache' => 'array',
                                'paths' => array(
                                        'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
                                ),
                        ),
                        'orm_default' => array(
                                'drivers' => array(
                                        'Application\Entity'  => 'users_entity',
                                        'Gedmo\Translatable\Entity' => 'translatable_metadata_driver',
                                ),
                        ),
                ),
        ),

        'controllers' => array(
                'invokables' => array(
                        'Users\Controller\Users' => 'Users\Controller\UsersController',
                ),
        ),
        'router' => array(
                'routes' => array(
                        'user' => array(
                                'type'    => 'segment',
                                'options' => array(
                                        // Change this to something specific to your module
                                        'route'    => '/user[/:action][/:id]',
                                        'constraints' => array(
                                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'id'     => '[0-9]+',
                                        ),
                                        'defaults' => array(
                                                // Change this value to reflect the namespace in which
                                                // the controllers for your module are found
                                                // '__NAMESPACE__' => 'Users',
                                                'controller'    => 'Users\Controller\Users',
                                                'action'        => 'index',
                                        ),
                                ),
                                'may_terminate' => true,
                                /*
                                 'child_routes' => array(
                                         // This route is a sane default when developing a module;
                                         // as you solidify the routes for your module, however,
                                         // you may want to remove it and replace it with more
                                         // specific routes.
                                         'default' => array(
                                                 'type'    => 'Segment',
                                                 'options' => array(
                                                         'route'    => '/user[/:action]',
                                                         'constraints' => array(
                                                                 'controller' => 'Users\Controller\Users',
                                                                 'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                         ),
                                                         'defaults' => array(
                                                         ),
                                                 ),
                                         ),
                                 ),
        */
                        ),
                ),
        ),
        'view_manager' => array(
                'template_path_stack' => array(
                        'users' => __DIR__ . '/../view',
                ),
        ),
);
