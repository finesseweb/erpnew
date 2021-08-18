<?php
class Application_Model_ErpAdmin extends Zend_Db_Table_Abstract
{
	  protected $_name = 'erp_admin';

           protected $_id = 'admin_id';

	
	public function checklogin($id)
	{
		$sql = $this->_db->select() ->from($this->_name)
				 ->where('admin_user_name=?',$id);
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return $result;				  
	}
	public function checkemil($id)
	{
		$sql = $this->_db->select() ->from($this->_name)
				 ->where('admin_user_email=?',$id);
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return $result;				  
	}
          
}
