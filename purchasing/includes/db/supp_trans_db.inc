<?php

/* * ********************************************************************

  Released under the terms of the GNU General Public License, GPL,
  as published by the Free Software Foundation, either version 3
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 * ********************************************************************* */

//-------------------------------------------------------------------------------------------------------------
//
//	FIXME - this should be revised for transaction update case.
//
function write_supp_trans($type, $trans_no, $supplier_id, $date_, $due_date, $reference, $supp_reference, $amount, $amount_tax, $discount, $err_msg = "", $rate = 0, $included = 0) {
    $new = $trans_no == 0;
    $date = date2sql($date_);
    if ($due_date == "")
        $due_date = "0000-00-00";
    else
        $due_date = date2sql($due_date);

    if ($new)
        $trans_no = get_next_trans_no($type);

    $curr = get_supplier_currency($supplier_id);

    if ($rate == 0)
        $rate = get_exchange_rate_from_home_currency($curr, $date_);


    $sql = "INSERT INTO " . TB_PREF . "supp_trans (trans_no, type, supplier_id, tran_date, due_date,
		reference, supp_reference, ov_amount, ov_gst, rate, ov_discount, tax_included) ";
    $sql .= "VALUES (" . db_escape($trans_no) . ", " . db_escape($type)
            . ", " . db_escape($supplier_id) . ", '$date', '$due_date',
		" . db_escape($reference) . ", " . db_escape($supp_reference) . ", " . db_escape($amount)
            . ", " . db_escape($amount_tax) . ", " . db_escape($rate) . ", " . db_escape($discount) . ", " . db_escape($included) . ")";

    if ($err_msg == "")
        $err_msg = "Cannot insert a supplier transaction record";
    
    db_query($sql, $err_msg);
    add_audit_trail($type, $trans_no, $date_);

    return $trans_no;
}

//--------------------------------------------------------------------------------------------------
 function structID($id) {
         $sql = "select structure_id from erp_fee_structure_master where academic_id =".$id;
         $result = db_query($sql, $err_msg);
          $row = db_fetch_row($result);
       
      if(count($row)==0){
         return 0;
      }else
      {
          return $row[0];
      }
    }

function addstudent($date, $student_flag, $academic_id, $term_id, $person_id, $selected_id, $memo) {
    
    set_global_connection(2);
     $gpa = 0.0;
    
    $structID = structID($academic_id);

    if ($structID != 0) {
         $sql = "select concat(stu_fname,' ',stu_lname) as name from erp_student_information where student_id =".$person_id;
         $result = db_query($sql, $err_msg);
          $student_info = db_fetch_row($result);
        
        //display_error($student_info[0]);
        
       $sql = "select t1_date ,t2_date,t3_date,t4_date,t5_date from erp_fee_structure_items where structure_id =".$structID;
         $result = db_query($sql, $err_msg);
          $due_dates = db_fetch_row($result);
        $t1_date = date("Y-m-d", strtotime($due_dates[0]));
        $t2_date = date("Y-m-d", strtotime($due_dates[1]));
        $t3_date = date("Y-m-d", strtotime($due_dates[2]));
        $t4_date = date("Y-m-d", strtotime($due_dates[3]));
        $t5_date = date("Y-m-d", strtotime($due_dates[4]));
        $total_fee_in_that_term = getFee($structID, $term_id, 1);
        $service_fee = getFee($structID, $term_id, 2);
        $otherAnnualCharges = getFee($structID, $term_id, 3);
        $tuition_fee = abs($total_fee_in_that_term - ((int) $service_fee + (int) $otherAnnualCharges));
       
        $results = array(
            'participants_name' => $student_info[0],
            'participants_id' => $_POST['selected_id'],
            'gpa' => $gpa,
            'fee' => $total_fee_in_that_term,
            'service_fee' => $service_fee,
            'other_annual_charges' => $otherAnnualCharges,
            'tuition_fee' => $tuition_fee,
            'fee_discount' => 0,
            'total_fee' => $total_fee_in_that_term,
            'batch_id' => $academic_id,
            'term_id' => $term_id,
            't1_date' => $t1_date,
            't2_date' => $t2_date,
            't3_date' => $t3_date,
            't4_date' => $t4_date,
            't5_date' => $t5_date
        );
        set_global_connection(0);
         insert_data($results,$person_id);
    } else{
        display_error('Did you  forget to create fee structure before confirming the student!');
        set_global_connection(0);
        return 0;
    }
}



