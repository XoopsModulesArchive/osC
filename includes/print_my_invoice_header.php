<?php
/*
  $Id: print_my_invoice_header.php,v 6.1 2005/06/05 18:20:38 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="header">
    <td valign="center" align="left"><?php echo tep_image(DIR_WS_IMAGES . 'oscommerce.gif', ''); ?></td>
	 <td class="main" valign="center" align="right"><B><?php echo nl2br(STORE_NAME_ADDRESS); ?></B></td>
	 <td class="main" valign="center" align="right"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
  </tr>
</table>

<table border="0" width="100%" height="27"cellspacing="0" cellpadding="0">
	<TR>
		<TD HEIGHT="22" ALIGN="center" VALIGN="middle" BACKGROUND="images/top1.jpg"><FONT FACE="Verdana" COLOR="#006699"><strong><a class="gensmall" href="#" onclick="window.print();return false"><?php echo PRINT_ORDER_HEADER_TEXT ?></a></strong></font></TD>
	</TR>
	<TR>
		<TD background="images/top2.jpg"><IMG SRC="images/top2.jpg" WIDTH=4 HEIGHT=10></TD>
	</TR>
</table>

<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message'])) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="headerError">
    <td class="headerError"><?php echo htmlspecialchars(urldecode($HTTP_GET_VARS['error_message'])); ?></td>
  </tr>
</table>
<?php
  }

  if (isset($HTTP_GET_VARS['info_message']) && tep_not_null($HTTP_GET_VARS['info_message'])) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="headerInfo">
    <td class="headerInfo"><?php echo htmlspecialchars($HTTP_GET_VARS['info_message']); ?></td>
  </tr>
</table>
<?php
  }
?>