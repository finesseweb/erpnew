<?php
/** 
 * @Framework Zend Framework
 * @Powered By TIS 
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2015 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 *	Authors Kannan and Rajkumar
 */
class Application_Model_Report extends Zend_Db_Table_Abstract
{
    public $_name = 'enquiry';
    protected $_id = 'enquiry_id';
	protected $_auth_id = '';
	protected $_role_id = '';
	
	//Get the common data
	public function init() {      
        $this->_act = new Application_Model_Adminactions();	
		$this->_auth_id = $this->_act->auth_id();
		$this->_role_id = $this->_act->role_id();		
    }
   
    //get details for show
    public function getRecords()
    {
	
		//if($this->_role_id == 1){
		
				$select=$this->_db->select()
				
						->from($this->_name)
						->joinLeft(array("branch" => "master_branch"), "branch.branch_id=$this->_name.branch", array("branch_name"))
						->joinLeft(array("country" => "erp_countries"), "country.countries_id=$this->_name.country", array("countries_name"))
						->joinLeft(array("state" => "erp_states"), "state.state_id=$this->_name.state", array("state_name"))
						->joinLeft(array("city" => "erp_cities"), "city.city_id=$this->_name.city", array("city_name"))
						->joinLeft(array("location" => "master_location"), "location.location_id=$this->_name.location_id", array("location_name"))
						->joinLeft(array("employee" => "master_employees"), "employee.employee_id=$this->_name.counsellor", array("employee_name"))
						->joinLeft(array("academic_year" => "master_academic_year"), "academic_year.academic_year_id=$this->_name.academic_year", array("academic_year as academic_year_name"))
						 ->joinLeft(array("subprogram" => "master_subprogram"), "subprogram.subprogram_id=$this->_name.subprogram_id", array("subprogram_name","subprogram_code"))
					  ->joinLeft(array("frequency" => "master_frequency"), "frequency.frequency_id=$this->_name.frequency", array("frequency_title"))
					  ->joinLeft(array("timing" => "master_timing"), "timing.timing_id=frequency.timing ",array("timing_title"))
					   ->joinLeft(array("father_company" => "master_add_company"), "father_company.company_id=$this->_name.father_company_id ",array("company_id as father_company_id","company_name as father_company_name"))
					    ->joinLeft(array("mother_company" => "master_add_company"), "mother_company.company_id=$this->_name.company_id ",array("company_id as mother_company_id","company_name as mother_company_name"))
						 ->joinLeft(array("occupation" => "master_occupation"), "occupation.occupation_id=$this->_name.father_occupation ",array("occupation_id as father_occupation_id","occupation_type as father_occupation_name"))
						  ->joinLeft(array("moccupation" => "master_occupation"), "moccupation.occupation_id=$this->_name.mother_occupation ",array("occupation_id as mother_occupation_id","occupation_type as mother_occupation_name"))
						   ->joinLeft(array("type_of_enquiry" =>"master_typeofenquiry"), "type_of_enquiry.id=$this->_name.type_of_enquiry ",array("typeofenquiry"))
						   ->joinLeft(array("source_of_enquiry" =>"master_sourceofenquiry"), "source_of_enquiry.id=$this->_name.source_of_enquiry ",array("sourceofenquiry"))
							->joinLeft(array("whyesperanza" =>"master_whyesperanza"), "whyesperanza.id=$this->_name.why_esperanza",array("whyesperanza"))
								->joinLeft(array("howdoyouknow" =>"master_how_know_esp"), "howdoyouknow.id=$this->_name.how_know_esperanza",array("howdoyouknowesp"))
								->joinLeft(array("enquiry_mindset" =>"master_enquirymindset"), "enquiry_mindset.id=$this->_name.enquiry_mind_set",array("enquirymindset","color_picker"))
								->joinLeft(array("enq_items"=>"enquiry_items"),"enq_items.enquiry_id=$this->_name.enquiry_id",array("GROUP_CONCAT(enq_items.father_mobile_no) as father_mobile","GROUP_CONCAT(enq_items.mother_mobile_no) as mother_mobile"))
						->order('enquiry_id DESC')
						->group("$this->_name.enquiry_id")
						->where("$this->_name.status!=2");
		/* }else{
		
			$select=$this->_db->select()
						->from($this->_name)
						->joinLeft(array("branch" => "master_branch"), "branch.branch_id=$this->_name.branch", array("branch_name"))
						->joinLeft(array("country" => "erp_countries"), "country.countries_id=$this->_name.country", array("countries_name"))
						->joinLeft(array("state" => "erp_states"), "state.state_id=$this->_name.state", array("state_name"))
						->joinLeft(array("city" => "erp_cities"), "city.city_id=$this->_name.city", array("city_name"))
						->joinLeft(array("location" => "master_location"), "location.location_id=$this->_name.location_id", array("location_name"))
						->joinLeft(array("employee" => "master_employees"), "employee.employee_id=$this->_name.counsellor", array("employee_name"))
						->joinLeft(array("academic_year" => "master_academic_year"), "academic_year.academic_year_id=$this->_name.academic_year", array("academic_year as academic_year_name"))
						 ->joinLeft(array("subprogram" => "master_subprogram"), "subprogram.subprogram_id=$this->_name.subprogram_id", array("subprogram_name","subprogram_code"))
					  ->joinLeft(array("frequency" => "master_frequency"), "frequency.frequency_id=$this->_name.frequency", array("frequency_title"))
					  ->joinLeft(array("timing" => "master_timing"), "timing.timing_id=frequency.timing ",array("timing_title"))
					   ->joinLeft(array("father_company" => "master_add_company"), "father_company.company_id=$this->_name.father_company_id ",array("company_id as father_company_id","company_name as father_company_name"))
					    ->joinLeft(array("mother_company" => "master_add_company"), "mother_company.company_id=$this->_name.company_id ",array("company_id as mother_company_id","company_name as mother_company_name"))
						 ->joinLeft(array("occupation" => "master_occupation"), "occupation.occupation_id=$this->_name.father_occupation ",array("occupation_id as father_occupation_id","occupation_type as father_occupation_name"))
						  ->joinLeft(array("moccupation" => "master_occupation"), "moccupation.occupation_id=$this->_name.mother_occupation ",array("occupation_id as mother_occupation_id","occupation_type as mother_occupation_name"))
						   ->joinLeft(array("type_of_enquiry" =>"master_typeofenquiry"), "type_of_enquiry.id=$this->_name.type_of_enquiry ",array("typeofenquiry"))
						   ->joinLeft(array("enquiry_mindset" =>"master_enquirymindset"), "enquiry_mindset.id=$this->_name.enquiry_mind_set",array("enquirymindset","color_picker"))
						     ->joinLeft(array("source_of_enquiry" =>"master_sourceofenquiry"), "source_of_enquiry.id=$this->_name.source_of_enquiry ",array("sourceofenquiry"))
							->joinLeft(array("whyesperanza" =>"master_whyesperanza"), "whyesperanza.id=$this->_name.why_esperanza",array("whyesperanza"))
								->joinLeft(array("howdoyouknow" =>"master_how_know_esp"), "howdoyouknow.id=$this->_name.how_know_esperanza",array("howdoyouknowesp"))
						->where("$this->_name.branch=?", $this->_auth_id)
						->where("$this->_name.status!=2")
						->order('enquiry_id DESC');
		} */
		
		
        $result=$this->getAdapter()
                      ->fetchAll($select);
					 
        return $result;
    }
	//FOLLOW UO SEARCH
	public function getReportSearchRecords($how_know_esperanza='',$mindset='',$search_type='')
    {       
        $where="";
		if(!empty($how_know_esperanza)){
			$where .=" AND enquiry.how_know_esperanza='$how_know_esperanza'"; 
		
		}
		if(!empty($mindset)){
			$where .=" AND enquiry.enquiry_mind_set='$mindset'"; 
		
		}
		if(!empty($search_type)){
			$where .=" AND enquiry.father_first_name LIKE '%$search_type%' OR enquiry.mother_first_name LIKE '%$search_type%' OR enquiry.guardian_first_name LIKE '%$search_type%' OR enquiry.guardian_mobile_no LIKE '%$search_type%' OR enquiry.primary_mobile_no LIKE '%$search_type%' OR enquiry.child_first_name LIKE '%$search_type%' OR enquiry.child_middle_name LIKE '%$search_type%' OR enquiry.child_last_name LIKE '%$search_type%' OR enquiry.email LIKE '%$search_type%' "; 
		
		} 
		
		if($this->_role_id == 1){
			$select = "SELECT `enquiry`.*, `branch`.`branch_name` FROM `enquiry` LEFT JOIN `master_branch` AS `branch` ON branch.branch_id=enquiry.branch  WHERE enquiry.status!=2 $where  ORDER BY enquiry.enquiry_id DESC";
		}else{
			$select = "SELECT `enquiry`.*, `branch`.`branch_name` FROM `enquiry` LEFT JOIN `master_branch` AS `branch` ON branch.branch_id=enquiry.branch  WHERE enquiry.status!=2 and enquiry.branch='".$this->_auth_id."' ".$where."ORDER BY enquiry.enquiry_id DESC";
		}
		
		$result=$this->getAdapter()
                      ->fetchAll($select);       
        return $result;
		
    } 
	
	
	public function getSearchRecords($search_type='',$country='',$state='',$city='',$location='',$branch='',$from_date='',$to_date='',$academic_year='',$program='',$subprogram='',$frequency='',$counselor='',$whyus='',$howdoyouknowesp='',$type_of_enquiry='',$source_of_enquiry='',$enquiry_mindset='',$enquiry_date='',$occupation='',$company='',$reference=''){
		
		$where="";
		if(!empty($search_type)){
			$where .= "AND CONCAT(enquiry.father_first_name, ' ', enquiry.father_last_name) = '$search_type' OR CONCAT(enquiry.mother_first_name, ' ', enquiry.mother_last_name) = '$search_type' OR  CONCAT(enquiry.child_first_name, ' ', enquiry.child_last_name) = '$search_type' OR enquiry.father_email IN ('$search_type') OR enquiry.mother_email IN ('$search_type')  OR enquiry.enquiry_id = '$search_type' OR enq_items.father_mobile_no = '$search_type' OR enq_items.mother_mobile_no = '$search_type'";		
		}
		if(!empty($country)){
			$where .= "AND enquiry.country = '$country'";		
		}
		if(!empty($state)){
			$where .= "AND enquiry.state = '$state'";		
		}
		if(!empty($city)){
			$where .= "AND enquiry.city = '$city'";		
		}
		if(!empty($location)){
		
			$where .= "AND enquiry.location_id = '$location'";		
		}
		if(!empty($branch)){
			$where .=" AND FIND_IN_SET(branch.branch_id,'$branch')"; 
		
		}
		
		if(!empty($from_date)){
			$where .= "AND enquiry.enquiry_date >= '$from_date'";				
		}
		if(!empty($from_date)){
			$where .= "AND enquiry.enquiry_date <= '$to_date'";				
		}
		if(!empty($academic_year)){
			$where .= "AND enquiry.academic_year = '$academic_year'";				
		}
		if(!empty($program)){
			$where .= "AND enquiry.program_id = '$program'";		
		}
		if(!empty($subprogram)){
			$where .= "AND enquiry.subprogram_id = '$subprogram'";		
		}
		if(!empty($frequency)){
			$where .= "AND enquiry.frequency = '$frequency'";		
		}
		if(!empty($counselor)){
			$where .= "AND enquiry.counsellor = '$counselor'";		
		}
		if(!empty($whyus)){
			$where .= "AND enquiry.why_esperanza = '$whyus'";		
		}
		if(!empty($howdoyouknowesp)){
			$where .= "AND enquiry.how_know_esperanza = '$howdoyouknowesp'";		
		}
		if(!empty($type_of_enquiry)){
			$where .= "AND enquiry.type_of_enquiry = '$type_of_enquiry'";		
		}
		if(!empty($source_of_enquiry)){
			$where .= "AND enquiry.source_of_enquiry = '$source_of_enquiry'";		
		}
		if(!empty($enquiry_mindset)){
			$where .= "AND enquiry.enquiry_mind_set = '$enquiry_mindset'";		
		}
		if(!empty($enquiry_date)){
			$where .= "AND enquiry.enquiry_date = '$enquiry_date'";		
		}
		if(!empty($occupation)){
			$where .= "AND enquiry.father_occupation = '$occupation' OR enquiry.mother_occupation = '$occupation'  
			OR enquiry.guardian_occupation = '$occupation'";		
		}
		if(!empty($company)){
			$where .= "AND enquiry.father_company_id = '$company' OR enquiry.company_id = '$company'  
			OR enquiry.guardian_company_id = '$company'";		
		}
		if(!empty($reference)){
			
			$where .= "AND enquiry.reference_parent_of_esperanza = '$reference'";	
			
		}
		
	$select = "SELECT `enquiry`.*, `branch`.`branch_name`, `country`.`countries_name`, `state`.`state_name`, `city`.`city_name`, `location`.`location_name`, `employee`.`employee_name`, `academic_year`.`academic_year` AS `academic_year_name`, `subprogram`.`subprogram_name`, `subprogram`.`subprogram_code`, `frequency`.`frequency_title`, `timing`.`timing_title`, `father_company`.`company_id` AS `father_company_id`, `father_company`.`company_name` AS `father_company_name`, `mother_company`.`company_id` AS `mother_company_id`, `mother_company`.`company_name` AS `mother_company_name`, `occupation`.`occupation_id` AS `father_occupation_id`, `occupation`.`occupation_type` AS `father_occupation_name`, `moccupation`.`occupation_id` AS `mother_occupation_id`, `moccupation`.`occupation_type` AS `mother_occupation_name`, `type_of_enquiry`.`typeofenquiry`, `source_of_enquiry`.`sourceofenquiry`, `whyesperanza`.`whyesperanza`, `howdoyouknow`.`howdoyouknowesp`, `enquiry_mindset`.`enquirymindset`,`enquiry_mindset`.`color_picker`, GROUP_CONCAT(enq_items.father_mobile_no) AS `father_mobile`, GROUP_CONCAT(enq_items.mother_mobile_no) AS `mother_mobile` FROM `enquiry` LEFT JOIN `master_branch` AS `branch` ON branch.branch_id=enquiry.branch LEFT JOIN `erp_countries` AS `country` ON country.countries_id=enquiry.country LEFT JOIN `erp_states` AS `state` ON state.state_id=enquiry.state LEFT JOIN `erp_cities` AS `city` ON city.city_id=enquiry.city LEFT JOIN `master_location` AS `location` ON location.location_id=enquiry.location_id LEFT JOIN `master_employees` AS `employee` ON employee.employee_id=enquiry.counsellor LEFT JOIN `master_academic_year` AS `academic_year` ON academic_year.academic_year_id=enquiry.academic_year LEFT JOIN `master_subprogram` AS `subprogram` ON subprogram.subprogram_id=enquiry.subprogram_id LEFT JOIN `master_frequency` AS `frequency` ON frequency.frequency_id=enquiry.frequency LEFT JOIN `master_timing` AS `timing` ON timing.timing_id=frequency.timing LEFT JOIN `master_add_company` AS `father_company` ON father_company.company_id=enquiry.father_company_id LEFT JOIN `master_add_company` AS `mother_company` ON mother_company.company_id=enquiry.company_id LEFT JOIN `master_occupation` AS `occupation` ON occupation.occupation_id=enquiry.father_occupation LEFT JOIN `master_occupation` AS `moccupation` ON moccupation.occupation_id=enquiry.mother_occupation LEFT JOIN `master_typeofenquiry` AS `type_of_enquiry` ON type_of_enquiry.id=enquiry.type_of_enquiry LEFT JOIN `master_sourceofenquiry` AS `source_of_enquiry` ON source_of_enquiry.id=enquiry.source_of_enquiry LEFT JOIN `master_whyesperanza` AS `whyesperanza` ON whyesperanza.id=enquiry.why_esperanza LEFT JOIN `master_how_know_esp` AS `howdoyouknow` ON howdoyouknow.id=enquiry.how_know_esperanza LEFT JOIN `master_enquirymindset` AS `enquiry_mindset` ON enquiry_mindset.id=enquiry.enquiry_mind_set LEFT JOIN `enquiry_items` AS `enq_items` ON enq_items.enquiry_id=enquiry.enquiry_id WHERE (enquiry.status!=2)  $where GROUP BY `enquiry`.`enquiry_id` ORDER BY `enquiry_id` DESC";
	//echo $select; die;
	
	$result =$this->getAdapter()
              ->fetchAll($select);   
//echo '<pre>'; print_r($result); die; 
 
        return $result;
	
}

