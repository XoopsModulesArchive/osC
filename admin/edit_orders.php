<?php
/*
  $Id: edit_orders.php,v 1.21 2004/09/18 15:46:00 jrb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
  
  Written by Jonathan Hilgeman of SiteCreative.com (osc@sitecreative.com)
- Updated By Jesse Ryan Barrick & Christian Binkert

  Version History
  ---------------------------------------------------------------
  
  3/9/05
  Source: 1.61 - SECOND ATTEMPT to resolve tax problems, targeting multi-currency environments, 
  such as Canadian stores.
  The biggest problem I could see is the fact the product is calculated in default currency and converted
  to the target currency before presentation. The shipping/discount is calculated in target currency
  in the beginning. That's why we had discrepancy.
  I also removed custom code that did not serve any purpose.

  18/02/05
  Source: 1.61 - FIRST ATTEMP to solve tax problems and calculation 'errors'

  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  THIS IS JUST AN ATTEMP TO FIND/SOLVE THESE PROBLEMS ! IN OUR SHOP IT SEEMS
  TO WORK PERFECT NOW - BUT DON'T COPY THE FILE ON YOUR edit_orders.php
  WITHOUT BEING SURE WHAT YOU ARE DOING.
  
  YOU CAN FIND ALL MY CHANGES BY SEARCHING FOR 'Michel Haase' ...
  
  IT WILL TAKE MAYBE 1 HOUR TO THINK ABOUT ALL MY CHANGES. FEEL FREE TO USE
  MY IDEAS IN THIS FILE FOR A BETTER VERSION OF 'ORDER EDITOR'.
  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    

  04/01/05
  1.60 - Huge list of changes ;-)

  18/09/04
  1.58 - Changed Order Edit page to show full Credit Card Number

  16/07/04
  1.56 - Added a check for missing ot_subtotal on update

  08/08/03
  1.2a - Fixed a query problem on osC 2.1 stores.

  08/08/03
  1.2 - Added more recommendations to the instructions.
        Added "Customer" fields for editing on osC 2.2.
        Corrected "Billing" fields so they update correctly.
        Added Company and Suburb Fields.
        Added optional shipping tax variable.
        First (and hopefully last) fix for currency formatting.

  08/08/03
  1.1 - Added status editing (fixed order status bug from 1.0).
        Added comments editing. (with compatibility for osC 2.1)
        Added customer notifications.
        Added some additional information to the instructions file.
        Fixed bug with product names containing single quotes.
  
  08/07/03      
  1.0 - Original Release.
  
  To Do in Version 1.3
  ---------------------------------------------------------------
  
  Note from the author
  ---------------------------------------------------------------
  This tool was designed and tested on osC 2.2 Milestone 2.2,
  but may work for other versions, as well. Most database changes
  were minor, so getting it to work on other versions may just
  need some tweaking. Hope this helps make your life easier!
  
  - Jonathan Hilgeman, August 7th, 2003  
*/

  include ('../../../include/cp_header.php');
  require('includes/application_top.php');
  include('../includes/classes/customer_group.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  include(DIR_WS_CLASSES . 'order.php');

  xoops_cp_header();

// START CONFIGURATION ################################

// Correction tax pre-values (Michel Haase, 2005-02-18)
// -> What was this ? Why 20.0, 20.0, 7.6 and 7.6 ???
//    It's used later in a 'hidden way' an produces unlogical results ...

// Optional Tax Rates, e.g. shipping tax of 17.5% is "17.5"
// $AddCustomTax = "20.0"; // class "ot_custom", used for all unknown total modules
  $AddCustomTax = "19.6";  // new
// $AddShippingTax = "20.0"; // class "ot_shippping"
  $AddShippingTax = "19.6";  // new
// $AddLevelDiscountTax = "7.6"; // class "ot_lev_discount"
  $AddLevelDiscountTax = "19.6";  // new
// $AddCustomerDiscountTax = "7.6"; // class "ot_customer_discount"
  $AddCustomerDiscountTax = "19.6";  // new
	
// END OF CONFIGURATION ################################


  // New "Status History" table has different format.
  $OldNewStatusValues = (tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "old_value") && tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "new_value"));
  $CommentsWithStatus = tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "comments");
  $SeparateBillingFields = tep_field_exists(TABLE_ORDERS, "billing_name");
  
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "'");
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                               'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : 'edit');

  // Update Inventory Quantity
  $order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$oID . "'");

  if (tep_not_null($action)) {
    switch ($action) {
    	
	// 1. UPDATE ORDER ###############################################################################################
	case 'update_order':
		
		$oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
		$order = new order($oID);
		$status = tep_db_prepare_input($HTTP_POST_VARS['status']);
		
		// 1.1 UPDATE ORDER INFO #####
		
		$UpdateOrders = "update " . TABLE_ORDERS . " set 
			customers_name = '" . tep_db_input(stripslashes($update_customer_name)) . "',
			customers_company = '" . tep_db_input(stripslashes($update_customer_company)) . "',
			customers_street_address = '" . tep_db_input(stripslashes($update_customer_street_address)) . "',
			customers_suburb = '" . tep_db_input(stripslashes($update_customer_suburb)) . "',
			customers_city = '" . tep_db_input(stripslashes($update_customer_city)) . "',
			customers_state = '" . tep_db_input(stripslashes($update_customer_state)) . "',
			customers_postcode = '" . tep_db_input($update_customer_postcode) . "',
			customers_country = '" . tep_db_input(stripslashes($update_customer_country)) . "',
			customers_telephone = '" . tep_db_input($update_customer_telephone) . "',
			customers_email_address = '" . tep_db_input($update_customer_email_address) . "',";
		
		if($SeparateBillingFields) { 
		// Original: all database fields point to $update_billing_xxx, now they are updated with the same values as the customer fields
		$UpdateOrders .= "billing_name = '" . tep_db_input(stripslashes($update_customer_name)) . "',
			billing_company = '" . tep_db_input(stripslashes($update_customer_company)) . "',
			billing_street_address = '" . tep_db_input(stripslashes($update_customer_street_address)) . "',
			billing_suburb = '" . tep_db_input(stripslashes($update_customer_suburb)) . "',
			billing_city = '" . tep_db_input(stripslashes($update_customer_city)) . "',
			billing_state = '" . tep_db_input(stripslashes($update_customer_state)) . "',
			billing_postcode = '" . tep_db_input($update_customer_postcode) . "',
			billing_country = '" . tep_db_input(stripslashes($update_customer_country)) . "',";
		}
		
		$UpdateOrders .= "delivery_name = '" . tep_db_input(stripslashes($update_delivery_name)) . "',
			delivery_company = '" . tep_db_input(stripslashes($update_delivery_company)) . "',
			delivery_street_address = '" . tep_db_input(stripslashes($update_delivery_street_address)) . "',
			delivery_suburb = '" . tep_db_input(stripslashes($update_delivery_suburb)) . "',
			delivery_city = '" . tep_db_input(stripslashes($update_delivery_city)) . "',
			delivery_state = '" . tep_db_input(stripslashes($update_delivery_state)) . "',
			delivery_postcode = '" . tep_db_input($update_delivery_postcode) . "',
			delivery_country = '" . tep_db_input(stripslashes($update_delivery_country)) . "',
			payment_method = '" . tep_db_input($update_info_payment_method) . "',
			cc_type = '" . tep_db_input($update_info_cc_type) . "',
			cc_owner = '" . tep_db_input($update_info_cc_owner) . "',";
			
		if(substr($update_info_cc_number,0,8) != "(Last 4)") {
		  $UpdateOrders .= "cc_number = '$update_info_cc_number',";
		}		
		$UpdateOrders .= "cc_expires = '$update_info_cc_expires',
			orders_status = '" . tep_db_input($status) . "'";
		
		if(!$CommentsWithStatus) {
			$UpdateOrders .= ", comments = '" . tep_db_input($comments) . "'";
		}
		$UpdateOrders .= " where orders_id = '" . tep_db_input($oID) . "';";

		tep_db_query($UpdateOrders);
		$order_updated = true;

    $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
    $check_status = tep_db_fetch_array($check_status_query);
	
		// 1.2 UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####
		
		if ($order->info['orders_status'] != $status) {
		
			// Notify Customer
      $customer_notified = '0';
			if (isset($HTTP_POST_VARS['notify']) && ($HTTP_POST_VARS['notify'] == 'on')) {
			  $notify_comments = '';
			  if (isset($HTTP_POST_VARS['notify_comments']) && ($HTTP_POST_VARS['notify_comments'] == 'on')) {
			    $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
			  }
			  $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
			  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
			  $customer_notified = '1';
			}			  
          		
			// "Status History" table has gone through a few different changes, so here are different versions of the status update. 
			// NOTE: Theoretically, there shouldn't be a orders_status field in the ORDERS table. It should really just use the latest value from this status history table.

/* ###################### Element supprim� afin d'�viter la double insertion de statut
			if($CommentsWithStatus) {
			tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " 
				(orders_id, orders_status_id, date_added, customer_notified, comments) 
				values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), " . tep_db_input($customer_notified) . ", '" . tep_db_input($comments)  . "')");
			
			} else {
				if($OldNewStatusValues) {
				  tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " 
					(orders_id, new_value, old_value, date_added, customer_notified) 
					values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', '" . $order->info['orders_status'] . "', now(), " . tep_db_input($customer_notified) . ")");
	
				} else {
			  tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " 
					(orders_id, orders_status_id, date_added, customer_notified) 
					values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), " . tep_db_input($customer_notified) . ")");

				}
			}
###################### Fin modification */
		}

// Mise � jour champs commentaires - R�soudre le bug lors pour envoi de mail quand le statut est toujours identique
 
 
 
 /* code ok sauf qu'il envoie toujours les mails
       if ($check_status['orders_status'] == $status) {
      	if ( tep_not_null($comments) && ($customer_notified = '0')){
          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");
         } else
      	if ( tep_not_null($comments) && ($customer_notified = '1')){

			  $customer_notified = '1';
				if (($check_status['orders_status'] = $status) && tep_not_null($comments) && ($customer_notified = '1')) {
				  $notify_comments = '';
				  if (isset($HTTP_POST_VARS['notify_comments']) && ($HTTP_POST_VARS['notify_comments'] == 'on')) {
					  $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
					  $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
					  tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
					  $customer_notified = '1';
	          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");
			    }
			   }	
       }
}
*/


// Code Ok sauf qu'il envoie 2 mails et inscrit 2 fois le process en cours
       if ( ($check_status['orders_status'] = $status) || tep_not_null($comments)) {
          tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . (int)$oID . "'");

          $customer_notified = '0';
          if (isset($HTTP_POST_VARS['notify']) && ($HTTP_POST_VARS['notify'] == 'on')) {
            $notify_comments = '';
            if (isset($HTTP_POST_VARS['notify_comments']) && ($HTTP_POST_VARS['notify_comments'] == 'on')) {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
            }

            $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            $customer_notified = '1';
          }
          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");
          $order_updated = true;
        }

// fin mise � jour


		// 1.3 UPDATE PRODUCTS #####
		
		$RunningSubTotal = 0;
		$RunningTax = 0;

    // Do pre-check for subtotal field existence (CWS)
		$ot_subtotal_found = false;
		
    foreach($update_totals as $total_details) {
		  extract($total_details,EXTR_PREFIX_ALL,"ot");
			if($ot_class == "ot_subtotal") {
			  $ot_subtotal_found = true;
    	break;
			}
		}
        
		// 1.3.1 Update orders_products Table
		foreach($update_products as $orders_products_id => $products_details)	{
		
			// 1.3.1.1 Update Inventory Quantity
			$order = tep_db_fetch_array($order_query);
			if ($products_details["qty"] != $order['products_quantity']) {
				$quantity_difference = ($products_details["qty"] - $order['products_quantity']);
					tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity - " . $quantity_difference . ", products_ordered = products_ordered + " . $quantity_difference . " where products_id = '" . (int)$order['products_id'] . "'");
			}

			if($products_details["qty"] > 0) { // a.) quantity found --> add to list & sum
			
				$Query = "update " . TABLE_ORDERS_PRODUCTS . " set
					products_model = '" . $products_details["model"] . "',
					products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
					final_price = '" . $products_details["final_price"] . "',
					products_tax = '" . $products_details["tax"] . "',
					products_quantity = '" . $products_details["qty"] . "'
					where orders_products_id = '$orders_products_id';";
				tep_db_query($Query);
                        	
		        // Update Tax and Subtotals: please choose sum WITH or WITHOUT tax, but activate only ONE version ;-)

// Correction tax calculation (Michel Haase, 2005-02-18)
// -> correct calculation, but why there is a division by 20 and afterwards a mutiplication with 20 ???
//    -> no changes made
				//$RunningSubTotal += (tep_add_tax(($products_details["qty"] * $products_details["final_price"]), $products_details["tax"])*20)/20; // version WITH tax
				$RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
				
				$RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));
            	
				// Update Any Attributes
				if(IsSet($products_details[attributes]))
				{
					foreach($products_details["attributes"] as $orders_products_attributes_id => $attributes_details)
					{
						$Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
							products_options = '" . $attributes_details["option"] . "',
							products_options_values = '" . $attributes_details["value"] . "'
							where orders_products_attributes_id = '$orders_products_attributes_id';";
						tep_db_query($Query);
					}
				}
				
			} else { // b.) null quantity found --> delete
			
				$Query = "delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id = '$orders_products_id';";
				tep_db_query($Query);
				$order = tep_db_fetch_array($order_query);
				
				if ($products_details["qty"] != $order['products_quantity']){
					$quantity_difference = ($products_details["qty"] - $order['products_quantity']);
					tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity - " . $quantity_difference . ", products_ordered = products_ordered + " . $quantity_difference . " where products_id = '" . (int)$order['products_id'] . "'");
				}
				
				$Query = "delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '$orders_products_id';";
				tep_db_query($Query);
			}
		}
		
		// 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####
		
		foreach($update_totals as $total_index => $total_details)	{
			extract($total_details,EXTR_PREFIX_ALL,"ot");
			
// Correction tax calculation (Michel Haase, 2005-02-18)
// Correction tax calculation (Shimon Pozin, 2005-09-03) 
// Here is the major caveat: the product is priced in default currency, while shipping etc. are priced in target currency. We need to convert target currency
// into default currency before calculating RunningTax (it will be converted back before display)
			if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
				$order = new order($oID);
				$RunningTax += $ot_value * $products_details['tax'] / $order->info['currency_value'] / 100 ; // corrected tax by cb
			}
		}
		
		// exit;
		// 1.5 UPDATE TOTALS #####
		
		$RunningTotal = 0;
		$sort_order = 0;
			
			// 1.5.1 Do pre-check for Tax field existence
			$ot_tax_found = 0;
			foreach($update_totals as $total_details)	{
				extract($total_details,EXTR_PREFIX_ALL,"ot");
				if($ot_class == "ot_tax")
				{
					$ot_tax_found = 1;
					break;
				}
			}
			
			// 1.5.2. Summing up total
			foreach($update_totals as $total_index => $total_details)	
			
			{
			
		 	 // 1.5.2.1 Prepare Tax Insertion			
			extract($total_details,EXTR_PREFIX_ALL,"ot");
			
			// inserisce la tassa se non la trova oppure ??
			if (trim(strtolower($ot_title)) == "iva" || trim(strtolower($ot_title)) == "iva:") 
				{
					if ($ot_class != "ot_tax" && $ot_tax_found == 0) 
					{
						// Inserting Tax
						$ot_class = "ot_tax";
						$ot_value = "x"; // This gets updated in the next step
						$ot_tax_found = 1;
					}
				}
				
			  // 1.5.2.2 Update ot_subtotal, ot_tax, and ot_total classes
				if (trim($ot_title) && trim($ot_value)) 
				
				{
				
					$sort_order++;
					if ($ot_class == "ot_subtotal") {
						$ot_value = $RunningSubTotal;
					}						
					if ($ot_class == "ot_tax") {
						$ot_value = $RunningTax;
						// print "ot_value = $ot_value<br>\n";
					}

				    // Check for existence of subtotals (CWS)                      
					if ($ot_class == "ot_total") 
						{

// Correction tax calculation (Michel Haase, 2005-02-18)
// I can't find out, WHERE the $RunningTotal is calculated - but the subtraction of the tax was wrong (in our shop)
//				          $ot_value = $RunningTotal-$RunningTax;
				          $ot_value = $RunningTotal;
				         		          
				          if ( !$ot_subtotal_found ) 
				          { // There was no subtotal on this order, lets add the running subtotal in.
				            //   $ot_value +=  $RunningSubTotal;
				          }
				       //   print $ot_value;
				       //   exit;
				         }
									
					// Set $ot_text (display-formatted value)

// Correction of number_format - German format (Michel Haase, 2005-02-18)
				    // $ot_text = "\$" . number_format($ot_value, 2, ',', '');

					$order = new order($oID);
					$ot_text = $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']);
						
					if ($ot_class == "ot_total") {
						$ot_text = "<b>" . $ot_text . "</b>";
					}
					
					if($ot_total_id > 0) { // Already in database --> Update
						$Query = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' SET
							title = "' . $ot_title . '",
							text = "' . $ot_text . '",
							value = "' . $ot_value . '",
							sort_order = "' . $sort_order . '"
							WHERE orders_total_id = "' . $ot_total_id . '"';
						tep_db_query($Query);
					} else { // New Insert
						$Query = 'INSERT INTO ' . TABLE_ORDERS_TOTAL . ' SET
							orders_id = "' . $oID . '",
							title = "' . $ot_title . '",
							text = "' . $ot_text . '",
							value = "' . $ot_value . '",
							class = "' . $ot_class . '",
							sort_order = "' . $sort_order . '"';
						tep_db_query($Query);
					}
					
					if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
						// Again, because products are calculated in terms of default currency, we need to align shipping, custom etc. values with default currency
						$RunningTotal += $ot_value / $order->info['currency_value'];
					}
					else
					{
						$RunningTotal += $ot_value;
					}
				
						//	print $ot_value."<br>";
				}
				
			elseif (($ot_total_id > 0) && ($ot_class != "ot_shipping")) { // Delete Total Piece
				
					$Query = "delete from " . TABLE_ORDERS_TOTAL . " where orders_total_id = '$ot_total_id'";
					tep_db_query($Query);
				}

			}
