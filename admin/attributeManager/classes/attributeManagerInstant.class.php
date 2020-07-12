<?php
/*
  $Id: attributeManagerInstant.class.php,v 1.0 21/02/06 Sam West$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
  
  Copyright © 2006 Kangaroo Partners
  http://kangaroopartners.com
  osc@kangaroopartners.com
*/

class attributeManagerInstant extends attributeManager {
	
	/**
	 * @access private
	 */
	var $intPID;
	
	/**
	 * __construct() assigns pid, calls the parent construct, registers page actions
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $intPID int
	 * @return void
	 */
	function attributeManagerInstant($intPID) {
		
		parent::attributeManager();
		
		$this->intPID = (int)$intPID;
		
		$this->registerPageAction('addAttributeToProduct','addAttributeToProduct');
		$this->registerPageAction('addOptionValueToProduct','addOptionValueToProduct');
		$this->registerPageAction('addNewOptionValueToProduct','addNewOptionValueToProduct');
		$this->registerPageAction('removeOptionFromProduct','removeOptionFromProduct');
		$this->registerPageAction('removeOptionValueFromProduct','removeOptionValueFromProduct');
		// QT Pro Plugin
		$this->registerPageAction('RemoveStockOptionValueFromProduct','RemoveStockOptionValueFromProduct');
		$this->registerPageAction('AddStockToProduct','AddStockToProduct');
		// QT Pro Plugin
		$this->registerPageAction('update','update');
		$this->registerPageAction('updateProductStockQuantity','updateProductStockQuantity');
		
		if(AM_USE_SORT_ORDER) {
			$this->registerPageAction('moveOptionUp','moveOptionUp');
			$this->registerPageAction('moveOptionDown','moveOptionDown');
			$this->registerPageAction('moveOptionValueUp','moveOptionValueUp');
			$this->registerPageAction('moveOptionValueDown','moveOptionValueDown');
		}
	}
	
	//----------------------------------------------- page actions

	/**
	 * Adds the selected attribute to the current product
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function addAttributeToProduct($get) {
		
		$this->getAndPrepare('option_id', $get, $optionId);
		$this->getAndPrepare('option_value_id', $get, $optionValueId);
		$this->getAndPrepare('price', $get, $price);
		$this->getAndPrepare('prefix', $get, $prefix);
		$this->getAndPrepare('sortOrder', $get, $sortOrder);
		
		$data = array(
			'products_id' => $this->intPID,
			'options_id' => $optionId,
			'options_values_id' => $optionValueId,
			'options_values_price' => $price,
			'price_prefix' => $prefix
		);
		if (AM_USE_SORT_ORDER) {
			$data[AM_FIELD_OPTION_VALUE_SORT_ORDER] = $sortOrder;
		}
		
		amDB::perform(TABLE_PRODUCTS_ATTRIBUTES, $data);
	}
	
	/**
	 * Adds an existing option value to a product
	 * @see addAttributeToProduct()
	 */
	function addOptionValueToProduct($get) {
		$this->addAttributeToProduct($get);
	}
	
