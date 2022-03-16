<template>
  <div>
    <b-modal
      id="modalMultipleFilterClient"
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
              placeholder="Please select a region . ."
              @input="checkAreas"
              option-value="id"
              option-text="name"
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
              style="height:35px;padding-left:13px"
              v-model.trim="cbFilter.data.location"
            />
          </div>
        </div>
      </div>
      <!-- business type -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.business_type"
        ></p-check
        >Business Type
        <div class="rowFields mx-auto row" v-if="cbFilter.business_type">
          <div class="col-lg-12">
            <model-list-select
              :list="business_type_list"
              v-model="cbFilter.data.business_type"
              option-value="name"
              option-text="name"
              placeholder="Place select business type . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- package -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.package"
        ></p-check
        >Package
        <div class="rowFields mx-auto row" v-if="cbFilter.package">
          <div class="col-lg-12">
            <model-list-select
              :list="packages"
              v-model="cbFilter.data.package_id"
              option-value="id"
              option-text="name"
              placeholder="Please select package . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- package type -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.package_type"
        ></p-check
        >Package Type
        <div class="rowFields mx-auto row" v-if="cbFilter.package_type">
          <div class="col-lg-12">
            <model-list-select
              :list="package_type_list"
              v-model="cbFilter.data.package_type_id"
              option-value="id"
              option-text="name"
              placeholder="Please select package type . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- sales -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.sales"
        ></p-check
        >Sales
        <div class="rowFields mx-auto row" v-if="cbFilter.sales">
          <div class="col-lg-12">
            <model-list-select
              :list="sales_list"
              v-model="cbFilter.data.sales_id"
              option-value="id"
              option-text="name"
              placeholder="Please select sales . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- referral -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-slim" color="success" v-model="cbFilter.referral"></p-check>Referral
        <div class="rowFields mx-auto row" v-if="cbFilter.referral">
          <div class="col-lg-12">
            <input
              type="text"
              class="form-control"
              placeholder="Please type referral name . ."
              style="height:35px;padding-left:13px"
              v-model.trim="cbFilter.data.referral"
            />
          </div>
        </div>
      </div>
      <!-- sales tech in charge -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.engineer"
        ></p-check
        >Tech in-charge
        <div class="rowFields mx-auto row" v-if="cbFilter.engineer">
          <div class="col-lg-12">
            <model-list-select
              :list="engineer_list"
              v-model="cbFilter.data.engineers_id"
              option-value="id"
              option-text="name"
              placeholder="Please select engineer . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- term -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.term"
        ></p-check
        >Term
        <div class="rowFields mx-auto row" v-if="cbFilter.term">
          <div class="col-lg-12">
            <input
              type="number"
              class="form-control"
              v-model.trim="cbFilter.data.term"
              placeholder="Please type term . . "
            />
          </div>
        </div>
      </div>
      <!-- contract status -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.contract"
        ></p-check
        >Contract
        <div class="rowFields mx-auto row" v-if="cbFilter.contract">
          <div class="col-lg-12">
            <model-list-select
              :list="contract_list"
              v-model="cbFilter.data.contract"
              option-value="id"
              option-text="name"
              placeholder="Please select contract status . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- communication protocol -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.communication_protocol"
        ></p-check
        >Communication Protocol
        <div
          class="rowFields mx-auto row"
          v-if="cbFilter.communication_protocol"
        >
          <div class="col-lg-12">
            <model-list-select
              :list="communication_protocol_list"
              v-model="cbFilter.data.communication_protocol"
              option-value="name"
              option-text="name"
              placeholder="Please select communication protocol . . "
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- client status -->
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
              :list="status_list"
              v-model="cbFilter.data.status"
              option-value="name"
              option-text="name"
              placeholder="Please select client status . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- bucket -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.bucket"
        ></p-check
        >Bucket IP
        <div class="rowFields mx-auto row" v-if="cbFilter.bucket">
          <div class="col-lg-12">
            <model-list-select
              :list="bucket_list"
              v-model="cbFilter.data.bucket_id"
              option-value="id"
              option-text="name"
              placeholder="Please select bucket . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- date of activation -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.date_activated"
        ></p-check
        >Date Activated
        <div class="rowFields mx-auto row" v-if="cbFilter.date_activated">
          <div class="col-lg-12">
            <center>
              <p-radio
                class="p-icon p-round p-pulse"
                v-model="cbFilter.date_activated_type"
                value="range"
                name="date_activated_type"
                color="success"
              >
                <i class="icon mdi mdi-check" slot="extra"></i>Date range of
                Activated
              </p-radio>

              <p-radio
                class="p-icon p-round p-pulse"
                v-model="cbFilter.date_activated_type"
                value="active"
                name="date_activated_type"
                color="success"
              >
                <i class="icon mdi mdi-check" slot="extra"></i>All Activated
              </p-radio>

              <p-radio
                class="p-icon p-round p-pulse"
                v-model="cbFilter.date_activated_type"
                value="not_active"
                name="date_activated_type"
                color="success"
              >
                <i class="icon mdi mdi-check" slot="extra"></i>All no date
                Activated
              </p-radio>
            </center>
          </div>
        </div>
        <div
          class="rowFields mx-auto row"
          v-if="cbFilter.date_activated_type == 'range'"
        >
          <div class="col-lg-6">
            <b-form-datepicker
              id="datePickerFrom1"
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
              @input="select_date_change('date_activated_from', $event)"
              local="en"
            ></b-form-datepicker>
          </div>

          <div class="col-lg-6">
            <b-form-datepicker
              id="datePickerTo1"
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
              @input="select_date_change('date_activated_to', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- date created -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.created_at"
        ></p-check
        >Date Created
        <div class="rowFields mx-auto row" v-if="cbFilter.created_at">
          <div class="col-lg-6">
            <b-form-datepicker
              id="datePickerFrom1"
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
              @input="select_date_change('created_at_from', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
          <div class="col-lg-6">
            <b-form-datepicker
              id="datePickerTo1"
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
              @input="select_date_change('created_at_to', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- No Installation Sched. -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.has_sched"
        ></p-check
        >Has Installation Sched.
      </div>

      <!-- Client ID -->
      <div class="cont-wrap" v-if="roles.admin">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.id"
        ></p-check
        >Client ID
        <div class="rowFields mx-auto row" v-if="cbFilter.id">
          <div class="col-lg-6">
            <input
              type="number"
              class="form-control"
              v-model.trim="cbFilter.data.id_from"
              placeholder="Client ID From . . "
            />
          </div>
          <div class="col-lg-6">
            <input
              type="number"
              class="form-control"
              v-model.trim="cbFilter.data.id_to"
              placeholder="Client ID To . . "
            />
          </div>
        </div>
      </div>

      <!-- //
      //-->
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
import PrettyRadio from "pretty-checkbox-vue/radio";
import Multiselect from "vue-multiselect";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  props: ["data"],
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    multiselect: Multiselect
  },
  data() {
    return {
      cbFilter: {
        id: false,
        region: false,
        area: false,
        address: false,
        business_type: false,
        term: false,
        contract: false,
        sales: false,
        referral:false,
        engineer: false,
        package: false,
        package_type: false,
        communication_protocol: false,
        status: false,
        bucket: false,
        date_activated: false,
        date_activated_type: null,
        created_at: false,
        has_sched: false,
        data: {
          id_from: 0,
          id_to: 0,
          region_id: 0,
          area_id: [],
          location: "",
          business_type: "",
          term: 0,
          contract: null,
          sales_id: 0,
          referral:"",
          engineers_id: 0,
          package_id: 0,
          package_type_id: 0,
          bucket_id: 0,
          communication_protocol: "",
          status: "",
          date_activated: {
            from: null,
            to: null
          },
          created_at: {
            from: null,
            to: null
          }
        }
      },
      contract_list: [
        {
          name: "Done",
          id: "Done"
        },
        {
          name: "Undone",
          id: "Undone"
        }
      ],
      otc_list: [
        {
          name: "Paid",
          id: "Paid"
        },
        {
          name: "Unpaid",
          id: "Unpaid"
        }
      ],
      layoutStatOption: [
        { id: "M1", name: "M1" },
        { id: "M2", name: "M2" },
        { id: "MM2", name: "MM2" },
        { id: "M3", name: "M3" }
      ],
      packages: [],
      regions: [],
      business_type_list: [],
      package_type_list: [],
      sales_list: [],
      engineer_list: [],
      bucket_list: [],
      contract_list: [
        {
          name: "Done",
          id: "Done"
        },
        {
          name: "Undone",
          id: "Undone"
        }
      ],
      communication_protocol_list: [
        {
          name: "Internet",
          id: "Internet"
        },
        {
          name: "Intranet",
          id: "Intranet"
        }
      ],
      status_list: [
        {
          name: "Active",
          id: "Active"
        },
        {
          name: "Disconnected",
          id: "Disconnected"
        }
      ],
      areas: [],
      user: [],
      roles: []
    };
  },
  created() {
    this.regions = this.$global.getRegion();
    this.package_type_list = this.$global.getPackageTypes();
    this.sales_list = this.$global.getSales();
    this.engineer_list = this.$global.getEngineer();

    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();

    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/BusinessType").then(response => {
        this.business_type_list = response.body;
      });
      this.$http.get("api/Bucket").then(response => {
        this.bucket_list = response.body;
      });
      this.$http.get("api/area").then(response => {
        this.areas = response.body;
      });

      this.$http.get("api/Package").then(function(response) {
        this.packages = response.body;
      });
    },
    filter_ok() {
      console.log(this.cbFilter);
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/Client/multipleFilter", this.cbFilter)
        .then(response => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
          if (this.data == "table") {
            this.$root.$emit("update_item", response.body, this.cbFilter);
          } else if (this.data == "ticket") {
            this.$root.$emit("add_data_to_client_ticket", response.body);
          } else if (this.data == "text") {
            this.$root.$emit("add_data_to_recipient", response.body);
          }

          this.$bvModal.hide("modalMultipleFilterClient");
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
    select_date_change(txt, event) {
      if (txt == "date_activated_from") {
        this.cbFilter.data.date_activated.from = this.formatDateMDY(event);
      } else if (txt == "date_activated_to") {
        this.cbFilter.data.date_activated.to = this.formatDateMDY(event);
      } else if (txt == "created_at_from") {
        this.cbFilter.data.created_at.from = this.formatDateMDY(event);
      } else if (txt == "created_at_to") {
        this.cbFilter.data.created_at.to = this.formatDateMDY(event);
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
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
