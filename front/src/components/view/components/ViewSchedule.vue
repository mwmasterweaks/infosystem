<template>
  <div class="mx-auto col-md-12 modified-continer" style>
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Schedule
          <button
            @click="reload_data"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Reload data
          </button>

          <button
            @click="OpenModalAdd"
            v-if="roles.create_client_details"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Create schedule
          </button>

          <router-link tag="span" to="/JobOrder" v-if="roles.create_job_order">
            <button
              @click="OpenModalAdd"
              v-if="roles.create_job_order"
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
            >
              Job order
            </button>
          </router-link>
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
                      style="min-width: 150px; max-width: 50px"
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
                        @click="clearFilter"
                        style="
                          width: 100px;
                          color: white;
                          border-radius: 0 5px 5px 0;
                        "
                        >Clear</b-button
                      >
                    </b-input-group-append>
                    <button
                      @click="fnExcelReport('scheduleTable')"
                      type="button"
                      class="btn btn-success"
                      style="width: 100px; color: white; margin-left: 10px"
                    >
                      Export
                    </button>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
            <!-- badges -->
            <b-row class="buttonRow">
              <button
                @click="filterChange('Filter: no_foc_layout_sched')"
                type="button"
                class="
                  btn btn-warning btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                No FOC Layout Sched.
                <b-badge v-if="total_no_foc_layout_sched > 0" variant="info">
                  {{ total_no_foc_layout_sched }}
                </b-badge>
              </button>
              <button
                @click="filterChange('Filter: assign')"
                type="button"
                class="
                  btn btn-info btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                Assign
                <b-badge v-if="totalNeedAssign > 0" variant="info">{{
                  totalNeedAssign
                }}</b-badge>
              </button>
              <button
                @click="filterChange('Filter: wfc')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                WFC
                <b-badge v-if="totalWFC > 0" variant="info">{{
                  totalWFC
                }}</b-badge>
              </button>

              <button
                v-b-modal="'modalMultipleFilter'"
                type="button"
                class="
                  btn btn-success btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                Multiple Filter
              </button>
              <button
                @click="loadTable"
                type="button"
                class="
                  btn btn-success btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
                v-if="!showTable"
              >
                <i class="fa fa-eye" aria-hidden="true"></i> Display Table
              </button>
              <button
                @click="hideTable"
                type="button"
                class="
                  btn btn-success btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
                v-else
              >
                <i class="fa fa-eye-slash" aria-hidden="true"></i> Hide Table
              </button>
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

        <div style="display: flex" v-if="showTable">
          <div
            class="row marginice"
            style="margin-left: 1px; float: left; width: 80%"
          >
            <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
          </div>
          <div class="row marginice" style="width: 8%">
            <b-row>
              <b-col style="float: right; padding-right: 0">
                <button
                  class="btn btn-labeled btn-set"
                  v-b-tooltip.hover
                  title="Show Columns"
                  @click="openColumnSettings"
                >
                  <i
                    class="fas fa-columns"
                    style="font-size: 15px; margin-top: 5px"
                  ></i>
                </button>
              </b-col>
              <b-col style="float: right">
                <b-form-group class="mb-0">
                  <b-form-select
                    v-b-tooltip.hover
                    title="Show Pages"
                    style="height: 30px; font-size: 12px"
                    v-model="perPage"
                    :options="pageOptions"
                  ></b-form-select>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
        </div>
        <!-- Scheduler table -->
        <div
          class="scrollmenu"
          id="scrollmenuContainer"
          @mousemove="mousemove"
          v-if="showTable"
        >
          <div class>
            <b-table
              id="scheduleTable"
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
              thead-tr-class="tblheadtrclass"
              head-variant=" elClr"
              thead-class="cursorPointer-th tblheadtrclass"
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
              <!-- <template v-slot:cell(modem_status)="data">
                <span v-html="data.value"></span>
              </template>-->
              <template v-slot:cell(client1.contract)="data">
                <span v-html="data.value"></span>
              </template>

              <template v-slot:cell(mapping_status)="data">
                <span v-html="data.value"></span>
              </template>

              <!-- <template slot="JobOrder.id" slot-scope="row"> -->
              <template v-slot:cell(JobOrder.id)="row">
                <b-button
                  class="mr-1 jobButton"
                  @click="openModalCreateJobOrder(row.item)"
                  size="sm"
                  v-if="row.item.JobOrder == null && roles.create_job_order"
                  >Create</b-button
                >
                <b-button
                  class="jobButton"
                  variant="success"
                  @click="openModalViewJobOrder(row.item)"
                  v-if="row.item.JobOrder != null && roles.create_job_order"
                  >{{ row.item.JobOrder.jo_num }}</b-button
                >
              </template>

              <!-- <template slot="date_activated" slot-scope="row"> -->
              <template v-slot:cell(date_activated)="row">
                <!-- <b-button
                  variant="success"
                  @click="sendOTP(row.item)"
                  v-if="
                    row.item.date_activated == null &&
                      row.item.status == 'done' &&
                      row.item.otc != null &&
                      row.item.target_date == datenow &&
                      row.item.contract != null
                  "
                  :disabled="!roles.sendcvc"
                >Send</b-button>-->
                <span v-if="roles.helpdesk">
                  <b-button
                    variant="success"
                    @click="openModalUpdateActivatedDate(row.item)"
                    v-if="
                      row.item.date_activated == null &&
                      row.item.status == 'done' &&
                      row.item.otc != null &&
                      row.item.target_date <= datenow &&
                      row.item.contract != null
                    "
                    >Activate</b-button
                  >
                </span>
                <b-button
                  variant="info"
                  v-if="row.item.status == 'finished'"
                  :disabled="!roles.update_client_details"
                  >{{ row.item.date_activated }}</b-button
                >
              </template>
              <template v-slot:cell(status1.target_date)="row">
                <b-button
                  class="instButton"
                  v-if="row.item.status == 'acknowledge'"
                  variant="warning"
                  :disabled="!roles.update_client_details"
                  >acknowledge</b-button
                >

                <b-button
                  class="instButton"
                  v-if="row.item.status == null"
                  variant="danger"
                  :disabled="!roles.update_client_details"
                  @click="wfcClicked(row.item, row.index, $event.target)"
                  >WFC</b-button
                >

                <!-- <span v-if="row.item.status == 'Confirmed'">N/A</span> -->
                <span v-if="row.item.status == 'finished'">
                  <b>Activated</b>
                </span>
                <!-- row.item.team_assigned == 1 && row.item.target_date == null -->
                <b-button
                  class="instButton"
                  v-if="row.item.target_date == null && row.item.status != null"
                  variant="info"
                  @click="openModalUpdateTargetDate(row.item)"
                  :disabled="!roles.update_client_details"
                  >Assign</b-button
                >

                <b-button
                  class="instButton"
                  @click="openModalUpdateTargetDate(row.item)"
                  variant="success"
                  v-if="
                    row.item.target_date != null &&
                    row.item.target_date != datenow &&
                    row.item.target_date != dateTomorrow &&
                    row.item.target_date != dateYesterday &&
                    1 > dateDiffInDays(datenow, row.item.target_date)
                  "
                  :disabled="!roles.rm"
                  >{{ row.item.status1.target_date }}</b-button
                >

                <b-button
                  class="instButton"
                  @click="openModalUpdateTargetDate(row.item)"
                  variant="success"
                  v-if="
                    row.item.target_date == datenow &&
                    row.item.status != 'finished'
                  "
                  :disabled="!roles.rm"
                  >Today</b-button
                >

                <b-button
                  class="instButton"
                  @click="openModalUpdateTargetDate(row.item)"
                  variant="success"
                  v-if="
                    row.item.target_date == dateTomorrow &&
                    row.item.status != 'finished'
                  "
                  :disabled="!roles.rm"
                  >Tomorrow</b-button
                >
                <b-button
                  class="instButton"
                  @click="openModalUpdateTargetDate(row.item)"
                  variant="danger"
                  v-if="
                    row.item.target_date == dateYesterday &&
                    row.item.target_date != null &&
                    row.item.status != 'finished'
                  "
                  :disabled="!roles.rm"
                  >Yesterday</b-button
                >
                <b-button
                  class="instButton"
                  @click="openModalUpdateTargetDate(row.item)"
                  variant="danger"
                  v-if="
                    1 < dateDiffInDays(datenow, row.item.target_date) &&
                    row.item.target_date != null &&
                    row.item.status != 'finished'
                  "
                  :disabled="!roles.rm"
                >
                  {{ dateDiffInDays(datenow, row.item.target_date) }} Days delay
                </b-button>
              </template>

              <!-- <template slot="headName" slot-scope="data" id="headWithArrow">
                <em>{{ data.label }}</em>
                <span v-if="sortDesc">
                  <i class="fas fa-arrow-up"></i>
                </span>
                <span v-else>
                  <i class="fas fa-arrow-down"></i>
                </span>
              </template>-->

              <template slot="table-caption"></template>
            </b-table>
          </div>
        </div>
      </div>
      <!-- scheduler table footer -->
      <div class="elClr panel-footer" v-if="showTable">
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

      <!--modalAdd-------->
      <b-modal
        id="modalAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        title
      >
        <div slot="modal-header">
          <div class="rowFields mx-auto row">
            <div class="col-lg-6">
              <h5>Installation Form</h5>
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="instFormType"
                value="0"
                name="instFormType"
                color="success-o"
                >New</p-radio
              >
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="instFormType"
                value="1"
                name="instFormType"
                color="success-o"
                >Line Transfer</p-radio
              >
            </div>
          </div>
        </div>

        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="clientSelected"
              option-value="id"
              :custom-text="getClientDesc"
              placeholder="Select/Search a client (Account name - region)"
              name="client_id"
              v-validate="'required'"
              @input="onChangeClientId"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('client_id')"
              >Please select / search a account name or location.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">FOC:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="cableCategoryOption"
              v-model="client_details.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Mapping Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.mapping_status"
            >
              <i class="fas fa-check" v-show="client_details.mapping_status" />
              <i class="fas fa-times" v-show="!client_details.mapping_status" />
            </p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Length(m):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="foc_length"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-model.trim="client_details.foc_length"
              v-on:keyup.enter="btnCreate"
              v-validate="'between:0,9000000'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('foc_length')"
              >Input valid number.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Contractor Status:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="focLayoutOption"
              v-model="client_details.foc_layout"
              option-value="id"
              option-text="name"
              placeholder="Select Contractor Status (optional)"
              v-on:keyup.enter="btnCreate"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Layout Status :</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="layoutStatOption"
              v-model="client_details.layout_status"
              option-value="id"
              option-text="name"
              placeholder="Select Layout Status (optional)"
              v-on:keyup.enter="btnCreate"
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="client_details.foc_layout != 'Completed'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Layout Remarks:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Layout remarks"
              placeholder="Layout Remarks"
              v-model.trim="client_details.layout_remarks"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">OTC:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="otcOption"
              v-model="client_details.otc"
              option-value="id"
              option-text="name"
              placeholder="Select OTC (optional)"
              v-on:keyup.enter="btnCreate"
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="
            client_details.otc == 'Paid' ||
            client_details.otc == 'Promo' ||
            client_details.otc == 'Billing' ||
            client_details.otc == 'Waived' ||
            client_details.otc == 'NTP'
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Aging started date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client_details.aging"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Modem Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.modem_status"
            >
              <i class="fas fa-check" v-show="client_details.modem_status" />
              <i class="fas fa-times" v-show="!client_details.modem_status" />
            </p-check>
          </div>
        </div>-->

        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Applied date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client_details.applied_date"
                :config="AppliedDateoptions"
                autocomplete="off"
                v-on:keyup.enter="btnCreate"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Installation Remarks:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Installation remarks"
              placeholder="Installation Remarks"
              v-model.trim="client_details.inst_remarks"
            />
          </div>
        </div>

        <!--Form-------->
        <div slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </div>
      </b-modal>
      <!--modalAdd-------->

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
        title="Manage Schedule"
        @ok="handleOk"
      >
        <!-- form -->

        <!-- <div class="rowFields mx-auto row" v-if="user.id == 1">
          <div class="col-lg-3">
            <p class="textLabel">ID: ({{ client_details.id }})</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="client_details.id_update"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row" v-if="user.id == 1">
          <div class="col-lg-3">
            <p class="textLabel">client_id</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="client_details.client_id"
            />
          </div>
        </div>-->

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <b>
              <p class="text-success textLabel">
                {{ client_details.client1.name }}
              </p>
            </b>
          </div>
        </div>
        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Address:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_details.client1.location }}</p>
          </div>
        </div>
        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Contact:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_details.client1.contact }}</p>
          </div>
        </div>
        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">FOC:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_details.cable_category }}</p>
            <!-- <model-list-select
              :list="cableCategoryOption"
              v-model="client_details.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
            ></model-list-select>-->
          </div>
        </div>
        <!-- Mapping Status -->
        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Mapping Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.mapping_status"
            >
              <i class="fas fa-check" v-show="client_details.mapping_status" />
              <i class="fas fa-times" v-show="!client_details.mapping_status" />
            </p-check>
          </div>
        </div>
        <!-- Length -->
        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Length(m):</p>
          </div>
          <div class="col-lg-9">
            <!-- <p class="textLabel">{{ client_details.foc_length }}</p> -->
            <input
              type="number"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-on:keyup.enter="btnUpdate"
              v-model.trim="client_details.foc_length"
            />
          </div>
        </div>
        <!-- FOC Layout Schedule -->
        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">FOC Layout Schedule:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client_details.foc_schedule"
                :config="AppliedDateoptions"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
                v-if="client_details.foc_sched_copy == null || roles.rm"
              ></date-picker>
              <p class="textLabel" v-else>{{ client_details.foc_schedule }}</p>
            </div>
          </div>
        </div>
        <!-- FOC Layout -->
        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">Contractor Status:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="focLayoutOption"
              v-model="client_details.foc_layout"
              v-on:keyup.enter="btnUpdate"
              option-value="id"
              option-text="name"
              placeholder="Select Contractor status "
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Layout Status :</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="layoutStatOption"
              v-model="client_details.layout_status"
              option-value="id"
              option-text="name"
              placeholder="Select Layout Status (optional)"
              v-on:keyup.enter="btnCreate"
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row client"
          v-if="client_details.foc_layout != 'Completed'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Layout Remarks:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Layout remarks"
              placeholder="Layout Remarks"
              v-model.trim="client_details.layout_remarks"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row stripe client">
          <div class="col-lg-3">
            <p class="textLabel">OTC:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_details.otc }}</p>
            <!-- <model-list-select
              :list="otcOption"
              v-model="client_details.otc"
              option-value="id"
              option-text="name"
              placeholder="Select OTC (optional)"
              v-on:keyup.enter="btnUpdate"
            ></model-list-select>-->
          </div>
        </div>
        <div class="rowFields mx-auto row client">
          <div class="col-lg-3">
            <p class="textLabel">Contract:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel" v-if="client_details.contract != null">
              Completed
            </p>
            <p class="textLabel" v-else>Pending</p>
          </div>
        </div>

        <div
          class="rowFields mx-auto row stripe client"
          v-if="
            client_details.otc == 'Paid' ||
            client_details.otc == 'Promo' ||
            client_details.otc == 'Billing' ||
            client_details.otc == 'Waived' ||
            client_details.otc == 'NTP'
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Aging started date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <p class="textLabel">{{ client_details.aging }}</p>
              <!-- <date-picker
                v-model="client_details.aging"
                :config="AppliedDateoptions"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              ></date-picker>-->
            </div>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Modem Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.modem_status"
            >
              <i class="fas fa-check" v-show="client_details.modem_status" />
              <i class="fas fa-times" v-show="!client_details.modem_status" />
            </p-check>
          </div>
        </div>-->

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-2"></div>
          <div class="col-lg-8">
            <center>
              <p
                style="color:red"
                v-if="client_details.client1.lat == null"
              >Account/Client no coordinates set.</p>
            </center>
            <GmapMap
              ref="gmapclient"
              :center="coordinates"
              :zoom="13"
              map-type-id="satellite"
              style="width: 100%; height: 500px"
              :clickable="true"
              @click="map_clicked"
            >
              <gmap-circle
                :key="m.name"
                v-for="m in markers_edit"
                v-bind:center.sync="m.position"
                v-bind:fillColor="'#AA0000'"
                v-bind:radius="650"
                v-bind:clickable="true"
              ></gmap-circle>

              <GmapMarker
                :key="index"
                v-for="(m, index) in markers_edit"
                :position.sync="m.position"
                :clickable="true"
                :draggable="true"
                :animation="2"
                :label="m.label"
                @click="openWindow($event.latLng, m, index, 'marker')"
              />

              <GmapMarker
                :key="m.name"
                v-for="(m, index) in nearClosure.filter(
                  m => m.dp_group_id != 1
                )"
                :position.sync="m.position"
                :clickable="true"
                :animation="2"
                :icon="{
                  url: require('../../../img/marker_closure.png'),
                  scaledSize: { width: 28, height: 38 },
                  labelOrigin: { x: 10, y: -10 }
                }"
                :label="{ text: m.name, color: 'white', fontSize: '15px' }"
                @click="openWindow($event.latLng, m, index, 'closure')"
              />
            </GmapMap>
          </div>
          <div class="col-lg-2"></div>
        </div>-->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks</p>
          </div>
          <div class="col-lg-9">
            <textarea
              autocomplete="off"
              autocorrect="off"
              autocapitalize="off"
              spellcheck="false"
              rows="5"
              name="remarks"
              class="form-control"
              v-b-tooltip.hover
              title="Input Remarks "
              placeholder="Remarks"
              v-model.trim="remarksText"
            ></textarea>
            <br />
            <button
              @click="addRemarks_clicked"
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
            >
              Add Remarks
            </button>
            <br />
            <div
              class="elClr panel"
              v-for="(item, index) in client_details.remarks_log"
              :key="index"
            >
              <div class="panel-heading">
                <p class="elClr panel-title">Marked by: {{ item.user.name }}</p>
                <p class="pull-right margin-right-10">
                  Marked On: {{ item.created_at }}
                </p>
              </div>
              <div class="elClr panel-body">
                <p class="pre-formatted">{{ item.remarks }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="info" v-if="roles.rm" @click="btnFSR()"
            >Print Preview FSR</b-button
          >

          <b-button
            size="sm"
            variant="success"
            v-if="
              client_details.status != 'finished' && roles.update_client_details
            "
            @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="
              client_details.status != 'finished' && roles.delete_client_details
            "
            @click="btnDelete()"
            >Delete</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!-- modalTargetDate -->
      <b-modal
        id="modal-update-target-date"
        centered
        title="Assign Team & Target Date"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        @ok="updateDateClicked"
      >
        <div
          class="rowFields mx-auto row"
          v-if="
            roles.create_client_details &&
            client_update_target_date.current_target_date != null
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Date Only:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_update_target_date.date_only"
            >
              <i
                class="fas fa-check"
                v-show="client_update_target_date.date_only"
              />
              <i
                class="fas fa-times"
                v-show="!client_update_target_date.date_only"
              />
            </p-check>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="
            roles.create_client_details &&
            client_update_target_date.current_target_date != null
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Change Team Only:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_update_target_date.team_only"
            >
              <i
                class="fas fa-check"
                v-show="client_update_target_date.team_only"
              />
              <i
                class="fas fa-times"
                v-show="!client_update_target_date.team_only"
              />
            </p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account Name:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_update_target_date.name }}</p>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="!client_update_target_date.team_only"
        >
          <div class="col-lg-3">
            <p class="textLabel">Select date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client_update_target_date.target_date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="!client_update_target_date.date_only"
        >
          <div class="col-lg-3">
            <p class="textLabel">Assigned Team:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              name="jo_cable_cat"
              :list="teams"
              v-model="client_update_target_date.team_id"
              option-value="id"
              option-text="name"
              placeholder="Select team"
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="
            roles.create_client_details &&
            client_update_target_date.current_target_date != null &&
            client_update_target_date.inst_remarks != ''
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Installation Remarks:</p>
          </div>
          <div class="col-lg-9" style="white-space: pre-wrap">
            {{ client_update_target_date.inst_remarks }}
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="
            roles.create_client_details &&
            client_update_target_date.current_target_date != null
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Installation Remarks:</p>
          </div>
          <div class="col-lg-9">
            <input
              name="changeschedremarks"
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Remarks"
              placeholder="Remarks"
              v-model="client_update_target_date.inst_remarks_temp"
              v-validate="'required'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('changeschedremarks')"
              >Remarks is required.</small
            >
          </div>
        </div>
      </b-modal>
      <!-- End modalTargetDate -->

      <!-- modalActivatedDate ----------------------------------------------------------------------->
      <b-modal
        id="modal-update-activated-date"
        centered
        title="Client Activation"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        @ok="updateActivatedDateClicked"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account Name:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ client_update_activated_date.name }}</p>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Input activation code:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the client confirmation code"
              placeholder="client confirmation code"
              v-model.trim="otp_input"
            />
          </div>
        </div>-->

        <div
          class="rowFields mx-auto row"
          v-if="client_update_activated_date.inst_remarks != ''"
        >
          <div class="col-lg-3">
            <p class="textLabel">Installation Remarks:</p>
          </div>
          <div class="col-lg-9" style="white-space: pre-wrap">
            {{ client_update_activated_date.inst_remarks }}
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Installation Remarks:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Installation remarks"
              placeholder="Installation Remarks"
              v-model.trim="client_update_activated_date.inst_remarks_temp"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Select date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client_update_activated_date.activated_date"
                :config="AppliedDateoptions"
                autocomplete="off"
                @dp-change="dpchange_date"
              ></date-picker>
              <br />
              <p
                class="text-danger pull-left"
                v-if="client_update_activated_date_chkdate == true"
              >
                Future date selected.
              </p>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Bucket:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="buckets"
              v-model="bucket_selected"
              :custom-text="getBucketDesc"
              option-value="id"
              option-text="name"
              name="bucket"
              placeholder="Select/Search a Bucket..."
            ></model-list-select>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="bucket_selected.name != 'Dole'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Subscription Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="subscription_name"
              class="form-control"
              v-b-tooltip.hover
              title="subscription name in the bucket"
              placeholder="subscription name"
              v-model.lazy="client_update_activated_date.subscription_name"
            />
          </div>
        </div>
      </b-modal>
      <!-- End modalActivatedDate -->

      <!-- modalCreateJobOrder --------------------------------------------------------------------------->
      <b-modal
        id="modal-create-job-order"
        size="lg"
        title="Create Job Order"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account Name:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>
                {{ Job_Order.name }}
                <span v-if="Job_Order.line_transfer == 1">- Line Transfer</span>
              </b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Project Description:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              <b>Installation</b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Job Order #:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              name="jo_num"
              v-b-tooltip.hover
              title="Input Job #(leave empty for auto increment)"
              placeholder="Job order number"
              v-on:keyup.enter="btnCreateJO"
              v-model.trim="Job_Order.jo_num"
              v-validate="'required'"
            />
            <small class="text-danger pull-left" v-show="errors.has('jo_num')"
              >JO# is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
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
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">FOC:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ Job_Order.cable_category }}</p>
            <!-- <model-list-select
              name="jo_cable_cat"
              :list="cableCategoryOption"
              v-model="Job_Order.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
              v-on:keyup.enter="btnCreateJO"
            ></model-list-select>-->
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Length(m):</p>
          </div>
          <div class="col-lg-9">
            <!-- <p class="textLabel">{{ Job_Order.foc_length }}</p> -->
            <input
              type="number"
              name="jo_foc_length"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-model="Job_Order.foc_length"
              v-validate="'between:0,9000000'"
              v-on:keyup.enter="btnCreateJO"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('jo_foc_length')"
              >Input valid number.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Tech In-Charge:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ Job_Order.engineer_in_charge_name }}</p>
            <!-- <model-list-select
              :list="engineers"
              name="techsales"
              v-model="Job_Order.engineer_in_charge"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Engineer..."
              v-on:keyup.enter="btnCreateJO"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('techsales')"
            >Tech In-charge in required.</small>-->
          </div>
        </div>

        <div class="rowFields mx-auto row">
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
              v-on:keyup.enter="btnCreateJO"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
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
              v-on:keyup.enter="btnCreateJO"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
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
              v-on:keyup.enter="btnCreateJO"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
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
              v-on:keyup.enter="btnCreateJO"
            ></model-list-select>
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreateJO()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!-- End modalCreateJobOrder -->

      <b-modal id="modal-center" centered title="BootstrapVue">
        <p class="my-4">Vertically centered modal!</p>
      </b-modal>

      <!-- modalViewJobOrder -->
      <b-modal
        id="modal-view-job-order"
        size="lg"
        title="View Job Order"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class>Account Name:</p>
          </div>
          <div class="col-lg-9">
            <p class>
              <b>
                {{ View_Job_Order_All.client1.name }}
                <span v-if="View_Job_Order_All.line_transfer == 1"
                  >- Line Transfer</span
                >
              </b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class>Project Description:</p>
          </div>
          <div class="col-lg-9">
            <p class>
              <b>{{ View_Job_Order_All.JobOrder.project_description }}</b>
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class>Job Order #</p>
          </div>
          <div class="col-lg-9">
            <p class>
              <b>{{ View_Job_Order_Id_selected }}</b>
            </p>
          </div>
        </div>

        <div
          class="ViewJobOrder"
          v-if="View_Job_Order_All.JobOrder.client_id != null"
        >
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date Started:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="View_Job_Order_All.JobOrder.started"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>

        <div
          class="ViewJobOrder"
          v-if="View_Job_Order_All.JobOrder.client_id != null"
        >
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date Finished:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="View_Job_Order_All.JobOrder.finished"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Region:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="regions"
                v-model="View_Job_Order_All.JobOrder.region_id"
                option-value="id"
                option-text="name"
                placeholder="Select/Search a Region..."
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Tech In-Charge:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">
                <b>{{ View_Job_Order_All.JobOrder.engineer.user.name }}</b>
              </p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Prepared by:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="engineers"
                v-model="View_Job_Order_All.JobOrder.prepare"
                option-value="id"
                option-text="name"
                placeholder="Select/Search a Engineer..."
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Approved by:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="engineers"
                v-model="View_Job_Order_All.JobOrder.approve"
                option-value="id"
                option-text="name"
                placeholder="Select/Search a Engineer..."
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Noted by:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="engineers"
                v-model="View_Job_Order_All.JobOrder.note"
                option-value="id"
                option-text="name"
                placeholder="Select/Search a Engineer..."
              ></model-list-select>
            </div>
          </div>
        </div>
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="info"
            @click="printPreview"
            v-if="View_Job_Order_All.JobOrder.client_id != null"
            >Print Preview</b-button
          >

          <b-button
            size="sm"
            variant="success"
            @click="btnUpdateJO()"
            v-if="
              View_Job_Order_All.JobOrder.client_id != null &&
              roles.update_job_order
            "
            >Update</b-button
          >

          <b-button
            size="sm"
            variant="danger"
            @click="btnDeleteJO()"
            v-if="
              View_Job_Order_All.JobOrder.client_id != null &&
              roles.delete_job_order
            "
            >Delete</b-button
          >
        </template>
      </b-modal>

      <b-modal
        id="modalColumnSettings"
        title="Columns"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        style="width: 50px"
      >
        <div v-on:click="changeColDisplay('colLocation')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colLocation"
          ></p-check
          >Location
        </div>
        <br />
        <div v-on:click="changeColDisplay('colPackage')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colPackage"
          ></p-check
          >Package
        </div>
        <br />
        <div v-on:click="changeColDisplay('colRegion')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colRegion"
          ></p-check
          >Region
        </div>
        <br />
        <div v-on:click="changeColDisplay('colModem')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colModem"
          ></p-check
          >Modem
        </div>
        <br />
        <div v-on:click="changeColDisplay('colProtocol')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colProtocol"
          ></p-check
          >Protocol
        </div>
        <br />
        <div v-on:click="changeColDisplay('colCableCat')" style>
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colCableCat"
          ></p-check
          >FOC
        </div>
        <br />
        <div v-on:click="changeColDisplay('colFOCLength')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colFOCLength"
          ></p-check
          >Length
        </div>
        <br />
        <div v-on:click="changeColDisplay('colSales')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colSales"
          ></p-check
          >Sales in-charge
        </div>
        <br />
        <div v-on:click="changeColDisplay('colTech')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colTech"
          ></p-check
          >Tech in-charge
        </div>
        <br />
        <div v-on:click="changeColDisplay('colAppliedDate')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colAppliedDate"
          ></p-check
          >Applied Date
        </div>
        <br />
        <div slot="modal-footer" slot-scope="{}"></div>
      </b-modal>
      <modal_inst_filter></modal_inst_filter>
      <scheduler_closure_marker
        v-bind:data="window_details"
      ></scheduler_closure_marker>
    </div>

    <modal_fsr v-bind:data="fsr_data"></modal_fsr>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import modal_inst_filter from "../../modal_vue/modal_inst_filter.vue";