	/**
	 * Adds a new option value to the database then assigns it to the product
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function addNewOptionValueToProduct($get) {
		$returnInfo = $this->addOptionValue($get);
		$get['option_value_id'] = $returnInfo['selectedOptionValue'];
		$this->addAttributeToProduct($get);
	}
	
	/**
	 * Removes a specific option and its option values from the current product
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function removeOptionFromProduct($get) {
		$this->getAndPrepare('option_id',$get,$optionId);
		amDB::query("delete from ".TABLE_PRODUCTS_ATTRIBUTES." where options_id = '$optionId' and products_id = '$this->intPID'");
	}
	
	/**
	 * Removes a specific option value from a the current product
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function removeOptionValueFromProduct($get) {
		$this->getAndPrepare('option_id',$get,$optionId);
		$this->getAndPrepare('option_value_id',$get,$optionValueId);
		amDB::query("delete from ".TABLE_PRODUCTS_ATTRIBUTES." where options_id = '$optionId' and options_values_id = '$optionValueId' and products_id = '$this->intPID'");
	}

// Begin QT Pro Plugin	
	/**
	 * Removes a specific stock option value from a the current product // for QT pro Plugin
	 * @access public
	 * @author Greg A. aka phocea - 
	 * @param $get $_GET
	 * @return void
	 */
	function RemoveStockOptionValueFromProduct($get) {
		$this->getAndPrepare('option_id',$get,$optionId);
		amDB::query("delete from ".TABLE_PRODUCTS_STOCK." where products_stock_id = '$optionId'");// and products_id = '$this->intPID'");
	}
	/**
	 * Removes a specific stock option value from a the current product // for QT pro Plugin
	 * @access public
	 * @author Greg A. aka phocea
	 * @param $get $_GET
	 * @return void
	 */
	/**
	 * Adds the selected attribute to the current product
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function addStockToProduct($get) {
		
//		$this->getAndPrepare('option_id', $get, $optionId);
//		$this->getAndPrepare('option_value_id', $get, $optionValueId);
//		$this->getAndPrepare('price', $get, $price);
//		$this->getAndPrepare('prefix', $get, $prefix);
//		$this->getAndPrepare('sortOrder', $get, $sortOrder);
		$this->getAndPrepare('stockQuantity',$get,$stockQuantity);
		
		amDB::query("delete from ".TABLE_PRODUCTS_STOCK." where products_stock_id = '$stockQuantity'");// and products_id = '$this->intPID'");
//		$data = array(
//			'products_id' => $this->intPID,
//			'options_id' => $optionId,
//			'options_values_id' => $optionValueId,
//			'options_values_price' => $price,
//			'price_prefix' => $prefix,
//			AM_FIELD_OPTION_VALUE_SORT_ORDER => $sortOrder
//		);
//		
//		amDB::perform(TABLE_PRODUCTS_ATTRIBUTES, $data);
	}
	
//	function addStockToProduct($get) {
//		customPrompt('debug','we are here');
//		$inputok = true;
//		// Work out how many option were sent
//		while(list($v1,$v2)=each($get)) {
//			if (preg_match("/^option(\d+)$/",$v1,$m1)) {
//				if (is_numeric($v2) and ($v2==(int)$v2)) $val_array[]=$m1[1]."-".$v2;
//        		else $inputok = false;
//      			}
//    		}
//    		
//    		$this->getAndPrepare('stockQuantity',$get,$stockQuantity);
//    		if (($inputok)) {
//    			sort($val_array, SORT_NUMERIC);
//    			$val=join(",",$val_array);
//    			$q=tep_db_query("select products_stock_id as stock_id from " . TABLE_PRODUCTS_STOCK . " where products_id ='$this->intPID' and products_stock_attributes='" . $val . "' order by products_stock_attributes");
//    			if (tep_db_num_rows($q)>0) {
//    				$stock_item=tep_db_fetch_array($q);
//    				$stock_id=$stock_item[stock_id];
//    				if ($stockQuantity=intval($stockQuantity)) {
//    					amDB::query("update " . TABLE_PRODUCTS_STOCK . " set products_stock_quantity=" . (int)$stockQuantity . " where products_stock_id=$stock_id");
//
//    				} else {
//    					amDB::query("delete from " . TABLE_PRODUCTS_STOCK . " where products_stock_id=$stock_id");
//        			}
//      			} else {
//        			amDB::query("insert into " . TABLE_PRODUCTS_STOCK . " values (0," . $this->intPID . ",'$val'," . (int)$stockQuantity . ")");
//        		}
//      			$q=tep_db_query("select sum(products_stock_quantity) as summa from " . TABLE_PRODUCTS_STOCK . " where products_id=" . (int)$VARS['product_id'] . " and products_stock_quantity>0");
//      			$list=tep_db_fetch_array($q);
//      			$summa= (empty($list[summa])) ? 0 : $list[summa];
//      			amDB::query("update " . TABLE_PRODUCTS . " set products_quantity=$summa where products_id=" . $this->intPID);
//      			if (($summa<1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
//        			amDB::query("update " . TABLE_PRODUCTS . " set products_status='0' where products_id=" . $this->intPI);
//      			}
//    		}
//	}

	/**
	 * Updates the quantity on the products stock table
	 * @author Phocea
	 * @param $get $_GET
	 * @return void
	 */
	function updateProductStockQuantity($get) {
		customprompt();
		$this->getAndPrepare('products_stock_id', $get, $products_stock_id);
		$this->getAndPrepare('productStockQuantity', $get, $productStockQuantity);		
		$data = array( 
			'product_stock_quantity' => $productStockQuantity
		);
		amDB::perform(TABLE_PRODUCTS_STOCK,$data, 'update',"products_stock_id='$products_stock_id'");
	}
// End QT Pro Plugin

