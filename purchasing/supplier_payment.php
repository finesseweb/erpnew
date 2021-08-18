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
$page_security = 'SA_SUPPLIERPAYMNT';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/allocation_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
//if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
//if ($use_date_picker)
	$js .= get_js_date_picker();

add_js_file('payalloc.js');

page(_($help_context = "Supplier Payment Entry"), false, false, "", $js);

if (isset($_GET['supplier_id']))
{
	$_POST['supplier_id'] = $_GET['supplier_id'];
}

//----------------------------------------------------------------------------------------

check_db_has_suppliers(_("There are no suppliers defined in the system."));

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------

if (!isset($_POST['supplier_id']))
	$_POST['supplier_id'] = get_global_supplier(false);

if (!isset($_POST['DatePaid']))
{
	$_POST['DatePaid'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['DatePaid']))
		$_POST['DatePaid'] = end_fiscalyear();
}

if (isset($_POST['_DatePaid_changed'])) {
  $Ajax->activate('_ex_rate');
}

if (list_updated('supplier_id')) {
	$_POST['amount'] = price_format(0);
	$_SESSION['alloc']->person_id = get_post('supplier_id');
	$Ajax->activate('amount');
	$Ajax->activate('final_amt1');

} elseif (list_updated('bank_account'))
	$Ajax->activate('alloc_tbl');

//----------------------------------------------------------------------------------------

if (!isset($_POST['bank_account'])) { // first page call
	$_SESSION['alloc'] = new allocation(ST_SUPPAYMENT, 0, get_post('supplier_id'));

	if (isset($_GET['PInvoice'])) {
		//  get date and supplier
		$inv = get_supp_trans($_GET['PInvoice'], ST_SUPPINVOICE);
		$dflt_act = get_default_bank_account($inv['curr_code']);
		$_POST['bank_account'] = $dflt_act['id'];
		if($inv) {
			$_SESSION['alloc']->person_id = $_POST['supplier_id'] = $inv['supplier_id'];
			$_SESSION['alloc']->read();
			$_POST['DatePaid'] = sql2date($inv['tran_date']);
			$_POST['memo_'] = $inv['supp_reference'];
			foreach($_SESSION['alloc']->allocs as $line => $trans) {
				if ($trans->type == ST_SUPPINVOICE && $trans->type_no == $_GET['PInvoice']) {
					$un_allocated = abs($trans->amount) - $trans->amount_allocated;
					$_SESSION['alloc']->amount = $_SESSION['alloc']->allocs[$line]->current_allocated = $un_allocated;
					$_POST['amount'] = $_POST['amount'.$line] = price_format($un_allocated);
					display_error($_SESSION['alloc']->amount);
					break;
				}
			}
			unset($inv);
		} else
			display_error(_("Invalid purchase invoice number."));
	}
}
if (isset($_GET['AddedID'])) {


	$payment_id = $_GET['AddedID'];

   	display_notification_centered( _("Payment has been sucessfully entered"));

	submenu_print(_("&Print This Remittance"), ST_SUPPAYMENT, $payment_id."-".ST_SUPPAYMENT, 'prtopt');
	submenu_print(_("&Email This Remittance"), ST_SUPPAYMENT, $payment_id."-".ST_SUPPAYMENT, null, 1);

	submenu_view(_("View this Payment"), ST_SUPPAYMENT, $payment_id);
    display_note(get_gl_view_str(ST_SUPPAYMENT, $payment_id, _("View the GL &Journal Entries for this Payment")), 0, 1);

	submenu_option(_("Enter another supplier &payment"), "/purchasing/supplier_payment.php?supplier_id=".$_POST['supplier_id']);
	submenu_option(_("Enter Other &Payment"), "/gl/gl_bank.php?NewPayment=Yes");
	submenu_option(_("Enter &Customer Payment"), "/sales/customer_payments.php");
	submenu_option(_("Enter Other &Deposit"), "/gl/gl_bank.php?NewDeposit=Yes");
	submenu_option(_("Bank Account &Transfer"), "/gl/bank_transfer.php");

	display_footer_exit();
}

