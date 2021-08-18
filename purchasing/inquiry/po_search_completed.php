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
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
//add_access_extensions();
include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

if (isset($_GET['type']))
{
  if($_GET['type']==18)
  $trans_type= ST_PURCHORDER;
  if($_GET['type']==60)
  $trans_type= ST_PURCHENQUIRY;
  if($_GET['type']==70)
  $trans_type= ST_PURCHQUOTE;
  if($_GET['type']==90)
  $trans_type= ST_PURCHINDENT;
$_SESSION["tran_type"] =$trans_type;
}

if (!@$_GET['popup'])
{
	$js = "";
	global $trans_type;
	
	
	//if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	//if ($use_date_picker)
		$js .= get_js_date_picker();
                //display_error($trans_type);
	if($trans_type == 90){
		page(_($help_context = "Search Purchase Indent"), false, false, "", $js);
	}else if($trans_type == 60){
		page(_($help_context = "Search RFQ"), false, false, "", $js);
	}else if($trans_type == 70){
		page(_($help_context = "Search Purchase Quotation"), false, false, "", $js);
	}else 
	page(_($help_context = "Search Purchase Orders"), false, false, "", $js);
}
if (isset($_GET['order_number']))
{
	$order_number = $_GET['order_number'];
}


//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders')) 
{
	
	$Ajax->activate('orders_tbl');
	
} elseif(get_post('calculateprice')){ 

	$calculateprice = $_POST['calculateprice'];
	$Ajax->activate('orders_tbl');
	
}
elseif(get_post('loadtime')){
	$loadtime = $_POST['loadtime'];
	$Ajax->activate('orders_tbl');
	
}elseif (get_post('_order_number_changed')) 
{
	$disable = get_post('order_number') !== '';

	$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
	$Ajax->addDisable(true, 'OrdersToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	$Ajax->addDisable(true, 'SelectStockFromList', $disable);

	if ($disable) {
		$Ajax->addFocus(true, 'order_number');
	} else
		$Ajax->addFocus(true, 'OrdersAfterDate');

	$Ajax->activate('orders_tbl');
}
else if(get_post('_enq_ref_changed')){
	$disable = get_post('enq_ref') !== '';
	
	$Ajax->addDisable(true, 'order_number', $disable);
	$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
	$Ajax->addDisable(true, 'OrdersToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	//$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	//$Ajax->addDisable(true, 'SelectStockFromList', $disable);
	$Ajax->addDisable(true, 'supplier_id', $disable);
}
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
	global $trans_type;
	start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("#:"), 'order_number', '',null, '', true);
if($_SESSION["tran_type"] == 70){
ref_cells(_("Enquiry Reference:"), 'enq_ref', '',null, '', true);
}

date_cells(_("from:"), 'OrdersAfterDate', '', null, -30);
date_cells(_("to:"), 'OrdersToDate');

locations_list_cells(_("into location:"), 'StockLocation', null, true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(_("for item:"), 'SelectStockFromList', null, true);
if($trans_type != 90){
if (!@$_GET['popup'])
	supplier_list_cells(_("Select a supplier: "), 'supplier_id', null, true, true);
}

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'ICON_SUBMIT');
end_row();
end_table(1);
if($_SESSION["tran_type"] == 70){
start_table(TABLESTYLE_NOBORDER);
start_row();
submit_cells('calculateprice', _("Price"),"style='padding-left:780px;'",_('Select price'), 'ICON_SUBMIT');
submit_cells('loadtime', _("Load Time"),'',_('Select load'), 'ICON_SUBMIT');
end_row();
end_table(0);
}
//---------------------------------------------------------------------------------------------
if (isset($_POST['order_number']))
{
	$order_number = $_POST['order_number'];
}
if(isset($_POST['enq_ref'])){
	
	$enquiry_ref = $_POST['enq_ref'];
}
if (isset($_POST['SelectStockFromList']) &&	($_POST['SelectStockFromList'] != "") &&
	($_POST['SelectStockFromList'] != ALL_TEXT))
{
 	$selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
	unset($selected_stock_item);
}

//---------------------------------------------------------------------------------------------
function trans_view($trans)
{
	global $trans_type;
	
	return get_trans_view_str($_SESSION["tran_type"], $trans["order_no"]);
}


function check_overdue($row)
{
	global $trans_type;
	
	if ($_SESSION["tran_type"] == ST_PURCHQUOTE){
	
		return (date1_greater_date2(Today(), sql2date($row['ord_date'])));
		
	

	}
	
	else if ($trans_type == 60)
		return (date1_greater_date2(Today(), sql2date($row['delivery_date'])));
	
}

function edit_link($row) 
{
	if (@$_GET['popup'])
		return '';
	global $trans_type;

	//$modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
	if($_SESSION["tran_type"] == ST_PURCHORDER)
	 $modify="ModifyOrderNumber";
	 else if($_SESSION["tran_type"] == ST_PURCHQUOTE)
	 $modify="ModifyQuotationNumber";
	else if($_SESSION["tran_type"] == ST_PURCHINDENT)
		$modify="ModifyIndent";
	else
	 $modify="ModifyEnquiryNumber";	 
  	return pager_link( _("Edit"),
		"/purchasing/po_entry_items.php?$modify=" . $row["order_no"], ICON_EDIT);
}
function prt_link($row)
{
	global $trans_type;
	return print_document_link($row['order_no'], _("Print"), true, $_SESSION["tran_type"], ICON_PRINT);
}

function order_link($row)
{
  return pager_link( _("Purchase Order"),
	"/purchasing/po_entry_items.php?NewQuoteToSalesOrder=" .$row['order_no'], ICON_DOC);
}
function quotation_link($row)
{
  return pager_link( _("Purchase Quotation"),
	"/purchasing/po_entry_items.php?NewEnquiryToQuoteOrder=" .$row['order_no'], ICON_DOC);
}
function enquiry_link($row)
{
  return pager_link( _("Purchase Enquiry"),
	"/purchasing/po_entry_items.php?NewIndentToEnquiryOrder=" .$row['order_no'], ICON_DOC);
}

function indent_date($row){
	return sql2date($row['ord_date']);

}

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_po_search_completed(!@$_GET['popup'] ? $_POST['supplier_id'] : ALL_TEXT,$_SESSION["tran_type"]);

if($_SESSION["tran_type"]==90){
$sql = get_sql_for_po_search_typecompleted(!@$_GET['popup'] ? $_POST['supplier_id'] : ALL_TEXT,90);
}
if($_SESSION["tran_type"] == 60){
	$sql = get_sql_for_rfq_search_completed(!@$_GET['popup'] ? $_POST['supplier_id'] : ALL_TEXT,60);
}
if($_SESSION["tran_type"]==18)
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''), 
		_("Reference"), 
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Supplier's Reference"), 
		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Currency") => array('align'=>'center'), 
		_("Order Total") => 'amount',
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link'),
);
else if($_SESSION["tran_type"]==70)
	
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''), 
		_("Reference"), 
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Enquiry Reference"), 
		_("Quotation Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Currency") => array('align'=>'center'), 
		_("Quotation Total") => 'amount',
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'order_link'),
		array('insert'=>true, 'fun'=>'prt_link'),
);

