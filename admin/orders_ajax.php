<?php
  //header('Content-type: text/html; charset=ISO-8859-1');
  require('includes/application_top.php');
  include '../../../include/cp_header.php';  
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  
  function tep_db_update_totals($order_id) {
	  //we have to update the orders_total table
	  $products_total_query = tep_db_query("select final_price, products_quantity, products_tax from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $order_id . "'");
	  $price = 0;
	  $total = 0;
	  $taxes = array();
	  while ($products_total = tep_db_fetch_array($products_total_query)) {
		  $iva = round((100 + (float)$products_total['products_tax']) / 100, 4);
		  $price += (float)round(((float)$products_total['final_price'] * (int)$products_total['products_quantity']) * $iva, 2);
		  //fill the array of taxes to know which tax is used.
		  if (tep_not_null($products_total['products_tax'])) {
			  $tax_description_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " where tax_rate = '" . $products_total['products_tax'] . "'");
			  $tax_description = tep_db_fetch_array($tax_description_query);
			  
			  if (sizeof($taxes)) {
				  $ya_esta = false;
				  for ($i=0; $i<sizeof($taxes); $i++) {
					  if (in_array($tax_description['tax_description'] . ':', $taxes[$i])) {
						  $ya_esta = $i;
					  }
				  }
				  if ($ya_esta === false) {
					  $taxes[] = array('description' => $tax_description['tax_description'] . ':', 'value' => round(((((float)$products_total['final_price'] * (int)$products_total['products_quantity']) * (float)$products_total['products_tax']) / 100), 4));
				  } else {
					  $taxes[$ya_esta]['value'] += round(((((float)$products_total['final_price'] * (int)$products_total['products_quantity']) * (float)$products_total['products_tax']) / 100), 4);
				  }
			  } else {
				  $taxes[] = array('description' => $tax_description['tax_description'] . ':', 'value' => round(((((float)$products_total['final_price'] * (int)$products_total['products_quantity']) * (float)$products_total['products_tax']) / 100), 4));
			  }
		  }
	  }
	  
	  $orders_total_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class != 'ot_tax' order by sort_order");//first anything but the tax (this part is trickier, let's do it appart)
	  while ($order_total = tep_db_fetch_array($orders_total_query)) {
		  if ($order_total['class'] == 'ot_subtotal') {
			  if ($order_total['value'] == 0.0000) {//because of create_order problem
				  $old_value_txt = '0.00';
			  } else {
				  $old_value_txt = round((float)$order_total['value'], 2);
			  }
			  $new_value = (float)$price;
			  $new_text = str_replace($old_value_txt, round($new_value, 2), $order_total['text']);
			  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set text = '" . $new_text . "', value = '" . $new_value . "' where orders_total_id = '" . $order_total['orders_total_id'] . "' and class = 'ot_subtotal'");
			  $total += (float)$new_value;
		  } elseif ($order_total['class'] == 'ot_total') {
			  if ($order_total['value'] == 0.0000) {//because of create_order problem
				  $old_value_txt = '0.00';
			  } else {
				  $old_value_txt = round((float)$order_total['value'], 2);
			  }
			  $new_value = (float)$total;
			  $new_text = str_replace($old_value_txt, round($new_value, 2), $order_total['text']);
			  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set text = '" . $new_text . "', value = '" . $new_value . "' where orders_total_id = '" . $order_total['orders_total_id'] . "' and class = 'ot_total'");
		  } else {
			  $total += round((float)$order_total['value'], 4);
		  }
	  }
	  //the taxes
	  if (sizeof($taxes)) {
		  $orders_total_tax_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $order_id . "' and class = 'ot_tax'");
		  //update the ot_tax with the same title
		  //if title doesn't exist, insert it
		  $tax_updated = array();
		  while ($orders_total_tax = tep_db_fetch_array($orders_total_tax_query)) {
			  $eliminate_tax = true;
			  for ($i=0; $i<sizeof($taxes); $i++) {
				  if (in_array($orders_total_tax['title'], $taxes[$i])) {
					  $eliminate_tax = false;
					  //keep in variable that this tax is done
					  $tax_updated[] = $orders_total_tax['title'];
				  	  //first check if the ot_tax with that title already exists, otherwise, create it
					  $old_value_txt = round((float)$orders_total_tax['value'], 2);
					  $new_value = $taxes[$i]['value'];
					  $new_value_txt = round((float)$new_value, 2);
					  $new_text = str_replace($old_value_txt, (string)$new_value_txt, $orders_total_tax['text']);
					  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set text = '" . $new_text . "', value = '" . $new_value . "' where orders_total_id = '" . $orders_total_tax['orders_total_id'] . "' and class = 'ot_tax'");
				  }
			  }
			  if ($eliminate_tax == true) {//we have eliminate the last product of one tax_rate->eliminate the ot_field
				  tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_total_id = '" . $orders_total_tax['orders_total_id'] . "' limit 1");
			  }
		  }
		  //insert a new tax rate in the orders_total table, if all of taxes[] is not in $tax_updated[]
		  for ($i=0; $i<sizeof($taxes); $i++) {
			  if ((!in_array($taxes[$i]['description'], $tax_updated)) && ((float)$taxes[$i]['value'] > 0)) {
				  //get the order's currency
				  $currency_query = tep_db_query("select currency from " . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
				  $currency = tep_db_fetch_array($currency_query);
				  //prepare text (value with currency)
				  $texto = round((float)$taxes[$i]['value'], 2);
				  $texto = (string)$texto . $currency['currency'];
				  tep_db_query("insert into " . TABLE_ORDERS_TOTAL . " (orders_id, title, text, value, class, sort_order) values ('" . $order_id . "', '" . $taxes[$i]['description'] . "', '" . $texto . "', '" . $taxes[$i]['value'] . "', 'ot_tax', '" . $orders_total_tax['sort_order'] . "')");
			  }
		  }
	  }
  }
  
  $action = $HTTP_GET_VARS['action'];
  if (($action == 'eliminate') || ($action == 'update_product')) {//eliminate or modify product
	  if ($action == 'eliminate') {
		  tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "' limit 1");
		  $attributes_query = tep_db_query("select orders_products_attributes_id from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "'");
		  if (tep_db_num_rows($attributes_query)) {//if the products has attributes, eliminate them
			  while ($attributes = tep_db_fetch_array($attributes_query)) {
				  tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_attributes_id = '" . $attributes['orders_products_attributes_id'] . "' limit 1");
			  }
		  }
	  } else {
		  //get the price to change order totals
		  //but first, change it if we change price of attributes (so we get directly the good final_price)
		  $field = $HTTP_GET_VARS['field'];
		  if ($field == 'options') {
			  tep_db_query("update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set products_options_values = '" . $HTTP_GET_VARS['new_value'] . "',  options_values_price = '" . round((float)$HTTP_GET_VARS['option_price'], 4) . "' where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "' and products_options = '" . $HTTP_GET_VARS['extra'] . "'");
			  tep_db_query("update " . TABLE_ORDERS_PRODUCTS . " set final_price = (products_price + '" . round((float)$HTTP_GET_VARS['option_price'], 4) . "') where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "'");
		  } elseif (stristr($field, 'price')) {
			  $adapt_price_query = tep_db_query("select options_values_price from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "' and options_values_price != 0");
			  if (tep_db_num_rows($adapt_price_query)) {
				  $adapt_price = tep_db_fetch_array($adapt_price_query);
				  $option_price = (float)$adapt_price['options_values_price'];
			  } else {
				  $option_price = 0;
			  }
			  if (stristr($field, '_excl')) {
				  $new_price = round((float)$HTTP_GET_VARS['new_value'], 4);
			  } else {
				  $tax_query = tep_db_query("select products_tax from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "' and products_tax != 0");
				  if (tep_db_num_rows($tax_query)) {
					  $tax_ = tep_db_fetch_array($tax_query);
					  $percent = (float)$tax_['products_tax'];
					  $percent = round(($percent/100), 4);
					  $percent = $percent + 1;
					  $new_price = round(round((float)$HTTP_GET_VARS['new_value']/$percent, 4), 4);
				  } else {
					  $new_price = round((float)$HTTP_GET_VARS['new_value'], 4);
				  }
			  }
			  tep_db_query("update " . TABLE_ORDERS_PRODUCTS . " set final_price = '" . $new_price . "', products_price = '" . ($new_price - $option_price) . "' where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "'");
		  } else {
			  tep_db_query("update " . TABLE_ORDERS_PRODUCTS . " set " . $field . " = '" . tep_db_prepare_input($HTTP_GET_VARS['new_value']) . "' where orders_products_id = '" . $HTTP_GET_VARS['pID'] . "'");
		  }
	  }
	  //we have to update the orders_total table
	  tep_db_update_totals($HTTP_GET_VARS['order']);
	  
	  //that's it, tell the administrator
	  //ENGLISH
	  echo (($action == 'eliminate') ? 'Product eliminated.' : 'Order updated.' ) . "\n" . 'Refresh the browser to see the changes.';
	  //FRANÇAIS
	  //echo (($action == 'eliminate') ? 'Produit eliminé.' : 'Commande actualisée.' ) . "\n" . 'Rafraichir le navigateur pour voir les changements.';
	  //ESPAÑOL
	  //echo (($action == 'eliminate') ? 'Producto eliminado.' : 'Pedido actualizado.' ) . "\n" . 'Refrescar el navegador para ver los cambios.';
  } elseif ($action == 'update_order_field') {
	  tep_db_query("update " . TABLE_PREFIX . $HTTP_GET_VARS['db_table'] . " set " . $HTTP_GET_VARS['field'] . " = '" . tep_db_prepare_input($HTTP_GET_VARS['new_value']) . "' where orders_id = '" . $HTTP_GET_VARS['oID'] . "'");
	  //that's it, tell the administrator
	  //ENGLISH
	  echo 'Field updated.' . "\n" . 'Refresh the browser to see the changes.';
	  //FRANÇAIS
	  //echo 'Donnée actualisée.' . "\n" . 'Rafraichir le navigateur pour voir les changements.';
	  //ESPAÑOL
	  //echo 'Pedido actualizado.' . "\n" . 'Refrescar el navegador para ver los cambios.';
  } elseif ($action == 'search') {//search products in the db.
	  $products_query = tep_db_query("select distinct p.products_id, pd.products_name, p.products_model from " . TABLE_PRODUCTS_DESCRIPTION . " pd left join " . TABLE_PRODUCTS . " p on (p.products_id = pd.products_id) where (pd.products_name like '%" . tep_db_input($HTTP_GET_VARS['keyword']) . "%' or  p.products_model like '%" . tep_db_input($HTTP_GET_VARS['keyword']) . "%') and  pd.language_id = '" . $languages_id . "' and p.products_status = '1' order  by pd.products_name asc limit 20");
	  if (tep_db_num_rows($products_query)) {
		  while ($products = tep_db_fetch_array($products_query)) {
			  $results[] = '<a href="javascript:selectProduct(\'' .  $products['products_id'] . '\', \'' .  addslashes(tep_output_string_protected($products['products_name'])) . '\');">' .  $products['products_name'] . (($products['products_model'] != '') ? ' (' . $products['products_model'] . ')' : '') . '</a>' . "\n";
		  }
	  } else {
		  $results[] = PRODUCTS_SEARCH_NO_RESULTS;
	  }
	  echo implode('<br>' . "\n", $results);
  } elseif ($action == 'attributes') {
	  //we create an AJAX form
	  $attributes = '<form name="attributes" id="attributes" action="" onsubmit="setAttr(); return false"><input type="hidden" name="products_id" value="' . (int)$HTTP_GET_VARS['prID'] . '">';
	  //this part comes integraly from OSC catalog/product_info.php
	  $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['prID'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
	  $products_attributes = tep_db_fetch_array($products_attributes_query);
	  if ($products_attributes['total'] > 0) {
		  $attributes .= '<table border="0" cellspacing="0" cellpadding="2" class="dataTableRow" width="100%"><tr><td class="dataTableContent" colspan="2">' . TEXT_PRODUCT_OPTIONS . '</td>            </tr>';
		  $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['prID'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' order by popt.products_options_name");
		  while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
			  $products_options_array = array();
	          $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$HTTP_GET_VARS['prID'] . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "'");
	          while ($products_options = tep_db_fetch_array($products_options_query)) {
		          $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
		          if ($products_options['options_values_price'] != '0') {
			          $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
		          }
	          }
	          $attributes .= '<tr><td class="main">' . $products_options_name['products_options_name'] . ':</td><td class="main">' . tep_draw_pull_down_menu('atrid_' . $products_options_name['products_options_id'], $products_options_array, $selected_attribute) . '</td></tr>';
          }
          $attributes .= '<tr><td colspan="2">' . tep_image_submit('button_confirm.gif', IMAGE_CONFIRM) . '</td></tr></table></form>';
      } else {
	      $attributes .= tep_image_submit('button_confirm.gif', IMAGE_CONFIRM) . '</form>';
      }
      echo $attributes;
  } elseif ($action == 'set_attributes') {
	  $attributes = array();
	  $products_id = 0;
	  $products_quantity = 0;
	  foreach($HTTP_POST_VARS as $key => $value) {
		  if ($key == 'products_id') {
			  $products_id = $value;
		  } elseif ($key == 'products_quantity') {
			  $products_quantity = $value;
		  } elseif (stristr($key, 'trid_')) {
			  $attributes[] = array(substr($key, 6), $value);
		  }
	  }
	  $orders_id = $HTTP_GET_VARS['oID'];
	  $product_info_query = tep_db_query("select p.products_model, pd.products_name, p.products_price, p.products_tax_class_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where p.products_id = '" . $products_id . "' and pd.language_id = '" . (int)$languages_id . "'");
	  $product_info = tep_db_fetch_array($product_info_query);
	  if (DISPLAY_PRICE_WITH_TAX == 'true') {
		  $tax_query = tep_db_query("select tax_rate, tax_description from " . TABLE_TAX_RATES . " where tax_rates_id = '" . $product_info['products_tax_class_id'] . "'");
		  $tax_ = tep_db_fetch_array($tax_query);
		  $tax = $tax_['tax_rate'];
		  $tax_desc = $tax_['tax_description'];
	  } else {
		  $tax = 0;
	  }
	  //OJO, hay que insertar primero el product para saber el orders_products_id...
	  $attribute_price_sum = 0;
	  $attribute_update = false;
	  if (sizeof($attributes) > 0) {
		  $attribute_update = true;
		  for ($j=0; $j<sizeof($attributes); $j++) {
			  $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $products_id . "' and options_id = '" . $attributes[$j][0] . "' and options_values_id = '" . $attributes[$j][1] . "'");
			  $attribute_price = tep_db_fetch_array($attribute_price_query);
			  if ($attribute_price['price_prefix'] == '+') {
				  $attribute_price_sum += (float)$attribute_price['options_values_price'];
			  } else {
				  $attribute_price_sum -= (float)$attribute_price['options_values_price'];
			  }
			  $attribute_name_query = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $attributes[$j][0] . "' and language_id = '" . (int)$languages_id . "'");
			  $attribute_name = tep_db_fetch_array($attribute_name_query);
			  $options_name_query = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $attributes[$j][1] . "' and language_id = '" . (int)$languages_id . "'");
			  $options_name = tep_db_fetch_array($options_name_query);
			  
			  tep_db_query("insert into " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " (orders_id, orders_products_id, products_options, products_options_values, options_values_price, price_prefix) values ('" . $orders_id . "', '0', '" . tep_db_input((string)$attribute_name['products_options_name']) . "', '" . tep_db_input((string)$options_name['products_options_values_name']) . "', '" . tep_db_input((float)$attribute_price['options_values_price']) . "', '" . tep_db_input((string)$attribute_price['price_prefix']) . "')");
		  }
	  }
	  $final_price = (float)$product_info['products_price'] + (float)$attribute_price_sum;
	  if (tep_not_null($tax)) {
		  $final_price = $final_price * (float)('1.' . (strlen((string)$tax['tax_rate'] < 6) ? '0' : '') . ((int)($tax['tax_rate'] * 100)));
	  }
	  tep_db_query("insert into " . TABLE_ORDERS_PRODUCTS . " (orders_id, products_id, products_model, products_name, products_price, final_price, products_tax, products_quantity) values ('" . $orders_id . "', '" . $products_id . "', '" . $product_info['products_model'] . "', '" . $product_info['products_name'] . "', '" . $product_info['products_price'] . "', '" . $final_price . "', '" . $tax . "', '" . $products_quantity . "')");
	  $orders_products_id = tep_db_insert_id();
	  if ($attribute_update == true){
		  tep_db_query("update  " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set orders_products_id = '" . $orders_products_id . "' where orders_products_id = '0'");
	  }
	  tep_db_update_totals($orders_id);
	  //that's it, tell the administrator
	  //ENGLISH
	  echo 'Product added.' . "\n" . 'Refresh the browser to see the changes.';
	  //FRANÇAIS
	  //echo 'Article ajouté.' . "\n" . 'Rafraichir le navigateur pour voir les changements.';
	  //ESPAÑOL
	  echo 'Producto insertado.' . "\n" . 'F5 para ver los cambios.';
  } elseif ($action == 'orders_total_update') {
	  if ($HTTP_GET_VARS['column'] == 'value') {
		  $text_query = tep_db_query("select text, value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $HTTP_GET_VARS['oID'] . "' and class = '" . $HTTP_GET_VARS['class'] . "'");
		  $text_ = tep_db_fetch_array($text_query);
		  $text = str_replace(round((float)$text_['value'], 2), round((float)$HTTP_GET_VARS['new_value'] , 2), $text_['text']);
		  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set text = '" . $text . "' where orders_id = '" . $HTTP_GET_VARS['oID'] . "' and class = '" . $HTTP_GET_VARS['class'] . "'");
	  }
	  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set " . $HTTP_GET_VARS['column'] . " = '" . $HTTP_GET_VARS['new_value'] . "' where orders_id = '" . $HTTP_GET_VARS['oID'] . "' and class = '" . $HTTP_GET_VARS['class'] . "'");
	  tep_db_update_totals($HTTP_GET_VARS['oID']);
	  //that's it, tell the administrator
	  //ENGLISH
	  echo 'Total updated.' . "\n" . 'Refresh the browser to see the changes.';
	  //FRANÇAIS
	  //echo 'Total actualisé.' . "\n" . 'Rafraichir le navigateur pour voir les changements.';
	  //ESPAÑOL
	  //echo 'Total actualizado.' . "\n" . 'F5 para ver los cambios.';
  } elseif ($action == 'new_order_total') {
	  $sort_order_query = tep_db_query("select max(sort_order) as maxim from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $HTTP_GET_VARS['oID'] . "' and class != 'ot_total'");
	  $sort_order = tep_db_fetch_array($sort_order_query);
	  $new_sort_order = (int)$sort_order['maxim'] + 1;
	  
	  //get the order's currency
	  $currency_query = tep_db_query("select currency from " . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
	  $currency = tep_db_fetch_array($currency_query);
	  
	  $class_query = tep_db_query("select class from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $HTTP_GET_VARS['oID'] . "' and class like '%ot_extra_%'");
	  $classs = 'ot_extra_' . (tep_db_num_rows($class_query) + 1);
	  
	  tep_db_query("insert into " . TABLE_ORDERS_TOTAL . " (orders_id, title, text, value, class, sort_order) values ('" . $HTTP_GET_VARS['oID'] . "', '" . $HTTP_GET_VARS['title'] . ":', '" . round((float)$HTTP_GET_VARS['value'], 2) . $currency['currency'] . "', '" . round((float)$HTTP_GET_VARS['value'], 4) . "', '" . $classs . "', '" . $new_sort_order . "')");
	  tep_db_update_totals($HTTP_GET_VARS['oID']);
	  //that's it, tell the administrator
	  //ENGLISH
	  echo 'Total updated.' . "\n" . 'Refresh the browser to see the changes.';
	  //FRANÇAIS
	  //echo 'Total actualisé.' . "\n" . 'Rafraichir le navigateur pour voir les changements.';
	  //ESPAÑOL
	  //echo 'Total actualizado.' . "\n" . 'F5 para ver los cambios.';
  }
?>
