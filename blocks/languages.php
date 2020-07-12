<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

  Babelfish mod to languages.php, Babelfish mod v2.0a

  This is not part of the official release of osCommerce.

  Babelfish is a registered trademark of Altavista.
  
  Mod by Coopco 2007

*/
function show_languages() {
  global $xoopsDB,$xoopsConfig,$xoopsTpl,$xoopsConfig;

  include_once XOOPS_ROOT_PATH."/modules/osC/includes/configure.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/database_tables.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/filenames.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/functions/database.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/functions/sessions.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/functions/general.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/functions/html_output.php";
  include_once XOOPS_ROOT_PATH."/modules/osC/includes/classes/boxes.php";


  $info_box_contents = array();
  $info_box_contents[] = array('text' => 'Babelfish Translation');

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => '



<!-- BEGIN ALL EDITING -->

<table width="100%" border="0">
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_zh">Chinese</a><br></td>
    <td><img src="images/languages/chinese.gif" border="0"></td>
  </tr>
  <tr>
    <td><font size=1><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_nl">Nederlands</a></td>
    <td><img src="images/languages/nl.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_fr">Français</a><br></td>
    <td><img src="images/languages/french.gif" border="0"></td>
  </tr>
  <tr>
    <td><font size=1><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_de">Deutsch</a></td>
    <td><img src="images/languages/german.gif" border="0"></td>
  </tr>
  <tr>
    <td><font size=1><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_el">Greek</a></td>
    <td><img src="images/languages/gr.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_it">Italiano</a><br></td>
    <td><img src="images/languages/italian.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_ja">Japanese</a><br></td>
    <td><img src="images/languages/japanese.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_ko">Korean</a><br></td>
    <td><img src="images/languages/korean.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_pt">Portuguese</a><br></td>
    <td><img src="images/languages/portuguese.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_ru">Pyccko</a></td>
    <td><img src="images/languages/ru.gif" border="0"></td>
  </tr>
  <tr>
    <td><a href="http://babelfish.altavista.com/babelfish/tr?doit=done&url='. $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] .'&lp=en_es">Español</a></td>
    <td><img src="images/languages/spanish.gif" border="0"></td>
  </tr>
</table>

<!-- FINISH ALL EDITING -->

');

  new infoBox($info_box_contents);
?>
			</td>
      	</tr>
<!-- languages_eof //--> 
