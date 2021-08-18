<?php


function delete_po($po)
{
	global $Refs;

	begin_transaction();
	hook_db_prevoid($po, ST_PURCHORDER);
	$sql = "DELETE FROM ".TB_PREF."purch_orders WHERE order_no=".db_escape($po);
	db_query($sql, "The order header could not be deleted");

	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no =".db_escape($po);
	db_query($sql, "The order detail lines could not be deleted");

	$Refs->restore_last(ST_PURCHORDER, $po);
	commit_transaction();
}

//----------------------------------------------------------------------------------------

function add_po(&$po_obj)
{

	//display_error($po_obj->filename);
	global $Refs;

	begin_transaction();
	hook_db_prewrite($po_obj, ST_PURCHORDER);
	

     /*Insert to purchase order header record */
        
        
     $sql = "INSERT INTO ".TB_PREF."purch_orders (supplier_id, trans_type,filename, Comments, ord_date, reference, 
     	requisition_no, into_stock_location, delivery_address, total, tax_included,desig_group,designation_id,department_id,employee_id,suppliers_id,enq_ref) VALUES(";
     $sql .= db_escape($po_obj->supplier_id) . "," .
	     db_escape($po_obj->trans_type) . "," .
		 db_escape($po_obj->filename) . "," .
         db_escape($po_obj->Comments) . ",'" .
         date2sql($po_obj->orig_order_date) . "', " .
		 db_escape($po_obj->reference) . ", " .
         db_escape($po_obj->supp_ref) . ", " .
         db_escape($po_obj->Location) . ", " .
         db_escape($po_obj->delivery_address) . ", " .
         db_escape($po_obj->get_trans_total()). ", " .
         db_escape($po_obj->tax_included) . ",
		 ".db_escape($po_obj->desig_group).",".db_escape($po_obj->designation_id).",".db_escape($po_obj->department_id).",".db_escape($po_obj->employee_id).",".db_escape($po_obj->supplier_id).",".db_escape($po_obj->enq_ref).")";

	db_query($sql, "The purchase order header record could not be inserted");

     /*Get the auto increment value of the order number created from the sql above */
       // display_error($sql);
     $po_obj->order_no = db_insert_id();

    if($_SESSION["QuotationValD"]){
    	$sqlQuotation = "UPDATE ".TB_PREF."purch_orders SET quotation=" . db_escape(1);
	    $sqlQuotation .= " WHERE order_no = " . $_SESSION["QuotationValD"];
		db_query($sqlQuotation, "The purchase order could not be updated");
		unset($_SESSION['QuotationValD']);
		
    }

     if($_SESSION["OrderValD"]){
    	$sqlQuotation = "UPDATE ".TB_PREF."purch_orders SET submitorder=" . db_escape(1);
	    $sqlQuotation .= " WHERE order_no = " . $_SESSION["OrderValD"];
		db_query($sqlQuotation, "The purchase order could not be updated");
		unset($_SESSION['OrderValD']);
		
    }
  
     /*Insert the purchase order detail records */
     foreach ($po_obj->line_items as $line_no => $po_line)
     {
		$sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, trans_type, item_code, description, delivery_date,unit_price,quantity_ordered,gst,cst,ist,hsn_no,gst_amt,cst_amt,ist_amt) VALUES (";
		$sql .= $po_obj->order_no . ", " .
		db_escape($po_obj->trans_type). "," .
		db_escape($po_line->stock_id). "," .
		db_escape($po_line->item_description). ",'" .
		date2sql($po_line->req_del_date) . "'," .
		db_escape($po_line->price) . ", " .
                db_escape($po_line->quantity) . ", " .
                db_escape($po_line->gst) . ", " .
                db_escape($po_line->cst) . ", " .
                db_escape($po_line->ist) . ", " .
                db_escape($po_line->hsn_no) . ", " .
                db_escape($po_line->gst_amt) . ", " .
                db_escape($po_line->cst_amt) . ", " .
		db_escape($po_line->ist_amt). ")";
		db_query($sql, "One of the purchase order detail records could not be inserted");
		$po_obj->line_items[$line_no]->po_detail_rec = db_insert_id();
		
		if($po_obj->trans_type == 100){
			$query = "INSERT INTO ".TB_PREF."stock_moves(trans_no,stock_id,type,loc_code,tran_date,reference,qty)VALUES(".db_escape($po_obj->order_no).",".db_escape($po_line->stock_id).",".
			db_escape($po_obj->trans_type). ",".db_escape($po_obj->Location).",'" .
         date2sql($po_obj->orig_order_date) . "',". db_escape($po_obj->reference).",".db_escape(-$po_line->quantity).")";
		 
		 	//display_error($sql);
			db_query($query, "The stock not deducted");

		}
     }

	$Refs->save($po_obj->trans_type, $po_obj->order_no, $po_obj->reference);

	//add_comments(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date, $po_obj->Comments);

	add_audit_trail($po_obj->trans_type, $po_obj->order_no, $po_obj->orig_order_date);
	hook_db_postwrite($po_obj, $po_obj->trans_type);
	commit_transaction();

	return $po_obj->order_no;
}