function getAutoID()
{
    $id =0 ;
     set_global_connection(2);
        $sql2 = "SELECT short_code  FROM  academic_master WHERE academic_year_id =" . $_POST['batch_id'];

    $academic_data = db_query($sql2);
    $academic_result = db_fetch_row($academic_data);
    $id_prefix = explode('-', $academic_result[0]);
  $id_prefix_concat = $id_prefix[0].'01';
  
  
   $sql1 = "SELECT MAX(stu_id) FROM  erp_student_information WHERE stu_id LIKE '" . $id_prefix[0] . "%'";

    $student_data = db_query($sql1);
    $max_batch_id = db_fetch_row($student_data);

    $sql1 = "SELECT MAX(stu_id) FROM  erp_student_information WHERE academic_id ='" . $_POST['batch_id']."'";
    $student_data = db_query($sql1);
    $studentid_result = db_fetch_row($student_data);

  if(!$studentid_result[0])
  $id_prefix_inc = (int)substr($studentid_result[0],3,strlen($studentid_result[0])-6)+1;
  else
  $id_prefix_inc = (int)substr($studentid_result[0],3,strlen($studentid_result[0])-6);



  if (strlen($id_prefix_inc) == 1)
  $id_prefix_inc = '0' . $id_prefix_inc;
  else
  $id_prefix_inc =  $id_prefix_inc;
  $id_prefix_concat = $id_prefix[0].$id_prefix_inc;

    $last_student = substr($studentid_result[0], strlen($studentid_result[0])-3);
    $student_inc_id = $last_student + 1;

    if (empty($last_student)) {
        $id = $id_prefix_concat . '00' . 1;
    } else {
        if (strlen($student_inc_id) == 1) {
            $id = $id_prefix_concat . '00' . $student_inc_id;
        } else if (strlen($student_inc_id) == 2) {
            $id = $id_prefix_concat . '0' . $student_inc_id;
        } else if(strlen($student_inc_id) == 3){
            $id = $id_prefix_concat . $student_inc_id;
        }
        else
        {
                 $id_prefix_inc = (int)substr($studentid_result[0],3,strlen($studentid_result[0])-6)+1;
                $id_prefix_concat = $id_prefix[0].$id_prefix_inc;
                $id = $id_prefix_concat . '00' . 1;
        }
    }

             set_global_connection(0);
             return $id;
}






function insert_data($result,$person_id){
      
   $sql = "update erp_student_information set stu_id ='".$_POST['selected_id']."', academic_id='".$_POST['batch_id']."' where stu_status !=2 AND  student_id =".$person_id;

$person_id = 0;
     $result_id = getAutoID();
     set_global_connection(2);
        db_query($sql, $err_msg);
        $sql = "INSERT INTO student_fee_details(`participants_name`,`participants_id`,`gpa`,`fee`,`service_fee`,`other_annual_charges`,`tuition_fee`,`fee_discount`,`total_fee`,`batch_id`,`term_id`,`t1_date`,`t2_date`,`t3_date`,`t4_date`,`t5_date`,`student_id`)";
    $sql .= "VALUES ('".$result['participants_name']."','".$result_id."',".$result['gpa'].",".$result['fee'].",".$result['service_fee'].",".$result['other_annual_charges'].",".$result['tuition_fee'].",".$result['fee_discount'].",".$result['total_fee'].",".$result['batch_id'].",".$result['term_id'].",'".$result['t1_date']."','".$result['t2_date']."','".$result['t3_date']."','".$result['t4_date']."','".$result['t5_date']."',".$person_id.")";
  
    $last_id = db_query($sql, $err_msg);
    if(!empty($last_id)){
        
       $sql1 = "SELECT MAX(fee_details_id) as id FROM  student_fee_details ";
                    $last_data = db_query($sql1);
                    $last_id = db_fetch_row($last_data);
                        $debit_credit_Acc = glTrans();
                     $person_id = $last_id[0];
                     $check = checkWithPdmId($result['participants_id'], $result['batch_id'], $result['term_id']);
                       if ($check == 0){
                          
                             for($gl_account = 0; $gl_account<2; $gl_account++){
                            $gl_trans['type'] = 110;
                            $gl_trans['type_no']  = $last_id[0];
                            $gl_trans['tran_date'] = date('Y-m-d');
                            $gl_trans['account'] = $debit_credit_Acc[$gl_account];
                            $gl_trans['memo_'] = 'Tuition fee';
                            
                            if($gl_account == 0)
                            {
                            $gl_trans['amount'] = $result['total_fee']-((int)$result['total_fee']+(int)$result['total_fee']);
                            }
                            elseif($gl_account == 1){
                                $gl_trans['amount'] = $result['total_fee'];
                            }
                                
                                $gl_trans['person_type_id'] = 5;
                                   set_global_connection(0);
                                insetData($gl_trans,'fa_gl_trans',$gl_account); 
                            }
                     }
                    meta_forward($_SERVER['PHP_SELF'], "student_flag=1&batch_id=".$_POST['batch_id'].'&person_id='.$person_id); 
    }
       set_global_connection(0);
}


