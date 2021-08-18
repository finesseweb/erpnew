<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function isTallyOpen() {
    global $tallyServer;
    set_company_info();
    $server = $tallyServer;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);

    return !empty(curl_errno($ch)) ? false : true;
}

//=========[EXPORTING TALLY GROUPS]=============//
function kv_list_of_ledgers_tally_row($label, $name, $selected_id = null, $submit_on_change = false) {
    global $tallyServer;
    set_company_info();
    //$name = $_POST['nm'];
    //EXPORT XML TEMPLATES
    $data['server'] = $tallyServer;
    $data['content_type'] = 'text/xml';
    $data['content'] = xmlTemplatesExport('List of Accounts');
    $values = curlRequestAndResponse($data);
    $group_list = array_unique(list_of_groups($values));
    $key_list = array_unique(list_of_groups($values));
    echo "<tr><td class='label'>$label</td><td>";
    $options = array(
        'select_submit' => $submit_on_change
    );
    echo array_selector($name, $selected_id, array_combine($group_list, $key_list), $options);
    echo "</td></tr>\n";
}

//=========[EXPORTING TALLY LEDGERS]==============//
function kv_list_of_ledgers1_tally_row($label, $name, $selected_id = null, $submit_on_change = false) {
    global $tallyServer;
    set_company_info();
    //$name = $_POST['nm'];
    //EXPORT XML TEMPLATES
    $data['server'] = $tallyServer;
    $data['content_type'] = 'text/xml';
    $data['content'] = exportLedgers();
    $values = curlRequestAndResponse($data, 'ledgerFile.xml');
    $group_list = array_unique(list_of_groups($values, 'LEDGER'));
    $key_list = array_unique(list_of_groups($values, 'LEDGER'));
    echo "<tr><td class='label'>$label</td><td>";
    $options = array(
        'select_submit' => $submit_on_change
    );
    echo array_selector($name, $selected_id, array_combine($group_list, $key_list), $options);
    echo "</td></tr>\n";
}

function getTallyDayBook($id = 'Payroll', $fromDate = '', $toDate = '') {
    global $tallyServer;
    set_company_info();
    $data['server'] = $tallyServer;
    $data['content_type'] = 'text/xml';
    $data['content'] = xmlTemplatesExport1($id, $fromDate, $toDate);
    $values = curlRequestAndResponse1($data, 'DayBook.xml', '../..');
    return $values;
}

function get_tally_employees() {
    $values = getTallyDayBook();
    $tally_employe_info = array();
    $i = 0;
    $category = filterCostcenterCategory($values);
    $employee = filterCostcenterEmployees($values);
    $empl_info = filter_empl_xml_arr($xml_arr);

    $employee['category'] = $category;
    // $employee['info'] = $empl_info;
    return $employee;
    // print_r($payheads);
}

function get_tally_empl_info() {

    global $tallyServer;
    set_company_info();
    $data['server'] = $tallyServer;
    $data['content_type'] = 'text/xml';
    $data['content'] = xmlTemplatesCostCenter('Employees');
    $values = curlRequestAndResponse1($data, 'costcenter.xml', '../..');
    $tally_employe_info = array();
    $i = 0;
    return filter_empl_xml_arr($values);
    // return $values;
}

//=========[-- EMPL INFO --]========================//
function filter_empl_xml_arr($values) {
    $empl_info = array();
    $i = 0;
    $info_count = 50;
    foreach ($values as $key => $value) {
        if (strtolower($value['tag']) == 'costcentre') {
            if (!empty($value['attributes']['NAME'])) {
                $empl_info[$i]['empl_id'] = $value['attributes']['NAME'];
                $empl_info[$i]['address'] = $values[$key + 2]['value'];
                $empl_info[$i]['empl_name'] = $values[$key + 7]['value'];
                $empl_info[$i]['dob'] = $values[$key + 7 + 8]['value'];
                $empl_info[$i]['doj'] = !empty($values[$key + 7 + 8 + 2]['value']) ? date('Y-m-d', strtotime($values[$key + 7 + 8 + 2]['value'])) : '0000-00-00';
                $inc = 17;
                for ($j = 0; $j < $info_count; $j++) {
                    $inc += 2;
                    $empl_info[$i][strtolower($values[$key + $inc]['tag'])] = !empty($values[$key + $inc]['value']) ? $values[$key + $inc]['value'] : '';
                }
                $i++;
            }
        }
    }
    return $empl_info;
}

function filterCostcenterCategory($values) {
    $category = array();
    $i = 0;
    foreach ($values as $key => $value) {
        if (strtolower($value['tag']) == 'category') {
            $category[$i]['category'] = $value['value'];
            $i++;
        }
    }
    return $category;
}

//===============[FORMATING EMPLOYEES IN ARRAY] ==========//
function filterCostcenterEmployees($values) {
    $employees = array();
    $i = 0;
    $category = '';
    $date = '00-00-0000';
    foreach ($values as $key => $value) {
        if (strtolower($value['tag']) == 'date') {
            $date = date('d-m-Y', strtotime($value['value']));
        } else if (strtolower($value['tag']) == 'category') {
            $category = $value['value'];
        } else if (strtolower($value['tag']) == 'employeename') {
            $empl = $value['value'];
            $sal_pay = 0;
            $i++;
        } else if (strtolower($value['tag']) == 'payheadname') {
            $employees[$i][$category]['name'] = $empl;
            $employees[$i][$category]['date'] = $date;
            $sal_pay += $values[$key + 6]['value'];
            $employees[$i][$category]['Salary Payable'] = $sal_pay;
            $employees[$i][$category][$value['value']] = $values[$key + 6]['value'];
        }
    }
    return $employees;
}

function curlRequestAndResponse(array $data, $filename = 'account_list.xml', $root = "../..") {
    $server = $data['server'];
    $requestXML = $data['content'];
    $headers = array("Content-type: {$data['content_type']}", "Content-length: " . strlen($data['content']), "Connection: close");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_error($ch);
        echo "Something went wrong..... try later";
    } else {


        $file_name = writeFile($data, $root . "/tally/tally_core/{$filename}");
        $values = xml_parser($file_name);
        if (!$values) {
            display_error($data);
        } else {
            return $values;
        }

        curl_close($ch);
    }
}