//----------------------------------------------------------------------------------------

function update_po(&$po_obj)
{
	begin_transaction();
	hook_db_prewrite($po_obj, ST_PURCHORDER);

    /*Update the purchase order header with any changes */
    $sql = "UPDATE ".TB_PREF."purch_orders SET Comments=" . db_escape($po_obj->Comments) . ",
		requisition_no= ". db_escape( $po_obj->supp_ref). ",
		into_stock_location=" . db_escape($po_obj->Location). ",
		ord_date='" . date2sql($po_obj->orig_order_date) . "',
		delivery_address=" . db_escape($po_obj->delivery_address).",
		total=". db_escape($po_obj->get_trans_total()).",
		
		desig_group=".db_escape($po_obj->desig_group).",
		designation_id=".db_escape($po_obj->designation_id).",
		department_id=".db_escape($po_obj->department_id).",
		employee_id=".db_escape($po_obj->employee_id).",
		filename=".db_escape($po_obj->filename).",
		suppliers_id=".db_escape($po_obj->suppliers_id).",
		enq_ref=".db_escape($po_obj->enq_ref).",
		tax_included=". db_escape($po_obj->tax_included);
    $sql .= " WHERE order_no = " . $po_obj->order_no;
	db_query($sql, "The purchase order could not be updated");

	$sql = "DELETE FROM ".TB_PREF."purch_order_details WHERE order_no="
		.db_escape($po_obj->order_no);
	db_query($sql, "could not delete old purch order details");

    /*Now Update the purchase order detail records */
    foreach ($po_obj->line_items as $po_line)
    {
    	$sql = "INSERT INTO ".TB_PREF."purch_order_details (po_detail_item, order_no, item_code, 
    		description, delivery_date, unit_price, quantity_ordered, quantity_received,gst,cst,ist,hsn_no,gst_amt,cst_amt,ist_amt) VALUES ("
			.db_escape($po_line->po_detail_rec ? $po_line->po_detail_rec : 0). ","
			.$po_obj->order_no . ","
			.db_escape($po_line->stock_id). ","
			.db_escape($po_line->item_description). ",'"
			.date2sql($po_line->req_del_date) . "',"
			.db_escape($po_line->price) . ", "
			.db_escape($po_line->quantity) . ", "
                
			.db_escape($po_line->qty_received) . ","
                         .db_escape($po_line->gst) . ", " 
                       .db_escape($po_line->cst) . ", " 
                         .db_escape($po_line->ist) . ", "
                        .db_escape($po_line->hsn_no) . ", " 
                       .db_escape($po_line->gst_amt) . ", " 
                        .db_escape($po_line->cst_amt) . ", "
		        .db_escape($po_line->ist_amt). ")";
		
		db_query($sql, "One of the purchase order detail records could not be updated");
    }

	// add_comments(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date, $po_obj->Comments);

	add_audit_trail($po_obj->trans_type, $po_obj->order_no, Today(), _("Updated."));
	hook_db_postwrite($po_obj, ST_PURCHORDER);
	commit_transaction();

	return $po_obj->order_no;
}

//----------------------------------------------------------------------------------------

