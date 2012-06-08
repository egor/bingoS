<?php

if (!defined('ABSTRACT_PHP')) {
    require_once PATH . 'library/Abstract.php';

    require_once PATH . 'library/Interface.php';
}

class Controller extends Main_Abstract
{
    
    public function factory() {
      return true;
   }
   
   public function main() {
       return true;
   }
    
}
