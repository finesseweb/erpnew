<?php


class Invoice_Form_ErpPurchaseInvoice extends Zend_Form {

    protected $_name = 'erp_purchase_invoice';
    protected $_id = 'purchase_invoice_id';

    public function init() {
       $InventoryGrn = new Application_Model_ErpInventoryGrn();
        $data = $InventoryGrn->getDropDownList();
        $inventory_grn_id = $this->createElement('select', 'inventory_grn_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addMultiOptions(array('' => 'Select GRN ID', '-1' => 'Direct Invoice'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($inventory_grn_id);
		/*
		 $ErpVendorMaster_model = new Application_Model_ErpVendorMaster();
		$data = $ErpVendorMaster_model->getDropDownList();
        //print_r($data);die;
		$vendor_id = $this->createElement('select','vendor_id')
                ->removeDecorator('label')->setAttrib('class',array('form-control','chosen-select'))
                //->setRequired(true)	
				->addMultiOptions(array('' => 'Select Vendor'))	
                ->addMultiOptions($data)
              //  ->setAttrib('required','true')
            	->removeDecorator("htmlTag");
                $this->addElement($vendor_id);
		
		$vehicle = $this->createElement('select','vehicle')
                ->removeDecorator('label')->setAttrib('class',array('form-control','chosen-select'))
                //->setRequired(true)
                ->addMultiOptions(array( '' => 'Select Vehicle',
                                          '1'=>'Own',
                                         '2'=>'3rd Party',
                                         '3'=>'Any'  ))
                ->removeDecorator("htmlTag");
        $this->addElement($vehicle);
		
		$vehicle_no = $this->createElement('text','vehicle_no')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                ->setRequired(true)
				//->setAttrib('autocomplete','off')
               // ->setAttrib('required','true')
                ->removeDecorator("htmlTag");
        $this->addElement($vehicle_no); */
		
       /* $ErpEmployees_model = new Application_Model_ErpEmployees();
        $data = $ErpEmployees_model->getDropDownList();
        $purchase_invoice_authorized_signature = $this->createElement('select', 'purchase_invoice_authorized_signature')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)
                ->addMultiOptions(array('' => 'Select Authorized Signature Employee'))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($purchase_invoice_authorized_signature); */
    }

}
