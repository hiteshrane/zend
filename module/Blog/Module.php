<?php

// Module namespace declaration
namespace Blog;
 
//database
use Blog\Model\Blog;
use Blog\Model\BlogTable;

//database 
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

//Must Extend this interface
 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {    
     //autoload classes and namespace
     public function getAutoloaderConfig()
     {
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
   
 }
