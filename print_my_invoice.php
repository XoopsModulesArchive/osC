<?php
/*
  $Id: print_my_invoice.php,v 6.1 2005/06/05 23:03:52 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($HTTP_GET_VARS['order_id']) || (isset($HTTP_GET_VARS['order_id']) && !is_numeric($HTTP_GET_VARS['order_id']))) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }
  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
  $customer_info_query = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '". (int)$HTTP_GET_VARS['order_id'] . "'");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRINT_MY_INVOICE);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $HTTP_GET_VARS['order_id']), tep_href_link(FILENAME_PRINT_MY_INVOICE, 'order_id=' . $HTTP_GET_VARS['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($HTTP_GET_VARS['order_id']);
  $date = date('M d, Y');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo STORE_NAME; ?> <?php echo INVOICE_TEXT_INVOICE; ?> <?php// echo INVOICE_TEXT_NUMBER_SIGN; ?><?php// echo date("y"); ?><?php// echo INVOICE_TEXT_DASH; ?><?php echo $oID; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" onload="window.print();return false">
<!-- body_text //-->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
      <TD ALIGN="left" VALIGN="top"><span class="pageHeadingSM"><FONT FACE="Verdana" SIZE="1" COLOR="#006699"><strong><?php echo nl2br(STORE_NAME_ADDRESS); ?></strong></font></span></td>
	   <td style="padding: 10px 10px 0px 0px; vertical-align: top;" align="right"><?php echo '<input type="image" src="' . INVOICE_IMAGE . '" width="' . INVOICE_IMAGE_WIDTH . '" height="' . INVOICE_IMAGE_HEIGHT . '" " alt="' . INVOICE_IMAGE_ALT_TEXT . '" onClick="javascript:ShowHide(\'comments_open\',\'comments_close\');">'; ?></td>

</tr>
<tr>
<TD ALIGN="left" VALIGN="top">
<table>
<tr><td><?php echo INVOICE_TEXT_INVOICE_NR; ?></td><td><?php// echo date("y"); ?><?php// echo INVOICE_TEXT_NUMBER_SIGN; ?><?php// echo INVOICE_TEXT_DASH; ?> <?php echo $HTTP_GET_VARS['order_id']; ?></td>
<tr><td><?php echo INVOICE_TEXT_INVOICE_DATE; ?></td><td><?php echo tep_date_short($order->info['date_purchased']); ?></td>
</tr></table>
</TD>

  </tr>
 <tr>
   <td colspan="2">
     <table border="0" width="100%" cellspacing="0" cellpadding="0">
       
       <tr>
         <TD>
           <table width="100%" border="0" cellspacing="0" cellpadding="2">
             <tr>
               <td colspan="4">
                 <table width="100%" border="0" cellspacing="0" cellpadding="2">
                   <tr>
                     <td width="10%"><hr size="2"></td>
                     <td align="center" class="pageHeading_INVOICE"><em><b><?php echo INVOICE_TEXT_INVOICE; ?></b></em></td>
                     <td width="100%"><hr size="2"></td>
                   </tr>
               </table></td>
             </tr>
             <tr>
               <td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '100', '5'); ?></td>
             </tr>
             <tr>
               <td width="3"> </td>
               <td valign="top">
                 <table width="100%" border="0" cellpadding="0" cellspacing="0">
                   <tr>
                     <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
                     <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="100%" height="16" alt="" ></td>
                     <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
                   </tr>
                   <tr>
                     <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="100%" alt=""></td>
                     <td align="center" style="background-color: white; ">
                       <table width="100%" border="0" cellspacing="0" cellpadding="0" class="main_INVOICE">
                         <tr>
                           <td align="left" valign="top" class="order_infobox_heading_INVOICE"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['telephone']; ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['email_address']; ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE"><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
                         </tr>
                     </table></td>
                     <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="100%" alt=""></td>
                   </tr>
                   <tr>
                     <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
                     <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="100%" height="18" alt=""></td>
                     <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
                   </tr>
               </table></td>
               <td width="45"> </td>
               <td valign="top">
                 <table width="100%" border="0" cellpadding="0" cellspacing="0">
                   <tr>
                     <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
                     <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="100%" height="16" alt=""></td>
                     <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
                   </tr>
                   <tr>
                     <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="100%" alt=""></td>
                     <td align="center" style="background-color: white; ">
                       <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main_INVOICE">
                         <tr>
                           <td align="left" valign="top" class="order_infobox_heading_INVOICE"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                         </tr>
                         <tr>
                           <td class="order_infobox_data_INVOICE"><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
                         </tr>
                     </table></td>
                     <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="100%" alt=""></td>
                   </tr>
                   <tr>
                     <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
                     <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="100%" height="18" alt=""></td>
                     <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
                   </tr>
               </table></td>
             </tr>
         </table></TD>
       </tr>
       <tr>
         <TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '100', '15'); ?></td>
       </tr>
       <tr>
         <TD COLSPAN="2">
           <table width="100%" border="0" cellpadding="0" cellspacing="0">
             <tr>
               <td width="9"> </td>
               <td>
                 <table width="100%" border="0" cellpadding="0" cellspacing="0">
                   <tr>
                     <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
                     <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="100%" height="16" alt="" ></td>
                     <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
                   </tr>
                   <tr>
                     <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="100%" alt=""></td>
                     <td align="center" style="background-color: white; ">
                       <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main_INVOICE">
                         <tr>
                          <td width="25%" class="order_infobox_data_INVOICE"><strong>&nbsp;<?php echo INVOICE_TEXT_ORDER; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?><?php echo INVOICE_TEXT_COLON; ?></strong> <?php echo $HTTP_GET_VARS['order_id']; ?></td>
                          <td width="25%" class="order_infobox_data_INVOICE"><strong>&nbsp;<?php echo INVOICE_TEXT_DATE_OF_ORDER; ?><?php echo INVOICE_TEXT_COLON; ?> </strong><?php echo tep_date_short($order->info['date_purchased']); ?></td>
                          



<?php
  if (tep_not_null($order->info['cc_number']))  {
    $this->cc_card_number_less_middle_digits = substr($order->info['cc_number'], 0, 4) . str_repeat('x', (strlen($order->info['cc_number']) - 8)) . substr($order->info['cc_number'], -4);
?>
                           <td class="order_infobox_data_INVOICE"><span class="order_infobox_heading_INVOICE">&nbsp;<b><?php echo ENTRY_PAYMENT_METHOD; ?></b></span>&nbsp;<?php echo $order->info['payment_method']; ?>&nbsp;(<?php echo $order->info['cc_type']; ?>)<br>
                               <?php echo tep_draw_separator('pixel_trans.gif', '100%', '6'); ?><br>
                               <span class="order_infobox_heading_INVOICE">&nbsp;<b><?php echo ENTRY_PAYMENT_CC_NUMBER; ?></b></span>&nbsp;<?php echo $this->cc_card_number_less_middle_digits; ?></td>
<?php
  } else {
?>
                           <td class="order_infobox_data_INVOICE"><span class="order_infobox_heading_INVOICE">&nbsp;<b><?php echo ENTRY_PAYMENT_METHOD; ?></b></span>&nbsp;<?php echo $order->info['payment_method']; ?></td>

<?php
  }
?>


                        </tr>
                         <tr>
                           <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
                         </tr>
                     </table></td>
                     <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="100%" alt=""></td>
                   </tr>
                   <tr>
                     <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
                     <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="100%" height="18" alt=""></td>
                     <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
                   </tr>
               </table></td>
             </tr>
         </table></td>
       </tr>
       <tr>
         <TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
       </tr>
       <tr>
         <TD COLSPAN="2" align="center">
			<table border="0" width="98%" cellspacing="0" cellpadding="2">
             <tr class="dataTableHeadingRow_INVOICE">
               <td colspan="2" class="dataTableHeadingContent_INVOICE">&nbsp;<font color="#000000"><?php echo TABLE_HEADING_PRODUCTS; ?></font></td>
               <td WIDTH="80" class="dataTableHeadingContent_INVOICE"><font color="#000000"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></font></td>
               <td WIDTH="80" align="right" class="dataTableHeadingContent_INVOICE"><font color="#000000"><?php echo TABLE_HEADING_UNIT_PRICE; ?></font></td>
               <TD WIDTH="80" ALIGN="right" CLASS="dataTableHeadingContent_INVOICE"><font color="#000000"><?php echo TABLE_HEADING_TOTAL; ?></font>&nbsp;</TD>
             </tr>
<?php
   for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
	  echo '      <tr class="dataTableRow_INVOICE">' . "\n" .
			 '        <td class="dataTableContent_INVOICE" valign="top" align="left">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
			 '        <td class="dataTableContent_INVOICE" valign="top" align="left">' . $order->products[$i]['name'];

     if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
       for ($j = 0; $j < $k; $j++) {
         echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
         if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
         echo '</i></small></nobr>';
       }
     }

     echo '          </td>' . "\n" .
          '          <td WIDTH="80" class="dataTableContent_INVOICE" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n";
     echo '          <td WIDTH="80" class="dataTableContent_INVOICE" align="right" valign="top">' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
          '          <td WIDTH="80" class="dataTableContent_INVOICE" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '&nbsp;</b></td>' . "\n";
     echo '         </tr>' . "\n";
   }
?>
             <tr>
               <td align="right" colspan="5">
                 <table border="0" cellspacing="0" cellpadding="2">
<?php
 for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
   echo '         <tr>' . "\n" .
        '          <td align="right" class="smallText_INVOIVE">' . $order->totals[$i]['title'] . '</td>' . "\n" .
        '          <td align="right" class="smallText_INVOIVE">' . $order->totals[$i]['text'] . '</td>' . "\n" .
        '         </tr>' . "\n";
 }
?>
               </table></td>
             </tr>
         </table></td>
       </tr>
		 <!-- ORDER COMMENTS CODE STARTS HERE //-->
       <tr>
         <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '18'); ?></td>
       </tr>
       <tr>
      	<TD>
         <table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
               <td width="9"> </td>
               <td>
		         <table width="100%" border="0" cellpadding="0" cellspacing="0">
  				     <tr>
            	    <td width="11"><img src="images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
		             <td background="images/borders/mainwhite_02.gif"><img src="images/borders/mainwhite_02.gif" width="100%" height="16" alt="" ></td>
      		       <td width="19"><img src="images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
  				     </tr>
  				     <tr>
		             <td background="images/borders/mainwhite_04.gif"><img src="images/borders/mainwhite_04.gif" width="11" height="100%" alt=""></td>
      		       <td align="center" style="background-color: white;">
						 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
   	   		       <tr>
      	      	       <td colspan="3" align="left" valign="top" class="order_infobox_heading"><b><?php echo TEXT_INFO_ORDERS_STATUS_NAME; ?></b></td>
		   	          </tr>
<?php
  $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$HTTP_GET_VARS['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' order by osh.date_added");
  while ($statuses = tep_db_fetch_array($statuses_query)) {
      echo '		   	          <tr>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data" width="114"><br>&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . tep_date_short($statuses['date_added']) . '</strong></td>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data" width="85"><br>' . $statuses['orders_status_name'] . '</td>' . "\n";
      echo '            		       <td valign="top" class="product_infobox_data"><br>' . (empty($statuses['comments']) ? '&nbsp;' : nl2br(tep_output_string_protected($statuses['comments']))) . '</td>' . "\n";
      echo '		   	          </tr>' . "\n";
  }
?>
   	   		       <tr>
            		       <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '7'); ?></td>
		   	          </tr>
						 </table>
						 </td>
						 <td background="images/borders/mainwhite_06.gif"><img src="images/borders/mainwhite_06.gif" width="19" height="100%" alt=""></td>
  				     </tr>
  				     <tr>
  				       <td><img src="images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
  				       <td background="images/borders/mainwhite_08.gif"><img src="images/borders/mainwhite_08.gif" width="100%" height="18" alt=""></td>
  				       <td><img src="images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
  				     </tr>
		         </table>
		         </td>
				</tr>
         </table>
<!-- ORDER COMMENTS CODE ENDS HERE //-->
<br>
<CENTER><span class="smallText_INVOIVE"><FONT FACE="Verdana" COLOR="#006699"><strong><?php echo INVOICE_TEXT_THANK_YOU; ?><BR><?php echo STORE_NAME; ?><BR><?php echo STORE_URL_ADDRESS; ?></strong></font></span></CENTER>
<!-- body_text_eof //-->

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
