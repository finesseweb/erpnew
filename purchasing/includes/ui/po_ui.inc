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
include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");

//--------------------------------------------------------------------------------------------------



 
function copy_from_cart()
{
	$cart = &$_SESSION['PO'];
	$suppliers = explode(',',$cart->suppliers_id);
	$_POST['suppliers'] = $suppliers;
	$_POST['supplier_id'] = $cart->supplier_id;
	$_POST['desig_group'] =  $cart->desig_group;
	$_POST['designation_id'] =  $cart->designation_id;
	$_POST['department_id'] = $cart->department_id ;
	$_POST['employee_id'] = $cart->employee_id ;
	$_POST['OrderDate'] = $cart->orig_order_date;
	if ($cart->trans_type==ST_SUPPINVOICE)
		$_POST['due_date'] = $cart->due_date;
    $_POST['supp_ref'] = $cart->supp_ref;
	
    $_POST['ref'] = $cart->reference;
	if ($cart->trans_type==ST_PURCHQUOTE)
	   $tmpname = $_FILES['filename']['tmp_name'];
		$dir =  company_path()."/attachments";
		if (!file_exists($dir))
		{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}
         
		$filename = basename($_FILES['filename']['name']);
		//display_error($cart->filename);die;
		$filesize = $_FILES['filename']['size'];
		$filetype = $_FILES['filename']['type'];
		move_uploaded_file($tmpname, $dir."/".$filename);
	$_POST['filename'] = $filename;
	$_POST['Comments'] = $cart->Comments;
    $_POST['StkLocation'] = $cart->Location;
    $_POST['delivery_address'] = $cart->delivery_address;
	$_POST['_ex_rate'] = $cart->ex_rate;
	$_POST['suppliers_id'] = $suppliers;
	$_POST['enq_ref'] = $cart->enq_ref;
	
    foreach($cart->tax_overrides as $id => $value)
	   $_POST['mantax'][$id] = price_format($value);
}

function copy_to_cart()
{
       
	$cart = &$_SESSION['PO'];
	$suppliers = implode(',',$_POST['suppliers']);
	
	$cart->suppliers_id = $suppliers;	
	$cart->supplier_id = $_POST['supplier_id'];	
	$cart->desig_group = $_POST['desig_group'];
	$cart->designation_id = $_POST['designation_id'];
	$cart->department_id = $_POST['department_id'];
	$cart->employee_id = $_POST['employee_id'];
	$cart->orig_order_date = $_POST['OrderDate'];
	if ($cart->trans_type==ST_SUPPINVOICE)
		$cart->due_date = $_POST['due_date'];
	$cart->reference = $_POST['ref'];
	$cart->supp_ref = $_POST['supp_ref'];
	$cart->enq_ref = $_POST['enq_ref'];
	if ($cart->trans_type==ST_PURCHQUOTE)
	
	   $tmpname = $_FILES['filename']['tmp_name'];
	  // display_error($tmpname);die;
		$dir =  company_path()."/attachments";
		if (!file_exists($dir))
		{
			mkdir ($dir,0777);
			$index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
			$fp = fopen($dir."/index.php", "w");
			fwrite($fp, $index_file);
			fclose($fp);
		}  
		$filename = basename($_FILES['filename']['name']);
		
		$filesize = $_FILES['filename']['size'];
		$filetype = $_FILES['filename']['type'];
		move_uploaded_file($tmpname, $dir."/".$filename);
	    $cart->filename = $filename;
	$cart->Comments = $_POST['Comments'];	
	$cart->Location = $_POST['StkLocation'];
	$cart->delivery_address = $_POST['delivery_address'];
	$cart->suppliers_id = $suppliers; 
	$cart->ex_rate = input_num('_ex_rate', null);
    if (isset($_POST['mantax'])) {
		foreach($_POST['mantax'] as $id => $tax) {
			$cart->tax_overrides[$id] = user_numeric($_POST['mantax'][$id]); }
	}
}
// ------------------------------------------------------------------------------

function get_supplier_details_to_order(&$order, $supplier_id)
{
	$sql = "SELECT curr_code, supp_name, tax_group_id,address_state, supp.tax_included,
			supp.credit_limit - Sum(IFNULL(ov_amount + ov_gst + ov_discount,0)) as cur_credit,
				terms.terms, terms.days_before_due, terms.day_in_following_month
		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id
			 LEFT JOIN ".TB_PREF."payment_terms terms ON supp.payment_terms=terms.terms_indicator
		WHERE supp.supplier_id = ".db_escape($supplier_id)."
		GROUP BY
			  supp.supp_name";

	$result = db_query($sql, "The supplier details could not be retreived");

	$myrow = db_fetch($result);
	$order->credit = $myrow["cur_credit"];
	$order->terms = array( 
		'description' => $myrow['terms'],
		'days_before_due' => $myrow['days_before_due'], 
		'day_in_following_month' => $myrow['day_in_following_month'] );

	$_POST['supplier_id'] = $supplier_id;
	$_POST['supplier_name'] = $myrow["supp_name"];
	$_POST['curr_code'] = $myrow["curr_code"];
       
	$order->set_supplier($supplier_id, $myrow["supp_name"], $myrow["curr_code"], 
		$myrow["tax_group_id"], $myrow["tax_included"],$myrow["address_state"]);
}
 
//---------------------------------------------------------------------------------------------------

