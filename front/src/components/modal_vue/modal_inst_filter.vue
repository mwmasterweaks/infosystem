<template>
  <div>
    <b-modal
      id="modalMultipleFilter"
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
              @input="checkAreas"
              placeholder="Please select region . ."
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
              v-model.trim="cbFilter.data.address"
              placeholder="Please type address . ."
              style="height: 35px; padding-left: 13px"
            />
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
      <!-- installation date -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.installation_date"
        ></p-check
        >Installation Date (target date)
        <div class="rowFields mx-auto row" v-if="cbFilter.installation_date">
          <div class="col-lg-6">
            <b-form-datepicker
              id="datepickerFrom"
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
              @input="select_date_change('from', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
          <div class="col-lg-6">
            <b-form-datepicker
              id="datepickerTo"
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
              @input="select_date_change('to', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- foc schedule -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.foc_schedule"
        ></p-check
        >FOC Schedule
        <div class="rowFields mx-auto row" v-if="cbFilter.foc_schedule">
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
                weekday: 'short',
              }"
              placeholder="Select date From"
              @input="select_date_change('foc_schedule', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- aging -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.aging"
        ></p-check
        >Aging (aging is base on date that client paid the otc)
        <div class="rowFields mx-auto row" v-if="cbFilter.aging">
          <div class="col-lg-6">
            <b-form-datepicker
              id="datepickerFrom"
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
              @input="select_date_change('agingfrom', $event)"
              local="en"
            ></b-form-datepicker>
          </div>

          <div class="col-lg-6">
            <b-form-datepicker
              id="datepickerTo"
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
              @input="select_date_change('agingto', $event)"
              local="en"
            ></b-form-datepicker>
          </div>
        </div>
      </div>
      <!-- contract status-->
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
      <!-- otc status-->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.otc"
        ></p-check
        >OTC
        <div class="rowFields mx-auto row" v-if="cbFilter.otc">
          <div class="col-lg-12">
            <model-list-select
              :list="otc_list"
              v-model="cbFilter.data.otc"
              option-value="id"
              option-text="name"
              placeholder="Please select otc status"
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- layout status -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-slim"
          color="success"
          v-model="cbFilter.layout_status"
        ></p-check
        >Layout Status
        <div class="rowFields mx-auto row" v-if="cbFilter.layout_status">
          <div class="col-lg-12">
            <model-list-select
              :list="layoutStatOption"
              v-model="cbFilter.data.layout_status"
              option-value="id"
              option-text="name"
              placeholder="Please select layout status . ."
            ></model-list-select>
          </div>
        </div>
      </div>
      <!-- date activated -->
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
                <i class="icon mdi mdi-check" slot="extra"></i>
                Date range of Activated
              </p-radio>
              <p-radio
                class="p-icon p-round p-pulse"
                color="success"
                v-model="cbFilter.date_activated_type"
                value="active"
                name="date_activated_type"
              >
                <i slot="extra" class="icon mdi mdi-check"></i>
                All Activated
              </p-radio>
              <p-radio
                class="p-icon p-round p-pulse"
                color="success"
                v-model="cbFilter.date_activated_type"
                value="not_active"
                name="date_activated_type"
              >
                <i slot="extra" class="icon mdi mdi-check"></i>
                All No Date Activated
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
                weekday: 'short',
              }"
              placeholder="Select Date From"
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
                weekday: 'short',
              }"
              placeholder="Select Date To"
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
          v-model="cbFilter.created"
        ></p-check
        >Date Created
        <div class="rowFields mx-auto row" v-if="cbFilter.created">
          <!-- <div class="col-lg-12">
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
              placeholder="Select date From"
              @input="select_date_change('created', $event)"
              local="en"
            ></b-form-datepicker>
          </div> -->

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
                weekday: 'short',
              }"
              placeholder="Select Date From"
              @input="select_date_change('date_created_from', $event)"
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
                weekday: 'short',
              }"
              placeholder="Select Date To"
              @input="select_date_change('date_created_to', $event)"
              local="en"
            ></b-form-datepicker>
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
import VueRangedatePicker from "vue-rangedate-picker";
import Multiselect from "vue-multiselect";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    multiselect: Multiselect,
  },
  data() {
    return {
      cbFilter: {
        region: false,
        area: false,
        address: false,
        package: false,
        sales: false,
        installation_date: false,
        foc_schedule: false,
        aging: false,
        contract: false,
        otc: false,
        layout_status: false,
        date_activated: false,
        date_activated_type: null,
        created: false,
        data: {
          region_id: 0,
          area_id: [],
          address: "",
          package_id: 0,
          sales_id: 0,
          installation_date: {
            from: null,
            to: null,
          },
          foc_schedule_date: null,
          aging: {
            from: null,
            to: null,
          },
          contract: null,
          otc: null,
          layout_status: null,
          date_activated: {
            from: null,
            to: null,
          },
          created: {
            from: null,
            to: null,
          },
        },
      },
      contract_list: [
        {
          name: "Done",
          id: "Done",
        },
        {
          name: "Undone",
          id: "Undone",
        },
      ],
      otc_list: [
        {
          name: "Paid",
          id: "Paid",
        },
        {
          name: "Unpaid",
          id: "Unpaid",
        },
      ],
      layoutStatOption: [
        { id: "M1", name: "M1" },
        { id: "M2", name: "M2" },
        { id: "MM2", name: "MM2" },
        { id: "M3", name: "M3" },
      ],
      packages: [],
      regions: [],
      sales_list: [],
      areas: [],
      user: [],
      roles: [],
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.regions = this.$global.getRegion();
    this.sales_list = this.$global.getSales();
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/area").then((response) => {
        this.areas = response.body;
      });
      this.$http.get("api/Package").then(function (response) {
        this.packages = response.body;
      });
    },
    filter_ok() {
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/clientDetail/multipleFilter", this.cbFilter)
        .then((response) => {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          this.$root.$emit("update_item", response.body, this.cbFilter);
          this.$bvModal.hide("modalMultipleFilter");
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          });
          this.$root.$emit("pageLoaded");
        });
    },
    select_date_change(txt, event) {
      if (txt == "from") {
        this.cbFilter.data.installation_date.from = this.formatDateMDY(event);
      } else if (txt == "to") {
        this.cbFilter.data.installation_date.to = this.formatDateMDY(event);
      } else if (txt == "foc_schedule") {
        this.cbFilter.data.foc_schedule_date = this.formatDateMDY(event);
      } else if (txt == "agingfrom") {
        this.cbFilter.data.aging.from = this.formatDateMDY(event);
      } else if (txt == "agingto") {
        this.cbFilter.data.aging.to = this.formatDateMDY(event);
      } else if (txt == "date_activated_from") {
        this.cbFilter.data.date_activated.from = this.formatDateMDY(event);
      } else if (txt == "date_activated_to") {
        this.cbFilter.data.date_activated.to = this.formatDateMDY(event);
      } else if (txt == "date_created_from") {
        this.cbFilter.data.created.from = this.formatDateMDY(event);
      } else if (txt == "date_created_to") {
        this.cbFilter.data.created.to = this.formatDateMDY(event);
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
        "Dec.",
      ];
      return [mstring[month - 1], day, year].join(" ");
    },
    checkAreas() {
      this.$http
        .get("api/checkAreas/" + this.cbFilter.data.region_id)
        .then((response) => {
          console.log(response.body);
          this.cbFilter.data.area_id = response.body;
          this.cbFilter.area = true;
        });
    },
  },
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
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