//	  print "Totale ".$RunningTotal;
//	  exit;		

		// 1.6 SUCCESS MESSAGE #####
		
		if ($order_updated)	{
			$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
		}

		tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
		
	break;

	// 2. ADD A PRODUCT ###############################################################################################
	case 'add_product':
	
		if($step == 5)
		{
			// 2.1 GET ORDER INFO #####
			
			$oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
			$order = new order($oID);

			$AddedOptionsPrice = 0;

			// 2.1.1 Get Product Attribute Info
			if(IsSet($add_product_options))
			{
				foreach($add_product_options as $option_id => $option_value_id)
				{
					$result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id=pa.options_id LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pov.products_options_values_id=pa.options_values_id WHERE products_id='$add_product_products_id' and options_id=$option_id and options_values_id=$option_value_id and po.language_id = '" . (int)$languages_id . "' and pov.language_id = '" . (int)$languages_id . "'");
					$row = tep_db_fetch_array($result);
					extract($row, EXTR_PREFIX_ALL, "opt");
					$AddedOptionsPrice += $opt_options_values_price;
					$option_value_details[$option_id][$option_value_id] = array ("options_values_price" => $opt_options_values_price);
					$option_names[$option_id] = $opt_products_options_name;
					$option_values_names[$option_value_id] = $opt_products_options_values_name;
				}
			}

			// 2.1.2 Get Product Info
			$InfoQuery = "select p.products_model,p.products_price,pd.products_name,p.products_tax_class_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id where p.products_id='$add_product_products_id' and pd.language_id = '" . (int)$languages_id . "'";
			$result = tep_db_query($InfoQuery);

			$row = tep_db_fetch_array($result);
			extract($row, EXTR_PREFIX_ALL, "p");
			
			// risolviamo il bug delle offerte
			$rs_offerte = tep_db_query("select * from " . TABLE_SPECIALS . " where products_id ='". $add_product_products_id."'");
			$offerte = tep_db_fetch_array($rs_offerte);
			
			
			if ($offerte) 
			{
			$p_products_price = $offerte['specials_new_products_price'];
			}
			
			// Following functions are defined at the bottom of this file
			$CountryID = tep_get_country_id($order->delivery["country"]);
			$ZoneID = tep_get_zone_id($CountryID, $order->delivery["state"]);
			
			$ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);
			
			// 2.2 UPDATE ORDER #####
			
			$Query = "insert into " . TABLE_ORDERS_PRODUCTS . " set
				orders_id = $oID,
				products_id = $add_product_products_id,
				products_model = '$p_products_model',
				products_name = '" . str_replace("'", "&#39;", $p_products_name) . "',
				products_price = '$p_products_price',
				final_price = '" . ($p_products_price + $AddedOptionsPrice) . "',
				products_tax = '$ProductsTax',
				products_quantity = $add_product_quantity;";
			tep_db_query($Query);
			$new_product_id = tep_db_insert_id();
			
			// 2.2.1 Update inventory Quantity
			tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity - " . $add_product_quantity . ", products_ordered = products_ordered + " . $add_product_quantity . " where products_id = '" . $add_product_products_id . "'");

			if (IsSet($add_product_options)) {
				foreach($add_product_options as $option_id => $option_value_id) {
					$Query = "insert into " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
						orders_id = $oID,
						orders_products_id = $new_product_id,
						products_options = '" . $option_names[$option_id] . "',
						products_options_values = '" . tep_db_input($option_values_names[$option_value_id]) . "',
						options_values_price = '" . $option_value_details[$option_id][$option_value_id]["options_values_price"] . "',
						price_prefix = '+';";
					tep_db_query($Query);
				}
			}
			
			// 2.2.2 Calculate Tax and Sub-Totals
			$order = new order($oID);
			$RunningSubTotal = 0;
			$RunningTax = 0;

			for ($i=0; $i<sizeof($order->products); $i++) {

// Correction of $RunningSubTotal - our SubTotal in our shop is WITH TAX (Michel Haase, 2005-02-18)
// -> in line 240 (or near) there was the choice WITH/WITHOUT tax; if WITH tax, then you have to
// calculate it here also (or ?) !!!
			  $RunningSubTotal += ($order->products[$i]['qty'] * $order->products[$i]['final_price']);
			  //$RunningSubTotal += (tep_add_tax(($order->products[$i]['qty'] * $order->products[$i]['final_price']), $order->products[$i]['tax'])*20)/20;

			  $RunningTax += (($order->products[$i]['tax'] / 100) * ($order->products[$i]['qty'] * $order->products[$i]['final_price']));			
			}
			
			// 2.2.2.1 Tax
