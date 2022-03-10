<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Ticket List
          <button
            v-b-modal="'modalSendText'"
            v-if="roles.create_ticket"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Send Advisory
          </button>
          <button
            @click="OpenModalEmailTicket"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Send Email
          </button>
          <button
            @click="reload_data"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Reload data
          </button>
          <button
            v-b-modal="'modalAddStat'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Manage Status
          </button>
          <button
            v-b-modal="'modalAddComp'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Manage Complaints
          </button>
          <button
            v-b-modal="'modalAddAdv'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Manage Advisory
          </button>
          <button
            v-b-modal="'modalAddTicket'"
            v-if="roles.create_ticket"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            <i class="fas fa-plus"></i>
            Create Ticket
          </button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div id="searchPanel">
          <div style="display: flex">
            <div style="width: 50%">
              <b-row style="width: 100%; margin-left: 5px; margin-top: 0">
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
                          @click="filterClear"
                          style="
                            width: 100px;
                            color: white;
                            border-radius: 0 5px 5px 0;
                          "
                          >Clear</b-button
                        >
                      </b-input-group-append>
                      <button
                        @click="fnExcelReport('ticketTable')"
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

              <b-row
                style="
                  margin-top: 20px;
                  margin-left: 0;
                  width: 100%;
                  height: 30px;
                  margin-bottom: 10px;
                "
              >
                <button
                  @click="filterChange('1')"
                  type="button"
                  class="
                    btn btn-warning btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Pendings
                  <b-badge v-if="totalPendings > 0" variant="info">{{
                    totalPendings
                  }}</b-badge>
                </button>
                <button
                  @click="filterChange('9')"
                  type="button"
                  class="
                    btn btn-dark btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Modem/Line Transfer
                  <b-badge v-if="totalTransfer > 0" variant="info">{{
                    totalTransfer
                  }}</b-badge>
                </button>
                <button
                  @click="filterChange('2')"
                  type="button"
                  class="
                    btn btn-danger btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Urgents
                  <b-badge v-if="totalUrgents > 0" variant="info">{{
                    totalUrgents
                  }}</b-badge>
                </button>
                <button
                  @click="filterChange('7')"
                  type="button"
                  class="
                    btn btn-info btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  For ITND
                  <b-badge v-if="totalITND > 0" variant="info">{{
                    totalITND
                  }}</b-badge>
                </button>
                <button
                  @click="filterChange('4')"
                  type="button"
                  class="
                    btn btn-primary btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  For tech visit
                  <b-badge v-if="totalTechVisit > 0" variant="info">{{
                    totalTechVisit
                  }}</b-badge>
                </button>
                <button
                  @click="filterChange('3')"
                  type="button"
                  class="
                    btn btn-success btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Fixed
                </button>
                <button
                  v-b-modal="'modalMultipleFilterTicket'"
                  type="button"
                  class="
                    btn btn-success btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                  @click="filterData = 'table'"
                >
                  Multiple Filter
                </button>
              </b-row>
            </div>
            <div style="width: 50%; height: 100%">
              <b-card
                border-variant="default"
                align="center"
                style="width: 80%; float: right; margin-right: 15px"
                class="trend-bcard"
                bg-variant="light"
              >
                <header v-if="trendTime == '3'">
                  <p style="font-weight: bold">TICKETS TREND AS OF TODAY</p>
                </header>
                <header v-if="trendTime == '' || trendTime == '1'">
                  <p style="font-weight: bold">
                    TICKETS TREND FOR THE PAST 24 HOURS
                  </p>
                </header>
                <header v-if="trendTime == '2'">
                  <p style="font-weight: bold">
                    TICKETS TREND FOR THE PAST 48 HOURS
                  </p>
                </header>
                <b-card-text style="width: 100%">
                  <div
                    class="input-group input-group-sm mb-3"
                    style="display: flex"
                  >
                    <model-list-select
                      style="float: left; background: #e4e4e4; width: 20%"
                      :list="time"
                      v-model="trendTime"
                      option-value="id"
                      option-text="name"
                      placeholder="24 Hours"
                    ></model-list-select>
                    <model-list-select
                      style="
                        float: left;
                        margin-left: 5px;
                        background: #e4e4e4;
                        width: 53%;
                      "
                      :list="regions"
                      v-model="trendregion"
                      option-value="id"
                      option-text="name"
                      placeholder="Select region"
                    ></model-list-select>
                    <div class="input-group-append">
                      <button
                        class="btn"
                        type="button"
                        style="
                          font-size: 12px;
                          background: green;
                          color: white;
                          width: 55px;
                        "
                        @click="generateTrend"
                      >
                        Filter
                      </button>
                      <button
                        class="btn"
                        type="button"
                        style="
                          font-size: 12px;
                          background: #6c757d;
                          color: white;
                          margin-left: 2px;
                        "
                        @click="clearTrend"
                      >
                        Clear Filter
                      </button>
                    </div>
                  </div>
                </b-card-text>
                <br />

                <div class="top-records">
                  <!-- <div style="width:32%"> -->
                  <b-card
                    no-body
                    class="text-center"
                    v-for="(trend, index) in data"
                    :key="index"
                  >
                    <div class="text-danger">
                      <h5>{{ trend.total }}</h5>

                      <p>{{ trend.name }}</p>
                    </div>
                  </b-card>
                </div>
              </b-card>

              <br />
            </div>
          </div>

          <div style="display: flex">
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
                    v-b-modal="'modalColumnSettings'"
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
        </div>

        <div id="scrollmenuContainer" class="scrollmenu" @mousemove="mousemove">
          <b-table
            id="ticketTable"
            class="elClr"
            striped
            show-empty
            hover
            outlined
            :fields="fields"
            :items="items"
            :filter="tblFilter"
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            thead-class="cursorPointer-th"
            @row-clicked="tblRowClicked"
            @filtered="onFiltered"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
          >
            <template v-slot:cell(statname)="data">
              <span v-html="data.value"></span>
            </template>
            <template v-slot:cell(compname)="data">
              <span v-html="data.value"></span>
            </template>
            <template v-slot:cell(connection_status)="data">
              <span v-html="data.value"></span>
            </template>

            <template v-slot:cell(mTicket_id)="data">
              <span v-html="data.value"></span>
            </template>
            <!-- <span slot="statname" slot-scope="data" v-html="data.value"></span>
            <span slot="mTicket_id" slot-scope="data" v-html="data.value"></span>-->
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <!-- <template slot="target_date" slot-scope="row"> -->

            <!-- <span
                v-if="row.item.target_date == null &&
                row.item.statname == 'Pending'"
            >None</span>-->
            <template v-slot:cell(target_date)="row">
              <center>
                <span v-if="row.item.statname == 'Close'">Fixed</span>
              </center>
              <center>
                <span v-if="row.item.statname == 'For Rebates'">Fixed</span>
              </center>

              <b-button
                style="width: 100%"
                v-show="
                  (row.item.statname == 'Urgent' &&
                    row.item.target_date == null) ||
                  (row.item.statname == 'For Tech Visit' &&
                    row.item.target_date == null) ||
                  (row.item.statname == 'Modem/Line Transfer' &&
                    row.item.target_date == null)
                "
                variant="info"
                @click="openModalUpdateTargetDate(row.item)"
                :disabled="!roles.create_client_details"
                >Assign</b-button
              >

              <b-button
                variant="success"
                style="width: 100%"
                v-if="
                  row.item.target_date != null &&
                  row.item.target_date != datenow &&
                  row.item.target_date != dateTomorrow &&
                  row.item.target_date != dateYesterday &&
                  1 > dateDiffInDays(datenow, row.item.target_date)
                "
                :disabled="
                  row.item.statname != 'Urgent' || !roles.create_client_details
                "
                @click="openModalUpdateTargetDate(row.item)"
                >{{ row.item.tdateFormated }}</b-button
              >

              <b-button
                variant="success"
                style="width: 100%"
                v-if="row.item.target_date == datenow"
                :disabled="
                  row.item.statname != 'Urgent' || !roles.create_client_details
                "
                @click="openModalUpdateTargetDate(row.item)"
                >Today</b-button
              >
              <b-button
                variant="success"
                style="width: 100%"
                v-if="row.item.target_date == dateTomorrow"
                :disabled="
                  row.item.statname != 'Urgent' || !roles.create_client_details
                "
                @click="openModalUpdateTargetDate(row.item)"
                >Tomorrow</b-button
              >

              <b-button
                variant="warning"
                style="width: 100%"
                v-if="row.item.target_date == dateYesterday"
                :disabled="
                  row.item.statname != 'Urgent' || !roles.create_client_details
                "
                @click="openModalUpdateTargetDate(row.item)"
                >Yesterday</b-button
              >

              <b-button
                variant="danger"
                style="width: 100%"
                v-if="
                  1 < dateDiffInDays(datenow, row.item.target_date) &&
                  row.item.target_date != null
                "
                :disabled="
                  row.item.statname != 'Urgent' || !roles.create_client_details
                "
                @click="openModalUpdateTargetDate(row.item)"
                >{{ dateDiffInDays(datenow, row.item.target_date) }} Days
                delay</b-button
              >
            </template>

            <template slot="table-caption"></template>
          </b-table>
        </div>
      </div>
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

      <!-- modals_container----------------------------------------------------------------------------------- -->
      <div class="modals_container">
        <!--modalAddTicketStatus-------->
        <b-modal
          id="modalAddStat"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="Manage Status"
        >
          <!-- ADD STATUS-------------------------------------------------------------------------------------->
          <div class="elBG panel" v-if="roles.create_ticket_status">
            <div class="panel-heading">
              <p class="elClr panel-title">Add Status</p>
            </div>
            <div class="elClr panel-body">
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Status:</p>
                </div>
                <div class="col-lg-9">
                  <input
                    type="text"
                    name="status"
                    ref="status"
                    class="form-control"
                    v-b-tooltip.hover
                    title="Input the new status"
                    placeholder="Status"
                    v-validate="'required'"
                    v-model.trim="ticketStat.name"
                    autocomplete="off"
                    autofocus="on"
                  />
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('status')"
                    >Status is required.</small
                  >
                </div>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="heading-elements">
                <button
                  type="button"
                  class="btn btn-success btn-labeled pull-right"
                  v-on:click="btnAddStat"
                >
                  ADD
                </button>
              </div>
            </div>
          </div>
          <!-- END ADD STATUS------------------------------->
          <hr v-if="roles.create_ticket_status" />
          <!--Form-------->
          <div class="elBG panel">
            <div class="panel-heading">
              <p class="elClr panel-title">View Status</p>
            </div>
            <div class="elClr panel-body">
              <div>
                <b-row style="margin: 10px">
                  <b-col md="5" class="my-1">
                    <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                      <b-input-group>
                        <b-form-input
                          v-model="tblFilter"
                          placeholder="Filter"
                        ></b-form-input>
                        <b-input-group-append>
                          <b-button
                            :disabled="!tblFilter"
                            @click="tblFilter = ''"
                            >Clear</b-button
                          >
                        </b-input-group-append>
                      </b-input-group>
                    </b-form-group>
                  </b-col>
                  <b-col md="5 " class="my-1"></b-col>

                  <b-col md="2 " class="my-1">
                    <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                      <b-form-select
                        v-model="perPage"
                        :options="pageOptions"
                      ></b-form-select>
                    </b-form-group>
                  </b-col>
                </b-row>

                <b-table
                  class="elClr"
                  striped
                  show-empty
                  hover
                  outlined
                  :fields="statfield"
                  :items="Status_Ticket"
                  :filter="tblFilter"
                  :busy="tblisBusy"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :tbody-tr-class="StattblRowClass"
                  head-variant=" elClr"
                  thead-class="cursorPointer-th"
                  @filtered="StatonFiltered"
                >
                  <template v-slot:cell(actions)="row">
                    <b-button
                      size="sm"
                      variant="info"
                      @click="
                        OpenModeleditStat(row.item, row.index, $event.target)
                      "
                      v-if="roles.update_ticket_status"
                      >Update</b-button
                    >
                    <b-button
                      size="sm"
                      variant="danger"
                      @click="delStatus(row.item, row.index, $event.target)"
                      v-if="roles.delete_ticket_status"
                      >Delete</b-button
                    >
                  </template>

                  <div slot="table-busy" class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                  </div>
                </b-table>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="row" style="background-color: ; padding: 15px">
                <div class="col-md-8" style="background-color: ">
                  <span class="elClr">{{ StattotalRows }} item/s found.</span>
                </div>

                <div class="col-md-4" style="background-color: ">
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="StattotalRows"
                    :per-page="perPage"
                    class="my-0 pull-right"
                  ></b-pagination>
                </div>
              </div>
            </div>
          </div>
          <!--Form-------->

          <div slot="modal-footer" slot-scope="{}"></div>
        </b-modal>
        <!--end of modalAddTicketStatus-------->

        <!--editStatmodal------------------------------------------------------------------------------------>
        <b-modal
          id="editStatmodal"
          centered
          title="Edit Status"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
        >
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">edit status:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                name="status"
                ref="status"
                class="form-control"
                v-b-tooltip.hover
                title="Input the new status"
                placeholder="Status"
                v-validate="'required'"
                v-model.trim="ticketStat.name"
                autocomplete="off"
                autofocus="on"
              />
            </div>
          </div>
          <template slot="modal-footer" slot-scope="{}">
            <b-button size="sm" variant="success" @click="StatbtnUpdate()"
              >Update</b-button
            >
          </template>
        </b-modal>
        <!--end of editStatmodal-->

        <!-- modalAddComp -->
        <b-modal
          id="modalAddComp"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="Manage Complaints"
        >
          <!-- ADD COMPLAINT-------------------------------------------------------------------------------------->
          <div class="elBG panel" v-if="roles.create_ticket_status">
            <div class="panel-heading">
              <p class="elClr panel-title">Add Complaint</p>
            </div>
            <div class="elClr panel-body">
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Complaint:</p>
                </div>
                <div class="col-lg-9">
                  <input
                    type="text"
                    name="complaint"
                    ref="complaint"
                    class="form-control"
                    v-b-tooltip.hover
                    title="Input the new complaint"
                    placeholder="Complaint"
                    v-validate="'required'"
                    v-model.trim="ticketComp.name"
                    autocomplete="off"
                    autofocus="on"
                  />
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('complaint')"
                    >Complaint is required.</small
                  >
                </div>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="heading-elements">
                <button
                  type="button"
                  class="btn btn-success btn-labeled pull-right"
                  v-on:click="btnAddComp"
                >
                  ADD
                </button>
              </div>
            </div>
          </div>
          <hr v-if="roles.create_ticket_status" />
          <!--Table-------->
          <div class="elBG panel">
            <div class="panel-heading">
              <p class="elClr panel-title">View Complaints</p>
            </div>
            <div class="elClr panel-body">
              <div>
                <b-row style="margin: 10px">
                  <b-col md="5" class="my-1">
                    <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                      <b-input-group>
                        <b-form-input
                          v-model="tblFilter"
                          placeholder="Filter"
                        ></b-form-input>
                        <b-input-group-append>
                          <b-button
                            :disabled="!tblFilter"
                            @click="tblFilter = ''"
                            >Clear</b-button
                          >
                        </b-input-group-append>
                      </b-input-group>
                    </b-form-group>
                  </b-col>
                  <b-col md="5 " class="my-1"></b-col>

                  <b-col md="2 " class="my-1">
                    <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                      <b-form-select
                        v-model="perPage"
                        :options="pageOptions"
                      ></b-form-select>
                    </b-form-group>
                  </b-col>
                </b-row>

                <b-table
                  class="elClr"
                  striped
                  show-empty
                  hover
                  outlined
                  :fields="compField"
                  :items="complaints_new"
                  :filter="tblFilter"
                  :busy="tblisBusy"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :tbody-tr-class="ComptblRowClass"
                  head-variant=" elClr"
                  thead-class="cursorPointer-th"
                  @filtered="ComponFiltered"
                >
                  <template v-slot:cell(actions)="row">
                    <b-button
                      size="sm"
                      variant="info"
                      @click="
                        OpenModeleditComp(row.item, row.index, $event.target)
                      "
                      v-if="roles.update_ticket_status"
                      >Update</b-button
                    >
                    <b-button
                      size="sm"
                      variant="danger"
                      @click="delComp(row.item, row.index, $event.target)"
                      v-if="roles.delete_ticket_status"
                      >Delete</b-button
                    >
                  </template>

                  <div slot="table-busy" class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                  </div>
                </b-table>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="row" style="background-color: ; padding: 15px">
                <div class="col-md-8" style="background-color: ">
                  <span class="elClr">{{ ComptotalRows }} item/s found.</span>
                </div>

                <div class="col-md-4" style="background-color: ">
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="ComptotalRows"
                    :per-page="perPage"
                    class="my-0 pull-right"
                  ></b-pagination>
                </div>
              </div>
            </div>
          </div>
          <!--Form-------->

          <div slot="modal-footer" slot-scope="{}"></div>
        </b-modal>
        <!-- end of modalAddComp -->

        <!-- editCompmodal -->
        <b-modal
          id="editCompmodal"
          centered
          title="Edit Complaint"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
        >
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Edit Complaint:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                name="complaint_update"
                ref="complaint_update"
                class="form-control"
                v-b-tooltip.hover
                title="Input the new complaint"
                placeholder="Complaint"
                v-validate="'required'"
                v-model.trim="ticketComp.name"
                autocomplete="off"
                autofocus="on"
              />
            </div>
          </div>
          <template slot="modal-footer" slot-scope="{}">
            <b-button size="sm" variant="success" @click="CompbtnUpdate()"
              >Update</b-button
            >
          </template>
        </b-modal>
        <!-- end of editCompmodal -->

        <!-- modalAddAdv -->
        <b-modal
          id="modalAddAdv"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="Manage Advisory Templates"
        >
          <!-- ADD template-------------------------------------------------------------------------------------->
          <div class="elBG panel" v-if="roles.create_ticket_status">
            <div class="panel-heading">
              <p class="elClr panel-title">Add Template</p>
            </div>
            <div class="elClr panel-body">
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Template:</p>
                </div>
                <div class="col-lg-9">
                  <input
                    type="text"
                    name="title"
                    ref="title"
                    class="form-control"
                    v-b-tooltip.hover
                    title="Input template's name"
                    placeholder="Template Name"
                    v-validate="'required'"
                    v-model.trim="manageAdv.name"
                    autocomplete="off"
                    autofocus="on"
                  />
                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('title')"
                    >Template name is required.</small
                  >
                </div>
              </div>
              <br />
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Content:</p>
                </div>
                <div class="col-lg-9">
                  <textarea
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
                    rows="8"
                    name="content"
                    ref="content"
                    class="form-control"
                    v-b-tooltip.hover
                    title="Input Content "
                    placeholder="Enter message here. . ."
                    :maxlength="maxText"
                    v-model.trim="manageAdv.content"
                    v-validate="'required'"
                  ></textarea>

                  <small
                    class="text-danger pull-left"
                    v-show="errors.has('content')"
                    >Template content is required.</small
                  >
                  <span style="float: right">
                    <b>/450</b>
                  </span>
                  <span style="float: right">
                    <b v-text="maxText - msg.length"></b>
                  </span>
                </div>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="heading-elements">
                <button
                  type="button"
                  class="btn btn-success btn-labeled pull-right"
                  v-on:click="btnAddAdv"
                >
                  ADD
                </button>
              </div>
            </div>
          </div>
          <hr v-if="roles.create_ticket_status" />
          <!--Table-------->
          <div class="elBG panel">
            <div class="panel-heading">
              <p class="elClr panel-title">View Templates</p>
            </div>
            <div class="elClr panel-body">
              <div>
                <b-row style="margin: 10px">
                  <b-col md="5" class="my-1">
                    <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                      <b-input-group>
                        <b-form-input
                          v-model="tblFilter"
                          placeholder="Filter"
                        ></b-form-input>
                        <b-input-group-append>
                          <b-button
                            :disabled="!tblFilter"
                            @click="tblFilter = ''"
                            >Clear</b-button
                          >
                        </b-input-group-append>
                      </b-input-group>
                    </b-form-group>
                  </b-col>
                  <b-col md="5 " class="my-1"></b-col>

                  <b-col md="2 " class="my-1">
                    <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                      <b-form-select
                        v-model="perPage"
                        :options="pageOptions"
                      ></b-form-select>
                    </b-form-group>
                  </b-col>
                </b-row>

                <b-table
                  class="elClr"
                  striped
                  show-empty
                  hover
                  outlined
                  :fields="advField"
                  :items="templates"
                  :filter="tblFilter"
                  :busy="tblisBusy"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :tbody-tr-class="ComptblRowClass"
                  head-variant=" elClr"
                  thead-class="cursorPointer-th"
                  @filtered="AdvonFiltered"
                >
                  <template v-slot:cell(actions)="row">
                    <b-button
                      size="sm"
                      variant="info"
                      @click="
                        OpenModeleditAdv(row.item, row.index, $event.target)
                      "
                      v-if="roles.update_ticket_status"
                      >Update</b-button
                    >
                    <b-button
                      size="sm"
                      variant="danger"
                      @click="delAdv(row.item, row.index, $event.target)"
                      v-if="roles.delete_ticket_status"
                      >Delete</b-button
                    >
                  </template>

                  <div slot="table-busy" class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                  </div>
                </b-table>
              </div>
            </div>
            <div class="elClr panel-footer">
              <div class="row" style="background-color: ; padding: 15px">
                <div class="col-md-8" style="background-color: ">
                  <span class="elClr">{{ AdvtotalRows }} item/s found.</span>
                </div>

                <div class="col-md-4" style="background-color: ">
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="AdvtotalRows"
                    :per-page="perPage"
                    class="my-0 pull-right"
                  ></b-pagination>
                </div>
              </div>
            </div>
          </div>
          <!--Form-------->

          <div slot="modal-footer" slot-scope="{}"></div>
        </b-modal>
        <!-- editAddAdv -->
        <b-modal
          id="editAdvmodal"
          centered
          title="Edit Template"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
        >
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Template:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                name="title_update"
                ref="title_update"
                class="form-control"
                v-b-tooltip.hover
                title="Input the new complaint"
                placeholder="Complaint"
                v-model="manageAdv.name"
                autocomplete="off"
                autofocus="on"
              />
            </div>
          </div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Content:</p>
            </div>
            <div class="col-lg-9">
              <textarea
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                spellcheck="false"
                rows="8"
                name="content_update"
                ref="content_update"
                class="form-control"
                v-b-tooltip.hover
                title="Input Content "
                placeholder="Enter message here. . ."
                :maxlength="maxText"
                v-model="manageAdv.content"
              ></textarea>
              <span style="float: right">
                <b>/450</b>
              </span>
              <span style="float: right">
                <b v-text="maxText - msg.length"></b>
              </span>
            </div>
          </div>
          <template slot="modal-footer" slot-scope="{}">
            <b-button size="sm" variant="success" @click="AdvbtnUpdate()"
              >Update</b-button
            >
          </template>
        </b-modal>
        <!-- end of editCompmodal -->
        <!-- end of modalAddAdv -->

        <!-- modalSendSMS ---------------------------------------------------------------------------------------->
        <b-modal
          id="modalSendText"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="INET SMS"
        >
          <div style="width: 50%; display: flex; float: right; height: 80%">
            <model-list-select
              style="margin-left: 5px; background: #e4e4e4; width: 60%"
              :list="templates"
              v-model="selectedTemp"
              option-value="id"
              option-text="name"
              placeholder="Select Template"
              @input="selectTemp"
            ></model-list-select>
            <div class="input-group-append" style="width: 40%">
              <b-button
                squared
                variant="dark"
                style="width: 50%; max-height: 100%; color: white"
                @click="clearTemp"
                >Clear Filter</b-button
              >
              <b-button
                v-b-modal="'modalMultipleFilterClient'"
                squared
                variant="success"
                style="
                  width: 50%;
                  max-height: 100%;
                  color: white;
                  margin-left: 3px;
                "
                @click="filterData = 'text'"
                >Bulk Add</b-button
              >
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            style="width: 100%; display: flex; margin-top: 50px"
          >
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 60%">Send To:</p>
              <b-button
                variant="outline-dark"
                style="width: 40%; float: right; height: 40px"
                v-b-tooltip="'Clear Recipients'"
                @click="resetRecipients"
                >X</b-button
              >
            </div>
            <div style="width: 85%" id="multiselectSMS">
              <multiselect
                v-model="selectedContacts"
                :options="contacts"
                label="name"
                track-by="contact"
                variant="success"
                multiple
                tag-placeholder="Add this number"
                open-direction="bottom"
                placeholder="Type to search or type number"
                :hide-selected="true"
                :clear-on-select="false"
                :close-on-select="false"
                :taggable="true"
                @tag="addTag"
              ></multiselect>
            </div>
          </div>

          <div class="rowFields mx-auto row" style="width: 100%; display: flex">
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 70%">Message:</p>
              <b-button
                variant="outline-dark"
                style="width: 40%; float: right; height: 30px"
                v-b-tooltip="'Clear Field'"
                @click="resetField"
                >X</b-button
              >
            </div>
            <div style="width: 85%">
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
                placeholder="Enter message here. . ."
                :maxlength="maxText"
                v-model="msg"
              ></textarea>
              <!-- <small style="float:right" v-text="maxText - msg.length"> </small> -->
              <span style="float: right">
                <b>/450</b>
              </span>
              <span style="float: right">
                <b v-text="maxText - msg.length"></b>
              </span>
            </div>
          </div>
          <div slot="modal-footer" slot-scope="{}">
            <b-button squared variant="success" @click="send">Send</b-button>
            <!-- <b-button squared variant="success" @click="showImage = !showImage" -->
          </div>
        </b-modal>

        <!-- end of modal sms -->

        <b-alert
          v-model="showSending"
          class="position-fixed fixed-bottom m-0 rounded-0"
          style="z-index: 2000"
          variant="warning"
          dismissible
          >Sending Message/s. . . . . . .</b-alert
        >

        <b-alert
          v-model="showSent"
          class="position-fixed fixed-bottom m-0 rounded-0"
          style="z-index: 2000"
          variant="success"
          dismissible
          >Messages Sent Successfully!</b-alert
        >

        <b-alert
          v-model="showFailed"
          class="position-fixed fixed-bottom m-0 rounded-0"
          style="z-index: 2000"
          variant="danger"
          dismissible
          >Sending Failed !</b-alert
        >

        <!-- modalAddTicket ---------------------------------------------------------------------------------------->
        <b-modal
          id="modalAddTicket"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          body-text-variant="dark"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="Ticket Form"
          @ok="handleOk"
        >
          <!-- form -->
          <form @submit.prevent>
            <div id="new ticket">
              <b-card align="center" style="height: 20px; border: none">
                <center>
                  <div class="ticketOption">
                    <p-radio
                      class="p-icon p-curve p-jelly"
                      v-model="ticket.ticketType"
                      value="1"
                      name="ticketType"
                      color="success"
                    >
                      <i class="icon mdi mdi-check" slot="extra"></i>
                      Individual
                    </p-radio>
                    <p-radio
                      class="p-icon p-curve p-jelly"
                      v-model="ticket.ticketType"
                      value="2"
                      name="ticketType"
                      color="success"
                    >
                      <i class="icon mdi mdi-check" slot="extra"></i>
                      Multiple
                    </p-radio>
                    <p-radio
                      class="p-icon p-curve p-jelly"
                      v-model="ticket.ticketType"
                      value="3"
                      name="ticketType"
                      color="success"
                    >
                      <i class="icon mdi mdi-check" slot="extra"></i>
                      Group
                    </p-radio>
                  </div>
                  <div class="statusBadge">
                    <h6>
                      Status
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '1'"
                        variant="warning"
                        >Pending</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '9'"
                        variant="dark"
                        >Modem/Line Transfer</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '2'"
                        variant="danger"
                        >Urgent</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '3'"
                        variant="success"
                        >Fixed</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '4'"
                        variant="primary"
                        >For Tech Visit</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '6'"
                        variant="secondary"
                        >For Observation</b-badge
                      >
                      <b-badge
                        v-if="ticket.Status_Ticket_id == '7'"
                        variant="info"
                        >For ITND</b-badge
                      >
                    </h6>
                  </div>
                </center>
              </b-card>

              <br />
              <!-- client details card  -->
              <b-card bg-variant="light" align="center" class="b-card-ticket">
                <center>
                  <div style="display: flex; width: 95%">
                    <div style="width: 15%; height: 20%">
                      <h6 style="text-align: left">Client Details</h6>
                    </div>
                    <br />

                    <!-- for individual tickets  -->
                    <div
                      style="width: 85%; height: 80%"
                      v-if="ticket.ticketType == '1'"
                    >
                      <b-row class="clientRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Account Name</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="clients"
                                v-model="clientSelected"
                                :custom-text="getClientDesc"
                                option-value="id"
                                option-text="name"
                                placeholder="Select/Search a account (name - region)"
                                @input="onChangeSelectClient"
                                name="client_id"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="ticket_exist"
                                :hidden="this.clientSelected.id == '108'"
                              >
                                Client already has a ticket. Refer to ticket no.
                                {{ this.ticket_num }}
                              </small>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('client_id')"
                                >Account name is required.</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                      <span style="display: none">{{ clientSelected.id }}</span>
                      <div
                        class="input-group"
                        style="margin-top: -10px; width: 50%"
                        v-show="ref.includes(clientSelected.id)"
                      >
                        <label
                          style="font-size: 12px; margin-top: 6px"
                          class="input-group-addon"
                          >Input Client Name</label
                        >
                        <input
                          id="msg"
                          type="text"
                          class="form-control ticketInputLine"
                          name="msg"
                          v-model.trim="ticket.complain"
                        />
                      </div>
                      <b-row class="clientRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Address</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-form-input
                              id="input-default"
                              style="height: 30px"
                              :placeholder="clientSelected.address"
                              disabled
                            ></b-form-input>
                          </b-form-group>
                        </b-col>
                      </b-row>
                      <b-row class="clientRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Area</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="areas"
                                v-model="ticket.area_id"
                                option-value="id"
                                option-text="name"
                                name="area"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('area')"
                                >Please select area</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                    </div>
                    <!-- for multiple tickets -->
                    <div
                      style="width: 85%; height: 80%"
                      v-if="ticket.ticketType == '2'"
                    >
                      <div class="rowFields mx-auto row multipleClient">
                        <div class="multipleClient2">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Account Name</label
                          >
                        </div>
                        <div style="width: 78%; margin-left: 1.5px">
                          <multiselect
                            v-model="ticket.multipleClients"
                            :options="clients"
                            label="name"
                            track-by="id"
                            multiple
                            tag-placeholder="Add this account"
                            open-direction="bottom"
                            placeholder="Type to search or type account name"
                            :hide-selected="true"
                            :clear-on-select="false"
                            :close-on-select="false"
                            :taggable="true"
                            @input="checkClients"
                          ></multiselect>
                        </div>
                      </div>
                      <b-row class="clientRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Client Region</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="clientRegions"
                                v-model="ticket.clientRegion"
                                option-value="id"
                                option-text="name"
                                placeholder="Select client region"
                                name="region"
                              ></model-list-select>
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                      <div
                        class="input-group"
                        style="margin-top: -10px; width: 50%"
                        v-show="ticket.clientRegion != null"
                      >
                        <label
                          style="font-size: 12px; margin-top: 6px"
                          class="input-group-addon"
                          >Input Client Name</label
                        >
                        <input
                          id="msg"
                          type="text"
                          class="form-control ticketInputLine"
                          name="msg"
                          v-model.trim="ticket.complain"
                        />
                        <button
                          class="btn btn-success btn-labeled"
                          style="margin-left: 3px"
                          @click="addClient(ticket.complain)"
                        >
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>
                      <b-row class="clientRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Area</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="areas"
                                v-model="ticket.area_id"
                                option-value="id"
                                option-text="name"
                                name="area"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('area')"
                                >Please select area</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                    </div>
                    <!-- for group tickets -->
                    <div
                      style="width: 85%; height: 80%"
                      v-if="ticket.ticketType == '3'"
                    >
                      <b-button
                        v-b-modal="'modalMultipleFilterClient'"
                        squared
                        variant="success"
                        style="
                          width: 20%;
                          max-height: 100%;
                          color: white;
                          float: right;
                        "
                        @click="filterData = 'ticket'"
                        >Bulk Add</b-button
                      >
                      <br />
                      <div class="rowFields mx-auto row multipleClient">
                        <div class="multipleClient2">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 3px;
                            "
                            >Account Name</label
                          >
                        </div>
                        <div style="width: 78%; margin-left: 1.5px">
                          <multiselect
                            v-model="ticket.multipleClients"
                            :options="clients"
                            label="name"
                            track-by="id"
                            multiple
                            tag-placeholder="Add this account"
                            open-direction="bottom"
                            placeholder="Type to search or type account name"
                            :hide-selected="true"
                            :clear-on-select="false"
                            :close-on-select="false"
                            :taggable="true"
                          ></multiselect>
                        </div>
                      </div>
                    </div>
                  </div>
                </center>
              </b-card>
              <br />
              <!-- complaint details card -->
              <b-card bg-variant="light" align="center" class="b-card-ticket">
                <center>
                  <div style="display: flex; width: 95%">
                    <div style="width: 15%; height: 20%">
                      <h6 style="text-align: left">Complaint Details</h6>
                    </div>
                    <br />

                    <div style="width: 85%; height: 80%">
                      <b-row class="findingsRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 5px;
                            "
                            >Complaint</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="complaints_new"
                                v-model="ticket.complaint_new"
                                option-value="id"
                                option-text="name"
                                placeholder="Select Client Complaint"
                                name="complaint_id"
                                v-validate="'required'"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('complaint_id')"
                                >Please select complaint</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>

                      <b-row class="findingsRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 5px;
                            "
                            >Connection Status</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="connection_status_list"
                                v-model="ticket.connection_status"
                                option-value="id"
                                option-text="name"
                                placeholder="Select Connection Status"
                                name="connection_status"
                                v-validate="'required'"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('connection_status')"
                                >Please select connection status</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                      <b-row class="findingsRow">
                        <b-col class="clientCol">
                          <label
                            style="
                              float: left;
                              padding-left: 7px;
                              margin-top: 5px;
                            "
                            >Ticket Status</label
                          >
                        </b-col>
                        <b-col
                          style="
                            margin-left: -4px;
                            margin-top: -5px;
                            max-width: 80%;
                          "
                        >
                          <b-form-group>
                            <b-input-group>
                              <model-list-select
                                :list="Status_Ticket"
                                v-model="ticket.Status_Ticket_id"
                                option-value="id"
                                option-text="name"
                                placeholder="select status"
                                name="status"
                                v-validate="'required'"
                              ></model-list-select>
                              <small
                                class="text-danger pull-left"
                                v-show="errors.has('status')"
                                >Please select ticket status</small
                              >
                            </b-input-group>
                          </b-form-group>
                        </b-col>
                      </b-row>
                      <b-row class="findingsRow">
                        <b-col
                          style="float: left; margin-top: -5px; width: 100%"
                        >
                          <b>NOTE:</b>
                          <b-form-textarea
                            id="textarea-small"
                            size="sm"
                            v-model.lazy="ticket.cacti"
                            class="ticketInputLine2"
                            rows="1"
                            max-rows="3"
                            no-resize
                            placeholder="add details here . ."
                          ></b-form-textarea>
                        </b-col>
                      </b-row>

                      <br />
                    </div>
                  </div>

                  <!-- trouble checkboxes -->
                  <div id="troubleBoxes">
                    <b-card align="center" class="trouble-bcard">
                      <div
                        id="bucket"
                        style="display: flex; margin-top: 0; width: 100%"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes"
                              >BUCKET SERVER STATUS :</label
                            >
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="8"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Can arp-ed</label>
                            </div>
                          </div>
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="9"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Can't be arp-ed</label>
                            </div>
                          </div>
                          <div
                            class="input-group"
                            style="width: 53%; margin-right: 30px; float: right"
                          >
                            <label class="tsCheckboxes">Usage</label>
                            <input
                              id="bwmon"
                              type="text"
                              class="form-control usageLine"
                              v-model="ticket.bwmon"
                              name="msg"
                            />
                          </div>
                        </div>
                      </div>

                      <div
                        id="device"
                        style="width: 100%; display: flex; margin-top: 0"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes">DEVICE STATUS :</label>
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="10"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Blinking LOS</label>
                            </div>
                          </div>
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="11"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Blinking PON</label>
                            </div>
                          </div>
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="12"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Stable</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div
                        id="others"
                        style="width: 100%; display: flex; margin-top: -15px"
                      >
                        <div style="width: 30%"></div>
                        <div style="width: 70%">
                          <div class="input-group" style="width: 95%">
                            <label class="tsCheckboxes">Others</label>
                            <input
                              id="loss"
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="ticket.device"
                              name="msg"
                            />
                          </div>
                        </div>
                      </div>
                      <div
                        id="loss"
                        style="width: 100%; display: flex; margin-top: 5px"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes">OPTICAL POWER :</label>
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div class="input-group" style="width: 95%">
                            <label class="tsCheckboxes">Loss</label>
                            <input
                              id="loss"
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="ticket.loss"
                              name="msg"
                            />
                          </div>
                        </div>
                      </div>
                      <div
                        id="downtime"
                        style="width: 100%; display: flex; margin-top: 0"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes"
                              >AFFECTED BY DOWNTIME :</label
                            >
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div class="input-group" style="width: 95%">
                            <label class="tsCheckboxes">Details</label>
                            <input
                              id="downtime"
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="ticket.downtime"
                              name="msg"
                            />
                          </div>
                        </div>
                      </div>
                      <div
                        id="ping"
                        style="width: 100%; display: flex; margin-top: 0"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes"
                              >PING & TRACEROUTE TEST :</label
                            >
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="6"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Attached Results</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div
                        id="speed"
                        style="width: 100%; display: flex; margin-top: 0"
                      >
                        <div style="width: 30%">
                          <b-form-group style="float: left; text-align: left">
                            <label class="tsCheckboxes">SPEED TEST :</label>
                          </b-form-group>
                        </div>
                        <div style="width: 70%">
                          <div
                            class="
                              pretty
                              p-icon p-jelly p-bigger
                              troubleCheckbox
                            "
                          >
                            <input
                              type="checkbox"
                              v-model="ticket.selected_trouble"
                              value="7"
                            />
                            <div class="state p-success">
                              <i class="icon mdi mdi-check"></i>
                              <label>Attached Results</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- </div> -->
                    </b-card>
                  </div>

                  <!-- <span>{{ ticket.selected_trouble }}</span> -->
                </center>
              </b-card>
              <br />
              <!-- attachments card -->
              <b-card bg-variant="light" align="center" class="b-card-ticket">
                <center>
                  <div style="display: flex; width: 100%">
                    <div style="width: 15%">
                      <h6 style="text-align: left">Attachments</h6>
                    </div>
                    <br />

                    <div style="width: 85%; height: 80%">
                      <UploadImages
                        type="input"
                        accept="image/*"
                        id="files"
                        name="files"
                        @changed="handleImages($event)"
                      />
                    </div>
                  </div>
                </center>
              </b-card>
              <!-- remarks -->
              <br />
              <b-card
                id="remarks"
                align="center"
                style="
                  max-height: 100%;
                  border: none;
                  background: none;
                  display: none;
                "
                :body-text-variant="' elClr'"
              >
                <center>
                  <div style="width: 100%">
                    <div
                      style="
                        width: 100%;
                        float: left;
                        font-size: 13px;
                        font-weight: bold;
                      "
                    >
                      <label style="float: left">Remarks</label>
                    </div>
                    <br />
                    <div style="width:100%;max-height;100%margin-top:10px">
                      <b-row class="reportRow">
                        <b-col style="margin-left: -10px; margin-top: 10px">
                          <b-form-group>
                            <b-form-textarea
                              id="textarea-small"
                              size="sm"
                              v-model.lazy="ticket.remarks"
                              style="width: 100%; height: 100%"
                              placeholder="Type remarks here. . ."
                            ></b-form-textarea>
                          </b-form-group>
                        </b-col>
                      </b-row>
                    </div>
                  </div>
                </center>
              </b-card>
            </div>
          </form>
          <!-- /form -->
          <template slot="modal-footer" slot-scope="{}">
            <b-button
              size="sm"
              variant="success"
              type="file"
              accept="image/*"
              @click="btnCreate()"
              >Create</b-button
            >
          </template>
        </b-modal>
        <!-- End modalAddTicket -->

        <!-- modalEditTicket -->
        <b-modal
          id="modalEditTicket"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          body-text-variant="dark"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
        >
          <div slot="modal-header" class="w-100">
            <div class="rowFields mx-auto row">
              <div class="col-lg-6">
                <h5>Ticket Info</h5>
              </div>
              <div class="col-lg-6">
                <i>
                  <p class="float-right textLabel1">
                    Created By: {{ editTicket.user.name }}
                    <br />
                    <br />
                    Created On: {{ editTicket.created_at_formated }}
                  </p>
                </i>
              </div>
            </div>
          </div>
          <!-- <template v-slot:modal-title style="background-color:red">

          </template>-->

          <!-- form -->

          <div id="newEditTicket">
            <b-card align="center" style="height: 20px; border: none">
              <center>
                <div style="float: left; margin-top: -20px; margin-left: -15px">
                  <button
                    class="btn btn-labeled btn-copy"
                    v-b-tooltip.hover
                    title="Duplicate ticket"
                    @click="duplicateTicket"
                  >
                    <i class="far fa-copy"></i> &nbsp;Copy
                  </button>
                </div>
                <div
                  style="float: right; margin-top: -20px; margin-right: -15px"
                >
                  <h6>
                    Status
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '1'"
                      variant="warning"
                      >Pending</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '9'"
                      variant="dark"
                      >Modem/Line Transfer</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '2'"
                      variant="danger"
                      >Urgent</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '3'"
                      variant="success"
                      >Fixed</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '4'"
                      variant="primary"
                      >For Tech Visit</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '6'"
                      variant="secondary"
                      >For Observation</b-badge
                    >
                    <b-badge
                      v-if="editTicket.Status_Ticket_id == '7'"
                      variant="info"
                      >For ITND</b-badge
                    >
                  </h6>
                </div>
              </center>
            </b-card>
            <br />
            <!-- edit client details -->
            <b-card
              bg-variant="light"
              align="center"
              style="max-height: 100%"
              class="editTicket-bcard"
              id="client"
              v-if="editTicket.ticketType == '1'"
            >
              <center>
                <div style="font-size: 12px" v-if="editChangeClient">
                  <b-row class="clientRow">
                    <p
                      class="textbtnedit"
                      @click="editChangeClient = !editChangeClient"
                    >
                      Cancel
                    </p>
                  </b-row>
                  <b-row class="clientRow">
                    <b-col class="clientCol">
                      <label
                        style="float: left; padding-left: 7px; margin-top: 3px"
                        >Account Name</label
                      >
                    </b-col>
                    <b-col
                      style="
                        margin-left: -4px;
                        margin-top: -5px;
                        max-width: 80%;
                      "
                    >
                      <b-form-group>
                        <b-input-group>
                          <model-list-select
                            :list="clients"
                            v-model="clientSelected"
                            :custom-text="getClientDesc"
                            option-value="id"
                            option-text="name"
                            placeholder="Select/Search a account (name - region)"
                            @input="onChangeSelectClientEdit"
                            name="client_id"
                          ></model-list-select>
                          <small
                            class="text-danger pull-left"
                            v-show="ticket_exist"
                            :hidden="this.clientSelected.id == '108'"
                          >
                            Client already has a ticket. Refer to ticket no.
                            {{ this.ticket_num }}
                          </small>
                          <small
                            class="text-danger pull-left"
                            v-show="errors.has('client_id')"
                            >Account name is required.</small
                          >
                        </b-input-group>
                      </b-form-group>
                    </b-col>
                  </b-row>
                  <b-row
                    class="clientRow"
                    v-show="ref.includes(clientSelected.id)"
                  >
                    <b-col
                      style="
                        margin-left: -4px;
                        margin-top: -4px;
                        max-width: 100%;
                      "
                    >
                      <div
                        class="input-group"
                        style="margin-top: -10px; width: 50%"
                      >
                        <label
                          style="font-size: 12px; margin-top: 6px"
                          class="input-group-addon"
                          >Input Client Name</label
                        >
                        <input
                          id="msg"
                          type="text"
                          class="form-control ticketInputLine"
                          name="msg"
                          v-model.trim="editTicket.complain"
                        />
                      </div>
                    </b-col>
                  </b-row>
                </div>
                <div style="display: flex; font-size: 12px" v-else>
                  <div style="width: 50%; float: left">
                    <label
                      style="
                        display: block;
                        text-align: left;
                        font-weight: bold;
                        color: green;
                      "
                      >Ticket No.: {{ editTicket.mTicket_id }}</label
                    >
                    <label
                      style="display: block; text-align: left"
                      :hidden="clientRef.includes(editTicket.client.name)"
                      >Account Name: {{ editTicket.client.name }}</label
                    >
                    <label
                      style="display: block; text-align: left"
                      v-show="clientRef.includes(editTicket.client.name)"
                      >Account Name: {{ editTicket.complain }}</label
                    >

                    <label style="display: block; text-align: left"
                      >Address: {{ editTicket.client.location }}</label
                    >
                    <label style="display: block; text-align: left"
                      >Contact No.: {{ editTicket.client.contact }}</label
                    >
                  </div>
                  <div style="width: 50%">
                    <label style="display: block; text-align: left"
                      >IP Assigned: {{ editTicket.ip_assigned }}</label
                    >
                    <label style="display: block; text-align: left"
                      >VLAN: {{ editTicket.vlan }}</label
                    >
                    <label style="display: block; text-align: left"
                      >OLT: {{ editTicket.ip }}</label
                    >

                    <label style="display: block; text-align: left"
                      >PON: {{ editTicket.pon }} - {{ editTicket.onu }}</label
                    >
                  </div>
                  <div style="float: right">
                    <p
                      class="textbtnedit"
                      @click="editChangeClient = !editChangeClient"
                    >
                      Edit
                    </p>
                  </div>
                  <br />
                </div>
              </center>
            </b-card>
            <b-card
              bg-variant="light"
              align="center"
              class="b-card-ticket"
              v-else
            >
              <div style="display: flex; font-size: 12px">
                <div style="width: 50%; float: left">
                  <label
                    style="
                      display: block;
                      text-align: left;
                      font-weight: bold;
                      color: green;
                    "
                    >Ticket No.: {{ editTicket.mTicket_id }}</label
                  >
                </div>

                <div style="float: right">
                  <p
                    class="textbtnedit"
                    @click="editChangeClient = !editChangeClient"
                  >
                    + Edit
                  </p>
                </div>
                <br />
              </div>
              <div style="float: right; margin-top: -10px" hidden>
                <p
                  class="textbtnedit"
                  @click="editChangeClient = !editChangeClient"
                >
                  + Edit
                </p>
              </div>
              <br />
              <center>
                <div style="display: flex; width: 95%">
                  <div style="width: 100%; height: 50%; margin-top: -15px">
                    <div
                      class="rowFields mx-auto row multipleClient"
                      v-show="editChangeClient"
                    >
                      <div style="width: 100%; margin-left: 1.5px">
                        <multiselect
                          v-model="addedClients"
                          :options="clients"
                          label="name"
                          track-by="id"
                          multiple
                          tag-placeholder="Add this account"
                          open-direction="bottom"
                          placeholder="Type to search or type account name"
                          :hide-selected="true"
                          :clear-on-select="false"
                          :close-on-select="false"
                          :taggable="true"
                          @input="addClientGroup()"
                        ></multiselect>
                      </div>
                    </div>
                    <br />
                    <b-table
                      :items="editTicket.ticket_group_client"
                      :fields="clientField"
                      striped
                      sticky-header
                      head-variant="dark"
                      class="group-clients"
                    >
                      <template v-slot:cell(actions)="row">
                        <b-button
                          size="sm"
                          variant="danger"
                          v-b-tooltip.hover
                          title="Remove Client"
                          @click="removeClient(row.item.client_id)"
                        >
                          <i class="fas fa-trash-restore-alt"></i>
                        </b-button>
                      </template>
                    </b-table>
                  </div>
                </div>
              </center>
            </b-card>
            <br />
            <!-- edit complaint details -->
            <b-card
              bg-variant="light"
              align="center"
              class="b-card-ticket"
              id="editComplaint"
            >
              <center>
                <div style="display: flex; width: 95%">
                  <div style="width: 15%; height: 20%">
                    <h6 style="text-align: left">Complaint Details</h6>
                  </div>
                  <br />

                  <div style="width: 85%; height: 80%">
                    <b-row class="findingsRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 5px;
                          "
                          >Complaint</label
                        >
                      </b-col>
                      <b-col
                        style="
                          margin-left: -4px;
                          margin-top: -5px;
                          max-width: 80%;
                        "
                      >
                        <b-form-group>
                          <b-input-group>
                            <model-list-select
                              :list="complaints_new"
                              v-model="editTicket.complaint_new"
                              option-value="id"
                              option-text="name"
                              placeholder="Select Client Complaint"
                              name="complaints_new"
                              v-validate="'required'"
                            ></model-list-select>
                            <small
                              class="text-danger pull-left"
                              v-show="errors.has('complaints_new')"
                              >Please select complaint</small
                            >
                          </b-input-group>
                        </b-form-group>
                      </b-col>
                    </b-row>

                    <b-row class="findingsRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 5px;
                          "
                          >Connection Status</label
                        >
                      </b-col>
                      <b-col
                        style="
                          margin-left: -4px;
                          margin-top: -5px;
                          max-width: 80%;
                        "
                      >
                        <b-form-group>
                          <b-input-group>
                            <model-list-select
                              :list="connection_status_list"
                              v-model="editTicket.connection_status"
                              option-value="id"
                              option-text="name"
                              placeholder="Select Connection Status"
                              name="connection_status"
                              v-validate="'required'"
                            ></model-list-select>
                            <small
                              class="text-danger pull-left"
                              v-show="errors.has('connection_status')"
                              >Please select connection status</small
                            >
                          </b-input-group>
                        </b-form-group>
                      </b-col>
                    </b-row>

                    <b-row class="findingsRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 5px;
                          "
                          >Ticket Status</label
                        >
                      </b-col>
                      <b-col
                        style="
                          margin-left: -4px;
                          margin-top: -5px;
                          max-width: 80%;
                        "
                      >
                        <b-form-group>
                          <b-input-group>
                            <model-list-select
                              :list="Status_Ticket"
                              v-model="editTicket.Status_Ticket_id"
                              option-value="id"
                              option-text="name"
                              placeholder="select status"
                              name="status"
                              v-validate="'required'"
                            ></model-list-select>
                            <small
                              class="text-danger pull-left"
                              v-show="errors.has('status')"
                              >Please select ticket status</small
                            >
                          </b-input-group>
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="findingsRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Area</label
                        >
                      </b-col>
                      <b-col
                        style="
                          margin-left: -4px;
                          margin-top: -5px;
                          max-width: 80%;
                        "
                      >
                        <b-form-group>
                          <b-input-group>
                            <model-list-select
                              :list="areas"
                              v-model="editTicket.area_id"
                              option-value="id"
                              option-text="name"
                              placeholder="select area"
                              name="area"
                            ></model-list-select>
                            <small
                              class="text-danger pull-left"
                              v-show="errors.has('area')"
                              >Please select area</small
                            >
                          </b-input-group>
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="findingsRow">
                      <b-col style="float: left; margin-top: -5px; width: 100%">
                        <b>NOTE:</b>
                        <b-form-textarea
                          id="textarea-small"
                          size="sm"
                          v-model.lazy="editTicket.cacti"
                          class="ticketInputLine2"
                          rows="1"
                          max-rows="3"
                          no-resize
                          placeholder="add details here . ."
                        ></b-form-textarea>
                      </b-col>
                    </b-row>

                    <br />
                  </div>
                </div>

                <!-- trouble checkboxes -->
                <div id="troubleBoxes">
                  <b-card align="center" class="trouble-bcard">
                    <div
                      id="bucket"
                      style="display: flex; margin-top: 0; width: 100%"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes"
                            >BUCKET SERVER STATUS :</label
                          >
                        </b-form-group>
                      </div>
                      <!-- <div style="width:70%;">
                        <b-form-group>
                          <b-form-checkbox
                            v-model="editTicket.selected_trouble"
                            style="float:left"
                            value="8"
                          >
                            <label class="tsCheckboxes">Can arp-ed</label>
                          </b-form-checkbox>
                          <b-form-checkbox
                            v-model="editTicket.selected_trouble"
                            style="float:left"
                            value="9"
                          >
                            <label class="tsCheckboxes">Can't be arp-ed</label>
                          </b-form-checkbox>
                          <div class="input-group" style="width:53%;">
                            <label class="tsCheckboxes">Usage</label>
                            <input
                              id="bwmon"
                              type="text"
                              class="form-control usageLine"
                              v-model="editTicket.bwmon"
                              name="msg"
                            />
                          </div>
                        </b-form-group>
                      </div>-->
                      <div style="width: 70%">
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="8"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Can arp-ed</label>
                          </div>
                        </div>
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="9"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Can't be arp-ed</label>
                          </div>
                        </div>
                        <div
                          class="input-group"
                          style="width: 53%; margin-right: 30px; float: right"
                        >
                          <label class="tsCheckboxes">Usage</label>
                          <input
                            id="bwmon"
                            type="text"
                            class="form-control usageLine"
                            v-model="editTicket.bwmon"
                            name="msg"
                          />
                        </div>
                      </div>
                    </div>
                    <div
                      id="device"
                      style="width: 100%; display: flex; margin-top: 0"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes">DEVICE STATUS :</label>
                        </b-form-group>
                      </div>
                      <div style="width: 70%">
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="10"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Blinking LOS</label>
                          </div>
                        </div>
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="11"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Blinking PON</label>
                          </div>
                        </div>
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="12"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Stable</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      id="others"
                      style="width: 100%; display: flex; margin-top: -15px"
                    >
                      <div style="width: 30%"></div>
                      <div style="width: 70%">
                        <div class="input-group" style="width: 95%">
                          <label class="tsCheckboxes">Others</label>
                          <input
                            id="loss"
                            type="text"
                            class="form-control ticketInputLine"
                            v-model="editTicket.device"
                            name="msg"
                          />
                        </div>
                      </div>
                    </div>
                    <div
                      id="loss"
                      style="width: 100%; display: flex; margin-top: 5px"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes">OPTICAL POWER :</label>
                        </b-form-group>
                      </div>
                      <div style="width: 70%">
                        <div class="input-group" style="width: 95%">
                          <label class="tsCheckboxes">Loss</label>
                          <input
                            id="loss"
                            type="text"
                            class="form-control ticketInputLine"
                            v-model="editTicket.loss"
                            name="msg"
                          />
                        </div>
                      </div>
                    </div>
                    <div
                      id="downtime"
                      style="width: 100%; display: flex; margin-top: 0"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes"
                            >AFFECTED BY DOWNTIME :</label
                          >
                        </b-form-group>
                      </div>
                      <div style="width: 70%">
                        <div class="input-group" style="width: 95%">
                          <label class="tsCheckboxes">Details</label>
                          <input
                            id="downtime"
                            type="text"
                            class="form-control ticketInputLine"
                            v-model="editTicket.downtime"
                            name="msg"
                          />
                        </div>
                      </div>
                    </div>
                    <div
                      id="ping"
                      style="width: 100%; display: flex; margin-top: 0"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes"
                            >PING & TRACEROUTE TEST :</label
                          >
                        </b-form-group>
                      </div>
                      <div style="width: 70%">
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="6"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Attached Results</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      id="speed"
                      style="width: 100%; display: flex; margin-top: 0"
                    >
                      <div style="width: 30%">
                        <b-form-group style="float: left; text-align: left">
                          <label class="tsCheckboxes">SPEED TEST :</label>
                        </b-form-group>
                      </div>
                      <div style="width: 70%">
                        <div
                          class="pretty p-icon p-jelly p-bigger troubleCheckbox"
                        >
                          <input
                            type="checkbox"
                            v-model="editTicket.selected_trouble"
                            value="7"
                          />
                          <div class="state p-success">
                            <i class="icon mdi mdi-check"></i>
                            <label>Attached Results</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- </div> -->
                  </b-card>
                </div>

                <!-- <span>{{ ticket.selected_trouble }}</span> -->
              </center>
            </b-card>
            <br />
            <!-- old ticket info -->
            <b-card
              bg-variant="light"
              align="center"
              style="max-height: 100%"
              class="editTicket-bcard"
              id="client"
            >
              <center>
                <div style="display: flex; font-size: 12px">
                  <div style="float: left">
                    <label
                      style="
                        display: block;
                        text-align: left;
                        font-weight: bold;
                        color: green;
                      "
                      >OLD TICKET FORM INFO</label
                    >
                    <label style="display: block; text-align: left"
                      >Complaint: {{ editTicket.complain }}</label
                    >
                    <label style="display: block; text-align: left"
                      >Findings: {{ editTicket.findings }}</label
                    >
                    <label style="display: block; text-align: left"
                      >Action: {{ editTicket.action }}</label
                    >
                  </div>
                  <br />
                </div>
              </center>
            </b-card>
            <!-- report bcard -->
            <b-card
              id="report"
              border-variant="dark"
              bg-variant="light"
              align="center"
              style="max-height: 100%"
              v-if="editTicket.Status_Ticket_id == '3'"
            >
              <center>
                <div style="display: flex; width: 95%">
                  <div style="width: 15%; height: 20%">
                    <h6 style="float: left">Report</h6>
                  </div>
                  <br />

                  <div style="width: 85%; height: 80%">
                    <b-row class="reportRow">
                      <b-col class="reportCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Findings</label
                        >
                      </b-col>
                      <b-col style="margin-left: -4px; margin-top: -5px">
                        <b-form-group>
                          <b-form-textarea
                            id="textarea-small"
                            size="sm"
                            style="width: 100%"
                            v-model="editTicket.rep_findings"
                            name="rep_findings"
                            v-validate="'required'"
                          ></b-form-textarea>
                          <small
                            class="text-danger pull-left"
                            v-show="errors.has('rep_findings')"
                            >Please input findings.</small
                          >
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="reportRow">
                      <b-col class="reportCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Action</label
                        >
                      </b-col>
                      <b-col style="margin-left: -4px; margin-top: -5px">
                        <b-form-group>
                          <b-form-textarea
                            id="textarea-small"
                            size="sm"
                            v-model="editTicket.rep_action"
                            style="width: 100%; height: 100%"
                            name="rep_action"
                            v-validate="'required'"
                          ></b-form-textarea>
                          <small
                            class="text-danger pull-left"
                            v-show="errors.has('rep_action')"
                            >Please input actions.</small
                          >
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="clientRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Consolidated Tech</label
                        >
                      </b-col>
                      <b-col style="margin-left: -4px; margin-top: -5px">
                        <b-form-group>
                          <b-form-input
                            id="input-default"
                            style="max-width: 100%; height: 30px"
                            v-model.lazy="editTicket.technical_assigned"
                            name="consolidated_tech"
                            v-validate="'required'"
                          ></b-form-input>
                          <small
                            class="text-danger pull-left"
                            v-show="errors.has('consolidated_tech')"
                            >Please input technical personnel/s assigned.</small
                          >
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="clientRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Date Time Fixed</label
                        >
                      </b-col>
                      <b-col style="margin-left: -4px; margin-top: -5px">
                        <b-form-group>
                          <date-picker
                            v-model="editTicket.date_time_fixed"
                            :config="DateTimeOptions"
                            @input="datetimeChange"
                            autocomplete="off"
                            style="max-width: 100%; height: 30px"
                            name="date_fixed"
                            v-validate="'required'"
                          ></date-picker>
                          <small
                            class="text-danger pull-left"
                            v-show="errors.has('date_fixed')"
                            >Please input date time fixed.</small
                          >
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="clientRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Hrs of Downtime</label
                        >
                      </b-col>
                      <b-col style="margin-left: -4px; margin-top: -5px">
                        <b-form-group>
                          <b-form-input
                            id="input-default"
                            style="max-width: 100%; height: 30px"
                            placeholder="Hours"
                            v-model="editTicket.downtime_hours"
                          ></b-form-input>
                        </b-form-group>
                      </b-col>
                    </b-row>
                    <b-row class="clientRow">
                      <b-col class="clientCol">
                        <label
                          style="
                            float: left;
                            padding-left: 7px;
                            margin-top: 3px;
                          "
                          >Rebatable</label
                        >
                      </b-col>
                      <b-col style="margin-left: 10px; margin-top: -5px">
                        <b-form-group>
                          <b-form-checkbox
                            style="float: left"
                            value="1"
                            v-model="editTicket.rebatable"
                          >
                            <label class="tsCheckboxes">Yes</label>
                          </b-form-checkbox>
                          <b-form-checkbox
                            style="float: left"
                            value="2"
                            v-model="editTicket.rebatable"
                          >
                            <label class="tsCheckboxes">No</label>
                          </b-form-checkbox>
                        </b-form-group>
                      </b-col>
                    </b-row>
                  </div>
                </div>
              </center>
            </b-card>
            <!-- remarks area -->
            <b-card
              id="remarks"
              align="center"
              style="max-height: 100%; border: none; background: none"
              :body-text-variant="' elClr'"
            >
              <center>
                <div style="width: 100%">
                  <div
                    style="
                      width: 100%;
                      float: left;
                      font-size: 13px;
                      font-weight: bold;
                    "
                  >
                    <label style="float: left">Remarks</label>
                  </div>
                  <br />
                  <div
                    style="
                      width: 100%;
                      float: left;
                      margin-top: 2px;
                      margin-bottom: 12px;
                      display: flex;
                    "
                  >
                    <div style="width: 80%; flex-grow: 1">
                      <b-form-textarea
                        id="textarea-small"
                        size="sm"
                        v-model.lazy="remarksText"
                        style="width: 100%; height: 90%"
                        placeholder="Type remarks here. . ."
                      ></b-form-textarea>
                    </div>
                    <div
                      style="
                        width: 12%;
                        height: 100%;
                        flex-grow: 1;
                        margin-left: 15px;
                      "
                    >
                      <b-button
                        squared
                        variant="success"
                        @click="addRemarks_clicked"
                        style="
                          width: 100%;
                          height: 32px;
                          float: right;
                          color: white;
                        "
                        >ADD REMARKS</b-button
                      >
                    </div>
                  </div>
                  <br />
                  <!-- remarks display -->
                  <div
                    style="margin-top: 20px"
                    v-for="(remarks, index) in editTicket.remarks_log"
                    :key="index"
                    v-show="remarks.form_type == 'ticket'"
                  >
                    <div id="title" style="display: flex; width: 100%">
                      <label
                        style="
                          float: left;
                          text-align: left;
                          font-size: 12px;
                          font-weight: bold;
                          width: 70%;
                        "
                      >
                        <i
                          class="fa fa-user-circle"
                          aria-hidden="true"
                          style="margin-right: 5px"
                        ></i>
                        {{ remarks.user.name }}
                      </label>

                      <label
                        style="
                          float: right;
                          text-align: right;
                          font-size: 10px;
                          width: 30%;
                        "
                        >{{ remarks.created_at }}</label
                      >
                    </div>

                    <br />
                    <div
                      id="body"
                      style="display: flex; width: 100%; margin-top: -8px"
                    >
                      <div style="width: 82%; margin-left: 5px" class="wrapper">
                        <!-- <label
                          style="float:left;text-align:left;"
                          role="textarea"
                          >{{ remarks.remarks }}</label
                        >-->
                        <b-form-textarea
                          id="textarea-small"
                          size="sm"
                          v-model.lazy="remarks.remarks"
                          class="remarks"
                          readonly="readonly"
                          rows="2"
                          max-rows="5"
                          no-resize
                        ></b-form-textarea>
                      </div>
                      <div style="width: 18%">
                        <label
                          style="float: right; cursor: pointer; color: blue"
                          @click="remarks.commentVisibility = 'show'"
                          v-show="remarks.commentVisibility == 'hide'"
                        >
                          Reply
                          <i class="fas fa-caret-down"></i>
                        </label>
                        <label
                          style="float: right; cursor: pointer; color: blue"
                          @click="remarks.commentVisibility = 'hide'"
                          v-show="remarks.commentVisibility == 'show'"
                        >
                          Reply
                          <i class="fas fa-caret-up"></i>
                        </label>
                      </div>
                    </div>

                    <!-- comments display -->

                    <div
                      style="
                        margin-top: 0;
                        background: none;
                        width: 95%;
                        float: right;
                      "
                      v-for="reply in remarks.replies"
                      :key="reply.id"
                    >
                      <div id="title" style="display: flex; width: 100%">
                        <label
                          style="
                            float: left;
                            text-align: left;
                            font-size: 12px;
                            font-weight: bold;
                            width: 70%;
                          "
                        >
                          <i
                            class="fa fa-user-circle"
                            aria-hidden="true"
                            style="margin-right: 5px"
                          ></i>
                          {{ reply.user.name }}
                        </label>

                        <label
                          style="
                            float: right;
                            text-align: right;
                            font-size: 10px;
                            width: 30%;
                          "
                          >{{ reply.created_at }}</label
                        >
                      </div>

                      <br />
                      <div
                        id="body"
                        style="display: flex; width: 95%; margin-top: -8px"
                      >
                        <label style="float: left; text-align: left">
                          {{ reply.comments }}
                        </label>
                      </div>
                    </div>

                    <!-- new comment -->
                    <div
                      style="
                        width: 90%;
                        float: right;
                        margin-top: 2px;
                        margin-bottom: 12px;
                        display: flex;
                      "
                      v-show="remarks.commentVisibility == 'show'"
                    >
                      <div style="width: 80%; flex-grow: 1">
                        <b-form-textarea
                          id="textarea - small"
                          size="sm"
                          v-model="commentsText[index]"
                          style="width: 100%; height: 90%"
                          placeholder="Type comment here. . ."
                        ></b-form-textarea>
                      </div>
                      <div
                        style="
                          width: 12%;
                          height: 100%;
                          flex-grow: 1;
                          margin-left: 15px;
                        "
                      >
                        <b-button
                          squared
                          variant="success"
                          @click="
                            addComments_clicked(
                              remarks.id,
                              index,
                              remarks.replies
                            )
                          "
                          style="
                            width: 100%;
                            height: 32px;
                            float: right;
                            color: white;
                          "
                          >ADD COMMENT</b-button
                        >
                      </div>
                    </div>
                  </div>
                  <br />
                </div>
              </center>
            </b-card>
          </div>

          <!-- form -->
          <div slot="modal-footer" class="w-100">
            <i>
              <p class="float-left textLabel1">
                Updated By: {{ editTicket.updated_by }}
              </p>
            </i>

            <b-button
              class="float-right margin-right-10"
              size="sm"
              variant="success"
              v-if="roles.update_ticket"
              @click="btnUpdate()"
              accept="image/*"
              >Save changes</b-button
            >
            <b-button
              class="float-right margin-right-10"
              size="sm"
              variant="danger"
              type="file"
              v-if="roles.delete_ticket"
              @click="btnDelete()"
              >Delete</b-button
            >

            <!-- <b-button
              class="float-right margin-right-10"
              size="sm"
              variant="info"
              v-if="roles.update_ticket"
              @click="btnAddRebates()"
              >Add Rebates</b-button
            >-->
            <!--
            <b-button
              class="float-right margin-right-10"
              size="sm"
              v-if="editTicket.downtime_hours != null"
              variant="warning"
              @click="btnRebates()"
              >Calculate Rebates</b-button
            >-->

            <b-button
              class="float-right margin-right-10"
              size="sm"
              v-if="roles.accounting && editTicket.Status_Ticket_id == '3'"
              variant="warning"
              v-b-modal="'modalRebates'"
              >Add Rebates</b-button
            >

            <b-button
              class="float-right margin-right-10"
              size="sm"
              v-if="roles.update_ticket"
              variant="primary"
              v-b-modal="'viewAttachments'"
              >View attachments</b-button
            >
            <b-button
              class="float-right margin-right-10"
              size="sm"
              v-if="roles.update_ticket && editTicket.Status_Ticket_id == '3'"
              variant="warning"
              @click="reopen"
              >Re-open Ticket</b-button
            >
            <b-button
              class="float-right margin-right-10"
              size="sm"
              variant="info"
              @click="btnFSR()"
              >Print Preview FSR</b-button
            >
          </div>
        </b-modal>
        <!-- End modalEditTicket -->

        <!-- attachment modal -->
        <b-modal
          id="viewAttachments"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
          title="ATTACHMENTS"
        >
          <div style="float: right; margin-top: -10px; margin-bottom: 10px">
            <b-button squared variant="outline-dark" @click="isShowing ^= true">
              <i class="material-icons">add_photo_alternate</i>
            </b-button>
          </div>
          <div style="width: 100%; display: flex" v-if="preview">
            <b-img
              thumbnail
              fluid
              :src="$attachment_path + preview"
              style="width: 100%; height: 500px !important"
            ></b-img>
          </div>
          <br />
          <div style="width: 100%; display: flex">
            <b-container class="bv-example-row">
              <b-row>
                <b-col
                  cols="2"
                  v-for="(item, i) in editTicket.attachments"
                  :key="i"
                  style="background: none"
                >
                  <b-img
                    thumbnail
                    fluid
                    v-b-tooltip="'Preview Image'"
                    :v-for="(item, i) in editTicket.attachments"
                    :src="$attachment_path + item"
                    @click="previewImg(item)"
                    style="
                      height: 80px !important;
                      width: 100px !important;
                      cursor: pointer;
                    "
                  ></b-img>
                </b-col>
              </b-row>
            </b-container>
          </div>

          <div class="rowFields mx-auto row" v-show="isShowing">
            <UploadImages
              type="input"
              accept="image/*"
              id="files"
              name="files"
              @changed="handleImages($event)"
            />
          </div>
        </b-modal>

        <!--modalUpdatePackage-------->
        <b-modal
          id="modalUpdatePackage"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="xl"
          title="Update Package"
        >
          <!--Form-------->
          <div class="rowFields mx-auto row" style="display: none">
            <div class="col-lg-3">
              <p class="textLabel">Package:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="packages"
                v-model="packEdit"
                option-value="id"
                option-text="name"
                placeholder="Select a package code..."
                @input="onChangeEditclient"
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Package type:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="packageTypes"
                v-model="editClient.package_type_id"
                option-value="id"
                option-text="name"
                placeholder="Select a package type..."
              ></model-list-select>
            </div>
          </div>

          <!--Form-------->
          <template slot="modal-footer" slot-scope="{}">
            <b-button
              size="sm"
              variant="success"
              @click="btnUpdatePackage($event)"
              >Update</b-button
            >
          </template>
        </b-modal>
        <!--modalUpdatePackage-------->

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
              roles.operator &&
              ticket_update_target_date.current_target_date != null
            "
          >
            <div class="col-lg-3">
              <p class="textLabel">Date Only:</p>
            </div>
            <div class="col-lg-9">
              <p-check
                class="textLabel p-switch p-fill"
                color="success"
                v-model="ticket_update_target_date.date_only"
              >
                <i
                  class="fas fa-check"
                  v-show="ticket_update_target_date.date_only"
                />
                <i
                  class="fas fa-times"
                  v-show="!ticket_update_target_date.date_only"
                />
              </p-check>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-if="
              roles.operator &&
              ticket_update_target_date.current_target_date != null
            "
          >
            <div class="col-lg-3">
              <p class="textLabel">Change Team Only:</p>
            </div>
            <div class="col-lg-9">
              <p-check
                class="textLabel p-switch p-fill"
                color="success"
                v-model="ticket_update_target_date.team_only"
              >
                <i
                  class="fas fa-check"
                  v-show="ticket_update_target_date.team_only"
                />
                <i
                  class="fas fa-times"
                  v-show="!ticket_update_target_date.team_only"
                />
              </p-check>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Account Name:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ ticket_update_target_date.name }}</p>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-if="!ticket_update_target_date.team_only"
          >
            <div class="col-lg-3">
              <p class="textLabel">Select date:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="ticket_update_target_date.target_date"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-if="!ticket_update_target_date.date_only"
          >
            <div class="col-lg-3">
              <p class="textLabel">Assigned Team:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                name="jo_cable_cat"
                :list="teams"
                v-model="ticket_update_target_date.team_id"
                option-value="id"
                option-text="name"
                placeholder="Select team"
              ></model-list-select>
            </div>
          </div>
        </b-modal>
        <!-- End modalTargetDate -->

        <!-- modalCalculateRebates ----------------------------------------------------------------------->
        <b-modal
          id="modalCalculateRebates"
          centered
          title="Calculate Rebates"
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
              <p class="textLabel">{{ editTicket.name }}</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">MRR</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                placeholder="MRR"
                v-model="rebate.mrr"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Days of the month</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                placeholder="MRR"
                v-model="rebate.days"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Total hrs of Downtime</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ rebate.totalHour }} hr/s.</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Fraction/ Rate</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ rebate.rate }}</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Downtime rebate</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ rebate.downtimeRate }}</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Total Payable</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ rebate.payable }}</p>
            </div>
          </div>

          <template v-slot:modal-footer>
            <b-button size="sm" variant="success" @click="calculateRebates"
              >Calculate</b-button
            >
          </template>
        </b-modal>
        <!-- End modalCalculateRebates -->

        <!-- modalAddRebates ----------------------------------------------------------------------->
        <b-modal
          id="modalAddRebates"
          centered
          title="Add Rebates"
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
              <p class="textLabel">{{ editTicket.name }}</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Total hrs of Downtime</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                placeholder="Hours"
                v-model="Addrebate.totalHour"
              />
            </div>
          </div>

          <template v-slot:modal-footer>
            <!-- <b-button size="sm" variant="success" @click="calculateHours">Calculate hours</b-button> -->
            <b-button size="sm" variant="success" @click="AddRebates_ok"
              >Add</b-button
            >
          </template>
        </b-modal>
        <!-- End modalAddRebates -->

        <!-- modalEmailTicket -->
        <b-modal
          id="modalEmailTicket"
          title="List of tickets that has no connection"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="xl"
        >
          <div id="printSec" style="width: 90%; color: black">
            <table
              style="
                width: 100%;
                border-collapse: collapse;
                border: 1px solid black;
                padding: 10px;
              "
            >
              <tr>
                <th
                  style="
                    border: 1px solid black;
                    padding: 10px;
                    text-align: center;
                  "
                >
                  #
                </th>
                <th
                  style="
                    border: 1px solid black;
                    padding: 10px;
                    text-align: center;
                  "
                >
                  Ticket No.
                </th>
                <th
                  style="
                    border: 1px solid black;
                    padding: 10px;
                    text-align: center;
                  "
                >
                  Con. Status
                </th>
                <th
                  style="
                    border: 1px solid black;
                    padding: 10px;
                    text-align: center;
                  "
                >
                  Status
                </th>
                <th
                  style="
                    border: 1px solid black;
                    padding: 10px;
                    text-align: center;
                  "
                >
                  Aging
                </th>
              </tr>
              <tr
                v-for="(data, index) in emailTicketItem"
                :key="data.mTicket_id"
              >
                <td style="border: 1px solid black; padding: 8px">
                  {{ index + 1 }}
                </td>
                <td style="border: 1px solid black; padding: 8px">
                  {{ data.mTicket_id }}
                </td>
                <td style="border: 1px solid black; padding: 8px">
                  <span v-if="data.connection_status == 'up'">UP</span>
                  <span v-else-if="data.connection_status == 'down'">DOWN</span>
                  <span v-else-if="data.connection_status == 'intermittent'"
                    >INTERMITTENT</span
                  >
                  <span v-else>{{ data.connection_status }}</span>
                </td>
                <td style="border: 1px solid black; padding: 8px">
                  {{ data.statname }}
                </td>
                <td style="border: 1px solid black; padding: 8px">
                  {{ data.aging }}
                </td>
              </tr>
            </table>
          </div>

          <template slot="modal-footer" slot-scope="{}">
            <b-button
              size="sm"
              variant="success"
              @click="EmailElement('printSec')"
              >Email</b-button
            >
          </template>
        </b-modal>

        <!-- modalColumnSettings -->
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
          <div v-on:click="changeColDisplay('colConStatus')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colConStatus"
            ></p-check
            >Connection Status
          </div>
          <br />
          <div v-on:click="changeColDisplay('colAction')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colAction"
            ></p-check
            >Action
          </div>
          <br />
          <div v-on:click="changeColDisplay('colRemarks')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colRemarks"
            ></p-check
            >Remarks
          </div>
          <br />
          <div v-on:click="changeColDisplay('colTechAsign')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colTechAsign"
            ></p-check
            >Consolidated Tech
          </div>
          <br />
          <div v-on:click="changeColDisplay('colCreatedBy')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colCreatedBy"
            ></p-check
            >Created By
          </div>

          <br />
          <div v-on:click="changeColDisplay('colCreated')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colCreated"
            ></p-check
            >Created
          </div>
          <br />
          <div v-on:click="changeColDisplay('colUpdatedAt')">
            <p-check
              class="checkboxStyle p-switch p-slim"
              color="success"
              v-model="colUpdatedAt"
            ></p-check
            >Updated At
          </div>
          <br />

          <div slot="modal-footer" slot-scope="{}"></div>
        </b-modal>

        <!--modal rebates -->
        <b-modal
          id="modalRebates"
          title="Add Client/s Rebates"
          :header-bg-variant="' elBG'"
          :header-text-variant="' elClr'"
          :body-bg-variant="' elBG'"
          :body-text-variant="' elClr'"
          :footer-bg-variant="' elBG'"
          :footer-text-variant="' elClr'"
          size="lg"
        >
          <div
            class="rowFields mx-auto row"
            style="width: 100%; display: flex; margin-top: 10px"
          >
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 60%">Ticket No. :</p>
            </div>
            <div style="width: 85%">
              <p class="textLabel" style="width: 60%; font-weight: bold">
                {{ editTicket.mTicket_id }}
              </p>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            style="width: 100%; display: flex; margin-top: 15px"
          >
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 60%">Clients:</p>
              <b-button
                variant="outline-dark"
                style="width: 35%; float: right; height: 35px"
                v-b-tooltip="'Clear Recipients'"
                @click="clearClients"
                >X</b-button
              >
            </div>
            <div style="width: 85%">
              <multiselect
                v-model="selectedClientRebates"
                :options="clients"
                label="name"
                track-by="id"
                multiple
                tag-placeholder="Add this account"
                open-direction="bottom"
                placeholder="Type to search or type account name"
                :hide-selected="true"
                :clear-on-select="false"
                :close-on-select="false"
                :taggable="true"
              ></multiselect>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            style="width: 100%; display: flex; margin-top: 10px"
          >
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 60%">Downtime Hour/s :</p>
            </div>
            <div style="width: 85%">
              <p class="textLabel" style="width: 60%; font-weight: bold">
                {{ editTicket.downtime_hours }}
              </p>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            style="width: 100%; display: flex; margin-top: 10px"
          >
            <div style="width: 15%; display: flex">
              <p class="textLabel" style="width: 60%">Date Effective :</p>
            </div>
            <div style="width: 85%">
              <date-picker
                v-model="date_effective"
                :config="DateTimeOptions"
                autocomplete="off"
                style="max-width: 100%; height: 30px"
                name="date_effective"
                v-validate="'required'"
              ></date-picker>
              <small
                class="text-danger pull-left"
                v-show="errors.has('date_effective')"
                >Please input date time.</small
              >
            </div>
          </div>
          <div slot="modal-footer" slot-scope="{}">
            <button class="btn btn-success btn-labeled" @click="saveRebates">
              Save
            </button>
          </div>
        </b-modal>

        <modal_ticket_filter></modal_ticket_filter>
        <modal_client_filter v-bind:data="filterData"></modal_client_filter>
        <!-- <modal_ticket_group v-bind:data="editTicketGroup"></modal_ticket_group> -->
      </div>
    </div>

    <modal_fsr v-bind:data="fsr_data"></modal_fsr>
  </div>
