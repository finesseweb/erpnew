<?php

class Application_Model_UserHoldBook extends Zend_Db_Table_Abstract {

	public $_name = 'user_holdbook';
	protected $_id = 'id';
	
	public function getHoldRequest($user_id) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                ->where('direct_issue=?', 1)
                ->where('status=?', 1);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	public function getMaxData() {
        $select = $this->_db->select()
                ->from($this->_name, array(('max(issueReqId) as maxId')))
                 ->where('issueReqId Like?', 'IssueId-%');
                //->where('direct_issue=?', 1);
        $result = $this->getAdapter()
                ->fetchAll($select);
				//echo $select;
				//exit;
        return $result;
    }

    public function getMaxDataTest() {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('issueReqId Like?', 'IssueId-%');
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	
	public function getLatestReturnDate($isbn) {
		$date = new DateTime('+20 days');
		$book_return_date=$date->format('Y-m-d');
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('ISBN=?', $isbn)
                ->where('status=?', 1)
				->where('bookReturndDate <?', $book_return_date)
                ->order('bookReturndDate ASC');
               // ->order('bookReturndDate BETWEEN ');
			 // echo $select; 
			  //echo '<br/>';
			  //echo '<br/>';
        $result = $this->getAdapter()
                ->fetchRow($select);
			
        return $result;
    }
	
    public function getIssueBook($user_id) {
		
        $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                //->where('direct_issue=?', 1)
                ->where('status=?', 1);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
    public function getUserBookList($user_id , $isbn) {

        $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                ->where('ISBN=?', $isbn)
				->where('direct_issue=?', 0);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return count($result);
    }
	 public function getHoldBookList($user_id) {
		
            $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                ->where('direct_issue=?', 1);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
	public function getHoldBookList2($user_id) {
		
            $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                ->where('direct_issue=?', 1)
				->where('status !=?', 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
				
				//echo $select;
				
        return $result;
    }
	public function getIssueBookList2($user_id) {
		
            $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                ->where('direct_issue=?', 0)
				->where('status !=?', 2);
        $result = $this->getAdapter()
                ->fetchAll($select);
				
				//echo $select;
				
        return $result;
    }
	public function getEXTBookList($user_id) {
		
       $select = $this->_db->select()
                ->from($this->_name)
                ->where('user_id=?', $user_id)
                
                ->where('ext_date !=?', 'NULL');
        $result = $this->getAdapter()
                ->fetchAll($select);
				//echo $select;
        return $result;
    }
   
	
}
