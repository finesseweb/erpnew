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
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");

$request_id = $_POST['request_id'];
$employees_id = $_POST['employees_id'];
$status = $_POST['status'];
$comments = $_POST['comments'];
$checks = $_POST['checks'];
$leave_type = $_POST['leave_type'];
$reason = $_POST['reason'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$num_days = $_POST['num_days'];
$requested_date = $_POST['requested_date'];

if($_POST['checks'] == 'on'){
$checks = 1;
}
else{
	$checks = 0;
}
$approved_leaves = $_POST['approve_leaves'];
//print_r($comments);die;
if(!empty($request_id)){
	//$sql = "UPDATE ".TB_PREF."kv_allocation_request SET status=".db_escape($status).",comments=".db_escape($comments).",comments=".db_escape($checks).",comments=".db_escape($approved_leaves)." WHERE allocate_id=".db_escape($request_id);
	
	$sql= "INSERT INTO ".TB_PREF."kv_allocation_request_status (allocate_id, 	employees_id, type_leave, reason,from_date,to_date,no_of_days,comments,request_date,checks,approved_leaves,req_status)
		VALUES (".db_escape($request_id) . ", "
		  .db_escape($employees_id) . ", "
		  .db_escape($leave_type) . ", "
		  .db_escape($reason) . ", "
		  .db_escape($from_date) . ", "
		  .db_escape($to_date) .","
		  .db_escape($num_days) .","
		  .db_escape($comments) .","
		  .db_escape($requested_date) .","
		  .db_escape($checks) .",
		  ".db_escape($approved_leaves) . ",".db_escape($status) . ")"; 
	db_query($sql, "could not update Leave Request");	
}

?>