function glTrans()
{
     set_global_connection(0);
    $sql = "SELECT `fa_kv_hrm_finance_setup`.`allowance_debit_gl_code` AS `debit`, `fa_kv_hrm_finance_setup`.`allowance_credit_gl_code` AS `credit` FROM `fa_kv_hrm_finance_setup` WHERE (type ='course-fee') AND (inactive =0)";
             $result = db_query($sql, $err_msg);
          $gl_edit = db_fetch_row($result);
            $x[0] = $gl_edit[0];
            $x[1] = $gl_edit[1];        
           set_global_connection(2);
            return $x;
        
}
//-------------------insert data-------------------------------
function insetData(array $data,$db_name ,$i){

    
    $sql = "INSERT INTO $db_name(";
    
    foreach($data as $key => $value)
        $sql.= "`".$key."`,";
    $sql = rtrim($sql,',');
    $sql .= ")values(";
    foreach($data as $value){
        if(is_numeric($value))
            $sql .= $value.',';
        else 
            $sql .="'".$value."',";
    }
    $sql = rtrim($sql,',');
    $sql.=')'; 
    $result = db_query($sql, $err_msg);
}



  function checkWithPdmId($pdm_id, $batch_id, $term_id)
    {
      
      $sql1 = "SELECT fee_details_id  FROM  student_fee_details where term_id =".$term_id." AND batch_id=".$batch_id."AND participants_id = '".$pdm_id."'";
                    $last_data = db_query($sql1);
                    $last_id = db_fetch_row($last_data);
                    if($last_id[0]=='')
                        return 0;
                    else
                        return 1; 
    }

//---------------------------------------------------------------------------------------------------

    
    
    
 function getFee($structure_id,$term_id, $category_id){  
     
     $sql = "select sum(fees) as total from erp_fee_structure_term_items where structure_id ='".$structure_id."' AND category_id ='".$category_id."' AND terms_id=".(int)$term_id;
         $result = db_query($sql, $err_msg);
          $row = db_fetch_row($result);
    return $row[0];
    
    }
//---------------------------------------------------------------------------------------------------

function write_stu_trans($type, $trans_no, $stu_id, $date_, $due_date, $reference, $supp_reference, $amount, $amount_tax, $discount, $err_msg = "", $rate = 0, $included = 0) {
    $Fees = getStudentFee();
    $total_paid = get_total_paid();
    $fee_arr = json_decode($Fees, true);
    $term_no = getTermInfo($fee_arr[0]['term_id']);

    $new = $trans_no == 0;
    $date = date2sql($date_);



    if ($new)
        $trans_no = get_next_trans_no($type);

    //display_error($term_no);exit;
    $sql = "INSERT INTO " . TB_PREF . "stu_trans (trans_no, type, stu_id, trans_date, due_date,
		 ov_amount) ";
    $sql .= "VALUES (" . db_escape($trans_no) . ", " . db_escape($type) . ", " . db_escape($stu_id) . ", '$date','" . $fee_arr[0]['t' . $term_no . '_date'] . "', " . db_escape($amount) . ")";
    if ($err_msg == "")
        $err_msg = "Cannot insert a supplier transaction record";

    db_query($sql, $err_msg);
    add_audit_trail($type, $trans_no, $date_);

    return $trans_no;
}