// Correction of number_format - German format (Michel Haase, 2005-02-18)
			$Query = "update " . TABLE_ORDERS_TOTAL . " set
				text = '" . number_format($RunningTax, 2, ',', '') . ' ' . $order->info['currency'] . "', 
				value = '" . $RunningTax . "'
				where class='ot_tax' and orders_id=$oID";
			tep_db_query($Query);

			// 2.2.2.2 Sub-Total
// Correction of number_format - German format (Michel Haase, 2005-02-18)
			$Query = "update " . TABLE_ORDERS_TOTAL . " set
				text = '" . number_format($RunningSubTotal + $RunningTax, 2, ',', '') . ' ' . $order->info['currency'] . "',
				value = '" . $RunningSubTotal . "'
				where class='ot_subtotal' and orders_id=$oID";
			tep_db_query($Query);

			// 2.2.2.3 Total
// Correction of number_format - German format (Michel Haase, 2005-02-18)
			$Query = "select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and orders_id=$oID";
			$result = tep_db_query($Query);
			$row = tep_db_fetch_array($result);
			$Total = $row["total_value"];

			$Query = "update " . TABLE_ORDERS_TOTAL . " set
				text = '<b>" . number_format($Total, 2, ',', '') . ' ' . $order->info['currency'] . "</b>',
				value = '" . $Total . "'
				where class='ot_total' and orders_id=$oID";
			tep_db_query($Query);

			// 2.3 REDIRECTION #####
			
			tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));

		}
	
	  break;
		
  }
}

  if (($action == 'edit') && isset($HTTP_GET_VARS['oID'])) {
    $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);

    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
    $order_exists = true;
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
    }
  }
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<style type="text/css">
.Subtitle {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  font-weight: bold;
  color: #FF6600;
}
</style>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="96%" cellspacing="0" cellpadding="2">
		