//----------------------------------------------------------------------------------------

function check_inputs()
{
	global $Refs;

	if (!get_post('supplier_id')) 
	{
		display_error(_("There is no supplier selected."));
		set_focus('supplier_id');
		return false;
	} 
	
	if (@$_POST['amount'] == "") 
	{
		$_POST['amount'] = price_format(0);
	}

	if (!check_num('amount', 0))
	{
		display_error(_("The entered amount is invalid or less than zero."));
		set_focus('amount');
		return false;
	}

	if (isset($_POST['charge']) && !check_num('charge', 0)) {
		display_error(_("The entered amount is invalid or less than zero."));
		set_focus('charge');
		return false;
	}

	if (isset($_POST['charge']) && input_num('charge') > 0) {
		$charge_acct = get_company_pref('bank_charge_act');
		if (get_gl_account($charge_acct) == false) {
			display_error(_("The Bank Charge Account has not been set in System and General GL Setup."));
			set_focus('charge');
			return false;
		}	
	}

	if (@$_POST['discount'] == "") 
	{
		$_POST['discount'] = 0;
	}

	if (!check_num('discount', 0))
	{
		display_error(_("The entered discount is invalid or less than zero."));
		set_focus('amount');
		return false;
	}

	//if (input_num('amount') - input_num('discount') <= 0) 
	if (input_num('amount') <= 0) 
	{
		display_error(_("The total of the amount and the discount is zero or negative. Please enter positive values."));
		set_focus('amount');
		return false;
	}

	if (isset($_POST['bank_amount']) && input_num('bank_amount')<=0)
	{
		display_error(_("The entered bank amount is zero or negative."));
		set_focus('bank_amount');
		return false;
	}


   	if (!is_date($_POST['DatePaid']))
   	{
		display_error(_("The entered date is invalid."));
		set_focus('DatePaid');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_POST['DatePaid'])) 
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('DatePaid');
		return false;
	}

	$limit = get_bank_account_limit($_POST['bank_account'], $_POST['DatePaid']);

	if (($limit !== null) && (floatcmp($limit, input_num('amount')) < 0))
	{
		display_error(sprintf(_("The total bank amount exceeds allowed limit (%s)."), price_format($limit)));
		set_focus('amount');
		return false;
	}

    if (!$Refs->is_valid($_POST['ref'])) 
    {
		display_error(_("You must enter a reference."));
		set_focus('ref');
		return false;
	}

	if (!is_new_reference($_POST['ref'], ST_SUPPAYMENT)) 
	{
		display_error(_("The entered reference is already in use."));
		set_focus('ref');
		return false;
	}

	if (!db_has_currency_rates(get_supplier_currency($_POST['supplier_id']), $_POST['DatePaid'], true))
		return false;

	$_SESSION['alloc']->amount = -input_num('amount');

	if (isset($_POST["TotalNumberOfAllocs"]))
		return check_allocations();
	else
		return true;
}

//----------------------------------------------------------------------------------------

function handle_add_payment()
{
	
	$payment_id = write_supp_payment(0, $_POST['supplier_id'], $_POST['bank_account'],
		$_POST['DatePaid'], $_POST['ref'], input_num('amount'),	input_num('discount'), $_POST['memo_'], 
		input_num('charge'), input_num('bank_amount', input_num('amount')),$_POST['tds']);
	new_doc_date($_POST['DatePaid']);

	$_SESSION['alloc']->trans_no = $payment_id;
	$_SESSION['alloc']->date_ = $_POST['DatePaid'];
	$_SESSION['alloc']->write();

   	unset($_POST['bank_account']);
   	unset($_POST['DatePaid']);
   	unset($_POST['currency']);
   	unset($_POST['memo_']);
   	unset($_POST['amount']);
   	unset($_POST['discount']);
   	unset($_POST['ProcessSuppPayment']);

	meta_forward($_SERVER['PHP_SELF'], "AddedID=$payment_id&supplier_id=".$_POST['supplier_id']);
}

//----------------------------------------------------------------------------------------

