<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(

     'module_config' =>array(
             'search_index' => __DIR__ . '/../data/search_index',
     ),

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
                    'application_entity' => array(
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
                                    'Application\Entity'  => 'application_entity',
                                    'Gedmo\Translatable\Entity' => 'translatable_metadata_driver',
                            ),
                    ),
            ),
            'authentication' => array(
                    'orm_default' => array(
                            'object_manager' => 'Doctrine\ORM\EntityManager',
                            'identity_class' => 'Application\Entity\User',
                            'identity_property' => 'email',
                            'credential_property' => 'password',
                    ),
            ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'twitter' => array(
                    'type'    => 'Literal',
                    'may_terminate' => true,
                    'options' => array(
                            'route'    => '/twitter',
                            'constraints' => array(
                                    'p' => '[0-9]*',
                            ),
                            'defaults' => array(
                                    'controller' => 'Application\Controller\Index',
                                    'action'     => 'index',
                            ),
                    ),
                    'may_terminate' => true,
                    'child_routes' =>array(
                            'variant' => array(
                                    'type' => 'Segment',
                                    'options' => array(
                                            'route' => '[/:variant[/:p]]',
                                            'constraints' => array(
                                                    'variant'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Index',
                                                    'action'     => 'index',
                                                    'p'   => '',
                                            ),
                                    ),
                            ),
                            'search' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/search/search',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Search',
                                                    'action'     => 'index',
                                            ),
                                    ),
                            ),
                            'generate' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/search/generate',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Search',
                                                    'action'     => 'generateIndex',
                                            ),
                                    ),
                            ),
                            'create' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/create',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Index',
                                                    'action'     => 'create'
                                            ),
                                    ),
                            ),
                            'edit' => array(
                                    'type' => 'Segment',
                                    'options' => array(
                                            'route' => '/edit/:id',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Index',
                                                    'action'     => 'edit',
                                                    'id'   => 0
                                            ),
                                            'constraints' => array(
                                                    'id'        => '[0-9]*',
                                            ),
                                    ),
                            ),
                            'remove' => array(
                                    'type' => 'Segment',
                                    'options' => array(
                                            'route' => '/remove/:id',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Index',
                                                    'action'     => 'remove',
                                                    'id'   => 0
                                            ),
                                            'constraints' => array(
                                                    'id'        => '[0-9]*',
                                            ),
                                    ),
                            ),
                            'ajax_query'   => array(
                                    'type'    => 'Segment',
                                    'options' => array(
                                            'route'    => '/ajax/:uid',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Index',
                                                    'action'     => 'follow',
                                            ),
                                            'constraints' => array(
                                                    'uid'        => '[0-9]*',
                                            ),
                                    ),
                            ),
                    ),
            ),
            'restful-twitts' => array(
                    'type' => 'Literal',
                    'options' => array(
                            'route' => '/rest/twitter',
                            'defaults' => array(
                                    'controller'=> 'Application\Controller\TwitterRestfull'
                            ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                            'client' => array(
                                    'type'    => 'Segment',
                                    'options' => array(
                                            'route'    => '/client[/:action]',
                                            'constraints' => array(
                                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\TwitterRestfullClient',
                                                    'action'     => 'index'
                                            ),
                                    ),
                            ),
                    ),
            ),
            'user' => array(
                    'type'    => 'Literal',
                    'options' => array(
                            // Change this to something specific to your module
                            'route'    => '/user',
                            'defaults' => array(
                                    // Change this value to reflect the namespace in which
                                    // the controllers for your module are found
                                    // '__NAMESPACE__' => 'Users',
                                    'controller'    => 'Application\Controller\Users',
                                    'action'        => 'index',
                            ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                             // This route is a sane default when developing a module;
                             // as you solidify the routes for your module, however,
                             // you may want to remove it and replace it with more
                             // specific routes.
                             'default' => array(
                                     'type'    => 'Segment',
                                     'options' => array(
                                             'route'    => '[/:action]',
                                             'constraints' => array(
                                                     'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                             ),
                                             'defaults' => array(
                                                     'controller' => 'Application\Controller\Users',
                                                     'action'     => 'login'
                                             ),
                                     ),
                             ),
                            'edit' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/edit',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'edit',
                                            ),
                                    ),
                            ),
                            'remove' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/remove',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'remove',
                                            ),
                                    ),
                            ),
                            'login' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/login',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'login',
                                            ),
                                    ),
                            ),
                            'postlogin' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/postlogin',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'postlogin',
                                            ),
                                    ),
                            ),
                            'process' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/process',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'process',
                                            ),
                                    ),
                            ),
                            'register' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/register',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'register',
                                            ),
                                    ),
                            ),
                            'logout' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/logout',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'logout',
                                            ),
                                    ),
                            ),
                            'confirm' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                            'route' => '/confirm',
                                            'defaults' => array(
                                                    'controller' => 'Application\Controller\Users',
                                                    'action'     => 'confirm',
                                            ),
                                    ),
                            ),
                     ),
            ),
        ),
    ),

   'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index'   => 'Application\Controller\IndexController',
            'Application\Controller\Users'   => 'Application\Controller\UsersController',
            'Application\Controller\TwitterRestfull' => 'Application\Controller\TwitterRestfullController',
            'Application\Controller\Search' => 'Application\Controller\SearchController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
