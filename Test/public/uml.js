class Sequence{
	constructor($container, xml){
		this.$xml = $(xml);
		this.$container = $container;
		this.components = new Array();
		
		this.setComponents();
		this.draw();
	}
	
	draw(){
		this.$container.addClass("sequence");
		this.drawComponents();
		this.drawLifeLines();
	}
	
	drawComponents(){
		var $components = $("<div>");
		$components.addClass("components");
		
		var i = 0;
		while (i < this.components.length) {
			var $component = $("<div>");
			$component.addClass("component");
			$component.html(this.components[i]);
			
			$component.appendTo($components);
			i++;
		}
		
		$components.appendTo(this.$container);
	}
	
	drawLifeLines(){
		var $lifelines = $("<div>");
		$lifelines.addClass("lifelines");
		
		var i = 0;
		while (i < this.components.length) {
			var $lifeline = $("<div>");
			$lifeline.addClass("lifeline");
			
			$lifeline.appendTo($lifelines);
			i++;
		}
		
		$lifelines.appendTo(this.$container);
	}
	
	setComponents(){
		var components = this.components;
		
		components.push("client");
	
		this.$xml.find("sequence request host").each(function(i){
			var host = this.innerHTML;
		
			if (components.indexOf(host) == -1) {
				components.push(host);
			}
		});
	}
}