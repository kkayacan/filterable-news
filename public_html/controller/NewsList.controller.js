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
			sap.ui.core.BusyIndicator.hide();
		}
	});
});