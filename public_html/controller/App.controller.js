sap.ui.define([
    "sap/ui/core/mvc/Controller"
 ], function (Controller) {
    "use strict";
    return Controller.extend("fn.controller.App", {
      onAfterRendering: function() {
			var aPanels = sap.ui.getCore().byFieldGroupId("panelHeader");
			for(var i =0; i < aPanels.length; i++) 
			{
			  aPanels[i].attachBrowserEvent("mousedown", function() {
				if(typeof this.getParent().setExpanded === 'function'){
					this.getParent().setExpanded(!this.getParent().getExpanded());
				}
			});
			}
		},

		onScrollToTop: function() {
			this.byId("homePage").scrollTo(0,1000);
		}
    });
 });