</template>

<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyInput from "pretty-checkbox-vue/input";
import PrettyRadio from "pretty-checkbox-vue/radio";
import Multiselect from "vue-multiselect";

import Swal2 from "sweetalert2";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import VueRangedatePicker from "vue-rangedate-picker";

import modal_ticket_filter from "../modal_vue/modal_ticket_filter.vue";
import modal_client_filter from "../modal_vue/modal_client_filter.vue";
import modal_ticket_group from "../modal_vue/modal_ticket_group.vue";

import UploadImages from "vue-upload-drop-images";
import modal_fsr from "../modal_vue/modal_fsr.vue";

export default {
  components: {
    //"json-excel": JsonExcel,
    multiselect: Multiselect,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,

    modal_ticket_filter: modal_ticket_filter,
    modal_client_filter: modal_client_filter,
    modal_ticket_group: modal_ticket_group,

    "p-check": PrettyCheck,
    "p-input": PrettyInput,
    "p-radio": PrettyRadio,
    "rangedate-picker": VueRangedatePicker,
    UploadImages,
    Swal2: Swal2,
    modal_fsr: modal_fsr,
  },
  data() {
    return {
      isShowing: false,
      slide: 0,
      sliding: null,
      show: false,
      tblisBusy: true,
      sortBy: "updated_at",
      sortDesc: true,
      statfield: [
        { key: "name", label: "Status", sortable: true },
        { key: "actions", label: "Actions" },
      ],
      compField: [
        { key: "name", label: "Complaint", sortable: true },
        { key: "actions", label: "Actions" },
      ],
      advField: [
        { key: "name", label: "Template", sortable: true },
        { key: "actions", label: "Actions" },
      ],
      Status_Ticket: [],
      areas: [],
      ticketStat: {
        name: "",
      },
      ticketComp: {
        name: "",
      },
      manageAdv: {
        name: "",
        content: "",
      },
      StattotalRows: 1,
      ComptotalRows: 1,
      AdvtotalRows: 1,
      fields: [
        { key: "client.name", label: "Account Name", sortable: true },
        { key: "compname", label: "Complaint", sortable: true },
        { key: "action", label: "Action", sortable: true },
        { key: "remarks", label: "Remarks", sortable: true },
        { key: "statname", label: "Status", sortable: true },
        {
          key: "technical_assigned",
          label: "Consolidated Tech",
          sortable: true,
        },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true },
      ],
      datenow: new Date(),
      dateTomorrow: new Date(),
      dateYesterday: new Date(),
      items: [],
      items_copy: [],
      tblFilter: null,
      tblFilter_copy: "",
      totalRows: 1,
      currentPage: 1,
      perPage: 50,
      pageOptions: [5, 10, 20, 50, 100],

      ticket: {
        ticketType: "1",
        client_id: "",
        region_id: "",
        user_id: "",
        complain: "",
        findings: "",
        action: "",
        remarks: "",
        Status_Ticket_id: "",
        area_id: "",
        connection_status: null,
        team_assigned: "",
        technical_assigned: "",
        target_date: null,
        selected_trouble: [],
        attachments: [],
        complaint_new: null,
        bwmon: "",
        cacti: "",
        device: "",
        loss: "",
        downtime: "",
        multipleClients: [],
        clientRegion: null,
      },
      editTicket: {
        client_id: "",
        complain: "",
        findings: "",
        action: "",
        remarks: "",
        team_assigned: "",
        Status_Ticket_id: "",
        technical_assigned: "",
        downtime_hours: "",
        client: {
          name: "",
          location: "",
          contact: "",
        },
        user: {
          name: "",
        },
        selected_trouble: [],
        attachments: [],
        complaint_new: "",
        bwmon: "",
        cacti: "",
        device: "",
        loss: "",
        downtime: "",
        rep_findings: "",
        rep_action: "",
        rebatable: "",
      },
      ticketGroup: {
        client_id: "",
        complain: "",
        findings: "",
        action: "",
        remarks: "",
        team_assigned: "",
        Status_Ticket_id: "",
        technical_assigned: "",
        downtime_hours: "",
        client: {
          name: "",
          location: "",
          contact: "",
        },
        user: {
          name: "",
        },
        selected_trouble: [],
        attachments: [],
        complaint_new: "",
        bwmon: "",
        cacti: "",
        device: "",
        loss: "",
        downtime: "",
        rep_findings: "",
        rep_action: "",
        rebatable: "",
      },
      status_id_update: "",
      comp_id_update: "",
      adv_id_update: "",
      clients: [],
      roles: [],
      colConStatus: false,
      colAction: false,
      colRemarks: false,
      colTechAsign: true,
      colCreatedBy: true,
      colCreated: false,
      colUpdatedAt: false,
      colDateFixed: false,
      clientSelected: {
        id: "",
        region_id: "",
        package_type: {
          name: "",
        },
        area_id: "",
      },
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false,
      },
      DateTimeOptions: {
        format: "YYYY-MM-DD HH:mm",
        useCurrent: false,
        showClear: true,
        showClose: true,
      },
      user: [],
      totalPendings: 0,
      totalUrgents: 0,
      totalTechVisit: 0,
      totalITND: 0,
      totalTransfer: 0,
      data: [],
      totalRebates: 0,
      packEdit: {
        package_id: "",
        package_type_id: "",
      },
      editClient: {
        id: "",
        package_id: "",
        package_type_id: "",
      },
      teams: [],
      packages: [],
      packageTypes: [],
      complaint_list: [],
      editChangeClient: false,
      ticket_update_target_date: {
        date_only: false,
        team_only: false,
        ticket_id: "",
        target_date: "",
        region_id: "",
        team_id: "",
        type: "ticket",
        name: "",
      },
      rebate: {
        from: "",
        to: "",
        mrr: 0,
        days: 30,
        rate: 0,
        downtimeRate: 0,
        payable: 0,
        totalHour: 0,
      },
      Addrebate: {
        from: "",
        to: "",
        totalHour: 0,
      },
      reloader_counter: 0,
      reloader_interval: [],
      searchby_list: [
        {
          name: "Ticket ID(last 4 digit)",
          id: "tickets.id",
        },

        {
          name: "Account Name",
          id: "clients.name",
        },
        {
          name: "Complaint",
          id: "complaint_lists.name",
        },
        {
          name: "Findings",
          id: "findings",
        },
        {
          name: "Consolidated Tech",
          id: "technical_assigned",
        },
        {
          name: "Others",
          id: "tickets.complain",
        },
      ],
      time: [
        {
          name: "24 Hours",
          id: "1",
        },

        {
          name: "48 Hours",
          id: "2",
        },
        {
          name: "All",
          id: "3",
        },
      ],
      searchby: "clients.name",
      trendTime: "",
      trendregion: "",
      remarksText: "",
      commentsText: [],
      remarks_id: "",
      selectedTemp: "",
      connection_status_list: [
        {
          id: "up",
          name: "Up",
        },
        {
          id: "down",
          name: "Down",
        },
        {
          id: "slow",
          name: "Slow",
        },
        {
          id: "intermittent",
          name: "Intermittent",
        },
      ],
      complaints_new: [],
      emailTicketItem: [],
      ticket_exist: false,
      ticket_num: 0,
      selectedContacts: [],
      contacts: [],
      recipients: "",
      maxText: 450,
      msg: "",
      filterData: "table",
      selected_trouble: [],
      attachments: [],
      showSending: false,
      showSent: false,
      showFailed: false,
      regions: [],
      ref: [11769, 11768, 11767, 11766, 11765, 1136, 108, 57],
      clientRef: [
        "Client",
        "CDO Client",
        "Digos Clients",
        "Tagum Clients",
        "Gensan Client",
        "Butuan Client",
        "Valencia Client",
        "SanFranz Client",
      ],
      clientRegions: [
        { id: "108", name: "Davao Clients" },
        { id: "11765", name: "CDO Clients" },
        { id: "57", name: "Digos Clients" },
        { id: "1136", name: "Tagum Clients" },
        { id: "11766", name: "Gensan Clients" },
        { id: "11767", name: "Butuan Clients" },
        { id: "11768", name: "Valencia Clients" },
        { id: "11769", name: "San Franz Clients" },
      ],
      progressBar: false,
      templates: [],
      preview: "",
      filterIn: null,
      cbFilter: null,
      daterange: "",
      showComment: false,
      retRemarks: "",
      iPrev: 0,
      existTickets: [],
      delDetails: [],
      selectedClientRebates: [],
      date_effective: "",
      fsr_data: {
        user: {
          email: "",
        },
      },
      addedClients: [],
      editChangeClient: false,
      clientField: [
        { key: "client.name", label: "Name", sortable: true },
        { key: "actions", label: "Actions" },
      ],
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.packageTypes = this.$global.getPackageTypes();
    this.dateTomorrow.setDate(this.dateTomorrow.getDate() + 1);
    this.dateTomorrow = this.formatDate(this.dateTomorrow);
    this.dateYesterday.setDate(this.dateYesterday.getDate() - 1);
    this.dateYesterday = this.formatDate(this.dateYesterday);
    this.datenow = this.formatDate(this.datenow);
    this.regions = this.$global.getRegion();

    this.roles = this.$global.getRoles();
    this.teams = this.$global.getTeam();
    this.Status_Ticket = this.$global.getTicketStatus();

    this.changeColDisplay("colCreated");

    this.loadClients();
    this.loadComplaints();
    this.loadTemplates();
  },
  mounted() {
    this.load();
    this.loadComplaints();
    this.loadTemplates();
    this.totalRows = this.items.length;
    this.StattotalRows = this.Status_Ticket.length;

    this.$root.$on("update_ticket_list", (item, filt) => {
      this.items = item.items;
      this.totalPendings = item.pendingCount;
      this.totalUrgents = item.urgentCount;
      this.totalTechVisit = item.techVisitCount;
      this.totalITND = item.itndCount;
      this.data = item.data;
      this.reloader_counter++;
      this.tblisBusy = false;
      this.totalRows = this.items.length;
      this.filterIn = "multi";
      this.cbFilter = filt;
    });
    this.$root.$on("add_data_to_recipient", (item) => {
      var items = item.items;
      items.forEach((item) => {
        if (item.contact != null) {
          var temp = item.contact.split(",");
          temp.forEach((contact) => {
            contact = contact.replace(/\/-/g, "");
            if (contact.length == 11) {
              var data = {
                contact: contact,
                name: item.name,
              };

              this.selectedContacts.push(data);
            }
          });
        }
      });
    });
    this.$root.$on("add_data_to_client_ticket", (item) => {
      var items = item.items;
      items.forEach((item) => {
        var data = {
          id: item.id,
          name: item.name,
        };

        this.ticket.multipleClients.push(data);
        console.log(this.ticket.multipleClients);
      });
    });
  },

  updated() {},
  methods: {
    load() {
      this.$nextTick(function () {
        setTimeout(function () {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarCalendar").style.backgroundColor = "";
          document.getElementById("navbarTicket").style.backgroundColor =
            "#2196f3";
          document.getElementById("navbarMap").style.backgroundColor = "";
          document.getElementById("navbarInstallation").style.backgroundColor =
            "";
          document.getElementById("navbarComponents").style.backgroundColor =
            "";
          document.getElementById("navbarAccounts").style.backgroundColor = "";
        }, 100);
      });

      this.$http.post("api/getComplaint").then((response) => {
        this.complaint_list = response.body;
      });

      this.$http.get("api/area").then((response) => {
        this.areas = response.body;
      });

      this.$http.get("api/getContacts").then((response) => {
        this.contacts = response.body;
        //   console.log(response.body);
      });
      this.$http.get("api/Package").then(function (response) {
        this.packages = response.body;
      });

      this.$http
        .get("api/Ticket/subIndex/" + this.user.region_id)
        .then(function (response) {
          this.items = response.body.items;
          this.items_copy = response.body.items;
          this.totalPendings = response.body.pendingCount;
          this.totalUrgents = response.body.urgentCount;
          this.totalTechVisit = response.body.techVisitCount;
          this.totalTransfer = response.body.transferCount;
          this.totalITND = response.body.itndCount;
          this.data = response.body.data;
          this.reloader_counter++;
          this.tblisBusy = false;
          this.totalRows = this.items.length;
        });
    },

    loadComplaints() {
      this.$http.get("api/ComplaintList").then((response) => {
        this.complaints_new = response.body;
        this.ComptotalRows = this.complaints_new.length;
      });
    },
    loadClients() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function (response) {
          this.clients = response.body;
        });
    },
    loadTemplates() {
      this.$http.get("api/Advisory").then((response) => {
        this.templates = response.body;
        this.AdvtotalRows = this.templates.length;
      });
    },
    getClientDesc(client) {
      return `${client.name} - ${client.region_name}`;
    },
    reload_data() {
      if (this.tblisBusy == false) {
        this.tblisBusy = true;
        this.reloader_counter = 0;
        this.$http.get("api/Package").then(function (response) {
          this.packages = response.body;
          this.reloader_counter++;
        });

        this.$http.get("api/PackageType").then(function (response) {
          this.$global.setPackageTypes(response.body);
          this.packageTypes = response.body;
          this.reloader_counter++;
        });

        this.$http.get("api/TicketStatus").then(function (response) {
          this.$global.setTicketStatus(response.body);
          this.Status_Ticket = response.body;
          this.reloader_counter++;
        });

        this.$http
          .get("api/Ticket/subIndex/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.totalPendings = response.body.pendingCount;
            this.totalUrgents = response.body.urgentCount;
            this.totalTechVisit = response.body.techVisitCount;
            this.totalITND = response.body.itndCount;
            this.totalTransfer = response.body.transferCount;
            this.data = response.body.data;
            this.reloader_counter++;
          });
        this.$http
          .get("api/getClients/" + this.user.region_id)
          .then(function (response) {
            this.clients = response.body;
            this.reloader_counter++;
          });

        this.reloader_interval = setInterval(this.reloader_breaker, 100);
      }
    },
    reloader_breaker() {
      if (this.reloader_counter == 5) {
        this.tblisBusy = false;
        clearInterval(this.reloader_interval);
      }
    },
    delStatus(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this Status",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .delete("api/TicketStatus/" + item.id)
            .then((response) => {
              this.$global.setTicketStatus(response.body);
              swal("Deleted!", "", "success").then((value) => {
                this.Status_Ticket = response.body;
                this.StattotalRows = this.Status_Ticket.length;
                this.tblisBusy = false;
              });
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              }).then((value) => {
                if (value) {
                  this.tblisBusy = false;
                }
              });
            });
        }
      });
    },
    tblRowClass(Status_Ticket, type) {
      if (!Status_Ticket) return;
      else if (this.roles.update_ticket) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    StattblRowClass(Status_Ticket, type) {
      if (!Status_Ticket) return;
      else if (this.roles.update_statTicket) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    ComptblRowClass(complaints_new, type) {
      if (!complaints_new) return;
      else if (this.roles.update_statTicket) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    // AdvtblRowclass(templates, type) {
    //   if (!templates) return;
    //   else if (this.roles.update_statTicket) {
    //     return "elClr cursorPointer";
    //   } else return "elClr";
    // },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    StatonFiltered(StatfilteredItems) {
      this.StattotalRows = StatfilteredItems.length;
      this.currentPage = 1;
    },
    ComponFiltered(CompfilteredItems) {
      this.ComptotalRows = CompfilteredItems.length;
      this.currentPage = 1;
    },
    AdvonFiltered(AdvfilteredItems) {
      this.AdvtotalRows = AdvfilteredItems.length;
      this.currentPage = 1;
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      this.preview = "";
      this.editTicket = item;
      console.log(this.editTicket);
      this.editChangeClient = false;

      this.commentsText = new Array(this.editTicket.remarks_log.length);

      let text = item.ticket_id;
      let result = text.includes("GRP");

      if (result == true) {
        console.log("ticket group");
        this.editTicket.ticketType = "3";
        console.log(item);
      } else {
        console.log("individual");
        this.editTicket.ticketType = "1";
        console.log(item);
      }
      this.$bvModal.show("modalEditTicket");
      //  for adding client rebates
      this.selectedClientRebates.splice(0);
      var client = {
        id: item.client_id,
        name: item.client.name,
        package_mrr: null,
      };

      this.selectedClientRebates.push(client);
    },
    addClientGroup() {
      this.addedClients.forEach((item) => {
        const data = {
          ticket_group_id: this.editTicket.id,
          client_id: item.id,
          client: item,
        };
        swal("Client Added", "success");

        this.editTicket.ticket_group_client.push(data);
        this.addedClients = [];
      });
    },
    removeClient(id) {
      for (
        var index = 0;
        index < this.editTicket.ticket_group_client.length;
        index++
      ) {
        if (this.editTicket.ticket_group_client[index].client_id == id) {
          this.editTicket.ticket_group_client.splice(index, 1);
        }
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.ticket.user_id = this.user.id;
      this.ticket.user_name = this.user.name;

      this.$validator.validateAll().then((result) => {
        if (result) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/Ticket", this.ticket)
            .then((response) => {
              this.ticket = {
                ticketType: "1",
                client_id: "",
                region_id: "",
                user_id: "",
                complain: "",
                findings: "",
                action: "",
                complaint_new: null,
                bwmon: "",
                device: "",
                loss: "",
                downtime: "",
                cacti: "",
                remarks: "",
                Status_Ticket_id: "",
                area_id: "",
                connection_status: null,
                team_assigned: "",
                report: "",
                technical_assigned: "",
                target_date: null,
                selected_trouble: [],
                attachments: [],
                multipleClients: [],
                clientRegion: null,
              };
              this.items = response.body.items;
              this.items_copy = response.body.items;
              this.totalRows = this.items.length;
              this.$bvModal.hide("modalAddTicket");
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
              this.totalPendings = response.body.pendingCount;
              this.totalUrgents = response.body.urgentCount;
              this.totalTechVisit = response.body.techVisitCount;
              this.totalITND = response.body.itndCount;
              this.totalTransfer = response.body.transferCount;
              this.data = response.body.data;
              this.clientSelected = {
                id: "",
                area_id: "",
                region_id: "",
                package_type: {
                  name: "",
                },
              };

              swal("Ticket", "Added successfully", "success");
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },

    btnDelete() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this Ticket permanently",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.items = [];
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          var data = {
            id: this.editTicket.id,
            region: this.user.region_id,
            filterIn: this.filterIn,
            cbFilter: this.cbFilter,
            searchby: this.searchby,
            tblFilter: this.tblFilter_copy,
            start: this.daterange.start,
            end: this.daterange.end,
            ticketType: this.editTicket.ticketType,
            user_id: this.user.id,
            user_name: this.user.name,
          };

          this.$http
            .post("api/Ticket/deleteTicket", data)
            .then((response) => {
              // console.log(response.body);
              this.$bvModal.hide("modalEditTicket");
              this.items_copy = response.body.items;
              this.totalPendings = response.body.pendingCount;
              this.totalUrgents = response.body.urgentCount;
              this.totalTechVisit = response.body.techVisitCount;
              this.totalITND = response.body.itndCount;
              this.totalTransfer = response.body.transferCount;
              this.data = response.body.data;
              swal("Deleted!", "", "success").then((value) => {
                this.items = response.body.items;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
                this.$root.$emit("pageLoaded");
              });
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
    },
    btnUpdate() {
      this.editTicket.updated_by = this.user.name;

      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this Ticket?",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            if (update) {
              this.$root.$emit("pageLoading");
              this.editTicket.region_id = this.user.region_id;
              this.editTicket.user_name = this.user.name;
              this.editTicket.attachments = this.ticket.attachments;
              this.editTicket.filterIn = this.filterIn;
              this.editTicket.cbFilter = this.cbFilter;
              this.editTicket.searchby = this.searchby;
              this.editTicket.tblFilter = this.tblFilter_copy;
              this.editTicket.start = this.daterange.start;
              this.editTicket.end = this.daterange.end;

              this.$http
                .put("api/Ticket/" + this.editTicket.id, this.editTicket)
                .then((response) => {
                  this.items = response.body.items;
                  this.items_copy = response.body.items;
                  // this.filterIn = null;
                  swal("Update!", "", "success");
                  this.$bvModal.hide("modalEditTicket");
                  this.totalPendings = response.body.pendingCount;
                  this.totalUrgents = response.body.urgentCount;
                  this.totalTechVisit = response.body.techVisitCount;
                  this.totalITND = response.body.itndCount;
                  this.totalTransfer = response.body.transferCount;
                  this.data = response.body.data;
                  this.$root.$emit("pageLoaded");
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
            }
          });
        }
      });
    },
    openModalUpdateTargetDate(item) {
      if (this.roles.update_ticket) {
        this.ticket_update_target_date.team_only = false;
        this.ticket_update_target_date.date_only = false;
        this.ticket_update_target_date.target_date = "";
        this.ticket_update_target_date.current_target_date = item.target_date;
        this.ticket_update_target_date.team_id = "";
        this.ticket_update_target_date.ticket_id = item.id;
        this.ticket_update_target_date.region_id = this.user.region_id;
        this.ticket_update_target_date.name = item.name;
        this.$bvModal.show("modal-update-target-date");
      }
    },
    updateDateClicked(bvModalEvt) {
      bvModalEvt.preventDefault();
      if (
        this.ticket_update_target_date.target_date != "" ||
        this.ticket_update_target_date.team_only == true
      ) {
        if (
          this.formatDate(this.ticket_update_target_date.target_date) >=
          this.datenow
        )
          if (
            this.ticket_update_target_date.team_id != "" ||
            this.ticket_update_target_date.date_only == true
          ) {
            this.updateTargetDate();
          } else swal("Ops.", "Please select team", "warning");
        else
          swal("Ops.", "Please select present or future dates only", "warning");
      } else swal("Ops.", "Please select date first", "warning");
    },
    updateTargetDate() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "info",
        buttons: ["No", "Yes"],
      }).then((yes) => {
        if (yes) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          this.ticket_update_target_date.user_id = this.user.id;
          this.ticket_update_target_date.user_name = this.user.name;
          this.ticket_update_target_date.filterIn = this.filterIn;
          this.ticket_update_target_date.cbFilter = this.cbFilter;
          this.ticket_update_target_date.searchby = this.searchby;
          this.ticket_update_target_date.tblFilter = this.tblFilter_copy;
          this.$http
            .post("api/Ticket/updateTargetDate", this.ticket_update_target_date)
            .then((response) => {
              this.$bvModal.hide("modal-update-target-date");
              this.items_copy = response.body.items;
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
              swal("Updated", "", "success").then((value) => {
                this.items = response.body.items;
                this.totalRows = this.items.length;
              });
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
    },

    OpenModalEmailTicket() {
      this.$http
        .post("api/Ticket/emailTicket")
        .then((response) => {
          console.log(response.body);
          this.emailTicketItem = response.body.items;

          this.$bvModal.show("modalEmailTicket");
        })
        .catch((response) => {
          console.log(response);
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
    },
    OpenModeleditStat(item, index, button) {
      this.status_id_update = item.id;
      this.$bvModal.show("editStatmodal");
    },

    OpenModeleditComp(item, index, button) {
      this.comp_id_update = item.id;
      this.$bvModal.show("editCompmodal");
    },

    OpenModeleditAdv(item, index, button) {
      this.loadTemplates();
      this.adv_id_update = item.id;
      this.manageAdv.name = item.name;
      this.manageAdv.content = item.content;
      this.$bvModal.show("editAdvmodal");
    },

    btnAddStat() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.ticketStat.user_id = this.user.id;
          this.ticketStat.user_name = this.user.name;
          this.$http
            .post("api/TicketStatus", this.ticketStat)
            .then((response) => {
              this.Status_Ticket = response.body;
              swal("Status", "Added successfully", "success");
              this.ticketStat.name = "";
              this.$global.setTicketStatus(response.body);
              this.StattotalRows = this.Status_Ticket.length;
              this.$bvModal.hide("modalAddStat");
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
      });
    },
    StatbtnUpdate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this status?",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            if (update) {
              this.tblisBusy = true;
              this.ticketStat.user_id = this.user.id;
              this.ticketStat.user_name = this.user.name;
              this.$http
                .put(
                  "api/TicketStatus/" + this.status_id_update,
                  this.ticketStat
                )
                .then((response) => {
                  this.Status_Ticket = response.body;
                  this.$global.setTicketStatus(response.body);
                  swal("Update!", "", "success");
                  this.$bvModal.hide("editStatmodal");
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
        }
      });
    },

    changeColDisplay(check) {
      this.fields = [
        {
          key: "mTicket_id",
          label: "Ticket No.",
          sortable: true,
          formatter: (value) => {
            return "<i><b>" + value + "</b></i>";
          },
        },
        {
          key: "statname",
          label: "Status",
          sortable: true,
          formatter: (value) => {
            if (value == "Close")
              return '<span class="btn btn-success disabled" style="width:100%;"> Fixed </span>';
            else if (value == "For Rebates")
              return '<span class="btn btn-success disabled" style="width:100%;"> For Rebates </span>';
            else if (value == "Pending")
              return '<span class="btn btn-warning disabled" style="width:100%;"> Pending </span>';
            else if (value == "For ITND")
              return '<span class="btn btn-info disabled" style="width:100%;"> For ITND </span>';
            else if (value == "Urgent")
              return '<span class="btn btn-danger disabled" style="width:100%;"> Urgent </span>';
            else if (value == "For Tech Visit")
              return '<span class="btn btn-primary disabled" style="width:100%;"> For tech visit </span>';
            else if (value == "Modem/Line Transfer")
              return '<span class="btn btn-dark disabled" style="width:100%;"> Modem/Line Transfer </span>';
            else return value;
          },
        },
        {
          key: "target_date",
          label: "Target Date",
          sortable: true,
        },
        {
          key: "aging",
          label: "Aging",
          sortable: true,
        },
        {
          key: "diffHuman",
          label: "Downtime",
          sortable: true,
        },
        { key: "client.name", label: "Account Name", sortable: true },
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
        },
      ];

      if ("forExport" == check) {
        var temp = {
          key: "compname",
          label: "Complaint",
        };
        this.fields.push(temp);

        var temp4 = {
          key: "technical_assigned",
          label: "Consolidated Tech",
          sortable: true,
        };
        this.fields.push(temp4);

        var findings = {
          key: "rep_findings",
          label: "Findings",
          sortable: true,
        };
        this.fields.push(findings);

        var action = {
          key: "rep_action",
          label: "Action",
          sortable: true,
        };
        this.fields.push(action);

        var date_time_fixed = {
          key: "date_time_fixed",
          label: "Date Time Fixed",
          sortable: true,
        };
        this.fields.push(date_time_fixed);

        var downtime_hours = {
          key: "downtime_hours",
          label: "Downtime Hours",
          sortable: true,
        };
        this.fields.push(downtime_hours);

        var created_at = {
          key: "created_at",
          sortable: true,
        };
        this.fields.push(created_at);

        var updated_at = {
          key: "updated_at",
          sortable: true,
        };
        this.fields.push(updated_at);
      } else {
        if ("colConStatus" == check) {
          this.colConStatus = !this.colConStatus;
        }
        if (this.colConStatus)
          this.fields.splice(1, 0, {
            key: "connection_status",
            label: "Con. Status",
            sortable: true,
            formatter: (value) => {
              if (value == "up")
                return '<span class="btn btn-success disabled" style="width:100%;"> UP </span>';
              else if (value == "down")
                return '<span class="btn btn-danger disabled" style="width:100%;"> DOWN </span>';
              else if (value == "intermittent")
                return '<span class="btn btn-warning disabled" style="width:100%;"> INTERMITTENT </span>';
              else return value;
            },
          });

        var temp = {
          key: "compname",
          label: "Complaint",
        };
        this.fields.push(temp);

        if ("colAction" == check) {
          this.colAction = !this.colAction;
        }
        if (this.colAction) {
          var temp = {
            key: "action",
            label: "Action",
            formatter: (value) => {
              var temp = "";
              if (value != null) {
                if (value.length > 50) temp = "...";
                return value.slice(0, 50) + temp;
              } else return "";
            },
          };
          this.fields.push(temp);
        }

        if ("colRemarks" == check) {
          this.colRemarks = !this.colRemarks;
        }
        if (this.colRemarks) {
          var temp = {
            key: "remarks",
            label: "Remarks",
            formatter: (value) => {
              var temp = "";
              if (value != null) {
                if (value.length > 50) temp = "...";
                return value.slice(0, 50) + temp;
              } else return "";
            },
          };
          this.fields.push(temp);
        }

        if ("colTechAsign" == check) {
          this.colTechAsign = !this.colTechAsign;
        }
        if (this.colTechAsign) {
          var temp = {
            key: "technical_assigned",
            label: "Consolidated Tech",
            sortable: true,
          };
          this.fields.push(temp);
        }

        if ("colCreatedBy" == check) {
          this.colCreatedBy = !this.colCreatedBy;
        }
        if (this.colCreatedBy) {
          var temp = {
            key: "user.name",
            label: "Created By",
            sortable: true,
          };
          this.fields.push(temp);
        }

        if ("colCreated" == check) {
          this.colCreated = !this.colCreated;
        }

        if (this.colCreated) {
          var temp = {
            key: "created_at",
            label: "Created At",
            sortable: true,
          };
          this.fields.push(temp);
        }

        if ("colUpdatedAt" == check) {
          this.colUpdatedAt = !this.colUpdatedAt;
        }
        if (this.colUpdatedAt) {
          var temp = {
            key: "updated_at",
            label: "Updated At",
            sortable: true,
          };
          this.fields.push(temp);
        }
      }

      if (this.user.id == 1) {
        var temp = {
          key: "id",
          label: "ID",
          sortable: true,
        };
        this.fields.push(temp);
      }
    },
    onChangeSelectClientEdit() {
      if (this.clientSelected.id != null) {
        this.$http
          .get("api/checkTicket/" + this.clientSelected.id)
          .then((response) => {
            console.log(response.body);
            var except = [108, 11767, 11765, 57, 11766, 11769, 1136, 11768];
            if (response.body.ticket == null) {
              this.ticket_exist = false;
              this.editTicket.area_id = response.body.area;
              console.log("ok");
            } else {
              if (except.includes(this.clientSelected.id)) {
                this.ticket_exist = false;
                console.log("ddddd");
              } else {
                this.ticket_exist = true;
                this.ticket_num =
                  response.body.ticket.ticket_id + "" + response.body.ticket.id;
                console.log("aaaaa");
                swal(
                  "Warning!",
                  "You have entered client with pending ticket, please check " +
                    this.ticket_num,
                  "warning"
                );
              }
              this.editTicket.area_id = response.body.area;
            }
          });
      }

      if (this.clientSelected.package_type_name == null) {
        swal({
          title: "Warning",
          text: "This account has no package type, Do you want to add package type to this account?",
          icon: "warning",
          buttons: ["No", "Yes"],
        }).then((value) => {
          if (value) {
            this.editClient.id = this.clientSelected.id;
            this.$bvModal.show("modalUpdatePackage");
            this.clientSelected = {
              id: "",
              region_id: "",
              package_type: {
                name: "",
              },
            };
          } else {
            this.clientSelected = {
              id: "",
              region_id: "",
              package_type: {
                name: "",
              },
            };
          }
        });
      } else {
        // console.log(this.clientSelected);
        // const dt1 = new Date();
        this.editTicket.client_id = this.clientSelected.id;
        this.editTicket.region_id = this.clientSelected.region_id;
        // this.editTicket.ticket_id =
        //   this.clientSelected.package_type_name +
        //   dt1.getFullYear().toString() +
        //   ("0" + (dt1.getMonth() + 1)).slice(-2) +
        //   ("0" + dt1.getDate()).slice(-2);
      }
    },
    onChangeSelectClient() {
      if (this.clientSelected.id != null) {
        this.$http
          .get("api/checkTicket/" + this.clientSelected.id)
          .then((response) => {
            console.log(response.body);

            var except = [108, 11767, 11765, 57, 11766, 11769, 1136, 11768];
            if (response.body.ticket == null) {
              this.ticket_exist = false;
              this.ticket.area_id = response.body.area;
              console.log("ok");
            } else {
              if (except.includes(this.clientSelected.id)) {
                this.ticket_exist = false;
                console.log("ddddd");
              } else {
                this.ticket_exist = true;
                this.ticket_num =
                  response.body.ticket.ticket_id + "" + response.body.ticket.id;
                console.log("aaaaa");
                swal(
                  "Warning!",
                  "You have entered client with pending ticket, please check " +
                    this.ticket_num,
                  "warning"
                );
              }
              this.ticket.area_id = response.body.area;
            }
          });
      }

      if (this.clientSelected.package_type_name == null) {
        swal({
          title: "Warning",
          text: "This account has no package type, Do you want to add package type to this account?",
          icon: "warning",
          buttons: ["No", "Yes"],
        }).then((value) => {
          if (value) {
            this.editClient.id = this.clientSelected.id;
            this.$bvModal.show("modalUpdatePackage");
            this.clientSelected = {
              id: "",
              region_id: "",
              package_type: {
                name: "",
              },
            };
          } else {
            this.clientSelected = {
              id: "",
              region_id: "",
              package_type: {
                name: "",
              },
            };
          }
        });
      } else {
        const dt1 = new Date();
        this.ticket.client_id = this.clientSelected.id;
        this.ticket.region_id = this.clientSelected.region_id;
        this.ticket.ticket_id =
          this.clientSelected.package_type_name +
          dt1.getFullYear().toString() +
          ("0" + (dt1.getMonth() + 1)).slice(-2) +
          ("0" + dt1.getDate()).slice(-2);
      }
    },
    onDateSelected(daterange) {
      this.filterIn = null;
      this.daterange = daterange;
      this.$root.$emit("pageLoading");
      daterange.region_id = this.user.region_id;
      // this.tblisBusy = true;
      this.$http.post("api/Ticket/filterByDate", daterange).then((response) => {
        this.filterIn = "date";

        this.items = response.body.items;
        this.totalRows = this.items.length;
        // this.tblisBusy = false;
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
    formatDateDefualt(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("/");
    },
    filterChange(txt) {
      this.filterIn = null;
      this.$root.$emit("pageLoading");
      this.$http
        .get("api/Ticket/search_data/Status_Ticket_id/" + txt)
        .then((response) => {
          this.filterIn = "badge";
          this.tblFilter_copy = txt;
          this.items = response.body.items;
          this.totalRows = this.items.length;
          this.$root.$emit("pageLoaded");
        });
    },
    filterClear() {
      this.tblFilter = "";
      this.tblFilter_copy = "";
      this.items = this.items_copy;
      this.totalRows = this.items.length;
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
    fnExcelReport(tbl) {
      this.changeColDisplay("forExport");
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
            this.changeColDisplay("");
            this.perPage = 50;
            return sa;
          }.bind(this),
          1000
        );
      });

      // this.$nextTick(function() {
      //   setTimeout(this.changeColDisplay(""), 3000);
      // });
    },
    onChangeEditclient() {
      this.editClient.package_id = this.packEdit.id;
      this.editClient.package_type_id = this.packEdit.package_type.id;
    },
    btnUpdatePackage(element) {
      if (this.editClient.package_type_id != "") {
        element.target.disabled = true;
        this.$http
          .post("api/updatePackage", this.editClient)
          .then((response) => {
            swal("Updated!", "Please re select account", "success");
            element.target.disabled = false;
            this.clients = response.body;

            this.$bvModal.hide("modalUpdatePackage");
          });
      } else {
        swal("", "Please select a package first", "info");
      }
    },
    autocomplete(inp, arr, dat) {
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      var thisisthis = this;
      inp.addEventListener("input", function (e) {
        var a,
          b,
          i,
          val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
          return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.innerHTML =
              "<strong>" + arr[i].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            b.addEventListener("click", function (e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              if (dat == "createticket")
                thisisthis.ticket.complain =
                  this.getElementsByTagName("input")[0].value;
              if (dat == "editticket")
                thisisthis.editTicket.complain =
                  this.getElementsByTagName("input")[0].value;
              closeAllLists();
            });
            a.appendChild(b);
          }
        }
      });

      inp.addEventListener("keydown", function (e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          currentFocus++;

          addActive(x);
        } else if (e.keyCode == 38) {
          currentFocus--;

          addActive(x);
        } else if (e.keyCode == 13) {
          /*If the ENTER key is pressed, prevent the form from being submitted,*/
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }
        }
      });
      function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = x.length - 1;
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
      }
      function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
          x[i].classList.remove("autocomplete-active");
        }
      }
      function closeAllLists(elmnt) {
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
          if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }

      document.addEventListener("click", function (e) {
        closeAllLists(e.target);
      });
    },
    btnRebates() {
      this.rebate.mrr = 0;
      this.rebate.days = 30;
      this.rebate.downtimeRate = 0;
      this.rebate.payable = 0;
      this.rebate.totalHour = this.editTicket.downtime_hours;
      var rate = 0;
      var totalHr = this.editTicket.downtime_hours;
      if (totalHr > 24) {
        while (totalHr > 24) {
          rate++;
          totalHr -= 24;
        }
      }
      if (totalHr >= 15) {
        rate++;
        totalHr = 0;
      }
      if (totalHr >= 12) {
        rate += 0.8;
        totalHr = 0;
      }
      if (totalHr >= 9) {
        rate += 0.6;
        totalHr = 0;
      }
      if (totalHr >= 6) {
        rate += 0.4;
        totalHr = 0;
      }
      if (totalHr >= 3) {
        rate += 0.2;
        totalHr = 0;
      }
      if (totalHr >= 0.5) {
        rate += 0.1;
        totalHr = 0;
      }
      this.rebate.rate = rate;
      this.$bvModal.show("modalCalculateRebates");
    },
    calculateRebates() {
      this.rebate.downtimeRate =
        (this.rebate.mrr / this.rebate.days) * this.rebate.rate;
      this.rebate.payable = this.rebate.mrr - this.rebate.downtimeRate;
      //  this.$http.post("api/CalculateRebates", this.rebate).then(response => {
      //     //console.log(response.body);
      //     this.rebate.totalHour = response.body.totalHour;
      //     this.rebate.rate = response.body.rate;
      //     this.rebate.downtimeRate = response.body.downtimeRate;
      //     this.rebate.payable = response.body.payable;
      //   });
    },
    countHours(date1, date2) {
      var dt1 = new Date(date1);
      var dt2 = new Date(date2);
      var hours = Math.abs(dt1 - dt2) / 36e5;
      return hours.toFixed(1);
    },
    btnAddRebates() {
      this.Addrebate.id = this.editTicket.id;
      this.Addrebate.totalHour = 0;
      // this.Addrebate.from = this.editTicket.created_at;
      // this.Addrebate.to = this.editTicket.updated_at;
      this.$bvModal.show("modalAddRebates");
      // this.calculateHours();
      // console.log(this.Addrebate);
    },
    calculateHours() {
      this.Addrebate.totalHour = this.countHours(
        this.Addrebate.from,
        this.Addrebate.to
      );
    },
    AddRebates_ok() {
      this.tblisBusy = true;
      this.Addrebate.region_id = this.user.region_id;
      this.$http.post("api/updateRebates", this.Addrebate).then((response) => {
        this.items = response.body.items;
        this.items_copy = response.body.items;
        this.totalPendings = response.body.pendingCount;
        this.totalUrgents = response.body.urgentCount;
        this.totalTechVisit = response.body.techVisitCount;
        this.totalITND = response.body.itndCount;
        this.totalTransfer = response.body.transferCount;
        this.data = response.body.data;
        this.$bvModal.hide("modalAddRebates");
        this.$bvModal.hide("modalEditTicket");
        swal("", "Rebate added", "success");
        this.tblisBusy = false;
      });
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
    search_data() {
      this.filterIn = null;
      // this.tblisBusy = true;
      this.$http
        .get(
          "api/Ticket/search_data/" + this.searchby + "/" + this.tblFilter_copy
        )
        .then((response) => {
          this.filterIn = "search";
          this.items = response.body.items;
          this.totalRows = this.items.length;
          // this.tblisBusy = false;
        });
    },
    addRemarks_clicked() {
      if (this.remarksText != "") {
        var data = {
          ticket_id: this.editTicket.id,
          user_id: this.user.id,
          remarks: this.remarksText,
          form_type: "ticket",
        };

        this.$http.post("api/TicketRemarksLog", data).then((response) => {
          this.remarksText = "";
          this.editTicket.remarks_log = response.body;
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the remarks text area");
      }
    },
    addComments_clicked(id, i, stor) {
      // console.log(id);
      if (this.commentsText[i] != "") {
        var data = {
          remarks_id: id,
          user_id: this.user.id,
          user: this.user,
          comments: this.commentsText[i],
        };
        stor.push(data);
        // console.log(data);
        this.$http.post("api/TicketCommentsLog", data).then((response) => {
          this.commentsText[i] = "";
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the comments text area");
      }
    },
    updateremarksss() {
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/TicketRemarksLog/moveTicketRemarks")
        .then((response) => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
        });
    },
    datetimeChange() {
      var hours =
        Math.abs(
          new Date(this.editTicket.created_at) -
            new Date(this.editTicket.date_time_fixed)
        ) / 36e5;
      this.editTicket.downtime_hours = hours;
    },
    Add_to_SOA() {
      swal({
        title: "Are you sure?",
        text: "Do you want to Add rebated to SOA?",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.$root.$emit("pageLoading");
          this.editTicket.region_id = this.user.region_id;
          this.editTicket.user_name = this.user.name;
          this.editTicket.to_soa = 1;
          this.$http
            .put("api/Ticket/" + this.editTicket.id, this.editTicket)
            .then((response) => {
              this.items = response.body.items;
              this.items_copy = response.body.items;
              swal("Update!", "", "success");
              this.$bvModal.hide("modalEditTicket");
              this.$root.$emit("pageLoaded");
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
        }
      });
    },
    EmailElement(id) {
      //sad
      var bodymsg = document.getElementById(id).innerHTML;
      this.$bvModal.hide("modalEmailTicket");

      Swal2.fire({
        title: "Enter passcode to send",
        input: "password",
        showCancelButton: true,
        confirmButtonText: "Ok",
      }).then((value) => {
        console.log(value);
        if (value.value == "send") {
          var data = {
            subject: "(Beta)List of tickets that has no connection",
            msg: bodymsg,
            user_email: this.user.name,
            user_name: this.user.email,
            sendTo: [
              {
                email: "gquisido@dctechmicro.com",
                name: "Glenn T. Quisido",
              },
            ],
            CCTO: [
              {
                email: this.user.email,
                name: this.user.name,
              },
              {
                email: "egdoromal@dctechmicro.com",
                name: "Elric Guy Doromal",
              },
              {
                email: "earawiran@dctechmicro.com",
                name: "Edward Ellie V. Arawiran",
              },
            ],
          };
          console.log(data);
          this.$http
            .post("api/Billing/emailSOA", data)
            .then((response) => {
              if (response.body == "ok") swal("Email Sent!");
              else swal("Send Failed");
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error + " " + response.body.message,
                icon: "error",
                dangerMode: true,
              });
              this.tblisBusy = false;
            });
        } else swal("Wrong password");
      });
    },
    send() {
      this.showSent = false;
      this.showFailed = false;
      this.showSending = true;

      this.$bvModal.hide("modalSendText");
      var data = {
        number: this.selectedContacts,
        msg: this.msg,
      };
      this.$http
        .post("api/Ticket/sendText", data)
        .then((response) => {
          console.log(response.body);
          if (response.body.message == "sent successfully") {
            swal("Message Sent Successfully!");
            this.showSending = false;
            this.showSent = true;
          } else {
            this.showFailed = true;
            swal("Sending Failed!");
          }
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          });
          this.showFailed = true;
        });
    },
    addTag(newTag) {
      const tag = {
        name: newTag,
        contact: newTag,
      };
      this.contacts.push(tag);
      this.selectedContacts.push(tag);
    },
    handleImages(e) {
      this.ticket.attachments = [];
      e.forEach((item) => {
        var fileReader = new FileReader();
        fileReader.readAsDataURL(item);

        fileReader.onload = (item) => {
          this.ticket.attachments.push(item.target.result);
          console.log(this.ticket.attachments);
        };
      });
    },
    generateTrend() {
      console.log(this.trendTime);
      var trend = {
        trendTime: this.trendTime,
        trendregion: this.trendregion,
      };
      this.$http.post("api/getTrend", trend).then((response) => {
        console.log(response.body);
        this.data = response.body.data;
      });
    },
    clearTrend() {
      this.trendregion = "";
      this.trendTime = "";

      this.$http
        .get("api/Ticket/subIndex/" + this.user.region_id)
        .then(function (response) {
          console.log(response.body.data);
          this.data = response.body.data;
        });
    },
    btnAddComp() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.ticketComp.user_id = this.user.id;
          this.ticketComp.user_name = this.user.name;
          this.$http
            .post("api/ComplaintList", this.ticketComp)
            .then((response) => {
              this.complaints_new = response.body;
              swal("Complaint", "Added successfully", "success");
              this.ticketComp.name = "";

              this.ComptotalRows = this.complaints_new.length;

              this.$bvModal.hide("modalAddComp");
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
      });
    },
    CompbtnUpdate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this complaint?",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            if (update) {
              this.tblisBusy = true;
              this.ticketComp.user_id = this.user.id;
              this.ticketComp.user_name = this.user.name;
              this.$http
                .put(
                  "api/ComplaintList/" + this.comp_id_update,
                  this.ticketComp
                )
                .then((response) => {
                  this.complaints_new = response.body;

                  swal("Update!", "", "success");
                  this.$bvModal.hide("editCompmodal");
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
        }
      });
    },
    delComp(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this complaint",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .delete("api/ComplaintList/" + item.id)
            .then((response) => {
              this.complaints_new = response.body;
              swal("Deleted!", "", "success").then((value) => {
                this.complaints_new = response.body;
                this.ComptotalRows = this.complaints_new.length;
                this.tblisBusy = false;
              });
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              }).then((value) => {
                if (value) {
                  this.tblisBusy = false;
                }
              });
            });
        }
      });
    },
    btnAddAdv() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.manageAdv.user_id = this.user.id;
          this.manageAdv.user_name = this.user.name;
          this.$http
            .post("api/Advisory", this.manageAdv)
            .then((response) => {
              this.templates = response.body;

              swal("Template", "Added successfully", "success");

              this.AdvtotalRows = this.templates.length;
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
        this.manageAdv.name = "";
        this.manageAdv.content = "";
      });
    },
    AdvbtnUpdate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this template?",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true,
          }).then((update) => {
            if (update) {
              this.tblisBusy = true;
              this.manageAdv.user_id = this.user.id;
              this.manageAdv.user_name = this.user.name;
              this.$http
                .put("api/Advisory/" + this.adv_id_update, this.manageAdv)
                .then((response) => {
                  this.templates = response.body;
                  this.manageAdv.name = "";
                  this.manageAdv.content = "";

                  swal("Update!", "", "success");
                  this.$bvModal.hide("editAdvmodal");

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
        }
      });
    },
    delAdv(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this template",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .delete("api/Advisory/" + item.id)
            .then((response) => {
              this.templates = response.body;
              swal("Deleted!", "", "success").then((value) => {
                this.templates = response.body;
                this.AdvtotalRows = this.templates.length;
                this.tblisBusy = false;
              });
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              }).then((value) => {
                if (value) {
                  this.tblisBusy = false;
                }
              });
            });
        }
      });
    },
    selectTemp() {
      if (this.selectedTemp != null) {
        var temp = {
          template: this.selectedTemp,
        };

        console.log(temp);
        this.$http.post("api/getTemplate", temp).then((response) => {
          console.log(response.body);
          if (response.body.length > 1) {
            this.msg = response.body;
          }
          // this.msg = response.body;
        });
      }
    },
    clearTemp() {
      this.selectedTemp = "";
      this.msg = "";
    },
    previewImg(item) {
      console.log(item);
      this.preview = item;
    },
    resetRecipients() {
      this.selectedContacts = [];
    },
    resetField() {
      this.msg = "";
    },
    reopen() {
      var data = {
        ticket_id: this.editTicket.id,
        user_id: this.user.id,
        remarks:
          "RE-OPENED TICKET DETAILS" +
          "\n" +
          "FINDINGS:" +
          this.editTicket.rep_findings +
          "\n" +
          "ACTIONS:" +
          this.editTicket.rep_action +
          "\n" +
          "CONSOLIDATED TECH:" +
          this.editTicket.technical_assigned +
          "\n" +
          "DATE FIXED:" +
          this.editTicket.date_time_fixed +
          "\n",
      };
      this.$http.post("api/TicketRemarksLog", data).then((response) => {
        this.remarksText = "";
        this.editTicket.rep_findings = " ";
        this.editTicket.rep_action = " ";
        this.editTicket.technical_assigned = " ";
        this.editTicket.date_time_fixed = " ";
        this.editTicket.downtime_hours = " ";
        this.editTicket.remarks_log = response.body;
        this.btnUpdate();
        console.log(response.body);
      });
    },
    checkClients() {
      console.log(this.ticket.multipleClients);
      this.ticket.multipleClients.forEach((item) => {
        this.$http.get("api/checkTicket/" + item.id).then((response) => {
          console.log(response.body);

          var except = [108, 11767, 11765, 57, 11766, 11769, 1136, 11768];
          if (response.body.ticket == null) {
            this.ticket_exist = false;
            console.log("ok");
          } else {
            if (except.includes(item.id)) {
              this.ticket_exist = false;
            } else {
              this.ticket_exist = true;
              this.existTickets =
                response.body.ticket.ticket_id + "" + response.body.ticket.id;

              swal(
                "Warning!",
                "You have entered client with pending ticket, please check " +
                  this.existTickets,
                "warning"
              );
            }
          }
        });
        const dt1 = new Date();
        this.ticket.ticket_id =
          item.package_type_name +
          dt1.getFullYear().toString() +
          ("0" + (dt1.getMonth() + 1)).slice(-2) +
          ("0" + dt1.getDate()).slice(-2);
      });
    },
    addClient(newTag) {
      const tag = {
        name: newTag,
        id: this.ticket.clientRegion,
        package_type_name: "RES",
      };
      this.clients.push(tag);
      this.ticket.multipleClients.push(tag);
      this.ticket.complain = "";
    },
    duplicateTicket() {
      console.log(this.duplicate);
      console.log(this.editTicket);
      this.$bvModal.show("modalAddTicket");
      this.$bvModal.hide("modalEditTicket");
      //  duplicated data
      this.ticket.complaint_new = this.editTicket.complaint_new;
      this.ticket.connection_status = this.editTicket.connection_status;
      this.ticket.Status_Ticket_id = this.editTicket.Status_Ticket_id;
      this.ticket.cacti = this.editTicket.cacti;
      this.ticket.selected_trouble = this.editTicket.selected_trouble;
      this.ticket.bwmon = this.editTicket.bwmon;
      this.ticket.cacti = this.editTicket.cacti;
      this.ticket.device = this.editTicket.device;
    },
    clearClients() {
      this.selected = [];
    },
    saveRebates() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          var data = {
            user_id: this.user.id,
            user_name: this.user.name,
            ticket_no: this.editTicket.id,
            clients: this.selectedClientRebates,
            downtime: this.editTicket.downtime_hours,
            date_effective: this.date_effective,
          };
          this.$http
            .post("api/Rebates", data)
            .then((response) => {
              console.log(response.body);
              swal("Rebates", "Added successfully", "success");
              this.$bvModal.hide("modalRebates");
              this.date_effective = "";
            })

            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
      });
    },
    btnFSR() {
      this.fsr_data = {
        id: this.editTicket.id,
        name: this.editTicket.client.name,
        contact_person: this.editTicket.client.contact_person,
        location: this.editTicket.client.location,
        contact: this.editTicket.client.contact,
        complaint: this.editTicket.compname,
      };
      this.$bvModal.show("modalFSRPrintPreview");
    },
  },
};
</script>
<style scoped>
.top-records {
  width: 100%;
  display: flex;
  padding-top: 7px;
  margin-top: -25px;
}
.top-records > div {
  flex-grow: 1;
}
.trend-bcard > .card-body {
  padding: 7px;
}
.trouble-bcard > .card-body {
  padding: 10px;
  padding-left: 13px;
  max-width: 100%;
  background: #fff;
  border: none;
}
.editTicket-bcard > .card-body {
  padding: 10px;
  padding-left: 13px;
  max-width: 100%;
  border: 1px solid #ced4da;
}

