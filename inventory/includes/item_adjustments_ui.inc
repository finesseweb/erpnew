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
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");

//--------------------------------------------------------------------------------

function add_to_order(&$order, $new_item, $new_item_qty, $standard_cost)
{
    if ($order->find_cart_item($new_item)){
         display_error(_("For Part :") . $new_item . " " . "This item is already on this document. You can change the quantity on the existing line if necessary.");
    }
	else{
         $order->add_to_cart (count($order->line_items), $new_item, $new_item_qty, $standard_cost);
	}
}

//--------------------------------------------------------------------------------
$kv_empl_type = array(
	1 => _('Technical'),
        2 => _('Non Technical'),
	//3 => _('Visiting Faculty(Practitioner)'),
//        4 => _('Visiting Faculty(Academic)')
        );

function kv_empl_type_list_row($label, $name, $selected_id=null, $submit_on_change=true) {
	global $kv_empl_type;
		
	echo "<tr><td class='label'>$label</td><td>";
	$options = array(
	    'select_submit'=> $submit_on_change
	);
	echo array_selector($name, $selected_id, $kv_empl_type, $options);
	echo "</td></tr>\n";
}
function display_order_header(&$order)
{
	global $Refs;

	start_outer_table(TABLESTYLE2, "width='76%'"); // outer table
	table_section(1);

	locations_list_row(_("Location:"), 'StockLocation', null);
	ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_INVADJUST));

	table_section(2, "56%");

    date_row(_("Date:"), 'AdjDate', '', true);

	table_section(3, "86%");

    //movement_types_list_row(_("Detail:"), 'type', ' ', true);

    //movement_types_list_row(_("Detail:"), 'type', null, false, true);
    kv_empl_type_list_row(_("Employee Type:"), 'type', null);
    // if($_POST['type']==3){
    // 	$empl_type=1;
    // }elseif ($_POST['type']==4) {
    // 	$empl_type=2;
    // }
    $empl_type=$_POST['type'];
    
    employees_list_row21(_("Users:"), 'employee_id', null, false, true,$empl_type);
	
    if (!isset($_POST['Increase']))
    	$_POST['Increase'] = 1;
    yesno_list_row(_("Type:"), 'Increase', $_POST['Increase'],
    	_("Positive Adjustment"), _("Negative Adjustment"));

	end_outer_table(1); // outer table
}

//---------------------------------------------------------------------------------

