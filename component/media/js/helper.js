var J4Stree;

var chooseElement = new Class({
	Implements	: [Events, Options],
	attrib 		: {},
	loadingAttr : false,
	attrib_type : '',
	dataType	: '',
	editorHelper: '',
	jsonType	: {},
	jsonAttrib 	: {},
	nonStdValue : '',
	schema_attr : '',
	schema_type : '',
	selectedText: '',
	type		: {},
	fromType	: {},
	//elements
	dateTime	: '', calendarHolder : '', metaPropr : '', valuesList : '', valuesChoose : '', valuesDescr : '',
	warning		: '',
	options 	: {
		BASE_URL  : 'index.php?option=com_j4schema&format=json&view=',
		DATATYPES : ['text', 'number', 'date', 'duration', 'integer', 'url', 'enum'],
		editor : '',
		mode : '',
		type_container : '',
		attrib_container : '',
		html_code : '',
		paste_button : '',
		add_type : '',
		add_attrib : '',
		lite : false
	},

	initialize : function(options)
	{
		var self = this;
		this.setOptions(options);
		this.initProperties();
		this.initType();
		this.initAttrib();
		//this.editorHelper = new J4S(this.options.editor, this.options.mode);
		//this.options.html_code.value = this.editorHelper.getSelectedText();
		this.setEvents();

		this.jsonAttrib = new Request.JSON({
			url		: self.options.BASE_URL + 'attribute',
			onRequest : function(){
				self.valuesDescr.empty().addClass('loader-bg-small');
				self.nonStdValue = '';
				//document.id('proprPlusTypeHolder').addClass('hidden');
				self.valuesChoose.addClass('hidden');
				self.valuesList.addClass('hidden');
				document.id('attrib_descr').empty().addClass('loader-bg-small');
				self.dateTime.addClass('hidden');
				document.id('propOnly').checked = true;
				self.schema_attr = '';
				self.warning.empty();
			},
			onError : function(){
				return;
			},
			onSuccess : function(response){
				self.valuesDescr.removeClass('loader-bg-small').set('html', response.value_descr);
				document.id('attrib_descr').removeClass('loader-bg-small').set('html', response.descr);
				self.valuesList.empty();
				self.calendarHolder.addClass('hidden');
				document.id('timeHolder').addClass('hidden');
				self.dateTime.addClass('hidden')

				//if possible values are "types", create a list to select them
				var standard = response.value.some(function(item){return self.options.DATATYPES.contains(item.toLowerCase())});
				if(!standard) self.valuesChoose.removeClass('hidden');
				if(!standard && response.value.length > 1)
				{
					var i = 0;

					response.value.each(function(item){
						i++;
						var input = new Element('input', {
							type  : 'radio',
							value : 'https://schema.org/' + item,
							name  : 'valueList',
							id	  : 'valueList' + i,
							checked : 'true'
						});

						var br = new Element('br');
						var label = new Element('label', {html : item});
						label.setProperty('for', 'valueList' + i);

						self.valuesList.adopt(input, label, br);
					})

					self.valuesList.removeClass('hidden');
				}
				else if(!standard && response.value.length == 1) self.nonStdValue = response.value;
				else if(response.value == 'Duration' && self.options.lite == false){
					self.dateTime.removeClass('hidden');
					document.id('timeHolder').removeClass('hidden');
				}
				else if(response.value == 'Date' && self.options.lite == false)
				{
					self.calendarHolder.removeClass('hidden');
					self.dateTime.removeClass('hidden');
					document.id('timeHolder').removeClass('hidden');
				}
				else if(response.value == 'Enum') self.dataType = 'enum';

				self.schema_attr = response.schema;
			}
		});

		this.jsonType = new Request.JSON({
			url : self.options.BASE_URL + 'type',
			onRequest : function(){
				document.id('type_descr').empty().addClass('loader-bg-small');
				self.warning.empty();
				self.schema_type = '';
				self.schema_attr = '';
			},
			onSuccess : function(response){
				document.id('type_descr').removeClass('loader-bg-small').set('html', response.descr);
				self.schema_type = response.schema;
			}

		});
	},

	initProperties : function()
	{
		this.calendarHolder = document.id('calendarHolder');
		this.dateTime 		= document.id('dateTime');
//		this.metaPropr		= document.id('metaProp');
		this.valuesChoose	= document.id('values_choose');
		this.valuesDescr	= document.id('values_descr');
		this.valuesList		= document.id('values_list');
		this.warning		= document.id('warning');
	},

	initType : function()
	{
		var self = this;

		this.type = new Mif.Tree({
			container: this.options.type_container,
			forest: true,
			types: {
				folder: {
					openIcon: 'mif-tree-open-icon',
					closeIcon: 'mif-tree-close-icon'
				},
				loader: {
					openIcon: 'mif-tree-loader-open-icon',
					closeIcon: 'mif-tree-loader-close-icon',
					dropDenied: ['inside','after']
				}
			},
			dfltType: 'folder',
			height: 18
		});

		if(typeof(this.type.load) !== 'function')
		{
			alert('Type tree is not ready yet, please refresh the page');
			return;
		}

		this.type.addEvent('select', function(node){
			if(self.loadingAttr)
			{
				alert('Please wait until the attrib tree loading completes');
				self.loadingAttr = false;
				self.type.select(self.fromType);
				return;
			}

			self.loadingAttr = true;

			self.attrib.del();
			self.options.attrib_container.addClass('loader-bg-small');
			self.attrib.load();
			self.getTypeDescr(node);
		});

		new Request.JSON({
			url : self.options.BASE_URL + 'types',
			onSuccess : function(response){
				self.type.load({
					json: response
					});
			}

		}).get({'ty_parent' : ''});
	},

	initAttrib : function()
	{
		var self = this;
		this.attrib = new Mif.Tree(
				{
					container: this.options.attrib_container,
					forest: true,
					types: {
						folder: {
							openIcon: 'mif-tree-open-icon',
							closeIcon: 'mif-tree-close-icon'
						}
					},
					dfltType: 'folder',
					height: 18
				});

		if(typeof(this.attrib.load) !== 'function')
		{
			alert('Attrib tree is not ready yet, please refresh the page');
			return;
		}

		this.attrib.load({json:[{property: {name: 'root'}}]});
		this.attrib.loadOptions = function(node){
			return {
				url: self.options.BASE_URL + 'attributes&id=' + self.type.getSelected().name
			};
		};
		this.attrib.addEvent('loadChildren', function(){self.options.attrib_container.removeClass('loader-bg-small');})
		this.attrib.addEvent('select', function(node){
			if(!node.getParent().getParent()) return;
			self.getAttribDescr(node);
		});
		this.attrib.addEvent('load', function(){
			self.loadingAttr = false;
		});
	},

	getSelectedText : function (input)
	{
		var startPos = input.selectionStart;
		var endPos   = input.selectionEnd;
		var doc 	 = document.selection;

		if(doc && doc.createRange().text.length != 0)	return doc.createRange().text;
		else if (!doc && input.value.substring(startPos,endPos).length != 0)
		{
			return input.value.substring(startPos,endPos);
		}
	},

	appendText : function(element, text)
	{
		var holder = new Element('div').set('html', text);
		element.inject(holder, 'top');

		return holder.get('html');
	},

	displayWarning : function(type)
	{
		if(type == 'type') 	var message = 'Please choose a type';
		else				var message = 'Please choose an attribute';

		this.warning.set('html', message);
		this.warning.highlight();
	},

	addTypeSchema : function()
	{
		if(this.schema_type == ''){
			this.displayWarning('type');
			return;
		}
		var text   = this.selectedText ? this.selectedText : this.options.html_code.value;

		var holder = new Element('div').set('html', text);

		if(document.id('property').checked && holder.getFirst()) 	var html = holder.getFirst();
		else{
			if(document.id('newDiv').checked) var element = 'div';
			else					var element = 'span';

			var html = new Element(element).set('html', text);
		}

		html.setProperty('itemtype', this.schema_type).setProperty('itemscope', '');
		var new_html = new Element('div').adopt(html.clone()).get('html');

		if(this.options.lite)
		{
			//get previous value
			var result = new Element('div').set('html', new_html).getFirst().get('html');
			var wrapper = new Element('div').adopt(html.clone()).getFirst();

			if(!result.test('itemscope')) result += 'itemscope';
			if(wrapper.getProperty('itemtype'))
			{
				if(result.test('itemtype'))	result = result.replace(/itemtype\=".*"/, 'itemtype="' + wrapper.getProperty('itemtype')+'"');
				else						result += ' itemtype="' + wrapper.getProperty('itemtype')+'"'
			}

			if(wrapper.getProperty('itemprop'))
			{
				if(result.test('itemprop'))	result = result.replace(/itemprop\=".*"/, 'itemprop="' + wrapper.getProperty('itemprop')+'"');
				else						result += ' itemprop="' + wrapper.getProperty('itemprop')+'"'
			}

			this.options.html_code.value = result;
		}
		else
		{
			this.options.html_code.value = this.options.html_code.value.replace(text, new_html).replace(/\sid=""/ig, '');
		}

		this.options.html_code.highlight();
	},

	addAttribSchema : function()
	{
		if(this.schema_attr == ''){
			this.displayWarning('attrib');
			return;
		}

		var element = '';
		var text    = this.selectedText ? this.selectedText : this.options.html_code.value;
		var holder  = new Element('div').set('html', text);

		if(this.dateTime.isVisible())
		{
			var timeISO = '';

			if(holder.getFirst())	var timeHtml = holder.getFirst().get('html');
			else					var timeHtml = holder.get('html');

			if(this.calendarHolder.isVisible()){
				if(document.id('calendar').value.test(/^(\d{4})\-(\d{2})\-(\d{2})+$/)) timeISO  = document.id('calendar').value;
				else{
					alert('Date format must be YYYY-MM-DD');
					return;
				}
			}

			if(document.id('timeHolder').isVisible())	timeISO += document.id('calendarTime').value.timeToISO(this.schema_attr);

			if(this.metaPropr.checked)
				var html = this.appendText(new Element('meta').setProperty('datetime', timeISO), text);
			else
			{
				var html = new Element('time').setProperty('datetime', timeISO).set('html', timeHtml);
				html.setProperty('itemprop', this.schema_attr);
			}
		}
		else if(this.dataType == 'enum')
		{
			var itemprop = this.schema_type.replace('https://schema.org/', '');
			if(!this.metaPropr.checked){
					var html = this.appendText(new Element('link').setProperty('itemprop', itemprop).setProperty('href', 'https://schema.org/'+this.schema_attr), text);}
			else	var html = this.appendText(new Element('meta').setProperty('itemprop', itemprop).setProperty('href', 'https://schema.org/'+this.schema_attr), text)
		}
		else
		{	//i try to add the attribute to the current tag (if any)
			if(document.id('property').checked && holder.getFirst()) 	var html = holder.getFirst();
			else
			{
				if(document.id('newDiv').checked) var element = 'div';
				else					var element = 'span';

				var html = new Element(element).set('html', text);
			}
			//property can have more than one value to choose
			if(this.nonStdValue != '' && document.id('proprPlusType').checked)
			{
				var values_list = $$('input[name="valueList"]');
				var attrib_mode;
				if(values_list.length > 1)
				{
					values_list.each(function(item){
						if(item.checked) attrib_mode = item.value;
					});
				}
				else attrib_mode = 'https://schema.org/' + this.nonStdValue;

				html.setProperty('itemtype', attrib_mode).setProperty('itemscope', '');
			}

			//we add the itempropr property at the end, so it'll be the first one in HTML code
			html.setProperty('itemprop', this.schema_attr);
		}

		if(typeof html == 'object')	var new_html = new Element('div').adopt(html.clone()).get('html');
		else						var new_html = html;

		if(this.options.lite)
		{
			if(typeof html != 'object') html = new Element('div').set('html', html).getFirst();

			//get previous value
			var result = new Element('div').set('html', new_html).getFirst().get('html');
			var wrapper = new Element('div').adopt(html.clone()).getFirst();

			if(wrapper.getProperty('itemscope') === '' && !result.test('itemscope')) result += ' itemscope';

			if(wrapper.getProperty('itemtype'))
			{
				if(result.test('itemtype'))	result = result.replace(/itemtype\=".*"/, 'itemtype="' + wrapper.getProperty('itemtype')+'"');
				else						result += ' itemtype="' + wrapper.getProperty('itemtype')+'"'
			}

			if(wrapper.getProperty('itemprop'))
			{
				if(result.test('itemprop'))	result = result.replace(/itemprop\=".*"/, 'itemprop="' + wrapper.getProperty('itemprop')+'"');
				else						result += ' itemprop="' + wrapper.getProperty('itemprop')+'"'
			}

			this.options.html_code.value = result;
		}
		else
		{
			this.options.html_code.value = this.options.html_code.value.replace(text, new_html).replace(/\sid=""/ig, '');
		}

		this.options.html_code.highlight();
	},

	removeSchemas : function()
	{
		if(!confirm('Do you really want do remove every schema.org microdata?')) return;

		var cur_html = this.options.html_code.value.toString();
		this.options.html_code.value = cur_html.replace(/itemscope=".*"|itemscope|itempropr=".*"|itemtype=".*"/ig, '');
	},

	setEvents : function()
	{
		var self = this;

		this.options.add_attrib.addEvent('click', function(){self.addAttribSchema()});
		this.options.add_type.addEvent('click', function(){self.addTypeSchema()});

		document.id('wrap').addEvent('click', function(){document.id('newElement').removeClass('hidden')});
		document.id('property').addEvent('click', function(){document.id('newElement').addClass('hidden')});

		if(document.id('toggleEditor')){
			document.id('toggleEditor').addEvent('click', function(){

				var editorFx = new Fx.Tween('html_code', {duration : 'short'});

				if(document.id('j4sSettings').hasClass('hidden'))
				{
					editorFx.start('height', '50px').chain(
							function(){document.id('j4sSettings').removeClass('hidden');}
						);

					this.set('html', 'Expand editor');
				}
				else
				{
					document.id('j4sSettings').addClass('hidden');
					editorFx.start('height', '430px');

					this.set('html', 'Expand settings');
				}
			});
		}

		//enable these events only if we're on the extended view
		if(this.options.lite == false){
			this.options.paste_button.addEvent('click', function(){J4SDialog.insert()});
			document.id('remove_schemas').addEvent('click', function(){self.removeSchemas()});

			//mouse selection
			this.options.html_code.addEvent('mouseup', function(){
				self.selectedText = self.getSelectedText(this);

				if(self.selectedText != '' && typeof self.selectedText != "undefined"){
					var text = self.selectedText;
					if(text.length > 340) text = text.substring(0, 340) + '...(continue)...';
					document.id('currSelection').set('html', htmlentities(text));}
				else{
					document.id('currSelection').set('html', '&nbsp;');}
			});

			this.options.html_code.addEvent('keyup', function(event){
				if(!event.shift && (event.code < 33 || event.code > 40))return;
				self.pasteText(this);
			});
		}
	},

	pasteText : function(input){
		this.selectedText = this.getSelectedText(input);

		if(this.selectedText != '' && typeof this.selectedText != "undefined"){
			var text = this.selectedText;
			if(text.length > 340) text = text.substring(0, 340) + '...(continue)...';
			document.id('currSelection').set('html', htmlentities(text));}
		else{
			document.id('currSelection').set('html', '&nbsp;');}
	},

	getAttribDescr 	: function(node){ this.jsonAttrib.get({'id_attributes' : node.name, 'task' : 'getDescr', 'layout' : 'default_descr'})},
	getTypeDescr 	: function(node){ this.jsonType.get({'id_types' : node.name, 'task' : 'getDescr', 'layout' : 'default_descr'})}
});

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