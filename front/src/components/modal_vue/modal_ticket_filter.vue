<template>
  <div>
    <b-modal
      id="modalMultipleFilterTicket"
      title="Multiple Filter"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <!-- regions -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.region"
        ></p-check
        >Region
        <div class="rowFields mx-auto row" v-if="cbFilter.region">
          <div class="col-lg-12">
            <model-list-select
              :list="regions"
              v-model="cbFilter.data.region_id"
              option-value="id"
              option-text="name"
              placeholder="Please select a region . ."
              style="float:right"
              @input="checkAreas"
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- area -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.area"
        ></p-check
        >Area
        <div class="rowFields mx-auto row" v-if="cbFilter.area">
          <div class="col-lg-12">
            <multiselect
              v-model="cbFilter.data.area_id"
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
      </div>
      <!-- address -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.address"
        ></p-check
        >Address
        <div class="rowFields mx-auto row" v-if="cbFilter.address">
          <div class="col-lg-12">
            <input
              type="text"
              class="form-control"
              placeholder="Please type address . ."
              v-model.trim="cbFilter.data.address"
              style="height:35px;padding-left:13px"
            />
          </div>
        </div>
      </div>
      <!-- connection status -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.con_status"
        ></p-check
        >Connection Status
        <div class="rowFields mx-auto row" v-if="cbFilter.con_status">
          <div class="col-lg-12">
            <model-list-select
              :list="connection_status_list"
              v-model="cbFilter.data.con_status"
              option-value="id"
              option-text="name"
              placeholder="Please select connection status . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- ticket status -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.status"
        ></p-check
        >Status
        <div class="rowFields mx-auto row" v-if="cbFilter.status">
          <div class="col-lg-12">
            <model-list-select
              :list="Status_Ticket"
              v-model="cbFilter.data.status_id"
              option-value="id"
              option-text="name"
              style="height:35px"
              placeholder="Please select ticket status . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- target date -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.target_date"
        ></p-check
        >Target Date
        <div class="rowFields mx-auto row" v-if="cbFilter.target_date">
          <div class="col-lg-12">
            <b-form-datepicker
              id="datepickerFrom"
              today-button
              reset-button
              close-button
              :date-format-options="{
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                weekday: 'short'
              }"
              placeholder="Please select date . ."
              @input="select_date_change('target_date', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- complaint -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.complaint"
        ></p-check
        >Complaint
        <div class="rowFields mx-auto row" v-if="cbFilter.complaint">
          <div class="col-lg-12">
            <input
              type="text"
              class="form-control"
              v-model.trim="cbFilter.data.complaint"
              placeholder="Please type complaint . ."
              style="height:35px;padding-left:13px"
            />
          </div>
        </div>
      </div>
      <!-- findings -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.findings"
        ></p-check
        >Findings
        <div class="rowFields mx-auto row" v-if="cbFilter.findings">
          <div class="col-lg-12">
            <input
              type="text"
              class="form-control"
              v-model.trim="cbFilter.data.findings"
              placeholder="Please type findings . ."
              style="height:35px;padding-left:13px"
            />
          </div>
        </div>
      </div>
      <!-- consolidated tech -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.consolidated_tech"
        ></p-check
        >Consolidated Tech
        <div class="rowFields mx-auto row" v-if="cbFilter.consolidated_tech">
          <div class="col-lg-12">
            <input
              type="text"
              class="form-control"
              v-model.trim="cbFilter.data.consolidated_tech"
              placeholder="Please type consolidated tech . ."
              style="height:35px;padding-left:13px"
            />
          </div>
        </div>
      </div>
      <!-- date created -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.aging"
        ></p-check
        >Date Created
        <div class="rowFields mx-auto row" v-if="cbFilter.aging">
          <div class="col-lg-12">
            <rangedate-picker
              style="width: 100% !important"
              class="picker"
              @selected="onDateSelected"
              i18n="EN"
              placeholder="Please select date created . ."
            ></rangedate-picker>
          </div>
        </div>
      </div>
      <!-- date fixed -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.fixed"
        ></p-check
        >Date Fixed
        <div class="rowFields mx-auto row" v-if="cbFilter.fixed">
          <div class="col-lg-12">
            <rangedate-picker
              style="width: 100% !important"
              class="picker"
              @selected="onDateFixedSelected"
              i18n="EN"
              placeholder="Please select date fixed . ."
            ></rangedate-picker>
          </div>
        </div>
      </div>
      <!-- user -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.username"
        ></p-check
        >Created by
        <div class="rowFields mx-auto row" v-if="cbFilter.username">
          <div class="col-lg-12">
            <model-list-select
              :list="users"
              v-model="cbFilter.data.user_id"
              option-value="id"
              option-text="name"
              placeholder="Please select username . ."
            ></model-list-select>
          </div>
        </div>
      </div>

      <div slot="modal-footer" slot-scope="{}">
        <button class="btn btn-success btn-labeled" @click="filter_ok">
          <i class="fas fa-filter"></i>
          Apply Filter
        </button>
      </div>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import Multiselect from "vue-multiselect";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  // props: ["data"],
  components: {
    multiselect: Multiselect,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck
  },
  data() {
    return {
      cbFilter: {
        region: false,
        address: false,
        con_status: false,
        status: false,
        area: false,
        target_date: false,
        aging: false,
        fixed: false,
        created: false,
        complaint: false,
        findings: false,
        consolidated_tech: false,
        username: false,
        data: {
          region_id: 0,
          address: "",
          con_status: null,
          status_id: null,
          area_id: [],
          target_date: null,
          // aging: {
          //   from: null,
          //   to: null
          // },
          // fixed: {
          //   from: null,
          //   to: null
          // },
          date_created: "",
          date_fixed: "",
          complaint: "",
          findings: "",
          consolidated_tech: "",
          user_id: 0
        }
      },
      connection_status_list: [
        {
          id: "up",
          name: "Up"
        },
        {
          id: "down",
          name: "Down"
        },
        {
          id: "intermittent",
          name: "Intermittent"
        }
      ],
      Status_Ticket: [],
      areas: [],
      regions: [],
      user: [],
      users: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.regions = this.$global.getRegion();
    this.Status_Ticket = this.$global.getTicketStatus();
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/user").then(function(response) {
        this.users = response.body;
      });

      this.$http.get("api/area").then(response => {
        this.areas = response.body;
      });
    },
    filter_ok() {
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/Ticket/multipleFilter", this.cbFilter)
        .then(response => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
          // // if (this.data == "table")
          this.$root.$emit("update_ticket_list", response.body, this.cbFilter);
          // // else
          // // this.$root.$emit("add_data_to_recipient", response.body);
          this.$bvModal.hide("modalMultipleFilterTicket");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
          this.$root.$emit("pageLoaded");
        });
    },
    onDateSelected(daterange) {
      console.log(daterange);
      this.cbFilter.data.date_created = daterange;
    },
    onDateFixedSelected(daterange) {
      console.log(daterange);
      this.cbFilter.data.date_fixed = daterange;
    },
    select_date_change(txt, event) {
      if (txt == "target_date") {
        this.cbFilter.data.target_date = this.formatDateMDY(event);
      }
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
    checkAreas() {
      this.$http
        .get("api/checkAreas/" + this.cbFilter.data.region_id)
        .then(response => {
          console.log(response.body);
          this.cbFilter.data.area_id = response.body;
          this.cbFilter.area = true;
        });
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
.cont-wrap {
  margin-bottom: 10px;
  font-size: 12px;
  max-height: 200px;
}

.textLabel {
  font-size: 13px;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
