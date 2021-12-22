+<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$page_security = 'SA_STATUTORY_USER';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");

//add_access_extensions();
//page(_($help_context = "statutory")); 
include_once($path_to_root . "/admin/db/attachments_db.inc");
include($path_to_root . "/managements/includes/db/statutory_db.inc");
include($path_to_root . "/managements/includes/db/ao.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/managements/includes/db/nameReturn_db.inc");
include($path_to_root . "/modules/ExtendedHRM/includes/Payroll.inc" );
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_sub_category_db.inc");
include($path_to_root . "/includes/ui.inc");

if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1) {
    $row = get_employee_cv($view_id);
    if ($row['filename'] != "") {
        if (in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'] . '?vw=' . $view_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: " . $type);
            header('Content-Length: ' . $row['filesize']);
            //if ($type == 'application/octet-stream')
            //	header('Content-Disposition: attachment; filename='.$row['filename']);
            //else
            header("Content-Disposition: inline");
            $dir = company_path() . '/statutory/' . $_POST['statutory'] . $_POST['return_name'] . $_POST['freq_name'] . $_POST['year'];
            echo file_get_contents($dir . $row['unique_name']);
            exit();
        }
    }
}
if (list_updated('category_id')) {
	$Ajax->activate('sub_cat_name');
}
if (list_updated('sub_cat_name')) {
	$Ajax->activate('item_name');
}
$empl_id = $_SESSION['wa_current_user']->empl_id;

$version_id = get_company_prefs('version_id');

$js = '';
if ($version_id['version_id'] == '2.4.1') {
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);

    if (user_use_date_picker())
        $js .= get_js_date_picker();
}else {
    if ($use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if ($use_date_picker)
        $js .= get_js_date_picker();
}
page(_($help_context = "Assembled Assets"), false, false, "", $js);

simple_page_mode(true);

check_db_has_employees(_("There is no employee in this system. Kindly Open <a href='" . $path_to_root . "/modules/ExtendedHRM/manage/employees.php'>Add And Manage Employees</a> to update it"));


$_POST['dynamic'] = 1;


if (isset($_POST['empl_id']) && !empty($_POST['empl_id']))
    $empl_id = $_POST['empl_id'];

if (isset($_GET['NewProduct'])) {
	$_SESSION['page_title'] = _($help_context = "Assemble Assets");
	create_new_ao();
}
if (isset($_POST['EnterLine'])){
     handle_add_new_item(); 
}
         


function canprocess() {
    
}

function handle_add_new_item()
{       
$allow_update = true;
	if ($allow_update == true)
	{ 
            $newIndex = count($_SESSION['ao']->line_items);
		if (count($_SESSION['ao']->line_items) > 0)
		{  
		    foreach ($_SESSION['ao']->line_items as $order_item) 
		    { 
    			/* do a loop round the items on the order to see that the item
    			is not already on this order */
   			    if (($order_item->stock_id == $_POST['item_name']) ) 
   			    {
					display_warning(_("The selected item is already on this order."));
			    }
                            
		    } /* end of the foreach loop to look for pre-existing items of the same code */
		}

		 if ($allow_update == true)
		{
                     
			

			 if ($allow_update)
			{ 
				//$myrow = db_fetch($result);
                                 // display_error($_POST['hsn_no']); die();
				$_SESSION['ao']->add_to_order (count($_SESSION['ao']->line_items), $_POST['item_name'], input_num('qty'),$_POST['category_id'], $_POST['sub_cat_name']);
                           
				//unset_form_variables();
				$_POST['item_name']	= "";
	   		} 
	   		else 
	   		{
			     display_error(_("The selected item does not exist or it is a kit part and therefore cannot be purchased."));
		   	} 

		}  /* end of if not already on the order and allow input was true*/
    }
	line_start_focus();
}
function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  $Ajax->activate('_page_body');
  set_focus('_stock_id_edit');
}


if ($Mode == 'ADD_ITEM') {
    begin_transaction();
    $last_id = is_asset($_POST['name'], $_POST['code']);
    if(!$last_id){
        $last_id = add_asset_main($_POST['name'], $_POST['code']);
    }
        if($last_id){
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
   			    {
			   add_assemble_asset_main($order_item->item_category, $order_item->item_sub_category, $order_item->stock_id,$order_item->quantity, $last_id);
                                          }
                    }
                    create_new_ao();
                }
            
            commit_transaction();
        }
    
 
        $_POST['upload'] = 1;
  

    // }
    $Ajax->activate('_page_body');


    $Mode = 'RESET';
}

if ($Mode == 'UPDATE_ITEM') {
   // display_error($selected_id);
    if($selected_id != -1)
    update_asset($_POST['name'], $_POST['code'], $selected_id);
   
   
     if (count($_SESSION['ao']->line_items) > 0)
		{   delete_assemble_asset($selected_id);
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
   			    {
			   add_assemble_asset_main($order_item->item_category, $order_item->item_sub_category, $order_item->stock_id,$order_item->quantity, $selected_id);
                           }
                    }
                    create_new_ao();
                }
    $Mode = 'RESET';
}


