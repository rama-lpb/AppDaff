<?php

namespace App\Core\Abstract ;
use App\Core\Database;

use \PDO;

abstract class AbstractRepository extends Database{
  
  protected PDO  $DB ;


  protected function __construct(){
    $this->DB = parent::getInstance()->getConnection();
  }

 abstract public function selectAll();
    abstract public function insert(array $data);
    abstract public function update();
    abstract public function delete();
    abstract public function selectById($id);
    abstract public function selectBy(array $filter);

    
  
}