	public function getExportSearchRecords($search_type='',$country='',$state='',$city='',$location='',$branch='',$from_date='',$to_date='',$academic_year='',$program='',$subprogram='',$frequency='',$counselor='',$whyus='',$howdoyouknowesp='',$type_of_enquiry='',$source_of_enquiry='',$enquiry_mindset='',$enquiry_date='',$occupation='',$company='',$reference=''){
//echo $state; die;
		
		$where="";
if (!empty($search_type) || !empty($country) || !empty($state) || !empty($city) || !empty($location) ||!empty($branch) || !empty($from_date) || !empty($to_date) || !empty($academic_year) || !empty($program) || !empty($subprogram) || !empty($frequency) ||  !empty($counselor) || !empty($whyus)  || !empty($howdoyouknowesp) || !empty($type_of_enquiry)|| !empty($source_of_enquiry) || !empty($enquiry_mindset) || !empty($enquiry_date) ||  !empty($occupation) || !empty($company)  || !empty($reference)) {
		
		if(!empty($search_type)){
			$where .= "AND CONCAT(enquiry.father_first_name, ' ', enquiry.father_last_name) = '$search_type' OR CONCAT(enquiry.mother_first_name, ' ', enquiry.mother_last_name) = '$search_type' OR  CONCAT(enquiry.child_first_name, ' ', enquiry.child_last_name) = '$search_type' OR enquiry.father_email IN ('$search_type') OR enquiry.mother_email IN ('$search_type')  OR enquiry.enquiry_id = '$search_type' OR enq_items.father_mobile_no = '$search_type' OR enq_items.mother_mobile_no = '$search_type'";		
		}
		if(!empty($country)){
			$where .= "AND enquiry.country = '$country'";		
		}
		if(!empty($state)){
			$where .= "AND enquiry.state = '$state'";		
		}
		if(!empty($city)){
			$where .= "AND enquiry.city = '$city'";		
		}
		if(!empty($location)){
		
			$where .= "AND enquiry.location_id = '$location'";		
		}
		if(!empty($branch)){
			$where .=" AND FIND_IN_SET(branch.branch_id,'$branch')"; 
		
		}
		
		if(!empty($from_date)){
			$where .= "AND enquiry.enquiry_date >= '$from_date'";				
		}
		if(!empty($from_date)){
			$where .= "AND enquiry.enquiry_date <= '$to_date'";				
		}
		if(!empty($academic_year)){
			$where .= "AND enquiry.academic_year = '$academic_year'";				
		}
		if(!empty($program)){
			$where .= "AND enquiry.program_id = '$program'";		
		}
		if(!empty($subprogram)){
			$where .= "AND enquiry.subprogram_id = '$subprogram'";		
		}
		if(!empty($frequency)){
			$where .= "AND enquiry.frequency = '$frequency'";		
		}
		if(!empty($counselor)){
			$where .= "AND enquiry.counsellor = '$counselor'";		
		}
		if(!empty($whyus)){
			$where .= "AND enquiry.why_esperanza = '$whyus'";		
		}
		if(!empty($howdoyouknowesp)){
			$where .= "AND enquiry.how_know_esperanza = '$howdoyouknowesp'";		
		}
		if(!empty($type_of_enquiry)){
			$where .= "AND enquiry.type_of_enquiry = '$type_of_enquiry'";		
		}
		if(!empty($source_of_enquiry)){
			$where .= "AND enquiry.source_of_enquiry = '$source_of_enquiry'";		
		}
		if(!empty($enquiry_mindset)){
			$where .= "AND enquiry.source_of_enquiry = '$source_of_enquiry'";		
		}
		if(!empty($enquiry_date)){
			$where .= "AND enquiry.date = '$enquiry_date'";		
		}
		if(!empty($occupation)){
			$where .= "AND enquiry.father_occupation = '$occupation' OR enquiry.mother_occupation = '$occupation'  
			OR enuiry.guardian_occupation = '$occupation'";		
		}
		if(!empty($company)){
			$where .= "AND enquiry.father_company_id = '$company' OR enquiry.company_id = '$company'  
			OR enuiry.mother_company_id = '$company'";		
		}
		if(!empty($reference)){
			
			$where .= "AND enquiry.reference_parent_of_esperanza = '$reference'";	
		}
				  
		
		$select = "SELECT `country`.`countries_name`, `state`.`state_name`, `city`.`city_name`, `location`.`location_name`,`branch`.`branch_name`, `employee`.`employee_name`, `academic_year`.`academic_year` AS `academic_year_name`,`enquiry`.`enquiry_id`,CONCAT(`enquiry`.`enquiry_date`, '', `enquiry`.`enquiry_time`) as `enquiry_date_time`,`enquiry`.`next_follow_up_date`,CONCAT(`enquiry`.`child_first_name`, '', `enquiry`.`child_last_name`) as `child_name`,CONCAT(`enquiry`.`father_first_name`, '', `enquiry`.`father_last_name`) as `father_name`,CONCAT(`enquiry`.`mother_first_name`, '', `enquiry`.`mother_last_name`) as `mother_name`, GROUP_CONCAT(`enq_items`.`father_mobile_no`) AS `father_mobile`, GROUP_CONCAT(`enq_items`.`mother_mobile_no`) AS `mother_mobile`,
	`enquiry`.`father_email`,`enquiry`.`mother_email`,`subprogram`.`subprogram_name`,  CONCAT(`frequency`.`frequency_title`,'', `timing`.`timing_title`) as `frequency_timing_title`, `father_company`.`company_name` AS `father_company_name`, `mother_company`.`company_name` AS `mother_company_name`, `occupation`.`occupation_type` AS `father_occupation_name`, `moccupation`.`occupation_type` AS `mother_occupation_name`, `type_of_enquiry`.`typeofenquiry`, `source_of_enquiry`.`sourceofenquiry`,  `enquiry_mindset`.`enquirymindset`,`whyesperanza`.`whyesperanza`, `howdoyouknow`.`howdoyouknowesp`,`enquiry`.`reference_parent_of_esperanza`,`enquiry`.`comments` FROM `enquiry` LEFT JOIN `master_branch` AS `branch` ON branch.branch_id=enquiry.branch LEFT JOIN `erp_countries` AS `country` ON country.countries_id=enquiry.country LEFT JOIN `erp_states` AS `state` ON state.state_id=enquiry.state LEFT JOIN `erp_cities` AS `city` ON city.city_id=enquiry.city LEFT JOIN `master_location` AS `location` ON location.location_id=enquiry.location_id LEFT JOIN `master_employees` AS `employee` ON employee.employee_id=enquiry.counsellor LEFT JOIN `master_academic_year` AS `academic_year` ON academic_year.academic_year_id=enquiry.academic_year LEFT JOIN `master_subprogram` AS `subprogram` ON subprogram.subprogram_id=enquiry.subprogram_id LEFT JOIN `master_frequency` AS `frequency` ON frequency.frequency_id=enquiry.frequency LEFT JOIN `master_timing` AS `timing` ON timing.timing_id=frequency.timing LEFT JOIN `master_add_company` AS `father_company` ON father_company.company_id=enquiry.father_company_id LEFT JOIN `master_add_company` AS `mother_company` ON mother_company.company_id=enquiry.company_id LEFT JOIN `master_occupation` AS `occupation` ON occupation.occupation_id=enquiry.father_occupation LEFT JOIN `master_occupation` AS `moccupation` ON moccupation.occupation_id=enquiry.mother_occupation LEFT JOIN `master_typeofenquiry` AS `type_of_enquiry` ON type_of_enquiry.id=enquiry.type_of_enquiry LEFT JOIN `master_sourceofenquiry` AS `source_of_enquiry` ON source_of_enquiry.id=enquiry.source_of_enquiry LEFT JOIN `master_whyesperanza` AS `whyesperanza` ON whyesperanza.id=enquiry.why_esperanza LEFT JOIN `master_how_know_esp` AS `howdoyouknow` ON howdoyouknow.id=enquiry.how_know_esperanza LEFT JOIN `master_enquirymindset` AS `enquiry_mindset` ON enquiry_mindset.id=enquiry.enquiry_mind_set LEFT JOIN `enquiry_items` AS `enq_items` ON enq_items.enquiry_id=enquiry.enquiry_id WHERE (enquiry.status!=2)  $where GROUP BY `enquiry`.`enquiry_id` ORDER BY `enquiry_id` DESC";
		 }
	else{
			$select = "SELECT `country`.`countries_name`, `state`.`state_name`, `city`.`city_name`, `location`.`location_name`,`branch`.`branch_name`, `employee`.`employee_name`, `academic_year`.`academic_year` AS `academic_year_name`,`enquiry`.`enquiry_id`,CONCAT(`enquiry`.`enquiry_date`, '', `enquiry`.`enquiry_time`) as `enquiry_date_time`,`enquiry`.`next_follow_up_date`,CONCAT(`enquiry`.`child_first_name`, '', `enquiry`.`child_last_name`) as `child_name`,CONCAT(`enquiry`.`father_first_name`, '', `enquiry`.`father_last_name`) as `father_name`,CONCAT(`enquiry`.`mother_first_name`, '', `enquiry`.`mother_last_name`) as `mother_name`, GROUP_CONCAT(`enq_items`.`father_mobile_no`) AS `father_mobile`, GROUP_CONCAT(`enq_items`.`mother_mobile_no`) AS `mother_mobile`,`enquiry`.`father_email`,`enquiry`.`mother_email`,`subprogram`.`subprogram_name`,  CONCAT(`frequency`.`frequency_title`,'', `timing`.`timing_title`) as `frequency_timing_title`, `father_company`.`company_name` AS `father_company_name`, `mother_company`.`company_name` AS `mother_company_name`, `occupation`.`occupation_type` AS `father_occupation_name`, `moccupation`.`occupation_type` AS `mother_occupation_name`, `type_of_enquiry`.`typeofenquiry`, `source_of_enquiry`.`sourceofenquiry`,  `enquiry_mindset`.`enquirymindset`,`whyesperanza`.`whyesperanza`, `howdoyouknow`.`howdoyouknowesp`,`enquiry`.`reference_parent_of_esperanza`,`enquiry`.`comments` FROM `enquiry` LEFT JOIN `master_branch` AS `branch` ON branch.branch_id=enquiry.branch LEFT JOIN `erp_countries` AS `country` ON country.countries_id=enquiry.country LEFT JOIN `erp_states` AS `state` ON state.state_id=enquiry.state LEFT JOIN `erp_cities` AS `city` ON city.city_id=enquiry.city LEFT JOIN `master_location` AS `location` ON location.location_id=enquiry.location_id LEFT JOIN `master_employees` AS `employee` ON employee.employee_id=enquiry.counsellor LEFT JOIN `master_academic_year` AS `academic_year` ON academic_year.academic_year_id=enquiry.academic_year LEFT JOIN `master_subprogram` AS `subprogram` ON subprogram.subprogram_id=enquiry.subprogram_id LEFT JOIN `master_frequency` AS `frequency` ON frequency.frequency_id=enquiry.frequency LEFT JOIN `master_timing` AS `timing` ON timing.timing_id=frequency.timing LEFT JOIN `master_add_company` AS `father_company` ON father_company.company_id=enquiry.father_company_id LEFT JOIN `master_add_company` AS `mother_company` ON mother_company.company_id=enquiry.company_id LEFT JOIN `master_occupation` AS `occupation` ON occupation.occupation_id=enquiry.father_occupation LEFT JOIN `master_occupation` AS `moccupation` ON moccupation.occupation_id=enquiry.mother_occupation LEFT JOIN `master_typeofenquiry` AS `type_of_enquiry` ON type_of_enquiry.id=enquiry.type_of_enquiry LEFT JOIN `master_sourceofenquiry` AS `source_of_enquiry` ON source_of_enquiry.id=enquiry.source_of_enquiry LEFT JOIN `master_whyesperanza` AS `whyesperanza` ON whyesperanza.id=enquiry.why_esperanza LEFT JOIN `master_how_know_esp` AS `howdoyouknow` ON howdoyouknow.id=enquiry.how_know_esperanza LEFT JOIN `master_enquirymindset` AS `enquiry_mindset` ON enquiry_mindset.id=enquiry.enquiry_mind_set LEFT JOIN `enquiry_items` AS `enq_items` ON enq_items.enquiry_id=enquiry.enquiry_id WHERE (enquiry.status!=2)   GROUP BY `enquiry`.`enquiry_id` ORDER BY `enquiry_id` DESC";
		
}

$result =$this->getAdapter()
              ->fetchAll($select);    
 $data = array();
    
 foreach($result as $k => $val){
	 $data[$k]['countries_name'] = $val['countries_name'];
	 $data[$k]['state_name'] = $val['state_name'];
	 $data[$k]['city_name'] = $val['city_name'];
	 $data[$k]['location_name'] = $val['location_name'];
	 $data[$k]['branch_name'] = $val['branch_name'];
	 $data[$k]['employee_name'] = $val['employee_name'];
	 $data[$k]['academic_year_name'] = $val['academic_year_name'];
	 $data[$k]['enquiry_id'] = $val['enquiry_id'];
	 $data[$k]['enquiry_date_time'] = $val['enquiry_date_time'];
	 $data[$k]['next_follow_up_date'] = $val['next_follow_up_date'];
	 $data[$k]['child_name'] = $val['child_name'];
	 $data[$k]['father_name'] = $val['father_name'];
	 $data[$k]['mother_name'] = $val['mother_name']; 
	 $data[$k]['father_mobile'] = $val['father_mobile'];
	 $data[$k]['mother_mobile'] = $val['mother_mobile'];
	 if($val['father_email'] != 'N;'){
	 $str = $this->_act->unserialize_php($val['father_email']);
	 $data[$k]['father_email'] = implode(',',$str);
	 }else{
		 $data[$k]['father_email'] = '';
	 }
	  if($val['mother_email'] != 'N;'){
	 $str1 = $this->_act->unserialize_php($val['mother_email']);
	 $data[$k]['mother_email'] = implode(',',$str1);
	  }else{
		  $data[$k]['mother_email'] = '';
	  }
	 $data[$k]['subprogram_name'] = $val['subprogram_name'];
	 $data[$k]['frequency_timing_title'] = $val['frequency_timing_title']; 
	 $data[$k]['father_company_name'] = $val['father_company_name'];  
	 $data[$k]['mother_company_name'] = $val['mother_company_name'];  
	 $data[$k]['father_occupation_name'] = $val['father_occupation_name'];
	 $data[$k]['mother_occupation_name'] = $val['mother_occupation_name'];
	 $data[$k]['typeofenquiry'] = $val['typeofenquiry'];
	 $data[$k]['sourceofenquiry'] = $val['sourceofenquiry'];
	 $data[$k]['enquirymindset'] = $val['enquirymindset'];
	 $data[$k]['whyesperanza'] = $val['whyesperanza'];
	 $data[$k]['howdoyouknowesp'] = $val['howdoyouknowesp'];
	 if($val['reference_parent_of_esperanza'] == 1){
	 $data[$k]['reference_parent_of_esperanza'] = 'Yes';
	 }else if($val['reference_parent_of_esperanza'] == 2){
		  $data[$k]['reference_parent_of_esperanza'] = 'No';
	 }
	 $data[$k]['comments'] = $val['comments'];
	
}	  

	return $data;
        
		}

}