function display_adjustment_items($title, &$order,$empl_id)
{
	global $path_to_root;

	$emplId=$_POST['employee_id'];

	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='80%'");
	$th = array(_("Item Code"), _("Item Description"), _("Quantity"),
		_("Unit"), _("Unit Cost"), _("Total"), "", "");
	if ( count($order->line_items)) $th[] = '';

	table_header($th);
	$total = 0;
	$k = 0;  //row colour counter

	$low_stock = $order->check_qoh($_POST['StockLocation'], $_POST['AdjDate'], !$_POST['Increase']);
	//unset($_SESSION['session_id']);
$Mode = '';

	$id = -1;
	if(find_submit('Edit')!=-1){
		$Mode = 'Edit';
		$id = find_submit('Edit');
	}
	else if(find_submit('Delete')!=-1)
	{
		$Mode = 'Delete';
	$id = find_submit('Delete');
		}
		else if (find_submit('UpdateItem')!=-1) {
			$Mode = 'Update';
		}
//display_error($Mode);
		//display_error($id_edit.'==='.$id);
	if($id != -1){
	$_SESSION['session_id'][] = $id;
}
else if($id == -1){
	unset($_SESSION['session_id']);
}

	$id_arr = $_SESSION['session_id'];
//echo "<prE>";print_r($order->line_items);
	$query = "SELECT * FROM ".TB_PREF."purch_order_details LEFT JOIN ".TB_PREF."purch_orders ON fa_purch_orders.order_no = fa_purch_order_details.order_no WHERE fa_purch_order_details.trans_type=100 AND fa_purch_orders.employee_id =$emplId";
	
	$result = db_query($query);
	//$myrow = db_fetch($result);
	$onlyarray = array();
	if($Mode != 'Update' ){
	$order->line_items = array();
	}
	while ($myrow = db_fetch_assoc($result))
    {
    $onlyarray[] = $myrow;
   $sql = "SELECT * FROM ".TB_PREF."stock_master
				WHERE stock_id=".db_escape($myrow['item_code']);
	$results = db_query($sql, "could not get bom");
	$cost=db_fetch($results);
	if($Mode != 'Update' ){
    $order->add_to_cart(count($order->line_items), $myrow['item_code'], $myrow['quantity_ordered'],$cost['material_cost'],null);}
    }

   //echo "<prE>";print_r($onlyarray);
//}
    
  //
  // $order->add_to_cart_inventory (count($order->line_items), $new_item, $new_item_qty, $standard_cost);
	// $order->line_items

//echo "<pre>";print_r($order->line_items);
   
	foreach ($onlyarray as $line_no=>$stock_item)
	{
		
				$stock_item['quantity'] = $order->line_items[$line_no]->quantity;
				$stock_item['standard_cost'] = $order->line_items[$line_no]->standard_cost;

		$total += ($stock_item['standard_cost'] * $stock_item['quantity']);


		if (!in_array($line_no,$id_arr))
		{
			if (in_array($stock_item['item_code'], $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else 
				alt_table_row_color($k);

			view_stock_status_cell($stock_item['item_code']);
			label_cell($stock_item['description']);
    		qty_cell($stock_item['quantity'], false, get_qty_dec($stock_item['item_code']));
    		$sql = "SELECT * FROM ".TB_PREF."stock_master
				WHERE stock_id=".db_escape($stock_item['item_code']);

			$result = db_query($sql, "could not get bom");

			$cost=db_fetch($result);
			
			label_cell($cost['units']);
			
			amount_decimal_cell($stock_item['standard_cost']);
			amount_cell($total);

			edit_button_cell("Edit$line_no", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
			end_row();
		}
		else
		{

			//echo "<prE>";print_r($stock_item);
				//$line_items = get_object_vars($order);
			//$line_items['line_items'] = $onlyarray;
				//echo "<pre>";print_r($order->line_items);
				if($Mode == 'Delete')
				   unset($order->line_items[$line_no]); 

				//echo "<pre>";print_r($order->line_items);
				//unset($order->line_items[1]);
				//unset($order->line_items[2]);  
			if($Mode == 'Edit')	{
				adjustment_edit_item_controls3($order, $line_no);
				//$_SESSION['adj_items'] = $order->line_items;
			}


		}
	}
	

	if ($id == -1)
		
		//adjustment_edit_item_controls2($order);

	//label_row(_("Total"), number_format2($total,user_price_dec()), "align=right colspan=5", "align=right", 2);

    end_table();
	if ($low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of adjustment."), 0, 1, "class='stockmankofg'");
	div_end();
}

/*function display_adjustment_items($title, &$order)
{
display_error('tesdshdg');
	global $path_to_root;

	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='80%'");
	$th = array(_("Item Code"), _("Item Description"), _("Quantity"),
		_("Unit"), _("Unit Cost"), _("Total"), "");
	if ( count($order->line_items)) $th[] = '';

	table_header($th);
	$total = 0;
	$k = 0;  //row colour counter

	$low_stock = $order->check_qoh($_POST['StockLocation'], $_POST['AdjDate'], !$_POST['Increase']);
	$id = find_submit('Edit');
	foreach ($order->line_items as $line_no=>$stock_item)
	{

		$total += ($stock_item->standard_cost * $stock_item->quantity);

		if ($id != $line_no)
		{
			if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else 
				alt_table_row_color($k);

			view_stock_status_cell($stock_item->stock_id);
			label_cell($stock_item->item_description);
    		qty_cell($stock_item->quantity, false, get_qty_dec($stock_item->stock_id));
			label_cell($stock_item->units);
			amount_decimal_cell($stock_item->standard_cost);
			amount_cell($stock_item->standard_cost * $stock_item->quantity);

			edit_button_cell("Edit$line_no", _("Edit"),
				_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"),
				_('Remove line from document'));
			end_row();
		}
		else
		{
			adjustment_edit_item_controls($order, $line_no);
		}
	}

	if ($id == -1)
		adjustment_edit_item_controls($order);

	label_row(_("Total"), number_format2($total,user_price_dec()), "align=right colspan=5", "align=right", 2);

    end_table();
	if ($low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of adjustment."), 0, 1, "class='stockmankofg'");
	div_end();
}

*/


function adjustment_edit_item_controls3(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();

	$dec2 = 0;
	$id = find_submit('Edit');
	 
	  //echo "<pre>";print_r($order->line_items);
	 //display_error($id.'====='.$line_no);
	
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, 
			$order->line_items[$id]->stock_id, $dec);
		//$_POST['std_cost'] = price_format($order->line_items[$id]->standard_cost);
		$_POST['std_cost'] = price_decimal_format($order->line_items[$id]->standard_cost, $dec2);
		$_POST['units'] = $order->line_items[$id]->units;

		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description, 'nowrap');
	    $Ajax->activate('items_table');
	}
	else
	{
    	stock_costable_items_list_cells(null, 'stock_id', null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);
		$dec = $item_info['decimals'];
   		$_POST['qty'] = number_format2(0, $dec);
		//$_POST['std_cost'] = price_format($item_info["standard_cost"]);
		$_POST['std_cost'] = price_decimal_format($item_info["standard_cost"], $dec2);
		$_POST['units'] = $item_info["units"];
	}

	//display_error($_POST['qty']);
	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	amount_cells(null, 'std_cost', null, null, null, $dec2);
	label_cell("&nbsp;");

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item bfdhf"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}








function adjustment_edit_item_controls(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();

	$dec2 = 0;
	$id = find_submit('Edit');

	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, 
			$order->line_items[$id]->stock_id, $dec);
		//$_POST['std_cost'] = price_format($order->line_items[$id]->standard_cost);
		$_POST['std_cost'] = price_decimal_format($order->line_items[$id]->standard_cost, $dec2);
		$_POST['units'] = $order->line_items[$id]->units;

		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description, 'nowrap');
	    $Ajax->activate('items_table');
	}
	else
	{
    	stock_costable_items_list_cells(null, 'stock_id', null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);
		$dec = $item_info['decimals'];
   		$_POST['qty'] = number_format2(0, $dec);
		//$_POST['std_cost'] = price_format($item_info["standard_cost"]);
		$_POST['std_cost'] = price_decimal_format($item_info["standard_cost"], $dec2);
		$_POST['units'] = $item_info["units"];
	}

	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	amount_cells(null, 'std_cost', null, null, null, $dec2);
	label_cell("&nbsp;");

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item bfdhf"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}


