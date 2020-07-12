# $Id: oscommerce.sql 156 2006-02-14 17:37:15Z Michael $
#
# osCommerce, Open Source E-Commerce Solutions
# http://www.oscommerce.com
#
# Copyright (c) 2003 osCommerce
#
# Released under the GNU General Public License
#
# NOTE: * Please make any modifications to this file by hand!
#       * DO NOT use a mysqldump created file for new changes!
#       * Please take note of the table structure, and use this
#         structure as a standard for future modifications!
#       * Any tables you add here should be added in admin/backup.php
#         and in catalog/install/includes/functions/database.php
#       * To see the 'diff'erence between MySQL databases, use
#         the mysqldiff perl script located in the extras
#         directory of the 'catalog' module.
#       * Comments should be like these, full line comments.
#         (don't use inline comments)


CREATE TABLE osc_address_book (
   address_book_id int NOT NULL auto_increment,
   customers_id int NOT NULL,
   entry_gender char(1) NOT NULL,
   entry_company varchar(32),
   entry_firstname varchar(32) NOT NULL,
   entry_lastname varchar(32) NOT NULL,
   entry_street_address varchar(64) NOT NULL,
   entry_suburb varchar(32),
   entry_postcode varchar(10) NOT NULL,
   entry_city varchar(32) NOT NULL,
   entry_state varchar(32),
   entry_country_id int DEFAULT '0' NOT NULL,
   entry_zone_id int DEFAULT '0' NOT NULL,
   PRIMARY KEY (address_book_id),
   KEY idx_address_book_customers_id (customers_id)
);

CREATE TABLE osc_address_format (
  address_format_id int NOT NULL auto_increment,
  address_format varchar(128) NOT NULL,
  address_summary varchar(48) NOT NULL,
  PRIMARY KEY (address_format_id)
);

CREATE TABLE osc_banners (
  banners_id int NOT NULL auto_increment,
  banners_title varchar(64) NOT NULL,
  banners_url varchar(255) NOT NULL,
  banners_image varchar(64) NOT NULL,
  banners_group varchar(10) NOT NULL,
  banners_html_text text,
  expires_impressions int(7) DEFAULT '0',
  expires_date datetime DEFAULT NULL,
  date_scheduled datetime DEFAULT NULL,
  date_added datetime NOT NULL,
  date_status_change datetime DEFAULT NULL,
  status int(1) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (banners_id)
);

CREATE TABLE osc_banners_history (
  banners_history_id int NOT NULL auto_increment,
  banners_id int NOT NULL,
  banners_shown int(5) NOT NULL DEFAULT '0',
  banners_clicked int(5) NOT NULL DEFAULT '0',
  banners_history_date datetime NOT NULL,
  PRIMARY KEY  (banners_history_id)
);

CREATE TABLE osc_categories (
   categories_id int NOT NULL auto_increment,
   categories_image varchar(64),
   parent_id int DEFAULT '0' NOT NULL,
   sort_order int(3),
   date_added datetime,
   last_modified datetime,
   PRIMARY KEY (categories_id),
   KEY idx_categories_parent_id (parent_id)
);

CREATE TABLE osc_categories_description (
   categories_id int DEFAULT '0' NOT NULL,
   language_id int DEFAULT '1' NOT NULL,
   categories_name varchar(32) NOT NULL,
   PRIMARY KEY (categories_id, language_id),
   KEY idx_categories_name (categories_name)
);

CREATE TABLE osc_configuration (
  configuration_id int NOT NULL auto_increment,
  configuration_title varchar(64) NOT NULL,
  configuration_key varchar(64) NOT NULL,
  configuration_value varchar(255) NOT NULL,
  configuration_description varchar(255) NOT NULL,
  configuration_group_id int NOT NULL,
  sort_order int(5) NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  use_function varchar(255) NULL,
  set_function varchar(255) NULL,
  PRIMARY KEY (configuration_id)
);

CREATE TABLE osc_configuration_group (
  configuration_group_id int NOT NULL auto_increment,
  configuration_group_title varchar(64) NOT NULL,
  configuration_group_description varchar(255) NOT NULL,
  sort_order int(5) NULL,
  visible int(1) DEFAULT '1' NULL,
  PRIMARY KEY (configuration_group_id)
);

CREATE TABLE osc_counter (
  startdate char(8),
  counter int(12)
);

CREATE TABLE osc_counter_history (
  month char(8),
  counter int(12)
);


CREATE TABLE osc_currencies (
  currencies_id int NOT NULL auto_increment,
  title varchar(32) NOT NULL,
  code char(3) NOT NULL,
  symbol_left varchar(12),
  symbol_right varchar(12),
  decimal_point char(1),
  thousands_point char(1),
  decimal_places char(1),
  value float(13,8),
  last_updated datetime NULL,
  PRIMARY KEY (currencies_id)
);

CREATE TABLE osc_customers (
   customers_id int NOT NULL auto_increment,
   customers_gender char(1) NOT NULL,
   customers_firstname varchar(32) NOT NULL,
   customers_lastname varchar(32) NOT NULL,
   customers_dob datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
   customers_email_address varchar(96) NOT NULL,
   customers_default_address_id int NOT NULL,
   customers_telephone varchar(32) NOT NULL,
   customers_fax varchar(32),
   customers_password varchar(40) NOT NULL,
   customers_newsletter char(1),
   PRIMARY KEY (customers_id)
);

CREATE TABLE osc_customers_basket (
  customers_basket_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  products_id tinytext NOT NULL,
  customers_basket_quantity int(2) NOT NULL,
  final_price decimal(15,4) NOT NULL,
  customers_basket_date_added char(8),
  PRIMARY KEY (customers_basket_id)
);

CREATE TABLE osc_customers_basket_attributes (
  customers_basket_attributes_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  products_id tinytext NOT NULL,
  products_options_id int NOT NULL,
  products_options_value_id int NOT NULL,
  PRIMARY KEY (customers_basket_attributes_id)
);

CREATE TABLE osc_customers_info (
  customers_info_id int NOT NULL,
  customers_info_date_of_last_logon datetime,
  customers_info_number_of_logons int(5),
  customers_info_date_account_created datetime,
  customers_info_date_account_last_modified datetime,
  global_product_notifications int(1) DEFAULT '0',
  PRIMARY KEY (customers_info_id)
);

CREATE TABLE osc_languages (
  languages_id int NOT NULL auto_increment,
  name varchar(32)  NOT NULL,
  code char(2) NOT NULL,
  image varchar(64),
  directory varchar(32),
  sort_order int(3),
  PRIMARY KEY (languages_id),
  KEY IDX_LANGUAGES_NAME (name)
);


CREATE TABLE osc_manufacturers (
  manufacturers_id int NOT NULL auto_increment,
  manufacturers_name varchar(32) NOT NULL,
  manufacturers_image varchar(64),
  date_added datetime NULL,
  last_modified datetime NULL,
  PRIMARY KEY (manufacturers_id),
  KEY IDX_MANUFACTURERS_NAME (manufacturers_name)
);

CREATE TABLE osc_manufacturers_info (
  manufacturers_id int NOT NULL,
  languages_id int NOT NULL,
  manufacturers_url varchar(255) NOT NULL,
  url_clicked int(5) NOT NULL default '0',
  date_last_click datetime NULL,
  PRIMARY KEY (manufacturers_id, languages_id)
);

CREATE TABLE osc_newsletters (
  newsletters_id int NOT NULL auto_increment,
  title varchar(255) NOT NULL,
  content text NOT NULL,
  module varchar(255) NOT NULL,
  date_added datetime NOT NULL,
  date_sent datetime,
  status int(1),
  locked int(1) DEFAULT '0',
  PRIMARY KEY (newsletters_id)
);

CREATE TABLE osc_orders (
  orders_id int NOT NULL auto_increment,
  customers_id int NOT NULL,
  customers_name varchar(64) NOT NULL,
  customers_company varchar(32),
  customers_street_address varchar(64) NOT NULL,
  customers_suburb varchar(32),
  customers_city varchar(32) NOT NULL,
  customers_postcode varchar(10) NOT NULL,
  customers_state varchar(32),
  customers_country varchar(32) NOT NULL,
  customers_telephone varchar(32) NOT NULL,
  customers_email_address varchar(96) NOT NULL,
  customers_address_format_id int(5) NOT NULL,
  delivery_name varchar(64) NOT NULL,
  delivery_company varchar(32),
  delivery_street_address varchar(64) NOT NULL,
  delivery_suburb varchar(32),
  delivery_city varchar(32) NOT NULL,
  delivery_postcode varchar(10) NOT NULL,
  delivery_state varchar(32),
  delivery_country varchar(32) NOT NULL,
  delivery_address_format_id int(5) NOT NULL,
  billing_name varchar(64) NOT NULL,
  billing_company varchar(32),
  billing_street_address varchar(64) NOT NULL,
  billing_suburb varchar(32),
  billing_city varchar(32) NOT NULL,
  billing_postcode varchar(10) NOT NULL,
  billing_state varchar(32),
  billing_country varchar(32) NOT NULL,
  billing_address_format_id int(5) NOT NULL,
  payment_method varchar(32) NOT NULL,
  cc_type varchar(20),
  cc_owner varchar(64),
  cc_number varchar(32),
  cc_expires varchar(4),
  last_modified datetime,
  date_purchased datetime,
  orders_status int(5) NOT NULL,
  orders_date_finished datetime,
  currency char(3),
  currency_value decimal(14,6),
  PRIMARY KEY (orders_id)
);

CREATE TABLE osc_orders_products (
  orders_products_id int NOT NULL auto_increment,
  orders_id int NOT NULL,
  products_id int NOT NULL,
  products_model varchar(12),
  products_name varchar(64) NOT NULL,
  products_price decimal(15,4) NOT NULL,
  final_price decimal(15,4) NOT NULL,
  products_tax decimal(7,4) NOT NULL,
  products_quantity int(2) NOT NULL,
  PRIMARY KEY (orders_products_id)
);

CREATE TABLE osc_orders_status (
   orders_status_id int DEFAULT '0' NOT NULL,
   language_id int DEFAULT '1' NOT NULL,
   orders_status_name varchar(32) NOT NULL,
   PRIMARY KEY (orders_status_id, language_id),
   KEY idx_orders_status_name (orders_status_name)
);