function read_po_header($order_no, &$order)
{
//	display_error($order_no);
   	$sql = "SELECT ".TB_PREF."purch_orders.*, ".TB_PREF."suppliers.supp_name, ".TB_PREF."suppliers.tax_group_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."locations.location_name
		FROM ".TB_PREF."purch_orders, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE ".TB_PREF."purch_orders.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."locations.loc_code = into_stock_location
		AND ".TB_PREF."purch_orders.order_no = ".db_escape($order_no);

   	$result = db_query($sql, "The order cannot be retrieved");
 
 
	if (db_num_rows($result) == 1)
	{

      	$myrow = db_fetch($result);
 //display_error($myrow['filename']);
      	$order->trans_type = ST_PURCHORDER;
      	$order->order_no = $order_no;
      	$order->set_supplier($myrow["supplier_id"], $myrow["supp_name"], $myrow["curr_code"],
      		$myrow['tax_group_id'], $myrow["tax_included"]);

		$order->credit = get_current_supp_credit($order->supplier_id);

      	$order->orig_order_date = sql2date($myrow["ord_date"]);
		if($order->trans_type = "ST_PURCHQUOTE"){
		 $order->filename = $myrow["filename"];
		}
      	$order->Comments = nl2br($myrow["comments"]);
      	$order->Location = $myrow["into_stock_location"];
      	$order->supp_ref = $myrow["requisition_no"];
      	$order->reference = $myrow["reference"];
		$order->employee_id = $myrow["employee_id"];
		$order->desig_group = $myrow["desig_group"];
		$order->department_id = $myrow["department_id"];
      	$order->delivery_address = $myrow["delivery_address"];
      	return true;
	}

	display_db_error("FATAL : duplicate purchase order found", "", true);
	return false;
}
//----------------------------------------------------
function read_po_header_location($order_no, &$order,$location_id)
{
   $sql = "SELECT ".TB_PREF."purch_orders.*, ".TB_PREF."suppliers.supp_name, ".TB_PREF."suppliers.tax_group_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."locations.location_name
		FROM ".TB_PREF."purch_orders, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE ".TB_PREF."purch_orders.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."purch_orders.into_stock_location = ".db_escape($location_id);

   	$result = db_query($sql, "The order cannot be retrieved");

	if (db_num_rows($result) == 1)
	{

      	$myrow = db_fetch($result);
      	//$order->trans_type = ST_PURCHORDER;
      	//$order->order_no = $order_no;
      	$order->set_supplier($myrow["supplier_id"], $myrow["supp_name"], $myrow["curr_code"],
      	$myrow['tax_group_id'], $myrow["tax_included"],$myrow["address_state"]);
		$order->credit = get_current_supp_credit($order->supplier_id);
      	$order->orig_order_date = sql2date($myrow["ord_date"]);
      	$order->Comments = nl2br($myrow["comments"]);
      	$order->Location = $myrow["into_stock_location"];
      	$order->supp_ref = $myrow["requisition_no"];
      	$order->reference = $myrow["reference"];
		$order->employee_id = $myrow["employee_id"];
		$order->desig_group = $myrow["desig_group"];
		$order->department_id = $myrow["department_id"];
      	$order->delivery_address = $myrow["delivery_address"];
      	//return true;
	}

	//display_db_error("FATAL : duplicate purchase order found", "", true);
	return true;
}
//----------------------------------------------------------------------
function read_rfq_header($order_no, &$order)
{

   	$sql = "SELECT ".TB_PREF."purch_orders.*, GROUP_CONCAT(".TB_PREF."suppliers.supp_name) AS supp_name, ".TB_PREF."suppliers.tax_group_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."locations.location_name
		FROM ".TB_PREF."purch_orders, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE FIND_IN_SET(".TB_PREF."suppliers.supplier_id,".TB_PREF."purch_orders.suppliers_id)
		AND ".TB_PREF."locations.loc_code = into_stock_location
		AND ".TB_PREF."purch_orders.order_no = ".db_escape($order_no);
		
		
		
   	$result = db_query($sql, "The order cannot be retrieved");
 
 
	if (db_num_rows($result) == 1)
	{

      	$myrow = db_fetch($result);
 
      	$order->trans_type = ST_PURCHORDER;
      	$order->order_no = $order_no;
      	$order->set_supplier($myrow["supplier_id"], $myrow["supp_name"], $myrow["curr_code"],
      		$myrow['tax_group_id'], $myrow["tax_included"]);

		$order->credit = get_current_supp_credit($order->supplier_id);

      	$order->orig_order_date = sql2date($myrow["ord_date"]);
      	$order->Comments = nl2br($myrow["comments"]);
      	$order->Location = $myrow["into_stock_location"];
      	$order->supp_ref = $myrow["requisition_no"];
      	$order->reference = $myrow["reference"];
		$order->employee_id = $myrow["employee_id"];
		$order->desig_group = $myrow["desig_group"];
		$order->department_id = $myrow["department_id"];
      	$order->delivery_address = $myrow["delivery_address"];
      	return true;
	}

	display_db_error("FATAL : duplicate RFQ found", "", true);
	return false;
}



