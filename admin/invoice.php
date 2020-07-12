<?php
/*
  $Id: invoice.php,v 6.1 2005/06/05 00:37:30 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  include '../../../include/cp_header.php';
  require('includes/application_top.php');
  require('../includes/classes/customer_group.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");

  include(DIR_WS_CLASSES . 'order.php');
  $order = new order($oID);
  $date = date('M d, Y');

// Customer Invoice in invoice.php
$the_extra_query= tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
$the_extra= tep_db_fetch_array($the_extra_query);
$the_customers_id= $the_extra['customers_id'];
$the_extra_query= tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $the_customers_id . "'");
$the_extra= tep_db_fetch_array($the_extra_query);
$the_customers_fax= $the_extra['customers_fax'];

// Customer Invoice in invoice.php
$customer_group= new customer_group($the_customers_id);

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo STORE_NAME; ?> <?php echo INVOICE_TEXT_INVOICE; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?><?php echo date("y"); ?><?php echo INVOICE_TEXT_DASH; ?><?php echo $oID; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script type="text/javascript" src='includes/admin_comments_popup.js'></script>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- body_text //-->

<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
	  <td align="left"><?php echo '<a href=# onClick="javascript:ShowHide(\'comments_open\',\'comments_close\');">'; ?><?php include(DIR_FS_DOCUMENT_ROOT . DIR_WS_LANGUAGES . "mainlogo.php"); ?></td>
	  <TD ALIGN="right" VALIGN="top"><FONT FACE="Verdana" SIZE="2" COLOR="#006699"><strong><?php echo INVOICE_TEXT_INVOICE; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?> <?php echo date("y"); ?><?php echo INVOICE_TEXT_DASH; ?> <?php echo $oID; ?><BR><?php echo $date; ?></strong></font></TD>
	</tr>
	<tr>
   	<td colspan="2">
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
    		<tr>
	 			<TD ALIGN="right" COLSPAN="2"><span class="pageHeadingSM"><FONT FACE="Verdana" SIZE="1" COLOR="#006699"><strong><?php echo nl2br(STORE_NAME_ADDRESS); ?></strong></font></span></TD>
			</tr>
			<tr>
				<TD>
				<table width="100%" border="0" cellspacing="0" cellpadding="2">
		        <tr>
					 <td colspan="4">
					 <table width="100%" border="0" cellspacing="0" cellpadding="2">
			        <tr>
	      		    <td width="10%"><hr size="2"></td>
			          <td align="center" class="pageHeading"><em><b><?php echo INVOICE_TEXT_INVOICE; ?></b></em></td>
      			    <td width="100%"><hr size="2"></td>
		   	     </tr>
			       </table>
			       </td>
				   </tr>
				   <tr>
				   	<td colspan="4"><?php echo tep_draw_separator('pixel_trans.gif', '100', '5'); ?></td>
				   </tr>
			      <tr>
		            <td width="3"> </td>
			      	<td valign="top">
			         <table width="100%" border="0" cellpadding="0" cellspacing="0">
         		     <tr>
		                <td width="11"><img src="../images/borders/maingrey_01.gif" width="11" height="16" alt=""></td>
      		          <td background="../images/borders/maingrey_02.gif"><img src="../images/borders/maingrey_02.gif" width="24" height="16" alt="" ></td>
            		    <td width="19"><img src="../images/borders/maingrey_03.gif" width="19" height="16" alt=""></td>
         		     </tr>
         		     <tr>
		                <td background="../images/borders/maingrey_04.gif"><img src="../images/borders/maingrey_04.gif" width="11" height="21" alt=""></td>
		                <td align="center" bgcolor="#F2F2F2">
							 	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="main">
            		        <tr>
                  		    <td align="left" valign="top"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
		                    </tr>
      		              <tr>
            		          <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                  		  </tr>
		                    <tr>
      		                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->customer['format_id'], $order->customer, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
            		        </tr>
                  		  <tr>
		                      <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      		              </tr>
            		        <tr>
                  		    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['telephone']; ?></td>
		                    </tr>
      		              <tr>
            		          <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $order->customer['email_address']; ?></td>
		                    </tr>
                  		  <tr>
		                      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
      		              </tr>
		                  </table>
							 </td>
		                <td background="../images/borders/maingrey_06.gif"><img src="../images/borders/maingrey_06.gif" width="19" height="21" alt=""></td>
         		     </tr>
         		     <tr>
		                <td><img src="../images/borders/maingrey_07.gif" width="11" height="18" alt=""></td>
      		          <td background="../images/borders/maingrey_08.gif"><img src="../images/borders/maingrey_08.gif" width="24" height="18" alt=""></td>
		                <td><img src="../images/borders/maingrey_09.gif" width="19" height="18" alt=""></td>
         		     </tr>
			         </table>
			         </td>
		            <td width="45"> </td>
			         <td valign="top">
		            <table width="100%" border="0" cellpadding="0" cellspacing="0">
      		        <tr>
            		    <td width="11"><img src="../images/borders/mainwhite_01.gif" width="11" height="16" alt=""></td>
		                <td background="../images/borders/mainwhite_02.gif"><img src="../images/borders/mainwhite_02.gif" width="24" height="16" alt=""></td>
      		          <td width="19"><img src="../images/borders/mainwhite_03.gif" width="19" height="16" alt=""></td>
            		  </tr>
		              <tr>
      		          <td background="../images/borders/mainwhite_04.gif"><img src="../images/borders/mainwhite_04.gif" width="11" height="21" alt=""></td>
            		    <td align="center" bgcolor="#FFFFFF">
								 <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
   	   		             <tr>
      	      		         <td align="left" valign="top"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
		   	                </tr>
      		              <tr>
            		          <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
                  		  </tr>
		               	    <tr>
	      		               <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, '', '<br>&nbsp;&nbsp;&nbsp;&nbsp;'); ?></td>
   	         		       </tr>
                  		  <tr>
		                      <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      		              </tr>
            		        <tr>
                  		    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
      		              <tr>
            		          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    </tr>
                  		  <tr>
		                      <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
      		              </tr>
								 </table>
							 </td>
		                <td background="../images/borders/mainwhite_06.gif"><img src="../images/borders/mainwhite_06.gif" width="19" height="21" alt=""></td>
      		        </tr>
            		  <tr>
		                <td><img src="../images/borders/mainwhite_07.gif" width="11" height="18" alt=""></td>
      		          <td background="../images/borders/mainwhite_08.gif"><img src="../images/borders/mainwhite_08.gif" width="24" height="18" alt=""></td>
            		    <td><img src="../images/borders/mainwhite_09.gif" width="19" height="18" alt=""></td>
		              </tr>
      		      </table>
			         </td>
			      </tr>
			    </table>
				 </TD>
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
            		   <td width="11"><img src="../images/borders/maingrey_01.gif" width="11" height="16" alt=""></td>
		               <td background="../images/borders/maingrey_02.gif"><img src="../images/borders/maingrey_02.gif" width="24" height="16" alt="" ></td>
      		         <td width="19"><img src="../images/borders/maingrey_03.gif" width="19" height="16" alt=""></td>
  				       </tr>
  				       <tr>
		               <td background="../images/borders/maingrey_04.gif"><img src="../images/borders/maingrey_04.gif" width="11" height="21" alt=""></td>
      		         <td align="center" bgcolor="#F2F2F2">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">
							 	<tr>
	   		   		      <td width="33%">&nbsp;<b><?php echo INVOICE_TEXT_ORDER; ?> <?php echo INVOICE_TEXT_NUMBER_SIGN; ?><?php echo INVOICE_TEXT_COLON; ?></b> <?php echo tep_db_input($oID); ?></td>
			   		         <td width="33%">&nbsp;<b><?php echo INVOICE_TEXT_DATE_OF_ORDER; ?><?php echo INVOICE_TEXT_COLON; ?> </b><?php echo tep_date_short($order->info['date_purchased']); ?></td>
<?php
  if (tep_not_null($order->info['cc_number']))  {
    $this->cc_card_number_less_middle_digits = substr($order->info['cc_number'], 0, 4) . str_repeat('x', (strlen($order->info['cc_number']) - 8)) . substr($order->info['cc_number'], -4);
?>
      			   		   <td>&nbsp;<b><?php echo ENTRY_PAYMENT_METHOD; ?></b>&nbsp;<?php echo $order->info['payment_method']; ?>&nbsp;(<?php echo $order->info['cc_type']; ?>)<br><?php echo tep_draw_separator('pixel_trans.gif', '100%', '6'); ?><br>&nbsp;<b><?php echo ENTRY_PAYMENT_CC_NUMBER; ?></b>&nbsp;<?php echo $this->cc_card_number_less_middle_digits; ?></td>
<?php
  } else {
?>
      			   		   <td>&nbsp;<b><?php echo ENTRY_PAYMENT_METHOD; ?></b>&nbsp;<?php echo $order->info['payment_method']; ?></td>
<?php
  }
?>
				   	      </tr>
				            <tr>
		      		      	<td><?php echo tep_draw_separator('pixel_trans.gif', '1', '7'); ?></td>
		            		</tr>
							</table>
							</td>
  				       	<td background="../images/borders/maingrey_06.gif"><img src="../images/borders/maingrey_06.gif" width="19" height="21" alt=""></td>
  				       </tr>
  				       <tr>
  				       	<td><img src="../images/borders/maingrey_07.gif" width="11" height="18" alt=""></td>
  				       	<td background="../images/borders/maingrey_08.gif"><img src="../images/borders/maingrey_08.gif" width="24" height="18" alt=""></td>
  				       	<td><img src="../images/borders/maingrey_09.gif" width="19" height="18" alt=""></td>
  				       </tr>
  				    </table>
                </td>
  		        </tr>
            </table>
				</td>
		  </tr>
		  <tr>
				<TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '15'); ?></td>
		  </tr>
		  <tr>
    			<TD COLSPAN="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  		  </tr>
  		  <tr>
    			<TD COLSPAN="2">
			   <table border="0" width="100%" cellspacing="0" cellpadding="2">
		        <tr class="dataTableHeadingRow">
	   		     <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
		   	     <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
      			  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
			        <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
   			     <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
		      	  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
	   		     <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
		        </tr>
<?php
   for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
	   echo '		        <tr class="dataTableRow">' . "\n" .
      	  '	   		     <td class="dataTableContent" valign="top" align="right">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
      	  '	   		     <td class="dataTableContent" valign="top">' . $order->products[$i]['name'];

      if (isset($order->products[$i]['attributes']) && (($k = sizeof($order->products[$i]['attributes'])) > 0)) {
        for ($j = 0; $j < $k; $j++) {
          echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
          if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '	   		     </td>' . "\n" .
           '	   		     <td class="dataTableContent" valign="top">' . $order->products[$i]['model'] . '</td>' . "\n";
      echo '	   		     <td class="dataTableContent" align="right" valign="top">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n" .
           '	   		     <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '	   		     <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '	   		     <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" .
           '	   		     <td class="dataTableContent" align="right" valign="top"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n";
      echo '		        </tr>' . "\n";
   }
?>
      			<tr>
      				<td align="right" colspan="8">
						<table border="0" cellspacing="0" cellpadding="2">
<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo '      			<tr>' . "\n" .
         '      				<td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '      				<td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '      			</tr>' . "\n";
  }
?>
      		</table>
				</td>
      	</tr>
      </table></td>
	</tr>
</table>
<!-- ORDER COMMENTS CODE STARTS HERE //-->
<div id="comments_open" style="position: relative;">
<?php 
	$orders_status_history_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
	if (tep_db_num_rows($orders_status_history_query)) {
 	  $has_comments = false;
     echo '      <br><br>';
     echo '      <table width="100%" border="0" cellpadding="0" cellspacing="0">';
     echo '      	<tr>';
     echo '      		<td width="9"> </td>';
     echo '      		<td>';
     echo '      		<table width="100%" border="0" cellpadding="0" cellspacing="0">';
     echo '      			<tr>';
     echo '      				<td width="11"><img src="../images/borders/maingrey_01.gif" width="11" height="16" alt=""></td>';
     echo '      				<td background="../images/borders/maingrey_02.gif"><img src="../images/borders/maingrey_02.gif" width="24" height="16" alt="" ></td>';
     echo '      				<td width="19"><img src="../images/borders/maingrey_03.gif" width="19" height="16" alt=""></td>';
     echo '      			</tr>';
     echo '      			<tr>';
     echo '      				<td background="../images/borders/maingrey_04.gif"><img src="../images/borders/maingrey_04.gif" width="11" height="21" alt=""></td>';
     echo '      				<td align="center" bgcolor="#F2F2F2">';
     echo '      				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">';
     echo '      					<tr>';
     echo '      						<td width="95%">&nbsp;<b>' . TABLE_HEADING_COMMENTS . '</b><br><br></td>';
     echo '      					</tr>';

     while ($orders_comments = tep_db_fetch_array($orders_status_history_query)) {
     	 if (tep_not_null($orders_comments['comments'])) {
	      $has_comments = true; // Not Null = Has Comments
	      if (tep_not_null($orders_comments['comments'])) {
           $sInfo = new objectInfo($orders_comments);
           echo '      					<tr>';
           echo '      						<td align="center" width="95%">';
           echo '      						<table width="95%" border="0" cellpadding="0" cellspacing="0">';
           echo '      							<tr>';
           echo '      								<td width="95%" class="smallText">';
           echo '      								<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">';
           echo '      									<tr>';
           echo '      										<td width="150" align="left" valign="top" class="smallText"><strong><u>' . TABLE_HEADING_DATE_ADDED . '</u></strong></td>';
           echo '      										<td align="left" valign="top" class="smallText"><strong><u>' . TABLE_HEADING_COMMENT_LEFT . '</u></strong></td>';
           echo '      									</tr>';
           echo '      								</table>';
           echo '      								</td>';
           echo '      							</tr>';
           echo '      						</table>';
           echo '      						</td>';
           echo '      					</tr>';
           echo '      					<tr>';
           echo '      						<td align="center" width="95%">';
           echo '      						<table width="95%" border="0" cellpadding="0" cellspacing="0">';
           echo '      							<tr>';
           echo '      								<td width="95%" class="smallText">';
           echo '      								<table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">';
           echo '      									<tr>';
           echo '      										<td width="150" align="left" valign="top" class="smallText">' . tep_date_short($sInfo->date_added) . '</td>';
           echo '      										<td align="left" valign="top" class="smallText">' . nl2br(tep_db_output($orders_comments['comments'])) . '<br><br></td>';
			  echo '      									</tr>';
			  echo '      								</table>';
           echo '      								</td>';
			  echo '      							</tr>';
           echo '      						</table>';
           echo '      						</td>';
           echo '      					</tr>';
	      }
       }
     }
     if ($has_comments == false) {
		 echo '           <tr>';
		 echo '            <td align="center" width="95%">';
		 echo '            <table width="95%" border="0" cellpadding="0" cellspacing="0">';
		 echo '             <tr>';
		 echo '              <td width="95%" class="smallText">';
		 echo '              <table width="100%" border="0" cellpadding="0" cellspacing="0" class="main">';
		 echo '               <tr>';
		 echo '                <td width="100%" align="left" valign="top" class="smallText">' . INVOICE_TEXT_NO_COMMENT . '</td>';
		 echo '               </tr>';
		 echo '              </table>';
		 echo '              </td>';
		 echo '             </tr>';
		 echo '            </table>';
		 echo '            </td>';
		 echo '           </tr>';
     }
	  echo '      					<tr>';
	  echo '      						<td>' . tep_draw_separator('pixel_trans.gif', '1', '7') . '</td>';
	  echo '      					</tr>';
	  echo '      				</table>';
	  echo '      				</td>';
	  echo '      				<td background="../images/borders/maingrey_06.gif"><img src="../images/borders/maingrey_06.gif" width="19" height="21" alt=""></td>';
	  echo '      			</tr>';
	  echo '      			<tr>';
	  echo '      				<td><img src="../images/borders/maingrey_07.gif" width="11" height="18" alt=""></td>';
	  echo '      				<td background="../images/borders/maingrey_08.gif"><img src="../images/borders/maingrey_08.gif" width="24" height="18" alt=""></td>';
	  echo '      				<td><img src="../images/borders/maingrey_09.gif" width="19" height="18" alt=""></td>';
	  echo '      			</tr>';
	  echo '      		</table>';
	  echo '      		</td>';
	  echo '      	</tr>';
	  echo '      </table>';
	}
?>
<!-- ORDER COMMENTS CODE ENDS HERE //-->
</div>
<br>
<CENTER><span class="smallText"><FONT FACE="Verdana" COLOR="#006699"><strong><?php echo INVOICE_TEXT_THANK_YOU; ?><BR><?php echo STORE_NAME; ?><BR><?php echo STORE_URL_ADDRESS; ?></strong></font></span></CENTER>
<!-- body_text_eof //-->
</body>
</html>
<?php 
// require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
