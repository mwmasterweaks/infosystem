<template>
  <div id="app">
    <login v-if="!isAuth"></login>

    <div v-if="isLoad == 7">
      <!-- <div v-if="!((user.id == 999) || (user.id == 1000))"> -->
      <div v-if="!roles.viewer">
        <my-navbar></my-navbar>
        <navbar2></navbar2>
      </div>

      <div class="container" style>
        <router-view></router-view>
      </div>
    </div>
    <transition name="bounce">
      <div class="elBG divLoaderDiv" v-if="isAuth && isLoad != 7">
        <img src="./img/loading.gif" class="imgLoader" />
        <!-- <div class="loaderCount">
          <b>
            <h4 style="color: black;">Loading {{ loadCount }}%</h4>
          </b>
        </div>-->
      </div>
    </transition>
    <div class="busyPageLoader" v-if="pageBusy">
      <img src="./img/loading.gif" class="imgLoader" />
    </div>
  </div>
</template>

<script>
import Navbar from "./components/Navbar.vue";
import SecondNavBar from "./components/SecondNavBar.vue";
import Login from "./components/user/Login.vue";
import Register from "./components/user/Register.vue";
import swal from "sweetalert";

export default {
  components: {
    "my-navbar": Navbar,
    navbar2: SecondNavBar,
    login: Login,
    register: Register,
  },
  data() {
    return {
      pageBusy: false,
      isAuth: null,
      isLoad: 0,
      loadCount: 0,
      user: [],
      email: "",
    };
  },

  created() {
    this.isAuth = this.$auth.isAuthenticated();
    this.load();
  },
  mounted() {
    this.$root.$on("pageLoading", () => {
      //console.log("pageLoading");
      this.pageBusy = true;
    });
    this.$root.$on("pageLoaded", () => {
      //console.log("pageLoaded");
      this.pageBusy = false;
    });
  },
  methods: {
    logout() {
      this.$global.destroyGlobal();
      this.$auth.destroyToken();
      window.location.href = "/login";
    },
    load() {
      if (this.isAuth) {
        this.getUser();
      }
    },
    getEngineer() {
      console.log("Load Engineer");
      this.$http
        .get("api/Engineer")
        .then(function (response) {
          this.$global.setEngineer(response.body);
          this.isLoad += 1; // 1
          console.log("Engineer loaded");
          this.getSales();
          this.loadCount += 10;
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
    getSales() {
      console.log("Load Sales");
      this.$http
        .get("api/Sales")
        .then(function (response) {
          this.$global.setSales(response.body);
          this.isLoad += 1; // 2
          console.log("Sales loaded");
          this.getPackageType();
          this.loadCount += 10;
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          });
          this.loadCount = "Error";
          this.logout();
        });
    },
    getPackageType() {
      console.log("Load PackageType");
      this.$http
        .get("api/PackageType")
        .then(function (response) {
          this.$global.setPackageTypes(response.body);
          this.isLoad += 1; // 6
          console.log("PackageType loaded");
          this.getTicketStatus();
          this.loadCount += 10;
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
    getTicketStatus() {
      console.log("Load PackageType");
      this.$http
        .get("api/TicketStatus")
        .then(function (response) {
          this.$global.setTicketStatus(response.body);
          console.log("TicketStatus loaded");
          this.getRegion();
          this.isLoad += 1; // 7
          this.loadCount += 15;
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
    getRegion() {
      console.log("Load Region");
      this.$http
        .get("api/Region")
        .then(function (response) {
          this.$global.setRegion(response.body);
          this.isLoad += 1; // 9
          this.loadCount += 20;
          console.log("region loaded");
          this.getTeam();
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
    getTeam() {
      console.log("Load team");
      this.$http
        .get("api/team")
        .then(function (response) {
          this.$global.setTeam(response.body);
          this.isLoad += 1; // 9
          this.loadCount += 15;
          console.log("team loaded");
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
    getUser() {
      this.email = this.$global.getEmail();
      this.$http.get("api/user/getUser/" + this.email).then((response) => {
        this.$global.setUser(response.body);
        this.user = this.$global.getUser();
        if (response.body.status == "Active") {
          this.getUserRoles();
          this.loadCount += 20;
        } else {
          swal({
            title: "Your account was locked!",
            text: "Please contact the system administrator.",
            icon: "info",
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        }
      });
    },
    getUserRoles() {
      console.log("Load role");
      this.$http
        .get("api/Role/" + this.user.id)
        .then((response) => {
          this.$global.setRoles(response.body.roles);
          // this.loadCount += 20;
          console.log("role loaded");
          this.getEngineer();
          this.roles = this.$global.getRoles();
          this.isLoad += 1; // 11
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          }).then((value) => {
            this.loadCount = "Error";
            this.logout();
          });
        });
    },
  },
};
</script>

<style>
.displayColor {
  height: 15px;
  width: 15px;
  margin-top: 2px;
  margin-right: 8px;
  border: 1px solid black;
}
body {
  background-color: #edebeb;
  color: white;
  font-size: 11px;
  line-height: 1;
}
.btn {
  font-size: 11px;
  padding: 3px;
}
.ui.selection.dropdown .menu > .item {
  font-size: 11px;
  padding: 5px;
}
.form-control {
  padding: 3px;
  height: 2.2em;
  font-size: 11px;
}
input {
  font-size: 11px;
  padding: 3px;
  height: 2.2em;
}
.menu-heading {
  font-size: 11px;
}
.dropdown-menu {
  font-size: 11px;
}

.table td,
.table th {
  padding: 0.4rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}

.textColor {
  color: aliceblue;
}
.ml-auto .dropdown-menu {
  left: auto !important;
  right: 0px;
}
.iconSize {
  font-size: 18px;
  width: 18px;
  height: 18px;
}
.panel {
  margin-top: 30px;
  margin-bottom: 20px;
  border-color: #ddd;
  border: 1px solid #ddd;
  border-top-right-radius: 6px;
  border-top-left-radius: 6px;
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 6px;
}
.panel.has-scroll {
  max-width: 100%;
  overflow-x: auto;
}
.panel-heading {
  position: relative;
  padding: 15px;
  height: auto;
  border-radius: 8px 8px 0px 0px;
  border-bottom: 1px solid #eeeeee;

  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0), inset 0 1px rgb(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
}
.panel-body {
  position: relative;
  padding: 10px;
  padding-top: 20px;
  padding-bottom: 20px;

  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  /* background: linear-gradient(#eeefef, #ffffff 50%); */
}
.panel-footer {
  position: relative;
  padding: 10px;
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 6px;
  border-top: 1px solid #eeeeee;

  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  /* background: linear-gradient(#eeefef, #ffffff 80%); */
}
.panel-title {
  position: relative;
  font-size: 17px;
}

.panel > .panel-heading {
  padding-top: 20px;
  padding-bottom: 20px;
}
.panel > .panel-heading > .panel-title {
  margin-top: 2px;
  margin-bottom: 2px;
}
.heading-elements {
  background-color: inherit;
  position: absolute;
  right: 20px;
  height: 36px;
  margin-top: -18px;
}
@media (max-width: 768px) {
  .heading-elements:not(.not-collapsible) {
    position: static;
    margin-top: 0;
    height: auto;
  }
}
.panel-footer > .heading-elements {
  position: static;
  margin-top: 0;
  padding-right: 20px;
}
.panel-footer > .heading-elements:after {
  content: "";
  display: table;
  clear: both;
}
.centerElem {
  margin: 0;
  position: relative;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
#printSec {
  margin: 0;
  position: relative;
  left: 50%;
  transform: translate(-50%, 0%);
}
.cursorPointer {
  cursor: pointer;
}
.tblheadtrclass > tr > th,
.bodyClass > tr > td {
  text-align: center;
}

.cursorPointer-th > tr > th {
  cursor: pointer;
}
.cursorPointer-th > tr > th:hover {
  text-decoration: none;
  background-color: #f5f5f5;
  color: #2196f3;
}
.imgLoader {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.loaderCount {
  margin: 0;
  position: absolute;
  top: 60%;
  left: 50%;
  transform: translate(-60%, -50%);
}
.btn1 {
  background-color: #f27474;
}
.bgOrange {
  background-color: orange;
}
.divLoader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 5000;
}
.divLoaderDiv {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 5000;
}
.busyPageLoader {
  background-color: rgba(192, 192, 192, 0.3);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 5000;
}
.scrollmenu {
  overflow: auto;
  white-space: nowrap;
  padding: 10px;
}
.borderbot-1 {
  border-bottom: solid black 1px;
}
.bordertop-2 {
  border-top: solid black 2px;
}
.borderleft-2 {
  border-left: solid black 2px;
}

.modified-continer {
  position: absolute;
  left: 0px;
}

.modified-row {
  position: absolute;
  width: 80%;
  left: 50%;
  transform: translate(-50%);
}

.marginice {
  margin: 15px;
  padding: 8px;
}
.checkboxStyle {
  margin-top: 4px;
  margin-right: 25px;
  margin-left: 25px;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.bounce-enter-active {
  animation: bounce-in 1s;
}
.bounce-leave-active {
  animation: bounce-in 1s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(2);
  }
  100% {
    transform: scale(1);
  }
}

.bd_style1 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#eeefef, #eeefef 10%);
}

.bd_style2 {
  box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1);
  background: linear-gradient(#ffffff, #eeefef 80%);
}

.bd_style3 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#eeefef, #ffffff 80%);
}

.bd_style4 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#ffffff, #eeefef 95%);
}

.margin-right-10 {
  margin-right: 10px;
}
.margin-left-10 {
  margin-left: 10px;
}
.margin-bottom-10 {
  margin-bottom: 10px;
}
.textLabel {
  margin-bottom: 1px;
  margin-top: 7px;
  font-size: 12px;
}
.rowFields {
  margin: 2px;
}
.col-1,
.col-2,
.col-3,
.col-4,
.col-5,
.col-6,
.col-7,
.col-8,
.col-9,
.col-10,
.col-11,
.col-12,
.col,
.col-auto,
.col-sm-1,
.col-sm-2,
.col-sm-3,
.col-sm-4,
.col-sm-5,
.col-sm-6,
.col-sm-7,
.col-sm-8,
.col-sm-9,
.col-sm-10,
.col-sm-11,
.col-sm-12,
.col-sm,
.col-sm-auto,
.col-md-1,
.col-md-2,
.col-md-3,
.col-md-4,
.col-md-5,
.col-md-6,
.col-md-7,
.col-md-8,
.col-md-9,
.col-md-10,
.col-md-11,
.col-md-12,
.col-md,
.col-md-auto,
.col-lg-1,
.col-lg-2,
.col-lg-3,
.col-lg-4,
.col-lg-5,
.col-lg-6,
.col-lg-7,
.col-lg-8,
.col-lg-9,
.col-lg-10,
.col-lg-11,
.col-lg-12,
.col-lg,
.col-lg-auto,
.col-xl-1,
.col-xl-2,
.col-xl-3,
.col-xl-4,
.col-xl-5,
.col-xl-6,
.col-xl-7,
.col-xl-8,
.col-xl-9,
.col-xl-10,
.col-xl-11,
.col-xl-12,
.col-xl,
.col-xl-auto {
  padding: 5px;
}
.textLabel1 {
  font-size: 12px;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
}

.tbl-display > tbody > tr > td {
  font-size: 12px;
  padding: 3px;
}

.tbl-display > thead > tr > th,
.tbl-display > tbody > tr > td {
  width: 20%;
}

.dateRangePicker > div {
  color: black;
  background-color: white;
}

html {
  max-width: 100%;
  overflow-x: hidden;
}

.multiselectemail
  > .multiselect
  > .multiselect__tags
  > .multiselect__tags-wrap
  > .multiselect__tag {
  background: #15a4bb;
}
.multiselectemail
  > .multiselect
  > .multiselect__content-wrapper
  > .multiselect__content
  > .multiselect__element
  > .multiselect__option--highlight {
  background: #15a4bb;
}
.treeview_icon {
  width: 20px;
  height: 25px;
}

@media screen {
  #printSection {
    display: none;
  }
}

@media print {
  body * {
    visibility: hidden;
  }
  #printSection,
  #printSection * {
    visibility: visible;
  }
  #printSection {
    position: absolute;
    left: 0;
    top: 0;
  }
  .bottomfet {
    color: white !important;
    text-align: center;
    background-color: rgba(0, 128, 0, 0.829) !important;
    -webkit-print-color-adjust: exact;
    font-size: 12px;
    padding: 3px;
  }
  .modal-modi-bg {
    background-color: silver !important;
  }

  /* @page {
    size: 8.5in 5.5in;
    size: landscape;
  } */
}
</style>
