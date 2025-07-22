<?php

namespace App\Core\Abstract ;
abstract class AbstractEntity  {

 abstract public  function toArray():array ;

abstract public static function toObject(array $array) : static ;

 public function toJson (){
    return json_encode($this->toArray());
}
}