function curlRequestAndResponse1(array $data, $filename = 'changeableFile.xml', $path_to_root = '../..') {

    $server = $data['server'];
    $requestXML = $data['content'];
    $headers = array("Content-type: {$data['content_type']}", "Content-length: " . strlen($requestXML), "Connection: close");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        print curl_error($ch);
        echo "Something went wrong..... try later";
    } else {


        $file_name = writeFile($data, $path_to_root . "/tally/tally_core/{$filename}");
        $values = xml_parser($file_name);
        if (!$values) {
            display_error($data);
        } else {
            return $values;
        }

        curl_close($ch);
    }
}

function list_of_groups($values, $tag = 'GROUP') {
    $new_arr1 = array();
    foreach ($values as $key => $new) {
        if ($new['tag'] == $tag) {
            if (array_key_exists('attributes', $new)) {
                if (array_key_exists('NAME', $new['attributes']))
                    $new_arr1[] = $new['attributes']['NAME'];
            }
        }
    }

    return $new_arr1;
}

function writeFile($data, $file_name) {
    $file = fopen($file_name, "w");
    fwrite($file, $data);
    fclose($file);
    return $file_name;
}

function xml_parser($filename) {


    $arr = explode('.', $filename);
    if (strtolower($arr[count($arr) - 1]) == 'xml') {
        $xmlfile = $filename;
        $xmlparser = xml_parser_create();
        $xmldata = file_get_contents($xmlfile);
        $xmldata = str_replace('&#4;', '', $xmldata);
        xml_parse_into_struct($xmlparser, $xmldata, $values);
        xml_parser_free($xmlparser);
        return $values;
    } else {
        return false;
    }
}

function xmlTemplatesCostCenter($id) {
    global $tallyCompanyName;
    set_company_info();
    $data = "<ENVELOPE>" .
            "<HEADER>" .
            "<TALLYREQUEST>Export Data</TALLYREQUEST>" .
            "</HEADER>" .
            "<BODY>" .
            "<EXPORTDATA>" .
            "<REQUESTDESC>" .
            "<REPORTNAME>List of Accounts</REPORTNAME>" .
            "<STATICVARIABLES>" .
            "<SVEXPORTFORMAT>\$\$SysName:XML</SVEXPORTFORMAT>" .
            "<SVCURRENTCOMPANY>'.$tallyCompanyName.'</SVCURRENTCOMPANY>" .
            "<ACCOUNTTYPE>" . $id . "</ACCOUNTTYPE>" .
            "<!--Other possible values for ACCOUNTTYPE tag are given below-->" .
            "<!--All Acctg. Masters, All Inventory Masters,All Statutory Masters-->" .
            "<!--Ledgers,Groups,Cost Categories,Cost Centres-->" .
            "<!--Units,Godowns,Stock Items,Stock Groups,Stock Categories-->" .
            "<!--Voucher types,Currencies,Employees,Budgets & Scenarios-->" .
            "</STATICVARIABLES>" .
            "</REQUESTDESC>" .
            "</EXPORTDATA>" .
            "</BODY>" .
            "</ENVELOPE>";

    return $data;
}

function xmlTemplatesExport($id) {

    global $tallyCompanyName;
    set_company_info();
    $requestXML = '<ENVELOPE>
	<HEADER>
		<VERSION>1</VERSION>
		<TALLYREQUEST>Export</TALLYREQUEST>
		<TYPE>Data</TYPE>
		<ID>' . $id . '</ID>
	</HEADER>
	<BODY>
		<DESC>
			<STATICVARIABLES>
				<EXPLODEFLAG>Yes</EXPLODEFLAG>
				<SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                                <SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
			</STATICVARIABLES>
		</DESC>
	</BODY>
</ENVELOPE>';

    return $requestXML;
}

function xmlTemplatesExport1($id = '', $fromDate = '', $toDate = '') {
    // $fromDate = $_POST['from_date'];
    // $toDate = $_POST['to_date'];
    global $tallyCompanyName;
    set_company_info();

    $fromDate = !empty($fromDate) ? date("Ymd", strtotime($fromDate)) : date('Ym') . '01';
    $toDate = !empty($toDate) ? date("Ymd", strtotime($toDate)) : date('Y') . date('m') + 1 . '01';

    $requestXML = '<ENVELOPE>
    <HEADER>
    <VERSION>1</VERSION>
    <TALLYREQUEST>EXPORT</TALLYREQUEST>
    <TYPE>DATA</TYPE>
    <ID>Voucher Register</ID>
    </HEADER>
    <BODY>
    <DESC>
    <STATICVARIABLES>
    <VoucherTypeName>' . $id . '</VoucherTypeName>
    <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
    <SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
    <SVFROMDATE TYPE="Date">' . $fromDate . '</SVFROMDATE>
    <SVTODATE TYPE="Date">' . $toDate . '</SVTODATE>
    </STATICVARIABLES>
    </DESC>
    </BODY>
    </ENVELOPE>';

    return $requestXML;
}

function addNew($file_name = 'changeableFile.xml', $ch = '') {
    global $tallyServer;
    set_company_info();
//display_error('helo');
    if ($ch == 1)
        $path = '../../tally/tally_core/';
    else
        $path = '../../tally/tally_core/';
    $xml_new = simplexml_load_file($path . $file_name);
    $requestXML = $xml_new->asXML();
    //$_POST['payhead'] = $requestXML;
    //display_error($requestXML);
    /* Actual code for importing goes here */
    $server = $tallyServer;
    $headers = array("Content-type: text/xml", "Content-length: " . strlen($requestXML), "Connection: close");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);
    $check['status'] = 0;
    $check['created'] = 0;
    $check['altered'] = 0;
    $check['newcheck'] = 0;
    if (curl_errno($ch)) {
        // print curl_error($ch);
        display_error("Something went wrong..... try later");
    } else {

        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $data, $values);
        xml_parser_free($xmlparser);
        foreach ($values as $key => $new) {
            if (strtolower($new['tag']) == 'status') {
                $check['status'] = $new['value'];
                $check['newcheck'] = $new['value'];
            }
            if (strtoupper($new['tag']) == 'ALTERED') {
                $check['status'] = $new['value'];
                $check['updated'] = $new['value'];
            }
            if (strtoupper($new['tag']) == 'CREATED') {
                $check['status'] = $new['value'];
                $check['added'] = $new['value'];
            }
        }
        $filename = writeFile($data, $path . "response.xml");
        if ($check['status'] > 0) {
            $stat = $check['newcheck'] > 0 ? 'status' . $check['status'] : '';
            display_notification($stat . ' ' . $check['added'] . ' created' . $check['updated'] . ' Altered' . 'Transaction Successfull');
        } else {
            foreach ($values as $key => $new) {
                if (strtolower($new['tag']) == 'lineerror') {
                    $check = $new['value'];
                    break;
                }
            }
            display_error($check);
        }
        curl_close($ch);
    }
}

