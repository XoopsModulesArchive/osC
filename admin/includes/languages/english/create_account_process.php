<?php
/*
  $Id: create_account_process.php,v 1 12:01 AM 17/08/2003 frankl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Create an Account');
define('HEADING_TITLE', 'Account Information');
define('HEADING_NEW', 'Order Process');
define('NAVBAR_NEW_TITLE', 'Order Process');

define('EMAIL_SUBJECT', 'Welcome to ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Dear Mr. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n");
define('EMAIL_GREET_MS', 'Dear Ms. ' . stripslashes($HTTP_POST_VARS['lastname']) . ',' . "\n");
define('EMAIL_GREET_NONE', 'Dear ' . stripslashes($HTTP_POST_VARS['firstname']) . ',' . "\n");
define('EMAIL_USER_EMAIL', stripslashes($HTTP_POST_VARS['email_address']));
define('EMAIL_WELCOME', 'We welcome you to <b>' . STORE_NAME . '</b>.');
define('EMAIL_TEXT', 'You can now take part in the <b>various services</b> we have to offer you.' . "\n" . 'In order to access your account please go to the registration page of our site. Please use the following e-mail address: '. EMAIL_USER_EMAIL .'' . "\n\n" . '<li><b>Registration Page</b>' . "\n" . '<a href="'. XOOPS_URL .'/register.php">'. XOOPS_URL .'/register.php</a>' . "\n" . 'Once registered you have access to all of the following services our website offers:' . "\n\n" . '<li><b>Store</b> - Visit and purchase our products.' . "\n" . '<a href="'. DIR_WS_CATALOG .'">'. DIR_WS_CATALOG .'</a>' . "\n\n" . '<li><b>Order History</b> - View all your orders placed on our site.' . "\n" . '<a href="'. DIR_WS_CATALOG .'account_history.php">'. DIR_WS_CATALOG .'account_history.php</a>' . "\n\n" . '<li><b>Newsletter</b> - Subscribe to our newsletter.' . "\n" . '<a href="'. DIR_WS_CATALOG .'account_newsletters.php">'. DIR_WS_CATALOG .'account_newsletters.php</a>' . "\n\n");
define('EMAIL_CONTACT', 'For help with any of our online services, please email the store-owner: ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");
define('EMAIL_WARNING', '<b>Note:</b>If you did not wish to register with our site, please send a email to ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n");

?>