CREATE TABLE osc_orders_status_history (
   orders_status_history_id int NOT NULL auto_increment,
   orders_id int NOT NULL,
   orders_status_id int(5) NOT NULL,
   date_added datetime NOT NULL,
   customer_notified int(1) DEFAULT '0',
   comments text,
   PRIMARY KEY (orders_status_history_id)
);

CREATE TABLE osc_orders_products_attributes (
  orders_products_attributes_id int NOT NULL auto_increment,
  orders_id int NOT NULL,
  orders_products_id int NOT NULL,
  products_options varchar(32) NOT NULL,
  products_options_values varchar(32) NOT NULL,
  options_values_price decimal(15,4) NOT NULL,
  price_prefix char(1) NOT NULL,
  PRIMARY KEY (orders_products_attributes_id)
);

CREATE TABLE osc_orders_products_download (
  orders_products_download_id int NOT NULL auto_increment,
  orders_id int NOT NULL default '0',
  orders_products_id int NOT NULL default '0',
  orders_products_filename varchar(255) NOT NULL default '',
  download_maxdays int(2) NOT NULL default '0',
  download_count int(2) NOT NULL default '0',
  PRIMARY KEY  (orders_products_download_id)
);

CREATE TABLE osc_orders_total (
  orders_total_id int unsigned NOT NULL auto_increment,
  orders_id int NOT NULL,
  title varchar(255) NOT NULL,
  text varchar(255) NOT NULL,
  value decimal(15,4) NOT NULL,
  class varchar(32) NOT NULL,
  sort_order int NOT NULL,
  PRIMARY KEY (orders_total_id),
  KEY idx_orders_total_orders_id (orders_id)
);

CREATE TABLE osc_products (
  products_id int NOT NULL auto_increment,
  products_quantity int(4) NOT NULL,
  products_model varchar(12),
  products_image varchar(64),
  products_price decimal(15,4) NOT NULL,
  products_date_added datetime NOT NULL,
  products_last_modified datetime,
  products_date_available datetime,
  products_weight decimal(5,2) NOT NULL,
  products_status tinyint(1) NOT NULL,
  products_tax_class_id int NOT NULL,
  manufacturers_id int NULL,
  products_ordered int NOT NULL default '0',
  PRIMARY KEY (products_id),
  KEY idx_products_date_added (products_date_added)
);

CREATE TABLE osc_products_attributes (
  products_attributes_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  options_id int NOT NULL,
  options_values_id int NOT NULL,
  options_values_price decimal(15,4) NOT NULL,
  price_prefix char(1) NOT NULL,
  PRIMARY KEY (products_attributes_id)
);

CREATE TABLE osc_products_attributes_download (
  products_attributes_id int NOT NULL,
  products_attributes_filename varchar(255) NOT NULL default '',
  products_attributes_maxdays int(2) default '0',
  products_attributes_maxcount int(2) default '0',
  PRIMARY KEY  (products_attributes_id)
);

CREATE TABLE osc_products_description (
  products_id int NOT NULL auto_increment,
  language_id int NOT NULL default '1',
  products_name varchar(64) NOT NULL default '',
  products_description text,
  products_url varchar(255) default NULL,
  products_viewed int(5) default '0',
  PRIMARY KEY  (products_id,language_id),
  KEY products_name (products_name)
);

CREATE TABLE osc_products_notifications (
  products_id int NOT NULL,
  customers_id int NOT NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (products_id, customers_id)
);

CREATE TABLE osc_products_options (
  products_options_id int NOT NULL default '0',
  language_id int NOT NULL default '1',
  products_options_name varchar(32) NOT NULL default '',
  PRIMARY KEY  (products_options_id,language_id)
);

CREATE TABLE osc_products_options_values (
  products_options_values_id int NOT NULL default '0',
  language_id int NOT NULL default '1',
  products_options_values_name varchar(64) NOT NULL default '',
  PRIMARY KEY  (products_options_values_id,language_id)
);

CREATE TABLE osc_products_options_values_to_products_options (
  products_options_values_to_products_options_id int NOT NULL auto_increment,
  products_options_id int NOT NULL,
  products_options_values_id int NOT NULL,
  PRIMARY KEY (products_options_values_to_products_options_id)
);

CREATE TABLE osc_products_to_categories (
  products_id int NOT NULL,
  categories_id int NOT NULL,
  PRIMARY KEY (products_id,categories_id)
);

CREATE TABLE osc_reviews (
  reviews_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  customers_id int,
  customers_name varchar(64) NOT NULL,
  reviews_rating int(1),
  date_added datetime,
  last_modified datetime,
  reviews_read int(5) NOT NULL default '0',
  PRIMARY KEY (reviews_id)
);

CREATE TABLE osc_reviews_description (
  reviews_id int NOT NULL,
  languages_id int NOT NULL,
  reviews_text text NOT NULL,
  PRIMARY KEY (reviews_id, languages_id)
);

CREATE TABLE osc_sessions (
  sesskey varchar(32) NOT NULL,
  expiry int(11) unsigned NOT NULL,
  value text NOT NULL,
  PRIMARY KEY (sesskey)
);

CREATE TABLE osc_specials (
  specials_id int NOT NULL auto_increment,
  products_id int NOT NULL,
  specials_new_products_price decimal(15,4) NOT NULL,
  specials_date_added datetime,
  specials_last_modified datetime,
  expires_date datetime,
  date_status_change datetime,
  status int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (specials_id)
);

CREATE TABLE osc_tax_class (
  tax_class_id int NOT NULL auto_increment,
  tax_class_title varchar(32) NOT NULL,
  tax_class_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (tax_class_id)
);

CREATE TABLE osc_tax_rates (
  tax_rates_id int NOT NULL auto_increment,
  tax_zone_id int NOT NULL,
  tax_class_id int NOT NULL,
  tax_priority int(5) DEFAULT 1,
  tax_rate decimal(7,4) NOT NULL,
  tax_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (tax_rates_id)
);

CREATE TABLE osc_geo_zones (
  geo_zone_id int NOT NULL auto_increment,
  geo_zone_name varchar(32) NOT NULL,
  geo_zone_description varchar(255) NOT NULL,
  last_modified datetime NULL,
  date_added datetime NOT NULL,
  PRIMARY KEY (geo_zone_id)
);

CREATE TABLE osc_whos_online (
  customer_id int,
  full_name varchar(64) NOT NULL,
  session_id varchar(128) NOT NULL,
  ip_address varchar(15) NOT NULL,
  time_entry varchar(14) NOT NULL,
  time_last_click varchar(14) NOT NULL,
  last_page_url varchar(64) NOT NULL
);

CREATE TABLE osc_zones (
  zone_id int NOT NULL auto_increment,
  zone_country_id int NOT NULL,
  zone_code varchar(32) NOT NULL,
  zone_name varchar(32) NOT NULL,
  PRIMARY KEY (zone_id)
);

CREATE TABLE osc_zones_to_geo_zones (
   association_id int NOT NULL auto_increment,
   zone_country_id int NOT NULL,
   zone_id int NULL,
   geo_zone_id int NULL,
   last_modified datetime NULL,
   date_added datetime NOT NULL,
   PRIMARY KEY (association_id)
);

# data

INSERT INTO osc_address_book VALUES ( '1', '1', 'm', 'ACME Inc.', 'John', 'Doe', '1 Way Street', '', '12345', 'NeverNever', '', '223', '12');

# 1 - Default, 2 - USA, 3 - Spain, 4 - Singapore, 5 - Germany
INSERT INTO osc_address_format VALUES (1, '$firstname $lastname$cr$streets$cr$city, $postcode$cr$statecomma$country','$city / $country');
INSERT INTO osc_address_format VALUES (2, '$firstname $lastname$cr$streets$cr$city, $state    $postcode$cr$country','$city, $state / $country');
INSERT INTO osc_address_format VALUES (3, '$firstname $lastname$cr$streets$cr$city$cr$postcode - $statecomma$country','$state / $country');
INSERT INTO osc_address_format VALUES (4, '$firstname $lastname$cr$streets$cr$city ($postcode)$cr$country', '$postcode / $country');
INSERT INTO osc_address_format VALUES (5, '$firstname $lastname$cr$streets$cr$postcode $city$cr$country','$city / $country');

INSERT INTO osc_banners VALUES (1, 'osCommerce', 'http://www.oscommerce.com', 'banners/oscommerce.gif', '468x50', '', 0, null, null, now(), null, 1);

