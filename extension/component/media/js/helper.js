Element.implement({
	isVisible : function(){
		var display = this.getStyle('display');
		if(display == 'none') 	return false;
		else					return true;
	}
});

String.implement({
	timeToISO : function(duration){
		var string = this.toString();
		var parts = string.split(':');
		
		if(!parts[1]) return '';
		
		var time = '';		
		if(duration.toLowerCase() == 'duration') time += 'P';
		time += 'T';
		
		if(parts[0].toInt() > 0) time += parts[0].toInt() + 'H';
		if(parts[1].toInt() > 0) time += parts[1].toInt() + 'M';
		
		return time;
	}
});

function insertAtCursor(myField, myValue) 
{
	//IE support
	if (document.selection) 
	{
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}
	else if (myField.selectionStart || myField.selectionStart == '0') 
	{
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
						+ myValue
						+ myField.value.substring(endPos, myField.value.length);
	}
	else 
	{
		myField.value += myValue;
	}
}