function addGroup($group_name, $parent_name = '', $is_true = 0) {


    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
</STATICVARIABLES>
</DESC>
<DATA>
<TALLYMESSAGE>
<GROUP NAME="' . $group_name . '" ACTION="Create">
<NAME>' . $group_name . '</NAME>';
    if ($is_true > 0)
        $requestXML .= "<PARENT>$parent_name</PARENT>";
    $requestXML .= '</GROUP>
</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';
    return $filename = writeFile($requestXML, "../../tally/tally_core/groupFile.xml");
}

function addLedger($parent_name, $ledger_name = '', $openingBalance = 0) {

    if (!empty($parent_name)) {
        $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
</STATICVARIABLES>
</DESC>
<DATA>
<TALLYMESSAGE>
<LEDGER NAME="' . $ledger_name . '" Action="Create">
<NAME>' . $ledger_name . '</NAME>
<PARENT>' . $parent_name . '</PARENT>
<OPENINGBALANCE>' . $openingBalance . '</OPENINGBALANCE>
</LEDGER>
</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';
        return $filename = writeFile($requestXML, "../../tally/tally_core/createLFile.xml");
    } else
        display_error('Tally Groups should not be empty !');
}

function importHeader() {
    global $tallyCompanyName;
    set_company_info();
    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
<SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
</STATICVARIABLES>
</DESC>
<DATA>
      <TALLYMESSAGE>';

    return $requestXML;
}

function addLedgerBody($parent_name, $ledger_name = '', $openingBalance = 0) {

    return '<LEDGER NAME="' . $ledger_name . '" Action="Create">
        <NAME>' . $ledger_name . '</NAME>
        <PARENT>' . $parent_name . '</PARENT>
        <OPENINGBALANCE>' . $openingBalance . '</OPENINGBALANCE>
        </LEDGER>';
}

function importFooter() {

    $requestXML = '</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';

    return $requestXML;
}

function deleteGroupL($ledger) {
    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
</STATICVARIABLES>
</DESC>
<DATA>
<TALLYMESSAGE>
<LEDGER NAME="' . $ledger . '" Action="Delete">
<NAME>' . $ledger . '</NAME>
</LEDGER>
</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';
    return $filename = writeFile($requestXML, "../../tally/tally_core/deleteLFile.xml");
}

//==============deleteGroup=========///
function deleteGroup($group_name) {
    global $tallyCompanyName;
    set_company_info();

    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
<SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
</STATICVARIABLES>
</DESC>
<DATA>
<TALLYMESSAGE>
<GROUP NAME="' . $group_name . '" ACTION="Delete">
<NAME>' . $group_name . '</NAME>';
    $requestXML .= '</GROUP>
</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';
    return $filename = writeFile($requestXML, "../../tally/tally_core/deleteFile.xml");
}

//========[--Export Ledger--]==================//
function exportLedgers($id = 'Remote Ledger Coll') {
    global $tallyCompanyName;
    set_company_info();
    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>EXPORT</TALLYREQUEST>
<TYPE>COLLECTION</TYPE>
<ID>' . $id . '</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
<SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
</STATICVARIABLES>
<TDL>
<TDLMESSAGE>
<COLLECTION NAME="Remote Ledger Coll"
ISINITIALIZE="Yes">
<TYPE>Ledger</TYPE>
<NATIVEMETHOD>Name</NATIVEMETHOD>
<NATIVEMETHOD>OpeningBalance
</NATIVEMETHOD>
</COLLECTION>
</TDLMESSAGE>
</TDL>
</DESC>
</BODY>
</ENVELOPE>';

    return $requestXML;
}

///=========[ADD VAOUCHERS]==================////


function tallyHeader() {
    global $tallyCompanyName;
    set_company_info();
    return '<ENVELOPE>
 <HEADER>
  <TALLYREQUEST>Import Data</TALLYREQUEST>
 </HEADER>
 <BODY>
  <IMPORTDATA>
   <REQUESTDESC>
    <REPORTNAME>Vouchers</REPORTNAME>
    <STATICVARIABLES>
     <SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
    </STATICVARIABLES>
   </REQUESTDESC>
   <REQUESTDATA>
    <TALLYMESSAGE xmlns:UDF="TallyUDF">';
}

