import Vue from "vue";
import VueRouter from "vue-router";

import Login from "./components/user/Login.vue";
import Register from "./components/user/Register.vue";
import Dashboard from "./components/Dashboard.vue";

import JobOrder from "./components/view/components/JobOrder.vue";
import ViewBranch from "./components/view/components/ViewBranch.vue";
import ViewRegion from "./components/view/components/ViewRegion.vue";
import ViewBucket from "./components/view/components/ViewBucket.vue";
import ViewArea from "./components/view/components/ViewArea.vue";
import ViewActivityTicket from "./components/view/components/ViewActivityTicket.vue";
import ViewNode from "./components/view/components/ViewNode.vue";
import ViewOLT from "./components/view/components/ViewOLT.vue";
import ViewClient from "./components/view/accounts/ViewClient.vue";
import ViewEngineer from "./components/view/accounts/ViewEngineer.vue";
import ViewModem from "./components/view/components/ViewModem.vue";
import ViewPackage from "./components/view/components/ViewPackages.vue";
import ViewPackageType from "./components/view/components/ViewPackageType.vue";
import ViewBusinessType from "./components/view/components/ViewBusinessType.vue";
import ViewSales from "./components/view/accounts/ViewSales.vue";
import ViewSalesAgent from "./components/view/accounts/ViewSalesAgent.vue";
import ViewSchedule from "./components/view/components/ViewSchedule.vue";
import Map from "./components/view/components/ViewMap.vue";
import ViewClosureType from "./components/view/components/ViewClosureType.vue";
import ViewSplitterType from "./components/view/components/ViewSplitterType.vue";
import ViewActivityTicketType from "./components/view/components/ViewActivityTicketType.vue";

import MacGenerator from "./components/view/components/MacGenerator.vue";
import ViewTeam from "./components/view/components/ViewTeam.vue";
import ViewUser from "./components/view/accounts/ViewUser.vue";

import Ticket_List from "./components/ticket/Ticket_List.vue";
import ComponentDashboard from "./components/DashboardComponents.vue";
import AccountSettings from "./components/others/accountSetting.vue";
import Suggestions from "./components/others/suggestion.vue";
import Profile from "./components/others/profile.vue";
import Help from "./components/others/Help.vue";

import PaymentMethodList from "./components/accounting/PaymentMethodList.vue";
import SOA from "./components/accounting/soa.vue";
import DailyCollectReport from "./components/accounting/DailyCollectReport.vue";
import AgingReport from "./components/accounting/AgingReport.vue";
import WHTReport from "./components/accounting/WHTReport.vue";
import RebateReport from "./components/accounting/RebateReport.vue";
import Transmittal from "./components/accounting/Transmittal.vue";
import Banking_Code from "./components/accounting/Banking_Code.vue";
import test from "./components/user/test.vue";
import TableQuery from "./components/view/components/TableQuery.vue";

Vue.use(VueRouter);

const router = new VueRouter({
  routes: [
    {
      path: "/test",
      component: test,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/login",
      component: Login,
      meta: {
        forVisitors: true
      }
    },
    {
      path: "/register",
      component: Register,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/home",
      component: Dashboard,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/",
      component: Dashboard,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/JobOrder",
      component: JobOrder,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewBranch",
      component: ViewBranch,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewRegion",
      component: ViewRegion,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewBucket",
      component: ViewBucket,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewArea",
      component: ViewArea,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewActivityTicket",
      component: ViewActivityTicket,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewOLT",
      component: ViewOLT,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewNode",
      component: ViewNode,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewClient",
      component: ViewClient,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewEngineer",
      component: ViewEngineer,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewModem",
      component: ViewModem,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewPackage",
      component: ViewPackage,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewPackageType",
      component: ViewPackageType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewActivityTicketType",
      component: ViewActivityTicketType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewBusinessType",
      component: ViewBusinessType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewSales",
      component: ViewSales,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewSalesAgent",
      component: ViewSalesAgent,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewSchedule",
      component: ViewSchedule,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Map",
      component: Map,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewClosureType",
      component: ViewClosureType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewSplitterType",
      component: ViewSplitterType,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/MacGenerator",
      component: MacGenerator,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewTeam",
      component: ViewTeam,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ViewUser",
      component: ViewUser,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/QueTicket",
      component: Ticket_List,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/ComponentDashboard",
      component: ComponentDashboard,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/AccountSettings",
      component: AccountSettings,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Suggestions",
      component: Suggestions,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Profile",
      component: Profile,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Help",
      component: Help,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/PaymentMethodList",
      component: PaymentMethodList,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/SOA",
      component: SOA,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/DailyCollectReport",
      component: DailyCollectReport,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/AgingReport",
      component: AgingReport,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/WHTReport",
      component: WHTReport,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/RebateReport",
      component: RebateReport,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Transmittal",
      component: Transmittal,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/Banking_Code",
      component: Banking_Code,
      meta: {
        forAuth: true
      }
    },
    {
      path: "/TableQuery",
      component: TableQuery,
      meta: {
        forAuth: true
      }
    }
  ],

  mode: "history"
});

export default router;