function read_po_header2($order_no, &$order)
{

   	$sql = "SELECT ".TB_PREF."purch_orders.* FROM ".TB_PREF."purch_orders
		WHERE ".TB_PREF."purch_orders.order_no = ".db_escape($order_no);

   	$result = db_query($sql, "The order cannot be retrieved");

	if (db_num_rows($result) == 1)
	{

      	$myrow = db_fetch($result);

      	$order->trans_type = ST_PURCHORDER;
      	$order->order_no = $order_no;
      	
     // 	$order->set_supplier($myrow["supplier_id"], $myrow["supp_name"], //$myrow["curr_code"],
      	//	$myrow['tax_group_id'], $myrow["tax_included"]);

	//	$order->credit = get_current_supp_credit($order->supplier_id);

      	$order->orig_order_date = sql2date($myrow["ord_date"]);
      	$order->Comments = nl2br($myrow["comments"]);
     // 	$order->Location = $myrow["into_stock_location"];
     // 	$order->supp_ref = $myrow["requisition_no"];
      	$order->reference = $myrow["reference"];
		$order->employee_id = $myrow["employee_id"];
		$order->desig_group = $myrow["desig_group"];
		$order->department_id = $myrow["department_id"];
      	$order->delivery_address = $myrow["delivery_address"];
      	return true;
	}

	display_db_error("FATAL : duplicate purchase order found", "", true);
	return false;
}

//----------------------------------------------------------------------------------------

function read_po_items($order_no, &$order, $open_items_only=false)
{
	/*now populate the line po array with the purchase order details records */

	    $sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE order_no =".db_escape($order_no);
  
    if ($open_items_only)
 		echo $sql .= " AND (".TB_PREF."purch_order_details.* > ".TB_PREF."purch_order_details.quantity_received) ";

	$sql .= " ORDER BY po_detail_item";

	$result = db_query($sql, "The lines on the purchase order cannot be retrieved");

    if (db_num_rows($result) > 0)
    {
		while ($myrow = db_fetch($result))
        {
		
        	$data = get_purchase_data($order->supplier_id, $myrow['item_code']);
			
        	if ($data !== false)
        	{
			
        		if ($data['supplier_description'] != "")
        			$myrow['description'] = $data['supplier_description'];
					
        		//if ($data['suppliers_uom'] != "")
        		//	$myrow['units'] = $data['suppliers_uom'];
				
        	}		
            if (is_null($myrow["units"]))
            {
		       	$units = "";
            }
            else
            {
                $units = $myrow["units"];
            }
            if ($order->add_to_order($order->lines_on_order, $myrow["item_code"],
            	$myrow["quantity_ordered"],$myrow["description"],
            	$myrow["unit_price"],$units, sql2date($myrow["delivery_date"]),
            	$myrow["qty_invoiced"], $myrow["quantity_received"],$myrow["gst"],$myrow["gst_amt"],$myrow["cst"],$myrow["cst_amt"],$myrow["ist"],$myrow["ist_amt"],'',$myrow["hsn_no"])) {
			
            	//display_error($myrow["unit_price"]);
            		$newline = &$order->line_items[$order->lines_on_order-1];
					
					$newline->po_detail_rec = $myrow["po_detail_item"];
					
					$newline->standard_cost = $myrow["std_cost_unit"];  /*Needed for receiving goods and GL interface */
				// set for later GRN edition
//	   	    	$newline->receive_qty = $newline->quantity - $newline->qty_dispatched;
			}
        } /* line po from purchase order details */
    } //end of checks on returned data set
}

//----------------------------------------------------------------------------------------

