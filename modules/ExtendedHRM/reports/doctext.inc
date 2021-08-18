<?php
/****************************************
/*  Author 	: Kvvaradha
/*  Module 	: Extended HRM
/*  E-mail 	: admin@kvcodes.com
/*  Version : 1.0
/*  Http 	: www.kvcodes.com
*****************************************/

	
	if ($this->formData['doctype'] == 'payslip'){
		$this->title = _("Payslip");
		$addr_title = _("Payable To");
		$this->formData['document_name'] =_("Payroll ID.");
	}elseif ($this->formData['doctype'] == 'loan'){
		$this->title = _("Loan Details");
		$addr_title = _("Employee Info");
		$this->formData['document_name'] =_("Employee ID.");
	}elseif($this->formData['doctype'] == 'paysummary'){
		$this->title = _("Payroll Summary");
		$addr_title = _("Pay Details");
		$this->formData['document_name'] =_(" ");
	}
	
	if($this->formData['doctype'] == 'paysummary'){
		$Addr1 = array(
			'title' => $addr_title,
			'id' => (isset($this->formData['empl_id']) ? $this->formData['empl_id']: ''),
			'year' => $this->formData['year']
			);
	}else{
		$Addr1 = array(
			'title' => $addr_title,
			'id' => $this->formData['empl_id'],
			'name' => @$this->formData['empl_name'] ? $this->formData['empl_name'] : @$this->formData['empl_name'],
			'dept' => $this->formData['department'], 
			'desig' => $this->formData['desig'], 
			'joining' => $this->formData['joining']
		);
	}
	
	$this->formData['domicile'] = $this->company['domicile'];

	$Footer[0] = _("All amounts stated in Company  Currency") ;

		// default values
	if (!isset($this->formData['document_date']))
		$this->formData['document_date'] = Today();

	if (!isset($this->formData['document_number']))
		$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference']) ? $this->formData['reference'] : @$this->formData['trans_no'];

	// footer generic content
	if (@$this->formData['bank_name'])
		$Footer[] = _("Bank"). ": ".$this->formData['bank_name']. ", " . _("Bank Account") . ": " . $this->formData['acc_no'];


	if ($this->formData['doctype'] == 'payslip')
 		$Footer[] = _("* Subject to Realisation of the Cheque.");

	if ($this->params['comments'] != '')
		$Footer[] = $this->params['comments'];

	if(isset($Addr1['name']))
		$this->formData['recipient_name'] = $Addr1['name'];
?>
