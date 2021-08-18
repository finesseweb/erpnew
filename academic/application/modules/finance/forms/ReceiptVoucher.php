<?php

class Finance_Form_ReceiptVoucher extends Zend_Form {   

    public function init() {
         $ErpDealerMaster_model = new Application_Model_ErpDealerMaster();
        $data = $ErpDealerMaster_model->getAllDropDownList();
		$dealer_id = $this->createElement('select', 'dealer_id')
                 ->removeDecorator('label')
                ->setAttrib('class', array('form-control','select2'))
                //->setAttrib('required', 'required')				
                //->setRequired(true)
                ->addMultiOptions(array('' => 'Select Customer'))
                ->addMultiOptions($data)				
                ->removeDecorator("htmlTag");
        $this->addElement($dealer_id);
		
		/* $invoice_id = $this->createElement('select', 'invoice_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
               // ->setRequired(true)                
                ->addMultiOptions(array('' => 'Select Invoice ID'))
				->setRegisterInArrayValidator(false)
                ->removeDecorator("htmlTag");
        $this->addElement($invoice_id); */
       
        $paid_amount = $this->createElement('text', 'paid_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
				->setAttrib('onchange', 'select_payment(this.value);')
				->setAttrib('onkeyup', 'select_payment(this.value);')
				->setAttrib('onkeypress', 'select_payment(this.value);')
                //->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($paid_amount);
		
		/* $balance = $this->createElement('text', 'balance')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
               // ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator("htmlTag");
        $this->addElement($balance); */

        /* $employee_list_model = new Application_Model_ErpEmployees();
        $data = $employee_list_model->getEmployeeList();
        $authorized_signature = $this->createElement('select', 'authorized_signature')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)                
                ->addMultiOptions(array('' => 'Select Approved By '))
                ->addMultiOptions($data)
                ->removeDecorator("htmlTag");
        $this->addElement($authorized_signature); */
		$payment_by = $this->createElement('select', 'payment_by')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                ->setAttrib('required', 'required')
                ->setRequired(true)                
                ->addMultiOptions(array('' => 'Select Payment Mode' ,'1' => 'CASH', '2' => 'CHEQUE', '3' => 'NEFT/RTGS'))                
                ->removeDecorator("htmlTag");
        $this->addElement($payment_by);
		
		/* $payment_by = $this->createElement('text', 'payment_by')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($payment_by); */
		
		$total_amount = $this->createElement('text', 'total_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($total_amount);
		
	/* 	$transaction_amount = $this->createElement('text', 'transaction_amount')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
               // ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($transaction_amount); */
		
		/* $opening_balance1 = $this->createElement('text', 'opening_balance1')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
				->setAttrib('readyonly', 'readyonly')
                ->removeDecorator("htmlTag");
        $this->addElement($opening_balance1); */
		
		
		$add_date = $this->createElement('text', 'add_date')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control','datepicker'))
                ->setAttrib('required', 'required')
                ->removeDecorator("htmlTag");
        $this->addElement($add_date);
		
		$remarks = $this->createElement('textarea','remarks')
                ->removeDecorator('label')->setAttrib('class',array('form-control','valid'))
                //->setRequired(true)
               ->setAttrib("class","smallinput valid")->setAttrib('rows','3')
                ->setAttrib('cols','56')
                ->removeDecorator("htmlTag");
        $this->addElement($remarks);
		$Bank_master = new Application_Model_ErpBankMaster();
		$data = $Bank_master->getDropDownList();
		$bank_id = $this->createElement('select', 'bank_id')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
				->addMultiOptions(array(''=>'Select'))
				->addMultiOptions($data)
                //->setAttrib('required', 'required')
				//->setAttrib('readonly', 'readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($bank_id);
		
		$cheque_no = $this->createElement('text', 'cheque_no')
                ->removeDecorator('label')
                ->setAttrib('class', array('form-control'))
                //->setAttrib('required', 'required')
				//->setAttrib('readonly', 'readonly')
                ->removeDecorator("htmlTag");
        $this->addElement($cheque_no);
		/* $file_upload = $this->createElement('file','file_upload ')
                ->removeDecorator('label')->setAttrib('class',array('form-control'))
                 ->addValidator('Size', false, 204800000)
                ->addValidator('Extension', false, 'jpg,jpeg,png,bmp,gif,jpeg,pjpeg,jpg,png,gif,bmp,doc,docx,log,msg,pages,rtf,txt,wpd,wps,csv,dat,efx,gbr,key,pps,ppt,pptx,sdf,tax2010,vcf,xml,aif,iff,m3u,m4a,mid,mp3,mpa,ra,wav,wma,3g2,3gp,asf,asx,avi,flv,mp4,mpg,indd,pct,,qxd,qxp,rels,xlr,xls,xlsx,7z,deb,gz,pkg,rar,rpm,sitx,tar.gz,zip,zipx,dmg,iso,toast,vcd,dbx,pdf')
                ->setAttrib('enctype', 'multipart/form-data')               
                ->removeDecorator("htmlTag");
        $this->addElement($file_upload);  */
		
		/* $payment_type = $this->createElement('text','payment_type')
						->removeDecorator('label')
						->setAttrib('class',array('form-control'))
						->setAttrib('required','required')
						->removeDecorator("htmlTag");
		$this->addElement($payment_type);		

			$description = $this->createElement('textarea','description')
						->removeDecorator('label')
						->setAttrib('class',array('form-control'))
						->setAttrib('rows',3)
						->removeDecorator("htmlTag");
			$this->addElement($description);		 */	
    }

}
