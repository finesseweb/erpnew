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
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "../..";


include_once($path_to_root . "/includes/session.inc");

$help_id = $_POST['help_id'];

$status = $_POST['status'];
//print_r($status);die;
if(!empty($help_id)){
	$sql = "UPDATE ".TB_PREF."maintenance_help_desk SET status=".db_escape($status)." WHERE help_id=".db_escape($help_id);
	//display_error($sql);
	db_query($sql, "could not update Help Desk Complaint");	
}

?>