function getTermInfo($term_id) {
    set_global_connection(2);
    $sql = "SELECT terms from erp_fee_structure_term_items where terms_id = $term_id";

    $result = db_query($sql, $err_msg);
    set_global_connection(0);
    $row = db_fetch_row($result);
    return $row[0];
}

//-------------------------------------------------------------------------------------------------------------

function get_supp_trans($trans_no, $trans_type = -1) {

    $sql = "SELECT " . TB_PREF . "supp_trans.*, (" . TB_PREF . "supp_trans.ov_amount+" . TB_PREF . "supp_trans.ov_gst+" . TB_PREF . "supp_trans.ov_discount) AS Total,
		" . TB_PREF . "suppliers.supp_name AS supplier_name, " . TB_PREF . "suppliers.curr_code AS curr_code ";

    if ($trans_type == ST_SUPPAYMENT || $trans_type == ST_BANKPAYMENT) {
        // it's a payment so also get the bank account
        $sql .= ", " . TB_PREF . "bank_accounts.bank_name, " . TB_PREF . "bank_accounts.bank_account_name, " . TB_PREF . "bank_accounts.bank_curr_code,
			" . TB_PREF . "bank_accounts.account_type AS BankTransType, " . TB_PREF . "bank_trans.amount AS bank_amount,
			" . TB_PREF . "bank_trans.ref ";
    }

    $sql .= " FROM " . TB_PREF . "supp_trans, " . TB_PREF . "suppliers ";

    if ($trans_type == ST_SUPPAYMENT || $trans_type == ST_BANKPAYMENT) {
        // it's a payment so also get the bank account
        $sql .= ", " . TB_PREF . "bank_trans, " . TB_PREF . "bank_accounts";
    }

    $sql .= " WHERE " . TB_PREF . "supp_trans.trans_no=" . db_escape($trans_no) . "
		AND " . TB_PREF . "supp_trans.supplier_id=" . TB_PREF . "suppliers.supplier_id";

    if ($trans_type > 0)
        $sql .= " AND " . TB_PREF . "supp_trans.type=" . db_escape($trans_type);

    if ($trans_type == ST_SUPPAYMENT || $trans_type == ST_BANKPAYMENT) {
        // it's a payment so also get the bank account
        $sql .= " AND " . TB_PREF . "bank_trans.trans_no =" . db_escape($trans_no) . "
			AND " . TB_PREF . "bank_trans.type=" . db_escape($trans_type) . "
			AND " . TB_PREF . "bank_accounts.id=" . TB_PREF . "bank_trans.bank_act ";
    }
    $result = db_query($sql, "Cannot retreive a supplier transaction");

    if (db_num_rows($result) == 0) {
        // can't return nothing
        display_db_error("no supplier trans found for given params", $sql, true);
        exit;
    }

    if (db_num_rows($result) > 1) {
        // can't return multiple
        display_db_error("duplicate supplier transactions found for given params", $sql, true);
        exit;
    }

    return db_fetch($result);
}

//----------------------------------------------------------------------------------------

