<?php

// List all controller used in module

 return array(

     'controllers'  => array(
         'factories' => array(
             'Blog\Controller\List' => 'Blog\Factory\ListControllerFactory',
             'Blog\Controller\Write' => 'Blog\Factory\WriteControllerFactory',
             'Blog\Controller\Delete' => 'Blog\Factory\DeleteControllerFactory'
         )     
    ),  
    'service_manager' => array(
//         'invokables' => array(
//             'Blog\Service\PostServiceInterface' => 'Blog\Service\PostService'
//         ),
         'factories' => array(
             'Blog\Mapper\PostMapperInterface'   => 'Blog\Factory\ZendDbSqlMapperFactory',             
             'Blog\Service\PostServiceInterface' => 'Blog\Factory\PostServiceFactory',
             'Zend\Db\Adapter\Adapter'           => 'Zend\Db\Adapter\AdapterServiceFactory'
         )
     ),
     // Adding Route for controller action
      'router' => array(         
         'routes' => array(
             'blog' => array(                 
                 'type' => 'literal',                 
                 'options' => array(
                     'route'    => '/blog',                     
                     'defaults' => array(
                         'controller' => 'Blog\Controller\List',
                         'action'     => 'index',                     
                    )
                 ),
                 'may_terminate' => true,
                 'child_routes'  => array(
                     'detail' => array(
                         'type' => 'segment',
                         'options' => array(
                             'route'    => '/:id',
                             'defaults' => array(
                                 'action' => 'detail'
                             ),
                             'constraints' => array(
                                 'id' => '[1-9]\d*'
                             )
                         )
                     ),
                    'add' => array(
                         'type' => 'literal',
                         'options' => array(
                             'route'    => '/add',
                             'defaults' => array(
                                 'controller' => 'Blog\Controller\Write',
                                 'action'     => 'add'
                             )
                         )
                     ),
                     'edit' => array(
                         'type' => 'segment',
                         'options' => array(
                             'route'    => '/edit/:id',
                             'defaults' => array(
                                 'controller' => 'Blog\Controller\Write',
                                 'action'     => 'edit'
                             ),
                             'constraints' => array(
                                 'id' => '\d+'
                             )
                         )
                     ),
                     'delete' => array(
                         'type' => 'segment',
                         'options' => array(
                             'route'    => '/delete/:id',
                             'defaults' => array(
                                 'controller' => 'Blog\Controller\Delete',
                                 'action'     => 'delete'
                             ),
                             'constraints' => array(
                                 'id' => '\d+'
                             )
                         )
                     ),
                 ),                 
             ),             
         )
     ),      
     'view_manager' => array(
         'template_path_stack' => array(
             'blog' => __DIR__ . '/../view',
         ),
     ),
 );