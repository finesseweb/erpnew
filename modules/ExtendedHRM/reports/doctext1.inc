<?php
/**********************************************************************
  
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
/*****
*
*	Set document type dependent elements of common page layout.
*
*/
/*	$Addr1 = array(
			'title' => _("Charge To"),
			'name' => @$this->formData['br_name'] ? $this->formData['br_name'] : @$this->formData['DebtorName'],
			'address' => @$this->formData['br_address'] ? $this->formData['br_address'] : @$this->formData['address']
	);
	$Addr2 = array(
			'title' => _("Delivered To"),
			'name' => @$this->formData['deliver_to'],
			'address' => @$this->formData['delivery_address']
	); */

	// default item column headers
	//$this->headers = array(_("Item Code"), _("Item Description"), _("Quantity"),
		//_("Unit"), _("Price"), _("Discount %"), _("Total"));

	// for links use 'text' => 'url'
	$Footer[0] = _("All amounts stated in") . " - " . @$this->formData['curr_code'];

	switch ($this->formData['doctype'])
	{
		case ST_SALESQUOTE:
			$this->title = _("SALES QUOTATION");
			$this->formData['document_name'] =_("Quotation No.");
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			$aux_info = array(
				_("Customer's Reference") => $this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Quotation No") => $this->formData['order_no'],
				_("Valid until") => sql2date($this->formData['delivery_date']),
			);
			break;
			
			case ST_SALESENQUIRY:
			$this->title = _("SALES ENQUIRY");
			$this->formData['document_name'] =_("ENQUIRY No.");
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			$aux_info = array(
				_("Customer's Reference") => $this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our ENQUIRY No") => $this->formData['order_no'],
				_("Valid until") => sql2date($this->formData['delivery_date']),
			);
			break;

		case ST_SALESORDER:
			$this->title = ($print_as_quote==1 ? _("QUOTE") : _("SALES ORDER"));
			$this->formData['document_name'] =_("Order No.");
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			$this->formData['document_amount'] = $this->formData['order_no'];

			$aux_info = array(
				_("Customer's Reference") => $this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Order No") => $this->formData['order_no'],
				_("Delivery Date") => sql2date($this->formData['delivery_date']),
			);
			break;

		case ST_CUSTDELIVERY:
			$this->title = ($packing_slip==1 ? _("PACKING SLIP") : _("DELIVERY NOTE"));
			$this->formData['document_name'] = _("Delivery Note No.");
			if (@$packing_slip)
				$Payment_Terms = '';
			$ref = $this->formData['order_'];
			if ($print_invoice_no == 0)
			{
				$ref = get_reference(ST_SALESORDER, $this->formData['order_']);
				if (!$ref)
					$ref = $this->formData['order_'];
			}
			$aux_info = array(
				_("Customer's Reference") => $this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Order No") => $ref,
				_("To Be Invoiced Before") => sql2date($this->formData['due_date']),
			);
			break;

		case ST_CUSTCREDIT:
			$this->title = _("CREDIT NOTE");
			$this->formData['document_name'] =_("Credit No.");
			$Footer[0] = _("Please quote Credit no. when paying. All amounts stated in") . " - " . $this->formData['curr_code'];

			$aux_info = array(
				_("Customer's Reference") => @$this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Order No") => $this->formData['order_'],
				_("Due Date") => '',
			);
			break;

		case ST_SALESINVOICE:
			$this->title = _("INVOICE");
			$this->formData['document_name'] =_("Invoice No.");
			$this->formData['domicile'] = $this->company['domicile'];
			$Footer[0] = _("Please quote Invoice no. when paying. All amounts stated in"). " - " . $this->formData['curr_code'];

			$deliveries = get_sales_parent_numbers(ST_SALESINVOICE, $this->formData['trans_no']);
			if ($print_invoice_no == 0)
			{
				foreach($deliveries as $n => $delivery) {
					$deliveries[$n] = get_reference(ST_CUSTDELIVERY, $delivery);
				}
			}
			$aux_info = array(
				_("Customer's Reference") => $this->formData["customer_ref"],
				_("Sales Person") => get_salesman_name($this->formData['salesman']),
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Delivery Note No.") => implode(',', $deliveries),
				_("Due Date") => sql2date($this->formData['due_date']),
			);
			break;

		case ST_SUPPAYMENT:
			global $systypes_array;

			$this->title = _("REMITTANCE");
			$this->formData['document_name'] =_("Remittance No.");
			$Addr1['title'] = _("Order To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Charge To");
			$Addr2['name'] = '';
			$Addr2['address'] = '';

			$aux_info = array(
				_("Customer's Reference") => $this->formData['supp_account_no'],
				_("Type") =>$systypes_array[$this->formData["type"]],
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Supplier's Reference") => '',
				_("Due Date") => sql2date($this->formData['tran_date']),
			);
			$this->headers = array(_("Trans Type"), _("#"), _("Date"), _("Due Date"), _("Total Amount"), _("Left to Allocate"), _("This Allocation"));
			break;
			
case ST_PURCHORDER:
			$this->title = _("PURCHASE ORDER");
			$this->formData['document_name'] =_("Purchase Order No.");
			$Addr1['title'] = _("Order To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			//$Addr2['address'] = $this->company['postal_address']; No, don't destroy delivery address!
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];

			$aux_info = array(
				_("Customer's Reference") => $this->formData['supp_account_no'],
				_("Sales Person") => $this->formData['contact'],
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Order Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break;
			
		/* case ST_PURCHORDER:
			$this->title = _("PURCHASE ORDER");
			$this->formData['document_name'] =_("Purchase Order No.");
			$Addr1['title'] = _("Order To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			//$Addr2['address'] = $this->company['postal_address']; No, don't destroy delivery address!
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
				$sql = "SELECT purch_order.desig_group,depart.description,empl_info.empl_firstname FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 
			
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			if($row[0] == 1){
			$desig = 'Labour';
			}else if($row[0] == 2){
			$desig = 'Engineer';
			}else if($row[0] == 3){
			$desig = 'Planning';
			}else if($row[0] == 4){
			$desig = 'Executive';
			}else if($row[0] == 5){
			$desig = 'Manager';
			}else if($row[0] == 6){
			$desig = 'Other';
			}

			$aux_info = array(
				_("Designation") => $desig,
				_("Department") => $row[1],
				_("Requested by") => $row[2],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Order Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break; */
			case ST_PURCHENQUIRY:
			$this->title = _("PURCHASE ENQUIRY");
			$this->formData['document_name'] =_("Enquirynq No.");
			$Addr1['title'] = _("ENQUIRY To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			//$Addr2['address'] = $this->company['postal_address']; No, don't destroy delivery address!
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];

			$aux_info = array(
				_("Customer's Reference") => $this->formData['supp_account_no'],
				_("Sales Person") => $this->formData['contact'],
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Enquiry Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break;
			
			
			/* case ST_PURCHENQUIRY:
			$this->title = _("PURCHASE ENQUIRY");
			$this->formData['document_name'] =_("Enquirynq No.");
			$Addr1['title'] = _("ENQUIRY To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			//$Addr2['address'] = $this->company['postal_address']; No, don't destroy delivery address!
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			$sql = "SELECT purch_order.desig_group,depart.description,empl_info.empl_firstname FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 
			
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			if($row[0] == 1){
			$desig = 'Labour';
			}else if($row[0] == 2){
			$desig = 'Engineer';
			}else if($row[0] == 3){
			$desig = 'Planning';
			}else if($row[0] == 4){
			$desig = 'Executive';
			}else if($row[0] == 5){
			$desig = 'Manager';
			}else if($row[0] == 6){
			$desig = 'Other';
			}
			$aux_info = array(
				_("Designation") => $desig,
				_("Department") => $row[1],
				_("Requested By") => $row[2],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Enquiry Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break;
			 */
			case ST_PURCHINDENT:
			$this->title = _("PURCHASE INDENT");
			$this->formData['document_name'] =_("Indent No.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			// $sql = "SELECT purch_order.desig_group,depart.description,empl_info.empl_firstname FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 
			
			$sql ="SELECT desig_groups.name as dgs_group,desig_master.name as dsg_name,depart.description,empl_info.empl_firstname,purch_order.reference,purch_order.ord_date FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id 
LEFT JOIN ".TB_PREF."kv_desig_group AS desig_groups ON desig_groups.id = purch_order.desig_group LEFT JOIN ".TB_PREF."designation_master AS desig_master ON desig_master.id = purch_order.designation_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 

$res = db_query($sql, "could not retreive the department name");
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			
			$aux_info = array(
				_("Designation Group") => $row[0],
				_("Designation") => $row[1],
				_("Department") => $row[2],
				_("Requested By") => $row[3],
				_("Reference") => 'IND-'.$this->formData['reference'],
				_("Indent Date") => sql2date($this->formData['ord_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"), _("Quantity"));
			break;
			
			case ST_INVENTORYISSUE:
			$this->title = _("INVENTORY ISSUE");
			$this->formData['document_name'] =_("Issue No.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			 $sql = "SELECT desig_groups.name as dgs_group,desig_master.name as dsg_name,depart.description,empl_info.empl_firstname,purch_order.reference,purch_order.ord_date FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id LEFT JOIN ".TB_PREF."kv_desig_group AS desig_groups ON desig_groups.id = purch_order.desig_group LEFT JOIN ".TB_PREF."designation_master AS desig_master ON desig_master.id = purch_order.designation_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 
			
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			
			$aux_info = array(
				_("Designation Group") => $row[0],
				_("Designation") => $row[1],
				_("Department") => $row[2],
				_("Issued To") => $row[3],
				_("Reference") => $this->formData['reference'],
				_("Issue Date") => sql2date($this->formData['ord_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"), _("Quantity"));
			break;
			
			case SA_PREVENTVIEW:
				//display_error('sdfsdf');
			$this->title = _("");
			$this->formData['document_name'] =_("Preventive ID.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
		//	display_error($_POST['PARAM_0']);
		  $prevent_id = $_POST['PARAM_0'];
		  $this->formData['document_number']= $_POST['PARAM_0'];
			 $sql = "SELECT prev.*,CONCAT(s.description,'--',freq.frequency_name) AS utfrequency,con.supp_name AS contractor FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= utly.items_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE prev.prevent_id= ".$prevent_id." AND utly.type=1 "; 
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			$this->formData['document_date'] = $row[1];
			/*$aux_info = array(
				_("Maintenace Date") =>$row[1],
				_("Utility & Frequency") => $row[9],
				_("Contractor") => $row[10],
				_("Observation Date") => $row[5],
				_("Observations") => $row[6],
			
			);*/
			
			//$this->headers = array(_("Item Code"), _("Quantity"));
			$this->headers = array(_(""), _(""), _(''),_(""), _(""), _(''));
			break;
			
			case SA_OPEN:
				//display_error('sdfsdf');
			$this->title = _("");
			$this->formData['document_name'] =_("Preventive ID.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
		//	display_error($_POST['PARAM_0']);
		  $prevent_id = $_POST['PARAM_0'];
		  $this->formData['document_number']= $_POST['PARAM_0'];
			 $sql = "SELECT prev.*,CONCAT(s.description,'--',freq.frequency_name) AS utfrequency,con.supp_name AS contractor FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= utly.items_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE prev.prevent_id= ".$prevent_id." AND utly.type=1 "; 
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			$this->formData['document_date'] = $row[1];
			/*$aux_info = array(
				_("Maintenace Date") =>$row[1],
				_("Utility & Frequency") => $row[9],
				_("Contractor") => $row[10],
				_("Observation Date") => $row[5],
				_("Observations") => $row[6],
			
			);*/
			
			//$this->headers = array(_("Item Code"), _("Quantity"));
			$this->headers = array(_(''), _(''));
			break;
			
			
			
			case SA_BREAKDOWNVIEW:
				//display_error('sdfsdf');
			$this->title = _("BREAKDOWN MAINTENANCE");
			$this->formData['document_name'] =_("Breakdown ID.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
		//	display_error($_POST['PARAM_0']);
		  $break_id = $_POST['PARAM_0'];
		  $this->formData['document_number']= $_POST['PARAM_0'];
			 $sql = "SELECT break.*,CONCAT(break.break_st_time,'--',break.break_end_time) AS stend_time,CASE  WHEN(utly.name !='') THEN  utly.name  WHEN (utly.items_id !='') THEN s.description END AS u_name,con.supp_name AS contractor FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= break.utility_id LEFT JOIN ".TB_PREF."stock_master AS s ON  utly.items_id=s.stock_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= break.contractor_id WHERE break.break_id= ".$break_id." "; 
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			$this->formData['document_date'] = $row[1];
			$aux_info = array(
				_("Maintenace Date") =>$row[1],
				_("Utility / Process") => $row[10],
				//_("Frequency") => $row[9],
				_("Contractor") => $row[11],
				_("Start Time & End Time") => $row[9],
				_("Reason") => $row[6],
			//	_("Corrective Action Suggested") => $row[6],
			//	_("Corrective Action Initiated") => $row[7],
			);
			
			$this->headers = array(_("Item Code"), _("Quantity"));
			break;
			
			case SA_PROCESSVIEW:
				//display_error('sdfsdf');
			$this->title = _("PROCESS MAINTENANCE");
			$this->formData['document_name'] =_("Process ID.");
			$Addr1['title'] = '';
			$Addr1['name'] = '';
			$Addr1['address'] = '';
			$Addr2['title'] = '';
			$Addr2['name'] = '';
			 $Addr2['address'] = '';
		//	display_error($_POST['PARAM_0']);
		  $process_id = $_POST['PARAM_0'];
		  $this->formData['document_number']= $_POST['PARAM_0'];
			 $sql = "SELECT proc.*,CONCAT(utly.name,'--',freq.frequency_name) AS utfrequency,con.supp_name AS contractor FROM ".TB_PREF."process_maintenance AS proc LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= proc.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= proc.frequency_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= proc.contractor_id WHERE proc.process_id= ".$process_id." AND utly.type=2 "; 
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			$this->formData['document_date'] = $row[1];
			$aux_info = array(
				_("Maintenace Date") => $row[1],
				_("Process & Frequency") => $row[9],
				_("Contractor") => $row[10],
				_("Observation Date") => $row[5],
				_("Observations") => $row[6],
			//	_("Corrective Action Suggested") => $row[6],
			//	_("Corrective Action Initiated") => $row[7],
			);
			
			$this->headers = array(_("Item Code"), _("Quantity"));
			$this->headers = array(_("S. No"), _("Parameters / Scope of work"), "");
			break;
			
			
			
			/* case ST_PURCHQUOTE:
			$this->title = _("PURCHASE QUOTE");
			$this->formData['document_name'] =_("Quotation No.");
			$Addr1['title'] = _("QUOTATION To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			$Addr2['address'] = '';
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];
			 $sql = "SELECT purch_order.desig_group,depart.description,empl_info.empl_firstname FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id WHERE purch_order.order_no = ".$this->formData['order_no']." "; 
			
			$res = db_query($sql, "could not retreive the department name");
			$row = db_fetch_row($res);
			if($row[0] == 1){
			$desig = 'Labour';
			}else if($row[0] == 2){
			$desig = 'Engineer';
			}else if($row[0] == 3){
			$desig = 'Planning';
			}else if($row[0] == 4){
			$desig = 'Executive';
			}else if($row[0] == 5){
			$desig = 'Manager';
			}else if($row[0] == 6){
			$desig = 'Other';
			}
			$aux_info = array(
				_("Designation") => $desig,
				_("Department") => $row[1],
				_("Requested By") => $row[2],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Quotation Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break; */
			
		case ST_PURCHQUOTE:
			$this->title = _("PURCHASE QUOTE");
			$this->formData['document_name'] =_("Quotation No.");
			$Addr1['title'] = _("QUOTATION To");
			$Addr1['name'] = $this->formData['supp_name'];
			$Addr1['address'] = $this->formData['address'];
			$Addr2['title'] = _("Deliver To");
			$Addr2['name'] = $this->company['coy_name'];
			//$Addr2['address'] = $this->company['postal_address']; No, don't destroy delivery address!
			$this->formData['document_date'] = $this->formData['ord_date'];
			$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
				? $this->formData['reference'] : $this->formData['order_no'];

			$aux_info = array(
				_("Customer's Reference") => $this->formData['supp_account_no'],
				_("Sales Person") => $this->formData['contact'],
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Supplier's Reference") => $this->formData['requisition_no'],
				_("Quotation Date") => sql2date($this->formData['document_date']),
			);

			$this->headers = array(_("Item Code"), _("Item Description"),
				_("Delivery Date"), _("Quantity"), 	_("Unit"), _("Price"), _("Total"));
			break;
	
 
		case ST_CUSTPAYMENT:
			global $systypes_array;

			$this->title = _("RECEIPT");
			$this->formData['document_name'] =_("Receipt No.");
			$Addr1['title'] = _("With thanks from");
			$aux_info = array(
				_("Customer's Reference") => $this->formData["debtor_ref"],
				_("Type") =>$systypes_array[$this->formData["type"]],
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Order No") => $this->formData['order_'],
				_("Due Date") => sql2date($this->formData['tran_date']),
			);
			$this->headers = array(_("Trans Type"), _("#"), _("Date"), _("Due Date"), _("Total Amount"), _("Left to Allocate"), _("This Allocation"));
			break;

		case ST_WORKORDER:
			global $wo_types_array;

			$this->title = _("WORK ORDER");
			$this->formData['document_name'] =_("Work Order No.");
			$this->formData['document_date'] = $this->formData['date_'];
			$this->formData['document_number'] = $this->formData['id'];
			$Addr1['name'] = $this->formData['location_name'];
			$Addr1['address'] = $this->formData['delivery_address'];
			$aux_info = array(
				_("Reference") => $this->formData['wo_ref'],
				_("Type") => $wo_types_array[$this->formData["type"]],
				_("Manufactured Item") => $this->formData["StockItemName"],
				_("Into Location") => $this->formData["location_name"],
				_("Quantity") => $this->formData["units_issued"],
			);
			$Payment_Terms = _("Required By").": ".sql2date($this->formData["required_by"]);
			$this->headers = array(_("Item Code"), _("Item Description"),
				_("From Location"), _("Work Centre"), 	_("Unit Quantity"), _("Total Quantity"), _("Units Issued"));
			unset($Footer[0]);
			break;


		case ST_STATEMENT:
			$this->formData['document_name'] = '';
			$this->formData['domicile'] = $this->company['domicile'];
			$Payment_Terms = '';
			$this->title = _("STATEMENT");
			$aux_info = array(
				_("Customer's Reference") => '',
				_("Sales Person") => '',
				_("Your VAT no.") => $this->formData['tax_id'],
				_("Our Order No") => '',
				_("Delivery Date") => '',
			);
			$this->headers = array(_("Trans Type"), _("#"), _("Date"), _("DueDate"), _("Charges"),
				_("Credits"), _("Allocated"), _("Outstanding"));
	}

	// default values
	if (!isset($this->formData['document_date']))
		$this->formData['document_date'] = $this->formData['tran_date'];

	if (!isset($this->formData['document_number']))
		$this->formData['document_number'] = $print_invoice_no == 0 && isset($this->formData['reference'])
			? $this->formData['reference'] : @$this->formData['trans_no'];
/*if($this->formData['doctype'] != ST_PURCHINDENT){
	if (!isset($Payment_Terms))
	{
		$id = $this->formData['payment_terms'];
		$sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
		$result = db_query($sql,"could not get paymentterms");
		$row = db_fetch($result);
		$Payment_Terms = _("Payment Terms") . ': ' . $row["terms"];
	}
	}*/

	// footer generic content
	if (@$this->formData['bank_name'])
		$Footer[] = _("Bank"). ": ".$this->formData['bank_name']. ", " . _("Bank Account") . ": " . $this->formData['bank_account_number'];

	if (@$this->formData['payment_service'])	//payment link
	{
		$amt = number_format($this->formData["ov_freight"] + $this->formData["ov_gst"] + $this->formData["ov_amount"], user_price_dec());
		$service = $this->formData['payment_service'];
		$url = payment_link($service, array(
			'company_email' => $this->company['email'],
			'amount' => $amt,
			'currency' => $this->formData['curr_code'],
			'comment' => $this->title . " " . $this->formData['reference']
			));
		$Footer[_("You can pay through"). " $service: "] = "$url";
	}

	if ($this->formData['doctype'] == ST_CUSTPAYMENT)
 		$Footer[] = _("* Subject to Realisation of the Cheque.");

	if ($this->params['comments'] != '')
		$Footer[] = $this->params['comments'];

	if (($this->formData['doctype'] == ST_SALESINVOICE || $this->formData['doctype'] == ST_STATEMENT) && $this->company['legal_text'] != "") 
	{
		foreach(explode("\n", $this->company['legal_text']) as $line)
			$Footer[] = $line;
	}

	$this->formData['recipient_name'] = $Addr1['name'];
?>
