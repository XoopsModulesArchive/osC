<script language="javascript" type="text/javascript">
// Configuration file for image browsing using tinyMCE and iBrowser.
tinyMCE.init({
  mode : "exact",
  language: "en",
  elements: "<?php echo $mce_str ?>",
  theme : "advanced",
	plugins : "preview, ibrowser2",
	theme_advanced_buttons1 : "preview, ibrowser2",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",

  fullscreen_settings : {
  theme_advanced_path_location : "top"},
  document_base_url : "<?php echo HTTP_SERVER.DIR_WS_CATALOG_IMAGES ?>",
  		width : "480",
		height : "200",
		//theme_advanced_disable : "image",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  extended_valid_elements : "img[src]"
});
</script>