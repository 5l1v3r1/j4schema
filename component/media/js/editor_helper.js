var J4SDialog = {
	init : function() {
		this.textarea = document.getElementById('html_code');
		this.textarea.value = tinyMCEPopup.editor.selection.getContent();
	},
		
	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].html_code.value);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(J4SDialog.init, J4SDialog);
