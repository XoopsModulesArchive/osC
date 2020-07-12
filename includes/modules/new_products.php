<?php
/**
 * $Id: new_products.php 64 2005-12-19 18:07:20Z Michael $
 * osCommerce, Open Source E-Commerce Solutions
 * http://www.oscommerce.com
 * Copyright (c) 2003 osCommerce
 * Released under the GNU General Public License
 * adapted 2005 for xoops by FlinkUX <http://www.flinkux.de>
 * @package xosC
 * @author Michael Hammelmann <michael.hammelmann@flinkux.de>
 * @version 1
**/
  $info_box_contents = array();
  $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));

  $header = new contentBoxHeading($info_box_contents);
  $header=$header->content;
  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    $new_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
  } else {
    $new_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, if(s.status, s.specials_new_products_price, p.products_price) as products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.parent_id = '" . (int)$new_products_category_id . "' and p.products_status = '1' order by p.products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
  }

  $row = 0;
  $col = 0;
  $info_box_contents = array();
  while ($new_products = tep_db_fetch_array($new_products_query)) {
    $new_products['products_name'] = tep_get_products_name($new_products['products_id']);
	$sproduct = new product($new_products['products_id']);
	$add_text='';
	$add_tax= '';

    if (  $customer_group->getdisplaytax() == '1') {
		$add_text='<br>'.TEXT_VAT_INFO.' '.$sproduct->get_tax().' % '.$sproduct->get_tax_title();
		$add_tax= tep_get_tax_rate($new_products['products_tax_class_id']);
	} 
    if( $customer_group->getdisplayshipment() == '1') {
	  $add_text.='<br>'.TEXT_SHIPPING_HANDLING_INFO;
	}
    $info_box_contents[$row][$col] = array('align' => 'center',
                                           'params' => 'class="smallText" width="33%" valign="top"',
                                           'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image($sproduct->get_image_path() . $sproduct->get_image(), $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . $new_products['products_name'] . '</a><br>' . $currencies->display_price($new_products['products_price'], $add_tax).$add_text);

    $col ++;
    if ($col > 2) {
      $col = 0;
      $row ++;
    }
  }

  $new_products = new contentBox($info_box_contents);
  $new_products=$new_products->content;
  $new_products = $header.$new_products;
  $xoopsTpl->assign("new_products",$new_products);
?>
