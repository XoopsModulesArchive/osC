<?php
/*
  $Id: print_my_invoice_footer.php,v 6.1 2005/06/05 22:30:54 PopTheTop Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_INCLUDES . 'counter.php');
?>
<BR>
<div align="center"><span class="copyright">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<TR>
		<td height="22" align="center" background="images/top1.jpg"><SMALL><SMALL><FONT FACE="Verdana" COLOR="#006699"><strong><?php echo PRINT_ORDER_FOOTER_TEXT ?></strong></font></SMALL></SMALL>&nbsp;<BR><?php echo tep_draw_separator('pixel_trans.gif', '100%', '1'); ?></td>
	</TR>
	<TR>
		<td height="9" background="images/base2.jpg"></td>
	</TR>
</TABLE>
</span></div>
