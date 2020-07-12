// ================================================
// PHP image browser - iBrowser 
// ================================================
// iBrowser - tinyMCE editor interface (IE & Gecko)
// ================================================
// Developed: net4visions.com
// Copyright: net4visions.com
// License: GPL - see license.txt
// (c)2005 All rights reserved.
// File: editor_plugin.js
// ================================================
// Revision: 1.0                   Date: 08/03/2005
// Modified by Rigadin (rigadin@osc-help.net) for osCommerce:
// Returns only the selected image, delete all previous html or text present in the textarea.
// ================================================

	//-------------------------------------------------------------------------
	// tinyMCE editor - open iBrowser
	function TinyMCE_ibrowser2_getControlHTML(control_name) {
		switch (control_name) {
			case 'ibrowser2':
				return '<img id="{$editor_id}_ibrowser2" src="{$pluginurl}/images/ibrowser.gif" title="Image Browser" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="(iBrowser2_click(\'{$editor_id}\'));">';
			}	
		return '';
	}
	//-------------------------------------------------------------------------
	// tinyMCE editor - init iBrowser
	function iBrowser2_click(editor) {
		ib.isMSIE = (navigator.appName == 'Microsoft Internet Explorer');
		ib.isGecko = navigator.userAgent.indexOf('Gecko') != -1;
		ib.oEditor = tinyMCE.getInstanceById(editor);
		ib.editor = editor;		
		ib.selectedElement = ib.getSelectedElement();
		ib.baseURL = tinyMCE.baseURL + '/plugins/ibrowser2/ibrowser.php';
		iBrowser2_open(); // starting iBrowser
	}
	//-------------------------------------------------------------------------
	// include common interface code
	var js  = document.createElement('script');
	js.type	= 'text/javascript';
	js.src  = tinyMCE.baseURL + '/plugins/ibrowser2/interface/common.js';
	// Add the new object to the HEAD element.
	document.getElementsByTagName('head')[0].appendChild(js) ; 
	//-------------------------------------------------------------------------	