function create_new_po($trans_type, $trans_no,$move="")
{

    
	global $Refs;
	if (isset($_SESSION['PO']))
		unset ($_SESSION['PO']->line_items, $_SESSION['PO']);

	$cart = new purch_order;
     
	$_POST['OrderDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['OrderDate']))
		$_POST['OrderDate'] = end_fiscalyear();
	$cart->due_date = $cart->orig_order_date = $_POST['OrderDate'];

	$cart->trans_type = $trans_type;
	$cart->order_no = $trans_no;
	/*read in all the selected order into the Items cart  */
	if ($trans_no) {
		read_po($trans_no, $cart);
		$cart->order_no = $trans_no;
	} else
	 
	  $cart->reference = $Refs->get_next1($trans_type);
	  $cart->trans_type = $trans_type;
          
	if($move=="IND-ENQ")
   {
     $cart->reference = $Refs->get_next1(60);
	 $cart->trans_type = 60;
     unset($cart->order_no);	 
   }
   if($move=="ENQ-QUOT")
   {

     $cart->reference = $Refs->get_next1(70);
	 $cart->trans_type = 70;
     unset($cart->order_no);	 
   }
   
   if($move=="QUOT-ORD")
   {
     
     $cart->reference = $Refs->get_next1(18);
	 $cart->trans_type = 18;
	 unset($cart->order_no);
   }
   $_SESSION['PO'] = &$cart;
   //print_r($_SESSION);die;
}
//------------------------------------------Ritika Kumari (function for get data from location)-------------
function create_new_po_location($trans_type, $trans_no,$move="", $location_id)
{
	global $Refs;
	if (isset($_SESSION['PO']))
		unset ($_SESSION['PO']->line_items, $_SESSION['PO']);

	$cart = new purch_order;
     
	$_POST['OrderDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['OrderDate']))
		$_POST['OrderDate'] = end_fiscalyear();
	$cart->due_date = $cart->orig_order_date = $_POST['OrderDate'];

	$cart->trans_type = $trans_type;
	$cart->order_no = $trans_no;
	/*read in all the selected order into the Items cart  */
	
	read_po_location($trans_no, $cart,'',$location_id);
	// if ($trans_no) {
	// 	read_po_location($trans_no, $cart,$location_id);
	// 	$cart->order_no = $trans_no;
	// } else
	 
	//   $cart->reference = $Refs->get_next($trans_type);
	//   $cart->trans_type = $trans_type;
          
	// if($move=="IND-ENQ")
 //   {
 //     $cart->reference = $Refs->get_next(60);
	//  $cart->trans_type = 60;
 //     unset($cart->order_no);	 
 //   }
 //   if($move=="ENQ-QUOT")
 //   {

 //     $cart->reference = $Refs->get_next(70);
	//  $cart->trans_type = 70;
 //     unset($cart->order_no);	 
 //   }
   
 //   if($move=="QUOT-ORD")
 //   {
     
 //     $cart->reference = $Refs->get_next(18);
	//  $cart->trans_type = 18;
	//  unset($cart->order_no);
 //   }
   $_SESSION['PO'] = &$cart;
   //print_r($_SESSION);die;
}
//---------------------------------------------------------------------------------------------------

function display_po_header(&$order)
{

	global $Ajax, $Refs;

	$editable = ($order->order_no == 0);

	start_outer_table(TABLESTYLE2, "width='80%'");
	
	if(($order->trans_type==90) || ($order->trans_type==100)){
	table_section(1);
		if($order->trans_type==90){
		date_row(_("Indent Date:"), 'OrderDate', '', true, 0, 0, 0, null, true);
		}
		else if($order->trans_type==100){
		date_row(_("Issue Date:"), 'OrderDate', '', true, 0, 0, 0, null, true);
		}
		if($order->trans_type==90){
		if ($editable)
		{

			$reference = $Refs->get_next1(90);
			hidden('ref', $reference);
			label_row(_("Reference:"),"IND-".$reference);
			//ref_row(_("Reference:"), ref);
		}
		else
		{
			hidden('ref', $order->reference);
		    label_row(_("Reference:"),"IND-".$order->reference);
		}
		} else if($order->trans_type==100) {
		if ($editable)
		{
			$reference = $Refs->get_next1(100);
			hidden('ref', $reference);
			label_row(_("Reference:"),"ISS-".$reference);
			//ref_row(_("Reference:"), ref);
		}
		else
		{
			hidden('ref', $order->reference);
		    label_row(_("Reference:"),"ISS-".$order->reference);
		}
		
		}
		if($order->trans_type == ST_INVENTORYISSUE){
			$sql = "SELECT location_name FROM ".TB_PREF."locations WHERE loc_code=".db_escape($_GET['NewIssue']);
			$result = db_query($sql);
    		$myrow = db_fetch_row($result);
    		
			locations_list_cells(_("From Location:"), 'StockLocation1', null, $myrow[0], true);
				
			if($_POST['StockLocation1']){
	            meta_forward($_SERVER['PHP_SELF'],'NewIssue='.$_POST['StockLocation1'].'&InventoryNewIssue=Yes');

	        }
        }
		table_section(2);
		departments_list_row(_("Department:"), 'department_id', null, false, true);
		desiggroups_list_row(_("Desgination Group:"), 'desig_group', null,false,true);
		$desig_group = $_POST['desig_group'];
		designation_list_row(_("Desgination:"), 'designation_id', null,false,true,$desig_group);
		
		if($desig_group){
			$Ajax->activate('designation_id');
		}

		$department = $_POST['department_id'];
		$designation = $_POST['designation_id'];

		
		
		if($order->trans_type != ST_INVENTORYISSUE){
			
			employees_list_row(_("Requested by:"), 'employee_id', null, false, true, $department,$desig_group,$designation);
		}else{
			employees_list_row(_("Issued To:"), 'employee_id', null, false, true, $department,$desig_group,$designation);
			

		}
		if($_POST['designation_id']){
			$Ajax->activate('employee_id');
		}

	}

	table_section(1);
        if($order->trans_type == ST_PURCHENQUIRY){ 
        	indent_list_row(_("Indent No:"), 'indent_no', null, true, true);
            if($_POST['indent_no']){
                $_SESSION['new_id']=$_POST['indent_no'];
              
               meta_forward($_SERVER['PHP_SELF'],'RFQ='.$_POST['indent_no'].'&Order-RFQ=Yes');
               
            }
        }
    
    if($order->trans_type == ST_PURCHQUOTE){
        rfq_list_row(_("RFQ No.:"), 'indent_no', null, true, true);
        if($_POST['indent_no']){
          meta_forward($_SERVER['PHP_SELF'],'NewQuotation='.$_POST['indent_no'].'&Order-NewQuotation=Yes');

        }
    }
    if($order->trans_type == ST_PURCHORDER){
        newOrder_list_row(_("Quotation list:"), 'indent_no', null, true, true);
        if($_POST['indent_no']){
            meta_forward($_SERVER['PHP_SELF'],'NewOrder='.$_POST['indent_no'].'&Order-NewOrder=Yes');

        }
    }
    if($order->trans_type == ST_SUPPINVOICE){
        grn_list_row(_("GRN NO.:"), 'grn_no', null, true, true);
        if($_POST['grn_no']){
            meta_forward($_SERVER['PHP_SELF'],'NewInvoice='.$_POST['grn_no'].'&Order-NewInvoice=Yes');

        }
    }


    if($order->trans_type == ST_PURCHENQUIRY){ 
      //mutli_supplier_list_row(_("Suppliers :"), 'suppliers', null, false, true, false, true);
        supplier_list_row(_("Supplier :"), 'supplier_id', null, _('Select'), false, false, false);
    }

    if ($editable)
    {
        if (!isset($_POST['supplier_id']) && (get_global_supplier() != ALL_TEXT))
        	$_POST['supplier_id'] = get_global_supplier();
	if(($order->trans_type != ST_INVENTORYISSUE) && ($order->trans_type != ST_PURCHINDENT) && ($order->trans_type != ST_PURCHENQUIRY)){
            
    	supplier_list_row(_("Supplier:"), 'supplier_id', null, _('Select'), true, false, true);
		}
		
                /*if($order->trans_type == ST_PURCHENQUIRY){ 
                    
                    
				mutli_supplier_list_row(_("Suppliers:"), 'suppliers', null, false, true, false, true);
                  // supplier_list_row(_("Supplier :"), 'supplier_id', null, false, true, false, true);
		}*/
                
	} else {
            // if ($_GET['Order-RFQ']=='Yes' )
            // {
            //     if($order->trans_type == ST_PURCHENQUIRY){ 
            //       //mutli_supplier_list_row(_("Suppliers :"), 'suppliers', null, false, true, false, true);
            //         supplier_list_row(_("Supplier :"), 'supplier_id', null, false, false, false, false);
            //     }

            // }  
            if ($_GET['Order-NewInvoice']=='Yes' )
            {
                if($order->trans_type == ST_SUPPINVOICE){ 
                  //mutli_supplier_list_row(_("Suppliers :"), 'suppliers', null, false, true, false, true);
                    supplier_list_row(_("Supplier :"), 'supplier_id', null, _('Select'), false, false, false);
                }

            }  
            
           if ($_GET['Order-NewQuotation']=='Yes')
            {  
                supplier_list_row(_("Supplier :"), 'supplier_id', null, _('Select'), true, false, true);

            }
            if ($_GET['Order-NewOrder']=='Yes')
            {  
                supplier_list_row(_("Supplier :"), 'supplier_id', null, _('Select'), true, false, true);

            }

            if ($order->trans_type !=60 && $order->trans_type !=70 && $order->trans_type !=ST_PURCHORDER && $order->trans_type !=ST_SUPPINVOICE)
            {
                        //display_error($order->filename);
                        hidden('supplier_id', $order->filename);
                        hidden('supplier_id', $order->supplier_id);
                        label_row(_("Supplier:"), $order->supplier_name);
            }		
    }
	
  

	if ($order->supplier_id != get_post('supplier_id',-1)) {
	 
		$old_supp = $order->supplier_id;
		get_supplier_details_to_order($order, $_POST['supplier_id']); 
	    get_duedate_from_terms($order);
		$_POST['due_date'] = $order->due_date;
		
		

		// supplier default price update
		foreach ($order->line_items as $line_no=>$item) {
			$line = &$order->line_items[$line_no];
			//$line->price =  get_purchase_price ($order->supplier_id, $line->stock_id);
			$line->price = $order->line_items[$line_no]->price;
			$line->quantity =
				$line->quantity/get_purchase_conversion_factor ($old_supp, $line->stock_id)
					*get_purchase_conversion_factor ($order->supplier_id, $line->stock_id);
		}
	    $Ajax->activate('items_table');
	    $Ajax->activate('due_date');
	}
	set_global_supplier($_POST['supplier_id']);
	
	if(($order->trans_type!=90) && ($order->trans_type!=60) && ($order->trans_type!=70) && ($order->trans_type!=100) ){
	
	date_row($order->trans_type==ST_PURCHORDER ? _("Order Date:") :
			($order->trans_type==ST_SUPPRECEIVE ? _("Delivery Date:") : _("Invoice Date:")),
		
		'OrderDate', '', true, 0, 0, 0, null, true);
		}
		
		
		
		else if($order->trans_type==60){
		date_row(_("Enquiry Date:"), 'OrderDate', '', true, 0, 0, 0, null, true);
		}
		else if($order->trans_type==70){
		date_row(_("Quotation Date:"), 'OrderDate', '', true, 0, 0, 0, null, true);
		}
		if(($order->trans_type!=90) && ($order->trans_type!=100)){
		//display_error($order->trans_type);
		if($order->trans_type==18){
			if ($editable)
			{
				$reference = $Refs->get_next(18);
				hidden('ref', $reference);
				label_row(_("Reference:"),"PO-".$reference);
				//ref_row(_("Reference:"), 'ref');
			}
			else
			{
				hidden('ref', $order->reference);
				label_row(_("Reference:"),"PO-".$order->reference);
			}
		 }
		 if($order->trans_type==25){
                    
			if ($editable)
			{
				$reference = $Refs->get_next(25);
				hidden('ref', $reference);
				label_row(_("Reference:"),"GRN-".$reference);
				//ref_row(_("Reference:"), 'ref');
			}
			else
			{
				hidden('ref', $order->reference);
				label_row(_("Reference:"),"GRN-".$order->reference);
			}
		 }
		if($order->trans_type==20){
                    
			if ($editable)
			{
				$reference = $Refs->get_next(20);
				hidden('ref', $reference);
				label_row(_("tt Reference:"),"INV-".$reference);
				//ref_row(_("Reference:"), 'ref');
			}
			else
			{
				hidden('ref', $order->reference);
				label_row(_(" ll Reference:"),"INV-".$order->reference);
			}
		}
		if($order->trans_type==60){
                       // $reference = $Refs->get_next(60);
                      //  hidden('ref', $reference);
                      //  label_row(_("Reference:"),"RFQ-".$reference);
			if ($editable)
			{
				$reference = $Refs->get_next(60);
				hidden('ref', $reference);
				label_row(_("Reference:"),"RFQ-".$reference);
				//ref_row(_("Reference:"), 'ref');
			}
			else
			{
				hidden('ref', $order->reference);
				label_row(_("Reference:"),"RFQ-".$order->reference);
			}
		 }

		 if($order->trans_type==70){
			if ($editable)
			{
				$reference = $Refs->get_next(70);
				hidden('ref', $reference);
				label_row(_("Reference:"),"PQ-".$reference);
				
			}
			else
			{	
				hidden('ref', $order->reference);
				label_row(_("Reference:"),"PQ-".$order->reference);
			}
		 }
		
		/* hrm_empl_desig_groups(_("Desgination Group:"), 'desig_group', null);
		
		departments_list_row(_("Department:"), 'department_id', null, false, true);
		$department = $_POST['department_id'];
		
		if($order->trans_type != ST_INVENTORYISSUE){
			employees_list_row(_("Requested by:"), 'employee_id', null, false, true, $department);
		}else{
			employees_list_row(_("Issued To:"), 'employee_id', null, false, true, $department);
		}
		if(list_updated('department_id')){
					$Ajax->activate('employee_id');
		}   */
} 

	if (isset($_POST['_OrderDate_changed'])) {
		$order->orig_order_date = $_POST['OrderDate'];
	    get_duedate_from_terms($order);
	    $_POST['due_date'] = $order->due_date;
		$Ajax->activate('due_date');
	}
	if(($order->trans_type != ST_PURCHINDENT) && ($order->trans_type != ST_INVENTORYISSUE)){
	supplier_credit_row($order->supplier_id, $order->credit);
        if(isset($_GET['RFQ'])){
            if($_GET['RFQ']!='Yes'){
                $order->curr_code ='';
            }
            if (!is_company_currency($order->curr_code)) {
		label_row(_("Supplier Currency:"), $order->curr_code);
		exchange_rate_display(get_company_currency(), $order->curr_code,
			$_POST['OrderDate']);
                
                
            }
        }else{
            if (!is_company_currency($order->curr_code))
            {
                    label_row(_("Supplier Currency:"), $order->curr_code);
                    exchange_rate_display(get_company_currency(), $order->curr_code,
                            $_POST['OrderDate']);


            }
        }
	
	}
       
        
        
if(($order->trans_type != ST_INVENTORYISSUE) && ($order->trans_type != ST_PURCHINDENT)){
	table_section(2);

	if ($order->trans_type==ST_SUPPINVOICE)
		date_row(_("Due Date:"), 'due_date', '', false, 0, 0, 0, null, true);

		if($order->trans_type ==ST_PURCHQUOTE)
  	text_row(_("Enquiry Reference:"), 'enq_ref', null, 16, 15);
	else
	text_row(_("Supplier's Reference:"), 'supp_ref', null, 16, 15);
	locations_list_row(_("Receive Into:"), 'StkLocation', null, false, true); 
	if ($_POST['StkLocation']) {
		$Ajax->activate('store');
	}
        
        
       
	locations_store_row(_("Store:"), 'store',$_POST['StkLocation'], null, false, true); 
        tax_type(_("Tax Type:"), 'tax_type',$_POST['tax_type'], 'null', false, true); 

	table_section(3);

    if (!isset($_POST['StkLocation']) || $_POST['StkLocation'] == "" ||
    	isset($_POST['_StkLocation_update']) || !isset($_POST['delivery_address']) ||
    	$_POST['delivery_address'] == "")
    {
    	/*If this is the first time the form loaded set up defaults */

        //$_POST['StkLocation'] = $_SESSION['UserStockLocation'];
        $sql = "SELECT delivery_address, phone FROM ".TB_PREF."locations WHERE loc_code=".db_escape($_POST['StkLocation']);
        $result = db_query($sql,"could not get location info");

        if (db_num_rows($result) == 1)
        {
    	  	$loc_row = db_fetch($result);
    	  	$_POST['delivery_address'] = $loc_row["delivery_address"];
			$Ajax->activate('delivery_address');
    	  	$_SESSION['PO']->Location = $_POST['StkLocation'];
    	  	$_SESSION['PO']->delivery_address = $_POST['delivery_address'];

        }
        else
        { /*The default location of the user is crook */
    	  	display_error(_("The default stock location set up for this user is not a currently defined stock location. Your system administrator needs to amend your user record."));
        }
    }

    if (isset($_POST['supplier_id']) || $_POST['supplier_id'] != "" )
    {
        
        $Ajax->activate('_page_body');
        $Ajax->activate('gst');
        $Ajax->activate('cst');
         $Ajax->activate('ist'); 
         $Ajax->activate('line_total');
 $Ajax->activate('stock_id');
  $Ajax->activate('Delete0');
         
         $_POST['gst']='0';
         $_POST['cst']='0';
         $_POST['ist']='0';
         $_POST['line_total']='0';
    	/*If this is the first time the form loaded set up defaults */

        //$_POST['StkLocation'] = $_SESSION['UserStockLocation'];
        $sql = "SELECT address,address_pin, address_state FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($_POST['supplier_id']);
        $result = db_query($sql,"could not get location info");
       
        if (db_num_rows($result) == 1)
        {
    	  	$loc_row = db_fetch($result);
    	  	$_POST['delivery_address'] = $loc_row["address"];
                $_POST['address_state'] = $loc_row["address_state"];
                $_POST['address_pin'] = $loc_row["address_pin"];
			$Ajax->activate('delivery_address');
    	  	$_SESSION['PO']->Location = $_POST['StkLocation'];
    	  	$_SESSION['PO']->delivery_address = $_POST['delivery_address'];

        }
        else
        { /*The default location of the user is crook */
    	  	display_error(_("The default stock location set up for this user is not a currently defined stock location. Your system administrator needs to amend your user record."));
        }
    }
     if (isset($_POST['address_state']) ||  $_POST['address_state'] != "" )
    {
        $sql = "SELECT state_name,state_code FROM ".TB_PREF."kv_states WHERE state_id=".db_escape($_POST['address_state']);
        $result = db_query($sql,"could not get location info");
        	$state_row = db_fetch($result);
    }
	textarea_row(_("Deliver to:"), 'delivery_address', $_POST['delivery_address'], 35, 4);
       // text_row(_("State:"), 'address_state', $_POST['address_state'], 35, 4);
         $_POST['address_state']= $_POST['address_state'];
        label_row(_("State:"),$state_row['state_name']);
        text_row(_("Pincode:"), 'address_pin', $_POST['address_pin'], 35, 4);
	}

	end_outer_table(); // outer table
}

//---------------------------------------------------------------------------------------------------
//display_error($_SESSION['PO']->trans_type);
function display_po_items(&$order, $editable=true)
{
     
    display_heading(_("Items"));
    div_start('items_table');
   
if(($order->trans_type !=ST_PURCHINDENT) && ($order->trans_type !=ST_INVENTORYISSUE) && ($order->trans_type != ST_PURCHENQUIRY)) {
  start_table(TABLESTYLE, "width='80%'");
  
 	$th = array(_("Item Code"), _("Item Description"),_("HSN"), _("Quantity"),
		_("Received"), _("Unit"),
   		_("Required Delivery Date"), $order->tax_included ? _("Price before Tax") : _("Price before Tax"), _("GST Percantage"),_("GST Amout"), _("CST Percantage"), _("CST Amount"), _("IST Percentage"),_("IST Amount"), _("Line Total"), "");
		}else if($order->trans_type ==ST_INVENTORYISSUE){
		  start_table(TABLESTYLE, "width='40%'");
		 	$th = array(_("Item Code"), _("Item Description"), _("Quantity On Hand"), _("Quantity"), "");
		}
		else{
		  start_table(TABLESTYLE, "width='40%'");
		 	$th = array(_("Item Code"), _("Item Description"), _("Quantity"), "");
		}
	if ($order->trans_type != ST_PURCHORDER)
		array_remove($th, 5);
		
	if (count($order->line_items)) $th[] = '';
   	table_header($th);

	$id = find_submit('Edit');
	$total = 0;
	$k = 0;
	
    if($order->trans_type ==ST_INVENTORYISSUE){
       
	    foreach ($order->line_items as $line_no => $po_line)
	   	{
               // print_r($po_line);
                //die();
               
                //$po_line->gst=11;
		    $val=$po_line->price+$po_line->gst*$po_line->price/100+$po_line->ist*$po_line->price/100+$po_line->cst*$po_line->price/100;
	    	$line_total =	round($po_line->quantity * $val,  user_price_dec());
	    	if (!$editable || ($id != $line_no))
			{
	    		alt_table_row_color($k);
	        	label_cell($po_line->stock_id);
	    		label_cell($po_line->item_description);
                        label_cell($po_line->hsn_no);
	    		label_cell($po_line->qty_val);
	    		qty_cell($po_line->quantity, false, get_qty_dec($po_line->stock_id));
	            
			if ($editable)
			{
				
					edit_button_cell("Edit$line_no", _("Edit"),
					  _('Edit document line'));
					 delete_button_cell("Delete$line_no", _("Delete"),
						_('Remove line from document'));
			}
			end_row();
			}
			else
			{
	                   
				po_item_controls($order, $k, $line_no);
			}
			$total += $line_total;
	    }
	}
        
   else
       {
       if ($_POST['tax_type']) {
	   $taxtype = $_POST['tax_type'];
	}
	foreach ($order->line_items as $line_no => $po_line)
   	{
           // print_r($po_line);
          //  $po_line->cst='9';
      // die();
       if($taxtype==1){
               $val=$po_line->price+$po_line->price*$po_line->gst/100+$po_line->price*$po_line->ist/100+$po_line->price*$po_line->cst/100;
               $gst_val= $po_line->price*$po_line->gst/100;
               $cst_val= $po_line->price*$po_line->cst/100;
               $ist_val= $po_line->price*$po_line->ist/100;
	
       }
       else {
           $val=$po_line->price+$po_line->price*$po_line->gst/100+$po_line->price*$po_line->cst/100+$po_line->price*$po_line->ist/100;
       }
    
    	$line_total =	round($po_line->quantity * $val,  user_price_dec());
		
    	if (!$editable || ($id != $line_no))
		{
            
    		alt_table_row_color($k);
        	label_cell($po_line->stock_id);
    		label_cell($po_line->item_description);
                $getcode = get_tax_slab_code($po_line->stock_id);
               //  print_r($getcode['code']);die();
                 $po_line->hsn_no=$getcode['code'];
             // amount_decimal_cell($po_line->hsn_no);
              $po_line->gst_amt=$gst_val;
              $po_line->cst_amt=$cst_val;
              $po_line->ist_amt=$ist_val;
              label_cells(null, $po_line->hsn_no);
       
            qty_cell($po_line->quantity, false, get_qty_dec($po_line->stock_id));
			if(($order->trans_type != 90) && ($order->trans_type != 100) && ($order->trans_type != 60)){
            qty_cell($po_line->qty_received, false, get_qty_dec($po_line->stock_id));
    		label_cell($po_line->units);
		if ($order->trans_type == ST_PURCHORDER)
        	    label_cell($po_line->req_del_date);
                       // else 
			 // label_cell($po_line->req_del_date);
                        
    		amount_decimal_cell($po_line->price);
                amount_decimal_cell($po_line->gst);
                amount_decimal_cell($po_line->gst_amt);
                amount_decimal_cell($po_line->cst);
                amount_decimal_cell($po_line->cst_amt);
                amount_decimal_cell($po_line->ist);
                amount_decimal_cell($po_line->ist_amt);
            amount_cell($line_total);
           
		}

			if ($editable)
			{
					edit_button_cell("Edit$line_no", _("Edit"),
					  _('Edit document line'));
					delete_button_cell("Delete$line_no", _("Delete"),
						_('Remove line from document'));
			}
			
		end_row();
		}
		else
		{
                   
			po_item_controls($order, $k, $line_no);
		}
		$total += $line_total;
    } 
	}

	if ($id==-1 && $editable)
		if($order->trans_type !=ST_INVENTORYISSUE){
			po_item_controls($order, $k);
		}
		//po_item_controls($order, $k);
if(($order->trans_type != 90) && ($order->trans_type != 100) && ($order->trans_type != 60)){
	$colspan = count($th)-2;
	if (count($order->line_items))
		$colspan--;

	$display_sub_total = price_format($total);

	label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right", 2);

	//  $taxes = $order->get_taxes(input_num('freight_cost'));
	
	$tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2, $order->trans_type==ST_SUPPINVOICE);

	$display_total = price_format(($total + input_num('freight_cost') + $tax_total));

	start_row();
	label_cells(_("Amount Total"), $display_total, "colspan=$colspan align='right'","align='right'");
	
	$order->order_no ? submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true)
		: label_cell('', "colspan=2");
		}
	end_row();

	end_table(1);
	div_end();
}

