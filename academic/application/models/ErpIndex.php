<?php
class Application_Model_ErpIndex extends Zend_Db_Table_Abstract
{
	//Purchase
	public function PurchaseProformaCount()
	{
		$sql = $this->_db->select() 
				->from('erp_purchase_proforma_invoice')
				->where("proforma_invoice_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function PurchaseCommercialCount()
	{
		$sql = $this->_db->select() 
				->from('erp_purchase_commercial_invoice')
				->where("commercial_invoice_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function PurchasePackingCount()
	{
		$sql = $this->_db->select() 
				->from('erp_purchase_packing_list')
				->where("packing_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	}
	public function PurchaseEnquiryCount()
	{
		$sql = $this->_db->select() ->from('erp_purchase_enquiry', array("count(purchase_enquiry_id) as count"))
			->where("erp_purchase_enquiry.purchase_enquiry_status != 2");
	
		 $result = $this->getAdapter()->fetchRow($sql);	
		 return $result['count'];				  
	}
	public function PurchaseQuotationCount()
	{
		$sql = $this->_db->select() ->from('erp_purchase_quotation', array("count(purchase_quotation_id) as count"))
			->where("erp_purchase_quotation.purchase_quotation_status != 2");
		 $result = $this->getAdapter()->fetchRow($sql);	
		 return $result['count'];				  
	}
	public function PurchaseOrderCount()
	{
		$sql = $this->_db->select() ->from('erp_purchase_order', array("count(purchase_order_id) as count"))
			->where("erp_purchase_order.purchase_order_status != 2");
		 $result = $this->getAdapter()->fetchRow($sql);	
		 return $result['count'];				  
	}
	
	public function PurchaseInvoiceCount()
	{
		$sql = $this->_db->select() 
				->from('erp_purchase_invoice', array("count(purchase_invoice_id) as count"))
				->where("erp_purchase_invoice.purchase_invoice_status != 2");
		 $result = $this->getAdapter()->fetchRow($sql);	
		 return $result['count'];			  
	}
	public function Professorcount(){
		$sql = $this->_db->select()
			->from('erp_staff_employees_master',array("count(staff_id) as staff_count"))
			->where("erp_staff_employees_master.staff_status != 2");
			$result = $this->getAdapter()->fetchRow($sql);
			return $result['staff_count'];
	}
	public function AssistantProfessorcount(){
		$sql = $this->_db->select()
				->from('erp_substaff_employee_master',array("count(substaff_emp_id) as substaff_count"))
				->where("erp_substaff_employee_master.substaff_emp_status != 2");
				$result = $this->getAdapter()->fetchRow($sql);
				return $result['substaff_count'];
	}
	public function OfficeStaffcount(){
		$sql = $this->_db->select()
				->from('erp_office_staff_employees_master',array("count(officestaff_emp_id) as officestaff_count"))
				->where("erp_office_staff_employees_master.officestaff_emp_status != 2");
				$result = $this->getAdapter()->fetchRow($sql);
				return $result['officestaff_count'];
	}
	public function Drivercount(){
		$sql = $this->_db->select()
				->from('erp_driver_employees_master',array("count(driver_id) as driver_count"))
				->where("erp_driver_employees_master.driver_status != 2");
					$result = $this->getAdapter()->fetchRow($sql);
					return $result['driver_count'];
	}
	public function Labourcount(){
		$sql = $this->_db->select()
				->from('erp_operators_employees_master',array("count(operator_id) as operator_count"))
				->where("erp_operators_employees_master.operator_status != 2");
				$result = $this->getAdapter()->fetchRow($sql);
				return $result['operator_count'];
		
	}
	public function paymentAmount()
	{
		$sql = $this->_db->select()
				->from('finance_receipt_voucher',array("sum(paid_amount) as total_amount"))
				->where("finance_receipt_voucher.status != 2");
			$result  = $this->getAdapter()->fetchRow($sql);
				return $result['total_amount'];
	}
	public function depositAmount()
	{
		$sql = $this->_db->select()
				->from('finance_payment_vouvher',array("sum(paid_amount) as total_amount"))
				->where("finance_payment_vouvher.status != 2");
			$result  = $this->getAdapter()->fetchRow($sql);
				return $result['total_amount'];
	}
	
	//Sales 
	public function SalesProformaCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_proforma_invoice')
				->where("proforma_invoice_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function SalesCommercialCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_commercial_invoice')
				->where("commercial_invoice_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function SalesPackingCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_packing_list')
				->where("packing_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	}
	
	public function SalesEnquiryCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_enquiry')
				->where("sales_enquiry_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function SalesQuotationCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_quotation')
				->where("sales_quotation_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 
	
	public function SalesOrderCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_order')
				->where("sales_order_status!=?", 2);				 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 

	public function SalesInvoiceCount()
	{
		$sql = $this->_db->select() 
				->from('erp_sales_invoice');								 
		 $result = $this->getAdapter()->fetchAll($sql);	
		 return count($result);				  
	} 	
	
	public function PlantMaintenance()
	{
		$sql = $this->_db->select() 
				->from('erp_preventive_maintenance')
				->joinLeft(array('emm' => 'erp_machine_master'), 'emm.machine_id = erp_preventive_maintenance.machine_id',array('machine_code'))
				->where('erp_preventive_maintenance.status !=?', 2);
		 $result = $this->getAdapter()
		 ->fetchAll($sql);	
		 return $result;				  
	} 
	
	         
}