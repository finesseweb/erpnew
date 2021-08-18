<?php

/* * **************************************
  /*  Author 	: Kvvaradha
  /*  Module 	: Extended HRM
  /*  E-mail 	: admin@kvcodes.com
  /*  Version : 1.0
  /*  Http 	: www.kvcodes.com
 * *************************************** */

$this->row = $this->pageHeight - $this->topMargin;

$upper = $this->row - 2 * $this->lineHeight;
$lower = $this->bottomMargin + 8 * $this->lineHeight;
$iline1 = $upper - 7.5 * $this->lineHeight;
$iline2 = $iline1 - 8 * $this->lineHeight;
$iline3 = $iline2 - 1.5 * $this->lineHeight;
$iline4 = $iline3 - 1.5 * $this->lineHeight;
$iline5 = $iline4 - 3 * $this->lineHeight;
$iline6 = $iline5 - 1.5 * $this->lineHeight;
$iline7 = $lower;
$right = $this->pageWidth - $this->rightMargin;
$width = ($right - $this->leftMargin) / 5;
$icol = $this->pageWidth / 2;
$ccol = $this->cols[0] + 4;
$c2col = $ccol + 60;
$ccol2 = $icol / 2;
$mcol = $icol + 8;
$mcol2 = $this->pageWidth - $ccol2;
$cols = count($this->cols);
$this->SetDrawColor(205, 205, 205);
$this->rectangle($this->leftMargin, $iline2, $right - $this->leftMargin, $iline2 - $iline3, "F", null, array(222, 231, 236));
$this->Line($iline2);
$this->Line($iline3);
if ($this->formData['doctype'] == 'payslip') {
    $this->rectangle($this->leftMargin, $iline5 - 314, $right - $this->leftMargin, $iline5 - $iline6 + 12, "F", null, array(255, 211, 179));
    //$this->Line($iline6-136);
}
//$this->Line($iline6-166);
$col = $this->leftMargin;
if ($this->formData['doctype'] == 'payslip') {
    $this->LineTo($col += $width + 80, $iline2, $col, $iline7 + 40);
    $this->LineTo($col += $width - 20, $iline2, $col, $iline7 + 40);
    $this->LineTo($col += $width + 80, $iline2, $col, $iline7 + 40);
}
$this->LineTo($right, $iline2, $right, $iline7 + 10);
$this->LineTo($this->leftMargin, $iline2, $this->leftMargin, $iline7 + 10);
// Company Logo
$this->NewLine();
$logo = company_path() . "/images/" . $this->company['coy_logo'];
if ($this->company['coy_logo'] != '' && file_exists($logo)) {
    $this->AddImage($logo, $ccol, $this->row, 0, 40);
} else {
    $this->fontSize += 4;
    $this->Font('bold');
    $this->Text($ccol, $this->company['coy_name'], $icol);
    $this->Font();
    $this->fontSize -= 4;
}
// Document title
$this->SetTextColor(190, 190, 190);
$this->fontSize += 10;
$this->Font('bold');
$this->TextWrap($mcol, $this->row, $this->pageWidth - $this->rightMargin - $mcol - 20, $this->title, 'right');
$this->Font();
$this->fontSize -= 10;
$this->NewLine();
$this->SetTextColor(0, 0, 0);
$adrline = $this->row;

// Company data
$this->TextWrapLines($ccol, $icol, $this->company['postal_address']);
$this->Font('italic');
if (@$this->company['phone']) {
    $this->Text($ccol, _("Phone"), $c2col);
    $this->Text($c2col, $this->company['phone'], $mcol);
    $this->NewLine();
}
if (@$this->company['fax']) {
    $this->Text($ccol, _("Fax"), $c2col);
    $this->Text($c2col, $this->company['fax'], $mcol);
    $this->NewLine();
}
if (@$this->company['email']) {
    $this->Text($ccol, _("Email"), $c2col);

    $url = "mailto:" . $this->company['email'];
    $this->SetTextColor(0, 0, 255);
    $this->Text($c2col, $this->company['email'], $mcol);
    $this->SetTextColor(0, 0, 0);
    $this->addLink($url, $c2col, $this->row, $mcol, $this->row + $this->lineHeight);

    $this->NewLine();
}

