/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 */
window.addEvent('load', function(){
	J4Stree = new chooseElement({
			lite			 : true,
			attrib_container : document.id('attrib_container'),
			add_attrib		 : document.id('add_attribute'),
			add_type		 : document.id('add_type'),
			html_code		 : document.id('to_replace'),
			paste_button	 : document.id('paste_editor'),
			type_container	 : document.id('tree_container')
		});
})