<?php
if (($action == 'edit') && ($order_exists == true)) {
  $order = new order($oID);
  $customer_group=new customer_group($order->customers_id);
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE . '&nbsp;(' . HEADING_TITLE_NUMBER . '&nbsp;' . $oID . '&nbsp;' . HEADING_TITLE_DATE  . '&nbsp;' . tep_datetime_short($order->info['date_purchased']) . ')'; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
          </tr>
          <tr>
	          <td class="main" colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="3"><?php echo HEADING_SUBTITLE; ?></td>
          </tr>
          <tr>
	          <td class="main" colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
        </table></td>
      </tr>

<!-- Begin Addresses Block -->

      <tr><?php echo tep_draw_form('edit_order', "edit_orders.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order'); ?>
	  </tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   

	<!-- Begin Update Block -->
<!-- Improvement: more "Update" buttons (Michel Haase, 2005-02-18) - here after FORM und before MENUE_TITLE_CUSTOMER -->
	
      <tr>
	      <td>
          <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td class="main" bgcolor="#FAEDDE"><?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
	          </tr>
          </table>
				</td>
      </tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>   
	<!-- End of Update Block -->

      <tr>
	    <td class="SubTitle"><?php echo MENUE_TITLE_CUSTOMER; ?></td>
	  </tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   

			<tr>
			  <td>
      
<table border="0" class="dataTableRow" cellpadding="2" cellspacing="0">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" width="80"></td>
    <td class="dataTableHeadingContent" width="150"><?php echo ENTRY_BILLING_ADDRESS; ?></td>
    <td class="dataTableHeadingContent" width="6">&nbsp;</td>
    <td class="dataTableHeadingContent" width="150"><?php echo ENTRY_SHIPPING_ADDRESS; ?></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_COMPANY; ?>: </b></td>
    <td><span class="main"><input name="update_customer_company" size="25" value="<?php echo tep_html_quotes($order->customer['company']); ?>"></span></td>
		<td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_company" size="25" value="<?php echo tep_html_quotes($order->delivery['company']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_NAME; ?>: </b></td>
    <td><span class="main"><input name="update_customer_name" size="25" value="<?php echo tep_html_quotes($order->customer['name']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_name" size="25" value="<?php echo tep_html_quotes($order->delivery['name']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_ADDRESS; ?>: </b></td>
    <td><span class="main"><input name="update_customer_street_address" size="25" value="<?php echo tep_html_quotes($order->customer['street_address']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_street_address" size="25" value="<?php echo tep_html_quotes($order->delivery['street_address']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_SUBURB; ?>: </b></td>
    <td><span class="main"><input name="update_customer_suburb" size="25" value="<?php echo tep_html_quotes($order->customer['suburb']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_suburb" size="25" value="<?php echo tep_html_quotes($order->delivery['suburb']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_CITY; ?>: </b></td>
    <td><span class="main"><input name="update_customer_city" size="25" value="<?php echo tep_html_quotes($order->customer['city']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_city" size="25" value="<?php echo tep_html_quotes($order->delivery['city']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_STATE; ?>: </b></td>
    <td><span class="main"><input name="update_customer_state" size="25" value="<?php echo tep_html_quotes($order->customer['state']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_state" size="25" value="<?php echo tep_html_quotes($order->delivery['state']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_POSTCODE; ?>: </b></td>
    <td><span class="main"><input name="update_customer_postcode" size="25" value="<?php echo $order->customer['postcode']; ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_postcode" size="25" value="<?php echo $order->delivery['postcode']; ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_COUNTRY; ?>: </b></td>
    <td><span class="main"><input name="update_customer_country" size="25" value="<?php echo tep_html_quotes($order->customer['country']); ?>"></span></td>
    <td>&nbsp;</td>
    <td><span class="main"><input name="update_delivery_country" size="25" value="<?php echo tep_html_quotes($order->delivery['country']); ?>"></span></td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_PHONE; ?>: </b></td>
    <td><span class="main"><input name="update_customer_telephone" size="25" value="<?php echo $order->customer['telephone']; ?>"></span></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="main"><b><?php echo ENTRY_CUSTOMER_EMAIL; ?>: </b></td>
    <td colspan="3"><span class="main"><input name="update_customer_email_address" size="25" value="<?php echo $order->customer['email_address']; ?>"></span></td>
  </tr>
</table>

				</td>
			</tr>
<!-- End Addresses Block -->

      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>      

<!-- Begin Payment Block -->
      <tr>
	      <td class="SubTitle"><?php echo MENUE_TITLE_PAYMENT; ?></td>
			</tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
	      <td>
				
<table border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td colspan="2" class="dataTableHeadingContent"><?php echo ENTRY_PAYMENT_METHOD; ?></td>
	</tr>
  <tr>
	  <td colspan="2" class="main"><input name='update_info_payment_method' size='35' value='<?php echo $order->info['payment_method']; ?>'></td>
	</tr>

	<!-- Begin Credit Card Info Block -->
	  <?php if ($order->info['cc_type'] || $order->info['cc_owner'] || $order->info['cc_number'] || $order->info['payment_method'] == "Credit Card" || $order->info['payment_method'] == "Kreditkarte") { ?>
	  <tr>
	    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	  </tr>
	  <tr>
	    <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
	    <td class="main"><input name='update_info_cc_type' size='10' value='<?php echo $order->info['cc_type']; ?>'></td>
	  </tr>
	  <tr>
	    <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
	    <td class="main"><input name='update_info_cc_owner' size='20' value='<?php echo $order->info['cc_owner']; ?>'></td>
	  </tr>
	  <tr>
	    <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
	    <td class="main"><input name='update_info_cc_number' size='20' value='<?php echo $order->info['cc_number']; ?>'></td>
	  </tr>
	  <tr>
	    <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
	    <td class="main"><input name='update_info_cc_expires' size='4' value='<?php echo $order->info['cc_expires']; ?>' maxlength="4"></td>
	  </tr>
	<?php } ?>
  <!-- End Credit Card Info Block -->
	
</table>

        </td>
      </tr>
	    <?php if ($order->info['payment_method'] != "Credit Card" || $order->info['payment_method'] != "Kreditkarte") { ?>
  	    <tr>
	        <td class="smalltext"></td>
	      </tr>
	    <?php } ?>
<!-- End Payment Block -->
	
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>

<!-- Begin Products Listing Block -->
      <tr>
	      <td class="SubTitle"><?php echo MENUE_TITLE_ORDER; ?></td>
			</tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
	      <td>
				  
	<?php
    // Override order.php Class's Field Limitations
		$index = 0;
		$order->products = array();
		$orders_products_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$oID . "'");
		while ($orders_products = tep_db_fetch_array($orders_products_query)) {
		$order->products[$index] = array('qty' => $orders_products['products_quantity'],
                                     'name' => str_replace("'", "&#39;", $orders_products['products_name']),
                                     'model' => $orders_products['products_model'],
                                     'tax' => $orders_products['products_tax'],
                                     'price' => $orders_products['products_price'],
                                     'final_price' => $orders_products['final_price'],
                                     'orders_products_id' => $orders_products['orders_products_id']);

		$subindex = 0;
		$attributes_query_string = "select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . (int)$oID . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
		$attributes_query = tep_db_query($attributes_query_string);

		if (tep_db_num_rows($attributes_query)) {
		while ($attributes = tep_db_fetch_array($attributes_query)) {
		  $order->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
		                                                            'value' => $attributes['products_options_values'],
		                                                            'prefix' => $attributes['price_prefix'],
		                                                            'price' => $attributes['options_values_price'],
		                                                            'orders_products_attributes_id' => $attributes['orders_products_attributes_id']);
		  $subindex++;
		  }
		}
		$index++;
	}
	
?>

<?php // Version with editable names & prices - NOT FINISHED ?>

<!--
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow">
	  <td class="dataTableHeadingContent" width="20"><?php // echo TABLE_HEADING_QUANTITY; ?></td>
	  <td class="dataTableHeadingContent" width="5">&nbsp;</td>
	  <td class="dataTableHeadingContent"><?php // echo TABLE_HEADING_PRODUCTS; ?></td>
	  <td class="dataTableHeadingContent"><?php // echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
	  <td class="dataTableHeadingContent"><?php // echo TABLE_HEADING_TAX; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php // echo TABLE_HEADING_UNIT_PRICE; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php // echo TABLE_HEADING_UNIT_PRICE_TAXED; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php // echo TABLE_HEADING_TOTAL_PRICE; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php // echo TABLE_HEADING_TOTAL_PRICE_TAXED; ?></td>
	</tr>
-->
	
<?php
/*
	for ($i=0; $i<sizeof($order->products); $i++) {
		$orders_products_id = $order->products[$i]['orders_products_id'];
		$RowStyle = "dataTableContent";
		echo '	  <tr class="dataTableRow">' . "\n" .
		     '	    <td class="' . $RowStyle . '" align="right">' . "<input name='update_products[$orders_products_id][qty]' size='2' value='" . $order->products[$i]['qty'] . "'>&nbsp;x</td>\n" . 
 		     '	    <td class="' . $RowStyle . '"></td>' . 
		     '	    <td class="' . $RowStyle . '">' . "<input name='update_products[$orders_products_id][name]' size='64' value='" . $order->products[$i]['name'] . "'>";
		
		// Has Attributes?
		if (sizeof($order->products[$i]['attributes']) > 0) {
			for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
				$orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
				echo '<br><nobr><small>&nbsp;<i> - ' . 
					 "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='32' value='" . $order->products[$i]['attributes'][$j]['option'] . "'>" . 
					 ': ' . 
					 "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='25' value='" . $order->products[$i]['attributes'][$j]['value'];
				if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
				echo "'>";
				echo '</i></small></nobr>';
			}
		}
		
		echo '	    </td>' . "\n" .
		     '	    <td class="' . $RowStyle . '">' . "<input name='update_products[$orders_products_id][model]' size='12' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
		     '	    <td class="' . $RowStyle . '">' . "<input name='update_products[$orders_products_id][tax]' size='4' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n" .
		     '	    <td class="' . $RowStyle . '" align="right">' . "<input name='update_products[$orders_products_id][final_price]' size='5' value='" . number_format($order->products[$i]['final_price'], 2, '.', '') . "'>" . '</td>' . "\n" . 
		     '	    <td class="' . $RowStyle . '" align="right">' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
		     '	  </tr>' . "\n";
	}
*/
?>
<!-- </table> -->


<?php // Version without editable names & prices ?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow">
	  <td class="dataTableHeadingContent" width="20"><?php echo TABLE_HEADING_QUANTITY; ?></td>
	  <td class="dataTableHeadingContent" width="5">&nbsp;</td>
	  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
	  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
	  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_UNIT_PRICE; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_UNIT_PRICE_TAXED; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_PRICE; ?></td>
	  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_PRICE_TAXED; ?></td>
	</tr>
	
<?php
	for ($i=0; $i<sizeof($order->products); $i++) {
		$orders_products_id = $order->products[$i]['orders_products_id'];
		$RowStyle = "dataTableContent";
		echo '	  <tr class="dataTableRow">' . "\n" .
		     '	    <td class="' . $RowStyle . '" align="left" width="20">' . "<input name='update_products[$orders_products_id][qty]' size='2' value='" . $order->products[$i]['qty'] . "'></td>\n" . 
 		     '	    <td class="' . $RowStyle . '" width="5">&nbsp;x&nbsp;</td>' . 
		     '	    <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input name='update_products[$orders_products_id][name]' size='64' type='hidden' value='" . $order->products[$i]['name'] . "'>";
		
		// Has Attributes?
		if (sizeof($order->products[$i]['attributes']) > 0) {
			for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
				$orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
				echo '<br><nobr><small>&nbsp;<i> - ' . 
					 "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='32' value='" . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option'], array("'"=>"&quot;")) . "'>" . 
					 ': ' . 
					 "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='25' value='" . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['value'], array("'"=>"&quot;"));
				if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
				echo "'>";
				echo '</i></small></nobr>';
			}
		}
		
		echo '	    </td>' . "\n" .
		     '	    <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
		     '	    <td class="' . $RowStyle . '">' . "<input name='update_products[$orders_products_id][tax]' size='4' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n" .
		     '	    <td class="' . $RowStyle . '" align="right">' . $currencies->format($order->products[$i]['final_price']) . "<input name='update_products[$orders_products_id][final_price]' size='5' type='hidden' value='" . $order->products[$i]['final_price'] . "'>" . '</td>' . "\n" . 
		     '	    <td class="' . $RowStyle . '" align="right">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
		     '	    <td class="' . $RowStyle . '" align="right">' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
		     '	    <td class="' . $RowStyle . '" align="right"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" . 
		     '	  </tr>' . "\n";
	}
	?>
