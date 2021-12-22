<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
/**********************************************************************
  Page for searching item list and select it to item selection
  in pages that have the item dropdown lists.
  Author: bogeyman2007 from Discussion Forum. Modified by Joe Hunt
***********************************************************************/
$page_security = "SA_ITEM";
$path_to_root = "../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/ExtendedHRM/db/empl_alloc_db.inc");

$mode = get_company_pref('no_item_list');
if ($mode != 0)
	$js = get_js_set_combo_item();
else
	$js = get_js_select_combo_item();

page(_($help_context = "Items"), true, false, "", $js);

if(get_post("search")) {
  $Ajax->activate("item_tbl");
}

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();
text_cells(_("Employee Id"), "empl_id");
text_cells(_("Category"), "description");
submit_cells("search", _("Search"), "", _("Search items"), "default");

end_row();

end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);

$th = array("", _("Empl ID"), _("Empl Name"), _("Category"));
table_header($th);
$name = $_GET["client_id"];
$k = 0;
$result = get_employees(get_post("empl_id"),get_post("description"));

while ($myrow = db_fetch_assoc($result))
{    //display_error($myrow['item_code']);
	alt_table_row_color($k);
        
	$value = $myrow["empl_id"];
	if ($mode != 0) {
		$text = $myrow["empl_id"]."-".$myrow['empl_firstname'];
  		ahref_cell(_("Select"), 'javascript:void(0)', '', 'setComboItem(window.opener.document, "'.$name.'",  "'.$value.'", "'.$text.'")');
	}
	else {
  		ahref_cell(_("Select"), 'javascript:void(0)', '', 'selectComboItem(window.opener.document, "'.$name.'", "'.$value.'")');
	}
  	label_cell($myrow["empl_id"]);
	label_cell($myrow["empl_firstname"]);
  	label_cell($myrow["description"]);
	end_row();
}

end_table(1);

div_end();
end_page(true);
