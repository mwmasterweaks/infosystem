<template>
  <div>
    <div class="elBG divLoader" v-if="!doneLoad">
      <img src="../img/loading.gif" class="imgLoader" />
    </div>
    <nav v-else class="elBG navbar navbar-expand-md navbar-light">
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <router-link
            tag="li"
            class="nav-item"
            to="/home"
            @click.native="navbarClick('navbarCalendar')"
            @mouseover.native="
              hoverSpin('dashBoardIcon', 'elClr fas fa-desktop')
            "
            @mouseleave.native="
              hoverStopSpin('dashBoardIcon', 'elClr fas fa-desktop')
            "
          >
            <a id="navbarCalendar" class="nav-link" href="javascript:void(0);">
              <i id="dashBoardIcon" class="elClr fas fa-desktop" style="font-size:18px"></i>
              <span class="elClr">Calendar</span>
            </a>
          </router-link>

          <router-link
            tag="li"
            class="nav-item"
            to="/QueTicket"
            @click.native="navbarClick('navbarTicket')"
            @mouseover.native="
              hoverSpin('ticketIcon', 'elClr fas fa-ticket-alt')
            "
            @mouseleave.native="
              hoverStopSpin('ticketIcon', 'elClr fas fa-ticket-alt')
            "
          >
            <a id="navbarTicket" class="nav-link" href="javascript:void(0);">
              <i id="ticketIcon" class="elClr fas fa-ticket-alt" style="font-size:18px"></i>
              <span class="elClr">CHD Ticket</span>
            </a>
          </router-link>

          <router-link
            tag="li"
            class="nav-item"
            to="/ViewActivityTicket"
            @click.native="navbarClick('navbarActivityTicket')"
            @mouseover.native="
              hoverSpin('activityTicketIcon', 'elClr fas fa-ticket-alt')
            "
            @mouseleave.native="
              hoverStopSpin('activityTicketIcon', 'elClr fas fa-ticket-alt')
            "
          >
            <a id="navbarActivityTicket" class="nav-link" href="javascript:void(0);">
              <i id="activityTicketIcon" class="elClr fas fa-ticket-alt" style="font-size:18px"></i>
              <span class="elClr">Job Order</span>
            </a>
          </router-link>

          <router-link
            tag="li"
            class="nav-item"
            to="/ViewSchedule"
            @click.native="navbarClick('navbarInstallation')"
            @mouseover.native="
              hoverSpin('InstallationIcon', 'elClr fas fa-tools')
            "
            @mouseleave.native="
              hoverStopSpin('InstallationIcon', 'elClr fas fa-tools')
            "
          >
            <a id="navbarInstallation" class="nav-link" href="javascript:void(0);">
              <i id="InstallationIcon" class="elClr fas fas fa-tools" style="font-size:18px"></i>
              <span class="elClr">Installation</span>
            </a>
          </router-link>

          <router-link
            tag="li"
            class="nav-item"
            to="/map"
            @click.native="navbarClick('navbarMap')"
            @mouseover.native="
              hoverSpin('MapIcon', 'elClr fas fa-map-marked-alt')
            "
            @mouseleave.native="
              hoverStopSpin('MapIcon', 'elClr fas fa-map-marked-alt')
            "
          >
            <a id="navbarMap" class="nav-link" href="javascript:void(0);">
              <i id="MapIcon" class="elClr fas fa-map-marked-alt" style="font-size:18px"></i>
              <span class="elClr">Map</span>
            </a>
          </router-link>

          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarComponents"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              @mouseover="
                hoverSpin('componentsIcon', 'elClr fas fa-bezier-curve')
              "
              @mouseleave="
                hoverStopSpin('componentsIcon', 'elClr fas fa-bezier-curve')
              "
              v-on:click="showMenu('componentMenu')"
            >
              <i id="componentsIcon" class="elClr fas fa-bezier-curve" style="font-size:18px"></i>
              <span id="componentLabel" class="elClr">Components</span>
            </a>
          </li>
          <div
            id="componentMenu"
            class="customeDropDown dropdown-menu"
            aria-labelledby="navbarComponents"
            v-click-outside="hideMenu"
          >
            <div class="row" style="padding-left:15px;">
              <div class="col-md-11">
                <ul class="menu-list">
                  <router-link tag="li" to="/ViewPackageType" id="navbarViewPackageType">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Package type</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewPackage" id="navbarViewPackage">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Packages</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewBusinessType" id="navbarViewBusinessType">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Business Type</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewClosureType" id="navbarViewClosureType">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Closure Type</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewSplitterType" id="navbarViewSplitterType">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Splitter Type</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewModem" id="navbarViewModem">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Modem</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewBranch" id="navbarViewBranch">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Branches</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewRegion" id="navbarViewRegion">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Regions</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewArea" id="navbarViewArea">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Areas</b>
                    </a>
                  </router-link>

                  <router-link tag="li" to="/ViewBucket" id="navbarViewBucket" v-if="roles.network">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Buckets</b>
                    </a>
                  </router-link>

                  <!-- <router-link tag="li" to="/ViewNode" id="navbarViewNode">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Nodes</b>
                    </a>
                  </router-link>-->
                  <router-link tag="li" to="/ViewTeam" id="navbarViewTeam">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Team</b>
                    </a>
                  </router-link>

                  <router-link
                    tag="li"
                    to="/MacGenerator"
                    id="navbarMacGenerator"
                    v-if="roles.network"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Mac Generator</b>
                    </a>
                  </router-link>
                </ul>
              </div>
            </div>
          </div>

          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarAccounts"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              @mouseover="hoverSpin('accountsIcon', 'elClr fas fa-users')"
              @mouseleave="hoverStopSpin('accountsIcon', 'elClr fas fa-users')"
              v-on:click="showMenu('accountsMenu')"
            >
              <i id="accountsIcon" class="elClr fas fa-users" style="font-size:18px"></i>
              <span id="accountsLabel" class="elClr">Accounts</span>
            </a>
          </li>
          <div
            id="accountsMenu"
            class="customeDropDown dropdown-menu"
            aria-labelledby="navbarAccounts"
            v-click-outside="hideMenu"
          >
            <div class="row" style="padding-left:15px;">
              <div class="col-md-11">
                <ul class="menu-list">
                  <router-link
                    tag="li"
                    to="/ViewUser"
                    id="navbarViewUser"
                    v-if="roles.create_account"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Users</b>
                    </a>
                  </router-link>
                  <router-link tag="li" to="/ViewClient" id="navbarViewClient">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Accounts</b>
                    </a>
                  </router-link>
                  <router-link tag="li" to="/ViewEngineer" id="navbarViewEngineer">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Tech Sales</b>
                    </a>
                  </router-link>
                  <router-link tag="li" to="/ViewSales" id="navbarViewSales">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Sales</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/ViewSalesAgent"
                    id="navbarViewSalesAgent"
                    v-if="roles.sales"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Sales Agent</b>
                    </a>
                  </router-link>
                </ul>
              </div>
            </div>
          </div>

          <li class="nav-item dropdown" v-if="roles.accounting || roles.create_ticket">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarAccounting"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              @mouseover="
                hoverSpin('accountingIcon', 'elClr fas fa-calculator')
              "
              @mouseleave="
                hoverStopSpin('accountingIcon', 'elClr fas fa-calculator')
              "
              v-on:click="showMenu('accountingMenu')"
            >
              <i id="accountingIcon" class="elClr fas fa-calculator" style="font-size:18px"></i>
              <span id="accountingLabel" class="elClr">Accounting</span>
            </a>
          </li>
          <div
            id="accountingMenu"
            class="customeDropDown dropdown-menu"
            aria-labelledby="navbarAccounting"
            v-click-outside="hideMenu"
          >
            <div class="row" style="padding-left:15px;">
              <div class="col-md-11">
                <ul class="menu-list">
                  <router-link tag="li" to="/SOA" id="navbarSOA">
                    <a href="javascript:void(0);" class="elClr">
                      <b>Statement of Account</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/DailyCollectReport"
                    id="navbarDailyCollectReport"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Daily Collection Report</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/AgingReport"
                    id="navbarAgingReport"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Aging Report</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/WHTReport"
                    id="navbarWHTReport"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>WHT Report</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/RebateReport"
                    id="navbarRebateReport"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Rebates Report</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/CollectEffiReport"
                    id="navbarCollectEffiReport"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Collection Efficiency Report</b>
                    </a>
                  </router-link>

                  <router-link
                    tag="li"
                    to="/Transmittal"
                    id="navbarTransmittal"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Transmittal</b>
                    </a>
                  </router-link>

                  <router-link
                    tag="li"
                    to="/PaymentMethodList"
                    id="navbarPaymentMethodList"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Payment Method List</b>
                    </a>
                  </router-link>
                  <router-link
                    tag="li"
                    to="/Banking_Code"
                    id="navbarBanking_Code"
                    v-if="roles.accounting"
                  >
                    <a href="javascript:void(0);" class="elClr">
                      <b>Online Banking Code</b>
                    </a>
                  </router-link>
                </ul>
              </div>
            </div>
          </div>
        </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarThemes"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i style="font-size:18px" class="elClr fa fa-cog fa-spin"></i>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarThemes">
              <div class style="margin-left: 25px; ">
                <i class="fas fa-dice-d20 fa-spin" style="font-size:18px"></i>
                Themes
              </div>
              <div class="dropdown-divider"></div>

              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="changeTheme('Black', 'White')"
              >Black, White</a>

              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="changeTheme('White', 'Black')"
              >White, Black</a>

              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="changeTheme('Gray', 'White')"
              >Gray, White</a>

              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="changeTheme('Black', 'Red')"
              >Black, Red</a>

              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="changeTheme('Black', 'Green')"
              >Black, Green</a>

              <div class="dropdown-item" href="javascript:void(0);">
                <form @submit.prevent="changeTheme1">
                  <input
                    class="inputTheme"
                    type="text"
                    v-on:keyup.enter="changeTheme1"
                    v-model="bg"
                    placeholder="#00a"
                  />
                  <input
                    class="inputTheme"
                    type="text"
                    v-on:keyup.enter="changeTheme1"
                    v-model="cl"
                    placeholder="#1ff"
                  />
                </form>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>
