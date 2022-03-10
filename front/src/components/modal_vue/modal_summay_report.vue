<template>
  <div>
    <button
      v-b-modal="'modalSelectDateRange'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Summary</button>
    <b-modal
      id="modalSelectDateRange"
      size="xl"
      title="Summary report"
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
        <template v-slot:thead-top>
          <b-tr>
            <b-th></b-th>
            <b-th
              colspan="2"
              style="background-color:#8af976;"
            >{{selected_date_from}} TO {{selected_date_to}}</b-th>
            <b-th colspan="6"></b-th>
          </b-tr>
          <b-tr>
            <b-th></b-th>
            <b-th colspan="4" style="background-color:yellow;">INSTALLATION</b-th>
            <b-th colspan="4" style="background-color:#ffe699;">TROUBLESHOOTING</b-th>
          </b-tr>
        </template>
        <div slot="table-busy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
        <template slot="table-caption"></template>
      </b-table>
      <template v-slot:modal-footer>
        <b-button size="sm" variant="success" @click="fnExcelReport('summaryTable')">Export</b-button>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "name", label: "REGION" },
        { key: "inst_planned", label: "PLANNED" },
        { key: "inst_actual", label: "ACTUAL" },
        { key: "inst_percent", label: "% INSTALLED" },
        { key: "inst_pending", label: "PENDING" },
        { key: "trbl_planned", label: "PLANNED" },
        { key: "trbl_actual", label: "ACTUAL" },
        { key: "trbl_percent", label: "% REPAIRED" },
        { key: "trbl_pending", label: "PLANNED" }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],

      selected_date_from: "?",
      selected_date_to: "?",
      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
  },
  methods: {
    onDateSelected(daterange) {
      this.selected_date_from = this.formatDateMDY(daterange.start);
      if (daterange.end == null) daterange.end = daterange.start;
      this.selected_date_to = this.formatDateMDY(daterange.end);
      this.tblisBusy = true;
      this.$http
        .post("api/summary_report_daterange", daterange)
        .then(response => {
          this.items = response.body.items;
          console.log(response.body);
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
            var tab_text = "<table border='2px'><tr>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table
            console.log(tab.rows.length);

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
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
</style>


