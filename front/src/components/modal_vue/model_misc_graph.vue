<template>
  <div>
    <button
      v-b-modal="'modalMiscellaneous'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Miscellaneous Graph</button>
    <b-modal
      id="modalMiscellaneous"
      size="xl"
      title="Miscellaneous Graph"
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

      <center>
        <div style="height: 50%; width: 50%; background-color:white">
          <h5></h5>
          <line-chart
            v-if="load_chart"
            :data="chart_data"
            :options="{responsive: true, maintainAspectRatio: false}"
          />
        </div>
      </center>
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
      fields: [{ key: "count_num", label: "#", sortable: true }],
      items: [],
      regions: [],
      selected_region: {
        id: "",
        name: ""
      },
      selected_date_from: "?",
      selected_date_to: "?",
      selectedDateRange: null,
      load_chart: false,
      chart_data: [],
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

      this.selectedDateRange.region_id = this.selected_region.id;

      this.$http
        .post("api/misc_summary", this.selectedDateRange)
        .then(response => {
          if (response.body.daily_sales_incoded.length > 0) {
            this.items = response.body.daily_sales_incoded;
            console.log(response.body);
            this.chart_addData(response.body);
          } else {
            this.chart_data = [];
            this.items = [];
          }
          this.tblisBusy = false;
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
            label: "Daily Sales Incoded",
            data: [10, 10, 10, 10],
            borderColor: ["green"],
            borderWidth: 1,
            fill: false
          }
        ]
      };
      this.chart_data.labels = data.date_label;
      this.chart_data.datasets[0].data = data.daily_sales_incoded_count;
      this.load_chart = true;
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


