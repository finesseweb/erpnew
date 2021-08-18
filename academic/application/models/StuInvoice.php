<?php 

class Application_Model_StuInvoice extends Application_Model_GlobalDbModel
{
  public $_name = 'fa_stu_invoice_items';
  protected $_id = 'id';
function __construct() {
    parent::__construct('erp');
    } 
    
}

