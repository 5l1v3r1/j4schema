chooseElement = new Class({
	Implements	: [Events, Options],
	attrib 		: {},
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
	//elements
	dateTime	: '', calendarHolder : '', metaPropr : '', valuesList : '', valuesChoose : '', valuesDescr : '',
	warning		: '',
	options 	: {
		BASE_URL  : 'index.php?option=com_j4schema&c=ajax&format=raw&task=',
		DATATYPES : ['text', 'number', 'date', 'duration', 'integer', 'url', 'enum'],
		editor : '',
		mode : '',
		type_container : '',
		attrib_container : '',
		html_code : '',
		paste_button : '',
		add_type : '',
		add_attrib : ''
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
			url		: self.options.BASE_URL + 'getDescr',
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
							value : 'http://schema.org/' + item,
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
				else if(response.value == 'Duration'){
					self.dateTime.removeClass('hidden');
					document.id('timeHolder').removeClass('hidden');
				}
				else if(response.value == 'Date')
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
			url : self.options.BASE_URL + 'getDescr',
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
		
		this.type.addEvent('select', function(node){
			self.attrib.del();
			self.options.attrib_container.addClass('loader-bg-small');
			self.attrib.load();
			self.getTypeDescr(node);
		});
		
		new Request.JSON({
			url : self.options.BASE_URL + 'getTypes',
			onSuccess : function(response){
				self.type.load({
					json: response
					});
			}
		
		}).post();
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
		
		this.attrib.load({json:[{property: {name: 'root'}}]});
		this.attrib.loadOptions = function(node){
			return {
				url: self.options.BASE_URL + 'getAttrib&type=' + self.type.getSelected().name
			};
		};
		this.attrib.addEvent('loadChildren', function(){self.options.attrib_container.removeClass('loader-bg-small');})
		this.attrib.addEvent('select', function(node){
			if(!node.getParent().getParent()) return;
			self.getAttribDescr(node);
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
		
		this.options.html_code.value = this.options.html_code.value.replace(text, new_html).replace(/\sid=""/ig, '');
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
			var itemprop = this.schema_type.replace('http://schema.org/', '');
			if(!this.metaPropr.checked){
					var html = this.appendText(new Element('link').setProperty('itemprop', itemprop).setProperty('href', 'http://schema.org/'+this.schema_attr), text);}
			else	var html = this.appendText(new Element('meta').setProperty('itemprop', itemprop).setProperty('href', 'http://schema.org/'+this.schema_attr), text)
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
				else attrib_mode = 'http://schema.org/' + this.nonStdValue;

				html.setProperty('itemtype', attrib_mode).setProperty('itemscope', '');
			}
			
			//we add the itempropr property at the end, so it'll be the first one in HTML code
			html.setProperty('itemprop', this.schema_attr);
		}		
		
		if(typeof html == 'object')	var new_html = new Element('div').adopt(html.clone()).get('html');
		else						var new_html = html;						

		this.options.html_code.value = this.options.html_code.value.replace(text, new_html).replace(/\sid=""/ig, '');
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
		
		this.options.paste_button.addEvent('click', function(){J4SDialog.insert()});
		this.options.add_attrib.addEvent('click', function(){self.addAttribSchema()});
		this.options.add_type.addEvent('click', function(){self.addTypeSchema()});
		
		document.id('remove_schemas').addEvent('click', function(){self.removeSchemas()});
		document.id('wrap').addEvent('click', function(){document.id('newElement').removeClass('hidden')});
		document.id('property').addEvent('click', function(){document.id('newElement').addClass('hidden')});
		
		document.id('toggleEditor').addEvent('click', function(){
			
			var editorFx = new Fx.Tween('html_code', {duration : 'short'});

			if(document.id('j4sSettings').hasClass('hidden'))
			{			
				editorFx.start('height', '40px').chain(
						function(){document.id('j4sSettings').removeClass('hidden');}
					);
				
				this.set('html', 'Expand editor');
			}
			else
			{	
				document.id('j4sSettings').addClass('hidden');
				editorFx.start('height', '440px');
				
				this.set('html', 'Expand settings');
			}
		});
		
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
		
	getAttribDescr 	: function(node){ this.jsonAttrib.post({'item' : node.name, 'type' : 'attrib'})},
	getTypeDescr 	: function(node){ this.jsonType.post({'item' : node.name, 'type' : 'type'})}
});