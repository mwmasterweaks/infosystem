<template>
  <div>
    <button
      v-b-modal="'modalTempGraph'"
      type="button"
      style="margin-top: -20px"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >
      Installation Graph
    </button>
    <b-modal
      id="modalTempGraph"
      size="xl"
      title="Installation Graph"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <!-- <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Select date range:</p>
        </div>
        <div class="col-lg-3">
          <rangedate-picker class="dateRangePicker" @selected="onDateSelected" i18n="EN"></rangedate-picker>
        </div>
      </div>-->

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Select date From:</p>
        </div>
        <div class="col-lg-3">
          <b-form-datepicker
            id="datepickerFrom"
            v-model="selected_date_from"
            today-button
            reset-button
            close-button
            :date-format-options="{
              year: 'numeric',
              month: 'short',
              day: '2-digit',
              weekday: 'short',
            }"
            placeholder="Select date From"
            local="en"
          ></b-form-datepicker>
        </div>
        <div class="col-lg-2">
          <p class="textLabel">Select date To:</p>
        </div>
        <div class="col-lg-3">
          <b-form-datepicker
            id="datepickerTo"
            v-model="selected_date_to"
            today-button
            reset-button
            close-button
            :date-format-options="{
              year: 'numeric',
              month: 'short',
              day: '2-digit',
              weekday: 'short',
            }"
            placeholder="Select date To"
            local="en"
          ></b-form-datepicker>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Region:</p>
        </div>
        <div class="col-lg-8">
          <!-- <model-list-select
            :list="regions"
            v-model="selected_region"
            option-value="id"
            option-text="name"
            name="region"
            ref="region"
            placeholder="Select/Search a Region..."k rlmnm  vmv  bbxn     bbgif
          ></model-list-select> -->

          <model-list-select
            :list="regions"
            v-model="selected_region"
            option-value="id"
            option-text="name"
            placeholder="Select/Search a Region..."
            @input="checkAreas"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Area:</p>
        </div>
        <div class="col-lg-8">
          <multiselect
            v-model="selected_area"
            :options="areas"
            label="name"
            track-by="id"
            variant="success"
            multiple
            tag-placeholder="Add this area"
            open-direction="bottom"
            placeholder="Type to search or type area"
            :hide-selected="true"
            :clear-on-select="false"
            :close-on-select="false"
            :taggable="true"
          ></multiselect>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-10">
          <button
            @click="select_region_change()"
            type="button"
            style="margin-top: -20px"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Generate
          </button>
        </div>
        <!--
          <p class="textLabel">Area:</p>

        <div class="col-lg-8">

        </div> -->
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <br />
          <br />
          <br />
          <table class="overall-table">
            <tr class="overall-tr" style="color: green">
              <td>Cum. Sales :</td>
              <td>{{ items.sales[items.sales.length - 1] }}</td>
            </tr>
            <tr class="overall-tr" style="color: blue">
              <td>Cum. Installed :</td>
              <td>{{ items.installed[items.installed.length - 1] }}</td>
            </tr>
            <tr class="overall-tr" style="color: red">
              <td>Installed - Sales :</td>
              <td>
                {{
                  items.installed_minus_sales[
                    items.installed_minus_sales.length - 1
                  ]
                }}
              </td>
            </tr>
            <tr class="overall-tr" style="color: yellow">
              <td>CNC Paid :</td>
              <td>{{ items.paidCounts[items.paidCounts.length - 1] }}</td>
            </tr>
            <tr class="overall-tr" style="color: orange">
              <td>Installed - CNC :</td>
              <td>
                {{
                  items.installed_minus_cnc[
                    items.installed_minus_cnc.length - 1
                  ]
                }}
              </td>
            </tr>
            <tr class="overall-tr" style="color: pink">
              <td>Contract :</td>
              <td>{{ items.contractCount[items.contractCount.length - 1] }}</td>
            </tr>
          </table>
        </div>
        <div class="col-lg-9" style="background-color: white">
          <div>
            <line-chart
              v-if="load_chart"
              :data="chart_data"
              :options="{ responsive: true, maintainAspectRatio: false }"
            />
          </div>
        </div>
      </div>
      <br />
      <br />

      <template v-slot:modal-footer>
        <!-- <b-button size="sm" variant="success" @click="fnExcelReport('summaryTable')">Export</b-button> -->
      </template>
    </b-modal>
  </div>