//======================== [START PAYROLL VOUCHER] ================================//
function addVoucherHeader($type, $vouchernumber, $fis_year, $transaction_type, $i = 0, $j = 0, $description, $amount, $empl_id, $month, $gui = '') {
    global $tallyCompanyName;
    set_company_info();
    $dateObj = DateTime::createFromFormat('!m', $month);
    $monthName = $dateObj->format('F');

    return '<VOUCHER  VCHTYPE="Payroll" ACTION="Create" OBJVIEW="PaySlip Voucher View">
      <DATE>' . $fis_year . $month . '01' . '</DATE>
      <NARRATION>Salary For the month of ' . $monthName . '-' . $fis_year . '</NARRATION>
      <VOUCHERTYPENAME>' . $type . '</VOUCHERTYPENAME>
      <VOUCHERNUMBER>' . $vouchernumber . '</VOUCHERNUMBER>
      <PARTYLEDGERNAME>Salary Payable</PARTYLEDGERNAME>
      <CSTFORMISSUETYPE/>
      <CSTFORMRECVTYPE/>
      <FBTPAYMENTTYPE>Default</FBTPAYMENTTYPE>
      <PERSISTEDVIEW>PaySlip Voucher View</PERSISTEDVIEW>
      <VCHGSTCLASS/>
      <DIFFACTUALQTY>No</DIFFACTUALQTY>
      <ISMSTFROMSYNC>No</ISMSTFROMSYNC>
      <ASORIGINAL>No</ASORIGINAL>
      <AUDITED>No</AUDITED>
      <FORJOBCOSTING>No</FORJOBCOSTING>
      <ISOPTIONAL>No</ISOPTIONAL>
      <EFFECTIVEDATE>20190201</EFFECTIVEDATE>
      <USEFOREXCISE>No</USEFOREXCISE>
      <ISFORJOBWORKIN>No</ISFORJOBWORKIN>
      <ALLOWCONSUMPTION>No</ALLOWCONSUMPTION>
      <USEFORINTEREST>No</USEFORINTEREST>
      <USEFORGAINLOSS>No</USEFORGAINLOSS>
      <USEFORGODOWNTRANSFER>No</USEFORGODOWNTRANSFER>
      <USEFORCOMPOUND>No</USEFORCOMPOUND>
      <USEFORSERVICETAX>No</USEFORSERVICETAX>
      <ISEXCISEVOUCHER>No</ISEXCISEVOUCHER>
      <EXCISETAXOVERRIDE>No</EXCISETAXOVERRIDE>
      <USEFORTAXUNITTRANSFER>No</USEFORTAXUNITTRANSFER>
      <EXCISEOPENING>No</EXCISEOPENING>
      <USEFORFINALPRODUCTION>No</USEFORFINALPRODUCTION>
      <ISTDSOVERRIDDEN>No</ISTDSOVERRIDDEN>
      <ISTCSOVERRIDDEN>No</ISTCSOVERRIDDEN>
      <ISTDSTCSCASHVCH>No</ISTDSTCSCASHVCH>
      <INCLUDEADVPYMTVCH>No</INCLUDEADVPYMTVCH>
      <ISSUBWORKSCONTRACT>No</ISSUBWORKSCONTRACT>
      <ISVATOVERRIDDEN>No</ISVATOVERRIDDEN>
      <IGNOREORIGVCHDATE>No</IGNOREORIGVCHDATE>
      <ISSERVICETAXOVERRIDDEN>No</ISSERVICETAXOVERRIDDEN>
      <ISISDVOUCHER>No</ISISDVOUCHER>
      <ISEXCISEOVERRIDDEN>No</ISEXCISEOVERRIDDEN>
      <ISEXCISESUPPLYVCH>No</ISEXCISESUPPLYVCH>
      <ISGSTOVERRIDDEN>No</ISGSTOVERRIDDEN>
      <GSTNOTEXPORTED>No</GSTNOTEXPORTED>
      <ISVATPRINCIPALACCOUNT>No</ISVATPRINCIPALACCOUNT>
      <ISBOENOTAPPLICABLE>No</ISBOENOTAPPLICABLE>
      <ISSHIPPINGWITHINSTATE>No</ISSHIPPINGWITHINSTATE>
      <ISOVERSEASTOURISTTRANS>No</ISOVERSEASTOURISTTRANS>
      <ISCANCELLED>No</ISCANCELLED>
      <HASCASHFLOW>No</HASCASHFLOW>
      <ISPOSTDATED>No</ISPOSTDATED>
      <USETRACKINGNUMBER>No</USETRACKINGNUMBER>
      <ISINVOICE>No</ISINVOICE>
      <MFGJOURNAL>No</MFGJOURNAL>
      <HASDISCOUNTS>No</HASDISCOUNTS>
      <ASPAYSLIP>Yes</ASPAYSLIP>
      <ISCOSTCENTRE>No</ISCOSTCENTRE>
      <ISSTXNONREALIZEDVCH>No</ISSTXNONREALIZEDVCH>
      <ISEXCISEMANUFACTURERON>No</ISEXCISEMANUFACTURERON>
      <ISBLANKCHEQUE>No</ISBLANKCHEQUE>
      <ISVOID>No</ISVOID>
      <ISONHOLD>No</ISONHOLD>
      <ORDERLINESTATUS>No</ORDERLINESTATUS>
      <VATISAGNSTCANCSALES>No</VATISAGNSTCANCSALES>
      <VATISPURCEXEMPTED>No</VATISPURCEXEMPTED>
      <ISVATRESTAXINVOICE>No</ISVATRESTAXINVOICE>
      <VATISASSESABLECALCVCH>No</VATISASSESABLECALCVCH>
      <ISVATDUTYPAID>No</ISVATDUTYPAID>
      <ISDELIVERYSAMEASCONSIGNEE>No</ISDELIVERYSAMEASCONSIGNEE>
      <ISDISPATCHSAMEASCONSIGNOR>No</ISDISPATCHSAMEASCONSIGNOR>
      <ISDELETED>No</ISDELETED>
      <CHANGEVCHMODE>No</CHANGEVCHMODE>
      <ALTERID> 39969</ALTERID>
      <MASTERID> 19065</MASTERID>
      <VOUCHERKEY>185624191565832</VOUCHERKEY>';
}

function voucherCategory($category) {
    $xmlReq = '<CATEGORYENTRY.LIST>
       <CATEGORY>' . $category . '</CATEGORY>';
    return $xmlReq;
}

function addVoucherTopBody($type, $vouchernumber, $dateofpay, $transaction_type, $i = 0, $j = 0, $description, $amount, $empl_id, $category, $order = 1) {
    /* if($order == 1){
      $xmlReq =  '<CATEGORYENTRY.LIST>
      <CATEGORY>'.$category.'</CATEGORY>';
      } */
    $xmlReq .= '<EMPLOYEEENTRIES.LIST>
           <EMPLOYEENAME>' . $empl_id . '</EMPLOYEENAME>
        <EMPLOYEESORTORDER>' . $order . '</EMPLOYEESORTORDER>
        <AMOUNT>' . $amount . '</AMOUNT>';
    return $xmlReq;
}

function addVoucherCreditBody($type, $vouchernumber, $dateofpay, $transaction_type, $i = 0, $j = 0, $description, $amount, $empl_id, $order = 1) {
    return '<PAYHEADALLOCATIONS.LIST>
         <PAYHEADNAME>' . $transaction_type . '</PAYHEADNAME>
         <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
         <PAYHEADSORTORDER> ' . $order . '</PAYHEADSORTORDER>
         <AMOUNT>-' . $amount . '</AMOUNT>
        </PAYHEADALLOCATIONS.LIST>';
}

function addVoucherDebitBody($type, $vouchernumber, $dateofpay, $transaction_type, $i = 0, $j = 0, $description, $amount, $empl_id, $order = 1) {
    return '<PAYHEADALLOCATIONS.LIST>
         <PAYHEADNAME>' . $transaction_type . '</PAYHEADNAME>
         <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
         <PAYHEADSORTORDER> ' . $order . '</PAYHEADSORTORDER>
         <AMOUNT>' . abs($amount) . '</AMOUNT>
        </PAYHEADALLOCATIONS.LIST>';
}

function addVoucherTopBodyEnd() {

    return '</EMPLOYEEENTRIES.LIST>';
}

function endCategory() {
    return "</CATEGORYENTRY.LIST>";
}

function addVoucherFooter() {

    return '<PAYROLLMODEOFPAYMENT.LIST>      </PAYROLLMODEOFPAYMENT.LIST>
      <ATTDRECORDS.LIST>      </ATTDRECORDS.LIST>
      <TEMPGSTRATEDETAILS.LIST>      </TEMPGSTRATEDETAILS.LIST>
     </VOUCHER>';
}

