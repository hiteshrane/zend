<?php
// Filename: /module/Blog/src/Blog/Mapper/ZendDbSqlMapper.php
 namespace Blog\Mapper;

 use Blog\Model\PostInterface;
 use Zend\Db\Adapter\AdapterInterface;
 use Zend\Db\Adapter\Driver\ResultInterface;
 use Zend\Db\ResultSet\HydratingResultSet;
 use Zend\Stdlib\Hydrator\HydratorInterface;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Insert;
 use Zend\Db\Sql\Update;
 use Zend\Db\Sql\Delete;
 
  
 
 class ZendDbSqlMapper implements PostMapperInterface
 {
     /**
      * @var \Zend\Db\Adapter\AdapterInterface
      */
     protected $dbAdapter;
     
     /**
      * @var \Zend\Stdlib\Hydrator\HydratorInterface
      */
     protected $hydrator;

     /**
      * @var \Blog\Model\PostInterface
      */
     protected $postPrototype;

     /**
      * @param AdapterInterface  $dbAdapter
      */
     public function __construct(AdapterInterface $dbAdapter, HydratorInterface $hydrator,PostInterface $postPrototype)
     {
         $this->dbAdapter = $dbAdapter;
         $this->hydrator       = $hydrator;
         $this->postPrototype  = $postPrototype;
     }

     /**
      * @param int|string $id
      *
      * @return PostInterface
      * @throws \InvalidArgumentException
      */
     public function find($id)
     {
         //Get Adapter
         $sql    = new Sql($this->dbAdapter);
         //Select table
         $select = $sql->select('posts');
         //Where Clause
         $select->where(array('id = ?' => $id));
         //Prepare statement
         $stmt   = $sql->prepareStatementForSqlObject($select);
         //Execute statement
         $result = $stmt->execute();
         
         //use hydrator to return result
         if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
             return $this->hydrator->hydrate($result->current(), $this->postPrototype);
         }
         
         throw new \InvalidArgumentException("Blog with given ID:{$id} not found.");
     }

     /**
      * @return array|PostInterface[]
      */
     public function findAll()
     {
          //Get Adapter,select table,prepare & execute statement
         $sql    = new Sql($this->dbAdapter);
         $select = $sql->select('posts');
         $stmt   = $sql->prepareStatementForSqlObject($select);
         $result = $stmt->execute();     
         
           //use hydrator to return result
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
             $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);

             return $resultSet->initialize($result);
         }
        
         return array();
     }
     
     /**
    * @param PostInterface $postObject
    *
    * @return PostInterface
    * @throws \Exception
    */
    public function save(PostInterface $postObject)
    {
       // Get postinterface object and extract data.
       $postData = $this->hydrator->extract($postObject);
       // unset
       unset($postData['id']); // Neither Insert nor Update needs the ID in the array

       if ($postObject->getId()) {
          // ID present, it's an Update
          $action = new Update('posts');
          $action->set($postData);
          $action->where(array('id = ?' => $postObject->getId()));
       } else {
          // ID NOT present, it's an Insert
          $action = new Insert('posts');
          $action->values($postData);
       }

       $sql    = new Sql($this->dbAdapter);
       $stmt   = $sql->prepareStatementForSqlObject($action);
       $result = $stmt->execute();

       if ($result instanceof ResultInterface) {
          if ($newId = $result->getGeneratedValue()) {
             // When a value has been generated, set it on the object
             $postObject->setId($newId);
          }

          return $postObject;
       }

       throw new \Exception("Database error");
    }
    
    /**
      * {@inheritDoc}
      */
     public function delete(PostInterface $postObject)
     {
         $action = new Delete('posts');
         $action->where(array('id = ?' => $postObject->getId()));

         $sql    = new Sql($this->dbAdapter);
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();

         return (bool)$result->getAffectedRows();
     }
 }
