<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class Module
{
    protected $whitelist = array('user/login');

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sharedEventManager = $eventManager->getSharedManager();

        $list = $this->whitelist;
        $serviceManager = $e->getTarget()->getServiceManager();
        $auth = $serviceManager->get('Zend\Authentication\AuthenticationService');

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function($e) use ($list, $auth) {
            $match = $e->getRouteMatch();

            // No route match, this is a 404
            if (!$match instanceof RouteMatch) {
                return;
            }

            // Route is whitelisted
            $name = $match->getMatchedRouteName();

            if (in_array($name, $list)) {
                return;
            }

            $router   = $e->getRouter();

            // User is authenticated
            if ($auth->identity()) {
                if ($name == 'home') {
                    // redirect to 'twitter/last24hourstwitts'
                    $url  = $router->assemble(array(), array(
                        'name' => 'twitter/last24hourstwitts'
                    ));
                }
                else return;
            }
            else {
                // Redirect to the user login page
                $url      = $router->assemble(array(), array(
                        'name' => 'user'
                ));
            }

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }, -200);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                    'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                        // If you are using DoctrineORMModule:
                        return $serviceManager->get('doctrine.authenticationservice.orm_default');
                    }
              )
         );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
