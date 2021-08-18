<?php
/**********************************************************************
  
	Released underhe GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
//$page_security = 'SA_SUPPLIER';
$page_security = 'SA_MAINTENANCE';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();
$js = "";
//if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
//if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Contractor"), @$_REQUEST['popup'], false, "", $js);

include($path_to_root . "/maintenance/includes/db/contractor_db.inc");

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

check_db_has_tax_groups(_("There are no tax groups defined in the system. At least one tax group is required before proceeding."));

if (isset($_GET['supplier_id'])) 
{
	$_POST['supplier_id'] = $_GET['supplier_id'];
}

$supplier_id = get_post('supplier_id'); 

//--------------------------------------------------------------------------------------------
function contractor_settings(&$supplier_id)
{

	start_outer_table(TABLESTYLE2);

	table_section(1);

	if ($supplier_id) 
	{
		//SupplierID exists - either passed when calling the form or from the form itself
		$myrow = get_contractor($_POST['supplier_id']);

		$_POST['supp_name']  = $myrow["supp_name"];
		$_POST['supp_ref']  = $myrow["supp_ref"];
		$_POST['gst_no']  = $myrow["gst_no"];
		$_POST['website']  = $myrow["website"];
		$_POST['tax_group_id']  = $myrow["tax_group_id"];
		$_POST['bank_account']  = $myrow["bank_account"];
		$_POST['payment_terms']  = $myrow["payment_terms"];
		$_POST['tax_included']  = $myrow["tax_included"];
		$_POST['payable_account']  = $myrow["payable_account"];
		$_POST['purchase_account']  = $myrow["purchase_account"];
		$_POST['payment_discount_account']  = $myrow["payment_discount_account"];
		$_POST['phone']  = $myrow["phone"];
		$_POST['phone2']  = $myrow["phone2"];
		$_POST['address']  = $myrow["address"];
		$_POST['supp_address']  = $myrow["supp_address"];
		$_POST['notes']  = $myrow["notes"];
		$_POST['contact']  = $myrow["contact"];
		$_POST['fax']  = $myrow["fax"];
		$_POST['email']  = $myrow["email"];
	//	$_POST['rep_lang']  = $myrow["rep_lang"];
	 	$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
	/*	$_POST['supp_name'] = $_POST['supp_ref'] = $_POST['address'] = $_POST['supp_address'] = 
			$_POST['tax_group_id'] = $_POST['website'] = $_POST['supp_account_no'] = $_POST['notes'] = '';
	//	$_POST['dimension_id'] = 0;
	//	$_POST['dimension2_id'] = 0;
		$_POST['tax_included'] = 0;
		$_POST['sales_type'] = -1;
		$_POST['gst_no'] = $_POST['bank_account'] = '';
		$_POST['payment_terms']  = '';
//		$_POST['credit_limit'] = price_format(0);

		$company_record = get_company_prefs();
		$_POST['curr_code']  = $company_record["curr_default"];
		$_POST['payable_account'] = $company_record["creditors_act"];
		$_POST['purchase_account'] = ''; // default/item's cogs account
		$_POST['payment_discount_account'] = $company_record['pyt_discount_act']; */
		
		
		$_POST['supp_name']  = $myrow["supp_name"]=$_POST['supp_ref']  = $myrow["supp_ref"]=$_POST['gst_no']  = $myrow["gst_no"]=$_POST['website']  = $myrow["website"]=$_POST['tax_group_id']  = $myrow["tax_group_id"]=$_POST['bank_account']  = $myrow["bank_account"]=$_POST['payment_terms']  = $myrow["payment_terms"]=$_POST['tax_included']  = $myrow["tax_included"]=$_POST['payable_account']  = $myrow["payable_account"]=$_POST['purchase_account']  = $myrow["purchase_account"]=$_POST['payment_discount_account']  = $myrow["payment_discount_account"]=$_POST['phone']  = $myrow["phone"]=$_POST['phone2']  = $myrow["phone2"]=$_POST['address']  = $myrow["address"]=$_POST['supp_address']  = $myrow["supp_address"]=$_POST['notes']  = $myrow["notes"]=$_POST['contact']  = $myrow["contact"]=$_POST['fax']  = $myrow["fax"]=$_POST['email']  = $myrow["email"]='';
	//	$_POST['rep_lang']  = $myrow["rep_lang"];
		
	}

	table_section_title(_("Basic Data"));

	text_row(_("Contractor Name:"), 'supp_name', null, 42, 40);
	text_row(_("Contractor Short Name:"), 'supp_ref', null, 30, 30);

	text_row(_("GSTNo:"), 'gst_no', null, 42, 40);
	link_row(_("Website:"), 'website', null, 35, 55);