function read_po_items_location($order_no, &$order, $open_items_only=false,$location_id)
{
	/*now populate the line po array with the purchase order details records */

	// $sql = "SELECT ".TB_PREF."purch_order_details.*, units,SUM(quantity_ordered)
	// 	FROM ".TB_PREF."purch_order_details
	// 	LEFT JOIN ".TB_PREF."stock_master
	// 	ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
	// 	LEFT JOIN ".TB_PREF."purch_orders
	// 	ON ".TB_PREF."purch_orders.trans_type=".TB_PREF."purch_order_details.trans_type
	// 	WHERE ".TB_PREF."purch_orders.into_stock_location=".db_escape($location_id)." AND ".TB_PREF."purch_order_details.trans_type=25 GROUP BY ".TB_PREF."purch_order_details.item_code";


		$sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		LEFT JOIN ".TB_PREF."stock_moves
		ON ".TB_PREF."stock_moves.trans_no=".TB_PREF."purch_order_details.order_no
		WHERE loc_code =".db_escape($location_id)." AND loc_code != '' GROUP BY ".TB_PREF."purch_order_details.item_code";

	$result = db_query($sql, "The lines on the purchase order cannot be retrieved");

	
    if (db_num_rows($result) > 0)
    {
		while ($myrow = db_fetch($result))
        {
        	$data = get_purchase_data($order->supplier_id, $myrow['item_code']);
			
        	if ($data !== false)
        	{
			
        		if ($data['supplier_description'] != "")
        			$myrow['description'] = $data['supplier_description'];
				
        	}		
            if (is_null($myrow["units"]))
            {
		       	$units = "";
            }
            else
            {
                $units = $myrow["units"];
            }
            $sql1 = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
            WHERE stock_id=".db_escape($myrow["item_code"])." AND loc_code = ".db_escape($location_id);

            $result2 = db_query($sql1); 
            $db_data = db_fetch($result2);
            if ($order->add_to_order($order->lines_on_order, $myrow["item_code"],
            	$myrow["quantity_ordered"],$myrow["description"],
            	$myrow["unit_price"],$units, sql2date($myrow["delivery_date"]),
            	$myrow["qty_invoiced"], $myrow["quantity_received"],$myrow["gst"],$myrow["gst_amt"],$myrow["cst"],$myrow["cst_amt"],$myrow["ist"],$myrow["ist_amt"],$db_data[0],$myrow["hsn_no"])) {

            	$newline = &$order->line_items[$order->lines_on_order-1];
					
				$newline->po_detail_rec = $myrow["po_detail_item"];
					
					$newline->standard_cost = $myrow["std_cost_unit"];  /*Needed for receiving goods and GL interface */
				// set for later GRN edition
//	   	    	$newline->receive_qty = $newline->quantity - $newline->qty_dispatched;
			}
           
        } /* line po from purchase order details */
    } //end of checks on returned data set
}

//----------------------------------------------------------------------------------------

function read_po($order_no, &$order, $open_items_only=false)
{   
	$sql = "SELECT porder.trans_type FROM ".TB_PREF."purch_orders porder  WHERE order_no = '$order_no'";
	$res = db_query($sql,"Retreive order details");
	$results = db_fetch_row($res);
	 if($results[0] == 60){
		$result = read_rfq_header($order_no,$order);
	}
        else if($results[0] == 65){
		$result = read_rfq_header($order_no,$order);
	}
	else if($results[0] == 90){
		$result = read_po_header2($order_no, $order);
	}
	else{ 
		$result = read_po_header($order_no, $order);
	}
	if ($result)
          
		read_po_items($order_no, $order, $open_items_only);
		
}
//-----------------------------------------
function read_po_location($order_no, &$order, $open_items_only=false,$location_id)
{   
	$sql = "SELECT porder.trans_type FROM ".TB_PREF."purch_orders porder  WHERE into_stock_location = '$location_id' AND trans_type=25";
	$res = db_query($sql,"Retreive order details");
	$results = db_fetch_row($res);
	$result = read_po_header_location($order_no, $order,$location_id);
 	if ($result)
          
	read_po_items_location($order_no, $order, $open_items_only,$location_id);
		
}

//----------------------------------------------------------------------------------------

