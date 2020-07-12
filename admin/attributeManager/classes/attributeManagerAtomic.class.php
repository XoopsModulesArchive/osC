<?php
/*
  $Id: attributeManager.class.php,v 1.0 21/02/06 Sam West$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
  
  Copyright © 2006 Kangaroo Partners
  http://kangaroopartners.com
  osc@kangaroopartners.com
*/

class attributeManagerAtomic extends attributeManager {
	
	/**
	 * Holder for a reference to the session variable for storing temp data
	 * @access private
	 */
	var $arrSessionVar = array();
	
	/**
	 * __constrct - Assigns the session variable and calls the parent construct registers page actions
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $arrSessionVar array - passed by Ref
	 * @return void
	 */
	function attributeManagerAtomic(&$arrSessionVar) {
		
		parent::attributeManager();
		$this->arrSessionVar = &$arrSessionVar;
		
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
  if(isset($get['option_id']) === true) {
    $this->getAndPrepare('option_id', $get, $getArray['option_id']);
  }

  if(isset($get['option_value_id']) === true) {
    $this->getAndPrepare('option_value_id', $get, $getArray['option_value_id']);
  }

  if(isset($get['price']) === true) {
    $this->getAndPrepare('price', $get, $getArray['price']);
  }

  if(isset($get['prefix']) === true) {
    $this->getAndPrepare('prefix', $get, $getArray['prefix']);
  }

  if(isset($get['sortOrder']) === true) {
    $this->getAndPrepare('sortOrder', $get, $getArray['sortOrder']);
  }

//	echo '<br><br>Array arrSessionVar:: <br><br>';
//	print_r($getArray);

  $this->arrSessionVar[] = $getArray;
}

	
	/**
	 * Adds an existing option value to a product
	 * @see addAttributeToProduct()
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function addOptionValueToProduct($get) {
		$this->addAttributeToProduct($get);
	}
	
	/**
	 * Adds a new option value to the session then to the product
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function addNewOptionValueToProduct($get) {
		$returnInfo = $this->addOptionValue($get);
		$get['option_value_id'] = $returnInfo['selectedOptionValue'];
		$this->addAttributeToProduct($get);
		return false;
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
		foreach($this->arrSessionVar as $id => $res) 
			if(($res['options_id'] == $optionId)) 
				unset($this->arrSessionVar[$id]);
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
		
		foreach($this->arrSessionVar as $id => $res) 
			if(($res['options_id'] == $optionId) && ($res['options_values_id'] == $optionValueId)) 
				unset($this->arrSessionVar[$id]);
	}
// QT pro
	/**
	 * Updates the quantity on the products stock table
	 * @access public
	 * @author Phocea
	 * @param $get $_GET
	 * @return void
	 */
    function AddStockToProduct($get) {
      customprompt();
      $this->getAndPrepare('stockQuantity',$get,$stockQuantity);
      //$this->getAndPrepare('option_id', $get, $optionId);
      //$this->getAndPrepare('option_value_id', $get, $optionValueId);
      //$this->getAndPrepare('price', $get, $price);
      //$this->getAndPrepare('prefix', $get, $prefix);
      //$this->getAndPrepare('sortOrder', $get, $sortOrder);
      
      $this->arrSessionVar[] = array(
      'product_stock_quantity' => $productStockQuantity
      );

    }

// QT pro
		
	/**
	 * Updates the price and prefix in the products attribute table
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @param $get $_GET
	 * @return void
	 */
	function update($get) {
          if(isset($get['option_id']) === true) {
            $this->getAndPrepare('option_id', $get, $getArray['option_id']);
          }
  
          if(isset($get['option_value_id']) === true) {
            $this->getAndPrepare('option_value_id', $get, $getArray['option_value_id']);
          }
  
          if(isset($get['price']) === true) {
            $this->getAndPrepare('price', $get, $getArray['price']);
          }
  
          if(isset($get['prefix']) === true) {
            $this->getAndPrepare('prefix', $get, $getArray['prefix']);
          }
  
          if(isset($get['sortOrder']) === true) {
            $this->getAndPrepare('sortOrder', $get, $getArray['sortOrder']);
          }
  
          $this->arrSessionVar[] = $getArray;
		
/*		$this->getAndPrepare('option_id', $get, $optionId);
		$this->getAndPrepare('option_value_id', $get, $optionValueId);
		$this->getAndPrepare('price', $get, $price);
		$this->getAndPrepare('prefix', $get, $prefix);
		$this->getAndPrepare('sortOrder', $get, $sortOrder);
		
		foreach($this->arrSessionVar as $id => $res) {
			if(($res['options_id'] == $optionId) && ($res['options_values_id'] == $optionValueId)) {
				$this->arrSessionVar[$id]['options_values_price'] = $price;
				$this->arrSessionVar[$id]['price_prefix'] = $prefix;
				if (AM_USE_SORT_ORDER) {
					$this->arrSessionVar[$id][AM_FIELD_OPTION_VALUE_SORT_ORDER] = $sortOrder;
				}
			}
		}*/
	}
	
	//----------------------------------------------- page actions end
	
	/**
	 * Returns all of the products options and values in the session
	 * @access public
	 * @author Sam West aka Nimmit - osc@kangaroopartners.com
	 * @return array
	 */
	function getAllProductOptionsAndValues($reset = false) {
		if(0 === count($this->arrAllProductOptionsAndValues) || true === $reset) {
			$this->arrAllProductOptionsAndValues = array();
			$allOptionsAndValues = $this->getAllOptionsAndValues();

//  		echo '<br><br>Array ARRSESSVAR:: <br><br>';
//    	print_r($this->arrSessionVar);

			$optionsId = null;
			foreach($this->arrSessionVar as $id => $res) {
				if($res['option_id'] != $optionsId) {
					$optionsId = $res['option_id'];
					$this->arrAllProductOptionsAndValues[$optionsId]['name'] = $allOptionsAndValues[$optionsId]['name'];
				}
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['option_value_id']]['name'] = $allOptionsAndValues[$optionsId]['values'][$res['option_value_id']];
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['option_value_id']]['price'] = $res['option_value_price'];
				$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['option_value_id']]['prefix'] = $res['prefix'];
				if (AM_USE_SORT_ORDER) {
					$this->arrAllProductOptionsAndValues[$optionsId]['values'][$res['option_value_id']]['sortOrder'] = $res['sortOrder'];
				}
			}
		}
		return $this->arrAllProductOptionsAndValues;
	}
}
?>
