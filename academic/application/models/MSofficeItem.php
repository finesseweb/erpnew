<?php
/**
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_MSofficeItem extends Zend_Db_Table_Abstract
{
    public $_name = 'master_msoffice_item';
    protected $_id = 'item_id';
  
    //get details by record for edit
	public function getRecord($program_id)
    {       
        $select=$this->_db->select()
                      ->from($this->_name)
                      ->where("$this->_name.program_id=?",$program_id);				 
        $result=$this->getAdapter()
                      ->fetchAll($select); 
      //  print_r($result);die;					  
        return $result;
    }
	public function trashItems($program_id='') { //print_r($program_id);die;

        $this->_db->delete($this->_name, "program_id=$program_id");

    }
}