//---------------------------------------------------------------------------------
//---------------------------------------------Ritika-------------------------------

function adjustment_edit_item_controls2(&$order, $line_no=-1)
{

	
	global $Ajax;
	start_row();

	$dec2 = 0;
	$id = find_submit('Edit');


	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]['item_code'];
		$_POST['qty'] = qty_format($order->line_items[$id]['quantity_ordered'], 
			$order->line_items[$id]['item_code'], $dec);
		//$_POST['std_cost'] = price_format($order->line_items[$id]->standard_cost);
		$_POST['std_cost'] = price_decimal_format($order->line_items[$id]->standard_cost, $dec2);
		$_POST['units'] = $order->line_items[$id]->units;

		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]['description'], 'nowrap');
	    $Ajax->activate('items_table');
	}
	else
	{

    	stock_costable_items_list_cells(null, 'stock_id', null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			    $Ajax->activate('std_cost');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);
		$dec = $item_info['decimals'];
   		$_POST['qty'] = number_format2(0, $dec);
		//$_POST['std_cost'] = price_format($item_info["standard_cost"]);
		$_POST['std_cost'] = price_decimal_format($item_info["standard_cost"], $dec2);
		$_POST['units'] = $item_info["units"];
	}
	
	qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);
	label_cell($_POST['units'], '', 'units');

	//amount_cells(null, 'std_cost', $_POST['std_cost']);
	amount_cells(null, 'std_cost', null, null, null, $dec2);
	label_cell("&nbsp;");

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}





//----------------------------------------------------End Function-------------------
function adjustment_options_controls()
{
	  echo "<br>";
	  start_table();

	  textarea_row(_("Memo"), 'memo_', null, 50, 3);

	  end_table(1);
}


//---------------------------------------Yes/No Select box-----------------------------/
function custom_list_cell($label, $name, $selected_id=null, $submit_on_change=false,$disable=FALSE, $dd_type = 'yes_no_select_box')
{
        global $config_master_value;
        $list1 = $config_master_value[$dd_type];
        $options = array(
            'select_submit'=> $submit_on_change,
            'disabled' => $disable
        );
        $select_data = array_selector($name, $selected_id, $list1, $options);
        
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo $select_data;
	echo "</td>\n";
}
function custom_list_row($label, $name, $selected_id=null,$submit_on_change=false,$disable=FALSE,$dd_type = 'yes_no_select_box')
{
	custom_list_cell(null, $name, $selected_id,$submit_on_change,$disable,$dd_type);
	
}


//---------------------------------------------------------------------------------

?>