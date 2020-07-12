<?php
/**
 * $Id: header.php 53 2005-12-01 10:33:38Z Michael $

 * osCommerce, Open Source E-Commerce Solutions
 * http://www.oscommerce.com

 * Copyright (c) 2002 osCommerce

 * Released under the GNU General Public License

 * adapted for xoops by michael hammelmann <michael.hammelmann@flinkux.de> <http://www.flinkux.de>
 
 * Copyright (c) 2005 FlinkUX Michael Hammelmann <michael.hammelmann@flinkux.de>
 
 * Updated and added functionality by Robert Stolorz <http://www.FeraTechInc.com>

 * Copyright (c) 2007 Fera Tech, Inc

 * @package xosC
 * @subpackage admin
**/

  if ($messageStack->size > 0) {
    echo $messageStack->output();
  }
?>

<script language="javascript" type="text/javascript" src="../tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins : "ibrowser",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,fontsizeselect,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,charmap",
	theme_advanced_buttons2 : "cut,copy,paste,separator,undo,redo,separator,link,unlink,separator,forecolor,backcolor,separator,help,code,ibrowser",
	theme_advanced_buttons3 : "",
	convert_urls : false
});
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo tep_image(DIR_WS_IMAGES . 'ferastore_logo.png', 'xosC', '92', '52'); ?></td>
    <td align="right"><?php echo '<a href="http://www.feratechinc.com" target="_blank">' . tep_image(DIR_WS_IMAGES . 'header_support.gif', HEADER_TITLE_SUPPORT_SITE, '50', '50') . '</a>&nbsp;&nbsp;<a href="' . tep_catalog_href_link() . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_ONLINE_CATALOG, '53', '50') . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_administration.gif', HEADER_TITLE_ADMINISTRATION, '50', '50') . '</a>'; ?>&nbsp;&nbsp;</td>
  </tr>
  <tr class="headerBar">
    <td class="headerBarContent">&nbsp;&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_TOP . '</a>'; ?></td>
    <td class="headerBarContent" align="right"><?php echo '<a href="http://feratechinc.com" class="headerLink">' . HEADER_TITLE_SUPPORT_SITE . '</a> &nbsp;|&nbsp; <a href="' . tep_catalog_href_link() . '" class="headerLink">' . HEADER_TITLE_ONLINE_CATALOG . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL') . '" class="headerLink">' . HEADER_TITLE_ADMINISTRATION . '</a>'; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