//===============================[END VOUCHER XML REQUESTS]================


function tallyFooter() {
    global $tallyCompanyName, $tallyStatename;
    set_company_info();
    return '</TALLYMESSAGE>
    <TALLYMESSAGE xmlns:UDF="TallyUDF">
     <COMPANY>
      <REMOTECMPINFO.LIST MERGE="Yes">
       <NAME>46bb84ce-0599-4c46-9798-fd02a9127f71</NAME>
       <REMOTECMPNAME>' . $tallyCompanyName . '</REMOTECMPNAME>
       <REMOTECMPSTATE>' . $tallyStatename . '</REMOTECMPSTATE>
      </REMOTECMPINFO.LIST>
     </COMPANY>
    </TALLYMESSAGE>
   </REQUESTDATA>
  </IMPORTDATA>
 </BODY>
</ENVELOPE>';
}

//================EMployee added and modified into payroll info 
function payRollHeader() {
    global $tallyCompanyName;
    set_company_info();
    return "<ENVELOPE>
 <HEADER>
  <TALLYREQUEST>Import Data</TALLYREQUEST>
 </HEADER>
 <BODY>
  <IMPORTDATA>
   <REQUESTDESC>
    <REPORTNAME>All Masters</REPORTNAME>
    <STATICVARIABLES>
     <SVCURRENTCOMPANY>$tallyCompanyName</SVCURRENTCOMPANY>
    </STATICVARIABLES>
   </REQUESTDESC>
   <REQUESTDATA>" . '<TALLYMESSAGE xmlns:UDF="TallyUDF">';
}

function costCategoryBody($category1, $category2) {


    return '
     <COSTCATEGORY NAME="' . $category1 . '" RESERVEDNAME="">
      <GUID>15d429f0-1c64-4832-a94b-8416a38d8cd3-00000180</GUID>
      <ISUPDATINGTARGETID>No</ISUPDATINGTARGETID>
      <ASORIGINAL>Yes</ASORIGINAL>
      <AFFECTSSTOCK>No</AFFECTSSTOCK>
      <ALLOCATEREVENUE>Yes</ALLOCATEREVENUE>
      <ALLOCATENONREVENUE>No</ALLOCATENONREVENUE>
      <ALTERID> 1159</ALTERID>
      <LANGUAGENAME.LIST>
       <NAME.LIST TYPE="String">
        <NAME>' . $category2 . '</NAME>
       </NAME.LIST>
       <LANGUAGEID> 1033</LANGUAGEID>
      </LANGUAGENAME.LIST>
     </COSTCATEGORY>';
}

function costCenter($category_name = '', $costcentername, $costcentername1) {

    $resultXML = '<COSTCENTRE NAME="' . $costcentername . '" RESERVEDNAME="">
      <GUID>15d429f0-1c64-4832-a94b-8416a38d8cd3-00000181</GUID>
      <PARENT/>';
    if (!empty($category_name))
        $resultXML .= '<CATEGORY>' . $category_name . '</CATEGORY>';
    $resultXML .= '<REVENUELEDFOROPBAL>No</REVENUELEDFOROPBAL>
      <ISUPDATINGTARGETID>No</ISUPDATINGTARGETID>
      <ASORIGINAL>Yes</ASORIGINAL>
      <AFFECTSSTOCK>No</AFFECTSSTOCK>
      <FORPAYROLL>Yes</FORPAYROLL>
      <FORJOBCOSTING>No</FORJOBCOSTING>
      <ISEMPLOYEEGROUP>Yes</ISEMPLOYEEGROUP>
      <SORTPOSITION> 1000</SORTPOSITION>
      <ALTERID> 1160</ALTERID>
      <LANGUAGENAME.LIST>
       <NAME.LIST TYPE="String">
        <NAME>' . $costcentername1 . '</NAME>
       </NAME.LIST>
       <LANGUAGEID> 1033</LANGUAGEID>
      </LANGUAGENAME.LIST>
      <PAYMENTDETAILS.LIST>      </PAYMENTDETAILS.LIST>
      <EMPLOYEEPERIOD.LIST>      </EMPLOYEEPERIOD.LIST>
      <ITDEDUCTIONDETAILS.LIST>      </ITDEDUCTIONDETAILS.LIST>
      <ITDECLARATIONDETAILS.LIST>      </ITDECLARATIONDETAILS.LIST>
      <ITOVERRIDEDETAILS.LIST>      </ITOVERRIDEDETAILS.LIST>
      <ITPREVEMPLYRDETAILS.LIST>      </ITPREVEMPLYRDETAILS.LIST>
      <ITOPENINGBALDETAILS.LIST>      </ITOPENINGBALDETAILS.LIST>
     </COSTCENTRE>';

    return $resultXML;
}