#Current XosC Project Version

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Project Version', 'PROJECT_VERSION', '.73', 'XosC version', '14', '1', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Store Name', 'STORE_NAME', 'osCommerce', 'The name of my store', '1', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Store Owner', 'STORE_OWNER', 'Harald Ponce de Leon', 'The name of my store owner', '1', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('E-Mail Address', 'STORE_OWNER_EMAIL_ADDRESS', 'root@localhost', 'The e-mail address of my store owner', '1', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('E-Mail From', 'EMAIL_FROM', 'osCommerce <root@localhost>', 'The e-mail address used in (sent) e-mails', '1', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Country', 'STORE_COUNTRY', '223', 'The country my store is located in <br><br><b>Note: Please remember to update the store zone.</b>', '1', '5', 'tep_get_country_name', 'tep_cfg_pull_down_country_list(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Zone', 'STORE_ZONE', '18', 'The zone my store is located in', '1', '6', 'tep_cfg_get_zone_name', 'tep_cfg_pull_down_zone_list(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Expected Sort Order', 'EXPECTED_PRODUCTS_SORT', 'desc', 'This is the sort order used in the expected products box.', '1', '9', 'tep_cfg_select_option(array(\'asc\', \'desc\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Expected Sort Field', 'EXPECTED_PRODUCTS_FIELD', 'date_expected', 'The column to sort by in the expected products box.', '1', '10', 'tep_cfg_select_option(array(\'products_name\', \'date_expected\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Switch To Default Language Currency', 'USE_DEFAULT_LANGUAGE_CURRENCY', 'false', 'Automatically switch to the language\'s currency when it is changed', '1', '11', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Send Extra Order Emails To', 'SEND_EXTRA_ORDER_EMAILS_TO', '', 'Send extra order emails to the following email addresses, in this format: Name 1 &lt;email@address1&gt;, Name 2 &lt;email@address2&gt;', '1', '12', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Use Search-Engine Safe URLs (still in development)', 'SEARCH_ENGINE_FRIENDLY_URLS', 'false', 'Use search-engine safe urls for all site links', '1', '13', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display Cart After Adding Product', 'DISPLAY_CART', 'true', 'Display the shopping cart after adding a product (or return back to their origin)', '1', '14', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Allow Guest To Tell A Friend', 'ALLOW_GUEST_TO_TELL_A_FRIEND', 'false', 'Allow guests to tell a friend about a product', '1', '15', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Default Search Operator', 'ADVANCED_SEARCH_DEFAULT_OPERATOR', 'and', 'Default search operators', '1', '17', 'tep_cfg_select_option(array(\'and\', \'or\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Store Address and Phone', 'STORE_NAME_ADDRESS', 'Store Name\nAddress\nCountry\nPhone', 'This is the Store Name, Address and Phone used on printable documents and displayed online', '1', '18', 'tep_cfg_textarea(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Show Category Counts', 'SHOW_COUNTS', 'true', 'Count recursively how many products are in each category', '1', '19', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Tax Decimal Places', 'TAX_DECIMAL_PLACES', '0', 'Pad the tax value this amount of decimal places', '1', '20', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('First Name', 'ENTRY_FIRST_NAME_MIN_LENGTH', '2', 'Minimum length of first name', '2', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Last Name', 'ENTRY_LAST_NAME_MIN_LENGTH', '2', 'Minimum length of last name', '2', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Date of Birth', 'ENTRY_DOB_MIN_LENGTH', '10', 'Minimum length of date of birth', '2', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('E-Mail Address', 'ENTRY_EMAIL_ADDRESS_MIN_LENGTH', '6', 'Minimum length of e-mail address', '2', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Street Address', 'ENTRY_STREET_ADDRESS_MIN_LENGTH', '5', 'Minimum length of street address', '2', '5', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Company', 'ENTRY_COMPANY_MIN_LENGTH', '2', 'Minimum length of company name', '2', '6', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Post Code', 'ENTRY_POSTCODE_MIN_LENGTH', '4', 'Minimum length of post code', '2', '7', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('City', 'ENTRY_CITY_MIN_LENGTH', '3', 'Minimum length of city', '2', '8', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('State', 'ENTRY_STATE_MIN_LENGTH', '2', 'Minimum length of state', '2', '9', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Telephone Number', 'ENTRY_TELEPHONE_MIN_LENGTH', '3', 'Minimum length of telephone number', '2', '10', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Password', 'ENTRY_PASSWORD_MIN_LENGTH', '5', 'Minimum length of password', '2', '11', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Credit Card Owner Name', 'CC_OWNER_MIN_LENGTH', '3', 'Minimum length of credit card owner name', '2', '12', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Credit Card Number', 'CC_NUMBER_MIN_LENGTH', '10', 'Minimum length of credit card number', '2', '13', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Review Text', 'REVIEW_TEXT_MIN_LENGTH', '50', 'Minimum length of review text', '2', '14', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Best Sellers', 'MIN_DISPLAY_BESTSELLERS', '1', 'Minimum number of best sellers to display', '2', '15', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Also Purchased', 'MIN_DISPLAY_ALSO_PURCHASED', '1', 'Minimum number of products to display in the \'This Customer Also Purchased\' box', '2', '16', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Address Book Entries', 'MAX_ADDRESS_BOOK_ENTRIES', '5', 'Maximum address book entries a customer is allowed to have', '3', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Search Results', 'MAX_DISPLAY_SEARCH_RESULTS', '20', 'Amount of products to list', '3', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Page Links', 'MAX_DISPLAY_PAGE_LINKS', '5', 'Number of \'number\' links use for page-sets', '3', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Special Products', 'MAX_DISPLAY_SPECIAL_PRODUCTS', '9', 'Maximum number of products on special to display', '3', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('New Products Module', 'MAX_DISPLAY_NEW_PRODUCTS', '9', 'Maximum number of new products to display in a category', '3', '5', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Products Expected', 'MAX_DISPLAY_UPCOMING_PRODUCTS', '10', 'Maximum number of products expected to display', '3', '6', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Manufacturers List', 'MAX_DISPLAY_MANUFACTURERS_IN_A_LIST', '0', 'Used in manufacturers box; when the number of manufacturers exceeds this number, a drop-down list will be displayed instead of the default list', '3', '7', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Manufacturers Select Size', 'MAX_MANUFACTURERS_LIST', '1', 'Used in manufacturers box; when this value is \'1\' the classic drop-down list will be used for the manufacturers box. Otherwise, a list-box with the specified number of rows will be displayed.', '3', '7', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Length of Manufacturers Name', 'MAX_DISPLAY_MANUFACTURER_NAME_LEN', '15', 'Used in manufacturers box; maximum length of manufacturers name to display', '3', '8', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('New Reviews', 'MAX_DISPLAY_NEW_REVIEWS', '6', 'Maximum number of new reviews to display', '3', '9', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Selection of Random Reviews', 'MAX_RANDOM_SELECT_REVIEWS', '10', 'How many records to select from to choose one random product review', '3', '10', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Selection of Random New Products', 'MAX_RANDOM_SELECT_NEW', '10', 'How many records to select from to choose one random new product to display', '3', '11', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Selection of Products on Special', 'MAX_RANDOM_SELECT_SPECIALS', '10', 'How many records to select from to choose one random product special to display', '3', '12', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Categories To List Per Row', 'MAX_DISPLAY_CATEGORIES_PER_ROW', '3', 'How many categories to list per row', '3', '13', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('New Products Listing', 'MAX_DISPLAY_PRODUCTS_NEW', '10', 'Maximum number of new products to display in new products page', '3', '14', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Best Sellers', 'MAX_DISPLAY_BESTSELLERS', '10', 'Maximum number of best sellers to display', '3', '15', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Also Purchased', 'MAX_DISPLAY_ALSO_PURCHASED', '6', 'Maximum number of products to display in the \'This Customer Also Purchased\' box', '3', '16', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Customer Order History Box', 'MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX', '6', 'Maximum number of products to display in the customer order history box', '3', '17', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Order History', 'MAX_DISPLAY_ORDER_HISTORY', '10', 'Maximum number of orders to display in the order history page', '3', '18', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Small Image Width', 'SMALL_IMAGE_WIDTH', '100', 'The pixel width of small images', '4', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Small Image Height', 'SMALL_IMAGE_HEIGHT', '80', 'The pixel height of small images', '4', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Heading Image Width', 'HEADING_IMAGE_WIDTH', '57', 'The pixel width of heading images', '4', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Heading Image Height', 'HEADING_IMAGE_HEIGHT', '40', 'The pixel height of heading images', '4', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Subcategory Image Width', 'SUBCATEGORY_IMAGE_WIDTH', '100', 'The pixel width of subcategory images', '4', '5', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Subcategory Image Height', 'SUBCATEGORY_IMAGE_HEIGHT', '57', 'The pixel height of subcategory images', '4', '6', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Calculate Image Size', 'CONFIG_CALCULATE_IMAGE_SIZE', 'true', 'Calculate the size of images?', '4', '7', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Image Required', 'IMAGE_REQUIRED', 'true', 'Enable to display broken images. Good for development.', '4', '8', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Gender', 'ACCOUNT_GENDER', 'true', 'Display gender in the customers account', '5', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Date of Birth', 'ACCOUNT_DOB', 'true', 'Display date of birth in the customers account', '5', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Company', 'ACCOUNT_COMPANY', 'true', 'Display company in the customers account', '5', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Suburb', 'ACCOUNT_SUBURB', 'true', 'Display suburb in the customers account', '5', '4', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('State', 'ACCOUNT_STATE', 'true', 'Display state in the customers account', '5', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_PAYMENT_INSTALLED', 'cc.php;cod.php', 'List of payment module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: cc.php;cod.php;paypal.php)', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_ORDER_TOTAL_INSTALLED', 'ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php', 'List of order_total module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_SHIPPING_INSTALLED', 'flat.php', 'List of shipping module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ups.php;flat.php;item.php)', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Cash On Delivery Module', 'MODULE_PAYMENT_COD_STATUS', 'True', 'Do you want to accept Cash On Delevery payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Payment Zone', 'MODULE_PAYMENT_COD_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort order of display.', 'MODULE_PAYMENT_COD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('Set Order Status', 'MODULE_PAYMENT_COD_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Credit Card Module', 'MODULE_PAYMENT_CC_STATUS', 'True', 'Do you want to accept credit card payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Split Credit Card E-Mail Address', 'MODULE_PAYMENT_CC_EMAIL', '', 'If an e-mail address is entered, the middle digits of the credit card number will be sent to the e-mail address (the outside digits are stored in the database with the middle digits censored)', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort order of display.', 'MODULE_PAYMENT_CC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Payment Zone', 'MODULE_PAYMENT_CC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('Set Order Status', 'MODULE_PAYMENT_CC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Flat Shipping', 'MODULE_SHIPPING_FLAT_STATUS', 'True', 'Do you want to offer flat rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Shipping Cost', 'MODULE_SHIPPING_FLAT_COST', '5.00', 'The shipping cost for all orders using this shipping method.', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Tax Class', 'MODULE_SHIPPING_FLAT_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Shipping Zone', 'MODULE_SHIPPING_FLAT_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_SHIPPING_FLAT_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Currency', 'DEFAULT_CURRENCY', 'USD', 'Default Currency', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Language', 'DEFAULT_LANGUAGE', 'en', 'Default Language', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Order Status For New Orders', 'DEFAULT_ORDERS_STATUS_ID', '1', 'When a new order is created, this order status will be assigned to it.', '6', '0', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true', 'Do you want to display the order shipping cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Allow Free Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false', 'Do you want to allow free shipping?', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) VALUES ('Free Shipping For Orders Over', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '50', 'Provide free shipping for orders over the set amount.', '6', '4', 'currencies->format', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Provide Free Shipping For Orders Made', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national', 'Provide free shipping for orders sent to the set destination.', '6', '5', 'tep_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display Sub-Total', 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true', 'Do you want to display the order sub-total cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '1', 'Sort order of display.', '6', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display Tax', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true', 'Do you want to display the order tax value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '3', 'Sort order of display.', '6', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display Total', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', 'Do you want to display the total order value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '4', 'Sort order of display.', '6', '2', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Country of Origin', 'SHIPPING_ORIGIN_COUNTRY', '223', 'Select the country of origin to be used in shipping quotes.', '7', '1', 'tep_get_country_name', 'tep_cfg_pull_down_country_list(', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Postal Code', 'SHIPPING_ORIGIN_ZIP', 'NONE', 'Enter the Postal Code (ZIP) of the Store to be used in shipping quotes.', '7', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Enter the Maximum Package Weight you will ship', 'SHIPPING_MAX_WEIGHT', '50', 'Carriers have a max weight limit for a single package. This is a common one for all.', '7', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Package Tare weight.', 'SHIPPING_BOX_WEIGHT', '3', 'What is the weight of typical packaging of small to medium packages?', '7', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Larger packages - percentage increase.', 'SHIPPING_BOX_PADDING', '10', 'For 10% enter 10', '7', '5', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Image', 'PRODUCT_LIST_IMAGE', '1', 'Do you want to display the Product Image?', '8', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Manufaturer Name','PRODUCT_LIST_MANUFACTURER', '0', 'Do you want to display the Product Manufacturer Name?', '8', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Model', 'PRODUCT_LIST_MODEL', '0', 'Do you want to display the Product Model?', '8', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Name', 'PRODUCT_LIST_NAME', '2', 'Do you want to display the Product Name?', '8', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Price', 'PRODUCT_LIST_PRICE', '3', 'Do you want to display the Product Price', '8', '5', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Quantity', 'PRODUCT_LIST_QUANTITY', '0', 'Do you want to display the Product Quantity?', '8', '6', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Product Weight', 'PRODUCT_LIST_WEIGHT', '0', 'Do you want to display the Product Weight?', '8', '7', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Buy Now column', 'PRODUCT_LIST_BUY_NOW', '4', 'Do you want to display the Buy Now column?', '8', '8', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Display Category/Manufacturer Filter (0=disable; 1=enable)', 'PRODUCT_LIST_FILTER', '1', 'Do you want to display the Category/Manufacturer Filter?', '8', '9', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Location of Prev/Next Navigation Bar (1-top, 2-bottom, 3-both)', 'PREV_NEXT_BAR_LOCATION', '2', 'Sets the location of the Prev/Next Navigation Bar (1-top, 2-bottom, 3-both)', '8', '10', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Check stock level', 'STOCK_CHECK', 'true', 'Check to see if sufficent stock is available', '9', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Subtract stock', 'STOCK_LIMITED', 'true', 'Subtract product in stock by product orders', '9', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Allow Checkout', 'STOCK_ALLOW_CHECKOUT', 'true', 'Allow customer to checkout even if there is insufficient stock', '9', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Mark product out of stock', 'STOCK_MARK_PRODUCT_OUT_OF_STOCK', '***', 'Display something on screen so customer can see which product has insufficient stock', '9', '4', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Stock Re-order level', 'STOCK_REORDER_LEVEL', '5', 'Define when stock needs to be re-ordered', '9', '5', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Store Page Parse Time', 'STORE_PAGE_PARSE_TIME', 'false', 'Store the time it takes to parse a page', '10', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Log Destination', 'STORE_PAGE_PARSE_TIME_LOG', '/var/log/xosc_store.log', 'Directory and filename of the page parse time log', '10', '2', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Log Date Format', 'STORE_PARSE_DATE_TIME_FORMAT', '%d/%m/%Y %H:%M:%S', 'The date format', '10', '3', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Display The Page Parse Time', 'DISPLAY_PAGE_PARSE_TIME', 'true', 'Display the page parse time (store page parse time must be enabled)', '10', '4', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Store Database Queries', 'STORE_DB_TRANSACTIONS', 'false', 'Store the database queries in the page parse time log (PHP4 only)', '10', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Use Cache', 'USE_CACHE', 'false', 'Use caching features', '11', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Cache Directory', 'DIR_FS_CACHE', '/tmp/', 'The directory where the cached files are saved', '11', '2', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('E-Mail Transport Method', 'EMAIL_TRANSPORT', 'sendmail', 'Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP. Servers running on Windows and MacOS should change this setting to SMTP.', '12', '1', 'tep_cfg_select_option(array(\'sendmail\', \'smtp\'),', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('E-Mail Linefeeds', 'EMAIL_LINEFEED', 'LF', 'Defines the character sequence used to separate mail headers.', '12', '2', 'tep_cfg_select_option(array(\'LF\', \'CRLF\'),', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Use MIME HTML When Sending Emails', 'EMAIL_USE_HTML', 'true', 'Send e-mails in HTML format', '12', '3', 'tep_cfg_select_option(array(\'true\', \'false\'),', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Verify E-Mail Addresses Through DNS', 'ENTRY_EMAIL_ADDRESS_CHECK', 'false', 'Verify e-mail address through a DNS server', '12', '4', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Send E-Mails', 'SEND_EMAILS', 'true', 'Send out e-mails', '12', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable download', 'DOWNLOAD_ENABLED', 'false', 'Enable the products download functions.', '13', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Download by redirect', 'DOWNLOAD_BY_REDIRECT', 'false', 'Use browser redirection for download. Disable on non-Unix systems.', '13', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Expiry delay (days)' ,'DOWNLOAD_MAX_DAYS', '7', 'Set number of days before the download link expires. 0 means no limit.', '13', '3', '', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Maximum number of downloads' ,'DOWNLOAD_MAX_COUNT', '5', 'Set the maximum number of downloads. 0 means no download authorized.', '13', '4', '', now());

# INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable GZip Compression', 'GZIP_COMPRESSION', 'false', 'Enable HTTP GZip compression.', '14', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
# INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Compression Level', 'GZIP_LEVEL', '5', 'Use this compression level 0-9 (0 = minimum, 9 = maximum).', '14', '2', now());

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Session Directory', 'SESSION_WRITE_DIRECTORY', '/tmp', 'If sessions are file based, store them in this directory.', '15', '1', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Force Cookie Use', 'SESSION_FORCE_COOKIE_USE', 'False', 'Force the use of sessions when cookies are only enabled.', '15', '2', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Check SSL Session ID', 'SESSION_CHECK_SSL_SESSION_ID', 'False', 'Validate the SSL_SESSION_ID on every secure HTTPS page request.', '15', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Check User Agent', 'SESSION_CHECK_USER_AGENT', 'False', 'Validate the clients browser user agent on every page request.', '15', '4', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Check IP Address', 'SESSION_CHECK_IP_ADDRESS', 'False', 'Validate the clients IP address on every page request.', '15', '5', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Prevent Spider Sessions', 'SESSION_BLOCK_SPIDERS', 'False', 'Prevent known spiders from starting a session.', '15', '6', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Recreate Session', 'SESSION_RECREATE', 'False', 'Recreate the session to generate a new session ID when the customer logs on or creates an account (PHP >=4.1 needed).', '15', '7', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());

INSERT INTO osc_configuration_group VALUES ('1', 'My Store', 'General information about my store', '1', '1');
INSERT INTO osc_configuration_group VALUES ('2', 'Minimum Values', 'The minimum values for functions / data', '2', '1');
INSERT INTO osc_configuration_group VALUES ('3', 'Maximum Values', 'The maximum values for functions / data', '3', '1');
INSERT INTO osc_configuration_group VALUES ('4', 'Images', 'Image parameters', '4', '1');
INSERT INTO osc_configuration_group VALUES ('5', 'Customer Details', 'Customer account configuration', '5', '1');
INSERT INTO osc_configuration_group VALUES ('6', 'Module Options', 'Hidden from configuration', '6', '0');
INSERT INTO osc_configuration_group VALUES ('7', 'Shipping/Packaging', 'Shipping options available at my store', '7', '1');
INSERT INTO osc_configuration_group VALUES ('8', 'Product Listing', 'Product Listing configuration options', '8', '1');
INSERT INTO osc_configuration_group VALUES ('9', 'Stock', 'Stock configuration options', '9', '1');
INSERT INTO osc_configuration_group VALUES ('10', 'Logging', 'Logging configuration options', '10', '1');
INSERT INTO osc_configuration_group VALUES ('11', 'Cache', 'Caching configuration options', '11', '1');
INSERT INTO osc_configuration_group VALUES ('12', 'E-Mail Options', 'General setting for E-Mail transport and HTML E-Mails', '12', '1');
INSERT INTO osc_configuration_group VALUES ('13', 'Download', 'Downloadable products options', '13', '1');
# INSERT INTO osc_configuration_group VALUES ('14', 'GZip Compression', 'GZip compression options', '14', '1');
# INSERT INTO osc_configuration_group VALUES ('15', 'Sessions', 'Session options', '15', '1');


INSERT INTO osc_currencies VALUES (1,'US Dollar','USD','$','','.',',','2','1.0000', now());
INSERT INTO osc_currencies VALUES (2,'Euro','EUR','','EUR','.',',','2','1.1036', now());

INSERT INTO osc_customers VALUES ( '1', 'm', 'John', 'doe', '2001-01-01 00:00:00', 'root@localhost', '1', '12345', '', 'd95e8fa7f20a009372eb3477473fcd34:1c', '0');

INSERT INTO osc_customers_info VALUES('1', '', '0', now(), '', '0');

INSERT INTO osc_languages VALUES (1,'English','en','icon.gif','english',1);

INSERT INTO osc_manufacturers VALUES (1,'Matrox','manufacturer_matrox.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (2,'Microsoft','manufacturer_microsoft.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (3,'Warner','manufacturer_warner.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (4,'Fox','manufacturer_fox.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (5,'Logitech','manufacturer_logitech.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (6,'Canon','manufacturer_canon.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (7,'Sierra','manufacturer_sierra.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (8,'GT Interactive','manufacturer_gt_interactive.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (9,'Hewlett Packard','manufacturer_hewlett_packard.gif', now(), null);

INSERT INTO osc_manufacturers_info VALUES (1, 1, 'http://www.matrox.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (2, 1, 'http://www.microsoft.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (3, 1, 'http://www.warner.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (4, 1, 'http://www.fox.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (5, 1, 'http://www.logitech.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (6, 1, 'http://www.canon.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (7, 1, 'http://www.sierra.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (8, 1, 'http://www.infogrames.com', 0, null);
INSERT INTO osc_manufacturers_info  VALUES (9, 1, 'http://www.hewlettpackard.com', 0, null);

INSERT INTO osc_orders_status VALUES ( '1', '1', 'For Review');
INSERT INTO osc_orders_status VALUES ( '2', '1', 'Ordered');
INSERT INTO osc_orders_status VALUES ( '3', '1', 'Processed');
INSERT INTO osc_orders_status VALUES ( '4', '1', 'Shipped');

INSERT INTO osc_tax_class VALUES (1, 'Taxable Goods', 'The following types of products are included non-food, services, etc', now(), now());

# USA/Florida
INSERT INTO osc_tax_rates VALUES (1, 1, 1, 1, 7.0, 'FL TAX 7.0%', now(), now());
INSERT INTO osc_geo_zones (geo_zone_id,geo_zone_name,geo_zone_description,date_added) VALUES (1,"Florida","Florida local sales tax zone",now());
INSERT INTO osc_zones_to_geo_zones (association_id,zone_country_id,zone_id,geo_zone_id,date_added) VALUES (1,223,18,1,now());

# USA
INSERT INTO osc_zones VALUES (1,223,'AL','Alabama');
INSERT INTO osc_zones VALUES (2,223,'AK','Alaska');
INSERT INTO osc_zones VALUES (3,223,'AS','American Samoa');
INSERT INTO osc_zones VALUES (4,223,'AZ','Arizona');
INSERT INTO osc_zones VALUES (5,223,'AR','Arkansas');
INSERT INTO osc_zones VALUES (6,223,'AF','Armed Forces Africa');
INSERT INTO osc_zones VALUES (7,223,'AA','Armed Forces Americas');
INSERT INTO osc_zones VALUES (8,223,'AC','Armed Forces Canada');
INSERT INTO osc_zones VALUES (9,223,'AE','Armed Forces Europe');
INSERT INTO osc_zones VALUES (10,223,'AM','Armed Forces Middle East');
INSERT INTO osc_zones VALUES (11,223,'AP','Armed Forces Pacific');
INSERT INTO osc_zones VALUES (12,223,'CA','California');
INSERT INTO osc_zones VALUES (13,223,'CO','Colorado');
INSERT INTO osc_zones VALUES (14,223,'CT','Connecticut');
INSERT INTO osc_zones VALUES (15,223,'DE','Delaware');
INSERT INTO osc_zones VALUES (16,223,'DC','District of Columbia');
INSERT INTO osc_zones VALUES (17,223,'FM','Federated States Of Micronesia');
INSERT INTO osc_zones VALUES (18,223,'FL','Florida');
INSERT INTO osc_zones VALUES (19,223,'GA','Georgia');
INSERT INTO osc_zones VALUES (20,223,'GU','Guam');
INSERT INTO osc_zones VALUES (21,223,'HI','Hawaii');
INSERT INTO osc_zones VALUES (22,223,'ID','Idaho');
INSERT INTO osc_zones VALUES (23,223,'IL','Illinois');
INSERT INTO osc_zones VALUES (24,223,'IN','Indiana');
INSERT INTO osc_zones VALUES (25,223,'IA','Iowa');
INSERT INTO osc_zones VALUES (26,223,'KS','Kansas');
INSERT INTO osc_zones VALUES (27,223,'KY','Kentucky');
INSERT INTO osc_zones VALUES (28,223,'LA','Louisiana');
INSERT INTO osc_zones VALUES (29,223,'ME','Maine');
INSERT INTO osc_zones VALUES (30,223,'MH','Marshall Islands');
INSERT INTO osc_zones VALUES (31,223,'MD','Maryland');
INSERT INTO osc_zones VALUES (32,223,'MA','Massachusetts');
INSERT INTO osc_zones VALUES (33,223,'MI','Michigan');
INSERT INTO osc_zones VALUES (34,223,'MN','Minnesota');
INSERT INTO osc_zones VALUES (35,223,'MS','Mississippi');
INSERT INTO osc_zones VALUES (36,223,'MO','Missouri');
INSERT INTO osc_zones VALUES (37,223,'MT','Montana');
INSERT INTO osc_zones VALUES (38,223,'NE','Nebraska');
INSERT INTO osc_zones VALUES (39,223,'NV','Nevada');
INSERT INTO osc_zones VALUES (40,223,'NH','New Hampshire');
INSERT INTO osc_zones VALUES (41,223,'NJ','New Jersey');
INSERT INTO osc_zones VALUES (42,223,'NM','New Mexico');
INSERT INTO osc_zones VALUES (43,223,'NY','New York');
INSERT INTO osc_zones VALUES (44,223,'NC','North Carolina');
INSERT INTO osc_zones VALUES (45,223,'ND','North Dakota');
INSERT INTO osc_zones VALUES (46,223,'MP','Northern Mariana Islands');
INSERT INTO osc_zones VALUES (47,223,'OH','Ohio');
INSERT INTO osc_zones VALUES (48,223,'OK','Oklahoma');
INSERT INTO osc_zones VALUES (49,223,'OR','Oregon');
INSERT INTO osc_zones VALUES (50,223,'PW','Palau');
INSERT INTO osc_zones VALUES (51,223,'PA','Pennsylvania');
INSERT INTO osc_zones VALUES (52,223,'PR','Puerto Rico');
INSERT INTO osc_zones VALUES (53,223,'RI','Rhode Island');
INSERT INTO osc_zones VALUES (54,223,'SC','South Carolina');
INSERT INTO osc_zones VALUES (55,223,'SD','South Dakota');
INSERT INTO osc_zones VALUES (56,223,'TN','Tennessee');
INSERT INTO osc_zones VALUES (57,223,'TX','Texas');
INSERT INTO osc_zones VALUES (58,223,'UT','Utah');
INSERT INTO osc_zones VALUES (59,223,'VT','Vermont');
INSERT INTO osc_zones VALUES (60,223,'VI','Virgin Islands');
INSERT INTO osc_zones VALUES (61,223,'VA','Virginia');
INSERT INTO osc_zones VALUES (62,223,'WA','Washington');
INSERT INTO osc_zones VALUES (63,223,'WV','West Virginia');
INSERT INTO osc_zones VALUES (64,223,'WI','Wisconsin');
INSERT INTO osc_zones VALUES (65,223,'WY','Wyoming');

# Canada
INSERT INTO osc_zones VALUES (66,38,'AB','Alberta');
INSERT INTO osc_zones VALUES (67,38,'BC','British Columbia');
INSERT INTO osc_zones VALUES (68,38,'MB','Manitoba');
INSERT INTO osc_zones VALUES (69,38,'NF','Newfoundland');
INSERT INTO osc_zones VALUES (70,38,'NB','New Brunswick');
INSERT INTO osc_zones VALUES (71,38,'NS','Nova Scotia');
INSERT INTO osc_zones VALUES (72,38,'NT','Northwest Territories');
INSERT INTO osc_zones VALUES (73,38,'NU','Nunavut');
INSERT INTO osc_zones VALUES (74,38,'ON','Ontario');
INSERT INTO osc_zones VALUES (75,38,'PE','Prince Edward Island');
INSERT INTO osc_zones VALUES (76,38,'QC','Quebec');
INSERT INTO osc_zones VALUES (77,38,'SK','Saskatchewan');
INSERT INTO osc_zones VALUES (78,38,'YT','Yukon Territory');

# Germany
INSERT INTO osc_zones VALUES (79,81,'NDS','Niedersachsen');
INSERT INTO osc_zones VALUES (80,81,'BAW','Baden-Württemberg');
INSERT INTO osc_zones VALUES (81,81,'BAY','Bayern');
INSERT INTO osc_zones VALUES (82,81,'BER','Berlin');
INSERT INTO osc_zones VALUES (83,81,'BRG','Brandenburg');
INSERT INTO osc_zones VALUES (84,81,'BRE','Bremen');
INSERT INTO osc_zones VALUES (85,81,'HAM','Hamburg');
INSERT INTO osc_zones VALUES (86,81,'HES','Hessen');
INSERT INTO osc_zones VALUES (87,81,'MEC','Mecklenburg-Vorpommern');
INSERT INTO osc_zones VALUES (88,81,'NRW','Nordrhein-Westfalen');
INSERT INTO osc_zones VALUES (89,81,'RHE','Rheinland-Pfalz');
INSERT INTO osc_zones VALUES (90,81,'SAR','Saarland');
INSERT INTO osc_zones VALUES (91,81,'SAS','Sachsen');
INSERT INTO osc_zones VALUES (92,81,'SAC','Sachsen-Anhalt');
INSERT INTO osc_zones VALUES (93,81,'SCN','Schleswig-Holstein');
INSERT INTO osc_zones VALUES (94,81,'THE','Thüringen');

# Austria
INSERT INTO osc_zones VALUES (95,14,'WI','Wien');
INSERT INTO osc_zones VALUES (96,14,'NO','Niederösterreich');
INSERT INTO osc_zones VALUES (97,14,'OO','Oberösterreich');
INSERT INTO osc_zones VALUES (98,14,'SB','Salzburg');
INSERT INTO osc_zones VALUES (99,14,'KN','Kärnten');
INSERT INTO osc_zones VALUES (100,14,'ST','Steiermark');
INSERT INTO osc_zones VALUES (101,14,'TI','Tirol');
INSERT INTO osc_zones VALUES (102,14,'BL','Burgenland');
INSERT INTO osc_zones VALUES (103,14,'VB','Voralberg');

# Swizterland
INSERT INTO osc_zones VALUES (104,204,'AG','Aargau');
INSERT INTO osc_zones VALUES (105,204,'AI','Appenzell Innerrhoden');
INSERT INTO osc_zones VALUES (106,204,'AR','Appenzell Ausserrhoden');
INSERT INTO osc_zones VALUES (107,204,'BE','Bern');
INSERT INTO osc_zones VALUES (108,204,'BL','Basel-Landschaft');
INSERT INTO osc_zones VALUES (109,204,'BS','Basel-Stadt');
INSERT INTO osc_zones VALUES (110,204,'FR','Freiburg');
INSERT INTO osc_zones VALUES (111,204,'GE','Genf');
INSERT INTO osc_zones VALUES (112,204,'GL','Glarus');
INSERT INTO osc_zones VALUES (113,204,'JU','Graubünden');
INSERT INTO osc_zones VALUES (114,204,'JU','Jura');
INSERT INTO osc_zones VALUES (115,204,'LU','Luzern');
INSERT INTO osc_zones VALUES (116,204,'NE','Neuenburg');
INSERT INTO osc_zones VALUES (117,204,'NW','Nidwalden');
INSERT INTO osc_zones VALUES (118,204,'OW','Obwalden');
INSERT INTO osc_zones VALUES (119,204,'SG','St. Gallen');
INSERT INTO osc_zones VALUES (120,204,'SH','Schaffhausen');
INSERT INTO osc_zones VALUES (121,204,'SO','Solothurn');
INSERT INTO osc_zones VALUES (122,204,'SZ','Schwyz');
INSERT INTO osc_zones VALUES (123,204,'TG','Thurgau');
INSERT INTO osc_zones VALUES (124,204,'TI','Tessin');
INSERT INTO osc_zones VALUES (125,204,'UR','Uri');
INSERT INTO osc_zones VALUES (126,204,'VD','Waadt');
INSERT INTO osc_zones VALUES (127,204,'VS','Wallis');
INSERT INTO osc_zones VALUES (128,204,'ZG','Zug');
INSERT INTO osc_zones VALUES (129,204,'ZH','Zürich');

# Spain
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'A Coruña','A Coruña');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Alava','Alava');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Albacete','Albacete');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Alicante','Alicante');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Almeria','Almeria');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Asturias','Asturias');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Avila','Avila');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Badajoz','Badajoz');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Baleares','Baleares');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Barcelona','Barcelona');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Burgos','Burgos');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Caceres','Caceres');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cadiz','Cadiz');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cantabria','Cantabria');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Castellon','Castellon');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ceuta','Ceuta');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ciudad Real','Ciudad Real');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cordoba','Cordoba');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Cuenca','Cuenca');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Girona','Girona');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Granada','Granada');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Guadalajara','Guadalajara');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Guipuzcoa','Guipuzcoa');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Huelva','Huelva');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Huesca','Huesca');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Jaen','Jaen');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'La Rioja','La Rioja');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Las Palmas','Las Palmas');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Leon','Leon');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Lleida','Lleida');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Lugo','Lugo');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Madrid','Madrid');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Malaga','Malaga');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Melilla','Melilla');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Murcia','Murcia');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Navarra','Navarra');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Ourense','Ourense');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Palencia','Palencia');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Pontevedra','Pontevedra');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Salamanca','Salamanca');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Santa Cruz de Tenerife','Santa Cruz de Tenerife');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Segovia','Segovia');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Sevilla','Sevilla');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Soria','Soria');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Tarragona','Tarragona');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Teruel','Teruel');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Toledo','Toledo');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Valencia','Valencia');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Valladolid','Valladolid');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Vizcaya','Vizcaya');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Zamora','Zamora');
INSERT INTO osc_zones (zone_country_id, zone_code, zone_name) VALUES (195,'Zaragoza','Zaragoza');

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Gift Coupon for new customers', 'NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '0', 'Gift coupon for new customers<br><br>Mettre 0 for none<br>', 1, 31, NULL, '2003-12-05 05:01:41', NULL, NULL);

INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Discount Coupon for new customers', 'NEW_SIGNUP_DISCOUNT_COUPON', '', 'Discount coupon for new customers.<br><br>Worth of discount coupon<BR>', 1, 32, NULL, '2003-12-05 05:01:41', NULL, NULL);



#
# Table structure for table `coupon_email_track`
#

CREATE TABLE osc_coupon_email_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id_sent int(11) NOT NULL default '0',
  sent_firstname varchar(32) default NULL,
  sent_lastname varchar(32) default NULL,
  emailed_to varchar(32) default NULL,
  date_sent datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (unique_id)
) TYPE=MyISAM;


#
# Table structure for table `coupon_gv_customer`
#

CREATE TABLE osc_coupon_gv_customer (
  customer_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  PRIMARY KEY  (customer_id),
  KEY customer_id (customer_id)
) TYPE=MyISAM;


#
# Table structure for table `coupon_gv_queue`
#

CREATE TABLE osc_coupon_gv_queue (
  unique_id int(5) NOT NULL auto_increment,
  customer_id int(5) NOT NULL default '0',
  order_id int(5) NOT NULL default '0',
  amount decimal(8,4) NOT NULL default '0.0000',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  ipaddr varchar(32) NOT NULL default '',
  release_flag char(1) NOT NULL default 'N',
  PRIMARY KEY  (unique_id),
  KEY uid (unique_id,customer_id,order_id)
) TYPE=MyISAM;


