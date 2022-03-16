<template>
  <div class="mx-auto col-md-12 modified-continer" style>
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Job Order
          <button
            @click="openModalAdd"
            v-if="roles.create_job_order"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Create Job Order
          </button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div style="display: flex">
          <div style="width: 60%">
            <!-- search -->
            <b-row class="searchRow">
              <b-col>
                <b-form-group>
                  <b-input-group>
                    <model-list-select
                      style="width: 25%"
                      :list="searchby_list"
                      v-model="searchby"
                      option-value="id"
                      option-text="name"
                      placeholder="Select Filter"
                    ></model-list-select>

                    <b-form-input
                      id="txtbox_filter"
                      style="
                        height: 30px;
                        margin-left: 5px;
                        border-radius: 5px 0 0 5px;
                      "
                      v-model="tblFilter_copy"
                      v-on:keyup.enter="search_data"
                      placeholder="Search"
                    ></b-form-input>

                    <b-input-group-append>
                      <b-button
                        @click="filterClear"
                        style="
                          width: 100px;
                          color: white;
                          border-radius: 0 5px 5px 0;
                        "
                        >Clear</b-button
                      >
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row class="searchRow">
              <b-col class="clientCol">
                <label style="float: left; padding-left: 7px; margin-top: 3px"
                  >Filter by Date Finished</label
                >
              </b-col>
              <b-col style="margin-left: 1px; margin-top: -4px">
                <b-form-group>
                  <b-input-group>
                    <rangedate-picker
                      @selected="onDateSelected"
                    ></rangedate-picker>
                    <button
                      @click="fnExcelReport('JobOrderTable')"
                      type="button"
                      class="btn btn-success"
                      style="
                        width: 100px;
                        color: white;
                        border-radius: 0 5px 5px 0;
                        margin-left: -1px;
                      "
                    >
                      Export
                    </button>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
          <!-- forecast -->
          <div style="width: 40%">
            <b-card
              border-variant="default"
              align="center"
              style="width: 100%; float: right; margin-right: 15px"
              class="trend-bcard"
              bg-variant="light"
            >
              <header>
                <p style="font-weight: bold">INSTALLATION FORECAST FOR TODAY</p>
              </header>

              <div class="top-records" style="display: flex; width: 100%">
                <div style="width: 50%">
                  <b-card no-body class="text-center">
                    <div class="text font-weight-bold">
                      <h5>{{ pendingTrend }}</h5>

                      <p>Pending Activations</p>
                    </div>
                  </b-card>
                </div>
                <div style="width: 50%">
                  <b-card no-body class="text-center">
                    <div class="text font-weight-bold">
                      <h5>{{ activatedTrend }}</h5>

                      <p>Activated for the Month</p>
                    </div>
                  </b-card>
                </div>
              </div>
            </b-card>

            <br />
          </div>
        </div>

        <!-- page options -->
        <div
          class="row marginice"
          style="width: 20%; float: left; margin-left: 0"
        >
          <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
        </div>
        <div class="row marginice" style="width: 10%; float: right">
          <b-row>
            <b-col>
              <label style="float: right; margin-top: 10px">Show</label>
            </b-col>
            <b-col style="width: 80%">
              <b-form-group class="mb-0">
                <b-form-select
                  style="height: 30px; font-size: 12px"
                  v-model="perPage"
                  :options="pageOptions"
                ></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
        </div>

        <!-- Scheduler table -->

        <div style="margin-top: 10px">
          <b-table
            id="JobOrderTable"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="fields"
            :items="items"
            :filter="tblFilter"
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            :tbody-tr-class="tblRowClass"
            thead-class="cursorPointer-th"
            head-variant=" elClr"
            @filtered="onFiltered"
            @row-clicked="tblRowClicked"
            @head-clicked="tblHeadClicked"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
        </div>
      </div>
      <!-- modalEdit ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalEdit"
        header-text-variant="dark"
        body-text-variant="dark"
        footer-text-variant="dark"
        :body-bg-variant="' modal-modi-bg'"
        :header-bg-variant="' modal-modi-bg'"
        :footer-bg-variant="' modal-modi-bg'"
        size="lg"
        title="Manage Job Order"
      >
        <!-- form -->
        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Project Description:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>{{ Job_Order.project_description }}</b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Job Order #:</p>
          </div>
          <div class="col-lg-9">
            <b>{{ Job_Order.jo_num }}</b>
          </div>
        </div>

        <div
          class="rowFields mx-auto row stripe client"
          v-if="Job_Order.client_id != '0'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>
                {{ Job_Order.client.name }}
                <span v-if="Job_Order.client_details != null">
                  <span v-if="Job_Order.client_details.line_transfer == 1"
                    >- Line Transfer</span
                  >
                </span>
              </b>
            </p>
          </div>
        </div>

        <div
          class="rowFields mx-auto row client"
          v-if="Job_Order.client_id != '0'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Address:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>{{ Job_Order.client.location }}</b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client" v-if="user.id == '1'">
          <div class="col-lg-3">
            <p class="textLabel">client_detail_id :</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="Job_Order.client_detail_id"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div
          class="rowFields mx-auto row client"
          v-if="Job_Order.client_id == '0'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Details :</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input details"
              placeholder="Details"
              v-model.trim="Job_Order.details"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div
          class="rowFields mx-auto row stripe client"
          v-if="Job_Order.client_id == '0'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Location :</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input location"
              placeholder="Location"
              v-model.trim="Job_Order.location"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Cable Category:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="cableCategoryOption"
              v-model="Job_Order.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">FOC Length(meter):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="foc_length"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-model.trim="Job_Order.foc_length"
              v-on:keyup.enter="btnUpdate"
              v-validate="'between:0,9000000'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('foc_length')"
              >Input valid number.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Region:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="regions"
              v-model="Job_Order.region_id"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Region..."
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Date started:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="Job_Order.started"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Date finished:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="Job_Order.finished"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Tech In-Charge:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.engineer_in_charge"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Prepared by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.prepare"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Approved by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.approve"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Noted by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.note"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="info" @click="printPreview"
            >Print Preview</b-button
          >

          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_job_order"
            @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_job_order"
            @click="btnDelete()"
            >Delete</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!--modalAdd-------->
      <b-modal
        id="modalAdd"
        header-text-variant="dark"
        body-text-variant="dark"
        footer-text-variant="dark"
        :body-bg-variant="' modal-modi-bg'"
        :header-bg-variant="' modal-modi-bg'"
        :footer-bg-variant="' modal-modi-bg'"
        size="lg"
        title="Job Order Form"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Project Description:</p>
          </div>
          <div class="col-lg-9" style="margin-top: 5px">
            <p-radio
              class="p-icon p-curve p-jelly"
              name="radio11"
              color="success"
              v-model="Job_Order.project_description"
              :value="'Restoration'"
            >
              <i slot="extra" class="icon fas fa-check"></i>
              Restoration
            </p-radio>
            <p-radio
              class="p-icon p-curve p-jelly"
              name="radio11"
              color="success"
              v-model="Job_Order.project_description"
              :value="'Installation'"
            >
              <i slot="extra" class="icon fas fa-check"></i>
              Installation
            </p-radio>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row" >
          <div class="col-lg-3">
            <p class="textLabel">Job Order #:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input Job #(leave empty for auto increment)"
              placeholder="Job order number"
              v-model.trim="Job_Order.jo_num"
            />
          </div>
        </div>-->

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Type:</p>
          </div>
          <div class="col-lg-9" style="margin-top: 5px">
            <p-radio
              class="p-icon p-curve p-jelly"
              name="radio66"
              color="success"
              v-model="Job_Order.job_order_type"
              :value="'Account'"
            >
              <i slot="extra" class="icon fas fa-check"></i>
              by Account
            </p-radio>
            <p-radio
              class="p-icon p-curve p-jelly"
              name="radio66"
              color="success"
              v-model="Job_Order.job_order_type"
              :value="'Area'"
            >
              <i slot="extra" class="icon fas fa-check"></i>
              by Area
            </p-radio>
          </div>
        </div>

        <div
          class="rowFields mx-auto row stripe client"
          v-if="Job_Order.job_order_type == 'Account'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="Job_Order.client_id"
              option-value="id"
              :custom-text="getClientDesc"
              placeholder="Select/Search a client (Account name - region)"
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row client"
          v-if="Job_Order.job_order_type == 'Area'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Details :</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input details"
              placeholder="Details"
              v-model.trim="Job_Order.details"
            />
          </div>
        </div>

        <div
          class="rowFields mx-auto row stripe client"
          v-if="Job_Order.job_order_type == 'Area'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Location :</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input location"
              placeholder="Location"
              v-model.trim="Job_Order.location"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Cable Category:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="cableCategoryOption"
              v-model="Job_Order.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">FOC Length(meter):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="foc_length"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-model.trim="Job_Order.foc_length"
              v-validate="'between:0,9000000'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('foc_length')"
              >Input valid number.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Region:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="regions"
              v-model="Job_Order.region_id"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Region..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Date started:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="Job_Order.started"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Date finished:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="Job_Order.finished"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Tech In-Charge:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.engineer_in_charge"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Prepared by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.prepare"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Approved by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.approve"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Noted by:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="engineers"
              v-model="Job_Order.note"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
            ></model-list-select>
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!--modalAdd-------->
      <!-- scheduler table footer -->
      <div class="elClr panel-footer">
        <div class="row" style="background-color: ; padding: 15px">
          <div class="col-md-8" style="background-color: ">
            <span class="elClr">{{ totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color: ">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              class="my-0 pull-right"
            ></b-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    "rangedate-picker": VueRangedatePicker,
  },
  data() {
    return {
      tblisBusy: true,
      sortBy: "",
      sortDesc: "",
      fields: [
        { key: "jo_num", label: "J.O. #", sortable: true },
        {
          key: "client_id",
          label: "Client ID",
          sortable: true,
        },
        { key: "client_detail_id", sortable: true },
        { key: "project_description", label: "Description", sortable: true },
        { key: "client.name", label: "Account name", sortable: true },

        {
          key: "client.location",
          label: "Address",
          formatter: (value) => {
            var temp = "";
            if (value != null) {
              if (value.length > 50) temp = "...";
              return value.slice(0, 50) + temp;
            } else return "";
          },
          sortable: true,
        },

        { key: "client.contact", label: "Contact", sortable: true },
        { key: "cable_category", label: "Cable cat.", sortable: true },
        { key: "foc_length", label: "Distance", sortable: true },
        { key: "engineer_in_charge.name", label: "In-charge", sortable: true },
        {
          key: "date_finished_formated",
          label: "Date Finished",
        },
      ],
      items: [],
      base_items: [],
      tblFilter: null,
      tblFilter_copy: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [6, 10, 20, 100, 500],

      clients: [],
      user: [],
      pendingTrend: 0,
      activatedTrend: 0,
      engineers: [],
      regions: [],
      focLayoutOption: [
        { id: "Completed", name: "Completed" },
        { id: "Outdoor layout done", name: "Outdoor layout done" },
        { id: "Indoor layout done", name: "Indoor layout done" },
      ],
      otcOption: [
        { id: "Paid", name: "Paid" },
        { id: "Promo", name: "Promo" },
        { id: "Billing", name: "Billing" },
        { id: "Waived", name: "Waived" },
        { id: "Waiting for C&C advisory", name: "Waiting for C&C advisory" },
        { id: "NTP", name: "NTP" },
      ],
      cableCategoryOption: [
        { id: "Drop Fiber", name: "Drop Fiber" },
        { id: "Hard Fiber", name: "Hard Fiber" },
        { id: "UTP", name: "Unshielded twisted pair (UTP)" },
      ],
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false,
      },
      Job_Order: {
        client: {
          name: "",
          location: "",
        },
        client_details: {
          line_transfer: null,
        },
        id: null,
        jo_num: null,
        client_id: 0,
        details: null,
        location: null,
        cable_category: "",
        foc_length: "",
        region_id: 1,
        project_description: "Restoration",
        job_order_type: "Account",
        started: null,
        finished: new Date(),
        engineer_in_charge: "",
        prepare: 3,
        approve: 2,
        note: 1,
      },
      roles: [],

      searchby_list: [
        {
          name: "Account Name(by account)",
          id: "clients.name",
        },
        {
          name: "Account Name(by area)",
          id: "details",
        },
        {
          name: "Address (by account)",
          id: "clients.location",
        },
        {
          name: "Address (by area)",
          id: "job_orders.location",
        },
        {
          name: "J.O num (last 4 digit)",
          id: "job_orders.jo_num",
        },
      ],
      searchby: "clients.name",
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.engineers = this.$global.getEngineer();
    this.regions = this.$global.getRegion();
    this.roles = this.$global.getRoles();

    this.load_item();
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load_item() {
      this.$http
        .get("api/JobOrder/subIndex/" + this.user.region_id)
        .then(function (response) {
          console.log(response.body);

          this.items = response.body;
          this.base_items = this.items;
          this.totalRows = this.items.length;
          this.tblisBusy = false;
          this.changeColDisplay("");
        });
    },
    load() {
      this.$nextTick(function () {
        setTimeout(function () {
          document.getElementById("accountsMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarCalendar").style.backgroundColor = "";
          document.getElementById("navbarTicket").style.backgroundColor = "";
          document.getElementById("navbarMap").style.backgroundColor = "";
          document.getElementById("navbarInstallation").style.backgroundColor =
            "#2196f3";
          document.getElementById("navbarComponents").style.backgroundColor =
            "";
          document.getElementById("navbarAccounts").style.backgroundColor = "";
        }, 100);
      });

      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function (response) {
          this.clients = response.body;
        });

      this.$http
        .post("api/clientDetail/getTrend/" + this.user.region_id)
        .then((response) => {
          console.log(response.body);
          this.pendingTrend = response.body.pendingTrend;
          this.activatedTrend = response.body.activatedTrend;
        });
    },
    getClientDesc(client) {
      return `${client.name} - ${client.region_name}`;
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      if (this.roles.update_job_order) {
        this.$bvModal.show("modalEdit");
        this.Job_Order = item;
        console.log(item);
      }
    },
    tblHeadClicked(key, field, event) {
      if (key == "date_finished_formated") {
        var temp = !this.sortDesc;
        this.sortBy = null;
        this.sortDesc = null;

        this.sortDesc = temp;
        this.sortBy = "finished";
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    openModalAdd() {
      this.$bvModal.show("modalAdd");
      this.Job_Order = {
        client: {
          name: "",
          location: "",
        },
        client_details: {
          line_transfer: null,
        },
        id: null,
        jo_num: null,
        client_id: 0,
        details: null,
        location: null,
        cable_category: "",
        foc_length: "",
        region_id: 1,
        project_description: "Restoration",
        job_order_type: "Account",
        started: null,
        finished: new Date(),
        engineer_in_charge: "",
        prepare: 3,
        approve: 2,
        note: 1,
      };

      this.$http
        .get("api/JobOrder/getMaxID/" + this.user.region_id)
        .then((response) => {
          var a = Number(response.body);
          this.Job_Order.jo_num = a + 1;
          // console.log(this.Job_Order.jo_num);
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
    btnCreate() {
      swal("not available right not");

      this.Job_Order.started = this.formatDate(this.Job_Order.started);
      this.Job_Order.finished = this.formatDate(this.Job_Order.finished);
      if (false)
        if (this.Job_Order.engineer_in_charge != "") {
          swal({
            title: "Are you sure?",
            text: "Do you really want to create job order?",
            icon: "info",
            buttons: ["No", "Yes"],
          }).then((yes) => {
            if (yes) {
              this.tblisBusy = true;
              this.Job_Order.user_id = this.user.id;
              this.Job_Order.user_name = this.user.name;
              this.Job_Order.region_id1 = this.user.region_id;
              this.$http
                .post("api/JobOrder", this.Job_Order)
                .then((response) => {
                  console.log(response.body);
                  this.tblisBusy = false;
                  this.$bvModal.hide("modalAdd");
                  this.items = response.body;
                  swal("Created", "", "success");
                  this.Job_Order = {
                    client: {
                      name: "",
                      location: "",
                    },
                    client_details: {
                      line_transfer: null,
                    },
                    id: null,
                    client_id: 0,
                    details: null,
                    location: null,
                    cable_category: "",
                    foc_length: "",
                    region_id: 1,
                    project_description: "Restoration",
                    job_order_type: "Account",
                    started: null,
                    finished: new Date(),
                    engineer_in_charge: "",
                    prepare: 3,
                    approve: 2,
                    note: 1,
                  };

                  this.base_items = this.items;
                });
            }
          });
        } else {
          swal({
            title: "Info",
            text: "Please select Engineer in-charge",
            icon: "info",
            dangerMode: true,
          }).then((value) => {
            if (value) {
            }
          });
        }
    },
    btnUpdate() {
      console.log(this.Job_Order);
      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            this.Job_Order.started = this.formatDate(this.Job_Order.started);
            if (this.Job_Order.finished != null)
              this.Job_Order.finished = this.formatDate(
                this.Job_Order.finished
              );
            this.Job_Order.user_id = this.user.id;
            this.Job_Order.user_name = this.user.name;
            if (update) {
              this.Job_Order.update_in = "jo";
              this.$http
                .put("api/JobOrder/" + this.Job_Order.id, this.Job_Order)
                .then((response) => {
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalEdit");
                  this.base_items = this.items;
                });
            }
          });
        }
      });
    },
    btnDelete() {
      console.log(this.Job_Order);
      if (this.roles.delete_job_order) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            this.items = [];
            this.tblisBusy = true;
            this.Job_Order.user_id = this.user.id;
            this.Job_Order.user_name = this.user.name;
            this.Job_Order.region_id = this.user.region_id;
            this.$http
              .post("api/jobOrder/destroy1", this.Job_Order)
              .then((response) => {
                this.$bvModal.hide("modalEdit");
                swal("Deleted", "", "success");
                this.items = response.body;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
              })
              .catch((response) => {
                swal({
                  title: "Error",
                  text: response.body.error,
                  icon: "error",
                  dangerMode: true,
                }).then((value) => {
                  if (value) {
                  }
                });
              });
          }
        });
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a Sales",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        });
      }
    },
    onDateSelected(daterange) {
      this.tblisBusy = true;
      this.$http
        .post("api/JobOrder/filterByDate", daterange)
        .then((response) => {
          this.items = response.body;
          this.totalRows = this.items.length;
          this.tblisBusy = false;
        });
    },
    changeColDisplay(check) {
      this.fields = [
        { key: "jo_num", label: "J.O. #", sortable: true },

        { key: "project_description", label: "Description", sortable: true },
        {
          key: "client.name",
          label: "Account name",
          formatter: (value, key, item) => {
            if (item.client_details != null) {
              if (item.client_details.line_transfer == 1)
                return item.client.name + " - Line Transfer";
              else return item.client.name;
            } else return item.client.name;
          },
          sortable: true,
        },

        {
          key: "client.location",
          label: "Address",
          formatter: (value) => {
            var temp = "";
            if (value != null) {
              if (value.length > 30) temp = "...";
              return value.slice(0, 30) + temp;
            } else return "";
          },
          sortable: true,
        },

        { key: "client.contact", label: "Contact", sortable: true },
        { key: "cable_category", label: "Cable cat.", sortable: true },
        { key: "foc_length", label: "Distance", sortable: true },
        { key: "engineer_in_charge.name", label: "In-charge", sortable: true },
        {
          key: "date_finished_formated",
          label: "Date Finished",
        },
      ];

      if (this.user.id == 1) {
        var temp = {
          key: "client_id",
          label: "Client ID",
          sortable: true,
        };
        var temp1 = { key: "client_detail_id", sortable: true };
        this.fields.splice(2, 0, temp, temp1);
      }
    },
    filterClear() {
      this.tblFilter = "";
      this.tblFilter_copy = "";
      this.items = this.base_items;
      //  this.totalRows = this.items.length;
    },
    clearFilter() {
      this.tblFilter = "";
      this.tblFilter_copy = "";
      this.items = this.items_copy;
      this.totalRows = this.items.length;
    },
    search_data() {
      this.$http
        .get(
          "api/jobOrder/search_data/" +
            this.searchby +
            "/" +
            this.tblFilter_copy
        )
        .then((response) => {
          console.log(response.body);
          this.items = response.body;

          this.totalRows = this.items.length;
        })
        .catch((response) => {
          console.log(response);
        });
    },

    printPreview() {
      var newWin = window.open("");
      var linetransfer = " - Line Transfer";
      if (this.Job_Order.client_details != null) {
        if (this.Job_Order.client_details.line_transfer == 1)
          linetransfer = " - Line Transfer";
        else linetransfer = "";
      } else linetransfer = "";
      var approve_sign = "";
      if (this.Job_Order.approve.signature != null) {
        approve_sign =
          "<div style='margin-top: -15px; margin-bottom: -20px; z-index: -1;'><img src='" +
          this.$img_path +
          this.Job_Order.approve.signature +
          "' width='100' height='50' style='position: relative; z-index: -1;' /></div>";
      }
      var prepare_sign = "";
      if (this.Job_Order.prepare.signature != null) {
        prepare_sign =
          "<div style='margin-top: -15px; margin-bottom: -20px; z-index: -1;'><img src='" +
          this.$img_path +
          this.Job_Order.prepare.signature +
          "' width='100' height='50' style='position: relative; z-index: -1;' /></div>";
      }

      var note_sign = "";
      if (this.Job_Order.note.signature != null) {
        note_sign =
          "<div style='margin-top: -15px; margin-bottom: -20px; z-index: -1;'><img src='" +
          this.$img_path +
          this.Job_Order.note.signature +
          "' width='100' height='50' style='position: relative; z-index: -1;' /></div>";
      }

      var engineer_in_charge_sign = "";
      if (this.Job_Order.engineer_in_charge.signature != null) {
        engineer_in_charge_sign =
          "<div style='margin-top: -15px; margin-bottom: -20px; z-index: -1;'><img src='" +
          this.$img_path +
          this.Job_Order.engineer_in_charge.signature +
          "' width='100' height='50' style='position: relative; z-index: -1;' /></div>";
      }

      if (this.Job_Order.client.contact == null)
        this.Job_Order.client.contact = "";

      var location;
      if (this.Job_Order.client.location != null)
        location = this.Job_Order.client.location;
      else location = this.Job_Order.location;

      newWin.document.write("<html><head>");

      newWin.document.write(
        '<style type="text/css" >' +
          "body{margin:0px;}" +
          ".center {text-align: center;}" +
          ".center1{ margin: 0; position: absolute; left: 50%; transform: translate(-50%);}" +
          "p {margin-top:-10; font-size: 10px;}" +
          ".borderbot-05 { border-bottom: solid black 0.5px;}" +
          ".borderbot-1 { border-bottom: solid black 1px;}" +
          ".bordertop-1 { border-top: solid black 1px;}" +
          ".bordertop-2 { border-top: solid black 2px;}" +
          ".borderleft-2 { border-left: solid black 2px;}" +
          "@media print { @page {size: A4 landscape } }" +
          ".row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px; padding-right:10px; padding-left:10px;}" +
          // "col-md-6{position:relative;width:100%;padding-right:15px;padding-left:15px}" +
          ".col-md-6{-ms-flex:0 0 48%;flex:0 0 48%;max-width:48%; padding-right:10px; padding-left:10px;}" +
          ".col-md-5{-ms-flex:0 0 44%;flex:0 0 44%;max-width:44%; padding-right:10px; padding-left:10px;}" +
          ".col-md-8{-ms-flex:0 0 72%;flex:0 0 72%;max-width:70%; padding-right:10px; padding-left:10px;}" +
          ".col-md-2{-ms-flex:0 0 20%;flex:0 0 20%;max-width:20%; padding-right:10px; padding-left:10px;}" +
          ".col-md-7{-ms-flex:0 0 62%;flex:0 0 62%;max-width:60%; padding-right:10px; padding-left:10px;}" +
          ".col-md-3{-ms-flex:0 0 30%;flex:0 0 30%;max-width:30%; padding-right:10px; padding-left:10px;}" +
          ".container{ font-size: 12px; width:100%; padding-right:15px; padding-left:15px; margin-right:auto; margin-left:auto}" +
          // "" +
          "</style>"
      );

      newWin.document.write("</head><body>");
      newWin.document.write(
        "<div>" +
          '<div class="row">' +
          '<div class="col-md-6">' + // first col
          '<div style="text-align: center;" >' +
          "<b>Dctech Micro Services Inc.</b>" +
          "<br>Dctech Bldg. Ponciano Reyes St. Davao City" +
          "<br>Tel. #: 082-221-2380 / Fax #: 082-221-2382" +
          "</div>" +
          "<br>" +
          // "<br>" +
          '<div class="borderbot-1 bordertop-2" style="text-align: center;">' +
          "<span><b>SERVICE ORDER FORM</b></span>" +
          "</div>" +
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5">' +
          "Project Description: <span class='borderbot-05'>" +
          this.Job_Order.project_description +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: right;">' +
          "Control #: <span class='borderbot-05'>" +
          this.Job_Order.jo_num +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          "<br>" +
          '<div class="borderbot-1 bordertop-1" style="text-align: center;">' +
          "<span><b>CLIENT INFORMATION</b></span>" +
          "</div>" +
          "<br>" +
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Account Name:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.client.name +
          linetransfer +
          "," +
          this.Job_Order.client.contact +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Address:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          location +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Started:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.date_started_formated +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Finished:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.date_finished_formated +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="borderbot-1 bordertop-1"  style="text-align: center;">' +
          "<span><b>SERVICE REPORT</b></span>" +
          "</div>" +
          "<br>" +
          //SERVICE REPORT ----- SERVICE REPORT ----- SERVICE REPORT----- SERVICE REPORT
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Dctech Region:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.region.name +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Cable Category:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.cable_category +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Estimated Distance(m):" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.foc_length +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Actual Distance(m):" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          "" +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Rx Power:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          "" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="borderbot-1" style="text-align: center;">' +
          "</div>" +
          "<br>" +
          // "<br>" +
          //Engineer In-Charge:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Engineer In-Charge:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          engineer_in_charge_sign +
          this.Job_Order.engineer.user.name +
          "</div>" +
          "</div>" +
          '<div class="row">' +
          '<div class="col-md-3">' +
          "" +
          "</div>" +
          '<div class="col-md-7"  style="text-align: center;">' +
          "Signature over Printed Name" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          // "<br>" +
          //Contractor/Lineman:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Contractor/Lineman:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          "" +
          "</div>" +
          "</div>" +
          '<div class="row">' +
          '<div class="col-md-3">' +
          "" +
          "</div>" +
          '<div class="col-md-7"  style="text-align: center;">' +
          "Signature over Printed Name" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          "<div>Prepared by:</div>" +
          prepare_sign +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          "<div>Approved by:</div>" +
          approve_sign +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.Job_Order.prepare_engineer.user.name +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1">' +
          this.Job_Order.approve_engineer.user.name +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          //
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          this.Job_Order.prepare_engineer.position +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          this.Job_Order.approve_engineer.position +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class=""  style="text-align: center;">' +
          "<div>Noted by:</div>" +
          note_sign +
          "</div>" +
          //
          //
          "<br>" +
          '<div   style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.Job_Order.note_engineer.user.name +
          "</span>" +
          "</div>" +
          '<div class=""  style="text-align: center;">' +
          "<span>" +
          this.Job_Order.note_engineer.position +
          "</span>" +
          "</div>" +
          "</div>" + // end first col
          //
          //
          //
          //
          //second
          '<div class="col-md-6 borderleft-2">' + // second col
          '<div style="text-align: center;" >' +
          "<b>Dctech Micro Services Inc.</b>" +
          "<br>Dctech Bldg. Ponciano Reyes St. Davao City" +
          "<br>Tel. #: 082-221-2380 / Fax #: 082-221-2382" +
          "</div>" +
          "<br>" +
          '<div class="borderbot-1 bordertop-2" style="text-align: center;">' +
          "<span><b>SERVICE ORDER FORM</b></span>" +
          "</div>" +
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5">' +
          "Project Description: <span class='borderbot-05'>" +
          this.Job_Order.project_description +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: right;">' +
          "Control #: <span class='borderbot-05'>" +
          this.Job_Order.jo_num +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          "<br>" +
          '<div class="borderbot-1 bordertop-1" style="text-align: center;">' +
          "<span><b>CLIENT INFORMATION</b></span>" +
          "</div>" +
          "<br>" +
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Account Name:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.client.name +
          linetransfer +
          "," +
          this.Job_Order.client.contact +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Address:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          location +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Started:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.date_started_formated +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Finished:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.date_finished_formated +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="borderbot-1 bordertop-1"  style="text-align: center;">' +
          "<span><b>SERVICE REPORT</b></span>" +
          "</div>" +
          "<br>" +
          //SERVICE REPORT ----- SERVICE REPORT ----- SERVICE REPORT----- SERVICE REPORT
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Dctech Region:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.region.name +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Cable Category:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.cable_category +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Estimated Distance (m)" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.Job_Order.foc_length +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Actual Distance (m)" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          "" +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Rx Power:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          "" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="borderbot-1" style="text-align: center;">' +
          "</div>" +
          "<br>" +
          // "<br>" +
          //Engineer In-Charge:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Engineer In-Charge:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          engineer_in_charge_sign +
          this.Job_Order.engineer.user.name +
          "</div>" +
          "</div>" +
          '<div class="row">' +
          '<div class="col-md-3">' +
          "" +
          "</div>" +
          '<div class="col-md-7"  style="text-align: center;">' +
          "Signature over Printed Name" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          // "<br>" +
          //Contractor/Lineman:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Contractor/Lineman:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          "" +
          "</div>" +
          "</div>" +
          '<div class="row">' +
          '<div class="col-md-3">' +
          "" +
          "</div>" +
          '<div class="col-md-7"  style="text-align: center;">' +
          "Signature over Printed Name" +
          "</div>" +
          "</div>" + //end row2
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          "Prepared by:" +
          prepare_sign +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          "Approved by:" +
          approve_sign +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.Job_Order.prepare_engineer.user.name +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.Job_Order.approve_engineer.user.name +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          //
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          this.Job_Order.prepare_engineer.position +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          this.Job_Order.approve_engineer.position +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class=""  style="text-align: center;">' +
          "<span>Noted by:</span>" +
          note_sign +
          "</div>" +
          //
          //
          "<br>" +
          '<div   style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.Job_Order.note_engineer.user.name +
          "</span>" +
          "</div>" +
          '<div class=""  style="text-align: center;">' +
          "<span>" +
          this.Job_Order.note_engineer.position +
          "</span>" +
          "</div>" +
          "</div>" + // end second col
          //
          "</div>" +
          "</div>"
      );
      newWin.document.write("</body></html>");
      newWin.alert(
        "If the print preview doesn't " +
          "show please disable ad blocker or manually press the Ctrl+p. And don't forget to close this page"
      );
      newWin.print();
    },
    fnExcelReport(tbl) {
      this.currentPage = 1;
      this.perPage = this.totalRows;
      this.$nextTick(function () {
        setTimeout(
          function () {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table

            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
              //tab_text=tab_text+"</tr>";
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
            this.perPage = 6;
            return sa;
          }.bind(this),
          1000
        );
      });
    },
  },
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
.modal-content,
modal-header {
  border: 1px solid red;
}
.searchRow {
  width: 80%;
  margin-left: 5px;
  margin-top: 0;
}
.clientCol {
  border: 1px solid #dfdfe0;
  padding-bottom: 0;
  max-width: 24.5%;
  margin-left: 5px;
  height: 27px !important;
  border-radius: 5px 5px 5px 5px;
  background: rgba(0, 167, 0, 0.193);
}
.trend-bcard {
  color: black;
}

.showPage {
  width: 10%;
  float: left;
  margin-top: -20px;
  margin-left: 0;
}

.stripe {
  background: #f5f9ff;
}

.client {
  font-size: small;
  margin-top: -5px;
}
</style>
