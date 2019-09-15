sap.ui.define([], function () {
	"use strict";
	return {

		getImage: function (sUrl, sCategory) {
			if(sUrl.length > 0) {
				return sUrl;
			} else {
				return "/img/" + sCategory + ".png";
			}
		},

		getCategory: function (sCategory) {
			return this.getOwnerComponent().getModel("i18n").getResourceBundle().getText(sCategory);
		}

	};
});