function employeesBody(array $empl_info, $empl_id1, $empl_id2) {

    return '
     <COSTCENTRE NAME="' . $empl_id1 . '" RESERVEDNAME="">
      <ADDRESS.LIST TYPE="String">
       <ADDRESS>Renter</ADDRESS>
      </ADDRESS.LIST>
      <MAILINGNAME.LIST TYPE="String">
       <MAILINGNAME>' . $empl_info['empl_name'] . '</MAILINGNAME>
      </MAILINGNAME.LIST>
      <DATEOFJOIN>' . date('Ymd', strtotime($empl_info['joining'])) . '</DATEOFJOIN>
      <PFJOININGDATE>' . date('Ymd', strtotime($empl_info['joining'])) . '</PFJOININGDATE>
      <GUID>15d429f0-1c64-4832-a94b-8416a38d8cd3-00000184</GUID>
      <CATEGORY>' . $empl_info['department'] . '</CATEGORY>
      <IFSCODE>' . $empl_info['ifsc_code'] . '</IFSCODE>
      <BANKDETAILS>' . $empl_info['bank_name'] . '</BANKDETAILS>
      <FUNCTION></FUNCTION>
      <LOCATION>' . $empl_info['city'] . '</LOCATION>
      <DESIGNATION>' . $empl_info['desig'] . '</DESIGNATION>
      <PFACCOUNTNUMBER>' . $empl_info['pf_number'] . '</PFACCOUNTNUMBER>
      <PANNUMBER>' . $empl_info['pan_no'] . '</PANNUMBER>
      <ESINUMBER>' . $empl_info['esi_no'] . '</ESINUMBER>
      <GENDER>' . $empl_info['gender'] . '</GENDER>
      <BLOODGROUP></BLOODGROUP>
      <FATHERNAME></FATHERNAME>
      <CONTACTNUMBERS>' . $empl_info['mobile_no'] . '</CONTACTNUMBERS>
      <EMAILID>' . $empl_info['email'] . '</EMAILID>
      <BANKACCOUNTNUMBER>' . $empl_info['acc_no'] . '</BANKACCOUNTNUMBER>
      <BANKBRANCH>' . $empl_info['city'] . '</BANKBRANCH>
      <SPOUSENAME>' . $empl_info['marital_status'] . '</SPOUSENAME>
      <FPFACCOUNTNUMBER>' . $empl_info['pf_number'] . '</FPFACCOUNTNUMBER>
      <EMPDISPLAYNAME>' . $empl_id2 . '</EMPDISPLAYNAME>
      <PRACCOUNTNUMBER>' . $empl_info['pran_no'] . '</PRACCOUNTNUMBER>
      <AADHARNUMBER>' . $empl_info['aadhaar_no'] . '</AADHARNUMBER>
      <UANNUMBER></UANNUMBER>
      <REVENUELEDFOROPBAL>No</REVENUELEDFOROPBAL>
      <ISUPDATINGTARGETID>No</ISUPDATINGTARGETID>
      <ASORIGINAL>Yes</ASORIGINAL>
      <AFFECTSSTOCK>No</AFFECTSSTOCK>
      <FORPAYROLL>Yes</FORPAYROLL>
      <FORJOBCOSTING>No</FORJOBCOSTING>
      <ISEMPLOYEEGROUP>No</ISEMPLOYEEGROUP>
      <SORTPOSITION> 1000</SORTPOSITION>
      <ALTERID> 1169</ALTERID>
      <LANGUAGENAME.LIST>
       <NAME.LIST TYPE="String">
        <NAME>' . $empl_id2 . '</NAME>
     
       </NAME.LIST>
       <LANGUAGEID> 1033</LANGUAGEID>
      </LANGUAGENAME.LIST>
      <PAYMENTDETAILS.LIST>
       <PAYMENTFAVOURING>' . $empl_info['empl_name'] . '</PAYMENTFAVOURING>
       <TRANSACTIONNAME>Primary</TRANSACTIONNAME>
       <SETASDEFAULT>No</SETASDEFAULT>
       <DEFAULTTRANSACTIONTYPE>Others</DEFAULTTRANSACTIONTYPE>
       <BENEFICIARYCODEDETAILS.LIST>       </BENEFICIARYCODEDETAILS.LIST>
      </PAYMENTDETAILS.LIST>
      <EMPLOYEEPERIOD.LIST>      </EMPLOYEEPERIOD.LIST>
      <ITDEDUCTIONDETAILS.LIST>      </ITDEDUCTIONDETAILS.LIST>
      <ITDECLARATIONDETAILS.LIST>      </ITDECLARATIONDETAILS.LIST>
      <ITOVERRIDEDETAILS.LIST>      </ITOVERRIDEDETAILS.LIST>
      <ITPREVEMPLYRDETAILS.LIST>      </ITPREVEMPLYRDETAILS.LIST>
      <ITOPENINGBALDETAILS.LIST>      </ITOPENINGBALDETAILS.LIST>
     </COSTCENTRE>';
}

function payRollFooter() {
    global $tallyCompanyName, $tallyStatename;
    set_company_info();
    return '<COMPANY>
      <REMOTECMPINFO.LIST MERGE="Yes">
       <NAME>15d429f0-1c64-4832-a94b-8416a38d8cd3</NAME>
       <REMOTECMPNAME>' . $tallyCompanyName . '</REMOTECMPNAME>
       <REMOTECMPSTATE>' . $tallyStatename . '</REMOTECMPSTATE>
      </REMOTECMPINFO.LIST>
     </COMPANY>
    </TALLYMESSAGE>
   </REQUESTDATA>
  </IMPORTDATA>
 </BODY>
</ENVELOPE>';
}

function payHeader() {
    global $tallyCompanyName;
    set_company_info();
    $requestXML = '<ENVELOPE>
<HEADER>
<VERSION>1</VERSION>
<TALLYREQUEST>Import</TALLYREQUEST>
<TYPE>Data</TYPE>
<ID>All Masters</ID>
</HEADER>
<BODY>
<DESC>
<STATICVARIABLES>
<IMPORTDUPS>@@DUPCOMBINE</IMPORTDUPS>
<SVCURRENTCOMPANY>' . $tallyCompanyName . '</SVCURRENTCOMPANY>
</STATICVARIABLES>
</DESC>
<DATA>
      <TALLYMESSAGE>';

    return $requestXML;
}

function payBody($parent_name, $ledgen_name, $type) {
    if ($type == 'Deductions')
        $type = 'Deductions From Employees';
    else
        $type = 'Earnings for Employees';

    return '<LEDGER NAME="' . $ledgen_name . '" Action="Create">
      <OLDAUDITENTRYIDS.LIST TYPE="Number">
       <OLDAUDITENTRYIDS>-1</OLDAUDITENTRYIDS>
      </OLDAUDITENTRYIDS.LIST>
      <GUID>38be79e0-7a62-477b-902a-a833952bcc3b-00000111</GUID>
      <PARENT>' . $parent_name . '</PARENT>
      <TAXCLASSIFICATIONNAME/>
      <TAXTYPE>Others</TAXTYPE>
      <GSTTYPE/>
      <APPROPRIATEFOR/>
      <PAYTYPE>' . $type . '</PAYTYPE>
      <PAYSLIPNAME>' . $ledgen_name . '</PAYSLIPNAME>
      <CALCULATIONTYPE>As User Defined Value</CALCULATIONTYPE>
      <PAYSTATTYPE>Fixed</PAYSTATTYPE>
      <EXCISELEDGERCLASSIFICATION/>
      <EXCISEDUTYTYPE/>
      <EXCISENATUREOFPURCHASE/>
      <LEDGERFBTCATEGORY/>
      <ISCOSTCENTRESON>Yes</ISCOSTCENTRESON>
      <ASORIGINAL>Yes</ASORIGINAL>
      <FORPAYROLL>Yes</FORPAYROLL>
      <ISCHEQUEPRINTINGENABLED>Yes</ISCHEQUEPRINTINGENABLED>
      <SHOWINPAYSLIP>Yes</SHOWINPAYSLIP>
      <AUDITED>No</AUDITED>
      <SORTPOSITION> 1000</SORTPOSITION>
      <ALTERID> 368</ALTERID>
      <SERVICETAXDETAILS.LIST>      </SERVICETAXDETAILS.LIST>
      <LBTREGNDETAILS.LIST>      </LBTREGNDETAILS.LIST>
      <VATDETAILS.LIST>      </VATDETAILS.LIST>
      <SALESTAXCESSDETAILS.LIST>      </SALESTAXCESSDETAILS.LIST>
      <GSTDETAILS.LIST>      </GSTDETAILS.LIST>
      <LANGUAGENAME.LIST>
       <NAME.LIST TYPE="String">
        <NAME>' . $ledgen_name . '</NAME>
       </NAME.LIST>
       <LANGUAGEID> 1033</LANGUAGEID>
      </LANGUAGENAME.LIST>
     </LEDGER>';
}

