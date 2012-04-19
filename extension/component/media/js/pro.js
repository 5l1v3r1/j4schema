/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 */
Mif.Tree.implement({
	customExpandTo: function(target)
	{
		//close every node & expand to the correct one (selecting it)
		var stop = false
		var self = this;
		
		this.root.recursive(function(){this.toggle(false)})
		this.root.recursive(function(){
			if(stop == true) return;
			if(this.name == target)
			{
				self.expandTo(this);
				self.select(this);
				stop = true;
			}
		});
	}
});

window.addEvent('domready', function(){
	document.id('values_descr').addEvent('click:relay(.expandToType)', function(event, clicked){	
		J4Stree.type.customExpandTo(clicked.get('html'));
	});
});