<template>
  <div>
    <button
      v-b-modal="'modalPerformance'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Performance Sheet</button>
    <b-modal
      id="modalPerformance"
      size="xl"
      title="Installation Performance Sheet"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Select date range:</p>
        </div>
        <div class="col-lg-3">
          <rangedate-picker class="dateRangePicker" @selected="onDateSelected" i18n="EN"></rangedate-picker>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Region:</p>
        </div>
        <div class="col-lg-3">
          <model-list-select
            :list="regions"
            v-model="selected_region"
            option-value="id"
            option-text="name"
            name="region"
            ref="region"
            placeholder="Select/Search a Region..."
            @input="select_region_change"
          ></model-list-select>
        </div>
      </div>
      <br />
      <br />
      <center>
        <table class="a" id="Performances">
          <tr>
            <th class="a" colspan="4">
              <h5>Performance Summary</h5>
            </th>
          </tr>
          <tr>
            <th class="a"></th>
            <th class="a">Result</th>
            <th class="a">Evaluation</th>
            <th class="a">Targets values</th>
          </tr>
          <tr>
            <td class="a">
              <b>Average Days/Client :</b>
            </td>
            <td class="a">{{AverageDaysPerClient_Result}}</td>
            <td class="a" style="color: green;" v-if="AverageDaysPerClient_Eval == 'PASS'">PASS</td>
            <td class="a" style="color: red;" v-if="AverageDaysPerClient_Eval == 'FAIL'">FAIL</td>
            <td class="a">less than 9 days</td>
          </tr>

          <tr>
            <td class="a">
              <b>Clients/Day</b>
            </td>
            <td class="a">{{clientPerDay_Result}}</td>
            <td class="a" style="color: green;" v-if="clientPerDay_Eval == 'PASS'">PASS</td>
            <td class="a" style="color: red;" v-if="clientPerDay_Eval == 'FAIL'">FAIL</td>
            <td class="a">{{clientPerDay_TargetValue}}</td>
          </tr>
        </table>
        <br />
        <br />
        <div style="height: 50%; width: 50%;">
          <h5>Per 7-Day Performance Summary</h5>
          <line-chart
            v-if="load_chart"
            :data="chart_data"
            :options="{responsive: true, maintainAspectRatio: false}"
          />
        </div>
        <br />
      </center>

      <br />
      <br />
      <b-table
        id="summaryTable"
        class="elClr centerText"
        tbody-tr-class="elClr"
        show-empty
        striped
        hover
        outlined
        :fields="fields"
        :items="items"
        :busy="tblisBusy"
        head-variant=" elClr"
      >
        <div slot="table-busy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <template slot="table-caption"></template>
      </b-table>

      <div>
        <p>Total no. of client/s: {{items.length}}</p>
        <p>Unique Actual Date: {{uniqueActualDate}}</p>
        <p>Unique CnC Confirm Date: {{uniqueCnCDate}}</p>
      </div>
      <br />
      <br />

      <h5>Unscheduled Paid Customers</h5>
      <b-table
        id="summaryTable1"
        class="elClr centerText"
        tbody-tr-class="elClr"
        show-empty
        striped
        hover
        outlined
        :fields="fields_unscheduled"
        :items="items_unscheduled"
        :busy="tblisBusy_unscheduled"
        head-variant=" elClr"
      >
        <div slot="table-busy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <template slot="table-caption"></template>
      </b-table>
      <div>
        <p>Total no. of client/s: {{items_unscheduled.length}}</p>
        <p>Unique CnC Confirm Date: {{cnc_unique}}</p>
        <p>CnC Daily Target Rate: {{cnc_rate}}</p>
      </div>
      <template v-slot:modal-footer>
        <b-button size="sm" variant="success" @click="fnExcelReport('summaryTable')">Export</b-button>
      </template>
    </b-modal>
  </div>