$this->Font();
$this->row = $adrline;
$this->NewLine();
$this->Text($mcol + 100, _("Date"));
$this->Text($mcol + 180, $this->formData['document_date']);

$this->NewLine();
$this->Text($mcol + 100, $this->formData['document_name']);
$this->Text($mcol + 180, $this->formData['document_number']);
$this->NewLine();

if ($this->pageNumber > 1)
    $this->Text($mcol + 180, _("Page") . ' ' . $this->pageNumber);
$this->row = $iline1 - $this->lineHeight;

$this->fontSize -= 4;
$this->fontSize += 6;
$this->Text($ccol, $Addr1['title'], $icol, 0, -30);
//$this->Text($mcol, $Addr1['title']);
$this->fontSize -= 2;

// address1
//$temp = $this->row = $this->row - $this->lineHeight - 5;
if ($this->formData['doctype'] == 'paysummary') {
    if ($Addr1['id'] != '') {
        $this->Text($ccol, 'Month', $icol, 0, -4);
        $this->Text($ccol + 100, kv_month_name_by_id($Addr1['id']), $icol, 0, -4);
        $this->NewLine();
    }

    $this->NewLine();
    $this->Text($ccol, 'Year', $icol, 0, 1);
    $fYear = get_fiscalyear($Addr1['year']);
    $this->Text($ccol + 100, $fYear['begin'] . ' - ' . $fYear['end'], $icol, 0, 1);
} else {
    
     $my_text = 'Salary';
    $month = trim($month,',');
        if (is_array($this->formData['month'])) {
        foreach ($this->formData['month'][0] as $key => $value) {
            $month .= date('M', mktime(0, 0, 0, $value, 10)) . ", ";
        }
        
            $payslip_year = get_employee_sal_year($Addr1['id'], $this->formData['year'][0]);
    //$pay_year=substr($payslip_year['date'],0,4);
    $pay_year = date("Y", strtotime($payslip_year['date']));
        $my_text ='Arrear';
    }
 else {
       $payslip_year = get_employee_sal_year($Addr1['id'], $this->formData['year']);
       
        
       $pay_year=substr($payslip_year['date'],0,4);
       $pay_year = date("Y", strtotime($payslip_year['date']));
       $month =   date('F', mktime(0, 0, 0,$this->formData['month'], 10));  
    }
            
     $fisical_year =  get_fiscalyear($this->formData['year']);
     $first_year = date('Y',strtotime($fisical_year['begin']));
     $second_year = date('Y',strtotime($fisical_year['end']));
     
     $first_year_month = array('April','May','June','July','August','Septmber','October','November','December');
     $second_year_month = array('January','February','March');
     
     if(in_array($month,$first_year_month)){
         $pay_year = $first_year;
     }
     else if(in_array($month,$second_year_month)){
         $pay_year = $second_year;
     }
     
     
    $this->Text($ccol, 'Year', $icol, 0, -6);
    $this->Text($ccol + 100,$pay_year , $icol, 0, -6);
    $this->Text($ccol + 350, 'Month', $icol, 0, -6);
    $this->Text($ccol + 430, $month, $icol, 0, -6);
    $this->NewLine();
    $empl_gender_prefx = get_employee($Addr1['id']);

    if ($empl_gender_prefx['empl_salutation'] == 1) {
        $name_prefx = 'Mr. ' . $Addr1['name'];
    } else if ($empl_gender_prefx['empl_salutation'] == 2) {
        $name_prefx = 'Ms. ' . $Addr1['name'];
    } else if ($empl_gender_prefx['empl_salutation'] == 3) {
        $name_prefx = 'Mrs. ' . $Addr1['name'];
    } else if ($empl_gender_prefx['empl_salutation'] == 4) {
        $name_prefx = 'Dr. ' . $Addr1['name'];
    } else if ($empl_gender_prefx['empl_salutation'] == 5) {
        $name_prefx = 'Prof. ' . $Addr1['name'];
    } else {
        $name_prefx = $empl_gender_prefx['salutation_text'].'. ' . $Addr1['name'];
    }
        $this->Text($ccol, 'Employee Name', $icol, 0, -2);
    $this->Text($ccol + 100, $name_prefx, $icol, 0, -2);


}
//echo "<pre>"; print_r($Addr1);
if ($this->formData['doctype'] != 'paysummary') {
  /*  $this->Text($ccol + 350, 'Department', $icol, 0, -1);
    $this->Text($ccol + 430, $Addr1['dept'], $icol, 0, -1);
   * */$pf_data = get_employee($Addr1['id']);
    $this->Text($ccol + 350, 'Pan No', $icol, 0, -2);
    $this->Text($ccol + 430, $pf_data['pan_no']?$pf_data['pan_no']:'N/A', $icol, 0, -2);
    $this->NewLine();
    
   
        
    $this->Text($ccol, 'Employee Code', $icol, 0, -2);
    $this->Text($ccol + 100, $Addr1['id'], $icol, 0, -2);
        $this->Text($ccol + 350, 'PF-UAN', $icol, 0, -2);
    $this->Text($ccol + 430, $pf_data['pf_number']?$pf_data['pf_number']:'N/A', $icol, 0, -2);
   /* $this->Text($ccol + 350, 'Date of Joining', $icol, 0, 1);
    $this->Text($ccol + 430, $Addr1['joining'], $icol, 0, 1);
    * */

    $this->NewLine();
    $this->Text($ccol, 'Designation', $icol, 0, -2);
    $designation = get_designationedit($Addr1['desig']);
    $this->Text($ccol + 100, $designation['name'], $icol, 0, -2);
    $this->Text($ccol + 350, 'ESI IP NO.', $icol, 0, -2);
    $this->Text($ccol + 430, $pf_data['esi_no']?$pf_data['esi_no']:'N/A', $icol, 0, -2);
    $this->NewLine();
    $this->Text($ccol, 'DOJ', $icol, 0, -2,'left');
    $this->Text($ccol+ 100, $Addr1['joining']?$Addr1['joining']:'N/A', $icol, 0, -2);
    $this->NewLine();
     $this->Text($ccol, 'Bank Name', $icol, 0, -4);
     $this->Text($ccol + 100, $this->formData['bank_name']?$this->formData['bank_name']:'N/A', $icol, 0, -4);
    $this->Text($ccol + 350, 'No of days worked', $icol, 0, -4);
        $total_days = cal_days_in_month(CAL_GREGORIAN,$this->formData['month'],$this->formData['year']);
    $this->Text($ccol + 430, $total_days - (float)($this->formData['lop']?$this->formData['lop']:'0') , $icol, 0, -4);
    $this->NewLine();
     $this->Text($ccol, 'Acc No.', $icol, 0, -4);
     $this->Text($ccol + 100, $this->formData['acc_no']?$this->formData['acc_no']:'N/A', $icol, 0, -4);
    $this->Text($ccol + 350, 'LOP', $icol, 0, -4);
    $this->Text($ccol + 430, $this->formData['lop']?$this->formData['lop']:'0', $icol, 0, -4);
    
   /* $this->Text($ccol + 350, 'LOP', $icol, 0, 1);
    $this->Text($ccol + 430, $this->formData['lop'], $icol, 0, 1);
    $this->NewLine(2);*/
    $this->Font('bold');
   
  //  $this->Text($ccol + 150, "$my_text for the month of " . $month . ' ' . $pay_year, $icol + 100, 0, 1);
    $this->Font();
}


// Line headers

$this->row = $iline2 - $this->lineHeight - 2;
$this->Font('bold');
$count = count($this->headers);
$this->cols[$count] = $right;
for ($i = 0; $i < $count; $i++)
    $this->TextCol($i, $i + 1, $this->headers[$i], -2);
$this->Font();

// Footer
$this->Font('italic');
$this->row = $iline7 - $this->lineHeight - 20;



$this->NewLine(2);


foreach ($Footer as $line => $txt) {
    if (!is_numeric($line)) {// title => link			
        $this->fontSize -= 2;
        $this->TextWrap($ccol, $this->row, $right - $ccol, $line, 'C');
        $this->row -= $this->lineHeight;
        $this->SetTextColor(0, 0, 255);
        $this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->addLink($txt, $ccol, $this->row, $this->pageWidth - $this->rightMargin, $this->row + $this->lineHeight);
        $this->fontSize += 2;
    } else
        $this->TextWrap($ccol, $this->row, $right - $ccol, $txt, 'C');
    $this->row -= $this->lineHeight;
}

$this->Font();
$temp = $iline6 - $this->lineHeight - 2;
?>