if (isset($_POST['ProcessSuppPayment']))
{
	 /*First off  check for valid inputs */
    if (check_inputs() == true) 
    {
    	handle_add_payment();
    	end_page();
     	exit;
    }
}

if (isset($_POST['TDS']))
{

	$val=$_POST['fascial_year'];
	$sql12 = "SELECT * FROM fa_fiscal_year WHERE id=". "'".$val."'";
    $user_data1 = db_query($sql12);
    $user_result1 = db_fetch_row($user_data1);

	 $sql = "SELECT * FROM ".TB_PREF."vendor_details vendor
			 LEFT JOIN ".TB_PREF."suppliers supp ON supp.vendor_type = vendor.cat_id
		WHERE supp.supplier_id = ".db_escape($_POST['supplier_id'])."
		GROUP BY
			  supp.supp_name";
    $user_data12 = db_query($sql);
    $user_result12 = db_fetch($user_data12);
   
    $sql123 ="SELECT SUM(amount) as amt FROM fa_gl_trans WHERE tran_date BETWEEN ". "'".$user_result1[1]."' AND ". "'".$user_result1[2]."' AND person_id= ". "'".$_POST['supplier_id']."' AND type=22 AND account!=1060";
   
    $user_data1234 = db_query($sql123);
    $user_result1267 = db_fetch($user_data1234);

   
    if($user_result1267['amt'] !=''){
    	$amountValue=$user_result12['cumulative_payment'];
    }else{
    	 $amountValue=$user_result12['single_payment'];
    }
   


	$tds_amount=$amountValue;
	$bill_amount=input_num('amount');
	if($bill_amount >= $tds_amount ){
		$tds_cal=$bill_amount/100*$user_result12['percentage'];
	}
	$_POST['amount']=$bill_amount-$tds_cal;
	$Ajax->activate('alloc_tbl2');
	// display_error($_POST['amount']);
	// display_error($bill_amount);
	// display_error($tds_cal);
	
}
//----------------------------------------------------------------------------------------

start_form();

	start_outer_table(TABLESTYLE2, "width='89%'", 5);

	table_section(1);

    supplier_list_row(_("Payment To:"), 'supplier_id', null, false, true);

	if (list_updated('supplier_id') || list_updated('bank_account')) {
	  $_SESSION['alloc']->read();
	  $_POST['memo_'] = $_POST['amount'] = '';
	  $Ajax->activate('alloc_tbl');
	}

	set_global_supplier($_POST['supplier_id']);

	if (!list_updated('bank_account') && !get_post('__ex_rate_changed'))
		$_POST['bank_account'] = get_default_supplier_bank_account($_POST['supplier_id']);
	else
		$_POST['amount'] = price_format(0);

    bank_accounts_list_row(_("From Bank Account:"), 'bank_account', null, true);

	bank_balance_row($_POST['bank_account']);

	table_section(2);

    date_row(_("Date Paid") . ":", 'DatePaid', '', true, 0, 0, 0, null, true);

//	display_error($Refs->get_next(ST_SUPPAYMENT));
   // ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_SUPPAYMENT));
	hidden('ref', $Refs->get_next(ST_SUPPAYMENT));
	label_row(_("Reference:"),"SP- ".$Refs->get_next(ST_SUPPAYMENT));

	table_section(3);

	$comp_currency = get_company_currency();
	$supplier_currency = $_SESSION['alloc']->set_person($_POST['supplier_id'], PT_SUPPLIER);
	if (!$supplier_currency)
		display_error('Test');
			$supplier_currency = $comp_currency;
	$_SESSION['alloc']->currency = $bank_currency = get_bank_account_currency($_POST['bank_account']);

	if ($bank_currency != $supplier_currency) 
	{
		amount_row(_("Bank Amount:"), 'bank_amount', null, '', $bank_currency, 2);
	}

	amount_row(_("Bank Charge:"), 'charge', null, '', $bank_currency);

	kv_fiscalyears_list_cells(_("Fiscal Year:"), 'fascial_year', null, true);

	echo amount($_POST['fascial_year']);


	end_outer_table(1);

	div_start('alloc_tbl');
	show_allocatable(false);
	div_end();

	div_start('alloc_tbl2');
	start_table(TABLESTYLE, "width='60%'");


	amount_row(_("TDS Amount:"), 'tds',$tds_cal, '', $supplier_currency);
	amount_row(_("Amount of Discount:"), 'discount', null, '', $supplier_currency);
	amount_row(_("Amount of Payment:"), 'amount', $_POST['amount'], '', $supplier_currency);
	textarea_row(_("Memo:"), 'memo_', null, 22, 4);
	submit_center('TDS',_("GET TDS Calculation"), true, '', 'default');
	end_table(1);
	div_end();
	submit_center('ProcessSuppPayment',_("Enter Payment"), true, '', 'default');