<script>
import vClickOutside from "v-click-outside";

export default {
  directives: {
    clickOutside: vClickOutside.directive
  },
  data() {
    return {
      roles: [],
      user: [],
      doneLoad: true,
      bg: "black",
      cl: "green"
    };
  },
  beforeCreate() {},
  created() {
    this.user = this.$global.getUser();
    this.$global.elBG(this.user.elBG);
    this.$global.elClr(this.user.elClr);
    if (this.user.img_bg != null)
      this.$global.img_bg(this.user.img_bg, this.$img_path);
    this.bg = this.user.elBG;
    this.cl = this.user.elClr;
    this.roles = this.$global.getRoles();
  },
  mounted() {
    //this.load();
    this.$root.$on("clearNav", item => {
      document.getElementById("navbarCalendar").style.backgroundColor = "";
      document.getElementById("navbarTicket").style.backgroundColor = "";
      document.getElementById("navbarActivityTicket").style.backgroundColor =
        "";
      document.getElementById("navbarInstallation").style.backgroundColor = "";
      document.getElementById("navbarComponents").style.backgroundColor = "";
      document.getElementById("navbarAccounts").style.backgroundColor = "";
      document.getElementById("navbarMap").style.backgroundColor = "";
      if (this.roles.accounting)
        document.getElementById("navbarAccounting").style.backgroundColor = "";
    });
  },
  methods: {
    // load() {
    //   this.$nextTick(function() {
    //     setTimeout(function() {

    //       this.doneLoad = true;
    //     }, 2000);
    //   });
    // },
    changeTheme(bg, cl) {
      this.bg = bg;
      this.cl = cl;
      this.changeTheme1();
    },
    changeTheme1() {
      var theme = {
        elBG: this.bg,
        elClr: this.cl
      };

      this.$http
        .post("api/user/updateTheme/" + this.user.id, theme)
        .then(response => {
          this.$global.elBG(this.bg);
          this.$global.elClr(this.cl);

          var local_user = this.$global.getUser();
          local_user.elBG = this.bg;
          local_user.elClr = this.cl;
          this.$global.setUser(local_user);
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          }).then(value => {
            if (value) {
            }
          });
        });
    },
    hoverSpin(id, cls) {
      document.getElementById(id).className = cls + " fa-spin";
    },
    hoverStopSpin(id, cls) {
      document.getElementById(id).className = cls;
    },
    showMenu(id) {
      document.getElementById(
        id
      ).style.backgroundColor = this.$global.getbgColor();

      if (
        document.getElementById(id).className == "customeDropDown dropdown-menu"
      ) {
        document.getElementById("componentMenu").className =
          "customeDropDown dropdown-menu";
        document.getElementById("accountsMenu").className =
          "customeDropDown dropdown-menu";
        document.getElementById("accountingMenu").className =
          "customeDropDown dropdown-menu";
        document.getElementById(id).className =
          "customeDropDown dropdown-menu show";
        this.$nextTick(function() {
          setTimeout(function() {
            document.getElementById(id).className =
              "customeDropDown dropdown-menu show";
            if (id == "accountsMenu") {
              var rect = document
                .getElementById("accountsLabel")
                .getBoundingClientRect();
              document.getElementById("accountsMenu").style.marginLeft =
                rect.left + "px";
            } else if (id == "componentMenu") {
              var rect = document
                .getElementById("componentLabel")
                .getBoundingClientRect();
              document.getElementById("componentMenu").style.marginLeft =
                rect.left + "px";
            } else if (id == "accountingMenu") {
              var rect = document
                .getElementById("accountingLabel")
                .getBoundingClientRect();
              document.getElementById("accountingMenu").style.marginLeft =
                rect.left + "px";
            }
          }, 100);
        });
      } else
        document.getElementById(id).className = "customeDropDown dropdown-menu";
    },
    hideMenu(e, el) {
      if (
        document.getElementById(el.id).className ==
        "customeDropDown dropdown-menu show"
      ) {
        document.getElementById(el.id).className =
          "customeDropDown dropdown-menu";
      }
    },
    navbarClick(id) {
      document.getElementById("navbarCalendar").style.backgroundColor = "";
      document.getElementById("navbarTicket").style.backgroundColor = "";
      document.getElementById("navbarActivityTicket").style.backgroundColor =
        "";
      document.getElementById("navbarInstallation").style.backgroundColor = "";
      document.getElementById("navbarComponents").style.backgroundColor = "";
      document.getElementById("navbarAccounts").style.backgroundColor = "";
      document.getElementById("navbarMap").style.backgroundColor = "";
      if (this.roles.accounting)
        document.getElementById("navbarAccounting").style.backgroundColor = "";

      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById(id).style.backgroundColor = "#2196f3";
          document.getElementById(id).color = "white";
        }, 100);
      });
    }
  }
};
</script>
<style scoped>
.customeDropDown {
  margin-left: 20px;
  width: 190px;
  border: 1px solid black;
}