#
# Table structure for table `coupon_redeem_track`
#

CREATE TABLE osc_coupon_redeem_track (
  unique_id int(11) NOT NULL auto_increment,
  coupon_id int(11) NOT NULL default '0',
  customer_id int(11) NOT NULL default '0',
  redeem_date datetime NOT NULL default '0000-00-00 00:00:00',
  redeem_ip varchar(32) NOT NULL default '',
  order_id int(11) NOT NULL default '0',
  PRIMARY KEY  (unique_id)
) TYPE=MyISAM;


#
# Table structure for table `coupons`
#

CREATE TABLE osc_coupons (
  coupon_id int(11) NOT NULL auto_increment,
  coupon_type char(1) NOT NULL default 'F',
  coupon_code varchar(32) NOT NULL default '',
  coupon_amount decimal(8,4) NOT NULL default '0.0000',
  coupon_minimum_order decimal(8,4) NOT NULL default '0.0000',
  coupon_start_date datetime NOT NULL default '0000-00-00 00:00:00',
  coupon_expire_date datetime NOT NULL default '0000-00-00 00:00:00',
  uses_per_coupon int(5) NOT NULL default '1',
  uses_per_user int(5) NOT NULL default '0',
  restrict_to_products varchar(255) default NULL,
  restrict_to_categories varchar(255) default NULL,
  restrict_to_customers text,
  coupon_active char(1) NOT NULL default 'Y',
  date_created datetime NOT NULL default '0000-00-00 00:00:00',
  date_modified datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (coupon_id)
) TYPE=MyISAM;