function payFooter() {
    $requestXML = '</TALLYMESSAGE>
</DATA>
</BODY>
</ENVELOPE>';
    return $requestXML;
}

function purchasedJuornalVoucherHeader($date, $naration = 'null', $inc = 1, $voucherType = '') {




    return "<VOUCHER>
    <DATE>$date</DATE>
      <NARRATION>$naration</NARRATION>
      <VOUCHERTYPENAME>$voucherType</VOUCHERTYPENAME>
      <VOUCHERNUMBER>$inc</VOUCHERNUMBER>
      <CSTFORMISSUETYPE/>
      <CSTFORMRECVTYPE/>
      <FBTPAYMENTTYPE>Default</FBTPAYMENTTYPE>
      <PERSISTEDVIEW>Accounting Voucher View</PERSISTEDVIEW>
      <VCHGSTCLASS/>
      <VOUCHERTYPEORIGNAME>$voucherType</VOUCHERTYPEORIGNAME>";
}

function purchasedJuornalPartyLedgerVoucher($ledgername, $amount) {


    return "<ALLLEDGERENTRIES.LIST>
       <LEDGERNAME>$ledgername</LEDGERNAME>
       <GSTCLASS/>
       <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
       <LEDGERFROMITEM>No</LEDGERFROMITEM>
       <REMOVEZEROENTRIES>No</REMOVEZEROENTRIES>
       <ISPARTYLEDGER>No</ISPARTYLEDGER>
       <ISLASTDEEMEDPOSITIVE>Yes</ISLASTDEEMEDPOSITIVE>
       <ISCAPVATTAXALTERED>No</ISCAPVATTAXALTERED>
       <ISCAPVATNOTCLAIMED>No</ISCAPVATNOTCLAIMED>
       <AMOUNT>$amount</AMOUNT>
      </ALLLEDGERENTRIES.LIST>";
}

function purchasedJuornalVoucher($ledgername, $amount) {

    return "<ALLLEDGERENTRIES.LIST>
         <OLDAUDITENTRYIDS.LIST TYPE='Number'>
        <OLDAUDITENTRYIDS>-1</OLDAUDITENTRYIDS>
       </OLDAUDITENTRYIDS.LIST>
       <LEDGERNAME>$ledgername</LEDGERNAME>
       <GSTCLASS/>
       <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
       <LEDGERFROMITEM>No</LEDGERFROMITEM>
       <REMOVEZEROENTRIES>No</REMOVEZEROENTRIES>
       <ISPARTYLEDGER>No</ISPARTYLEDGER>
       <ISLASTDEEMEDPOSITIVE>No</ISLASTDEEMEDPOSITIVE>
       <ISCAPVATTAXALTERED>No</ISCAPVATTAXALTERED>
       <ISCAPVATNOTCLAIMED>No</ISCAPVATNOTCLAIMED>
       <AMOUNT>$amount</AMOUNT>
      </ALLLEDGERENTRIES.LIST>";
}

function purchasedJuornalVoucherfooter() {

    return "</VOUCHER>";
}

//=====================EMPL-INFO-DB-QUERY================================//


function getEmplInfo($empl_id = '', $selectBody = 'employeesBody') {
    $sql = "SELECT * FROM " . TB_PREF . "kv_empl_info WHERE empl_id=" . db_escape($empl_id) . ' LIMIT 1';
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    $info = array();
    while ($row = db_fetch($result)) {
        $info['empl_id'] = $row['empl_id'];
        $info['empl_name'] = $row['empl_firstname'] . ' ' . $row['empl_middlename'] . ' ' . $row['empl_lastname'];
        $info['addr'] = $row['addr_line1'] . ' ' . $row['addr_line2'];
        $info['city'] = $row['empl_city'];
        $info['pin_code'] = $row['pincode'];
        $info['gender'] = $row['gender'] == 1 ? 'Male' : 'Female';
        $info['mobile_no'] = $row['mobile_phone'];
        $info['email'] = $row['email'];
        $info['pf_number'] = $row['pf_number'];
        $info['aadhaar_no'] = $row['aadhaar_no'];
        $info['esi_no'] = $row['esi_no'];
        $info['pan_no'] = $row['pan_no'];
        $info['marital_status'] = $row['marital_status'] == 1 ? 'unmarried' : 'married';
        $info['pran_no'] = $row['pran_no'];
    }
    //=======[from job]=========///
    $sql = "SELECT * FROM " . TB_PREF . "kv_empl_job WHERE empl_id=" . db_escape($empl_id) . ' LIMIT 1';
    // display_error($sql);
    $result = db_query($sql, "could not get empl leave request");
    while ($row = db_fetch($result)) {
        $info['employee_type'] = $row['employee_type'];
        $info['department'] = getDeptname($row['department']);
        $info['desig_group'] = getDesigGroup($row['desig_group']);
        $info['desig'] = getDesigM($row['desig']);
        $info['joining'] = $row['joining'];
        $info['empl_type'] = $row['empl_type'];
        $info['mod_of_pay'] = $row['mod_of_pay'];
        $info['ifsc_code'] = $row['ifsc_code'];
        $info['bank_name'] = $row['bank_name'];
        $info['acc_no'] = $row['acc_no'];
    }
    switch ($selectBody) {

        case 'employeesBody':
            return employeesBody($info, $info['empl_id'], $info['empl_id']);
            break;
        case 'category':
            return costCategoryBody($info['department'], $info['department']);
            break;
        case 'costcenter':
            return costCenter($info['department'], $info['desig_group'], $info['desig_group']);
            break;
        default:
            return employeesBody($info, $info['empl_id'], $info['empl_id']);
            break;
    }
}