.menu-heading {
  display: block;
  font-size: 14px;
  text-transform: uppercase;
  font-weight: 500;
  margin-bottom: 10px;
  padding-top: 4px;
  margin-left: 10px;
}
.menu-heading > i {
  float: left;
  margin-right: 7px;
}
.menu-heading.underlined {
  padding-bottom: 10px;
  border-bottom: 1px solid #eeeeee;
}

.menu-list {
  margin: 0 0 20px 0;
  list-style: none;
  padding: 0;
  overflow: hidden;
}
@media (min-width: 1200px) {
  [class*="col-lg-"] .menu-list {
    margin-bottom: 8px;
  }
}
@media (min-width: 1025px) {
  [class*="col-md-"] .menu-list {
    margin-bottom: 8px;
  }
}
@media (min-width: 769px) {
  [class*="col-sm-"] .menu-list {
    margin-bottom: 8px;
  }
}
@media (min-width: 480px) {
  [class*="col-xs-"] .menu-list {
    margin-bottom: 8px;
  }
}
.menu-list ul {
  margin: 0;
  padding: 0;
  list-style: none;
  position: absolute;
  display: none;
  left: 110%;
  width: 100%;
}
.menu-list li {
  position: relative;
  margin-top: 1px;
}
.menu-list li:first-child {
  margin-top: 0;
}
.menu-list li > a {
  text-decoration: none;
  display: block;
  padding: 8px 12px;
  border-radius: 3px;
}
.nav-link {
  text-decoration: none;
  display: block;
  padding: 8px 12px;
  border-radius: 3px;
}
.menu-list li > a:hover,
.menu-list li > a:focus {
  text-decoration: none;
  background-color: #f5f5f5;
}
.menu-list li > a > i {
  margin-right: 10px;
}
.menu-list li > a > .label,
.menu-list li > a > .badge {
  float: right;
  margin-left: 7px;
}
.menu-list li.router-link-active > a,
.menu-list li.router-link-active > a:hover,
.menu-list li.router-link-active > a:focus {
  text-decoration: none;
  color: #fff;
  background-color: #2196f3;
}
.menu-list li.disabled > a,
.menu-list li.disabled > a:hover,
.menu-list li.disabled > a:focus {
  background-color: transparent;
  color: #999999;
  cursor: not-allowed;
}
.menu-list li.disabled > a > .label,
.menu-list li.disabled > a > .badge,
.menu-list li.disabled > a > img {
  opacity: 0.8;
  filter: alpha(opacity=80);
}
@media (max-width: 768px) {
  .menu-list li > a {
    color: #fff;
  }
  .menu-list li > a:hover,
  .menu-list li > a:focus {
    background-color: rgba(0, 0, 0, 0.1);
  }
  .menu-list li.router-link-active > a,
  .menu-list li.router-link-active > a:hover,
  .menu-list li.router-link-active > a:focus {
    background-color: rgba(0, 0, 0, 0.1);
  }
  .menu-list li.disabled > a,
  .menu-list li.disabled > a:hover,
  .menu-list li.disabled > a:focus {
    background-color: transparent;
    color: rgba(255, 255, 255, 0.6);
    cursor: not-allowed;
  }
  .navbar-default .menu-list li > a:hover,
  .navbar-default .menu-list li > a:focus {
    background-color: #f5f5f5;
  }
  .navbar-default .menu-list li.router-link-active > a,
  .navbar-default .menu-list li.router-link-active > a:hover,
  .navbar-default .menu-list li.router-link-active > a:focus {
    color: #fff;
    background-color: #2196f3;
  }
  .navbar-default .menu-list li.disabled > a,
  .navbar-default .menu-list li.disabled > a:hover,
  .navbar-default .menu-list li.disabled > a:focus {
    background-color: transparent;
    color: #999999;
  }
}
@media (min-width: 769px) {
  .menu-list {
    overflow-y: auto;
    max-height: 340px;
  }
}
.inputTheme {
  width: 50px;
}
</style>