//	if ($supplier_id && !is_new_supplier($supplier_id) && (key_in_foreign_table($_POST['supplier_id'], 'supp_trans', 'supplier_id') ||
//		key_in_foreign_table($_POST['supplier_id'], 'purch_orders', 'supplier_id'))) 
//	{
//		label_row(_("Contractor's Currency:"), $_POST['curr_code']);
//		hidden('curr_code', $_POST['curr_code']);
//	} 
//	else 
//	{
		currencies_list_row(_("Contractor's Currency:"), 'curr_code', null);
//	}
	tax_groups_list_row(_("Tax Group:"), 'tax_group_id', null);
//	text_row(_("Our Customer No:"), 'supp_account_no', null, 42, 40);

	table_section_title(_("Purchasing"));
	text_row(_("Bank Name/Account:"), 'bank_account', null, 42, 40);
//	amount_row(_("Credit Limit:"), 'credit_limit', null);
	payment_terms_list_row(_("Payment Terms:"), 'payment_terms', null);
	//
	// tax_included option from supplier record is used directly in update_average_cost() function,
	// therefore we can't edit the option after any transaction waas done for the supplier.
	//
//	if (is_new_supplier($supplier_id))
		check_row(_("Prices contain tax included:"), 'tax_included');
//	else {
		hidden('tax_included');
		label_row(_("Prices contain tax included:"), $_POST['tax_included'] ? _('Yes') : _('No'));
//	}
	table_section_title(_("Accounts"));
	gl_all_accounts_list_row(_("Accounts Payable Account:"), 'payable_account', $_POST['payable_account']);
	gl_all_accounts_list_row(_("Purchase Account:"), 'purchase_account', $_POST['purchase_account'],
		false, false, _("Use Item Inventory/COGS Account"));
	gl_all_accounts_list_row(_("Purchase Discount Account:"), 'payment_discount_account', $_POST['payment_discount_account']);

		table_section_title(_("Contact Data"));
		text_row(_("Phone Number:"), 'phone', null, 32, 30);
		text_row(_("Secondary Phone Number:"), 'phone2', null, 32, 30);


	table_section(2);
/*	$dim = get_company_pref('use_dimension');
	if ($dim >= 1)
	{
		table_section_title(_("Dimension"));
		dimensions_list_row(_("Dimension")." 1:", 'dimension_id', null, true, " ", false, 1);
		if ($dim > 1)
			dimensions_list_row(_("Dimension")." 2:", 'dimension2_id', null, true, " ", false, 2);
	}
	if ($dim < 1)
		hidden('dimension_id', 0);
	if ($dim < 2)
		hidden('dimension2_id', 0); */


	table_section_title(_("Addresses"));
	textarea_row(_("Mailing Address:"), 'address', null, 35, 5);
	textarea_row(_("Physical Address:"), 'supp_address', null, 35, 5);

	table_section_title(_("General"));
	textarea_row(_("General Notes:"), 'notes', null, 35, 5);
	
	
	
		table_section_title(_("Contact Data"));
		text_row(_("Contact Person:"), 'contact', null, 42, 40);
		text_row(_("Fax Number:"), 'fax', null, 32, 30);
		email_row(_("E-mail:"), 'email', null, 35, 55);
	//	languages_list_row(_("Document Language:"), 'rep_lang', null, _('System default'));
		record_status_list_row(_("Contractor status:"), 'inactive');
	end_outer_table(1);

	div_start('controls');
	if ($supplier_id) 
	{
		submit_center_first('submit', _("Update Contractor"), 
		  _('Update Contractor data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', get_post('supplier_id'), _("Select this Contractor and return to document entry."));
		submit_center_last('delete', _("Delete Contractor"), 
		  _('Delete Contractor data if have been never used'), true);
	}
	else 
	{
		submit_center('submit', _("Add New Contractor Details"), true, '', 'default');
	}
	div_end();
}