function getDeptnameall() {

    $sql = "SELECT description FROM " . TB_PREF . "kv_departments";
    return db_query($sql, "could not get department");
}

function getDeptnameallId() {

    $sql = "SELECT id FROM " . TB_PREF . "kv_departments";
    return db_query($sql, "could not get department");
}

function getDesigGroupall() {
    $sql = "SELECT description,id FROM " . TB_PREF . "kv_desig_group";
    return db_query($sql, "could not get department");
}

function getAllEmployeeList() {

    $sql = "SELECT empl_id FROM " . TB_PREF . "kv_empl_info";
    // display_error($sql);
    return db_query($sql, "could not get empl leave request");
}

function getDeptname($id) {

    $sql = "SELECT description FROM " . TB_PREF . "kv_departments WHERE id = " . db_escape($id);
    $result = db_query($sql, "could not get department");
    $row = db_fetch($result);
    return $row[0];
}

function getDesigGroup($id) {

    $sql = "SELECT description FROM " . TB_PREF . "kv_desig_group WHERE id = " . db_escape($id);
    $result = db_query($sql, "could not get department");
    $row = db_fetch($result);
    return $row[0];
}

function getDesigM($id) {

    $sql = "SELECT name FROM " . TB_PREF . "designation_master WHERE id = " . db_escape($id);
    $result = db_query($sql, "could not get department");
    $row = db_fetch($result);
    return $row[0];
}

function getSalaryPayHeads($id, $type) {

    $sql = "SELECT type FROM " . TB_PREF . "kv_hrm_finance_setup WHERE allowance_debit_gl_code = " . db_escape($id) . " OR allowance_credit_gl_code = " . db_escape($id) . " AND inactive = 0";
    $result = db_query($sql, "could not get department");
    $row = db_fetch($result);
    if (in_array($row[0], $type))
        return true;
    else
        return false;
}

function set_company_info() {
    global $tallyServer, $tallyCompanyName;
    $result = get_syspref();
    $tallyServer = $result['tally_url'];
    $tallyCompanyName = $result['company_name'];
}

function get_syspref() {
    $sql = "SELECT * FROM " . TB_PREF . "sys_prefs";
    $result = db_query($sql, "could not query syspref table");
    $data = array();
    while ($row = db_fetch($result)) {
        if ($row['name'] == 'coy_name') {
            $data['company_name'] = $row['value'];
        }
        if ($row['name'] == 'tally_url') {
            $data['tally_url'] = $row['value'];
            break;
        }
    }
    return $data;
}

//========[PURCHASE QUERY START FROM HERE]========//

function getPurchaceGl($date = '', $accounts = '') {
    if (empty($date))
        $date = date('Y-m-d');
    $sql = "SELECT order_details.order_no,order_details.act_price as price, order_details.gst, order_details.cst, order_details.ist,grn_items.qty_recd as recved, gl.type, gl.type_no,gl.tran_date,gl.account,gl.amount,gl.memo_,gl.tally_status,supp.supp_name FROM fa_purch_order_details as order_details join fa_grn_items as grn_items on grn_items.po_detail_item = order_details.po_detail_item join fa_gl_trans as gl on gl.type_no = grn_items.grn_batch_id join fa_suppliers as supp on supp.supplier_id = gl.person_id where gl.type = 25 AND  gl.tran_date =" . db_escape($date);
    $results = db_query($sql, "could not get  purchaseGL");
    return $results;
}

function updatePurchaseGl($date = '', $accounts) {
    $sql = "update " . TB_PREF . "gl_trans set tally_status = 1 where type = 25 and tran_date = " . db_escape($date);
    db_query($sql, 'could not updated !');
}

function getAccountCodes($date) {
    $sql = 'select account from' . TB_PREF . 'gl_trans where type  = 25 AND tran_date =' . db_escape($date);
    $result = db_query($sql, 'could not get account codes from gl_tans');
    return $result;
}

function getDatesFromGl() {
    $sql = 'select distinct(tran_date)  from ' . TB_PREF . 'gl_trans where type = 25';
    $result = db_query($sql, "could not get trans_date");
    return $result;
}

//=====================[Fetch Purchase value]===============//
function purchaseInfo($voucher_name = '', $fromDate = '', $toDate = '') {
    $values = getTallyDayBook($voucher_name, $fromDate, $toDate);
    $values = getLedger($values);
    return $values;
}

function getLedger($values) {

    $ledger = array();
    $amount = array();
    $dates = array();
    foreach ($values as $ledgers => $result) {
        if ($result['tag'] == strtoupper('ledgername')) {
            $ledger[] = $result['value'];
        } else if ($result['tag'] == strtoupper('amount')) {
            $amount[] = $result['value'];
        }
        else if($result['tag'] == strtoupper('date')){
            $dates[] = $result['value'];
        }
    }
    $final_result = mergPurchaseDebitAndCreditAmount($ledger, $amount,$dates);
    return $final_result;
}

function mergPurchaseDebitAndCreditAmount($ledger, $amount, $dates) {
    $debitAmount = getPurchaseDebitAmount($ledger, $amount);
    $creditAmount = getPurchaseCreditAmount($ledger, $amount);

    $finalMergedDebitCreditAmount = array();
    $inc = 0;
    foreach ($debitAmount as $suppName => $debitAmountArr) {
        foreach ($debitAmountArr as $debitKey => $damount) {
            if ($inc == $debitKey) {
                foreach ($creditAmount as $ledgerName => $creditAmountArr) {
                    foreach ($creditAmountArr as $creditKey => $cAmount) {
                        $finalMergedDebitCreditAmount[$suppName][$debitKey][$ledgerName] = $creditAmountArr[$debitKey];
                    }
                }
                $finalMergedDebitCreditAmount[$suppName][$debitKey]['date'] = date('Y-m-d',strtotime($dates[$debitKey]));
                $inc++;
            }
        }
    }
    return $finalMergedDebitCreditAmount;
}

function getPurchaseDebitAmount($ledger, $amount) {
    $mergedDebitAmount = array();
    $inc = 0;
    foreach ($amount as $key => $value) {
        if ($value < 0) {
            $mergedDebitAmount[$ledger[$key]][$inc] = $value;
            $inc++;
        }
    }
    return $mergedDebitAmount;
}

function getPurchaseCreditAmount($ledger, $amount) {
    $mergedCreditAmount = array();
    foreach ($amount as $key => $value) {
        if ($value > 0) {
            $mergedCreditAmount[$ledger[$key]][] = $value;
        }
    }
    return $mergedCreditAmount;
}


