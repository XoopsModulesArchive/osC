<?php
/**
 * $Id: tools.php 46 2005-11-30 09:13:15Z Michael $
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
<!-- tools //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_TOOLS,
                     'link'  => tep_href_link(FILENAME_BACKUP, 'selected_box=tools'));

  if ($selected_box == 'tools') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_BACKUP) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_BACKUP . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_BANNER_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_CACHE) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_CACHE . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_DEFINE_LANGUAGE . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_FILE_MANAGER) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_FILE_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MAIL) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_MAIL . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_NEWSLETTERS) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_NEWSLETTER_MANAGER . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_SERVER_INFO) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_SERVER_INFO . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_WHOS_ONLINE) . '?selected_box=tools' . '" class="menuBoxContentLink">' . BOX_TOOLS_WHOS_ONLINE . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- tools_eof //-->
