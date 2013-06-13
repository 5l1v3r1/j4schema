(function() {
	tinymce.create('tinymce.plugins.J4schemaPlugin', {
		
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceJ4schema', function() {
				ed.windowManager.open({
					file 	: 'index.php?option=com_j4schema&view=editor&task=read&tmpl=component&render.toolbar=0',
					width 	: 900,
					height 	: 540,
					inline 	: 1,
					popup_css : false
				});
			});

			// Register example button
			ed.addButton('j4schema', {
				title : 'J4Schema.org',
				cmd : 'mceJ4schema',
				image : url + '/img/j4schema.png'
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('j4schema', tinymce.plugins.J4schemaPlugin);
})();