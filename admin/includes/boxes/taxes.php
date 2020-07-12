<?php
/**
 * $Id: taxes.php 46 2005-11-30 09:13:15Z Michael $
 * osCommerce, Open Source E-Commerce Solutions
 * http://www.oscommerce.com
 * Copyright (c) 2002 osCommerce
 * Released under the GNU General Public License
 * adapted for xoops by michael hammelmann <michael.hammelmann@flinkux.de> <http://www.flinkux.de>
 * Copyright (c) 2005 FlinkUX Michael Hammelmann <michael.hammelmann@flinkux.de>
 * @package xosC
 * @subpackage admin
**/
?>
<!-- taxes //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_LOCATION_AND_TAXES,
                     'link'  => tep_href_link(FILENAME_COUNTRIES, 'selected_box=taxes'));

  if ($selected_box == 'taxes') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_COUNTRIES, 'selected_box=taxes', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TAXES_COUNTRIES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_ZONES, 'selected_box=taxes', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TAXES_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_GEO_ZONES, 'selected_box=taxes', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TAXES_GEO_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, 'selected_box=taxes', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TAXES_TAX_CLASSES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_RATES, 'selected_box=taxes', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_TAXES_TAX_RATES . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- taxes_eof //-->
