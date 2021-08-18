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
$page_security = 'SA_MAINTENANCE';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Contractor")); 

include($path_to_root . "/maintenance/includes/db/contractor_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if (isset($_GET['supplier_id'])) 
{
	$_POST['supplier_id'] = $_GET['supplier_id'];
}

$supplier_id = get_post('supplier_id'); 
//-----------------------------------------------------------------------------------
function can_process() 
{
	
	if (strlen($_POST['supp_name']) == 0) 
	{
		display_error(_("The Contractor Name cannot be empty."));
		set_focus('supp_name');
		return false;
	}	
	
	return true;
}

//-----------------------------------------------------------------------------------

/*if ($Mode=='ADD_ITEM' && can_process()) 
{

	add_contractor($_POST['supp_name'], $_POST['supp_ref'], $_POST['address'], $_POST['supp_address'],
				$_POST['gst_no'], $_POST['website'],  $_POST['bank_account'],
				$_POST['curr_code'], $_POST['payment_terms'], $_POST['payable_account'], $_POST['purchase_account'],
				$_POST['payment_discount_account'],$_POST['phone'],$_POST['phone2'], $_POST['notes'],$_POST['contact'],$_POST['fax'],$_POST['email'], $_POST['tax_group_id'], check_value('tax_included'));
	display_notification(_('New Maintenance has been added'));
	$Mode = 'RESET';
} 


//-----------------------------------------------------------------------------------
if ($Mode=='UPDATE_ITEM' && can_process()) 
{
	//display_error('aaaaaaaaa'); die;
	display_notification(_('Selected Contractor has been updated'));
	update_contractor($selected_id, $_POST['supp_name'], $_POST['supp_ref'], $_POST['address'], $_POST['supp_address'],
				$_POST['gst_no'], $_POST['website'],  $_POST['bank_account'],
				$_POST['curr_code'], $_POST['payment_terms'], $_POST['payable_account'], $_POST['purchase_account'],
				$_POST['payment_discount_account'],$_POST['phone'],$_POST['phone2'], $_POST['notes'],$_POST['contact'],$_POST['fax'],$_POST['email'], $_POST['tax_group_id'], check_value('tax_included'));
	$Mode = 'RESET';
}

//-----------------------------------------------------------------------------------

function can_delete($selected_id)
{
	if (key_in_foreign_table($selected_id, 'debtors_master', 'credit_status'))
	{
		display_error(_("Cannot delete this Maintenance."));
		return false;
	}
	
	return true;
}


//-----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	if (can_delete($selected_id))
	{
		delete_contractor($selected_id);
		display_notification(_('Selected Contractor has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
} */
//-----------------------------------------------------------------------------------

//$result = get_all_contractor(check_value('show_inactive'));
//display_error('asdf');die;
	//$Ajax->activate('_page_body');
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
		display_error(_("The supplier name must be entered."));
		set_focus('supp_name');
	}

	if (strlen($_POST['supp_ref']) == 0 || $_POST['supp_ref'] == "") 
	{
		$input_error = 1;
		display_error(_("The supplier short name must be entered."));
		set_focus('supp_ref');
	}

	if ($input_error !=1 )
	{

		begin_transaction();
		if ($supplier_id) 
		{
			update_contractor($selected_id, $_POST['supp_name'], $_POST['supp_ref'], $_POST['address'], $_POST['supp_address'],
				$_POST['gst_no'], $_POST['website'],  $_POST['bank_account'],
				$_POST['curr_code'], $_POST['payment_terms'], $_POST['payable_account'], $_POST['purchase_account'],
				$_POST['payment_discount_account'],$_POST['phone'],$_POST['phone2'], $_POST['notes'],$_POST['contact'],$_POST['fax'],$_POST['email'], $_POST['tax_group_id'], check_value('tax_included'));
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


			display_notification(_("A new supplier has been added."));
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
		display_error(_("Cannot delete this supplier because there are transactions that refer to this supplier."));

	} 
	else 
	{
		if (key_in_foreign_table($_POST['supplier_id'], 'purch_orders', 'supplier_id'))
		{
			$cancel_delete = 1;
			display_error(_("Cannot delete the supplier record because purchase orders have been created against this supplier."));
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
	start_table(false, "", 3);
//	start_table(TABLESTYLE_NOBORDER);
	start_row();
	contractor_list_cells(_("Select a supplier: "), 'supplier_id', null,
		  _('New supplier'), true, check_value('show_inactive'));
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('supplier_id');
		set_focus('supplier_id');
	}
 




/*start_table(TABLESTYLE, "width=40%");
$th = array(_("Contractor Name"), _("Reference"),'','');
inactive_control_column($th);
table_header($th);

$k = 0;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);	

	label_cell($myrow["supp_name"]);
	label_cell($myrow["supp_ref"]);
//	label_cell($disallow_text);
	inactive_control_cell($myrow["supplier_id"], $myrow["inactive"], 'contractor', 'supplier_id');
 	edit_button_cell("Edit".$myrow['supplier_id'], _("Edit"));
 	delete_button_cell("Delete".$myrow['supplier_id'], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table();
echo '<br>'; */

//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing status code
        //  display_error($selected_id); die;
		$myrow = get_contractor($selected_id);
		//  display_error($selected_id); die;
		
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
	}
	hidden('selected_id', $selected_id);
} 
table_section_title(_("Basic Data"));
text_row(_("Supplier Name:"), 'supp_name', null, 42, 40);
	text_row(_("Supplier Short Name:"), 'supp_ref', null, 30, 30);

	text_row(_("GSTNo:"), 'gst_no', null, 42, 40);
	link_row(_("Website:"), 'website', null, 35, 55);
    tax_groups_list_row(_("Tax Group:"), 'tax_group_id', null);
	
	table_section_title(_("Purchasing"));
	text_row(_("Bank Name/Account:"), 'bank_account', null, 42, 40);
	payment_terms_list_row(_("Payment Terms:"), 'payment_terms', null);
	label_row(_("Prices contain tax included:"), $_POST['tax_included'] ? _('Yes') : _('No'));
	
	table_section_title(_("Accounts"));
	gl_all_accounts_list_row(_("Accounts Payable Account:"), 'payable_account', $_POST['payable_account']);
	gl_all_accounts_list_row(_("Purchase Account:"), 'purchase_account', $_POST['purchase_account'],
		false, false, _("Use Item Inventory/COGS Account"));
	gl_all_accounts_list_row(_("Purchase Discount Account:"), 'payment_discount_account', $_POST['payment_discount_account']);
	
	table_section_title(_("Contact Data"));
		text_row(_("Phone Number:"), 'phone', null, 32, 30);
		text_row(_("Secondary Phone Number:"), 'phone2', null, 32, 30);
	
	table_section_title(_("Addresses"));
	textarea_row(_("Mailing Address:"), 'address', null, 35, 5);
	textarea_row(_("Physical Address:"), 'supp_address', null, 35, 5);

	table_section_title(_("General"));
	textarea_row(_("General Notes:"), 'notes', null, 35, 5);
	
	table_section_title(_("Contact Data"));
		text_row(_("Contact Person:"), 'contact', null, 42, 40);
		text_row(_("Fax Number:"), 'fax', null, 32, 30);
		email_row(_("E-mail:"), 'email', null, 35, 55);
		languages_list_row(_("Document Language:"), 'rep_lang', null, _('System default'));
	
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//------------------------------------------------------------------------------------

end_page();

?>
