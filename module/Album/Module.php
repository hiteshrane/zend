<?php

// Module namespace declaration
 namespace Album;
 
//database
use Album\Model\Album;
use Album\Model\AlbumTable;

//database 
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

//Must Extend this interface
 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;
 
 //caching
 use Zend\Mvc\MvcEvent;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {    
     //autoload classes and namespace
     public function getAutoloaderConfig()
     {
         // class map autoloader can load individual class files in array
         //Standard autoloader used to load class using namespace //third party library.
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }    
    
     // Include moduleconfig
     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
     
     // Service Manager configuration for database interaction
     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Album\Model\AlbumTable' =>  function($sm) {
                     $tableGateway = $sm->get('AlbumTableGateway');
                     $table = new AlbumTable($tableGateway);
                     return $table;
                 },
                 'AlbumTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Album());
                     return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
     
//    public function onBootstrap(MvcEvent $e)
//    {
//        // A list of routes to be cached
//        $routes = array('album/index');
//
//        $app = $e->getApplication();
//        $em  = $app->getEventManager();
//        $sm  = $app->getServiceManager();
//
//        $em->attach(MvcEvent::EVENT_ROUTE, function($e) use ($sm) {
//            $route = $e->getRouteMatch()->getMatchedRouteName();
//            $cache = $sm->get('cache-service');
//            $key   = 'route-cache-' . $route;
//
//            if ($cache->hasItem($key)) {
//                // Handle response
//                $content  = $cache->getItem($key);
//                $response = $e->getResponse();
//                $response->setContent($content);
//                return $response;
//            }
//        }, -1000); // Low, then routing has happened
//
//        $em->attach(MvcEvent::EVENT_RENDER, function($e) use ($sm, $routes) {
//            $route = $e->getRouteMatch()->getMatchedRouteName();
//            if (!in_array($route, $routes)) {
//                return;
//            }
//
//            $response = $e->getResponse();
//            $content  = $response->getContent();
//
//            $cache = $sm->get('cache-service');
//            $key   = 'route-cache-' . $route;
//            $cache->setItem($key, $content);
//        }, -1000); // Late, then rendering has happened
//    }
 }
