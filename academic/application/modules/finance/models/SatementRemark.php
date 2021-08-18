<?php

class Finance_Model_SatementRemark extends Zend_Db_Table_Abstract {

     protected $_name = 'statement_remarks';
    protected $_id = 'id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $daily_book_id
     * @return single dimention array
     */
    public function getRecord($party_name) {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.party_name=?", $party_name);				
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    }

    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name);
                //->where("status!=2");
        $result = $this->getAdapter()
                ->fetchRow($select);
        return $result;
    } 
	
	 public function getRecordByDate($party_name='', $start_date='', $end_date='') {
        $select = $this->_db->select()
                ->from($this->_name)
                ->where("$this->_name.party_name=?", $party_name);
				//->where("$this->_name.end_date=?", $end_date);		
        $result = $this->getAdapter()
                ->fetchRow($select);		
        return $result;
    }
	
	public function getRecordsByDate($party_name='', $start_date='', $end_date='') {
        $select = $this->_db->select()
                ->from($this->_name, array("$this->_name.*", '(datediff(CURDATE(), end_date) ) as days_count' ) )
                ->where("$this->_name.party_name=?", $party_name)
				->where("$this->_name.start_date >= ?", $start_date)
				->where("$this->_name.end_date <= ?", $end_date);
				//->where("$this->_name.end_date=?", $end_date);
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchAll($select);
		$object = [];
        foreach ($result as $value) {
            $object[$value['auto_increment_count']] = $value;
        }		
        return $object;
    }
	
	public function dayscount($party_name='', $start_date='', $end_date=''){
		 $select = $this->_db->select()
                ->from($this->_name, array("$this->_name.*", '(datediff(CURDATE(), end_date) ) as days_count' ) )
                ->where("$this->_name.party_name=?", $party_name)				
				->order("$this->_name.id DESC");		 
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);
		$end_date1 = '';
		if( !empty($result['end_date']) ){
			$end_date1 = $result['end_date'];
		}		
		$select_sales = $this->_db->select()
                ->from('erp_sales_invoice', array('(datediff(CURDATE(), invoice_date) ) as days_count' ) )
				->join(array('ecm' => 'erp_dealer_master'), 'erp_sales_invoice.dealer_id = ecm.dealer_id', array("dealer_name","dealer_id"))
				->where("ecm.dealer_name=?", $party_name)
				->where("erp_sales_invoice.invoice_date >= ?", $end_date1);
		$sales_res = $this->getAdapter()
                ->fetchRow($select_sales);
		//echo $sales_res; die;
		if( $sales_res['days_count'] < 15 ){			
			return 0;
		}else{
			return $sales_res;
		}
        
	}
	
	public function formdayscount($party_name='', $start_date='', $end_date=''){
		 $select = $this->_db->select()
                ->from($this->_name, array("$this->_name.*", '(datediff(CURDATE(), end_date) ) as days_count' ) )
                ->where("$this->_name.party_name=?", $party_name)				
				->order("$this->_name.id DESC");		 
		//echo $select; die;
        $result = $this->getAdapter()
                ->fetchRow($select);		
		return $result;
	}

	
}