else if($_SESSION["tran_type"]==60)
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''), 
	    _("Reference"), 
		_("Suppliers") => array('ord'=>''),
		_("Location"),
		_("Supplier's Reference"), 
		_("Enquiry Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		//_("Currency") => array('align'=>'center'), 
	//	_("Enquiry Total") => 'amount',
		//array('insert'=>true, 'fun'=>'edit_link'),
		//array('insert'=>true, 'fun'=>'quotation_link'),
		//array('insert'=>true, 'fun'=>'prt_link'),
);
else if($_SESSION["tran_type"]==90)
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''), 
		_("Reference"), 
		_("Designation Group"),
		_("Designation"), 
		_("Department"),
	  _("Indent Date") => array('fun'=>'indent_date'),
	//	_("Indent ") => 'OrderValue',
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'enquiry_link'),
		array('insert'=>true, 'fun'=>'prt_link'),
);

if (get_post('StockLocation') != $all_items) {
	$cols[_("Location")] = 'skip';
}
//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);
//echo "<prE>";print_r($table);

//submit_cells('price', _("Price"),'',_('Select price'), 'ICON_SUBMIT');
//submit_cells('loadtime', _("Load Time"),'',_('Select load'), 'ICON_SUBMIT');

$table->width = "80%";

$table->set_marker('check_overdue', _("Marked items are overdue."));

display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page();
}	
?>
