<?php 

class create_assets{
var $trans_type; // order/grn/invoice (direct)
	var $line_items;/*Only used for modification of existing orders otherwise only established when order committed */
        var $item_category;
        var $item_sub_category;
        var $item_id;
        var $qty;
	var $lines_on_order = 0;
		// array of taxes manually inserted during sales invoice entry (direct invoice)
	function __construct()
	{
		/*Constructor function initialises a new purchase order object */
		$this->line_items = array();
                $this->lines_on_order =  0;
	}
function add_to_order($line_no, $stock_id, $qty, $category,$sub_category)
	{
		if (isset($qty) && $qty != 0)
		{
		$this->line_items[$line_no] = new assets_line_details($line_no, $stock_id, $qty, $category,$sub_category);
                        
                       
			$this->lines_on_order++;
			return 1;
		} 
		return 0;
	}
        
        
        function update_order_item($line_no, $qty, $price, $req_del_date, $description="")
	{
		display_error($gst);   die();
		if ($description != "")
			$this->line_items[$line_no]->item_description = $description;
		$this->line_items[$line_no]->quantity = $qty;
		$this->line_items[$line_no]->price = $price;
		$this->line_items[$line_no]->req_del_date = $req_del_date;
		$this->line_items[$line_no]->item_description = $description;
//		$this->line_items[$line_no]->gst = $gst;
//                $this->line_items[$line_no]->gst_amt = $gst_amt;
//		$this->line_items[$line_no]->cst = $cst;
//                $this->line_items[$line_no]->cst_amt = $cst_amt;
//		$this->line_items[$line_no]->ist = $ist;
//                $this->line_items[$line_no]->ist_amt = $ist_amt;
//                $this->line_items[$line_no]->hsn_no = $hsn_no;
	}

	function remove_from_order($line_no)
	{
		/*
		echo 'Love'. $line_no;
		echo '<pre>';
		print_r($this->line_items);*/

		//unset($this->line_items[$line_no]);
		array_splice($this->line_items, $line_no, 1);
	}

}


class assets_line_details
{

	var $line_no;
	var $stock_id;
        var $quantity;
	 var $item_category;
        var $item_sub_category;
        var $item_id;
	function __construct($line_no, $stock_id, $qty, $category,$sub_category)
	{
        
		/* Constructor function to add a new LineDetail object with passed params */
		$this->line_no = $line_no;
		$this->stock_id = $stock_id;
		$this->quantity = $qty;
                $this->item_category = $category;
                        $this->item_sub_category = $sub_category;
           //   display_error($this->ist);
             // die();
		
	}
}

