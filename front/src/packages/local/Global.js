export default function(Vue) {
  Vue.global = {
    setPackages(param1) {
      localStorage.setItem("_packages", JSON.stringify(param1));
    },
    getPackages() {
      return JSON.parse(localStorage.getItem("_packages"));
    },
    //--------------------------------------------------------------
    setPackageTypes(param1) {
      localStorage.setItem("_PackageTypes", JSON.stringify(param1));
    },
    getPackageTypes() {
      return JSON.parse(localStorage.getItem("_PackageTypes"));
    },
    //--------------------------------------------------------------

    setSales(param1) {
      localStorage.setItem("_Sales", JSON.stringify(param1));
    },
    getSales() {
      return JSON.parse(localStorage.getItem("_Sales"));
    },
    //--------------------------------------------------------------
    setRoles(param1) {
      localStorage.setItem("_Roles", JSON.stringify(param1));
    },
    getRoles() {
      return JSON.parse(localStorage.getItem("_Roles"));
    },
    //--------------------------------------------------------------
    setEngineer(param1) {
      localStorage.setItem("_Engineer", JSON.stringify(param1));
    },
    getEngineer() {
      return JSON.parse(localStorage.getItem("_Engineer"));
    },
    //--------------------------------------------------------------
    setRegion(param1) {
      localStorage.setItem("_Region", JSON.stringify(param1));
    },
    getRegion() {
      return JSON.parse(localStorage.getItem("_Region"));
    },
    //--------------------------------------------------------------
    setUser(param1) {
      localStorage.setItem("_User", JSON.stringify(param1));
    },
    getUser() {
      return JSON.parse(localStorage.getItem("_User"));
    },
    //--------------------------------------------------------------
    setEmail(param1) {
      localStorage.setItem("_email", JSON.stringify(param1));
    },
    getEmail() {
      return JSON.parse(localStorage.getItem("_email"));
    },
    //--------------------------------------------------------------
    setSchedule(param1) {
      localStorage.setItem("_Schedule", JSON.stringify(param1));
    },
    getSchedule() {
      return JSON.parse(localStorage.getItem("_Schedule"));
    },
    //--------------------------------------------------------------
    setTicketStatus(param1) {
      localStorage.setItem("_TicketStatus", JSON.stringify(param1));
    },
    getTicketStatus() {
      return JSON.parse(localStorage.getItem("_TicketStatus"));
    },
    //--------------------------------------------------------------
    setTicket(param1) {
      localStorage.setItem("_Ticket", JSON.stringify(param1));
    },
    getTicket() {
      return JSON.parse(localStorage.getItem("_Ticket"));
    },
    //--------------------------------------------------------------
    setOLT(param1) {
      localStorage.setItem("_OLT", JSON.stringify(param1));
    },
    getOLT() {
      return JSON.parse(localStorage.getItem("_OLT"));
    },
    //--------------------------------------------------------------
    setPON(param1) {
      localStorage.setItem("_PON", JSON.stringify(param1));
    },
    getPON() {
      return JSON.parse(localStorage.getItem("_PON"));
    },
    //--------------------------------------------------------------
    setEvent(param1) {
      localStorage.setItem("_event", JSON.stringify(param1));
    },
    getEvent() {
      return JSON.parse(localStorage.getItem("_event"));
    },
    //--------------------------------------------------------------
    setTeam(param1) {
      localStorage.setItem("_team", JSON.stringify(param1));
    },
    getTeam() {
      return JSON.parse(localStorage.getItem("_team"));
    },
    //--------------------------------------------------------------
    setSoaEmail(param1) {
      localStorage.setItem("_soaEmail", JSON.stringify(param1));
    },
    getSoaEmail() {
      return JSON.parse(localStorage.getItem("_soaEmail"));
    },
    //--------------------------------------------------------------
    setSoaEmailPass(param1) {
      localStorage.setItem("_soaEmailPass", JSON.stringify(param1));
    },
    getSoaEmailPass() {
      return JSON.parse(localStorage.getItem("_soaEmailPass"));
    },
    //--------------------------------------------------------------
    set(param1) {
      localStorage.setItem("_", JSON.stringify(param1));
    },
    get() {
      return JSON.parse(localStorage.getItem("_"));
    },
    //--------------------------------------------------------------
    destroyGlobal() {
      localStorage.removeItem("_packages");
      localStorage.removeItem("_Modems");
      localStorage.removeItem("_PackageTypes");
      localStorage.removeItem("_Sales");
      localStorage.removeItem("_Roles");
      localStorage.removeItem("_Engineer");
      localStorage.removeItem("_Region");
      localStorage.removeItem("_User");
      localStorage.removeItem("_email");
      localStorage.removeItem("_Schedule");
      localStorage.removeItem("_TicketStatus");
      localStorage.removeItem("_Ticket");
      localStorage.removeItem("_OLT");
      localStorage.removeItem("_PON");
      localStorage.removeItem("_event");
      localStorage.removeItem("_color");
      localStorage.removeItem("_bgColor");
      localStorage.removeItem("_team");
      localStorage.removeItem("_soaEmail");
      localStorage.removeItem("_soaEmailPass");
      localStorage.removeItem("_");
    },
    elClr(color) {
      var sheet = window.document.styleSheets[0];
      sheet.insertRule(
        ".elClr { color: " + color + "; }",
        sheet.cssRules.length
      );
      localStorage.setItem("_color", JSON.stringify(color));
    },
    getColor() {
      return JSON.parse(localStorage.getItem("_color"));
    },
    elBG(color) {
      var sheet = window.document.styleSheets[0];
      sheet.insertRule(
        ".elBG { background-color: " + color + "; }",
        sheet.cssRules.length
      );
      localStorage.setItem("_bgColor", JSON.stringify(color));
    },
    img_bg(img, path) {
      document.body.style.backgroundImage = "url('" + path + img + "')";
      document.body.style.backgroundRepeat = "repeat";
      // var sheet = window.document.styleSheets[0];
      // sheet.insertRule(
      //   "body { background-image: url('" +
      //     path +
      //     img +
      //     "'); background-repeat: repeat; }",
      //   sheet.cssRules.length
      // );
      //localStorage.setItem("_bgColor", JSON.stringify(color));
    },
    getbgColor() {
      return JSON.parse(localStorage.getItem("_bgColor"));
    },
    loadJS() {
      //   var plugin = document.createElement("script");
      //   plugin.setAttribute(
      //     "src",
      //     "src/plugins/assets/js/plugins/forms/styling/uniform.min.js"
      //   );
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
      //   plugin = document.createElement("script");
      //   plugin.setAttribute("src", "src/plugins/assets/js/core/app.js");
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
      //   plugin = document.createElement("script");
      //   plugin.setAttribute("src", "src/plugins/assets/js/pages/form_inputs.js");
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
    }
  };

  Object.defineProperties(Vue.prototype, {
    $global: {
      get() {
        return Vue.global;
      }
    }
  });
}
