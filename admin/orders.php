<?php
/*
  $Id: orders.php,v 1.112 2003/06/29 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  include ('../../../include/cp_header.php');
  require('includes/application_top.php');
  include('../includes/classes/customer_group.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$languages_id . "'");
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                               'text' => $orders_status['orders_status_name']);
    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'update_order':
        $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
        $status = tep_db_prepare_input($HTTP_POST_VARS['status']);
        $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);

        $order_updated = false;
        $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
        $check_status = tep_db_fetch_array($check_status_query);

        if ( ($check_status['orders_status'] != $status) || tep_not_null($comments)) {
          tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . (int)$oID . "'");

          $customer_notified = '0';
          if (isset($HTTP_POST_VARS['notify']) && ($HTTP_POST_VARS['notify'] == 'on')) {
            $notify_comments = '';
            if (isset($HTTP_POST_VARS['notify_comments']) && ($HTTP_POST_VARS['notify_comments'] == 'on')) {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
	            $customer_notified = '1';
            }
            
            // ** GOOGLE CHECKOUT **
            chdir("./..");
            require_once('includes/languages/' . $language . '/' .'modules/payment/googlecheckout.php');
            $payment_value= MODULE_PAYMENT_GOOGLECHECKOUT_TEXT_TITLE;
            $num_rows = tep_db_num_rows(tep_db_query("select google_order_number from google_orders where orders_id= ". (int)$oID));
            
            //Check if order is a Google Checkout order
            if($num_rows == 0) {
              $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
              tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            }else {
		          if($HTTP_POST_VARS['notify'] != 'on')
		          	unset($notify_comments);
		          google_checkout_state_change($check_status, $status, $oID, $customer_notified, $notify_comments);
            }
            // ** END GOOGLE CHECKOUT **

          }

          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . tep_db_input($comments)  . "')");

          $order_updated = true;
        }

        if ($order_updated == true) {
         $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
        } else {
          $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
        }

        tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
        break;
      case 'deleteconfirm':
        $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);

        tep_remove_order($oID, $HTTP_POST_VARS['restock']);

        tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action'))));
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

  include(DIR_WS_CLASSES . 'order.php');
  xoops_cp_header();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/ajax.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="add-Product" class="addProduct">
	<div><?php echo DIV_ADD_PRODUCT_HEADING; ?></div>
	<div id="add-product-product" class="addProductContents"><?php echo ADD_PRODUCT_SELECT_PRODUCT; ?></div>
	<div id="addProductSearch" class="addProductContentsSearch"><?php require (DIR_WS_INCLUDES . 'advanced_search.php'); ?></div>
	<div id="addProductFind">&nbsp;</div>
	<div id="ProdAttr">&nbsp;</div>
	<a href="javascript: hideAddProducts();"><?php echo tep_image(DIR_WS_LANGUAGES . $language  . '/images/buttons/button_cancel.gif'); ?></a>
</div>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (($action == 'edit') && ($order_exists == true)) {
    $order = new order($oID);
    $customer_group=new customer_group($order->customers_id);
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="3"><?php echo tep_draw_separator(); ?></td>
          </tr>
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_CUSTOMER; ?></b></td>
                <td class="main"><?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>', 'customers_', $oID); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_TELEPHONE_NUMBER; ?></b></td>
                <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'customers_telephone', '<?php echo $order->customer['telephone']; ?>');" class="ajaxLink"><?php echo $order->customer['telephone']; ?></a></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_EMAIL_ADDRESS; ?></b></td>
                <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'customers_email_address', '<?php echo $order->customer['email_address']; ?>');" class="ajaxLink"><?php echo $order->customer['email_address'] . '</a>'; ?></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_SHIPPING_ADDRESS; ?></b></td>
                <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>', 'delivery_', $oID); ?></td>
              </tr>
            </table></td>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><b><?php echo ENTRY_BILLING_ADDRESS; ?></b></td>
                <td class="main"><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, '', '<br>', 'billing_', $oID); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
            <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'payment_method', '<?php echo $order->info['payment_method']; ?>');" class="ajaxLink"><?php echo $order->info['payment_method']; ?></a></td>
          </tr>
<?php
    if (tep_not_null($order->info['cc_type']) || tep_not_null($order->info['cc_owner']) || tep_not_null($order->info['cc_number'])) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'cc_type', '<?php echo $order->info['cc_type']; ?>');" class="ajaxLink"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></a></td>
            <td class="main"><?php echo $order->info['cc_type']; ?></td>
          </tr>
          <tr>
            <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'cc_owner', '<?php echo $order->info['cc_owner']; ?>');" class="ajaxLink"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></a></td>
            <td class="main"><?php echo $order->info['cc_owner']; ?></td>
          </tr>
          <tr>
            <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'cc_number', '<?php echo $order->info['cc_number']; ?>');" class="ajaxLink"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></a></td>
            <td class="main"><?php echo $order->info['cc_number']; ?></td>
          </tr>
          <tr>
            <td class="main"><a href="javascript: updateOrderField('<?php echo $oID; ?>', 'orders', 'cc_expires', '<?php echo $order->info['cc_expires']; ?>');" class="ajaxLink"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></a></td>
            <td class="main"><?php echo $order->info['cc_expires']; ?></td>
          </tr>
<?php
    }
?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2"><?php echo sprintf(TABLE_HEADING_PRODUCTS, $oID); ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      echo '          <tr class="dataTableRow">' . "\n" .
           '            <td class="dataTableContent" valign="top" align="right"><a href="javascript: updateProduct(\'' . $oID . '\', \'' . $order->products[$i]['id'] . '\', \'\', \'eliminate\', \'\', \'\');">' . tep_image(DIR_WS_ICONS . 'cross.gif') . '</a>&nbsp;<a href="javascript: updateProduct(\'' . $oID . '\', \'' . $order->products[$i]['id'] . '\', \'products_quantity\', \'update\', \'\', \'' . $order->products[$i]['qty'] . '\');"><u>' . $order->products[$i]['qty'] . '&nbsp;x</u></a></td>' . "\n" .
           '            <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - <a href="javascript: updateProduct(\'' . $oID . '\', \'' . $order->products[$i]['id'] . '\', \'options\', \'update\', \'' . $order->products[$i]['attributes'][$j]['option'] . '\', \'' . $order->products[$i]['attributes'][$j]['value'] . '\');"><u>' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</u></a></i></small></nobr>';
        }
      }

      echo '            </td>' . "\n" .
           '            <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n" .
           '            <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
           '            <td class="dataTableContent" align="right" valign="top"><b><a href="javascript: updateProduct(\'' . $oID . '\', \'' . $order->products[$i]['id'] . '\', \'products_price_excl\', \'update\', \'\', \'' . $order->products[$i]['final_price'] . '\');"><u>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</u></a></b></td>' . "\n" .
           '            <td class="dataTableContent" align="right" valign="top"><b><a href="javascript: updateProduct(\'' . $oID . '\', \'' . $order->products[$i]['id'] . '\', \'products_price_incl\', \'update\', \'\', \'' . $order->products[$i]['final_price'] . '\');"><u>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</u></a></b></td>' . "\n" .
           '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '            <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n";
      echo '          </tr>' . "\n";
    }
?>
          <tr>
            <td align="right" colspan="8"><table border="0" cellspacing="0" cellpadding="2">
<?php
    for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
	    if (($order->totals[$i]['class'] != 'ot_subtotal') && ($order->totals[$i]['class'] != 'ot_total') && ($order->totals[$i]['class'] != 'ot_tax')) {
		    echo '              <tr>' . "\n" .
		    	 '                <td align="right" class="smallText"><a href="javascript: updateOrdersTotal(\'' . $oID . '\', \'' . $order->totals[$i]['class'] . '\', \'' . $order->totals[$i]['title'] . '\', \'title\')"><u>' . $order->totals[$i]['title'] . '</u></a></td>' . "\n" .
		    	 '                <td align="right" class="smallText"><a href="javascript: updateOrdersTotal(\'' . $oID . '\', \'' . $order->totals[$i]['class'] . '\', \'' . $order->totals[$i]['value'] . '\', \'value\')"><u>' . $order->totals[$i]['text'] . '</u></a></td>' . "\n" .
		    	 '              </tr>' . "\n";
	    } else {
		    echo '              <tr>' . "\n" .
		    	 '                <td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
		    	 '                <td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
		    	 '              </tr>' . "\n";
	    }
    }
    //link to create a new order_total
    echo '              <tr>' . "\n" .
    	 '              <td align="right" class="smallText" colspan="2"><a href="javascript: createOrdersTotal(\'' . $oID . '\')"><u>Add a field -></u></a></td>' . "\n" .
    	 '              </tr>' . "\n";
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><table border="1" cellspacing="0" cellpadding="5">
          <tr>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_DATE_ADDED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_STATUS; ?></b></td>
            <td class="smallText" align="center"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
          </tr>
<?php
    $orders_history_query = tep_db_query("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
    if (tep_db_num_rows($orders_history_query)) {
      while ($orders_history = tep_db_fetch_array($orders_history_query)) {
        echo '          <tr>' . "\n" .
             '            <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
             '            <td class="smallText" align="center">';
        if ($orders_history['customer_notified'] == '1') {
          echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
        } else {
          echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
        }
        echo '            <td class="smallText">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n" .
             '            <td class="smallText">' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n" .
             '          </tr>' . "\n";
      }
    } else {
        echo '          <tr>' . "\n" .
             '            <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
             '          </tr>' . "\n";
    }
?>
        </table></td>
      </tr>
      <tr>
        <td class="main"><br><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('status', FILENAME_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'); ?>
        <td class="main"><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo ENTRY_STATUS; ?></b> <?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status']); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b> <?php echo tep_draw_checkbox_field('notify', '', true); ?></td>
                <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b> <?php echo tep_draw_checkbox_field('notify_comments', '', true); ?></td>
              </tr>
            </table></td>
            <td valign="top"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
  
<!-- googlecheckout Tracking Number -->
<?php 
// orders_status == STATE_PROCESSING -> Processing before delivery
	if($order->info['payment_method'] == 'Google Checkout' && $order->info['orders_status'] == STATE_PROCESSING){
			echo '<td><table border="0" cellpadding="3" cellspacing="0" width="100%">   
				<tbody>
					<tr>  
						<td style="border-top: 2px solid rgb(255, 255, 255); border-right: 2px solid rgb(255, 255, 255);" nowrap="nowrap" colspan="2">
								<b>Shipping Information</b>  
						</td>  
					</tr>
					<tr>  
						<td nowrap="nowrap" valign="middle" width="1%">  
							<font size="2">  
								<b>Tracking:</b>  
							</font>  
						</td>  
						<td style="border-right: 2px solid rgb(255, 255, 255); border-bottom: 2px solid rgb(255, 255, 255);" nowrap="nowrap">   
							<input name="tracking_number" style="color: rgb(0, 0, 0);" id="trackingBox" size="20" type="text">   
						</td>  
					</tr>  
					<tr>  
						<td nowrap="nowrap" valign="middle" width="1%">  
							<font size="2">  
								<b>Carrier:</b>  
							</font>  
						</td>  
						<td style="border-right: 2px solid rgb(255, 255, 255);" nowrap="nowrap">  
							<select name="carrier_select" style="color: rgb(0, 0, 0);" id="carrierSelect">  
								<option value="select" selected="selected">
								 Select ...  
								</option>   
								<option value="USPS">
								 USPS  
								</option>   
								<option value="DHL">
								 DHL  
								</option>   
								<option value="UPS">
								 UPS  
								</option>   
								<option value="Other">
								 Other  
								</option>   
								<option value="FedEx">
								 FedEx  
								</option>   
							</select>  
						</td>  
					</tr>     
				</tbody> 
			</table></td>';
	  
	}
?>
<!-- end googlecheckout Tracking Number -->
          </tr>
        </table></td>
      </form></tr>
      <tr>
        <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $HTTP_GET_VARS['oID']) . '" TARGET="_blank">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $HTTP_GET_VARS['oID']) . '" TARGET="_blank">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr><?php echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit'); ?></td>
              </form></tr>
              <tr><?php echo tep_draw_form('status', FILENAME_ORDERS, '', 'get'); ?>
                <td class="smallText" align="right"><?php echo HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"'); ?></td>
              </form></tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDER_TOTAL; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    if (isset($HTTP_GET_VARS['cID'])) {
      $cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);
      $orders_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . (int)$cID . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by orders_id DESC";
    } elseif (isset($HTTP_GET_VARS['status']) && is_numeric($HTTP_GET_VARS['status']) && ($HTTP_GET_VARS['status'] > 0)) {
      $status = tep_db_prepare_input($HTTP_GET_VARS['status']);
      $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.orders_status_id = '" . (int)$status . "' and ot.class = 'ot_total' order by o.orders_id DESC";
    } else {
      $orders_query_raw = "select o.orders_id, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by o.orders_id DESC";
    }
    $orders_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_query_raw, $orders_query_numrows);
    $orders_query = tep_db_query($orders_query_raw);
    while ($orders = tep_db_fetch_array($orders_query)) {
    if ((!isset($HTTP_GET_VARS['oID']) || (isset($HTTP_GET_VARS['oID']) && ($HTTP_GET_VARS['oID'] == $orders['orders_id']))) && !isset($oInfo)) {
        $oInfo = new objectInfo($orders);
      }

      if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $orders['customers_name']; ?></td>
                <td class="dataTableContent" align="right"><?php echo strip_tags($orders['order_total']); ?></td>
                <td class="dataTableContent" align="center"><?php echo tep_datetime_short($orders['date_purchased']); ?></td>
                <td class="dataTableContent" align="right"><?php echo $orders['orders_status_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID')) . 'oID=' . $orders['orders_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');

      $contents = array('form' => tep_draw_form('orders', FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('restock') . ' ' . TEXT_INFO_RESTOCK_PRODUCT_QUANTITY);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . tep_datetime_short($oInfo->date_purchased) . '</b>');

$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $oInfo->orders_id) . '" TARGET="_blank">' . tep_image_button('button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a> <a href="' . tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $oInfo->orders_id) . '" TARGET="_blank">' . tep_image_button('button_packingslip.gif', IMAGE_ORDERS_PACKINGSLIP) . '</a>');
$contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
        if (tep_not_null($oInfo->last_modified)) $contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
        $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }


 /* ** GOOGLE CHECKOUT **/
  define('STATE_PENDING', "1");
  define('STATE_PROCESSING', "2");
  define('STATE_DELIVERED', "3");
 
 /*
  * Function which posts a request to the specified url.
  * @param url Url where request is to be posted
  * @param merid The merchant ID used for HTTP Basic Authentication
  * @param merkey The merchant key used for HTTP Basic Authentication
  * @param postargs The post arguments to be sent
  * @param message_log An opened log file poitner for appending logs
  */
  function send_google_req($url, $merid, $merkey, $postargs, $message_log) {
    // Get the curl session object
    $session = curl_init($url);

    $header_string_1 = "Authorization: Basic ".base64_encode($merid.':'.$merkey);
    $header_string_2 = "Content-Type: application/xml;charset=UTF-8";	
    $header_string_3 = "Accept: application/xml;charset=UTF-8";
	
//    fwrite($message_log, sprintf("\r\n%s %s %s\n",$header_string_1, $header_string_2, $header_string_3));
    // Set the POST options.
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_HTTPHEADER, array($header_string_1, $header_string_2, $header_string_3));
    curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
    curl_setopt($session, CURLOPT_HEADER, true);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	// Uncomment the following and set the path to your CA-bundle.crt file if SSL verification fails
	//curl_setopt($session, CURLOPT_CAINFO, "C:\\Program Files\\xampp\\apache\\conf\\ssl.crt\\ca-bundle.crt");

    // Do the POST and then close the session
    $response = curl_exec($session);
	if (curl_errno($session)) {
		die(curl_error($session));
	} else {
	    curl_close($session);
	}

    fwrite($message_log, sprintf("\r\n%s\n",$response));
	
    // Get HTTP Status code from the response
    $status_code = array();
    preg_match('/\d\d\d/', $response, $status_code);
    
    fwrite($message_log, sprintf("\r\n%s\n",$status_code[0]));
    // Check for errors
    switch( $status_code[0] ) {
      case 200:
      // Success
        break;
      case 503:
        die('Error 503: Service unavailable. An internal problem prevented us from returning data to you.');
	      break;
      case 403:
        die('Error 403: Forbidden. You do not have permission to access this resource, or are over your rate limit.');
        break;
      case 400:
        die('Error 400: Bad request. The parameters passed to the service did not match as expected. The exact error is returned in the XML response.');
        break;
      default:
        die('Error :' . $status_code[0]);
    }
  }
  
  function google_checkout_state_change($check_status, $status, $oID, $cust_notify, $notify_comments) {
    // If status update is from Pending -> Processing on the Admin UI
    // this invokes the processing-order and charge-order commands
    // 1->Pending, 2-> Processing
    global $carrier_select, $tracking_number;

      define('API_CALLBACK_MESSAGE_LOG', DIR_FS_CATALOG . "/googlecheckout/response_message.log");
      define('API_CALLBACK_ERROR_LOG', DIR_FS_CATALOG. "/googlecheckout/response_error.log");

      include_once(DIR_FS_CATALOG . '/includes/modules/payment/googlecheckout.php');
      $googlepay = new googlecheckout();
				
      //Setup the log file
      if (!$message_log = fopen(API_CALLBACK_MESSAGE_LOG, "a")) {
        error_func("Cannot open " . API_CALLBACK_MESSAGE_LOG . " file.\n", 0);
        exit(1);
      }
      $google_answer = tep_db_fetch_array(tep_db_query("select google_order_number, order_amount from " . $googlepay->table_order . " where orders_id = " . (int)$oID ));
      $google_order = $google_answer['google_order_number'];  
      $amt = $google_answer['order_amount'];  

    if($check_status['orders_status'] == STATE_PENDING && $status == STATE_PROCESSING) {
      if($google_order != '') {					
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <charge-order xmlns=\"".$googlepay->schema_url."\" google-order-number=\"". $google_order. "\">
                    <amount currency=\"" . DEFAULT_CURRENCY . "\">" . $amt . "</amount>
                    </charge-order>";
        fwrite($message_log, sprintf("\r\n%s\n",$postargs));
        send_google_req($googlepay->request_url, $googlepay->merchantid, $googlepay->merchantkey, 
                        $postargs, $message_log); 
        
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <process-order xmlns=\"".$googlepay->schema_url    ."\" google-order-number=\"". $google_order. "\"/> ";
        fwrite($message_log, sprintf("\r\n%s\n",$postargs));
        send_google_req($googlepay->request_url, $googlepay->merchantid, $googlepay->merchantkey, 
                    $postargs, $message_log); 
      }
    }			
    
    // If status update is from Processing -> Delivered on the Admin UI
    // this invokes the deliver-order and archive-order commands
    // 2->Processing, 3-> Delivered
    if($check_status['orders_status'] == STATE_PROCESSING &&  $status == STATE_DELIVERED) {
      $send_mail = "false";
      if($cust_notify == 1) 
        $send_mail = "true";
      if($google_order != '') {					
        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                     <deliver-order xmlns=\"".$googlepay->schema_url    ."\" google-order-number=\"". $google_order. "\"> 
                     	 <send-email> " . $send_mail . "</send-email>";
        if(isset($carrier_select) &&  ($carrier_select != 'select') && isset($tracking_number) && !empty($tracking_number)) {
					$postargs .=	"<tracking-data>
									        <carrier>" . $carrier_select . "</carrier>
									        <tracking-number>" . $tracking_number . "</tracking-number>
									    	 </tracking-data>";
					$comments = "Shipping Tracking Data:\n Carrier: " . $carrier_select . "\n Tracking Number: " . $tracking_number . "";
					tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($cust_notify) . "', '" . tep_db_input($comments)  . "')");
        }
				$postargs .=  "</deliver-order> ";
        fwrite($message_log, sprintf("\r\n%s\n",$postargs));
        send_google_req($googlepay->request_url, $googlepay->merchantid, $googlepay->merchantkey, 
	              $postargs, $message_log); 

        $postargs = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                     <archive-order xmlns=\"".$googlepay->schema_url."\" google-order-number=\"". $google_order. "\"/>";
        fwrite($message_log, sprintf("\r\n%s\n",$postargs));
        send_google_req($googlepay->request_url, $googlepay->merchantid, $googlepay->merchantkey, 
                        $postargs, $message_log); 
      }
    }
    
    if(isset($notify_comments)) {
      $send_mail = "false";
      if($cust_notify == 1) 
        $send_mail = "true";
      $postargs =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                   <send-buyer-message xmlns=\"http://checkout.google.com/schema/2\" google-order-number=\"". $google_order. "\">
                   <send-email> " . $send_mail . "</send-email>
                   <message>". strip_tags($notify_comments) . "</message>
                   </send-buyer-message>";    
      fwrite($message_log, sprintf("\r\n%s\n",$postargs));
      send_google_req($googlepay->request_url, $googlepay->merchantid, $googlepay->merchantkey,
                      $postargs, $message_log);

    }
  }
  // ** END GOOGLE CHECKOUT ** 



?>
          </tr>
        </table></td>
      </tr>
<?php
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
