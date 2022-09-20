var mce_basic_options = {
	script_url : '/board/ace_solution/lib/etc/tiny_mce/tiny_mce.js',
	language : "ko",
	mode : "textareas",
	theme : "advanced",
	editor_selector : "mceBasic",
	plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,visualblocks,imageupload,images",

	// Theme options
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,forecolor,backcolor,fontselect,fontsizeselect,image,images,media,|,insertdate,inserttime,preview",
	theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,cut,copy,paste,pastetext,pasteword,undo,redo,|,link,unlink,anchor,cleanup,code",
//	theme_advanced_buttons3 : "styleselect,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,print,|,ltr,rtl,|,fullscreen,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,",
//	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_fonts : "±º∏≤√º=±º∏≤;"+
				"πŸ≈¡√º=πŸ≈¡;"+
				"µ∏øÚ√º=µ∏øÚ;"+
				"±√º≠√º=±√º≠;"+
				"Andale Mono=andale mono,times;"+
                "Arial=arial,helvetica,sans-serif;"+
                "Arial Black=arial black,avant garde;"+
                "Book Antiqua=book antiqua,palatino;"+
                "Comic Sans MS=comic sans ms,sans-serif;"+
                "Courier New=courier new,courier;"+
                "Georgia=georgia,palatino;"+
                "Helvetica=helvetica;"+
                "Impact=impact,chicago;"+
                "Symbol=symbol;"+
                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                "Terminal=terminal,monaco;"+
                "Times New Roman=times new roman,times;"+
                "Trebuchet MS=trebuchet ms,geneva;"+
                "Verdana=verdana,geneva;"+
                "Webdings=webdings;"+
                "Wingdings=wingdings,zapf dingbats",
	
	forced_root_block : false,
    force_p_newlines : false,
    remove_linebreaks : false,
    force_br_newlines : true,
    remove_trailing_nbsp : false,
    verify_html : false,

	// Example content CSS (should be your site CSS)
	content_css : "/board/ace_solution/lib/etc/tiny_mce/css/content.css",


	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "/board/ace_solution/lib/etc/tiny_mce/lists/template_list.js",
	external_link_list_url : "/board/ace_solution/lib/etc/tiny_mce/lists/link_list.js",
	external_image_list_url : "/board/ace_solution/lib/etc/tiny_mce/lists/image_list.js",
	media_external_list_url : "/board/ace_solution/lib/etc/tiny_mce/lists/media_list.js",

	// Style formats
	style_formats : [
		{title : 'µŒ≤®øÓ ±€¿⁄', inline : 'b'},
		{title : 'ª°∞£ ±€¿⁄', inline : 'span', styles : {color : '#ff0000'}},
		{title : 'ª°∞£¡¶∏Ò', block : 'h1', styles : {color : '#ff0000'}},
		{title : 'Example 1', inline : 'span', classes : 'example1'},
		{title : 'Example 2', inline : 'span', classes : 'example2'},
		{title : 'Table styles'},
		{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
	],

	// Replace values for the template plugin
	//template_replace_values : {
	//	username : "Some User",
	//	staffid : "991234"
	//},
	add_form_submit_trigger : true,

	
	relative_urls : false,
    convert_urls : false
};


tinyMCE.init(mce_basic_options);