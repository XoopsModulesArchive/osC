<?php
/*
  $Id: invoice.php,v 1.1 2002/06/11 18:17:59 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('TABLE_HEADING_COMMENTS', 'Comments');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model');
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price (inc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total (inc)');

define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_PAYMENT_METHOD', 'Payment Method:');
define('ENTRY_SUB_TOTAL', 'Sub-Total:');
define('ENTRY_TAX', 'Tax:');
define('ENTRY_SHIPPING', 'Shipping:');
define('ENTRY_TOTAL', 'Total:');

//// START Edit the following defines to your liking ////
// Footing
define('INVOICE_TEXT_THANK_YOU', 'Thank you for shopping at'); // Printed at the bottom of your invoices
define('STORE_URL_ADDRESS', XOOPS_URL); // Your web address Printed at the bottom of your invoices

define('INVOICE_TEXT_CURRENT_YEAR', date("y"));

// Product Table Info Headings
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model #'); // Change this to "Model #" or leave it as "SKU #"

//// END Editing the above defines to your liking ////
// Misc Invoice Info
define('INVOICE_TEXT_NUMBER_SIGN', '#');
define('INVOICE_TEXT_DASH', '-');
define('INVOICE_TEXT_COLON', ':');

define('INVOICE_TEXT_INVOICE', 'Invoice');
define('INVOICE_TEXT_ORDER', 'Order');
define('INVOICE_TEXT_DATE_OF_ORDER', 'Date of Order');
define('INVOICE_TEXT_DATE_DUE_DATE', 'Payment Date');
define('ENTRY_PAYMENT_CC_NUMBER', 'Credit Card:');

// Customer Info
define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_PAYMENT_METHOD', 'Payment:');

// Product Table Info Headings
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price (inc)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total (inc)');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_UNIT_PRICE', 'Unit Price');
define('TABLE_HEADING_TOTAL', 'Total');

// Order Total Details Info
define('ENTRY_SUB_TOTAL', 'Sub-Total:');
define('ENTRY_SHIPPING', 'Shipping:');
define('ENTRY_TAX', 'Tax:');
define('ENTRY_TOTAL', 'Total:');

//Order Comments
define('TABLE_HEADING_COMMENTS', 'ORDER COMMENTS:');
define('TABLE_HEADING_DATE_ADDED', 'Date Added');
define('TABLE_HEADING_COMMENT_LEFT', 'Comment Left');
define('INVOICE_TEXT_NO_COMMENT', 'No comments have been left for this order');
define('INVOICE_IMAGE_ALT_TEXT', 'Order Comments'); // Change this to your logo's ALT text or leave blank
//Footer
define('INVOICE_TEXT_VAT_NR', 'Our VAT # ');
define('INVOICE_TEXT_VAT_NR2', 'VAT'); // Enter your VAT number here
define('INVOICE_TEXT_BANK_NR', 'Bank acount #');
define('INVOICE_TEXT_BANK_NR2', 'Bank acount'); // Enter your Bank acount number here
define('INVOICE_TEXT_VAT_NR3', 'Organisation #');
define('INVOICE_TEXT_VAT_NR4', 'Organisation #'); // Enter your company organisation number here (special for Sweden)
define('INVOICE_TEXT_VAT_LICENSE', 'Tax license #');
define('INVOICE_TEXT_VAT_LICENSE2', 'Tex license #'); // Enter your f-skatt number here (special for sweden)
?>
