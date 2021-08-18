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
$page_security = 'SA_BREAKDOWNVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
page(_($help_context = "View Breakdown Maintenance"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

//include_once($path_to_root . "/gl/includes/gl_db.inc");


if (isset($_GET['id']))
{
	$break_id = $_GET['id'];
	
}

// get the Breakdown info
//$sql = "SELECT break.*,utly.name AS u_name FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= break.utility_id WHERE break.break_id=$break_id";
$sql = "SELECT break.*,CASE  WHEN(utly.name !='') THEN  utly.name  WHEN (utly.items_id !='') THEN s.description END AS u_name,con.supp_name AS contractor FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= break.utility_id LEFT JOIN ".TB_PREF."stock_master AS s ON  utly.items_id=s.stock_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= break.contractor_id WHERE break.break_id=$break_id";
//display_error($result);
$result= db_query($sql,"something went wrong");
 if (db_num_rows($result) != 1)
	display_db_error("duplicate Breakdown Records found", ""); 

$from_trans = db_fetch_row($result);

display_heading(_("Breakdown Maintenance") . " #$break_id");

echo "<br>";
start_table(TABLESTYLE, "width='80%'");

start_row();
label_cells(_("Maintenance Date"), $from_trans[1], "class='tableheader'");
label_cells(_("Utility / Process"), $from_trans[9], "class='tableheader'");
end_row();

start_row();
label_cells();
label_cells();
label_cells();
label_cells();
end_row();

start_row();
label_cells(_("Contractor"), $from_trans[10], "class='tableheader'");
label_cells(_("Start Time"), $from_trans[4], "class='tableheader'");

end_row();

start_row();
label_cells(_("End Time"), $from_trans[5], "class='tableheader'");
label_cells(_("Reason"), $from_trans[6], "class='tableheader'");
end_row();

start_row();
label_cells(_("Observations"), $from_trans[7], "class='tableheader'");
label_cells(_("Comments"), $from_trans[8], "class='tableheader'");
end_row();
comments_display_row(SA_BREAKDOWNVIEW, $break_id);

end_table(1);

$voided = is_voided_display(SA_BREAKDOWNVIEW, $break_id, _("This Breakdown Maintenance has been voided."));

$sql1 = "SELECT break.break_id,items.item_id AS item,items.quantity AS qty,items.stock_qty AS stock_qty FROM ".TB_PREF."breakdown_maintenance AS break LEFT JOIN ".TB_PREF."breakdown_maintain_items AS items ON items.break_id= break.break_id  WHERE break.break_id=$break_id";
$items= db_query($sql1,"something went wrong");

if (db_num_rows($items)==0)
{
	display_note(_("There are no items for this Maintenance."));
}
else
{

	display_heading2(_("Items"));
    echo "<br>";
    start_table(TABLESTYLE, "width='40%'");
   
    $th = array(_("Item"), _("Quantity"),_("Total Stock Quantity"));
	table_header($th);

    $k = 0; //row colour counter
	$total_qty = 0;
	$total_stock = 0;

    while ($item = db_fetch($items))
    {

		//if ($item["prevent_id"] == $from_trans["prevent_id"])
		//{
    		alt_table_row_color($k);

        	label_cell($item["item"]);
    		label_cell($item["qty"],"align=right");
			label_cell($item["stock_qty"],"align=right");
          /*  if ($dim >= 1)
                label_cell(get_dimension_string($item['dimension_id'], true));
            if ($dim > 1)
                label_cell(get_dimension_string($item['dimension2_id'], true));
    		amount_cell($item["amount"]);
    		label_cell($item["memo_"]); */
    		end_row();
    		$total_qty += $item["qty"];
			$total_stock += $item["stock_qty"];
		//}
	}

	start_row();
		label_cell("Total","align=right");
		label_cell($total_qty,"align=right");
		label_cell($total_stock,"align=right");
	end_row();
	end_table(1);

	/*
	if (!$voided)
		display_allocations_from($from_trans['person_type_id'], $from_trans['person_id'], 1, $trans_no, $from_trans['settled_amount']); */
}


$sql3 = "SELECT break.break_id,break.n_item AS item,break.n_qty AS qty,break.n_bill_date AS bill_date,break.n_billno AS n_billno,break.n_contractor AS contractor FROM ".TB_PREF."breakdown_new_items AS break  WHERE break.break_id=$break_id";
$new_items= db_query($sql3,"something went wrong");

if (db_num_rows($new_items)==0)
{
	display_note(_("There is no New Items in this Maintenance."));
}
else
{

	display_heading2(_("New Items"));
    echo "<br>";
    start_table(TABLESTYLE, "width='60%'");
    $th = array(_("Item"), _("Quantity"), _("Bill Date"), _("Bill No."), _("Contractor"));
	table_header($th);

    $k = 0; //row colour counter
	$total_qty = 0;
  //  $total_stock=0;
    while ($item = db_fetch($new_items))
    {

    		alt_table_row_color($k);
        	label_cell($item["item"]);
    		label_cell($item["qty"],"align=right");
			label_cell($item["bill_date"]);
			label_cell($item["n_billno"]);
			label_cell($item["contractor"]);
    		end_row();
    		$total_qty += $item["qty"];
			//$total_stock += $item["stock_qty"];
		
	}

	start_row();
		label_cell("Total","align=right");
		label_cell($total_qty,"align=right");
		label_cell();
		label_cell();
		label_cell();
		//label_cell($total_stock,"align=right");
	end_row();
	end_table(1);

}



end_page(true, false, false, SA_BREAKDOWNVIEW, $break_id);
?>