//---------------------------------------------------------------------------------------------------

function display_po_summary($trans_type,&$po, $is_self=false, $editable=false)
{
     
   start_table(TABLESTYLE, "width='90%'");

    start_row();
	
   	if($trans_type == 60){
		label_cells(_("Reference"), "RFQ-".$po->reference, "class='tableheader2'");
	} else if($trans_type == 70){
	label_row(_(""),$po->filename);
		label_cells(_("Reference"), "PQ-".$po->reference, "class='tableheader2'");
	}
	else if($trans_type == 18){
	label_cells(_("Reference"), "PO-".$po->reference, "class='tableheader2'");
	}
	if($trans_type == 60){
		label_cells(_("Suppliers"), $po->supplier_name, "class='tableheader2'");
	} else {
	 label_cells(_("Supplier"), $po->supplier_name, "class='tableheader2'");
	}

    if (!is_company_currency($po->curr_code))
    	label_cells(_("Order Currency"), $po->curr_code, "class='tableheader2'");

    if (!$is_self)
    {
    	label_cells(_("Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no),
    		"class='tableheader2'");
    }
	end_row();
	start_row();
    label_cells(_("Date"), $po->orig_order_date, "class='tableheader2'");

    if ($editable)
    {
        if (!isset($_POST['Location']))
        	$_POST['Location'] = $po->Location;
        label_cell(_("Deliver Into Location"), "class='tableheader2'");
        locations_list_cells(null, 'Location', $_POST['Location']);
    }
    else
    {
    	label_cells(_("Deliver Into Location"), get_location_name($po->Location),
    		"class='tableheader2'");
    }

    if ($po->supp_ref != "")
    	label_cells(_("Supplier's Reference"), $po->supp_ref, "class='tableheader2'");
    end_row();

    if (!$editable)
    	label_row(_("Delivery Address"), $po->delivery_address, "class='tableheader2'",
    		"colspan=9");

    if ($po->Comments != "")
    	label_row(_("Order Comments"), $po->Comments, "class='tableheader2'",
    		"colspan=9");
    end_table(1);
}
function display_po_summaries(&$po, $is_self=false, $editable=false)
{
	
    start_table(TABLESTYLE, "width='90%'");
	
    start_row();
	$sql = "SELECT purch_order.desig_group,depart.description,empl_info.empl_firstname,purch_order.reference,purch_order.ord_date FROM ".TB_PREF."purch_orders purch_order LEFT JOIN ".TB_PREF."kv_departments AS depart ON depart.id = purch_order.department_id LEFT JOIN ".TB_PREF."kv_empl_info AS empl_info ON empl_info.id = purch_order.employee_id WHERE purch_order.order_no = ".$po->order_no." "; 
	//display_error($sql);
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

   label_cells(_("Designation"), $desig, "class='tableheader2'");

    label_cells(_("Department"), $row[1], "class='tableheader2'");
	
	

    if (!is_company_currency($po->curr_code))
    	label_cells(_("Order Currency"), $po->curr_code, "class='tableheader2'");

    if (!$is_self)
    {
    	label_cells(_("Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no),
    		"class='tableheader2'");
    }
	end_row();
	start_row();
	 label_cells(_("Requested By"), $row[2], "class='tableheader2'");
    label_cells(_("Date"), $po->orig_order_date, "class='tableheader2'");

  /*   if ($editable)
    {
        if (!isset($_POST['Location']))
        	$_POST['Location'] = $po->Location;
        label_cell(_("Deliver Into Location"), "class='tableheader2'");
        locations_list_cells(null, 'Location', $_POST['Location']);
    }
    else
    {
    	label_cells(_("Deliver Into Location"), get_location_name($po->Location),
    		"class='tableheader2'");
    } */

    if ($po->supp_ref != "")
    	label_cells(_("Supplier's Reference"), $po->supp_ref, "class='tableheader2'");
    end_row();
	label_row(_("Reference"),$po->reference, "class='tableheader2'",
    		"colspan=9");
    /* if (!$editable)
    	label_row(_("Delivery Address"), $po->delivery_address, "class='tableheader2'",
    		"colspan=9");

    if ($po->Comments != "")
    	label_row(_("Order Comments"), $po->Comments, "class='tableheader2'",
    		"colspan=9"); */
    end_table(1);
}
//--------------------------------------------------------------------------------

function po_item_controls(&$order, &$rowcounter, $line_no=-1)
{
   global $Ajax, $SysPrefs;

	alt_table_row_color($rowcounter);

	$dec2 = 0;
	$id = find_submit('Edit');
	

 if($order->trans_type ==ST_INVENTORYISSUE){
	if (($id != -1) && $line_no == $id)
	{
            
//		hidden('line_no', $id);

		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, $_POST['stock_id'], $dec);
		//$_POST['price'] = price_format($order->line_items[$id]->price);
		//echo "<pre>";print_r($order->line_items[$id]);
		$_POST['price'] = price_decimal_format($order->line_items[$id]->price, $dec2);
		//if ($order->trans_type == ST_PURCHORDER)
		//	$_POST['req_del_date'] = $order->line_items[$id]->req_del_date;
                    //  else 
                  //  $_POST['req_del_date'] = $order->line_items[$id]->req_del_date;      
		$_POST['units'] = $order->line_items[$id]->units;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
                $_POST['hsn_no'] = $order->line_items[$id]->hsn_no;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
			hidden('item_description', $_POST['item_description']);
//			label_cell($_POST['item_description']);
			label_cell($order->line_items[$id]->item_description); 
		}
		label_cell($order->line_items[$id]->qty_val);
                
               

	    $Ajax->activate('items_table');
		$qty_rcvd = $order->line_items[$id]->qty_received;
	}
	else
	{
            
//		hidden('line_no', ($_SESSION['PO']->lines_on_order + 1));

		//Chaitanya : Manufcatured item can be purchased
		stock_items_list_cells(null, 'stock_id', null, false, true, false, true);
		//stock_purchasable_items_list_cells(null, 'stock_id', null, false, true, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('price');
                            $Ajax->activate('gst');
                            $Ajax->activate('cst');
                            $Ajax->activate('ist');
                            $Ajax->activate('gst_amt');
                            $Ajax->activate('cst_amt');
                            $Ajax->activate('ist_amt');
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('req_del_date');
                            $Ajax->activate('hsn_no');
			    $Ajax->activate('line_total');
		}
    	$item_info = get_item_edit_info($_POST['stock_id']);
		$_POST['units'] = $item_info["units"];

   		$dec = $item_info["decimals"];
   		$_POST['qty'] =	number_format2(get_purchase_conversion_factor ($order->supplier_id, $_POST['stock_id']), $dec);
                
		//$_POST['price'] = price_format(get_purchase_price ($order->supplier_id, $_POST['stock_id']));
		$_POST['price'] = price_decimal_format(get_purchase_price ($order->supplier_id, $_POST['stock_id']), $dec2);
               
                
		if ($order->trans_type == ST_PURCHORDER)
			$_POST['req_del_date'] = add_days(Today(), $SysPrefs->default_delivery_required_by());
               // else 
                //    $_POST['req_del_date'] = add_days(Today(), $SysPrefs->default_delivery_required_by());
		$qty_rcvd = '';
	}

	qty_cells(null, 'qty', null, null, null, $dec);
	if(($order->trans_type!=90) && ($order->trans_type != 100) && ($order->trans_type != 60)){
	qty_cell($qty_rcvd, false, $dec);

	label_cell($_POST['units'], '', 'units');
	if ($order->trans_type == ST_PURCHORDER)
		date_cells(null, 'req_del_date', '', null, 0, 0, 0);
	//else 
          //  date_cells(null, 'req_del_date', '', null, 0, 0, 0);
	if ($qty_rcvd > 0)
	{
                
   		amount_decimal_cell($_POST['price']);
   		hidden('price', $_POST['price']);
   	}	
   	else
            
            amount_cells(null, 'price', null, null, null, $dec2);
            amount_cells(null, 'gst', null, null, null, $dec2,true);
            amount_cells(null, 'gst_amt', null, null, null, $dec2,true);
            amount_cells(null, 'cst', null, null, null, $dec2,true);
            amount_cells(null, 'cst_amt', null, null, null, $dec2,true);
            amount_cells(null, 'ist', null, null, null, $dec2,true);
            amount_cells(null, 'ist_amt', null, null, null, $dec2,true);
       
        $val=input_num('price')+input_num('gst')*input_num('price')/100+input_num('cst')*input_num('price')/100+input_num('ist')*input_num('price')/100;
       //  $val=input_num('price')+input_num('gst')+input_num('cst')+input_num('ist');
       
	//$line_total = $_POST['qty'] * $_POST['price'] * (1 - $_POST['Disc'] / 100);
	$line_total = round(input_num('qty')* $val,  user_price_dec());
	amount_cell($line_total, false, '','line_total');
        
        
        
	}

	if ($id!=-1)
	{
		
		button_cell('UpdateLine', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelUpdate', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('line_no', $line_no);
		set_focus('qty');
	}
	else
	{
		submit_cells('EnterLine', _("Add Item"), "colspan=2 align='center'",
		    _('Add new item to document'), true);
	}

	end_row();
}else{
	
	if (($id != -1) && $line_no == $id)
	{
             $company_setup= &$_SESSION['SysPrefs'];
$company_state =$company_setup->prefs['state'];   
//display_error($order->line_items);
           // die();
//		hidden('line_no', $id);

		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, $_POST['stock_id'], $dec);
                
		//$_POST['price'] = price_format($order->line_items[$id]->price);
		$_POST['price'] = price_decimal_format($order->line_items[$id]->price, $dec2);
		if ($order->trans_type == ST_PURCHORDER)
			$_POST['req_del_date'] = $order->line_items[$id]->req_del_date;
                 //else
                 // $_POST['req_del_date'] = $order->line_items[$id]->req_del_date;   
		$_POST['units'] = $order->line_items[$id]->units;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
               
       
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);

		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
			hidden('item_description', $_POST['item_description']);
//			label_cell($_POST['item_description']);
			label_cell($order->line_items[$id]->item_description); 
		}
                $code_info = get_tax_code($subcat);
       // print_r($code_info['code']);die();
                 label_cell($order->line_items[$id]->hsn_no);
                $_POST['hsn_no']=$order->line_items[$id]->hsn_no;
       
       
                
              
               
                if($_POST['address_state']==$company_state){
                 $_POST['gst'] = price_decimal_format($order->line_items[$id]->gst, $_POST['stock_id'], $dec);
                  $_POST['gst_amt'] = price_decimal_format($order->line_items[$id]->gst_amt, $_POST['stock_id'], $dec);
                  $_POST['cst'] = price_decimal_format($order->line_items[$id]->cst, $_POST['stock_id'], $dec);   
                  $_POST['cst_amt'] = price_decimal_format($order->line_items[$id]->cst_amt, $_POST['stock_id'], $dec);
                 //$_POST['gst']= $taxapply/2;
                 //$_POST['cst']= $taxapply/2;
                }
                else{
                     //$_POST['gst']= '';
                    // $_POST['cst']= '';
                       $_POST['ist'] = price_decimal_format($order->line_items[$id]->ist_amt, $_POST['stock_id'], $dec);
                }

	    $Ajax->activate('items_table');
		$qty_rcvd = $order->line_items[$id]->qty_received;
	}
	else
	{
         $company_setup= &$_SESSION['SysPrefs'];
$company_state =$company_setup->prefs['state'];   

//display_error($company_state);
//		hidden('line_no', ($_SESSION['PO']->lines_on_order + 1));

		//Chaitanya : Manufcatured item can be purchased
		stock_items_list_cells(null, 'stock_id', null, false, true, false, true);
		//stock_purchasable_items_list_cells(null, 'stock_id', null, false, true, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('price');
                            $Ajax->activate('gst');
                            $Ajax->activate('cst');
                            $Ajax->activate('ist');
                             $Ajax->activate('gst_amt');
                            $Ajax->activate('cst_amt');
                            $Ajax->activate('ist_amt');
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('req_del_date');
			    $Ajax->activate('line_total');
                            $Ajax->activate('hsn_no');
		}
    	$item_info = get_item_edit_info($_POST['stock_id']);
		$_POST['units'] = $item_info["units"];
                 $subcat= $item_info["sub_cat_name"];
               
                 $tax_info = get_tax_slab_subcat($subcat);
                 $taxapply=  $tax_info['tax_rate'];
                // $_POST['ist']= $taxapply;
                 //display_error($_POST['address_state']);
                // display_error($company_state);
                if($_POST['address_state']==$company_state){
                 $_POST['gst']= $taxapply/2;
                 $_POST['cst']= $taxapply/2;
                }
                else{
                     //$_POST['gst']= '';
                    // $_POST['cst']= '';
                     $_POST['ist']= $taxapply;
                }
                // display_error(get_company_state());
   		$dec = $item_info["decimals"];
   		$_POST['qty'] =	number_format2(get_purchase_conversion_factor ($order->supplier_id, $_POST['stock_id']), $dec);
                
		//$_POST['price'] = price_format(get_purchase_price ($order->supplier_id, $_POST['stock_id']));
		$_POST['price'] = price_decimal_format(get_purchase_price ($order->supplier_id, $_POST['stock_id']), $dec2);
               
                
		if ($order->trans_type == ST_PURCHORDER)
			$_POST['req_del_date'] = add_days(Today(), $SysPrefs->default_delivery_required_by());
              //  else
                ///  $_POST['req_del_date'] = add_days(Today(), $SysPrefs->default_delivery_required_by());  
		$qty_rcvd = '';
	}
        $code_info = get_tax_code($subcat);
       // print_r($code_info['code']);die();
      // display_error($id);
        $_POST['hsn_no']=$code_info['code'];
        if (($id == -1) && $line_no == -1)
        text_cells(null, 'hsn_no', $_POST['hsn_no'], null, null,false,false,false,false,false,true);
        qty_cells(null, 'qty', null, null, null, $dec);
	if(($order->trans_type!=90) && ($order->trans_type != 100) && ($order->trans_type != 60)){
	qty_cell($qty_rcvd, false, $dec);

	label_cell($_POST['units'], '', 'units');
	if ($order->trans_type == ST_PURCHORDER)
		date_cells(null, 'req_del_date', '', null, 0, 0, 0);
	//else
          //  date_cells(null, 'req_del_date', '', null, 0, 0, 0);
	if ($qty_rcvd > 0)
	{
                
   		amount_decimal_cell($_POST['price']);
   		hidden('price', $_POST['price']);
   	}	
   	else
            
            amount_cells(null, 'price', $_POST['price'], null, null, $dec2);
            amount_cells(null, 'gst', $_POST['gst'], null, null, $dec2,true);
            amount_cells(null, 'gst_amt', $_POST['gst_amt'], null, null, $dec2,true);
            amount_cells(null, 'cst', $_POST['cst'], null, null, $dec2,true);
            amount_cells(null, 'cst_amt', $_POST['cst_amt'], null, null, $dec2,true);
            amount_cells(null, 'ist', $_POST['ist'], null, null, $dec2,true);
            amount_cells(null, 'ist_amt', $_POST['ist_amt'], null, null, $dec2,true);
       
        $val=input_num('price')+input_num('gst')*input_num('price')/100+input_num('cst')*input_num('price')/100+input_num('ist')*input_num('price')/100;
       
	//$line_total = $_POST['qty'] * $_POST['price'] * (1 - $_POST['Disc'] / 100);
	$line_total = round(input_num('qty')* $val,  user_price_dec());
	amount_cell($line_total, false, '','line_total');
        
        
        
	}

	if ($id!=-1)
	{
		button_cell('UpdateLine', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelUpdate', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('line_no', $line_no);
		set_focus('qty');
	}
	else
	{
		submit_cells('EnterLine', _("Add Item"), "colspan=2 align='center'",
		    _('Add new item to document'), true);
	}

	end_row();
}
}


//---------------------------------------------------------------------------------------------------



?>