import scheduler_closure_marker from "../../modal_vue/scheduler_closure_marker.vue";

import modal_fsr from "../../modal_vue/modal_fsr.vue";

export default {
  components: {
    scheduler_closure_marker: scheduler_closure_marker,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    modal_inst_filter: modal_inst_filter,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    modal_fsr: modal_fsr,
  },
  data() {
    return {
      user: [],
      teams: [],
      doneLoad: true,
      tblisBusy: false,
      sortBy: "",
      sortDesc: "",
      fields: [],
      items: [],
      items_copy: [],
      buckets: [],
      tblFilter: null,
      tblFilter_copy: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 50,
      pageOptions: [6, 10, 20, 50, 100],
      client_details: {
        id_update: "",
        client_id: "",
        otc: "",
        contract: "",
        mapping_status: true,
        cable_category: "",
        foc_length: "",
        foc_layout: { id: "Pending", name: "Pending" },
        foc_schedule: "",
        layout_remarks: "",
        modem_status: false,
        applied_date: "",
        inst_remarks: "",
        inst_remarks_temp: "",
        target_date: "",
        status: "",
        layout_status: "",
        aging: null,
        client1: {
          name: "",
          location: "",
          contact: "",
        },
      },
      clientSelected: {
        id: "",
        region_id: "",
      },
      client_update_target_date: {
        date_only: false,
        team_only: false,
        client_detail_id: "",
        target_date: "",
        region_id: "",
        team_id: "",
        type: "installation",
        name: "",
      },
      client_update_activated_date_chkdate: false,
      client_update_activated_date: {
        client_id: "",
        activated_date: "",
        name: "",
      },
      bucket_selected: {
        id: "",
        name: "",
      },
      clients: [],
      sales: [],
      engineers: [],
      regions: [],
      focLayoutOption: [
        { id: "Completed", name: "Completed" },
        { id: "Outdoor layout done", name: "Outdoor layout done" },
        { id: "Indoor layout done", name: "Indoor layout done" },
        { id: "Pending", name: "Pending" },
      ],
      layoutStatOption: [
        { id: "M1", name: "M1" },
        { id: "M2", name: "M2" },
        { id: "MM2", name: "MM2" },
        { id: "M3", name: "M3" },
      ],
      otcOption: [
        { id: "Paid", name: "Paid" },
        { id: "Promo", name: "Promo" },
        { id: "Billing", name: "Billing" },
        { id: "Waived", name: "Waived" },
        { id: "NTP", name: "NTP" },
        { id: "Waiting for C&C advisory", name: "Waiting for C&C advisory" },
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
      datenow: new Date(),
      dateTomorrow: new Date(),
      dateYesterday: new Date(),
      daypass: 0,
      roles: [],
      colLocation: false,
      colPackage: false,
      colRegion: false,
      colModem: false,
      colProtocol: false,
      colCableCat: false,
      colFOCLength: false,
      colSales: false,
      colTech: false,
      colAppliedDate: true,
      Job_Order: {
        id: null,
        jo_num: null,
        client_id: null,
        name: "",
        cable_category: "",
        foc_length: "",
        region_id: null,
        region_id1: null,
        project_description: "Installation",
        started: null,
        engineer_in_charge: "",
        prepare: 3,
        approve: 2,
        note: 1,
      },
      View_Job_Order_Id_selected: "",
      View_Job_Order_Id: "",
      View_Job_Order_Id_region: "",
      View_Job_Order: {},
      View_Job_Order_All: {
        client1: {
          name: "",
        },
        JobOrder: {
          client_id: null,
          region: {
            id: "",
            name: "",
          },
          region_id: null,
          project_description: null,
          started: null,
          engineer_in_charge: "",
          prepare: null,
          approve: null,
          note: null,
          finished: null,
          copy_finished: "",
        },
      },
      otp: "",
      otp_input: "",
      instFormType: "0",
      totalWFC: 0,
      totalNeedAssign: 0,
      total_no_foc_layout_sched: 0,
      reloader_counter: 0,
      reloader_interval: [],
      searchby_list: [
        {
          name: "Account Name",
          id: "clients.name",
        },
        {
          name: "Address",
          id: "clients.location",
        },
        {
          name: "Contact",
          id: "clients.contact",
        },
        {
          name: "ID",
          id: "client_details.id",
        },
      ],
      searchby: "clients.name",
      closures: [],
      coordinates: {
        lat: 0,
        lng: 0,
      },
      markers: [
        {
          position: {
            lat: 0,
            lng: 0,
          },
        },
      ],
      markers_edit: [],
      remarksText: "",
      cbFilter: null,
      filterIn: null,
      showTable: false,
      pendingTrend: 0,
      activatedTrend: 0,
      window_position: {
        lat: 0,
        lng: 0,
      },
      window_details: {
        name: "",
        type: "",
        closure_type_id: 0,
      },
      marker: {
        position: {
          lat: 7.0688182,
          lng: 125.59859859999999,
        },
        client: "",
      },
      fsr_data: {
        user: {
          email: "",
        },
      },
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.dateTomorrow.setDate(this.dateTomorrow.getDate() + 1);
    this.dateTomorrow = this.formatDate(this.dateTomorrow);
    this.dateYesterday.setDate(this.dateYesterday.getDate() - 1);
    this.dateYesterday = this.formatDate(this.dateYesterday);
    this.datenow = this.formatDate(this.datenow);

    this.sales = this.$global.getSales();
    this.engineers = this.$global.getEngineer();
    this.roles = this.$global.getRoles();
    this.teams = this.$global.getTeam();
    this.regions = this.$global.getRegion();
    this.changeColDisplay("colAppliedDate");

    this.loadClients();

    this.$getLocation({})
      .then((coordinates) => {
        this.coordinates = coordinates;
      })
      .catch((error) => {
        var coor = {
          lat: 7.040641,
          lng: 125.577053,
        };
        this.coordinates = coor;
      });
  },
  mounted() {
    this.load();
    this.getCountNoti();

    this.$root.$on("update_item", (item, filt) => {
      this.showTable = true;
      this.items = item;
      this.cbFilter = filt;
      this.filterIn = "multi";
      if (this.items != null) this.totalRows = item.length;
    });
  },
  updated() {},
  computed: {
    nearClosure() {
      if (this.markers_edit.length > 0) {
        var retVal = [];
        this.closures.forEach((item) => {
          var latMax = this.markers_edit[0].position.lat + 0.005;
          var latMin = this.markers_edit[0].position.lat - 0.005;
          var lngMax = this.markers_edit[0].position.lng + 0.005;
          var lngMin = this.markers_edit[0].position.lng - 0.005;
          if (
            item.position.lat > latMin &&
            item.position.lat < latMax &&
            item.position.lng > lngMin &&
            item.position.lng < lngMax
          ) {
            item.position = {
              lat: parseFloat(item.lat),
              lng: parseFloat(item.lng),
            };
            item.marker_type = "closure";
            retVal.push(item);
          }
        });
        return retVal;
      } else return [];
    },
  },
  methods: {
    load() {
      this.$nextTick(function () {
        setTimeout(function () {
          document.getElementById("componentMenu").className =
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
      this.$http.get("api/Bucket").then((response) => {
        this.buckets = response.body;
      });
      this.$http
        .post("api/clientDetail/getTrend/" + this.user.region_id)
        .then((response) => {
          console.log(response.body);
          this.pendingTrend = response.body.pendingTrend;
          this.activatedTrend = response.body.activatedTrend;
        });
    },
    reload_data() {
      this.showTable = true;
      if (this.tblisBusy == false) {
        this.tblisBusy = true;
        this.reloader_counter = 0;
        this.$http.get("api/Engineer").then(function (response) {
          this.$global.setEngineer(response.body);
          this.engineers = response.body;
          this.reloader_counter++;
        });
        this.$http.get("api/Region").then(function (response) {
          this.$global.setRegion(response.body);
          this.regions = response.body;
          this.reloader_counter++;
        });

        this.$http.get("api/team").then(function (response) {
          this.$global.setTeam(response.body);
          this.teams = response.body;
          this.reloader_counter++;
        });

        this.$http
          .get("api/clientDetail/subIndex/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body;
            this.totalRows = this.items.length;
            this.items_copy = this.items;
            this.reloader_counter++;
            console.log(response.body);
          });
        this.$http
          .post("api/clientDetail/getTrend/" + this.user.region_id)
          .then((response) => {
            console.log(response.body);
            this.pendingTrend = response.body.pendingTrend;
            this.activatedTrend = response.body.activatedTrend;
          });
        this.reloader_interval = setInterval(this.reloader_breaker, 100);
      }
    },

    reloader_breaker() {
      if (this.reloader_counter == 4) {
        this.tblisBusy = false;
        clearInterval(this.reloader_interval);
      }
    },
    loadClients() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function (response) {
          this.clients = response.body;
        });

      this.$http.get("api/Closure").then((response) => {
        this.closures = response.body;
      });
    },
    getClientDesc(client) {
      if (this.instFormType == "transfer")
        return `${client.name} - ${client.region_name} - Line Transfer`;
      else return `${client.name} - ${client.region_name}`;
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      this.markers_edit = [];
      console.log(item);
      //+0.005

      this.client_details = item;
      this.client_details.region_id = this.user.region_id;
      this.client_details.foc_sched_copy = item.foc_schedule;

      if (item.inst_remarks == null) this.client_details.inst_remarks = "";
      this.client_details.inst_remarks_temp = "";
      if (item.client1.lat != null && item.client1.lng != null) {
        var coor = {
          position: {
            lat: parseFloat(item.client1.lat),
            lng: parseFloat(item.client1.lng),
          },
        };
        this.coordinates = coor.position;
        this.markers_edit.push(coor);
      }
      this.$bvModal.show("modalEdit");
      //gmapclient
      // const cityCircle = new google.maps.Circle({
      //   strokeColor: "#FF0000",
      //   strokeOpacity: 0.8,
      //   strokeWeight: 2,
      //   fillColor: "#FF0000",
      //   fillOpacity: 0.35,
      //   gmapclient,
      //   center: this.markers_edit.position,
      //   radius: 300
      // });
    }, //thead-class
    tblHeadClicked(key, field, event) {
      if (key == "applied_date_formated") {
        this.sortDesc = !this.sortDesc;
        if (this.sortBy != "applied_date_sort")
          this.sortBy = "applied_date_sort";
      }
      if (key == "status1.target_date") {
        this.sortDesc = !this.sortDesc;
        this.sortBy = "targetDateSort";
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    OpenModalAdd() {
      this.client_details = {
        client_id: "",
        otc: "",
        mapping_status: true,
        cable_category: "",
        foc_length: "",
        foc_layout: "",
        modem_status: false,
        applied_date: "",
        target_date: "",
        status: "",
        layout_status: "",
        aging: null,
        client1: {
          name: "",
          location: "",
          contact: "",
        },
      };

      this.$bvModal.show("modalAdd");
    },
    btnCreate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          // if (
          //   this.client_details.otc == "Paid" ||
          //   this.client_details.otc == "Promo" ||
          //   this.client_details.otc == "Billing" ||
          //   this.client_details.otc == "Waived" ||
          //   this.client_details.otc == "NTP"
          // ) {
          //   this.client_details.aging = this.formatDate(new Date());
          // }
          //this.client_details.region_id = this.user.region_id;
          this.client_details.user_id = this.user.id;
          this.client_details.user_name = this.user.name;
          this.client_details.line_transfer = this.instFormType;
          this.$http
            .post("api/ClientDetail", this.client_details)
            .then((response) => {
              swal("Created", "", "success");
              this.client_details.client_id = "";
              this.client_details.otc = "";
              this.client_details.mapping_status = true;
              this.client_details.cable_category = "";
              this.client_details.foc_length = "";
              this.client_details.foc_layout = "";
              this.client_details.modem_status = false;
              this.client_details.applied_date = "";
              this.client_details.inst_remarks = "";
              this.client_details.inst_remarks_temp = "";
              this.client_details.layout_status = "";
              this.client_details.aging = null;
              this.items = response.body;
              this.totalRows = this.items.length;

              this.$root.$emit("pageLoaded");
              this.tblisBusy = false;
              this.getCountNoti();
              this.$bvModal.hide("modalAdd");
            })
            .catch((response) => {
              this.tblisBusy = false;
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
    },
    btnUpdate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            if (update) {
              this.$root.$emit("pageLoading");

              if (this.client_details.inst_remarks_temp != "") {
                if (this.client_details.inst_remarks != "")
                  this.client_details.inst_remarks += ",\n";
                this.client_details.inst_remarks +=
                  "-Remarks: " + this.client_details.inst_remarks_temp;
              }

              this.client_details.user_id = this.user.id;
              this.client_details.user_name = this.user.name;
              this.client_details.filterIn = this.filterIn;
              this.client_details.cbFilter = this.cbFilter;
              this.client_details.searchby = this.searchby;
              this.client_details.tblFilter = this.tblFilter_copy;
              this.$http
                .put(
                  "api/ClientDetail/" + this.client_details.id,
                  this.client_details
                )
                .then((response) => {
                  this.items = response.body;
                  // this.filterIn = null;
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalEdit");
                  this.getCountNoti();
                  this.$root.$emit("pageLoaded");
                });
            }
          });
        }
      });
    },
    btnDelete() {
      if (this.roles.delete_client_details) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            this.tblisBusy = true;
            this.$root.$emit("pageLoading");
            var data = {
              id: this.client_details.id,
              region_id: this.user.region_id,
              user_id: this.user.id,
              user_name: this.user.name,
              filterIn: this.filterIn,
              cbFilter: this.cbFilter,
              searchby: this.searchby,
              tblFilter: this.tblFilter_copy,
            };

            this.$http
              .post("api/clientDetail/destroy1", data)
              .then((response) => {
                console.log(response.body);
                this.$bvModal.hide("modalEdit");
                this.items = response.body;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
                this.$root.$emit("pageLoaded");
                this.getCountNoti();
                swal("Deleted", "", "success");
              })
              .catch((response) => {
                this.tblisBusy = false;
                this.$root.$emit("pageLoaded");
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
          text: "You are not allow to delete a Schedule",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        });
      }
    },
    wfcClicked(item, index, button) {
      swal({
        title: "",
        text: "Confirmed for schedule?",
        icon: "info",
        buttons: ["No", "Yes"],
      }).then((yes) => {
        if (yes) {
          this.tblisBusy = true;
          item.user_id = this.user.id;
          item.user_name = this.user.name;
          item.user_region_id = this.user.region_id;
          this.$http
            .put("api/clientDetail/clientConfirm/" + item.id, item)
            .then((response) => {
              swal("Confirmed", "", "success").then((value) => {
                this.items = response.body;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
                this.getCountNoti();
              });
            })
            .catch((response) => {
              this.tblisBusy = false;
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
    },
    updateDateClicked(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.$validator.validateAll().then((result) => {
        if (result) {
          if (
            this.client_update_target_date.target_date != "" ||
            this.client_update_target_date.team_only != ""
          ) {
            // if (
            //   this.formatDate(this.client_update_target_date.target_date) >=
            //   this.datenow
            // )
            if (
              this.client_update_target_date.team_id != "" ||
              this.client_update_target_date.date_only == true
            ) {
              if (this.client_update_target_date.inst_remarks_temp != "") {
                if (this.client_update_target_date.inst_remarks != "")
                  this.client_update_target_date.inst_remarks += ",\n";
                this.client_update_target_date.inst_remarks +=
                  "-Change schedule remarks: " +
                  this.client_update_target_date.inst_remarks_temp;
              }

              this.updateTargetDate();
            } else swal("Ops.", "Please select team", "warning");
            // else
            //   swal("Ops.", "Please select present or future dates only", "warning");
          } else swal("Ops.", "Please select date first", "warning");
        }
      });
    },
    updateTargetDate() {
      console.log(this.client_update_target_date);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "info",
        buttons: ["No", "Yes"],
      }).then((yes) => {
        if (yes) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          this.client_update_target_date.user_id = this.user.id;
          this.client_update_target_date.user_name = this.user.name;

          this.client_update_target_date.filterIn = this.filterIn;
          this.client_update_target_date.cbFilter = this.cbFilter;
          this.client_update_target_date.searchby = this.searchby;
          this.client_update_target_date.tblFilter = this.tblFilter_copy;
          this.$http
            .post(
              "api/clientDetail/updateTargetDate",
              this.client_update_target_date
            )
            .then((response) => {
              this.$bvModal.hide("modal-update-target-date");
              this.tblisBusy = false;
              // this.filterIn = null;
              this.$root.$emit("pageLoaded");
              swal("Updated", "", "success").then((value) => {
                this.items = response.body;
                this.totalRows = this.items.length;
                this.getCountNoti();
              });
            });
          // .catch(response => {
          //   swal({
          //     title: "Error",
          //     text: response.body.error,
          //     icon: "error",
          //     dangerMode: true
          //   });
          //   this.tblisBusy = false;
          //   this.$root.$emit("pageLoaded");
          // });
        }
      });
    },
    updateActivatedDateClicked(bvModalEvt) {
      bvModalEvt.preventDefault();
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
      }).then((yes) => {
        if (yes) {
          this.$root.$emit("pageLoading");
          if (this.client_update_activated_date.inst_remarks_temp != "") {
            if (this.client_update_activated_date.inst_remarks != "")
              this.client_update_activated_date.inst_remarks += ",\n";
            this.client_update_activated_date.inst_remarks +=
              "-Activate Remarks: " +
              this.client_update_activated_date.inst_remarks_temp;
          }
          this.tblisBusy = true;
          this.client_update_activated_date.bucket = this.bucket_selected;
          this.client_update_activated_date.user_id = this.user.id;
          this.client_update_activated_date.user_email = this.user.email;
          this.client_update_activated_date.user_name = this.user.name;
          this.client_update_activated_date.otp_input = this.otp_input;
          this.client_update_activated_date.filterIn = this.filterIn;
          this.client_update_activated_date.cbFilter = this.cbFilter;
          this.client_update_activated_date.searchby = this.searchby;
          this.client_update_activated_date.tblFilter = this.tblFilter_copy;
          this.client_update_activated_date.sendTo = [
            {
              email: "amgt@dctechmicro.com",
              name: "Account Management",
            },
          ];
          this.client_update_activated_date.CCTO = [
            {
              email: "cnc@dctechmicro.com",
              name: "Credits and Collection",
            },
            {
              email: this.user.email,
              name: this.user.name,
            },
          ];

          this.$http
            .post(
              "api/clientDetail/activateClient",
              this.client_update_activated_date
            )
            .then((response) => {
              this.$root.$emit("pageLoaded");
              swal("Activated!", "", "success");
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$bvModal.hide("modal-update-activated-date");
              this.getCountNoti();
            })
            .catch((response) => {
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
      });
      // } else swal("Wrong! activation code", "", "warning");
    },
    openModalUpdateTargetDate(item) {
      this.client_update_target_date.item = item;
      this.client_update_target_date.current_target_date = null;
      this.client_update_target_date.date_only = false;
      this.client_update_target_date.team_only = false;
      this.client_update_target_date.current_target_date = item.target_date;
      this.client_update_target_date.target_date = "";
      this.client_update_target_date.team_id = "";
      this.client_update_target_date.client_detail_id = item.id;
      this.client_update_target_date.region_id = this.user.region_id;
      this.client_update_target_date.name = item.name;
      if (item.inst_remarks == null)
        this.client_update_target_date.inst_remarks = "";
      else this.client_update_target_date.inst_remarks = item.inst_remarks;
      this.client_update_target_date.inst_remarks_temp = "";

      this.$bvModal.show("modal-update-target-date");
      console.log(this.client_update_target_date);
    },
    openModalUpdateActivatedDate(item) {
      //this.otp = this.generateOTP();
      this.client_update_activated_date = item;
      this.client_update_activated_date.activated_date = "";

      if (item.inst_remarks == null)
        this.client_update_activated_date.inst_remarks = "";
      this.client_update_activated_date.inst_remarks_temp = "";

      // this.client_update_activated_date.client_id = item.client_id;
      // this.client_update_activated_date.region_id = item.region_id;
      // this.client_update_activated_date.name = item.name;
      this.client_update_activated_date.email = item.email_add;
      // this.client_update_activated_date.target_date = item.target_date;
      // this.client_update_activated_date.line_transfer = item.line_transfer;

      this.$bvModal.show("modal-update-activated-date");
    },

    openModalCreateJobOrder(item) {
      this.Job_Order = {
        id: null,
        jo_num: null,
        client_id: null,
        name: "",
        cable_category: "",
        foc_length: "",
        region_id: null,
        region_id1: null,
        project_description: "Installation",
        started: null,
        engineer_in_charge: "",
        prepare: 3,
        approve: 2,
        note: 1,
      };
      if (item.client1.engineer == null) {
        swal("Info", "This account has no tech in-charge", "info");
      } else {
        this.Job_Order.engineer_in_charge = item.client1.engineer.id;
        this.Job_Order.engineer_in_charge_name =
          item.client1.engineer.user.name;
      }
      this.Job_Order.client_id = item.client_id;
      this.Job_Order.name = item.name;
      this.Job_Order.cable_category = item.cable_category;
      this.Job_Order.foc_length = item.foc_length;
      this.Job_Order.client_detail_id = item.id;

      this.$http
        .get("api/JobOrder/getMaxID/" + this.user.region_id)
        .then((response) => {
          var a = Number(response.body);
          this.Job_Order.jo_num = a + 1;
          //console.log(response.body);
        });
      if (item.line_transfer == 1) this.Job_Order.line_transfer = 1;
      else this.Job_Order.line_transfer = 0;

      this.$bvModal.show("modal-create-job-order");
    },
    openModalViewJobOrder(item) {
      this.View_Job_Order_Id_selected = item.JobOrder.jo_num;
      this.View_Job_Order_Id = item.JobOrder.id;
      this.$bvModal.show("modal-view-job-order");

      console.log(item);
      this.View_Job_Order_All = item;
      this.View_Job_Order_All.copy_finished = item.JobOrder.finished;
      //console.log(this.View_Job_Order_All.JobOrder);

      this.doneLoad = true;
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
    btnCreateJO() {
      console.log(this.Job_Order);
      if (this.Job_Order.engineer_in_charge != "") {
        if (this.Job_Order.region_id != null) {
          swal({
            title: "Are you sure?",
            text: "",
            icon: "info",
            buttons: ["No", "Yes"],
          }).then((yes) => {
            if (yes) {
              this.tblisBusy = true;
              this.$root.$emit("pageLoading");
              this.Job_Order.user_id = this.user.id;
              this.Job_Order.user_name = this.user.name;
              this.Job_Order.region_id1 = this.user.region_id;
              this.$http
                .post("api/clientDetail/storeJobOrder", this.Job_Order)
                .then((response) => {
                  this.tblisBusy = false;
                  this.$root.$emit("pageLoaded");
                  this.$bvModal.hide("modal-create-job-order");
                  this.items = response.body;
                  swal("Created", "", "success");
                  this.Job_Order = {
                    id: null,
                    client_id: null,
                    region_id: null,
                    region_id1: null,
                    project_description: "Installation",
                    started: null,
                    engineer_in_charge: "",
                    prepare: 3,
                    approve: 2,
                    note: 1,
                  };
                })
                .catch((response) => {
                  this.tblisBusy = false;
                  this.$root.$emit("pageLoaded");
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true,
                  });
                });
            }
          });
        } else {
          swal({
            title: "Info",
            text: "Please select region",
            icon: "info",
            dangerMode: true,
          });
        }
      } else {
        swal({
          title: "Info",
          text: "This client has no Engineer incharge",
          icon: "info",
          dangerMode: true,
        });
      }
    },
    btnDeleteJO() {
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
          var data = {
            id: this.View_Job_Order_Id,
            region_id: this.user.region_id,
            user_id: this.user.id,
            user_name: this.user.name,
          };
          this.$http
            .post("api/clientDetail/destroyJobOrder", data)
            .then((response) => {
              this.$bvModal.hide("modal-view-job-order");
              swal("Deleted", "", "success").then((value) => {
                this.items = response.body;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
              });
            })
            .catch((response) => {
              this.tblisBusy = false;
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
    },
    btnUpdateJO() {
      console.log(this.View_Job_Order_All.JobOrder);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "info",
        buttons: ["No", "Yes"],
      }).then((yes) => {
        if (yes) {
          this.View_Job_Order_All.JobOrder.update_in = "schedule";
          this.$http
            .put(
              "api/JobOrder/" + this.View_Job_Order_Id,
              this.View_Job_Order_All.JobOrder
            )
            .then((response) => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modal-view-job-order");
            });
        }
      });
    },
    changeColDisplay(check) {
      this.fields = [
        //number joborder
        {
          key: "JobOrder.id",
          label: "Job #",
          sortable: true,
        },
        {
          key: "status1.target_date",
          label: "Installation Date",
        },
        {
          key: "account_name",
          label: "Account Name",
          formatter: (value, key, item) => {
            if (item.line_transfer)
              return item.client1.name + " - Line Transfer";
            else return item.client1.name;
          },
        },
        {
          key: "client1.region.name",
          label: "Region",
          sortable: true,
        },
        {
          key: "client1.area.name",
          label: "Area",
          sortable: true,
        },
        {
          key: "client1.branch.name",
          label: "Branch",
          sortable: true,
        },
      ];

      this.fields.push(temp);

      if ("colLocation" == check) {
        this.colLocation = !this.colLocation;
      }
      if (this.colLocation) {
        var temp = {
          key: "client1.location",
          label: "Location",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colPackage" == check) {
        this.colPackage = !this.colPackage;
      }
      if (this.colPackage) {
        var temp = {
          key: "client1.package.name",
          label: "Package",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colRegion" == check) {
        this.colRegion = !this.colRegion;
      }
      if (this.colRegion) {
        var temp = {
          key: "client1.region.name",
          label: "Region",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colModem" == check) {
        this.colModem = !this.colModem;
      }
      if (this.colModem) {
        var temp = {
          key: "client1.modem.name",
          label: "Modem",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colProtocol" == check) {
        this.colProtocol = !this.colProtocol;
      }
      if (this.colProtocol) {
        var temp = {
          key: "client1.communication_protocol",
          label: "Protocol",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colFOCLength" == check) {
        this.colFOCLength = !this.colFOCLength;
      }
      if (this.colFOCLength) {
        var temp = {
          key: "foc_length",
          label: "Length",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colSales" == check) {
        this.colSales = !this.colSales;
      }
      if (this.colSales) {
        var temp = {
          key: "client1.sales.user.name",
          label: "Sales",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colTech" == check) {
        this.colTech = !this.colTech;
      }
      if (this.colTech) {
        var temp = {
          key: "client1.engineer.user.name",
          label: "Tech Sales",
          sortable: true,
        };
        this.fields.push(temp);
      }

      extra = {
        key: "client1.contract",
        label: "Contract",
        // formatter: (value) => {
        //   if (check == "export") {
        //     if (value != null) return "O";
        //     else return "X";
        //   } else {
        //     if (value != null) return '<i class="fas fa-check" />';
        //     else return '<i class="fas fa-times" />';
        //   }
        // },
        sortable: true,
      };
      this.fields.push(extra);

      var extra = { key: "aging", label: "OTC", sortable: true };
      this.fields.push(extra);

      extra = {
        key: "mapping_status",
        label: "Mapping",
        formatter: (value) => {
          if (check == "export") {
            if (value == 1) return "O";
            else return "X";
          } else {
            if (value == 1) return '<i class="fas fa-check" />';
            else return '<i class="fas fa-times" />';
          }
        },
        sortable: true,
      };
      this.fields.push(extra);

      if ("colCableCat" == check) {
        this.colCableCat = !this.colCableCat;
      }
      if (this.colCableCat) {
        var temp = {
          key: "cable_category",
          label: "FOC.",
          sortable: true,
        };
        this.fields.push(temp);
      }

      // extra = { key: "foc_schedule", label: "FOC Schedule", sortable: true };
      // this.fields.push(extra);

      // extra = { key: "foc_layout", label: "Contractor stat.", sortable: true };
      // extra = { key: "layout_status", label: "layout stat.", sortable: true };
      // this.fields.push(extra);
      // extra = {
      //   key: "modem_status",
      //   label: "Modem",
      //   formatter: value => {
      //     if (check == "export") {
      //       if (value == 1) return "O";
      //       else return "X";
      //     } else {
      //       if (value == 1) return '<i class="fas fa-check" />';
      //       else return '<i class="fas fa-times" />';
      //     }
      //   },
      //   sortable: true
      // };
      // this.fields.push(extra);

      extra = { key: "agingCount", label: "Aging", sortable: true };
      this.fields.push(extra);

      // if (this.roles.admin) {
      //   var temp = {
      //     key: "foc_duration",
      //     label: "FOC Planning Duration",
      //     sortable: true,
      //   };
      //   this.fields.push(temp);
      // }

      //extra = { key: "foc_check_red", label: "foc_check_red", sortable: true };
      //this.fields.push(extra);

      if ("colAppliedDate" == check) {
        this.colAppliedDate = !this.colAppliedDate;
      }
      if (this.colAppliedDate) {
        var temp = {
          key: "applied_date_formated",
          label: "Applied Date",
        };
        this.fields.push(temp);
      }

      extra = {
        key: "created_at",
        sortable: true,
      };
      this.fields.push(extra);

      extra = {
        key: "date_activated",
        label: "Action",
        sortable: true,
      };
      this.fields.push(extra);

      if (this.user.id == 1) {
        var temp = {
          key: "id",
          label: "ID",
          sortable: true,
        };
        this.fields.push(temp);
      }
    },
    printPreview() {
      var newWin = window.open("");
      var linetransfer = " - Line Transfer";
      if (this.View_Job_Order_All.line_transfer == 1)
        linetransfer = " - Line Transfer";
      else linetransfer = "";

      newWin.document.write("<html><head>");

      newWin.document.write(
        '<style type="text/css" >' +
          ".center {text-align: center;}" +
          ".center1{ margin: 0; position: absolute; left: 50%; transform: translate(-50%);}" +
          "p {margin-top:-10; font-size: 12px;}" +
          ".borderbot-05 { border-bottom: solid black 0.5px;}" +
          ".borderbot-1 { border-bottom: solid black 1px;}" +
          ".bordertop-1 { border-top: solid black 1px;}" +
          ".bordertop-2 { border-top: solid black 2px;}" +
          ".borderleft-2 { border-left: solid black 2px;}" +
          "@media print { @page {size: A4 landscape; } }" +
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
          "<br>" +
          '<div class="borderbot-1 bordertop-2" style="text-align: center;">' +
          "<span><b>SERVICE ORDER FORM</b></span>" +
          "</div>" +
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5">' +
          "Project Description: <span class='borderbot-05'>" +
          this.View_Job_Order_All.JobOrder.project_description +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: right;">' +
          "Control #: <span class='borderbot-05'>" +
          this.View_Job_Order_Id_selected +
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
          this.View_Job_Order_All.client1.name +
          linetransfer +
          "," +
          this.View_Job_Order_All.client1.contact +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Address:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.client1.location +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Started:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder_started +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Finished:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder_finished +
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
          this.View_Job_Order_All.JobOrder.region.name +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Cable Category:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder.cable_category +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Estimated Distance(m):" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder.foc_length +
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
          "<br>" +
          //Engineer In-Charge:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Engineer In-Charge:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.engineer.user.name +
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
          "<br>" +
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
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          "Approved by:" +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.prepare_engineer.name +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.approve_engineer.name +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          //
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.prepare_engineer.position +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.approve_engineer.position +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class=""  style="text-align: center;">' +
          "<span>Noted by:</span>" +
          "</div>" +
          //
          //
          "<br>" +
          '<div   style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.note_engineer.name +
          "</span>" +
          "</div>" +
          '<div class=""  style="text-align: center;">' +
          "<span>" +
          this.View_Job_Order_All.JobOrder.note_engineer.position +
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
          "<br>" +
          '<div class="borderbot-1 bordertop-2" style="text-align: center;">' +
          "<span><b>SERVICE ORDER FORM</b></span>" +
          "</div>" +
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5">' +
          "Project Description: <span class='borderbot-05'>" +
          this.View_Job_Order_All.JobOrder.project_description +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: right;">' +
          "Control #: <span class='borderbot-05'>" +
          this.View_Job_Order_Id_selected +
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
          this.View_Job_Order_All.client1.name +
          linetransfer +
          "," +
          this.View_Job_Order_All.client1.contact +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Address:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.client1.location +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Started:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder_started +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-2">' +
          "Date Finished:" +
          "</div>" +
          '<div class="col-md-8 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder_finished +
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
          this.View_Job_Order_All.JobOrder.region.name +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Cable Category:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder.cable_category +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Estimated Distance(m)" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: left;">' +
          this.View_Job_Order_All.JobOrder.foc_length +
          "</div>" +
          "</div>" + //end row2
          //
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Actual Distance(m)" +
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
          "<br>" +
          //Engineer In-Charge:
          '<div class="row">' + //row2
          '<div class="col-md-3">' +
          "Engineer In-Charge:" +
          "</div>" +
          '<div class="col-md-7 borderbot-05"  style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.engineer.user.name +
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
          "<br>" +
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
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          "Approved by:" +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.prepare_engineer.name +
          "</span>" +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.approve_engineer.name +
          "</span>" +
          "</div>" +
          "</div>" + //end row1
          //
          '<div class="row">' + //row1
          '<div class="col-md-5" style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.prepare_engineer.position +
          "</div>" +
          '<div class="col-md-5" style="text-align: center;">' +
          this.View_Job_Order_All.JobOrder.approve_engineer.position +
          "</div>" +
          "</div>" + //end row1
          //
          "<br>" +
          '<div class=""  style="text-align: center;">' +
          "<span>Noted by:</span>" +
          "</div>" +
          //
          //
          "<br>" +
          '<div   style="text-align: center;">' +
          '<span class="borderbot-1" >' +
          this.View_Job_Order_All.JobOrder.note_engineer.name +
          "</span>" +
          "</div>" +
          '<div class=""  style="text-align: center;">' +
          "<span>" +
          this.View_Job_Order_All.JobOrder.note_engineer.position +
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

      //newWin.close();
    },
    dateDiffInDays(a, b) {
      const dt1 = new Date(b);
      const dt2 = new Date(a);
      return Math.floor(
        (Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) -
          Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) /
          (1000 * 60 * 60 * 24)
      );
    },
    onChangeClientId() {
      if (this.instFormType == "1") {
        this.client_details.client_id = this.clientSelected.id;
        this.client_details.region_id = this.user.region_id;
      } else {
        this.$http
          .get("api/client_has_sched/" + this.clientSelected.id)
          .then((response) => {
            if (response.body != 0) {
              if (response.body.status == "finished")
                swal('"' + this.clientSelected.name + '" is already activated');
              else
                swal(
                  '"' +
                    this.clientSelected.name +
                    '" is already in the schedule list'
                );
              this.clientSelected = {
                id: "",
                region_id: "",
              };
            } else {
              this.client_details.client_id = this.clientSelected.id;
              this.client_details.region_id = this.user.region_id;
            }
          });
      }
    },
    fnExcelReport(tbl) {
      this.changeColDisplay("export");
      this.currentPage = 1;
      this.perPage = this.totalRows;
      this.$global.excelReportCSV(tbl, "client installation");
      /*
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
            this.changeColDisplay("");
            return sa;
          }.bind(this),
          1000
        );
      });
*/
      // this.$nextTick(function() {
      //   setTimeout(this.changeColDisplay(""), 3000);
      // });
    },
    mousemove(e) {
      var scrol = document.getElementById("scrollmenuContainer");
      var wth = window.innerWidth - 200;
      if (wth < e.clientX) {
        scrol.scrollLeft += 5;
      }
      if (200 > e.clientX) {
        scrol.scrollLeft -= 5;
      }
      // console.log(scrol.scrollLeft);
      // console.log(e.clientX);
      // console.log(window.innerWidth);
    },
    generateOTP() {
      var string = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      let OTP = "";

      var len = string.length;
      for (let i = 0; i < 6; i++) {
        OTP += string[Math.floor(Math.random() * len)];
      }
      return OTP;
    },
    sendOTP(item) {
      this.otp = this.generateOTP();
      var data = {
        otp: this.otp,
        id: item.id,
        email: item.email_add,
        client_name: item.name,
        contact: item.contact,
      };
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/send_mail_otp", data)
        .then((response) => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
          if (response.body.error_string == null)
            swal("Activation code sent", "", "success");
          else
            swal(
              "Text message Error: " + response.body.error_string,
              "",
              "error"
            );
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: response.body,
            text: "",
            icon: "error",
            dangerMode: true,
          });
        });
    },
    filterChange(txt) {
      this.showTable = true;
      this.items = this.items_copy;
      this.totalRows = this.items.length;
      this.tblFilter = txt;
    },
    getCountNoti() {
      this.totalWFC = 0;
      this.totalNeedAssign = 0;
      this.total_no_foc_layout_sched = 0;
      this.items.forEach((item) => {
        if (item.status == null) this.totalWFC += 1;
        if (item.target_date == null && item.status != null)
          this.totalNeedAssign += 1;

        if (item.foc_plan_duration == null) this.total_no_foc_layout_sched += 1;
      });
    },
    clearFilter() {
      this.tblFilter = "";
      this.tblFilter_copy = "";
      this.items = this.items_copy;
      this.totalRows = this.items.length;
    },
    loadTable() {
      this.showTable = true;
      this.items = this.items_copy;
      this.totalRows = this.items.length;
    },
    search_data() {
      this.showTable = true;
      this.$http
        .get(
          "api/clientDetail/search_data/" +
            this.searchby +
            "/" +
            this.tblFilter_copy
        )
        .then((response) => {
          this.filterIn = "search";
          this.items = response.body;
          this.totalRows = this.items.length;
          console.log(response);
        })
        .catch((response) => {
          console.log(response);
        });
    },
    addRemarks_clicked() {
      if (this.remarksText != "") {
        var data = {
          client_detail_id: this.client_details.id,
          user_id: this.user.id,
          remarks: this.remarksText,
        };
        this.$http.post("api/InstallationRemarksLog", data).then((response) => {
          this.remarksText = "";
          this.client_details.remarks_log = response.body;
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the remarks text area");
      }
    },
    getBucketDesc(bucket) {
      return `${bucket.name} - ${bucket.IP}`;
    },
    hideTable() {
      this.showTable = false;
    },
    map_clicked(event) {
      //
      var coor = {
        lat: event.latLng.lat(),
        lng: event.latLng.lng(),
      };
      console.log(coor);
    },
    openWindow(event, marker, index, wit) {
      this.window_details = marker;
      this.$root.$emit("pageLoading");
      this.$http.get("api/Closure/" + marker.id).then((response) => {
        // console.log(response.body);
        this.window_details = response.body;
        this.window_details.client_id = this.client_details.client_id;
        this.window_details.client_name = this.client_details.client1.name;
        console.log("openWindow function in closure");
        this.$bvModal.show("scheduler_closure_marker");

        this.$root.$emit("pageLoaded");
      });
      // this.$bvModal.show("scheduler_closure_marker");
      // this.$root.$emit("pageLoaded");
    },

    openColumnSettings() {
      this.$bvModal.show("modalColumnSettings");
    },
    dpchange_date() {
      const dt1 = new Date();
      const dt2 = new Date(this.client_update_activated_date.activated_date);
      if (dt1.getTime() < dt2.getTime()) {
        this.client_update_activated_date_chkdate = true;
      } else {
        this.client_update_activated_date_chkdate = false;
      }
    },
    btnFSR() {
      this.fsr_data = {
        id: this.client_details.id,
        name: this.client_details.client1.name,
        contact_person: this.client_details.client1.contact_person,
        location: this.client_details.client1.location,
        contact: this.client_details.client1.contact,
        complaint: "FTTH INSTALLATION",
      };
      this.$bvModal.show("modalFSRPrintPreview");
    },
  },
};
</script>
<style scoped>
.statusBtn {
  flex: 1;
  padding-bottom: 4px;
  padding-top: 4px;
  font-weight: bold;
}
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
.modal-content,
.modal-header {
  border: 1px solid red;
}

.tblheadtrclass {
  text-align: center;
}
.pre-formatted {
  white-space: pre-wrap;
}
.export {
  width: 100px;
  color: white;
  margin-left: 10px;
}
.buttonRow {
  margin-top: 20px;
  margin-left: 0;
  width: 80%;
  height: 30px;
  margin-bottom: 10px;
}

.searchRow {
  width: 80%;
  margin-left: 5px;
  margin-top: 0;
}

.stripe {
  background: #f5f9ff;
}

.client {
  font-size: small;
  margin-top: -5px;
}

.instButton {
  width: 90%;
}

.jobButton {
  width: 100%;
}
.trend-bcard {
  color: black;
}

.btn-set {
  color: #000000;
  /* border: none; */
  background: #f8f9fa;
  float: right;
  width: 50%;
}
.btn-set:focus {
  box-shadow: none;
}
.btn-set:hover {
  background: #dfdfdf;
}
</style>
