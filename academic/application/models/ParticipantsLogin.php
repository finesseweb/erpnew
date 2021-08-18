<?php

class Application_Model_ParticipantsLogin extends Zend_Db_Table_Abstract {

    public $_name = 'participants_login';
    protected $_id = 'user_id';
    private $_flashMessenger = null;
    
    
    
    public function getInfo($email){
      $select = $this->_db->select()
				->from($this->_name)
				->where("participant_email =?",$email);
				$result = $this->getAdapter()         
                                ->fetchRow($select);  
                       return $result;
             
        
    }
    
}

