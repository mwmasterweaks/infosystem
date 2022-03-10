<template>
  <div>
    <button
      v-b-modal="'modalSalesGraph'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Sales Report</button>
    <b-modal
      id="modalSalesGraph"
      size="xl"
      title="Sales Report"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
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
              weekday: 'short'
            }"
            placeholder="Select date From"
            @input="select_area_change"
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
              weekday: 'short'
            }"
            placeholder="Select date To"
            @input="select_area_change"
            local="en"
          ></b-form-datepicker>
        </div>
      </div>

      <!-- <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Area:</p>
        </div>
        <div class="col-lg-3">
          <model-list-select
            :list="area"
            v-model="selected_area"
            option-value="id"
            option-text="name"
            name="area"
            ref="area"
            placeholder="Select/Search a area..."
            @input="select_area_change"
          ></model-list-select>
        </div>
      </div>-->
      <br />
      <center>
        <div class="rowFields mx-auto row">
          <b-table
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="fields"
            :items="items"
            :busy="tblisBusy"
            thead-class="cursorPointer-th"
            head-variant=" elClr"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <template v-slot:cell(total_clients)="row">
              <span
                @click="cellClick(row.item.total_clients)"
                class="c-point"
                >{{ row.item.total_clients.length }}</span
              >
            </template>

            <template v-slot:cell(total_no_contract)="row">
              <span
                @click="cellClick(row.item.total_no_contract)"
                class="c-point"
                >{{ row.item.total_no_contract.length }}</span
              >
            </template>

            <template v-slot:cell(total_no_otc)="row">
<<<<<<< HEAD
              <span @click="cellClick(row.item.total_no_otc)" class="c-point">{{
                row.item.total_no_otc.length
              }}</span>
=======
              <span @click="cellClick(row.item.total_no_otc)" class="c-point">
                {{
                row.item.total_no_otc.length
                }}
              </span>
>>>>>>> summary email Mice (2021-11-26)
            </template>

            <template v-slot:cell(for_activation)="row">
              <span
                @click="cellClick(row.item.for_activation)"
                class="c-point"
                >{{ row.item.for_activation.length }}</span
              >
            </template>

            <template v-slot:cell(activated)="row">
<<<<<<< HEAD
              <span @click="cellClick(row.item.activated)" class="c-point">{{
                row.item.activated.length
              }}</span>
=======
              <span @click="cellClick(row.item.activated)" class="c-point">
                {{
                row.item.activated.length
                }}
              </span>
>>>>>>> summary email Mice (2021-11-26)
            </template>

            <template slot="table-caption"></template>
          </b-table>

          <div>
            <table class="overall-table">
              <tr class="overall-tr">
                <td>{{ data.label1 }} clients</td>
                <td>: {{ data.overall_clients }}</td>
              </tr>
              <tr class="overall-tr">
                <td>{{ data.label1 }} no contract</td>
                <td>: {{ data.overall_no_contract }}</td>
              </tr>
              <tr class="overall-tr">
                <td>{{ data.label1 }} no OTC</td>
                <td>: {{ data.overall_no_otc }}</td>
              </tr>

              <tr class="overall-tr">
                <td>{{ data.label1 }} paid MRR</td>
                <td>: {{ data.overall_paid_mrr }}</td>
              </tr>

              <tr class="overall-tr">
                <td>{{ data.label1 }} for activation</td>
                <td>: {{ data.overall_for_activation }}</td>
              </tr>

              <tr class="overall-tr">
                <td>{{ data.label1 }} for activation MRR</td>
                <td>: {{ data.overall_for_activation_mrr }}</td>
              </tr>

              <tr class="overall-tr">
                <td>{{ data.label1 }} Activated</td>
                <td>: {{ data.overall_activated }}</td>
              </tr>
              <tr class="overall-tr">
                <td>{{ data.label1 }} Activated MRR</td>
                <td>: {{ data.overall_activated_mrr }}</td>
              </tr>

              <tr class="overall-tr">
                <td>{{ data.label1 }} quota</td>
                <td>: {{ data.overall_quota }}</td>
              </tr>
              <tr class="overall-tr">
                <td>{{ data.label1 }} hit rate</td>
                <td>: {{ data.overall_hit_rate }}</td>
              </tr>
            </table>
          </div>
        </div>
      </center>
      <br />
      <br />

      <template v-slot:modal-footer>
        <!-- <b-button size="sm" variant="success" @click="fnExcelReport('summaryTable')">Export</b-button> -->
      </template>
    </b-modal>

    <b-modal
      id="modalClientList"
      size="xl"
      title="Client List"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <b-form-input
        id="txtbox_filter"
        class="searchForm"
        v-model.lazy="client_tblFilter"
        placeholder="Search"
      ></b-form-input>

      <b-table
        class="elClr"
        show-empty
        striped
        hover
        outlined
        :fields="client_fields"
        :filter="client_tblFilter"
        :items="client_items"
        thead-class="cursorPointer-th"
        head-variant=" elClr"
      >
        <div slot="table-busy" class="text-center text-danger my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Loading...</strong>
        </div>
      </b-table>
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
        { key: "name_name", label: "Name", sortable: true },
        { key: "total_clients", label: "Total Client", sortable: true },
        { key: "total_no_contract", label: "No Contract", sortable: true },
        { key: "total_no_otc", label: "No OTC", sortable: true },
        { key: "paid_mrr", label: "Paid MRR", sortable: true },
        {
          key: "for_activation",
          label: "For Activation",
          sortable: true
        },
        {
          key: "for_activation_mrr",
          label: "For Activation MRR",
          sortable: true
        },
        { key: "activated", label: "Activated", sortable: true },
        { key: "activated_mrr", label: "Activated MRR", sortable: true },
        { key: "quota", label: "Quota", sortable: true },
        { key: "hit_rate", label: "Hit Rate", sortable: true }
      ],
      data: {},
      items: [],
      client_tblFilter: "",

      region: [],
      selected_area: 0,
      selected_date_from: null,
      selected_date_to: null,
      selectedDateRange: {},
      client_fields: [
        { key: "acc_no", label: "Account No.", sortable: true },
        { key: "name", label: "Account Name", sortable: true },
        { key: "location", label: "Address", sortable: true },
        { key: "contact", label: "Contact", sortable: true },
        { key: "email_add", label: "Email", sortable: true }
      ],
      client_items: [],
      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.region = this.$global.getRegion();

    var temp = {
      id: 0,
      name: "All Regions"
    };
    this.region.unshift(temp);
  },
  methods: {
    select_area_change() {
      console.log(this.selected_area);
      if (this.selected_date_from != null && this.selected_date_to != null) {
        this.callList();
      }
    },
    callList() {
      this.tblisBusy = true;
      this.selectedDateRange.start = this.selected_date_from;
      this.selectedDateRange.end = this.selected_date_to;
      this.selectedDateRange.area_id = this.selected_area;
      console.log(this.selectedDateRange);
      this.$http
        .post("api/sales_graph", this.selectedDateRange)
        .then(response => {
          console.log(response.body);
          this.data = response.body;
          this.items = this.data.data;
          if (this.selectedDateRange.area_id == 0) this.data.label1 = "Overall";
          else this.data.label1 = "Grand total";
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
    tblRowClicked() {},
    cellClick(item) {
      console.log(item);
      this.client_items = item;
      this.$bvModal.show("modalClientList");
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

td {
  margin-top: 5px;
  padding: 5px;
}

.c-point {
  cursor: pointer;
}
#txtbox_filter {
  margin: 4px;
}
</style>
