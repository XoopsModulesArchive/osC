<?php
/*
  $Id: customers.php,v 1.16 2003/07/09 01:18:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- customers //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

$heading[] = array('text' => BOX_HEADING_CUSTOMERS,
'link' => tep_href_link(FILENAME_CUSTOMERS, 'selected_box=customers'));

if ($selected_box == 'customers') {
$contents[] = array('text' => '<a href="' . tep_href_link(FILENAME_CUSTOMERS,  'selected_box=customers', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CUSTOMERS_CUSTOMERS . '</a><br>' .
'<a href="' . tep_href_link(FILENAME_CUSTOMER_GROUPS, 'selected_box=customers') . '" class="menuBoxContentLink">' . BOX_CUSTOMER_GROUPS . '</a><br>' .
'<a href="' . tep_href_link(FILENAME_ORDERS, 'selected_box=customers', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CUSTOMERS_ORDERS . '</a><br>' .
'<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, 'selected_box=customers', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_MANUAL_ORDER_CREATE_ACCOUNT . '</a><br>' .
'<a href="' . tep_href_link(FILENAME_CREATE_ORDER, 'selected_box=customers', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_MANUAL_ORDER_CREATE_ORDER . '</a>');
}

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- customers_eof //-->