</table>

        </td>
      <tr>
	      <td>
				  <table width="100%" cellpadding="0" cellspacing="0">
					  <tr>
						  <td valign="top"><?php echo "<span class='smalltext'>" . HINT_DELETE_POSITION . "</span>"; ?></td>
			        <td align="right"><?php echo '<a href="' . $PHP_SELF . '?oID=' . $oID . '&action=add_product&step=1">' . tep_image_button('button_add_article.gif', ADDING_TITLE) . '</a>'; ?></td>
						</tr>
					</table>
			  </td>
      </tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
			
	<!-- End Products Listings Block -->

	<!-- Begin Update Block -->
<!-- Improvement: more "Update" buttons (Michel Haase, 2005-02-18) -->
	
      <tr>
	      <td>
          <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td class="main" bgcolor="#FAEDDE"><?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
	          </tr>
          </table>
				</td>
      </tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>   
	<!-- End of Update Block -->

	<!-- Begin Order Total Block -->
      <tr>
	      <td class="SubTitle"><?php echo MENUE_TITLE_TOTAL; ?></td>
			</tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
	      <td>

<table border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
	<tr class="dataTableHeadingRow">
	  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TOTAL_MODULE; ?></td>
	  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TOTAL_AMOUNT; ?></td>
	  <td class="dataTableHeadingContent"width="1"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
	</tr>
