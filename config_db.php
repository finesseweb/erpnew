<?php

/*Connection Information for the database
$def_coy - the default company that is pre-selected on login

'host' - the computer ip address or name where the database is. The default is 'localhost' assuming that the web server is also the sql server.

'port' - the computer port where the database is. The default is '3306'. Set empty for default.

'dbuser' - the user name under which the company database should be accessed.
  NB it is not secure to use root as the dbuser with no password - a user with appropriate privileges must be set up.

'dbpassword' - the password required for the dbuser to authorise the above database user.

'dbname' - the name of the database as defined in the RDMS being used. Typically RDMS allow many databases to be maintained under the same server.
'collation' - the character set used for the database.
'tbpref' - prefix on table names, or '' if not used. Always use non-empty prefixes if multiply company use the same database.
*/


$def_coy = 0;

$tb_pref_counter = 0;

$db_connections = array (
  0 => 
  array (
    'name' => 'Finesse',
    'host' => 'localhost',
    'dbuser' => 'root',
    'dbpassword' => '',
    'dbname' => 'main-erp',
    'tbpref' => 'fa_',
  ),
    1 => 
  array (
    'name' => 'Finesse',
    'host' => 'localhost',
    'dbuser' => 'root',
    'dbpassword' => '',
    'dbname' => 'dmi-maiweb',
    'tbpref' => 'fa_',
  ),
    2 => 
  array (
    'name' => 'Finesse',
    'host' => 'localhost',
    'dbuser' => 'root',
    'dbpassword' => '',
    'dbname' => 'erp-academic',
    'tbpref' => 'fa_',
  ),
);
$acad_setup = array(
        'Academic Masters',
        'Academic Participants',
        'Academic Faculty Portal',
        'Academic Reports',
        'Academic Attendance',
        'Academic Transactions',
        'Academic LMS',
        'Academic Seating Arrangment',
        "Academic Events",
        "Academic placement",
    );

$purchase_taxes_account = array('price'=>'Sundry Debtors',
                                'gst'=>'Input CGST',
                                'cst'=>'Input SGST',
                                'ist'=>'Input IGST');

$Calender_year = array(2017,2018,2019,2020,2021,2022,2023,2024,2025,2026,2027,2028,2029,2030);

$first_year_month = array('April','May','June','July','August','September','October','November','December');
$second_year_month = array('January','February','March');



$role_names = array('director' => 'director', 'dean' => 'dean');

/*
 * Leave approval status
 */
$leave_approval_status = array(1 => 'Waiting', 2 => 'Approved', 3 => 'Rejected',4 =>'Cancel Leave Request',5 => 'Approved Cancel Request');

/*
 * Single/Group Tour
 */
$single_group_tour = array('single' => 'Single', 'group' => 'Group');
/*
 * Setting static value
 */
$config_master_value = array(
    'yes_no_select_box' => array(0 => 'No', 1 => 'Yes'),
    'tour_request_for' => array(1 => 'Self', 2 => 'Guest'),
    'tour_accommodation_by' => array(1 => 'ORG', 2 => 'Hosting Org.', 3 => 'Self', 4 => 'N/A' ),
    'tour_advance_in' => array(1 => 'Cash', 2 => 'Account Transfer', 3 => 'Cheque'),
    'tour_single_group' => array(1 => 'Single', 2 => 'Group'),
    'tour_status' => array(1 => 'Waiting', 2 => 'Approved', 3 => 'Rejected'),
	'tour_bill_status' => array(1 => 'Waiting', 2 => 'Approved', 3 => 'Bill Under Process'),
	'status' => array(1 => 'Active', 2 => 'Deactive'),
	'book_fine' => array(1 => 'Day', 2 => 'Weekly', 3 => 'Monthly'),
	'return' => array(1 => 'Good', 2 => 'Damage'),
	'product_receive' => array(1 => 'Good', 2 => 'Scrap'),
    'tour_doc_type' => array('application/pdf','image/gif','image/jpg','image/jpeg','image/png','image/pjpeg')
);

/*
 * 
 * 
 * leave short has been creste on 
 * 11-04-2019
 * to use auto leave in user_attend.php in 
 * attendence model 
 * but any one can use it by using global 
 * key word
 * 
 */
//----------------start leave gloabal
$leave_type_short_code = array(
    
   'CL' => 1 ,
   'HCL' => 9,
   'ML' => 2,
   'VL' => 3,
   'EL' => 11,
   'PTL' => 5,
   'MTL' => 6,
   'SCL' => 4,
   'WOP' => 7,

    );


/*
 * table name 
 * from fa_kv_leave_master
 * 
 */
$leave_field_name = array(
    1=>'no_of_cls',
    3=> 'no_of_pls',
    2 => 'no_of_medical_ls',
    4 => '0',
    5=> '0',
    6 => '0',
    11 => 'no_of_el'
);
/*
 * leave increment 
 * after running scheduled task
 * 
 */

$leave_inc = array(
    1 => 1,
    2 => 1,
    3 => 1,
    4 => 0,
    5 => 0,
    6 => 0,
    11 => 1
);

//---------------------------------[END LEAVE GLOBAL]

/*
 * 
 * global defined for tax calculation
 * 
 * 
 */
//--------------start tax global
    $other_perks_percentage = 10;
    $deductable_hra_percent = 40;
    $deductable_hra_percent_rent = 10;
    $qualifying_amount_us80cce = 150000;
   // $rebate_us_87a = 350001;
    //$rebate_us_87a_exempt = 2500;
    //======[changed on 06-08-2019] starts here===========//
    $rebate_us_87a = 500001;
    $rebate_us_87a_exempt = 12500;
    //=======[Ends here] ================================//
    $education_cess_percent = 2;
    $higher_edu_cess_percent = 1;


    $zero_tax_amount = 250000;
    $first_tax_amount = 500000;
    $second_tax_amount = 1000000;
    
//====[Percentage of first second and so on to calculate tax]
    
    $first_tax_amount_percent = 5;
    $second_tax_amount_percent = 20;
    $after_second_tax_amount_percent = 30;

//-------------------end tax globals



/*
 * 
 * global defined for purchase 
 * Account code from tally
 */