#
# Table structure for table `coupons_description`
#

CREATE TABLE osc_coupons_description (
  coupon_id int(11) NOT NULL default '0',
  language_id int(11) NOT NULL default '0',
  coupon_name varchar(32) NOT NULL default '',
  coupon_description text,
  KEY coupon_id (coupon_id)
) TYPE=MyISAM;

# xosC features
#
# Table structure for table `customer_groups`
#

CREATE TABLE osc_customer_group (
  customer_group_id int(11) NOT NULL auto_increment,
  customer_group_name varchar(50),
  customer_group_shipping text NOT NULL ,
  customer_group_payment text NOT NULL ,
  customer_group_description text,
  customer_group_tax int(1),
  customer_group_fsk18 int(1),
  customer_group_display_tax int(1),
  customer_group_display_shipment int(1),
  PRIMARY KEY customer_group_id  (customer_group_id )
) TYPE=MyISAM;

INSERT INTO osc_customer_group (customer_group_id,customer_group_name,customer_group_shipping,customer_group_payment,customer_group_description,customer_group_tax,customer_group_fsk18,customer_group_display_tax,customer_group_display_shipment) VALUES (1,'Standard','all.php','all.php','Standard installing group with all rights',1,1,1,1);

ALTER TABLE `osc_products` ADD `products_fsk18` int (1) NOT NULL ;
ALTER TABLE `osc_customers` ADD `customers_group_id` int (11) NOT NULL ;
ALTER TABLE `osc_categories` ADD `categories_status` int (1) NOT NULL ;
ALTER TABLE `osc_products_description` ADD `short_description` varchar(255) NOT NULL ;
ALTER TABLE `osc_categories_description` ADD `short_description` varchar(255) NOT NULL ;
update `osc_customers` set customers_group_id = 1;


# SMTP variables Added by Fera Tech, Inc.


INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('SMTP Server Host address', 'EMAIL_SMTP_HOST_SERVER', 'smtp.mailserver.net', 'EMAIL SMTP HOST SERVER', '12', '10', NULL, now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('SMTP Server Port (default 25) address', 'EMAIL_SMTP_PORT_SERVER', '25', 'EMAIL SMTP PORT SERVER', '12', '12', NULL, now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('SMTP Server use password address', 'EMAIL_SMTP_ACTIVE_PASSWORD', 'false', 'EMAIL SMTP ACTIVATE PASSWORD', '12', '13', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('SMTP E-mail Username', 'EMAIL_SMTP_USERNAME', 'e-mail usename', 'EMAIL SMTP USERNAME', '12', '14', NULL, now());
INSERT INTO osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('SMTP E-mail Password', 'EMAIL_SMTP_PASSWORD', 'e-mail password', 'EMAIL SMTP PASSWORD', '12', '15', NULL, now());


# Aus performance Gründen
ALTER TABLE `osc_specials` ADD INDEX ( `products_id` ) ;
ALTER TABLE `osc_products` ADD INDEX ( `products_id` , `products_status` );


ALTER TABLE `osc_products` ADD `products_price_class` varchar (30) NOT NULL default 'price_standard';
ALTER TABLE `osc_products` ADD `products_ean` varchar (16) NOT NULL;
ALTER TABLE `osc_products` ADD `products_pdf` varchar (64) NOT NULL;
ALTER TABLE `osc_products` ADD `products_distributor_id` int(11) NOT NULL;
ALTER TABLE `osc_products` ADD `products_distributor_article_id` varchar(30) NOT NULL;

CREATE TABLE `osc_distributors` (
 `distributor_id` int(11)  NOT NULL AUTO_INCREMENT,
 `distributor_name` varchar(30) NOT NULL,
 `pdf_prefix` varchar(255) NOT NULL,
 `image_prefix` varchar(255) NOT NULL,
 PRIMARY KEY  (`distributor_id`)
) TYPE=MyISAM;
# osCommerce, Open Source E-Commerce Solutions
# http://www.oscommerce.com
#
# Ver 2.0

insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) values ('Enable moneybookers Module', 'MODULE_PAYMENT_MONEYBOOKERS_STATUS', 'False', 'Do you want to accept moneybookers payments?', '6', '3', NULL, '2003-01-20 12:19:37', NULL, 'tep_cfg_select_option(array(\'True\', \'False\'),');
insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) values ('E-Mail Address', 'MODULE_PAYMENT_MONEYBOOKERS_ID', '', 'The eMail address to use for the moneybookers service', '6', '4', NULL, '2003-01-20 12:19:37', NULL, NULL);
insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) values ('Referral ID', 'MODULE_PAYMENT_MONEYBOOKERS_REFID', '', 'Your personal Referral ID from moneybookers.com', '6', '5', NULL, '2003-01-20 12:19:37', NULL, '');
insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) values ('Transaction Language', 'MODULE_PAYMENT_MONEYBOOKERS_LANGUAGE', 'EN', 'The language for the payment transactions', '6', '6', NULL, '2003-01-20 12:19:37', NULL, 'tep_cfg_select_option(array(\'Selected Language\',\'DE\', \'EN\', \'FR\', \'ES\'),');
insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) values ('Transaction Currency', 'MODULE_PAYMENT_MONEYBOOKERS_CURRENCY', 'EUR', 'The currency to use for credit card transactions', '6', '7', NULL, '2003-01-20 12:19:37', NULL, 'tep_cfg_select_option(array(\'Selected Currency\',\'EUR\', \'USD\', \'GBP\', \'HKD\', \'SGD\', \'JPY\', \'CAD\', \'AUD\', \'CHF\', \'DKK\', \'SEK\', \'NOK\', \'ILS\', \'MYR\', \'NZD\', \'TWD\', \'THB\', \'CZK\', \'HUF\', \'SKK\', \'ISK\', \'INR\'),');
insert into osc_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_MONEYBOOKERS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', '2003-01-20 12:19:37');