<?php
  // Override order.php Class's Field Limitations
  $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' order by sort_order");
  $order->totals = array();
  while ($totals = tep_db_fetch_array($totals_query)) { 
	  $order->totals[] = array('title' => $totals['title'], 'text' => $totals['text'], 'class' => $totals['class'], 'value' => $totals['value'], 'orders_total_id' => $totals['orders_total_id']); 
	}

// START OF MAKING ALL INPUT FIELDS THE SAME LENGTH 
	$max_length = 0;
	$TotalsLengthArray = array();
	for ($i=0; $i<sizeof($order->totals); $i++) {
		$TotalsLengthArray[] = array("Name" => $order->totals[$i]['title']);
	}
	reset($TotalsLengthArray);
	foreach($TotalsLengthArray as $TotalIndex => $TotalDetails) {
		if (strlen($TotalDetails["Name"]) > $max_length) {
			$max_length = strlen($TotalDetails["Name"]);
		}
	}
// END OF MAKING ALL INPUT FIELDS THE SAME LENGTH

	$TotalsArray = array();
	for ($i=0; $i<sizeof($order->totals); $i++) {
		$TotalsArray[] = array("Name" => $order->totals[$i]['title'], "Price" => number_format($order->totals[$i]['value'], 2, '.', ''), "Class" => $order->totals[$i]['class'], "TotalID" => $order->totals[$i]['orders_total_id']);
		$TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
	}
	
	array_pop($TotalsArray);
	foreach($TotalsArray as $TotalIndex => $TotalDetails)
	{
		$TotalStyle = "smallText";
		if($TotalDetails["Class"] == "ot_total")
		{
			echo '	<tr>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value']) . '</b>' . 
						    "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
						    "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
						    "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
						    "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
				   '	</tr>' . "\n";
		}
		elseif($TotalDetails["Class"] == "ot_subtotal") 
		{
			echo '	<tr>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value']) . '</b>' . 
						    "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
						    "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
						    "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
						    "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
				   '	</tr>' . "\n";
		}
		elseif($TotalDetails["Class"] == "ot_tax")
		{
			echo '	<tr>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . trim($TotalDetails["Name"]) . "</b><input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value']) . '</b>' . 
						    "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
						    "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
						    "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
				   '	</tr>' . "\n";
		}
		else
		{
			echo '	<tr>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '">' . "<input name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
				   '		<td align="right" class="' . $TotalStyle . '">' . "<input name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
						    "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
						    "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
				   '		<td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
					 '   </td>' . "\n" .
				   '	</tr>' . "\n";
		}
	}
