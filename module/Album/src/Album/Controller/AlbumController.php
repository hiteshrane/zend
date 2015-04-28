<?php

// Controller namespace
 namespace Album\Controller;  
// Every Controller extend this namespace
 use Zend\Mvc\Controller\AbstractActionController;
 //View model
 use Zend\View\Model\ViewModel; 
 //table namespace
 use Album\Model\Album;        
 //form namespace
 use Album\Form\AlbumForm;      

 class AlbumController extends AbstractActionController
 {
     //
     protected $albumTable;
     
     public function indexAction()
     {
         //Get all records from album table
//         return new ViewModel(array(
//            'albums' => $this->getAlbumTable()->fetchAll(),
//        ));
         // grab the paginator from the AlbumTable
        $paginator = $this->getAlbumTable()->fetchAll(true);
        // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        // set the number of items per page to 10
        $paginator->setItemCountPerPage(10);
        
        \Zend\Debug\Debug::dump($this->getServiceLocator()->get('Config'));
        
         $this->getServiceLocator()->get('memcached')->setItem('hitesh', 'test');

        return new ViewModel(array(
            'paginator' => $paginator
        ));
     }

     public function addAction()
     {
         // Get form
         $form = new AlbumForm();
         $form->get('submit')->setValue('Add');

         // Get a form data
         $request = $this->getRequest();         
         
         if ($request->isPost()) {
             //Get Model
             $album = new Album();        
             //Validate data
             $form->setInputFilter($album->getInputFilter());
             //submit data
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 //tabledatagateway
                 $album->exchangeArray($form->getData());
                 //call save method
                 $this->getAlbumTable()->saveAlbum($album);
                 // Redirect to list of albums
                 return $this->redirect()->toRoute('album');
             }
         }
         return array('form' => $form);
     }

     public function editAction()
     {
         //Get id from route
         $id = (int) $this->params()->fromRoute('id', 0);
         
         
         if (!$id) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'add'
             ));
         }
         // Get the Album with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             //get object
             $album = $this->getAlbumTable()->getAlbum($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'index'
             ));
         }
         //get a form object
         $form  = new AlbumForm();
         //Bind a form object to model
         $form->bind($album);
         $form->get('submit')->setAttribute('value', 'Edit');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($album->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getAlbumTable()->saveAlbum($album);

                 // Redirect to list of albums
                 return $this->redirect()->toRoute('album');
             }
         }

         return array(
             'id' => $id,
             'form' => $form,
         );
     }

     public function deleteAction()
     {
         //Get id from route
         $id = (int) $this->params()->fromRoute('id', 0);
         
         if (!$id) {
             return $this->redirect()->toRoute('album');
         }

         $request = $this->getRequest();
         if ($request->isPost()) {
             
             $del = $request->getPost('del', 'No');

             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $this->getAlbumTable()->deleteAlbum($id);
             }

             // Redirect to list of albums
             return $this->redirect()->toRoute('album');
         }

         return array(
             'id'    => $id,
             'album' => $this->getAlbumTable()->getAlbum($id)
         );
     }
     
     // Get Album table object 
     public function getAlbumTable()
     {
         if (!$this->albumTable) {
             $sm = $this->getServiceLocator();
             $this->albumTable = $sm->get('Album\Model\AlbumTable');
         }
         return $this->albumTable;
     }
 }