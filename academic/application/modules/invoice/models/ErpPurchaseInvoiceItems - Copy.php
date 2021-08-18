<?php
/**
 * Application_Model_ErpInventoryGrnItems
 *
 * @Framework Zend Framework
 * @Powered By TIS
 * @category   ERP Product
 * @copyright  Copyright (c) 2014-2014 Techintegrasolutions Pvt Ltd.
 * (http://www.techintegrasolutions.com)
 */
class Invoice_Model_ErpPurchaseInvoiceItems extends Zend_Db_Table_Abstract {

    protected $_name = 'erp_purchse_invoice_items';
    protected $_id = 'purchase_invoice_item_id';

    /**
     * Set Primary Key Id as a Parameter 
     *
     * @param string $id
     * @return single dimention array
     */
    public function getRecord($id) {
        $select = $this->_db->select()
                ->from($this->_name)
				 ->joinLeft(array("items_master" => "erp_items_master"), "items_master.item_id=erp_purchse_invoice_items.item_id", array("item_name"))
				 ->joinLeft(array("items_category_master" => "erp_items_category_master"), "items_category_master.category_id=erp_purchse_invoice_items.purchase_grn_category_id", array("category_name"))
				 ->joinLeft(array("wm" => "erp_item_uom_master"), "wm.item_uom_id=items_master.item_uom_id", array("item_uom_unit", "item_uom_title"))
                ->where("erp_purchse_invoice_items.purchase_invoice_no=?", $id);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    /**
     * Retrieve all Records
     *
     * @return Array
     */
    public function getRecords() {
        $select = $this->_db->select()
                ->from($this->_name);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }

    public function getGrnItems($inventory_grn_no) {
        $select = $this->_db->select()
                ->from(array("grn_items" => $this->_name), array("inventory_grn_actual_quantity", "inventory_grn_item_approved_quantity", "inventory_grn_item_rejected_quantity", "remarks"))
                ->joinLeft(array("items_master" => "erp_items_master"), "items_master.item_id=grn_items.inventory_grn_item_id", array("item_name"))
				->joinLeft(array("wm" => "erp_item_uom_master"), "wm.item_uom_id=items_master.item_uom_id", array("item_uom_unit", "item_uom_title"))
                ->where("inventory_grn_no=?", $inventory_grn_no);
        $result = $this->getAdapter()
                ->fetchAll($select);
        return $result;
    }
}