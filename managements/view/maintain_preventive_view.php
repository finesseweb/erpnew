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
$page_security = 'SA_PREVENTVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");
page(_($help_context = "View Preventive Maintenance"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");


if (isset($_GET['id']))
{
	$prevent_id = $_GET['id'];
	
}

// get the Preventive info
$sql = "SELECT prev.*,s.description AS Item,freq.frequency_name AS frequency,con.supp_name AS contractor FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."utility AS utly ON utly.id= prev.utility_id LEFT JOIN ".TB_PREF."frequency_master AS freq ON freq.freq_id= prev.frequency_id LEFT JOIN ".TB_PREF."stock_master s ON s.stock_id= utly.items_id LEFT JOIN ".TB_PREF."contractor con ON con.supplier_id= prev.contractor_id WHERE prev.prevent_id=$prevent_id AND utly.type=1";
//display_error($result);
$result= db_query($sql,"something went wrong");
 if (db_num_rows($result) != 1)
	display_db_error("duplicate Preventive Records found", ""); 

$from_trans = db_fetch_row($result);
display_heading(_("Preventive Maintenance") . " #$prevent_id");

echo "<br>";
start_table(TABLESTYLE, "width='80%'");

start_row();
label_cells(_("Maintenance Date"), $from_trans[1], "class='tableheader'");
label_cells(_("Utility"), $from_trans[9], "class='tableheader'");
label_cells(_("Frequency"), $from_trans[10], "class='tableheader'", "align=left");
end_row();
start_row();
label_cells();
label_cells();
label_cells();
label_cells();
label_cells();
label_cells();
end_row();
start_row();
label_cells(_("Contractor"), $from_trans[11], "class='tableheader'");
label_cells(_("Observation Date"), $from_trans[5], "class='tableheader'");
label_cells(_("Observations"), $from_trans[6], "class='tableheader'");


end_row();
start_row();
label_cells(_("Corrective Action Suggested"), $from_trans[7], "class='tableheader'");
label_cells(_("Corrective Action Initiated"), $from_trans[8], "class='tableheader'");
end_row();
comments_display_row(SA_PREVENTVIEW, $prevent_id);

end_table(1);

$voided = is_voided_display(SA_PREVENTVIEW, $prevent_id, _("This Preventive Maintenance has been voided."));

$sql1 = "SELECT prev.prevent_id,items.item_id AS item,items.quantity AS qty,items.stock_qty AS stock_qty FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."preventmaintain_entry_items AS items ON items.prevent_id= prev.prevent_id  WHERE prev.prevent_id=$prevent_id";
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
    $th = array(_("Item"), _("Quantity"), _("Total Stock Quantity"));
	table_header($th);

    $k = 0; //row colour counter
	$total_qty = 0;
    $total_stock=0;
    while ($item = db_fetch($items))
    {

    		alt_table_row_color($k);
        	label_cell($item["item"]);
    		label_cell($item["qty"],"align=right");
			label_cell($item["stock_qty"],"align=right");
    		end_row();
    		$total_qty += $item["qty"];
			$total_stock += $item["stock_qty"];
		
	}

	start_row();
		label_cell("Total","align=right");
		label_cell($total_qty,"align=right");
		label_cell($total_stock,"align=right");
	end_row();
	end_table(1);

}






$sql4 = "SELECT prev.prevent_id,param_names.param_title AS param FROM ".TB_PREF."prevent_maintain_entry AS prev LEFT JOIN ".TB_PREF."prevent_maintain_params AS params ON params.prevent_id= prev.prevent_id LEFT JOIN ".TB_PREF."utility_parameter_items AS param_names ON param_names.items_id= params.parameters  WHERE prev.prevent_id=$prevent_id";
$params= db_query($sql4,"something went wrong");

if (db_num_rows($params)==0)
{
	display_note(_("There are no Parameters for this Maintenance."));
}
else
{

	display_heading2(_("Parameters / Scope of work"));
    echo "<br>";
    start_table(TABLESTYLE, "width='40%'");
    $th = array(_("S. No"), _("Parameters / Scope of work"));
	table_header($th);

    $k = 0; //row colour counter
	$total_qty = 0;
  $i=1;
    while ($item = db_fetch($params))
    {

    		alt_table_row_color($k);
        	label_cell($i,"align=center");
    		label_cell($item["param"]);
    		end_row();
    	  $i++;
	}

	end_table(1);
}

$sql3 = "SELECT prevent.prevent_id,prevent.n_item AS item,prevent.n_qty AS qty,prevent.n_bill_date AS bill_date,prevent.n_billno AS n_billno,prevent.n_contractor AS contractor FROM ".TB_PREF."prevent_new_items AS prevent   WHERE prevent.prevent_id=$prevent_id";
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
end_page(true, false, false, SA_PREVENTVIEW, $prevent_id);
?>