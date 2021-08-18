<?php 

class Application_Model_Event extends Zend_Db_Table_Abstract {

	public $_name = 'ic_category';
	protected $_id = 'id';
	
	public function category() {
		
       $select = "SELECT * FROM `ic_category` where deleted=0";
        $result = $this->getAdapter()
                ->fetchAll($select);
		 
		$jsoncategory = array();
       foreach ($result as $entry)
        {
            $jsoncategory[] = array(
				//'category_id'      	=>  $entry['category_id'],
				'title'     =>  $entry['category_name'], 
				'start'=> '2019-07-07',
                'end'=> '2019-07-10',
               // 'backgroundColor'	=>  $entry['backgroundColor'],
               // 'borderColor'		=>  $entry['borderColor'],
               // 'textColor'			=>  $entry['textColor'], 
				//'count'				=> $entry->count,
				//'token'			    => $this->security->get_csrf_hash(), 
            );
        } 
		
		return $result;
	}
	public function categorybyId($cat_id){
		$select = "SELECT * FROM `ic_category` where deleted=0 AND category_id=$cat_id";
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;

	}

	public function category2() {
		
       $select = "SELECT * FROM `ic_category` where deleted=0";
        $result = $this->getAdapter()
                ->fetchAll($select);
		return $result;
	}


	public function events() {
		
      $select = "SELECT * FROM  `ic_events` where deleted=0";
		
		//$select = $this->_db->select()
              //  ->from('ic_category');
        $result = $this->getAdapter()
                ->fetchAll($select);
		 
		$jsonevent = array();
       foreach ($result as $entry)
        {
            $jsonevent[] = array(
				
				'id'	=>  $entry['eid'],
				'title'     =>  $entry['title'], 
				'start'=> $entry['start'],
                'end'=> $entry['end'],
                'color'	=>  $entry['backgroundColor'],
                'description'=>  $entry['description'], 
              
            );
           
        } 
		
		return $jsonevent;
	}	

	public function eventsList() {
		
      $select = "SELECT * FROM  `ic_events` where deleted=0";
        $result = $this->getAdapter()
                ->fetchAll($select);
		 
		return $result;
	}

	public function getEvent($id) {
		
    $select = "SELECT * FROM  `ic_events` Where eid=$id";
        $result = $this->getAdapter()
                ->fetchAll($select);
		 
		return $result;
	}

	public function userEvent() {

	if($_SESSION['admin_login']['admin_login']->role_id==0){
		$select = "SELECT * FROM  `ic_events` where only_faculty=0 AND deleted=0";
	}else{
		$select = "SELECT * FROM  `ic_events` where deleted=0";
	}
    
        $result = $this->getAdapter()
                ->fetchAll($select);

                $jsonevent = array();
       foreach ($result as $entry)
        {
            $jsonevent[] = array(
				//'category_id'      	=>  $entry['category_id'],
				'id'	=>  $entry['eid'],
				'title'     =>  $entry['title'], 
				'start'=> $entry['start'],
                'end'=> $entry['end'],
                'color'	=>  $entry['backgroundColor'],
                'url'=>  $entry['description'], 
                'description'=>  $entry['description'], 
            );
           
        } 
		
		return $jsonevent;
		 
		
	}

	public function insertCategory() {	

	}
	
   
	
}
 
/* End of file fulcalendar.php */
/* Location: ./application/models/fulcalendar.php */