end_form();


function kv_fiscalyears_list($name, $selected_id=null, $submit_on_change=false,$all_oprtion = false){

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year";

// default to the company current fiscal year

	return combo_input($name, $selected_id, $sql, 'id', '',
	array(
		'order' => 'begin',
                 'spec_option' => $all_oprtion,
		'default' => get_company_pref('f_year'),
		'format' => '_format_fiscalyears',
		'select_submit'=> $submit_on_change,
		'async' => true
	) );
}
     

function kv_fiscalyears_list_cells($label, $name, $selected_id=null, $submit_on_change=false,$all_option = false){
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td style='text:center;'>";
	echo kv_fiscalyears_list($name, $selected_id, $submit_on_change,$all_option);
	echo "</td>\n";
}

function kv_fiscalyears_list_row($label, $name, $selected_id=null, $submit_on_change=true){
	echo "<tr><td class='label'>$label</td>";
	kv_fiscalyears_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}



function amount($val){


	$val=$_POST['fascial_year'];
	$sql12 = "SELECT * FROM fa_fiscal_year WHERE id=". "'".$val."'";
    $user_data1 = db_query($sql12);
    $user_result1 = db_fetch_row($user_data1);

    $sql123 ="SELECT SUM(amount) as amt FROM fa_gl_trans WHERE tran_date BETWEEN ". "'".$user_result1[1]."' AND ". "'".$user_result1[2]."' AND person_id= ". "'".$_POST['supplier_id']."' AND type=22 AND account!=1060";
    $user_data12 = db_query($sql123);
    $user_result12 = db_fetch($user_data12);

    if($user_result12['amt'] !=''){
    	$vals='<tr><td class="label" style="text-align:left">Total paid Amount:</td>
<td>'.$user_result12['amt'].'</td>
</tr><input type="hidden" name="final_amt1" id="final_amt1" value="'.$user_result12['amt'].'">';
    }else {
    	$vals='<tr><td class="label" style="text-align:left">Total paid Amount:</td>
<td>0</td>
</tr>';
    }

    
    return $vals;

}


// function payment(){

//   $sql = "SELECT * FROM ".TB_PREF."vendor_details vendor
// 			 LEFT JOIN ".TB_PREF."suppliers supp ON supp.vendor_type = vendor.cat_id
// 		WHERE supp.supplier_id = ".db_escape($_POST['supplier_id'])."
// 		GROUP BY
// 			  supp.supp_name";
//     $user_data12 = db_query($sql);
//     $user_result12 = db_fetch($user_data12);

//     $sql123 ="SELECT SUM(amount) as amt FROM fa_gl_trans WHERE tran_date BETWEEN ". "'".$user_result1[1]."' AND ". "'".$user_result1[2]."' AND person_id= ". "'".$_POST['supplier_id']."' AND type=22 AND account=5111";
//     $user_data1234 = db_query($sql123);
//     $user_result1267 = db_fetch($user_data1234);

   
//     if($user_result1267['amt'] !=''){
//     	$amountValue=$user_result12['cumulative_payment'];
//     }else{
//     	 $amountValue=$user_result12['single_payment'];
//     }
   
//     return $amountValue;

// }

// function newpayment(){

// 	$val=payment();
// 	return $val;

// }


end_page();
?>
