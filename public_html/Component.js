sap.ui.define([
    "sap/ui/core/UIComponent",
    "sap/ui/model/json/JSONModel",
    "sap/ui/core/routing/HashChanger"
 ], function (UIComponent, JSONModel, HashChanger) {
    "use strict";
    return UIComponent.extend("fn.Component", {
       metadata : {
             manifest: "json"
       },
       init : function () {
          // call the init function of the parent
          UIComponent.prototype.init.apply(this, arguments);
          
          sap.ui.core.BusyIndicator.show();

          // set data model
          var oHashChanger = HashChanger.getInstance();
          oHashChanger.init();
          var oModel = new JSONModel("/api/news/report/" + oHashChanger.getHash());
          oModel.attachRequestCompleted(function() {
             sap.ui.core.BusyIndicator.hide();
          });
          this.setModel(oModel, "news");
       }
    });
 });