<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_GlobalDbModel extends Zend_Db_Table_Abstract{

    public $_db_name = array();
public function __construct($dbname = 'main_web'){
                  
                     $db_arr = $this->db_array();
                     foreach($db_arr as $key => $value){
                         $this->_db_name[$key] = new Zend_Db_Adapter_Pdo_Mysql($value);
                     }
      $this->_db =  $this->_db_name[$dbname];
       parent::__construct(); 
        
    }
    
    
    public function db_array(){
           $zendConfig = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
                     $customdbconfig = $zendConfig->customdbconfig->toArray();
                     foreach($customdbconfig as $key => $value){
                         foreach($value as $key1 => $value){
                             $db_arr[$key1][$key] = $value;
                         }
                     }
                     return $db_arr;
    }
    
  
}