function get_po_items($order_no)
{
	$sql = "SELECT item_code, quantity_ordered, quantity_received, qty_invoiced
		FROM ".TB_PREF."purch_order_details
		WHERE order_no=".db_escape($order_no)
		." ORDER BY po_detail_item";

	$result = db_query($sql, "could not query purch order details");
    check_db_error("Could not check that the details of the purchase order had not been changed by another user ", $sql);
    return $result;
}
//----------------------------------------------------------------------------------------

function get_short_info($stock_id)
{
	$sql = "SELECT description, units, mb_flag
		FROM ".TB_PREF."stock_master WHERE stock_id = ".db_escape($stock_id);

	return db_query($sql,"The stock details for " . $stock_id . " could not be retrieved");
}

function get_sql_for_po_search_completed($supplier_id=ALL_TEXT,$type)
{
   
	global $order_number,$enquiry_ref, $calculateprice, $loadtime, $selected_stock_item;
	
	

	$sql = "SELECT 
		porder.order_no,
		
	    CASE  WHEN ($type=70) THEN CONCAT('PQ-',porder.reference) WHEN ($type=18) THEN CONCAT('PO-',porder.reference) END AS reference,
		supplier.supp_name,
		location.location_name,
		CASE WHEN ($type=70) THEN porder.enq_ref ELSE porder.requisition_no
		END AS requisition_no, 
		porder.ord_date, 
		supplier.curr_code, 
		Sum((line.unit_price+line.gst+line.cst+line.ist)*line.quantity_ordered) AS OrderValue,
		GROUP_CONCAT(line.item_code) AS item_code,
		line.unit_price,
		
		porder.into_stock_location
		FROM ".TB_PREF."purch_orders as porder, "
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location, ".TB_PREF."payment_terms as pyt_terms
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND pyt_terms.terms_indicator = supplier.payment_terms
		AND porder.trans_type = $type
		AND location.loc_code = porder.into_stock_location ";
  
	if (isset($_GET['supplier_id']))
		$sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";
	if (isset($order_number) && $order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
	if(isset($enquiry_ref) && $enquiry_ref != ""){
		$sql .= "AND porder.enq_ref LIKE ".db_escape('%'.$enquiry_ref . '%');
		if(isset($selected_stock_item)){
		$sql .= " AND FIND_IN_SET(item_code,".db_escape($selected_stock_item).")";
		}
	}
	else
	{

		$data_after = date2sql($_POST['OrdersAfterDate']);
		$date_before = date2sql($_POST['OrdersToDate']);

		$sql .= " AND porder.ord_date >= '$data_after'";
		$sql .= " AND porder.ord_date <= '$date_before'";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}
		
		if (isset($selected_stock_item))
		{
			
			$sql .= " AND FIND_IN_SET(item_code,".db_escape($selected_stock_item).")";
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);
		
	} //end not order number selected
	if(isset($calculateprice)){
	$sql .= " GROUP BY porder.order_no order by line.unit_price ASC";
	}else if(isset($loadtime)){
	
	$sql .= " GROUP BY porder.order_no 
				ORDER BY 
				CASE ptterms.terms
				WHEN 'Payment due within 10 days' THEN days_before_due 
				WHEN 'Cash Only' THEN  porder.order_no
				ELSE day_in_following_month END 
				DESC";
	}else{
	$sql .= " GROUP BY porder.order_no";
	}
	
	//display_error($sql);
	return $sql;
}	
function get_sql_for_rfq_search_completed($supplier_id=ALL_TEXT,$type)
{
   //display_error($type);
	global $order_number, $selected_stock_item;;
	$sql = "SELECT 
		porder.order_no,
	    CONCAT('RFQ-',porder.reference),
                (supplier.supp_name) AS supp_name,
		location.location_name,
		porder.requisition_no, 
		porder.ord_date, 
		supplier.curr_code,
                line.item_code, 
		porder.into_stock_location
		FROM ".TB_PREF."purch_orders as porder,  "
                        .TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE FIND_IN_SET(supplier.supplier_id,porder.suppliers_id)
                AND porder.order_no = line.order_no  
		AND porder.trans_type = $type
                AND line.trans_type = $type
		AND location.loc_code = porder.into_stock_location ";
  
	if (isset($_GET['supplier_id']))
		$sql .= "AND supplier.supplier_id=".@$_GET['supplier_id']." ";
	if (isset($order_number) && $order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
        if (isset($selected_stock_item))
	{
        $sql .= " AND FIND_IN_SET(line.item_code,".db_escape($selected_stock_item).")";
        }
	
	else
	{

		$data_after = date2sql($_POST['OrdersAfterDate']);
		$date_before = date2sql($_POST['OrdersToDate']);

		$sql .= " AND porder.ord_date >= '$data_after'";
		$sql .= " AND porder.ord_date <= '$date_before'";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}
		if (isset($selected_stock_item))
		{
			//$sql .= " AND line.item_code LIKE".db_escape("%$selected_stock_item%");
                    $sql .= " AND FIND_IN_SET(line.item_code,".db_escape($selected_stock_item).")";
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);
		
	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	//display_error($sql);
	return $sql;
}	


function get_sql_for_po_search_typecompleted($supplier_id=ALL_TEXT,$type)
{
	global $order_number, $selected_stock_item;

	$sql = "SELECT 
		porder.order_no, 
		porder.reference,  
		desg_group.name as grp_name,
		desg.name as designation,
		dept.description as description,
		porder.ord_date, 
		Sum(line.unit_price * line.quantity_ordered) AS OrderValue,
		porder.approved_status
		FROM ".TB_PREF."purch_orders as porder,".TB_PREF."purch_order_details as line,".TB_PREF."kv_desig_group as desg_group,".TB_PREF."designation_master as desg,".TB_PREF."kv_departments as dept
		WHERE porder.order_no = line.order_no
		AND porder.desig_group = desg_group.id
		AND porder.designation_id = desg.id
		AND porder.department_id = dept.id
		AND porder.trans_type = $type AND porder.approved_status = '1'";
  
	
	if (isset($order_number) && $order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
	else
	{

		$data_after = date2sql($_POST['OrdersAfterDate']);
		$date_before = date2sql($_POST['OrdersToDate']);

		$sql .= " AND porder.ord_date >= '$data_after'";
	$sql .= " AND porder.ord_date <= '$date_before'";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
		{
		$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}
		if (isset($selected_stock_item))
		{
			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
		}
		
	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	
	return $sql;
}	

function get_sql_for_po_search($supplier_id=ALL_TEXT,$type="")
{

	global $all_items, $order_number, $selected_stock_item;
	
	
	$sql = "SELECT 
		porder.order_no, 
		CONCAT('PO-',porder.reference) as reference,
		supplier.supp_name, 
		location.location_name,
		porder.requisition_no, 
		porder.ord_date,
		supplier.curr_code,
		Sum((line.unit_price+line.gst+line.cst+line.ist)*line.quantity_ordered) AS OrderValue,
		Sum(line.delivery_date < '". date2sql(Today()) ."'
		AND (line.quantity_ordered > line.quantity_received)) As OverDue
		FROM "
			.TB_PREF."purch_orders as porder, "
			.TB_PREF."purch_order_details as line, "
			.TB_PREF."suppliers as supplier, "
			.TB_PREF."locations as location
		WHERE porder.order_no = line.order_no
		AND porder.supplier_id = supplier.supplier_id
		AND porder.trans_type = $type
		AND location.loc_code = porder.into_stock_location
		AND (line.quantity_ordered > line.quantity_received) ";

	if (isset($order_number) && $order_number != "")
	{
		$sql .= "AND porder.reference LIKE ".db_escape('%'. $order_number . '%');
	}
	else
	{
		$data_after = date2sql($_POST['OrdersAfterDate']);
		$data_before = date2sql($_POST['OrdersToDate']);

		$sql .= "  AND porder.ord_date >= '$data_after'";
		$sql .= "  AND porder.ord_date <= '$data_before'";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != $all_items)
		{
			$sql .= " AND porder.into_stock_location = ".db_escape($_POST['StockLocation']);
		}

		if (isset($selected_stock_item))
		{
			$sql .= " AND line.item_code=".db_escape($selected_stock_item);
		}
		if ($supplier_id != ALL_TEXT)
			$sql .= " AND supplier.supplier_id=".db_escape($supplier_id);
	} //end not order number selected

	$sql .= " GROUP BY porder.order_no";
	
	
	return $sql;
}
?>