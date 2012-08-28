window.addEvent('load', function(){
	J4Stree = new chooseElement({
			attrib_container : $('attrib_container'),
			add_attrib		 : $('add_attribute'),
			add_type		 : $('add_type'),
			html_code		 : $('html_code'),
			paste_button	 : $('paste_editor'),
			type_container	 : $('tree_container')
		});
})