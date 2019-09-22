sap.ui.define([
	"sap/ui/core/mvc/Controller",
	"sap/ui/model/json/JSONModel",
	"sap/ui/core/routing/HashChanger",
	"../model/formatter"
], function (Controller, JSONModel, HashChanger, formatter) {
	"use strict";
	return Controller.extend("fn.controller.NewsList", {
		formatter: formatter,
		onInit : function () {
			var that = this;
			this.getOwnerComponent().getModel("news").attachRequestCompleted(function() {
				sap.ui.core.BusyIndicator.hide();
			});
			var oHashChanger = HashChanger.getInstance();
			oHashChanger.init();
			oHashChanger.attachEvent("hashChanged", function(oEvent) {
			  sap.ui.core.BusyIndicator.show();
			  that.getOwnerComponent().getModel("news").loadData("/api/news/report/" + oEvent.getParameter("newHash"));
			});
		},
		
		onHourFilterPress: function(sHours) {
			HashChanger.getInstance().replaceHash("hours/" + sHours);
		}

	});
});