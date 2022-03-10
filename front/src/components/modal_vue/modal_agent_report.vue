<template>
  <div>
    <button
      v-b-modal="'modalSalesGraph'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-info btn-labeled pull-right margin-right-10"
      v-if="roles.rm"
    >
      Agent Payroll
    </button>
    <b-modal
      id="modalSalesGraph"
      size="xl"
      title="Agent Report"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">Select Month:</p>
        </div>
        <div class="col-lg-4" style="margin-top:5px">
          <date-picker
            v-model="selectedDate"
            :config="AppliedDateoptions"
            autocomplete="off"
            @input="select_agent_change"
          ></date-picker>
        </div>
        <div class="col-lg-2">
          <p class="textLabel">Sales Agent:</p>
        </div>
        <div class="col-lg-4">
          <model-list-select
            :list="agents"
            v-model="selected_agent"
            option-value="id"
            option-text="name"
            name="agent"
            ref="agent"
            placeholder="Select/Search sales agent..."
            @input="select_agent_change"
          ></model-list-select>
        </div>
      </div>
      <br />
      <div class="rowFields mx-auto row">
        <h6>AGENT PAYROLL FOR THE MONTH OF: {{ formattedFirst }}</h6>
      </div>
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
            :filter="tblFilter"
            :busy="tblisBusy"
            thead-class="cursorPointer-th"
            head-variant=" elClr"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
          <div style="width:100%;display:flex;">
            <div style="width:50%">
              <table class="overall-table" style="float:left">
                <tr class="overall-tr">
                  <td>NO. OF CLIENTS</td>
                  <td>: {{ no_clients }}</td>
                </tr>
                <tr class="overall-tr">
                  <td>QUOTA</td>
                  <td>: {{ quota }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>MTD MRR</td>
                  <td>: {{ total_mrr }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>HIT RATE</td>
                  <td
                    style="color:green;font-weight:bold"
                    v-if="hit_rate >= '100'"
                  >
                    : {{ hit_rate }} %
                  </td>
                  <td style="color:red;font-weight:bold" v-else>
                    : {{ hit_rate }} %
                  </td>
                </tr>
              </table>
            </div>
            <div style="width:50%">
              <table
                class="overall-table"
                style="float:left"
                v-if="formattedFirst != 'DECEMBER'"
              >
                <tr class="overall-tr">
                  <td>1ST PAYMENT ({{ formattedFirst }})</td>
                  <td>: {{ first }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>2ND PAYMENT</td>
                  <td>: {{ second }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>3RD PAYMENT</td>
                  <td>: {{ third }}.00</td>
                </tr>
                <tr class="overall-tr" v-if="hit_rate >= '100'">
                  <td>HIT BONUS</td>
                  <td>
                    :
                    {{ bonus }}.00
                  </td>
                </tr>

                <tr class="overall-tr">
                  <td style="color:green;font-weight:bold">TOTAL TO PAY</td>
                  <td>: {{ total }}.00</td>
                </tr>
              </table>

              <!-- if december -->
              <table class="overall-table" style="float:left" v-else>
                <tr class="overall-tr">
                  <td>1ST PAYMENT DECEMBER</td>
                  <td>: {{ first }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>2ND PAYMENT DECEMBER</td>
                  <td>: {{ secondDec }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>3RD PAYMENT DECEMBER</td>
                  <td>: {{ thirdDec }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>1ST PAYMENT NOVEMBER</td>
                  <td>: {{ firstNov }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>2ND PAYMENT NOVEMBER</td>
                  <td>: {{ secondNov }}.00</td>
                </tr>
                <tr class="overall-tr">
                  <td>3RD PAYMENT OCTOBER</td>
                  <td>: {{ third }}.00</td>
                </tr>
                <tr class="overall-tr" v-if="hit_rate >= '100'">
                  <td>HIT BONUS</td>
                  <td>
                    :
                    {{ bonus }}.00
                  </td>
                </tr>
                <tr class="overall-tr">
                  <td style="color:green;font-weight:bold">TOTAL TO PAY</td>
                  <td>: {{ total }}.00</td>
                </tr>
              </table>
            </div>
          </div>
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
import { ModelListSelect } from "vue-search-select";
import datePicker from "vue-bootstrap-datetimepicker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "date-picker": datePicker
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "sales_agent.name", label: "Agent Name", sortable: true },
        { key: "name", label: "Client Name", sortable: true },
        {
          key: "date_activated",
          label: "Date Activated",
          formatter: (value, key, item) => {
            if (item.date_activated != null) {
              var rawDate = new Date(item.date_activated);
              var date = rawDate.toDateString();
              return date;
            } else {
              return " ";
            }
          },
          sortable: true
        },
        { key: "package.mrr", label: "MRR", sortable: true }
      ],
      data: {},
      items: [],
      tblFilter: "",
      selectedDate: null,
      roles: [],
      agents: [],
      selected_agent: 0,
      AppliedDateoptions: {
        format: "YYYY-MM",
        useCurrent: false
      },
      selected: {},
      formattedFirst: "",
      formattedSecond: "",
      agent: "",
      quota: 0,
      total_mrr: 0,
      no_clients: null,
      hit_rate: null,
      bonus: 0,
      first: 0,
      second: 0,
      third: 0,
      secondDec: 0,
      thirdDec: 0,
      firstNov: 0,
      secondNov: 0,
      total: 0
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.getAgents();

    var temp = {
      id: 0,
      name: "All Agents"
    };
    this.agents.unshift(temp);
  },
  methods: {
    getAgents() {
      this.$http.get("api/sales_agent").then(function(response) {
        this.agents = response.body;
      });
    },

    select_agent_change() {
      if (this.selectedDate != null && this.selected_agent != null) {
        var rawDate = new Date(this.selectedDate);
        var date = rawDate.toDateString();
        var month = rawDate.toLocaleString("default", { month: "long" });
        // var year = rawDate.getFullYear();
        this.formattedFirst = month.toUpperCase();

        this.callList();
      }
    },

    callList() {
      console.log(this.selectedDate);
      this.tblisBusy = true;
      this.selected.date = this.selectedDate;
      this.selected.agent = this.selected_agent;
      this.$http.post("api/agent_report", this.selected).then(response => {
        var formatter = new Intl.NumberFormat("en-US");
        console.log(response.body);
        this.items = response.body.data;
        this.quota = formatter.format(response.body.quota);
        this.total_mrr = formatter.format(response.body.total_mrr);
        this.no_clients = response.body.total_clients;
        this.hit_rate = response.body.hit_rate;
        this.first = formatter.format(response.body.first);
        this.second = formatter.format(response.body.second);
        this.third = formatter.format(response.body.third);
        this.secondDec = formatter.format(response.body.secondDec);
        this.thirdDec = formatter.format(response.body.thirdDec);
        this.firstNov = formatter.format(response.body.firstNov);
        this.secondNov = formatter.format(response.body.secondNov);
        this.bonus = formatter.format(response.body.bonus);
        this.total = formatter.format(response.body.total);

        this.tblisBusy = false;
      });
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
</style>
