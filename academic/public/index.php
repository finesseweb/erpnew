<?php

date_default_timezone_set('Asia/Kolkata');
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));
/*
 * finance module
 *
 */
defined('DAILY_BOOK_ID') || define('DAILY_BOOK_ID', 'DB-');
/*
 * Global Variables Defination Start
 */
defined('GRIR_PREFIX') || define('GRIR_PREFIX', 'GRIR-');
defined('PROCESS_ISSUE_PREFIX') || define('PROCESS_ISSUE_PREFIX', 'PIS-');
defined('DEPARTMENT_PREFIX') || define('DEPARTMENT_PREFIX', 'DEP-');
defined('SHORTCODE_PREFIX') || define('SHORTCODE_PREFIX', 'PDM');
defined('DATE_PREFIX') || define('DATE_PREFIX', 'd-m-Y');
/*
 * Production Params
 */
defined('PR_INITIAL_INSPECTION_ID') || define('PR_INITIAL_INSPECTION_ID', 'PRIID-');
defined('PR_PROCESS_ID') || define('PR_PROCESS_ID', 'PR');

/*
Client Address
*/
define('COMPANY_NAME', 'DEVELOPMENT MANAGEMENT INSTITUTE');
define('CLIENT_ADD', '<br />Udhyog Bhawan, 2nd Floor,, E Gandhi Maidan Rd, Bakarganj, Patna, Bihar 800004,Ph.No : 0612-2675283, E-Mail: support@dmi.ac.in');
define('CLIENT_ADDTWO', '');
define('PAN_NO', '<b>PAN No:</b> AABCH1309N');
define('CST_TIN', '<b>CST/TIN:</b> 06571816091');
define('CIN', '<b>CIN:</b> U25199HR2000PTC034098');

/*
 * Global Variables Definations for Purchase Start
 */
defined('PO_PREFIX') || define('PO_PREFIX', 'PO-');
defined('PI_PREFIX') || define('PI_PREFIX', 'PI-');
defined('PE_PREFIX') || define('PE_PREFIX', 'PE-');
defined('PQ_PREFIX') || define('PQ_PREFIX', 'PQ-');
defined('PRINV_PREFIX') || define('PRINV_PREFIX', 'PROIN-');
defined('PCL_PREFIX') || define('PCL_PREFIX', 'PCL-');
defined('PCI_PREFIX') || define('PCI_PREFIX', 'PCI-');
/*
 * Global Variables Definations for Purchase End
 */
define('CLAIM_NO', 'BT/'.date('y').'/');
 /*
 * Global Variables Definations for Invoice Start
 */
defined('INVAL_PREFIX') || define('INVAL_PREFIX', 'INV-');
/*
 * Global Variables Definations for sales Start
 */
defined('SALE_TYRE_ENTRY_ID') || define('SALE_TYRE_ENTRY_ID', 'T');
defined('SALE_TYRE_ID') || define('SALE_TYRE_ID', 'T');
defined('SALE_ORDER_FORM_ID') || define('SALE_ORDER_FORM_ID', 'OR');
defined('SALE_ORD_PREFIX') || define('SALE_ORD_PREFIX', 'OR-');
defined('DISPATCH_PREFIX') || define('DISPATCH_PREFIX', 'DIS');
defined('SALE_INVOICE') || define('SALE_INVOICE', 'SI');
defined('WB_PREFIX') || define('WB_PREFIX', 'WB-');
/*
 * Global Variables Definations for sales End
 */
defined('CID_PREFIX') || define('CID_PREFIX', 'CID-');
defined('EID_PREFIX') || define('EID_PREFIX', 'EID-');

defined('SPI_PREFIX') || define('SPI_PREFIX', 'PI-');
defined('SPL_PREFIX') || define('SPL_PREFIX', 'PCL-');
defined('SCI_PREFIX') || define('SCI_PREFIX', 'CI-');
defined('MPR_PREFIX') || define('MPR_PREFIX', 'MPR-');

/*
 * Global Variables Defination End
 */
 /*
 * Global Variables Definations for sales Start
 */
defined('SALE_ENQ_PREFIX')
    || define('SALE_ENQ_PREFIX', 'SE-');
defined('SALE_QUTO_PREFIX')
    || define('SALE_QUTO_PREFIX', 'SQ-');
defined('SALE_ORD_PREFIX')
    || define('SALE_ORD_PREFIX', 'SO-');

defined('SALE_DA_PREFIX')
    || define('SALE_DA_PREFIX', 'DA-');

defined('SALE_DA_CA_PREFIX')
    || define('SALE_DA_CA_PREFIX', 'DAC-');

/* finance */	
defined('PC_PREFIX') || define('PC_PREFIX', 'PC-');
defined('T_PREFIX') || define('T_PREFIX', 'T-');
defined('COA_PREFIX') || define('COA_PREFIX', 'COA-');
/*
 * Global Variables Definations for sales End
 */
// Define Currency symbol
defined('CURRENCY_SYMBOL') || define('CURRENCY_SYMBOL', 'Rs');
defined('CURRENCY_TH_SYMBOL') || define('CURRENCY_TH_SYMBOL', ' (Rs.)');
defined('CURRENCY_TH_SYMBOL_NOS') || define('CURRENCY_TH_SYMBOL_NOS', '(Rs)');
/*** Consumable category id **/
defined('CONSUM_CATEGORYID') || define('CONSUM_CATEGORYID', '3'); 
/*
 * Global Variables Definations for sales End
 */
define('PRO_PREFIX', 'PRO-');
define('PROD_PREFIX', 'PROD-');
/*
 * Global Variables Defination End
 */
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
        ->run();