</template>
<script>
import LineChart from "../../packages/LineChart.js";
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    LineChart
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "count_num", label: "#", sortable: true },
        { key: "name", label: "Client Name ", sortable: true },
        { key: "aging", label: "CNC confirmation Date", sortable: true },
        { key: "foc_schedule", label: "FOC Sched.", sortable: true },
        { key: "foc_actual", label: "FOC Actual.", sortable: true },
        { key: "foc_offset", label: "FOC Offset.", sortable: true },
        {
          key: "target_date",
          label: "Planned date of installation",
          sortable: true
        },
        {
          key: "date_activated",
          label: "Actual date of installation",
          sortable: true
        },
        { key: "offset", label: "Offset", sortable: true },
        { key: "num_days", label: "No. of Days", sortable: true },
        { key: "inst_remarks", label: "Remarks" }
      ],
      items: [],
      regions: [],
      selected_region: {
        id: "",
        name: ""
      },
      selected_date_from: "?",
      selected_date_to: "?",
      selectedDateRange: null,
      AverageDaysPerClient_Result: 0,
      clientPerDay_TargetValue: 0,
      clientPerDay_Result: 0,
      uniqueActualDate: 0,
      uniqueCnCDate: 0,
      cnc_unique: 0,
      cnc_rate: 0,
      AverageDaysPerClient_Eval: "",
      clientPerDay_Eval: "",
      load_chart: false,
      chart_data: [],
      tblisBusy_unscheduled: false,
      fields_unscheduled: [
        { key: "count_num_nodate", label: "#", sortable: true },
        { key: "name", label: "Client Name ", sortable: true },
        { key: "aging", label: "CNC confirmation Date", sortable: true },
        { key: "foc_schedule", label: "FOC Sched.", sortable: true },
        { key: "foc_actual", label: "FOC Actual.", sortable: true },
        { key: "foc_offset", label: "FOC Offset.", sortable: true },
        { key: "inst_remarks", label: "Remarks" }
      ],
      items_unscheduled: [],

      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.regions = this.$global.getRegion();
    //this.chart_addData();
  },
  methods: {
    onDateSelected(daterange) {
      this.selected_date_from = this.formatDateMDY(daterange.start);
      if (daterange.end == null) daterange.end = daterange.start;
      this.selected_date_to = this.formatDateMDY(daterange.end);
      this.selectedDateRange = daterange;
      console.log(this.selectedDateRange);
      if (this.selected_region.id != "" && this.selectedDateRange != null) {
        this.callList();
      }
    },
    select_region_change() {
      if (this.selected_region.id != "" && this.selectedDateRange != null) {
        this.callList();
      }
    },
    callList() {
      this.tblisBusy = true;
      this.tblisBusy_unscheduled = true;

      this.selectedDateRange.region_id = this.selected_region.id;

      this.$http
        .post("api/performance_sheet", this.selectedDateRange)
        .then(response => {
          if (response.body.items.length > 0) {
            this.items = response.body.items;
            this.uniqueActualDate = response.body.result_dates_unique;
            this.uniqueCnCDate = response.body.target_item.length;
            this.cnc_unique = response.body.cnc_unique;
            this.cnc_rate = response.body.cnc_rate;
            this.AverageDaysPerClient_Result =
              response.body.AverageDaysPerClient_Result;
            this.clientPerDay_TargetValue =
              response.body.clientPerDay_TargetValue;
            this.clientPerDay_Result = response.body.clientPerDay_Result;
            this.AverageDaysPerClient_Eval =
              response.body.AverageDaysPerClient_Eval;
            this.clientPerDay_Eval = response.body.clientPerDay_Eval;
            console.log(response.body);
            this.chart_addData(response.body.c2);
            this.items_unscheduled = response.body.itemnodate;
            console.log(this.items_unscheduled);
          } else {
            this.chart_data = [];
            this.items = [];
            this.uniqueActualDate = "";
            this.uniqueCnCDate = "";
            this.cnc_unique = "";
            this.cnc_rate = "";
            this.AverageDaysPerClient_Result = "";
            this.clientPerDay_TargetValue = "";
            this.clientPerDay_Result = "";
            this.AverageDaysPerClient_Eval = "";
            this.clientPerDay_Eval = "";
            this.items_unscheduled = [];
          }
          this.tblisBusy = false;
          this.tblisBusy_unscheduled = false;
        });
    },
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    },
    fnExcelReport(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table><tr><th colspan='2' style='font-size: large;'>Installation Performance Sheet</th></tr>" +
              "<tr></tr><tr>" +
              "<td>From: " +
              this.selected_date_from +
              " To: " +
              this.selected_date_to +
              "</td>" +
              "</tr>" +
              "<tr>" +
              "<td>Region: " +
              this.selected_region.name +
              "</td>" +
              "</tr>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table
            var tab1 = document.getElementById("Performances");

            for (j = 0; j < tab1.rows.length; j++) {
              tab_text = tab_text + tab1.rows[j].innerHTML + "</tr>";
            }
            tab_text = tab_text + "<tr></tr> <tr></tr>";
            var j = 0;
            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
              // If Internet Explorer
              txtArea1.document.open("txt/html", "replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus();
              var sa = txtArea1.document.execCommand(
                "SaveAs",
                true,
                "Say Thanks to Sumit.xls"
              );
            } //other browser not tested on IE 11
            else
              var sa = window.open(
                "data:application/vnd.ms-excel," + encodeURIComponent(tab_text)
              );
            return sa;
          }.bind(this),
          1000
        );
      });
    },
    formatDateMDY(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;
      var mstring = [
        "Jan.",
        "Feb.",
        "Mar.",
        "Apr.",
        "May",
        "Jun.",
        "Jul.",
        "Aug.",
        "Sept.",
        "Oct.",
        "Nov.",
        "Dec."
      ];
      return [mstring[month - 1], day, year].join(" ");
    },
    chart_addData(data) {
      this.chart_data = {
        labels: ["1st 7Days", "2nd 7Days", "3rd 7Days", "Last 2Days"],
        datasets: [
          {
            label: "Result: Average Days/Client ",
            data: [10, 10, 10, 10],
            borderColor: ["green"],
            borderWidth: 1,
            fill: false
          },
          {
            label: "Targets value: Average Days/Client",
            data: [9, 9, 9, 9],
            borderColor: ["yellow"],
            borderWidth: 1,
            fill: false
          },
          {
            label: "Result: Clients/Day ",
            data: [1.5, 1.5, 1.5, 1.5],
            borderColor: ["Blue"],
            borderWidth: 1,
            fill: false
          },
          {
            label: "Targets value: Clients/Day",
            data: [2.1, 2.1, 2.1, 2.1],
            borderColor: ["orange"],
            borderWidth: 1,
            fill: false
          },
          {
            label: "Sales: Clients/Day ",
            data: [1.5, 1.5, 1.5, 1.5],
            borderColor: ["red"],
            borderWidth: 1,
            fill: false
          }
        ]
      };
      this.load_chart = true;
      this.chart_data.labels = data.data_chart_label;
      this.chart_data.datasets[0].data = data.data_result_Ave_day_client;
      this.chart_data.datasets[2].data = data.data_result_client_per_day;
      this.chart_data.datasets[3].data = data.data_target_client_per_day;
      this.chart_data.datasets[4].data = data.ClientsAve;
      this.chart_data.datasets[1].data = data.data_target_value;
      console.log(data);
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: left;
}

table.a {
  border-collapse: collapse;
  border: 1px solid black;
  table-layout: auto;
  width: 50%;
}

th.a,
td.a {
  border: 1px solid black;
}

th.a {
  background-color: silver;
}
</style>