?>
</table>

	      </td>
      <tr>
			  <td class="smalltext"><?php echo HINT_TOTALS; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
	<!-- End Order Total Block -->
	
	<!-- Begin Status Block -->
      <tr>
	      <td class="SubTitle"><?php echo MENUE_TITLE_STATUS; ?></td>
			</tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr> 
      <tr>
        <td class="main">
				  
<table border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
    <? if($CommentsWithStatus) { ?>
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
    <? } ?>
  </tr>
<?php
$orders_history_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
if (tep_db_num_rows($orders_history_query)) {
  while ($orders_history = tep_db_fetch_array($orders_history_query)) {
    echo '  <tr>' . "\n" .
         '    <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
         '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="center">';
    if ($orders_history['customer_notified'] == '1') {
      echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
    } else {
      echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
    }
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
    if ($CommentsWithStatus) {
      echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
           '    <td class="smallText" align="left">' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n";
    }
    echo '  </tr>' . "\n";
  }
} else {
  echo '  <tr>' . "\n" .
       '    <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
       '  </tr>' . "\n";
}
?>
</table>

			  </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>
      <tr>
			  <td>	
						
<table border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
    <td class="main" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
	<tr>
	  <td>
		  <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main" align="right"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b></td>
          <td class="main" align="right"><?php echo tep_draw_checkbox_field('notify', '', false); ?></td>
        </tr>
        <? if($CommentsWithStatus) { ?>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b></td>
          <td class="main" align="right"><?php echo tep_draw_checkbox_field('notify_comments', '', false); ?></td>
        </tr>
        <? } ?>
      </table>
	  </td>
    <td class="main" width="10">&nbsp;</td>
    <td class="main">
    <?
    if($CommentsWithStatus) {
   	  echo tep_draw_textarea_field('comments', 'soft', '40', '5', $order->info['comments']);
  // 	  echo tep_draw_textarea_field('comments', 'soft', '40', '5');
	  } else {
		  echo tep_draw_textarea_field('comments', 'soft', '40', '5', $order->info['comments']);
    }
	  ?>
    </td>
  </tr>
</table>

			  </td>
			</tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
	<!-- End of Status Block -->
	
	<!-- Begin Update Block -->
	
      <tr>
	      <td class="SubTitle"><?php echo MENUE_TITLE_UPDATE; ?></td>
			</tr>
      <tr>
	      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
	      <td>
          <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td class="main" bgcolor="#FAEDDE"><?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
	          </tr>
          </table>
				</td>
      </tr>
	<!-- End of Update Block -->
	
      </form>
			