.statusBtn {
  flex: 1;
  padding-bottom: 4px;
  padding-top: 4px;
  font-weight: bold;
  color: white;
}
.input-date {
  display: block;
  border: 1px solid rgb(1, 235, 71) !important;
  padding: 5px;
  font-size: 15px !important;
  width: 100% !important;
  cursor: pointer;
}
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
.width80 {
  width: 80%;
}
.width90 {
  width: 90%;
}
.width100 {
  width: 100%;
}
.textbtnedit {
  position: absolute;
  left: 95%;
  color: cornflowerblue;
  cursor: pointer;
}
.textbtnedit:hover {
  text-decoration: underline;
}
.pre-formatted {
  white-space: pre-wrap;
}

.clientRow {
  width: 100%;
  height: 30px;
  padding: 0;
  margin-top: 0;
  margin-left: 10px;
  margin-bottom: 10px;
  font-size: 11px;
}

.clientCol {
  border: 1px solid #dfdfe0;
  padding-bottom: 0;
  min-width: 20%px;
  max-width: 20%;
  margin-left: 0;
  height: 29px;
  border-radius: 5px 0 0 5px;
  /* background: #f0f0f0; */
  background: rgba(0, 167, 0, 0.193);
}
.findingsCol {
  border: 1px solid #dfdfe0;
  padding-bottom: 0;
  min-width: 20%px;
  max-width: 20%;
  margin-left: 0;
  height: 39px;
  border-radius: 5px 0 0 5px;
  /* background: #f0f0f0; */
  background: rgba(0, 167, 0, 0.193);
}
.findingsRow {
  width: 100%;
  height: 30px;
  padding: 0;
  margin-top: 0;
  margin-left: 10px;
  margin-bottom: 10px;
  font-size: 11px;
}
.reportRow {
  width: 100%;
  height: 10%;
  padding: 0;
  margin-top: 0;
  margin-left: 10px;
  margin-bottom: -10px;
  font-size: 11px;
}