create table osc_countries (
  countries_id int(11) not null auto_increment,
  countries_name varchar(64) not null ,
  countries_iso_code_2 char(2) not null ,
  countries_iso_code_3 char(3) not null ,
  countries_moneybookers char(3) ,
  address_format_id int(11) default '0' not null ,
  PRIMARY KEY (countries_id),
  KEY IDX_COUNTRIES_NAME (countries_name)
);

insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('1', 'Afghanistan', 'AF', 'AFG', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('2', 'Albania', 'AL', 'ALB', 'ALB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('3', 'Algeria', 'DZ', 'DZA', 'ALG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('4', 'American Samoa', 'AS', 'ASM', 'AME', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('5', 'Andorra', 'AD', 'AND', 'AND', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('6', 'Angola', 'AO', 'AGO', 'AGL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('7', 'Anguilla', 'AI', 'AIA', 'ANG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('8', 'Antarctica', 'AQ', 'ATA', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('9', 'Antigua and Barbuda', 'AG', 'ATG', 'ANT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('10', 'Argentina', 'AR', 'ARG', 'ARG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('11', 'Armenia', 'AM', 'ARM', 'ARM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('12', 'Aruba', 'AW', 'ABW', 'ARU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('13', 'Australia', 'AU', 'AUS', 'AUS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('14', 'Austria', 'AT', 'AUT', 'AUT', '5');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('15', 'Azerbaijan', 'AZ', 'AZE', 'AZE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('16', 'Bahamas', 'BS', 'BHS', 'BMS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('17', 'Bahrain', 'BH', 'BHR', 'BAH', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('18', 'Bangladesh', 'BD', 'BGD', 'BAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('19', 'Barbados', 'BB', 'BRB', 'BAR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('20', 'Belarus', 'BY', 'BLR', 'BLR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('21', 'Belgium', 'BE', 'BEL', 'BGM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('22', 'Belize', 'BZ', 'BLZ', 'BEL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('23', 'Benin', 'BJ', 'BEN', 'BEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('24', 'Bermuda', 'BM', 'BMU', 'BER', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('25', 'Bhutan', 'BT', 'BTN', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('26', 'Bolivia', 'BO', 'BOL', 'BOL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('27', 'Bosnia and Herzegowina', 'BA', 'BIH', 'BOS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('28', 'Botswana', 'BW', 'BWA', 'BOT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('29', 'Bouvet Island', 'BV', 'BVT', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('30', 'Brazil', 'BR', 'BRA', 'BRA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('31', 'British Indian Ocean Territory', 'IO', 'IOT', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('32', 'Brunei Darussalam', 'BN', 'BRN', 'BRU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('33', 'Bulgaria', 'BG', 'BGR', 'BUL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('34', 'Burkina Faso', 'BF', 'BFA', 'BKF', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('35', 'Burundi', 'BI', 'BDI', 'BUR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('36', 'Cambodia', 'KH', 'KHM', 'CAM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('37', 'Cameroon', 'CM', 'CMR', 'CMR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('38', 'Canada', 'CA', 'CAN', 'CAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('39', 'Cape Verde', 'CV', 'CPV', 'CAP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('40', 'Cayman Islands', 'KY', 'CYM', 'CAY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('41', 'Central African Republic', 'CF', 'CAF', 'CEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('42', 'Chad', 'TD', 'TCD', 'CHA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('43', 'Chile', 'CL', 'CHL', 'CHL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('44', 'China', 'CN', 'CHN', 'CHN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('45', 'Christmas Island', 'CX', 'CXR', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('46', 'Cocos (Keeling) Islands', 'CC', 'CCK', 'COO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('47', 'Colombia', 'CO', 'COL', 'COL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('48', 'Comoros', 'KM', 'COM', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('49', 'Congo', 'CG', 'COG', 'CON', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('50', 'Cook Islands', 'CK', 'COK', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('51', 'Costa Rica', 'CR', 'CRI', 'COS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('52', 'Cote D\'Ivoire', 'CI', 'CIV', 'COT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('53', 'Croatia', 'HR', 'HRV', 'CRO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('54', 'Cuba', 'CU', 'CUB', 'CUB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('55', 'Cyprus', 'CY', 'CYP', 'CYP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('56', 'Czech Republic', 'CZ', 'CZE', 'CZE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('57', 'Denmark', 'DK', 'DNK', 'DEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('58', 'Djibouti', 'DJ', 'DJI', 'DJI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('59', 'Dominica', 'DM', 'DMA', 'DOM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('60', 'Dominican Republic', 'DO', 'DOM', 'DRP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('61', 'East Timor', 'TP', 'TMP', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('62', 'Ecuador', 'EC', 'ECU', 'ECU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('63', 'Egypt', 'EG', 'EGY', 'EGY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('64', 'El Salvador', 'SV', 'SLV', 'EL_', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('65', 'Equatorial Guinea', 'GQ', 'GNQ', 'EQU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('66', 'Eritrea', 'ER', 'ERI', 'ERI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('67', 'Estonia', 'EE', 'EST', 'EST', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('68', 'Ethiopia', 'ET', 'ETH', 'ETH', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('69', 'Falkland Islands (Malvinas)', 'FK', 'FLK', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('70', 'Faroe Islands', 'FO', 'FRO', 'FAR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('71', 'Fiji', 'FJ', 'FJI', 'FIJ', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('72', 'Finland', 'FI', 'FIN', 'FIN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('73', 'France', 'FR', 'FRA', 'FRA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('74', 'France, Metropolitan', 'FX', 'FXX', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('75', 'French Guiana', 'GF', 'GUF', 'FRE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('76', 'French Polynesia', 'PF', 'PYF', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('77', 'French Southern Territories', 'TF', 'ATF', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('78', 'Gabon', 'GA', 'GAB', 'GAB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('79', 'Gambia', 'GM', 'GMB', 'GAM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('80', 'Georgia', 'GE', 'GEO', 'GEO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('81', 'Germany', 'DE', 'DEU', 'GER', '5');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('82', 'Ghana', 'GH', 'GHA', 'GHA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('83', 'Gibraltar', 'GI', 'GIB', 'GIB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('84', 'Greece', 'GR', 'GRC', 'GRC', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('85', 'Greenland', 'GL', 'GRL', 'GRL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('86', 'Grenada', 'GD', 'GRD', 'GRE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('87', 'Guadeloupe', 'GP', 'GLP', 'GDL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('88', 'Guam', 'GU', 'GUM', 'GUM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('89', 'Guatemala', 'GT', 'GTM', 'GUA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('90', 'Guinea', 'GN', 'GIN', 'GUI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('91', 'Guinea-bissau', 'GW', 'GNB', 'GBS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('92', 'Guyana', 'GY', 'GUY', 'GUY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('93', 'Haiti', 'HT', 'HTI', 'HAI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('94', 'Heard and Mc Donald Islands', 'HM', 'HMD', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('95', 'Honduras', 'HN', 'HND', 'HON', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('96', 'Hong Kong', 'HK', 'HKG', 'HKG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('97', 'Hungary', 'HU', 'HUN', 'HUN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('98', 'Iceland', 'IS', 'ISL', 'ICE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('99', 'India', 'IN', 'IND', 'IND', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('100', 'Indonesia', 'ID', 'IDN', 'IDS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('101', 'Iran (Islamic Republic of)', 'IR', 'IRN', 'IRN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('102', 'Iraq', 'IQ', 'IRQ', 'IRA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('103', 'Ireland', 'IE', 'IRL', 'IRE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('104', 'Israel', 'IL', 'ISR', 'ISR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('105', 'Italy', 'IT', 'ITA', 'ITA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('106', 'Jamaica', 'JM', 'JAM', 'JAM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('107', 'Japan', 'JP', 'JPN', 'JAP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('108', 'Jordan', 'JO', 'JOR', 'JOR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('109', 'Kazakhstan', 'KZ', 'KAZ', 'KAZ', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('110', 'Kenya', 'KE', 'KEN', 'KEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('111', 'Kiribati', 'KI', 'KIR', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('112', 'Korea, Democratic People\'s Republic of', 'KP', 'PRK', 'SKO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('113', 'Korea, Republic of', 'KR', 'KOR', 'KOR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('114', 'Kuwait', 'KW', 'KWT', 'KUW', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('115', 'Kyrgyzstan', 'KG', 'KGZ', 'KYR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('116', 'Lao People\'s Democratic Republic', 'LA', 'LAO', 'LAO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('117', 'Latvia', 'LV', 'LVA', 'LAT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('118', 'Lebanon', 'LB', 'LBN', 'LEB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('119', 'Lesotho', 'LS', 'LSO', 'LES', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('120', 'Liberia', 'LR', 'LBR', 'LIB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('121', 'Libyan Arab Jamahiriya', 'LY', 'LBY', 'LBY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('122', 'Liechtenstein', 'LI', 'LIE', 'LIE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('123', 'Lithuania', 'LT', 'LTU', 'LIT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('124', 'Luxembourg', 'LU', 'LUX', 'LUX', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('125', 'Macau', 'MO', 'MAC', 'MAC', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('126', 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD', 'F.Y', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('127', 'Madagascar', 'MG', 'MDG', 'MAD', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('128', 'Malawi', 'MW', 'MWI', 'MLW', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('129', 'Malaysia', 'MY', 'MYS', 'MLS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('130', 'Maldives', 'MV', 'MDV', 'MAL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('131', 'Mali', 'ML', 'MLI', 'MLI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('132', 'Malta', 'MT', 'MLT', 'MLT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('133', 'Marshall Islands', 'MH', 'MHL', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('134', 'Martinique', 'MQ', 'MTQ', 'MAR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('135', 'Mauritania', 'MR', 'MRT', 'MRT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('136', 'Mauritius', 'MU', 'MUS', 'MAU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('137', 'Mayotte', 'YT', 'MYT', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('138', 'Mexico', 'MX', 'MEX', 'MEX', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('139', 'Micronesia, Federated States of', 'FM', 'FSM', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('140', 'Moldova, Republic of', 'MD', 'MDA', 'MOL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('141', 'Monaco', 'MC', 'MCO', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('142', 'Mongolia', 'MN', 'MNG', 'MON', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('143', 'Montserrat', 'MS', 'MSR', 'MTT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('144', 'Morocco', 'MA', 'MAR', 'MOR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('145', 'Mozambique', 'MZ', 'MOZ', 'MOZ', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('146', 'Myanmar', 'MM', 'MMR', 'MYA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('147', 'Namibia', 'NA', 'NAM', 'NAM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('148', 'Nauru', 'NR', 'NRU', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('149', 'Nepal', 'NP', 'NPL', 'NEP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('150', 'Netherlands', 'NL', 'NLD', 'NED', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('151', 'Netherlands Antilles', 'AN', 'ANT', 'NET', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('152', 'New Caledonia', 'NC', 'NCL', 'CDN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('153', 'New Zealand', 'NZ', 'NZL', 'NEW', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('154', 'Nicaragua', 'NI', 'NIC', 'NIC', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('155', 'Niger', 'NE', 'NER', 'NIG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('156', 'Nigeria', 'NG', 'NGA', 'NGR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('157', 'Niue', 'NU', 'NIU', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('158', 'Norfolk Island', 'NF', 'NFK', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('159', 'Northern Mariana Islands', 'MP', 'MNP', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('160', 'Norway', 'NO', 'NOR', 'NWY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('161', 'Oman', 'OM', 'OMN', 'OMA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('162', 'Pakistan', 'PK', 'PAK', 'PAK', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('163', 'Palau', 'PW', 'PLW', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('164', 'Panama', 'PA', 'PAN', 'PAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('165', 'Papua New Guinea', 'PG', 'PNG', 'PAP', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('166', 'Paraguay', 'PY', 'PRY', 'PAR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('167', 'Peru', 'PE', 'PER', 'PER', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('168', 'Philippines', 'PH', 'PHL', 'PHI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('169', 'Pitcairn', 'PN', 'PCN', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('170', 'Poland', 'PL', 'POL', 'POL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('171', 'Portugal', 'PT', 'PRT', 'POR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('172', 'Puerto Rico', 'PR', 'PRI', 'PUE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('173', 'Qatar', 'QA', 'QAT', 'QAT', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('174', 'Reunion', 'RE', 'REU', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('175', 'Romania', 'RO', 'ROM', 'ROM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('176', 'Russian Federation', 'RU', 'RUS', 'RUS', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('177', 'Rwanda', 'RW', 'RWA', 'RWA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('178', 'Saint Kitts and Nevis', 'KN', 'KNA', 'SKN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('179', 'Saint Lucia', 'LC', 'LCA', 'SLU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('180', 'Saint Vincent and the Grenadines', 'VC', 'VCT', 'ST.', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('181', 'Samoa', 'WS', 'WSM', 'WES', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('182', 'San Marino', 'SM', 'SMR', 'SAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('183', 'Sao Tome and Principe', 'ST', 'STP', 'SAO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('184', 'Saudi Arabia', 'SA', 'SAU', 'SAU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('185', 'Senegal', 'SN', 'SEN', 'SEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('186', 'Seychelles', 'SC', 'SYC', 'SEY', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('187', 'Sierra Leone', 'SL', 'SLE', 'SIE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('188', 'Singapore', 'SG', 'SGP', 'SIN', '4');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('189', 'Slovakia (Slovak Republic)', 'SK', 'SVK', 'SLO', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('190', 'Slovenia', 'SI', 'SVN', 'SLV', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('191', 'Solomon Islands', 'SB', 'SLB', 'SOL', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('192', 'Somalia', 'SO', 'SOM', 'SOM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('193', 'South Africa', 'ZA', 'ZAF', 'SOU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('194', 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('195', 'Spain', 'ES', 'ESP', 'SPA', '3');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('196', 'Sri Lanka', 'LK', 'LKA', 'SRI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('197', 'St. Helena', 'SH', 'SHN', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('198', 'St. Pierre and Miquelon', 'PM', 'SPM', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('199', 'Sudan', 'SD', 'SDN', 'SUD', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('200', 'Suriname', 'SR', 'SUR', 'SUR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('201', 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('202', 'Swaziland', 'SZ', 'SWZ', 'SWA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('203', 'Sweden', 'SE', 'SWE', 'SWE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('204', 'Switzerland', 'CH', 'CHE', 'ZWI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('205', 'Syrian Arab Republic', 'SY', 'SYR', 'SYR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('206', 'Taiwan', 'TW', 'TWN', 'TWN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('207', 'Tajikistan', 'TJ', 'TJK', 'TAJ', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('208', 'Tanzania, United Republic of', 'TZ', 'TZA', 'TAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('209', 'Thailand', 'TH', 'THA', 'THA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('210', 'Togo', 'TG', 'TGO', 'TOG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('211', 'Tokelau', 'TK', 'TKL', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('212', 'Tonga', 'TO', 'TON', 'TON', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('213', 'Trinidad and Tobago', 'TT', 'TTO', 'TRI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('214', 'Tunisia', 'TN', 'TUN', 'TUN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('215', 'Turkey', 'TR', 'TUR', 'TUR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('216', 'Turkmenistan', 'TM', 'TKM', 'TKM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('217', 'Turks and Caicos Islands', 'TC', 'TCA', 'TCI', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('218', 'Tuvalu', 'TV', 'TUV', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('219', 'Uganda', 'UG', 'UGA', 'UGA', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('220', 'Ukraine', 'UA', 'UKR', 'UKR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('221', 'United Arab Emirates', 'AE', 'ARE', 'UAE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('222', 'United Kingdom', 'GB', 'GBR', 'GBR', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('223', 'United States', 'US', 'USA', 'UNI', '2');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('224', 'United States Minor Outlying Islands', 'UM', 'UMI', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('225', 'Uruguay', 'UY', 'URY', 'URU', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('226', 'Uzbekistan', 'UZ', 'UZB', 'UZB', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('227', 'Vanuatu', 'VU', 'VUT', 'VAN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('228', 'Vatican City State (Holy See)', 'VA', 'VAT', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('229', 'Venezuela', 'VE', 'VEN', 'VEN', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('230', 'Viet Nam', 'VN', 'VNM', 'VIE', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('231', 'Virgin Islands (British)', 'VG', 'VGB', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('232', 'Virgin Islands (U.S.)', 'VI', 'VIR', 'US_', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('233', 'Wallis and Futuna Islands', 'WF', 'WLF', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('234', 'Western Sahara', 'EH', 'ESH', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('235', 'Yemen', 'YE', 'YEM', 'YEM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('236', 'Yugoslavia', 'YU', 'YUG', 'YUG', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('237', 'Zaire', 'ZR', 'ZAR', '', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('238', 'Zambia', 'ZM', 'ZMB', 'ZAM', '1');
insert into osc_countries (countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, countries_moneybookers, address_format_id) values ('239', 'Zimbabwe', 'ZW', 'ZWE', 'ZIM', '1');

# Margin Report v2.56 SQL File

ALTER TABLE `osc_products` ADD `products_cost` DECIMAL( 15, 4 ) DEFAULT '0.0000' NOT NULL AFTER `products_price` ;
ALTER TABLE `osc_orders_products` ADD `products_cost` DECIMAL( 15, 4 ) DEFAULT '0.0000' NOT NULL AFTER `products_price`;

# Fancier Invoice & Packing Slip 6.1

INSERT INTO `osc_configuration` ( `configuration_id` , `configuration_title` , `configuration_key` , `configuration_value` , `configuration_description` , `configuration_group_id` , `sort_order` , `last_modified` , `date_added` , `use_function` , `set_function` ) VALUES ('', 'Send HTML or Text Invoices to Customers', 'EMAIL_INVOICE', 'true', 'If this is enabled, order invoices will be sent to the customer in HTML format.<br><br>Enabled = true<br>Disabled = false<br><br>To use this, you must have HTML E-mails enabled.<br><br>', '12', '0', NULL , NOW( ) , '',  "tep_cfg_select_option(array('true', 'false'),");
INSERT INTO `osc_configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES ('', 'Default E-Mailed HTML Invoice Template', 'EMAIL_TEMPLATE_FILE', 'html_invoice.php', 'Template file you will be using for HTML invoices to the customer<BR><BR><i>invoice.php</i> - Stock osC look<BR><i>html_invoice.php</i> - HTML version<BR><i>box_invoice.php</i> - HTML version<BR><BR>', 12, 0, NULL, NOW(), '', '');
