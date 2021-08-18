<?php

class Application_Model_GlTrans extends Application_Model_GlobalDbModel
{
  public $_name = 'fa_gl_trans';
  protected $_id = 'counter';
function __construct() {
    parent::__construct('erp');
    } 
    
    
    public function getDebitCreditAccount($course_fee){
            $select = $this->_db->select()
				->from('fa_kv_hrm_finance_setup',array('allowance_debit_gl_code as debit', 'allowance_credit_gl_code as credit'))
				->where("type =?",$course_fee)
				->where("inactive =?",0);
                                      // echo $select; exit;
				$result = $this->getAdapter() 
                                ->fetchRow($select);  
                        $x[0] = $result['debit'];
                        $x[1] =$result['credit'];
                        return $x;
    }
    
}