if ($Mode == 'RESET') {
    unset($_POST['empl_id']);
    unset($_POST['empl_firstname']);
    $_POST['upload'] = 1;
    $selected_id = -1;
}



function edit_link($row) {
    return button('Edit' . $row["id"], _("Edit"), _("Edit"), ICON_EDIT);
    hidden('id', $row["id"]);
}

function view_link($row) {
    return button('view' . $row["id"], _("View"), _("View"), ICON_VIEW);
}

function download_link($row) {
    return button('Download' . $row["id"], _("Download"), _("Download"), ICON_DOWN);
}

function delete_link($row) {
    submit_js_confirm("Delete" . $row["id"], sprintf(_("You are about to delete a Department Do you want to continue?"), $row["id"]));
    return button('Delete' . $row["id"], _("Delete"), _("Delete"), ICON_DELETE);
    hidden('id', $row["id"]);
}

start_form(true);
$result = get_all_asset_main(check_value('show_inactive'), $empl_id);
start_table(TABLESTYLE);
$th = array(_("Product_name"), _('Product_code'), ("Edit"),'');
table_header($th);

$k = 0;

//$result = get_all_return(check_value('show_inactive'));
//echo $result["return_name"][0];
while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);
    label_cell(get_product_name($myrow["product_id"])[1],'',null,'center');
    label_cell($myrow["code"],'',null,'center');
    edit_button_cell("Edit" . $myrow['asset_id'], _("Edit"));   
     //inactive_control_cell($myrow["id"], $myrow["inactive"], 'statutory_main', 'id');
    //delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();
}

//inactive_control_row($th);
end_table();
echo '<br>';

//---------------------------------------------------------------------------------------
//viewing_controls();


if($Mode == 'Remove'){
    $_SESSION['ao']->remove_from_order($selected_id);
}





if ($selected_id != -1) {

    if ($Mode == 'Edit') {
        $result = get_all_product_master($selected_id);
        create_new_ao();
        while ($myrow = db_fetch($result)) {
           $_POST['code'] = $myrow['code'];
        $_POST['name'] = $myrow['id'];
            $assetresult = get_all_assembeld_assets($myrow['id']);
            while ($row = db_fetch($assetresult)){
                $_POST['category_id'] =$row['item_category_id'];
                $_POST['sub_cat_name'] =$row['item_sub_category_id'];
                $_POST['item_name'] = $row['stock_id'];
                $_POST['qty'] =  $row['qty']; 
               handle_add_new_item(); 
            }
            
        }
        
    }
    hidden('selected_id', $selected_id);
}

br(2);
//echo $_POST['upload'];
if ($Mode != 'Uploadedit' && $Mode != 'View' ) {
    if (!$_POST['upload'])
        $_POST['upload'] = 1;
    start_table(TABLESTYLE2);
        product_list_cells(_('Product Name'), 'name', $_POST['name'], true);
        text_row("Product Code", 'code', $_POST['code'], 50,null);
    end_table(1);
    div_start('controls', 'items_table');
start_table(TABLESTYLE2,'width="70%"');
  $th = array(_("Item Category"), _("Item Sub Category"),_("Item code"), _('Item Name'), _('Qty Used'), _('#'));
    table_header($th);
            if (count($_SESSION['ao']->line_items) > 0)
		{  
		    foreach ($_SESSION['ao']->line_items as $line_no => $order_item) 
		    { 
                          if (($order_item->stock_id != $_POST['item_name'] )) 
   			    {
					  addLabels($order_item->item_category, $order_item->item_sub_category, $order_item->stock_id,$order_item->quantity,$line_no);
			    }
                    }
                }
               
            stock_categories_list_cells(null,'category_id', null, false, true);
            $category_id = $_POST['category_id'];
            sub_category_list_cells(null,'sub_cat_name', null, false, true, $category_id);
            $sub_cat_id = $_POST['sub_cat_name'];
            $getcode =get_sub_code($_POST['sub_cat_name']);
            label_cell($getcode['code']);
            item_name_list_cells(null,'item_name', null, false, true, $sub_cat_id);
            text_cells(null, 'qty', $_POST['qty'], 5);
            submit_cells('EnterLine', _("Add Item"), "colspan=1 align='center'",
		    _('Add new item to document'), true);   
    end_table(1);
    submit_add_or_update_center($selected_id == -1, '', 'both');
    div_end();
}
else {
    start_table(TABLESTYLE_NOBORDER);
    start_row();
    label_cells($label, "<button class='ajaxsubmit' type='submit' name='RESET' id='RESET' value='Cancel' title='Remove'><img src='../../themes/default/images/escape.png' height='12' alt=''><span>Cancel</span></button>");
    end_row();
    end_table();
}
end_form();

function addLabels($category_id, $sub_cat_name, $item_name,$qty,$line_no){
    start_row();
    $getcode =get_sub_code($sub_cat_name);
    $category = get_category($category_id);
label_cell($category['description']);
label_cell($getcode['sub_cat_name']); 
$getcode =get_sub_code($sub_cat_name);
            label_cell($getcode['code']);
label_cell($item_name); 
label_cell($qty);
button_cell('Remove' . $line_no, _("Remove"), _("Remove"), ICON_REMOVE);
end_row();
}
?>
<?php

end_page();
?>