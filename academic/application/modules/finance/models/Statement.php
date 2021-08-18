<?php

class Finance_Model_Statement extends Zend_Db_Table_Abstract {

    /**
     * Set Primary Key Id as a Parameter
     *
     * @param string $cp_id
     * @return single dimention array
     */   
	
	public function getAccountStatement()
	{
		$select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, `payment_by`, payment_status, erp_vendor_master.vendor_name FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, `payment_by`, receipt_status, erp_dealer_master.dealer_name FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' ORDER BY date";		
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	} 
	   // echo $query = $user->select()->where('firstname LIKE ?', $uname.'%')->ORwhere('lastname LIKE ?', $_POST['lname'].'%')->ORwhere('emailid LIKE ?', $_POST['email'].'%');

	public function getAccountStatementByDates($data)
	{
		
		/* if( !empty( $data['type'] ) && ($data['type'] == 5) )
		{
			$where = '';			
			if(!empty( $data['vendor_id'] )){
				$where .= ' and vendor_id ='.$data['vendor_id'];
			}
			if(!empty( $data['start_date'] )){
				$where .= " and add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY)";
			}
			
			//$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date, erp_vendor_master.vendor_name FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id where status !='2' ".$where." ORDER BY add_date";
			
		}else*/
		if( !empty( $data['type'] ) || !empty($data['party_id']) ){
		
			$where = '';
			$where1 = '';
			if(!empty( $data['type'] )){
				$where .= " and receipt_status ='".$data['type']."'";
				$where1 .= " and payment_status ='".$data['type']."'";
			}
			if(!empty( $data['party_id'] )){
				$where .= " and dealer_name ='".$data['party_id']."'";
				$where1 .= " and vendor_name ='".$data['party_id']."'";
			}
			if(!empty( $data['start_date'] )){
				$where .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
				$where1 .= " and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."'";
			}
			
			$select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, `payment_by`, payment_status, erp_vendor_master.vendor_name FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' ".$where1." UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, `payment_by`, receipt_status, erp_dealer_master.dealer_name FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' ".$where." ORDER BY date";			
			//echo $select; die;
			//$select ="SELECT *, DATE_FORMAT(add_date,'%Y-%m-%d') as date, receipt_status as payment_status erp_dealer_master.dealer_name as vendor_name FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id where status !='2' ".$where." ORDER BY add_date";
			
		}else{
			/* $select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, `payment_by`, payment_status, erp_vendor_master.vendor_name FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, `payment_by`, receipt_status, erp_dealer_master.dealer_name FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and add_date between '.".$data['start_date']."' and ADDDATE('".$data['end_date']."',INTERVAL 1 DAY) ORDER BY date";	 */
			$select ="SELECT finance_payment_vouvher.`payment_voucher_id`, DATE_FORMAT(finance_payment_vouvher.add_date,'%Y-%m-%d') as date, paid_amount, `payment_by`, payment_status, erp_vendor_master.vendor_name FROM `finance_payment_vouvher` left join erp_vendor_master on erp_vendor_master.vendor_id=finance_payment_vouvher.vendor_id WHERE `status` !='2' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."' UNION SELECT finance_receipt_voucher.`receipt_voucher_id`, DATE_FORMAT(finance_receipt_voucher.add_date,'%Y-%m-%d') as date, `paid_amount`, `payment_by`, receipt_status, erp_dealer_master.dealer_name FROM `finance_receipt_voucher` left join erp_dealer_master on erp_dealer_master.dealer_id=finance_receipt_voucher.dealer_id WHERE `status` !='2' and add_date >='".$data['start_date']."' and add_date <='".$data['end_date']."' ORDER BY date";	
		}
		
		//echo $select; die;
		$result = $this->getAdapter()
                ->fetchAll($select);
        return $result;	
	}
	
	public function getUsers()
	{
		$select ="SELECT `dealer_id`, `dealer_name` FROM erp_dealer_master WHERE dealer_master_status !='2' UNION SELECT `vendor_id`, `vendor_name` FROM erp_vendor_master WHERE vendor_status !='2'";		
		$result = $this->getAdapter()
                ->fetchAll($select);
		$data = array();
        foreach ($result as $val) {            
            $data[$val['dealer_name']] = $val['dealer_name'];            
        }
        return $data;	
	} 
	

}