function exists_supp_trans($type, $type_no) {
    if ($type == ST_SUPPRECEIVE)
        return exists_grn($type_no);

    $sql = "SELECT trans_no FROM " . TB_PREF . "supp_trans WHERE type=" . db_escape($type) . "
		AND trans_no=" . db_escape($type_no);
    $result = db_query($sql, "Cannot retreive a supplier transaction");

    return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------

function void_supp_trans($type, $type_no) {
    $sql = "UPDATE " . TB_PREF . "supp_trans SET ov_amount=0, ov_discount=0, ov_gst=0,
		alloc=0 WHERE type=" . db_escape($type) . " AND trans_no=" . db_escape($type_no);

    db_query($sql, "could not void supp transactions for type=$type and trans_no=$type_no");
}

//----------------------------------------------------------------------------------------

function clear_supp_trans($type, $type_no) {
    $sql = "DELETE FROM " . TB_PREF . "supp_trans 
			WHERE type=" . db_escape($type) . " AND trans_no=" . db_escape($type_no);

    db_query($sql, "could not clear supp transactions for type=$type and trans_no=$type_no");
}

//----------------------------------------------------------------------------------------

function post_void_supp_trans($type, $type_no) {
    if ($type == ST_SUPPAYMENT) {
        void_supp_payment($type, $type_no);
        return true;
    }

    if ($type == ST_SUPPINVOICE || $type == ST_SUPPCREDIT) {
        void_supp_invoice($type, $type_no);
        return true;
    }

    if ($type == ST_SUPPRECEIVE) {
        return void_grn($type_no);
    }

    return false;
}

//----------------------------------------------------------------------------------------

function get_sql_for_supplier_inquiry($filter, $after_date, $to_date, $supplier_id = ALL_TEXT) {
    $date_after = date2sql($after_date);
    $date_to = date2sql($to_date);

    $sql = "SELECT trans.type, 
		trans.trans_no,
		CASE WHEN (trans.type=1) THEN CONCAT('BP-',trans.reference) WHEN (trans.type=20) THEN CONCAT('INV-',trans.reference) WHEN (trans.type=21) THEN CONCAT('SCN-',trans.reference) WHEN (trans.type=22) THEN CONCAT('SP-',trans.reference) END AS reference, 
		supplier.supp_name, 
		trans.supp_reference,
    	trans.tran_date, 
		trans.due_date,
		supplier.curr_code, 
    	(trans.ov_amount + trans.ov_gst  + trans.ov_discount) AS TotalAmount, 
		trans.alloc AS Allocated,
		((trans.type = " . ST_SUPPINVOICE . " OR trans.type = " . ST_SUPPCREDIT . ") AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue,
    	(ABS(trans.ov_amount + trans.ov_gst  + trans.ov_discount) - trans.alloc <= " . FLOAT_COMP_DELTA . ") AS Settled
    	FROM " . TB_PREF . "supp_trans as trans, " . TB_PREF . "suppliers as supplier
    	WHERE supplier.supplier_id = trans.supplier_id
     	AND trans.tran_date >= '$date_after'
    	AND trans.tran_date <= '$date_to'
		AND trans.ov_amount != 0"; // exclude voided transactions

    $sql2 = "SELECT " . ST_SUPPRECEIVE . " as type, 
		trans.id as trans_no,
		CONCAT('POD-',trans.reference) as reference, 
		supplier.supp_name, 
		po.requisition_no AS supp_reference,
    	delivery_date as tran_date, 
		'' as due_date,
		supplier.curr_code, 
    	'' AS TotalAmount,
		'' AS Allocated,
		0 as OverDue,
    	1 as Settled
    	FROM " . TB_PREF . "grn_batch as trans, " . TB_PREF . "suppliers as supplier, " . TB_PREF . "purch_orders as po
    	WHERE supplier.supplier_id = trans.supplier_id
    	AND trans.purch_order_no = po.order_no
     	AND trans.delivery_date >= '$date_after'
    	AND trans.delivery_date <= '$date_to'";

    if ($supplier_id != ALL_TEXT) {
        $sql .= " AND trans.supplier_id = " . db_escape($supplier_id);
        $sql2 .= " AND trans.supplier_id = " . db_escape($supplier_id);
    }
    if (($filter == '6')) {
        $sql = $sql2;
    } elseif (!isset($filter) || $filter == ALL_TEXT || $filter == '6') {
        $sql = "SELECT * FROM (($sql) UNION ($sql2)) as tr";
    }

    if (isset($filter) && $filter != ALL_TEXT) {

        if (($filter == '1')) {
            $sql .= " AND (trans.type = " . ST_SUPPINVOICE . " OR trans.type = " . ST_BANKDEPOSIT . ")";
        } elseif (($filter == '2')) {
            $sql .= " AND trans.type = " . ST_SUPPINVOICE . " ";
        } elseif ($filter == '3') {

            $sql .= " AND (trans.type = " . ST_SUPPAYMENT . " OR trans.type = " . ST_BANKPAYMENT . ") ";
        } elseif (($filter == '4') || ($filter == '5')) {
            $sql .= " AND trans.type = " . ST_SUPPCREDIT . "  ";
        }

        if (($filter == '2') || ($filter == '5')) {
            $today = date2sql(Today());
            $sql .= " AND trans.due_date < '$today' ";
        }
    }
    return $sql;
}

?>