<?php
}
if($action == "add_product")
{
?>
      <tr>
        <td width="100%">
				  <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
              <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
            </tr>
          </table>
				</td>
      </tr>

<?
	// ############################################################################
	//   Get List of All Products
	// ############################################################################

		//$result = tep_db_query("SELECT products_name, p.products_id, x.categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " x ON x.categories_id=ptc.categories_id ORDER BY categories_id");
		$result = tep_db_query("SELECT products_name, p.products_id, cd.categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id where pd.language_id = '" . (int)$languages_id . "' ORDER BY categories_name");
		while($row = tep_db_fetch_array($result))
		{
			extract($row,EXTR_PREFIX_ALL,"db");
			$ProductList[$db_categories_id][$db_products_id] = $db_products_name;
			$CategoryList[$db_categories_id] = $db_categories_name;
			$LastCategory = $db_categories_name;
		}
		
		// ksort($ProductList);
		
		$LastOptionTag = "";
		$ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
		$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
		foreach($ProductList as $Category => $Products)
		{
			$ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
			$ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
			asort($Products);
			foreach($Products as $Product_ID => $Product_Name)
			{
				$ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
			}
			
			if($Category != $LastCategory)
			{
				$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
				$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
			}
		}
	
	
	// ############################################################################
	//   Add Products Steps
	// ############################################################################
	
		print "<tr><td><table border='0'>\n";
		
		// Set Defaults
			if(!IsSet($add_product_categories_id))
			$add_product_categories_id = 0;

			if(!IsSet($add_product_products_id))
			$add_product_products_id = 0;
		
		// Step 1: Choose Category
			print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
			print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 1:</b></td>\n";
			print "<td class='dataTableContent' valign='top'>";
			echo ' ' . tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
			print "<input type='hidden' name='step' value='2'>";
			print "</td>\n";
			print "<td class='dataTableContent'>" . ADDPRODUCT_TEXT_STEP1 . "</td>\n";
			print "</form></tr>\n";
			print "<tr><td colspan='3'>&nbsp;</td></tr>\n";

		// Step 2: Choose Product
		if(($step > 1) && ($add_product_categories_id > 0))
		{
			print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
			print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 2: </b></td>\n";
			print "<td class='dataTableContent' valign='top'><select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
			$ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
			asort($ProductList[$add_product_categories_id]);
			foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
			{
			$ProductOptions .= "<option value='$ProductID'> $ProductName\n";
			}
			$ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
			print $ProductOptions;
			print "</select></td>\n";
			print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
			print "<input type='hidden' name='step' value='3'>\n";
			print "<td class='dataTableContent'>" . ADDPRODUCT_TEXT_STEP2 . "</td>\n";
			print "</form></tr>\n";
			print "<tr><td colspan='3'>&nbsp;</td></tr>\n";
		}

		// Step 3: Choose Options
		if(($step > 2) && ($add_product_products_id > 0))
		{
			// Get Options for Products
			$result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id=pa.options_id LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pov.products_options_values_id=pa.options_values_id WHERE products_id='$add_product_products_id' and po.language_id = '" . (int)$languages_id . "'");
			
			// Skip to Step 4 if no Options
			if(tep_db_num_rows($result) == 0)
			{
				print "<tr class=\"dataTableRow\">\n";
				print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td>\n";
				print "<td class='dataTableContent' valign='top' colspan='2'><i>" . ADDPRODUCT_TEXT_OPTIONS_NOTEXIST . "</i></td>\n";
				print "</tr>\n";
				$step = 4;
			}
			else
			{
				while($row = tep_db_fetch_array($result))
				{
					extract($row,EXTR_PREFIX_ALL,"db");
					$Options[$db_products_options_id] = $db_products_options_name;
					$ProductOptionValues[$db_products_options_id][$db_products_options_values_id] = $db_products_options_values_name;
				}
			
				print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
				print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td><td class='dataTableContent' valign='top'>";
				foreach($ProductOptionValues as $OptionID => $OptionValues)
				{
					$OptionOption = "<b>" . $Options[$OptionID] . "</b> - <select name='add_product_options[$OptionID]'>";
					foreach($OptionValues as $OptionValueID => $OptionValueName)
					{
					$OptionOption .= "<option value='$OptionValueID'> $OptionValueName\n";
					}
					$OptionOption .= "</select><br>\n";
					
					if(IsSet($add_product_options))
					$OptionOption = str_replace("value='" . $add_product_options[$OptionID] . "'","value='" . $add_product_options[$OptionID] . "' selected",$OptionOption);
					
					print $OptionOption;
				}		
				print "</td>";
				print "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "'>";
				print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
				print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
				print "<input type='hidden' name='step' value='4'>";
				print "</td>\n";
				print "</form></tr>\n";
			}

			print "<tr><td colspan='3'>&nbsp;</td></tr>\n";
		}

		// Step 4: Confirm
		if($step > 3)
		{
			print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
			print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 4: </b></td>";
			print "<td class='dataTableContent' valign='top'><input name='add_product_quantity' size='2' value='1'> " . ADDPRODUCT_TEXT_CONFIRM_QUANTITY . "</td>";
			print "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

			if(IsSet($add_product_options))
			{
				foreach($add_product_options as $option_id => $option_value_id)
				{
					print "<input type='hidden' name='add_product_options[$option_id]' value='$option_value_id'>";
				}
			}
			print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
			print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
			print "<input type='hidden' name='step' value='5'>";
			print "</td>\n";
			print "</form></tr>\n";
		}
		
		print "</table></td></tr>\n";
}  
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?
  // Function    : tep_get_country_id
  // Arguments   : country_name		country name string
  // Return      : country_id
  // Description : Function to retrieve the country_id based on the country's name
  function tep_get_country_id($country_name) {
    $country_id_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_name = '" . $country_name . "'");
    if (!tep_db_num_rows($country_id_query)) {
      return 0;
    }
    else {
      $country_id_row = tep_db_fetch_array($country_id_query);
      return $country_id_row['countries_id'];
    }
  }

  // Function    : tep_get_country_iso_code_2
  // Arguments   : country_id		country id number
  // Return      : country_iso_code_2
  // Description : Function to retrieve the country_iso_code_2 based on the country's id
  function tep_get_country_iso_code_2($country_id) {
    $country_iso_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
    if (!tep_db_num_rows($country_iso_query)) {
      return 0;
    }
    else {
      $country_iso_row = tep_db_fetch_array($country_iso_query);
      return $country_iso_row['countries_iso_code_2'];
    }
  }

  // Function    : tep_get_zone_id
  // Arguments   : country_id		country id string    zone_name		state/province name
  // Return      : zone_id
  // Description : Function to retrieve the zone_id based on the zone's name
  function tep_get_zone_id($country_id, $zone_name) {
    $zone_id_query = tep_db_query("select * from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_name = '" . $zone_name . "'");
    if (!tep_db_num_rows($zone_id_query)) {
      return 0;
    }
    else {
      $zone_id_row = tep_db_fetch_array($zone_id_query);
      return $zone_id_row['zone_id'];
    }
  }
  
  // Function    : tep_field_exists
  // Arguments   : table	table name  field  	field name
  // Return      : true/false
  // Description : Function to check the existence of a database field
  function tep_field_exists($table,$field) {
    $describe_query = tep_db_query("describe $table");
    while($d_row = tep_db_fetch_array($describe_query))
    {
      if ($d_row["Field"] == "$field")
      return true;
    }
    return false;
  }

  // Function    : tep_html_quotes
  // Arguments   : string	any string
  // Return      : string with single quotes converted to html equivalent
  // Description : Function to change quotes to HTML equivalents for form inputs.
  function tep_html_quotes($string) {
    return str_replace("'", "&#39;", $string);
  }

  // Function    : tep_html_unquote
  // Arguments   : string	any string
  // Return      : string with html equivalent converted back to single quotes
  // Description : Function to change HTML equivalents back to quotes
  function tep_html_unquote($string) {
    return str_replace("&#39;", "'", $string);
  }

require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
