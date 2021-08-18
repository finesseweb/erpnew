<?php

class Application_Model_Library extends Application_Model_GlobalDbModel {

	//protected $_name = 'fa_books';
    function __construct() {
        parent::__construct('erp');
    }
    public function getBookList() {
		/*$select = $this->_db->select()
				->from('fa_books');
				//->where("category IN(?)",$holiday_category)
				//->where("deleted IS NULL")
				//->order('start');
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;*/
		
	$select=$this->_db->select()
                      ->from('fa_books')
                      //->where("status =?", 1)
                    ->joinLeft('fa_book_category', "fa_book_category.id=fa_books.category")
				    ->where("fa_books.status =?", 1);
				
        $result=$this->getAdapter()
                      ->fetchAll($select);    
		
        return $result;
    }
	
	public function getBookcopies($isbn) {
		$select = $this->_db->select()
				->from('fa_books_copies')
				->where("ISBN =?", $isbn)
				->where("status !=?", 1)
				->where("hold !=?", 1)
				// ->order("copies_no ASC")
				->limit(1);
				$result = $this->getAdapter()         
                          ->fetchAll($select);  
        return  $result;
    }
	
	public function getBookcopiesNumber($isbn) {
		    $select = $this->_db->select()
				->from('fa_books_copies')
				->where("ISBN =?", $isbn)
				->where("status !=?", 1)
				->where("issue !=?", 1)
				->where("damage =?", 0)
				->where("req_copy =?", 0)
				// ->order("copies_no ASC")
				->limit(1);
				$result = $this->getAdapter()         
                          ->fetchAll($select); 
        return  $result;
    }
	public function getBookCopyData($isbn_no) {
		  $select = $this->_db->select()
				->from('fa_books_copies')
				->where("ISBN =?", $isbn_no)
				->where("status !=?", 1)
				->where("hold !=?", 1)
				->where("issue !=?", 1)
				->where("req_copy !=?", 1)
				->limit(1);
				$result = $this->getAdapter()         
                          ->fetchAll($select);  
        return  $result;
    }
	public function getHoldBookList($id) {
		$select = $this->_db->select()
				->from('fa_hold_book')
				->where("ref_id =?", $id);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
	public function getIssueBookList($id) {
		$select = $this->_db->select()
				->from('fa_allowed_book')
				->where("ref_id =?", $id);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
    public function checkReq($copiesno,$isbn) {
		$select = $this->_db->select()
				->from('fa_books_copies')
				->where("copies_no =?", $copiesno)
				->where("status =?", 0)
				->where("ISBN =?", $isbn)
				->where("req_copy =?", 0);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
	public function getBookReturnDate($id) {
		$select = $this->_db->select()
				->from('fa_hold_book')
				->where("ref_id =?", $id);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
	public function getbook($isbn_no) {
		$select = $this->_db->select()
				->from('fa_books')
				->where("status =?", 1)
				->where("ISBN =?", $isbn_no);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
	public function getext($id) {
		$select = $this->_db->select()
				->from('fa_ext_policy')
				->where("ref_id =?", $id);
				$result = $this->getAdapter()         
                                ->fetchAll($select);  
        return $result;
    }
   	
	
	
}
