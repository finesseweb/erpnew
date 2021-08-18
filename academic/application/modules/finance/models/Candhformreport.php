<?php

class Finance_Model_Candhformreport extends Zend_Db_Table_Abstract {
	
	 protected $_name = 'erp_sales_invoice';
     protected $_id = 'sales_invoice_id';

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	
	public function getDiscountPriceC($data="")
	{
		$select = $this->_db->select()
                ->from($this->_name, array('sum(total_amount) as total_amount', 'count(sales_invoice_id) as sales_invoice_id'))
                ->where("$this->_name.invoice_status !=?", 2)
				->where('c_form_no !=?', '0')
				->where('c_form_no !=?', '')
				->where("invoice_date >= ?",  $data['start_date'])
				->where("invoice_date <= ?",  $data['end_date']);
				//echo $select; die;				
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;		
		
	}
	public function getDiscountPriceH($data="")
	{
		$select = $this->_db->select()
                ->from($this->_name, array('sum(total_amount) as total_amount', 'count(sales_invoice_id) as sales_invoice_id'))
                ->where("$this->_name.invoice_status !=?", 2)
				->where('h_form_no !=?', '0')
				->where('h_form_no !=?', '')
				->where("invoice_date >= ?",  $data['start_date'])
				->where("invoice_date <= ?",  $data['end_date']);
			//echo $select; die;	
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;		
		
	}
	public function getDiscountPrice($data="")
	{
		$select = $this->_db->select()
                ->from($this->_name, array('sum(total_amount) as total_amount', 'count(sales_invoice_id) as sales_invoice_id'))
                ->where("$this->_name.invoice_status !=?", 2)
				->where('c_form_no !=?', '0')
				->where('c_form_no !=?', '')
				->where("invoice_date >= ?",  $data['mid_date'])
				->where("invoice_date <= ?",  $data['end_date']);
				//echo $select; die;				
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;		
		
	}
/* 	public function getDiscountPrice()
	{
		//$select ="SELECT after_discount_price AS waybill_amount FROM erp_sales_waybill as waybill WHERE WHERE invoice_items.status !=2 ";
		//$select ="SELECT invoice_items.after_discount_price AS invoice_items as erp_sales_invoice_items"; ,"c_form_no","h_form_no"
		$select ="SELECT after_discount_price FROM erp_sales_invoice_items";
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);		
        return $result;			
		
	}	 */
		
}