if (isset($_POST['submit'])) 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['supp_name']) == 0 || $_POST['supp_name'] == "") 
	{
		$input_error = 1;
		display_error(_("The Contractor name must be entered."));
		set_focus('supp_name');
	}

	if (strlen($_POST['supp_ref']) == 0 || $_POST['supp_ref'] == "") 
	{
		$input_error = 1;
		display_error(_("The Contractor short name must be entered."));
		set_focus('supp_ref');
	}

	if ($input_error !=1 )
	{

		begin_transaction();
		if ($supplier_id) 
		{
			
			update_contractor($_POST['supplier_id'],$_POST['supp_name'], $_POST['supp_ref'], $_POST['address'], $_POST['supp_address'],
				$_POST['gst_no'], $_POST['website'],  $_POST['bank_account'],
				$_POST['curr_code'], $_POST['payment_terms'], $_POST['payable_account'], $_POST['purchase_account'],
				$_POST['payment_discount_account'],$_POST['phone'],$_POST['phone2'], $_POST['notes'],$_POST['contact'],$_POST['fax'],$_POST['email'], $_POST['tax_group_id'], get_post('tax_included', 0));
			update_record_status($_POST['supplier_id'], $_POST['inactive'],
				'contractor', 'supplier_id');

			$Ajax->activate('supplier_id'); // in case of status change
			display_notification(_("Contractor has been updated."));
		} 
		else 
		{
			add_contractor($_POST['supp_name'], $_POST['supp_ref'], $_POST['address'], $_POST['supp_address'],
				$_POST['gst_no'], $_POST['website'],  $_POST['bank_account'],
				$_POST['curr_code'], $_POST['payment_terms'], $_POST['payable_account'], $_POST['purchase_account'],
				$_POST['payment_discount_account'],$_POST['phone'],$_POST['phone2'], $_POST['notes'],$_POST['contact'],$_POST['fax'],$_POST['email'], $_POST['tax_group_id'], check_value('tax_included'));

			$supplier_id = $_POST['supplier_id'] = db_insert_id();

		/*	add_crm_person($_POST['supp_ref'], $_POST['contact'], '', $_POST['address'], 
				$_POST['phone'], $_POST['phone2'], $_POST['fax'], $_POST['email'], 
				$_POST['rep_lang'], '');

			add_crm_contact('supplier', 'general', $supplier_id, db_insert_id()); */

			display_notification(_("A new Contractor has been added."));
			$Ajax->activate('_page_body');
		}
		commit_transaction();
	}

} 
elseif (isset($_POST['delete']) && $_POST['delete'] != "") 
{
	//the link to delete a selected record was clicked instead of the submit button

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'supp_trans' , purch_orders

	if (key_in_foreign_table($_POST['supplier_id'], 'supp_trans', 'supplier_id'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this contractor because there are transactions that refer to this contractor."));

	} 
	else 
	{
		if (key_in_foreign_table($_POST['supplier_id'], 'purch_orders', 'supplier_id'))
		{
			$cancel_delete = 1;
			display_error(_("Cannot delete the contractor record because purchase orders have been created against this contractor."));
		}

	}
	if ($cancel_delete == 0) 
	{
		delete_contractor($_POST['supplier_id']);

		unset($_SESSION['supplier_id']);
		$supplier_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete supplier
}

start_form();

if (db_has_suppliers()) 
{
	start_table(false, "", 3);
//	start_table(TABLESTYLE_NOBORDER);
	start_row();
	contractor_list_cells(_("Select a Contractor: "), 'supplier_id', null,
		  _('New Contractor'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('supplier_id');
		set_focus('supplier_id');
	}
} 
else 
{
	hidden('supplier_id', get_post('supplier_id'));
}

if (!$supplier_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

/* tabbed_content_start('tabs', array(
		'settings' => array(_('&General settings'), $supplier_id),
		//'contacts' => array(_('&Contacts'), $supplier_id),
		//'transactions' => array(_('&Transactions'), $supplier_id),
		//'orders' => array(_('Purchase &Orders'), $supplier_id),
	)); */
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			contractor_settings($supplier_id); 
			break;
	/*	case 'contacts':
			$contacts = new contacts('contacts', $supplier_id, 'supplier');
			$contacts->show();
			break;
		case 'transactions':
			$_GET['supplier_id'] = $supplier_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/supplier_inquiry.php");
			break;
		case 'orders':
			$_GET['supplier_id'] = $supplier_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/po_search_completed.php");
			break; */
			
			
	};
br();
tabbed_content_end();
hidden('popup', @$_REQUEST['popup']);
end_form();

end_page(@$_REQUEST['popup']);

?>
