<?php

class Application_Model_LibraryHold extends Application_Model_GlobalDbModel {

	protected $_name = 'fa_books_copies';
    function __construct() {
        parent::__construct('erp');
    }

    public function update_copyno($get_copies) { 
		$data = array('req_copy' => 1);

		$where = array(
		'copies_no = ?' => $get_copies
		);

		$this->update('fa_books_copies', $data, $where);
	}
   
	
}