	/**
	 * Updates the price and prefix in the products attribute table
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function update($get) {
		
		$this->getAndPrepare('option_id', $get, $optionId);
		$this->getAndPrepare('option_value_id', $get, $optionValueId);
		$this->getAndPrepare('price', $get, $price);
		$this->getAndPrepare('prefix', $get, $prefix);
		$this->getAndPrepare('sortOrder', $get, $sortOrder);
		
		$data = array( 
			'options_values_price' => $price,
			'price_prefix' => $prefix
		);
		if (AM_USE_SORT_ORDER) {
			$data[AM_FIELD_OPTION_VALUE_SORT_ORDER] = $sortOrder;
		}

		amDB::perform(TABLE_PRODUCTS_ATTRIBUTES,$data, 'update',"products_id='$this->intPID' and options_id='$optionId' and options_values_id='$optionValueId'");

	}
	
	//----------------------------------------------- page actions end
	
	/**
	 * Returns all or the options and values in the database
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @return array
	 */
	function getAllProductOptionsAndValues($reset = false) {
		if(0 === count($this->arrAllProductOptionsAndValues)|| true === $reset) {
			$this->arrAllProductOptionsAndValues = array();
			
			$allOptionsAndValues = $this->getAllOptionsAndValues();
			
			$queryString = "select * from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '$this->intPID' order by ";			
			$queryString .= !AM_USE_SORT_ORDER ?  "options_id" : AM_FIELD_OPTION_VALUE_SORT_ORDER;
			$query = amDB::query($queryString);
			
			$optionsId = null;
			while($res = amDB::fetchArray($query)) {
				if($res['options_id'] != $optionsId) {
					$optionsId = $res['options_id'];
					$this->arrAllProductOptionsAndValues[$optionsId]['name'] = $allOptionsAndValues[$optionsId]['name'];
				}
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['options_values_id']]['name'] = $allOptionsAndValues[$optionsId]['values'][$res['options_values_id']];
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['options_values_id']]['price'] = $res['options_values_price'];
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['options_values_id']]['prefix'] = $res['price_prefix'];
				if (AM_USE_SORT_ORDER) {
					$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['options_values_id']]['sortOrder'] = $res[AM_FIELD_OPTION_VALUE_SORT_ORDER];
				}
			}
		}
		return $this->arrAllProductOptionsAndValues;
	}
	
	function moveOptionUp() {
		$this->moveOption();
	}
	
	function moveOptionDown() {
		$this->moveOption('down');
	}
	
	function moveOption($direc = 'up') {
		
	}
	
	function moveOptionValueUp() {
		$this->moveOptionValueUp();
	}
	
	function moveOptionValueDown() {
		$this->moveOptionValueDown();
	}
	
	function moveOptionValue($direc = 'up') {
		
	}
}

?>