</template>
<script>
import LineChart from "../../packages/LineChart.js";
import { ModelListSelect } from "vue-search-select";
import Multiselect from "vue-multiselect";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    multiselect: Multiselect,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    LineChart,
  },
  data() {
    return {
      tblisBusy: false,
      fields: [{ key: "count_num", label: "#", sortable: true }],
      items: {
        sales: [],
        installed: [],
        contractCount: [],
        installed_minus_cnc: [],
        installed_minus_sales: [],
        paidCounts: [],
      },
      regions: [],
      selected_region: {
        id: "",
        name: "",
      },
      selected_area: null,
      areas: [],
      selected_date_from: "?",
      selected_date_to: "?",
      selectedDateRange: {},
      load_chart: false,
      chart_data: [],
      roles: [],
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.regions = this.$global.getRegion();
    //this.chart_addData();
    var temp = {
      id: 0,
      name: "All regions",
    };
    this.regions.unshift(temp);
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/area").then((response) => {
        this.areas = response.body;
      });
    },
    onDateSelected(daterange) {
      this.selected_date_from = this.formatDateMDY(daterange.start);
      if (daterange.end == null) daterange.end = daterange.start;
      this.selected_date_to = this.formatDateMDY(daterange.end);
      this.selectedDateRange = daterange;
      if (this.selected_region.id != "" && this.selectedDateRange != null) {
        this.callList();
      }
    },
    select_region_change() {
      if (this.selected_date_from != null && this.selected_date_to != null) {
        this.callList();
      }
    },
    callList() {
      this.tblisBusy = true;
      this.selectedDateRange.start = this.selected_date_from;
      this.selectedDateRange.end = this.selected_date_to;
      this.selectedDateRange.region_id = this.selected_region.id;
      this.selectedDateRange.selected_area = this.selected_area;

      this.$root.$emit("pageLoading");
      this.$http
        .post("api/temp_graph", this.selectedDateRange)
        .then((response) => {
          if (response.body.sales.length > 0) {
            this.items = response.body;
            console.log(response.body);
            this.chart_addData(response.body);
          } else {
            this.chart_data = [];
            this.items = [];
          }
          this.tblisBusy = false;

          this.$root.$emit("pageLoaded");
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: response.body.error,
            text: "",
            icon: "error",
            dangerMode: true,
          });
          this.tblisBusy = false;
          this.$root.$emit("pageLoaded");
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
      this.$nextTick(function () {
        setTimeout(
          function () {
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
        "Dec.",
      ];
      return [mstring[month - 1], day, year].join(" ");
    },
    chart_addData(data) {
      this.chart_data = {
        labels: ["1st 7Days", "2nd 7Days", "3rd 7Days", "Last 2Days"],
        datasets: [
          {
            label: "Cumulative sales",
            data: [10, 10, 10, 10],
            borderColor: ["green"],
            borderWidth: 1,
            fill: false,
          },
          {
            label: "Cumulative Installed",
            data: [10, 10, 10, 10],
            borderColor: ["blue"],
            borderWidth: 1,
            fill: false,
          },
          {
            label: "Installed minus sales",
            data: [10, 10, 10, 10],
            borderColor: ["red"],
            borderWidth: 1,
            fill: false,
          },
          {
            label: "CNC Confirmed Paid",
            data: [10, 10, 10, 10],
            borderColor: ["yellow"],
            borderWidth: 1,
            fill: false,
          },
          {
            label: "Installed minus CNC",
            data: [10, 10, 10, 10],
            borderColor: ["orange"],
            borderWidth: 1,
            fill: false,
          },
          {
            label: "Contract",
            data: [10, 10, 10, 10],
            borderColor: ["pink"],
            borderWidth: 1,
            fill: false,
          },
        ],
      };
      this.chart_data.labels = data.date_label;
      this.chart_data.datasets[0].data = data.sales;
      this.chart_data.datasets[1].data = data.installed;
      this.chart_data.datasets[2].data = data.installed_minus_sales;
      this.chart_data.datasets[3].data = data.paidCounts;
      this.chart_data.datasets[4].data = data.installed_minus_cnc;
      this.chart_data.datasets[5].data = data.contractCount;
      this.load_chart = true;
      console.log(data);
    },
    checkAreas() {
      this.$http
        .get("api/checkAreas/" + this.selected_region.id)
        .then((response) => {
          this.selected_area = response.body;
        });
    },
  },
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

td {
  margin-top: 5px;
  padding: 5px;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
