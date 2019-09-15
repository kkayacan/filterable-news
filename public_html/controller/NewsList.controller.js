sap.ui.define([
	"sap/ui/core/mvc/Controller",
	"sap/ui/model/json/JSONModel",
	"../model/formatter"
], function (Controller, JSONModel, formatter) {
	"use strict";
	return Controller.extend("fn.controller.NewsList", {
		formatter: formatter,
		onInit : function () {
			sap.ui.core.BusyIndicator.show();
		},

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
			sap.ui.core.BusyIndicator.hide();
		}
	});
});