.reportCol {
  border: 1px solid #dfdfe0;
  padding-bottom: 5px;
  min-width: 20%px;
  max-width: 20%;
  margin-left: 0;
  height: 29px;
  border-radius: 5px 0 0 5px;
  /* background: #f0f0f0; */
  background: rgba(0, 167, 0, 0.193);
}
.tsCheckboxes {
  font-size: 11px;
  margin-top: 7px;
  margin-right: 14px;
}
.remarks {
  width: 100%;
  height: 100%;
  float: left;
  text-align: left;
  border: none;
  background: none;
  overflow-y: scroll;
  /* overflow-y: hidden !important; */
}
.remarks::-webkit-scrollbar {
  width: 8px;
  background-color: #f5f5f5;
}

.remarks::-webkit-scrollbar-thumb {
  border-radius: 10px;
  /* -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1); */
  background-color: #c8e9ca;
}
.ticketInputLine {
  border: none;
  border-bottom: 1px solid black;
  margin-left: 8px;
  width: 20%;
  box-shadow: none;
  border-radius: 0 0 0 0;
}
.ticketInputLine2 {
  border: none;
  border-bottom: 1px solid black;
  float: right;
  text-align: left;
  width: 80%;
  box-shadow: none;
  background: none;
  border-radius: 0 0 0 0;
  overflow-y: hidden !important;
}
.b-card-ticket {
  max-height: 100%;
  border: 1px solid #ced4da;
}
.usageLine {
  border: none;
  border-bottom: 1px solid black;
  margin-left: 0;
  width: 20%;
  box-shadow: none;
  border-radius: 0 0 0 0;
}

.statusBadge {
  display: flex;
  width: 25%;
  float: right;
  margin-top: -20px;
}
.ticketOption {
  display: flex;
  width: 21%;
  float: left;
  margin-top: -10px;
  font-size: 12px;
}

.multipleClient {
  width: 100%;
  display: flex;
  margin-top: 50px;
  margin-bottom: 10px;
}

.multipleClient2 {
  display: flex;
  margin-left: 10px;
  border: 1px solid #dfdfe0;
  padding-bottom: 0;
  min-width: 20%;
  max-width: 20%;
  height: 29px;
  border-radius: 5px 0 0 5px;
  background: rgba(0, 167, 0, 0.193);
}

.troubleCheckbox {
  display: flex;
  width: 17%;
  float: left;
  margin-top: 7px;
  font-size: 11px;
}
.btn-copy {
  color: #000000;
  float: left;
  border: none;
  background: none;
  font-weight: bold;
  font-size: small;
}
.btn-copy:focus {
  box-shadow: none;
}
.btn-copy:hover {
  color: green;
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

.btn .fa.fa-sm {
  font-size: 1rem;
  margin-top: -5px;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
