<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Internal Job Order
          <router-link tag="span" to="/ViewActivityTicketType">
            <button
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
            >
              Service Types
            </button>
          </router-link>

          <button
            type="button"
            @click="getTempDiscon"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Load Temp Discon
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
                        v-model="tblFilter"
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
                        @click="fnExcelReport('tbl_activity_ticket')"
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
              <b-row style="width: 100%; margin-left: 5px; margin-top: 0">
                <button
                  type="button"
                  @click="filter_data('activity_tickets.status', 'Completed')"
                  class="
                    btn btn-success btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Completed
                </button>
                <button
                  type="button"
                  @click="filter_data('activity_tickets.status', 'Pending')"
                  class="
                    btn btn-success btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Pending
                </button>
                <button
                  type="button"
                  @click="
                    filter_data('activity_tickets.status', 'Verification')
                  "
                  class="
                    btn btn-success btn-labeled
                    pull-right
                    margin-left-10
                    statusBtn
                  "
                >
                  Verification
                </button>
              </b-row>
            </div>
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
              <b-col style="float: right; padding-right: 0"></b-col>
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
        <b-table
          id="tbl_activity_ticket"
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="fields"
          :items="items"
          :busy="tblisBusy"
          :current-page="currentPage"
          :per-page="perPage"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
          @filtered="onFiltered"
          @row-clicked="tblRowClicked"
          style="cursor: pointer"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template v-slot:cell(action)="row">
            <b-button
              v-if="
                roles.helpdesk &&
                row.item.ticket_type_id == 1 &&
                row.item.status == 'Pending'
              "
              variant="success"
              @click="btnChangeStatus(row.item, 'Temp Discon')"
              size="sm"
              >Disconnect</b-button
            >
            <b-button
              v-if="
                roles.helpdesk &&
                row.item.ticket_type_id == 2 &&
                row.item.status == 'Pending'
              "
              variant="success"
              @click="btnChangeStatus(row.item, 'Disconnected')"
              size="sm"
              >Disconnect</b-button
            >
            <b-button
              v-if="
                roles.helpdesk &&
                row.item.ticket_type_id == 3 &&
                row.item.status == 'Pending'
              "
              variant="success"
              @click="btnChangeStatus(row.item, 'Active')"
              size="sm"
              >Activate</b-button
            >

            <b-button
              v-if="
                roles.helpdesk &&
                row.item.ticket_type_id == 4 &&
                row.item.status == 'Pending'
              "
              variant="success"
              @click="btnChangePackage(row.item, 'Upgrade')"
              size="sm"
              >Upgrade</b-button
            >

            <b-button
              v-if="
                roles.helpdesk &&
                row.item.ticket_type_id == 5 &&
                row.item.status == 'Pending'
              "
              variant="success"
              @click="btnChangePackage(row.item, 'Downgrade')"
              size="sm"
              >Downgrade</b-button
            >

            <b-button
              v-if="roles.accounting && row.item.status == 'Update Billing'"
              variant="success"
              @click="btnUpdateSoa(row.item)"
              size="sm"
              >Update SOA</b-button
            >

            <p-check
              v-if="
                roles.accounting &&
                row.item.ticket_type_id == 1 &&
                row.item.status == 'Verification'
              "
              class="p-icon p-curve p-jelly p-bigger"
              color="success"
              v-model="row.item.check"
              @change="checkVerify()"
            >
              <i slot="extra" class="icon fas fa-check"></i>
            </p-check>
          </template>

          <template v-slot:cell(ticket_type.name)="row">
            <b-button
              class="btn-status"
              variant="warning"
              v-if="row.item.ticket_type_id == 1"
              size="sm"
              :disabled="true"
              >Temp Discon</b-button
            >
            <b-button
              class="btn-status"
              variant="danger"
              v-if="row.item.ticket_type_id == 2"
              size="sm"
              :disabled="true"
              >Permanent Discon</b-button
            >
            <b-button
              class="btn-status"
              variant="info"
              v-if="row.item.ticket_type_id == 3"
              size="sm"
              :disabled="true"
              >Re-Activate</b-button
            >
            <b-button
              class="btn-status"
              variant="success"
              v-if="row.item.ticket_type_id == 4"
              size="sm"
              :disabled="true"
              >Upgrade</b-button
            >
            <b-button
              class="btn-status"
              variant="success"
              v-if="row.item.ticket_type_id == 5"
              size="sm"
              :disabled="true"
              >Downgrade</b-button
            >
          </template>

          <template slot="table-caption"></template>
        </b-table>

        <div class="rowFields mx-auto row">
          <div class="col-lg-9">
            <button
              v-if="showVerifyBtn"
              @click="btnVerify"
              type="button"
              class="btn btn-success btn-labeled"
            >
              Verify
            </button>
          </div>
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
    </div>

    <!-- modalViewJO -->
    <b-modal
      id="modalViewJO"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      body-text-variant="dark"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="lg"
      ok-only
    >
      <div slot="modal-header" class="w-100">
        <div class="rowFields mx-auto row">
          <div class="col-lg-6">
            <h5>Job Order Info</h5>
          </div>
          <div class="col-lg-6">
            <i>
              <p class="float-right textLabel1">
                Created By:{{ editTicket.created_by.name }}
                <br />
                <br />
                Created On:{{ editTicket.created_at }}
              </p>
            </i>
          </div>
        </div>
      </div>
      <b-card
        bg-variant="light"
        align="center"
        style="max-height: 100%"
        class="editTicket-bcard"
        id="client"
      >
        <center>
          <div style="display: flex; font-size: 12px">
            <div style="width: 50%; float: left">
              <label
                style="
                  display: block;
                  text-align: left;
                  font-weight: bold;
                  color: green;
                "
                >Ticket No.:{{ editTicket.id }}</label
              >
              <label style="display: block; text-align: left"
                >Account No.: {{ editTicket.client.acc_no }}</label
              >
              <label style="display: block; text-align: left"
                >Account Name: {{ editTicket.client.name }}</label
              >

              <label style="display: block; text-align: left"
                >Address: {{ editTicket.client.location }}</label
              >
              <label style="display: block; text-align: left"
                >Contact No.: {{ editTicket.client.contact }}</label
              >
            </div>
            <!-- <div style="width:50%; float:left">{{ editTicket }}</div> -->

            <br />
          </div>
        </center>
      </b-card>
      <hr />
      <b-card
        v-if="editTicket.ticket_type_id == 4 || editTicket.ticket_type_id == 5"
        bg-variant="light"
        align="center"
        style="max-height: 100%"
        class="editTicket-bcard"
      >
        <center>
          <div style="display: flex; font-size: 12px">
            <div style="width: 50%; float: left">
              <label
                style="
                  display: block;
                  text-align: left;
                  font-weight: bold;
                  color: green;
                "
                >Package to be change</label
              >
              <label style="display: block; text-align: left"
                >Package Code: {{ editTicket.packageToUpdate.name }}</label
              >
              <label style="display: block; text-align: left"
                >Max Speed: {{ editTicket.packageToUpdate.max_speed }}</label
              >
              <label style="display: block; text-align: left"
                >CIR: {{ editTicket.packageToUpdate.cir }}</label
              >
              <label style="display: block; text-align: left"
                >MRR: {{ editTicket.packageToUpdate.mrr }}</label
              >
            </div>
            <!-- <div style="width:50%; float:left">{{ editTicket }}</div> -->
            <br />
          </div>
        </center>
      </b-card>

      <!--  remarks area -->
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
                  style="width: 100%; height: 32px; float: right; color: white"
                  >ADD REMARKS</b-button
                >
              </div>
              <div style="width: 50%; float: left"></div>

              <br />
            </div>
            <br />
            <div
              style="margin-top: 20px"
              v-for="(remarks, index) in editTicket.remarks_log"
              :key="index"
              v-show="remarks.form_type == 'jobOrder'"
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
                    v-if="remarks.commentVisibility == 'hide'"
                  >
                    Reply
                    <i class="fas fa-caret-down"></i>
                  </label>
                  <label
                    style="float: right; cursor: pointer; color: blue"
                    @click="remarks.commentVisibility = 'hide'"
                    v-if="remarks.commentVisibility == 'show'"
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
                  <label style="float: left; text-align: left">{{
                    reply.comments
                  }}</label>
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
                v-if="remarks.commentVisibility == 'show'"
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
                      addComments_clicked(remarks.id, index, remarks.replies)
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

      <template slot="modal-footer" slot-scope="{}">
        <b-button
          v-if="editTicket.status == 'Addendum'"
          size="sm"
          variant="success"
          v-b-modal="'modalPrintPreviewAddendum'"
          >Print Preview Addendum</b-button
        >

        <b-button
          v-if="editTicket.status == 'Addendum'"
          size="sm"
          variant="success"
          @click="btnAddendumSigned"
          >Addendum Signed</b-button
        >
      </template>
    </b-modal>
    <!-- End modalViewJO -->

    <!-- modalPrintPreviewAddendum -->
    <b-modal
      id="modalPrintPreviewAddendum"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      :no-close-on-backdrop="true"
      :show-close="false"
      ok-only
    >
      <template v-slot:modal-title>
        <label>Print Preview</label>
      </template>
      <div id="printAddendum" class="printAddendum" style="color: black">
        <table class="parent">
          <tr style="background-color: rgba(255, 255, 255, 0.8)">
            <td>
              <div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td class="normalTbl" style="width: 30%">
                        <img
                          src="https://i.ibb.co/CW69PnT/email-logo.png"
                          style="width: 50%"
                        />
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 40%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="vertical-align: top; width: 30%"
                      >
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 12px;
                            line-height: 1.3;
                            float: right;
                          "
                        >
                          <b>
                            Control No: {{ editTicket.id }} <br />Dctech
                            Building, Shanghai Street, <br />Matina Aplaya,
                            Davao City <br />Davao Del Sur 8000, Philippines
                            <br />Tel #: (082) 221-2380 <br />VAT Registered
                            TIN: 003-375-571-000
                          </b>
                        </p>
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td class="normalTbl" style="vertical-align: center">
                        <center>
                          <h4>
                            <strong>Addendum to an Existing Contract</strong>
                          </h4>
                        </center>
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row" style="margin-top: 30px">
                  <div class="rowFields2 mx-auto row">
                    <label class="pull-left paragraph">
                      This document is in reference to a contract agreement
                      dated __________________________, between the following
                      parties that are named below in this document.
                    </label>
                  </div>
                </div>
                <div class="rowFields mx-auto row">
                  <div class="rowFields2 mx-auto row">
                    <label class="pull-left paragraph">
                      May it be known that the undersigned parties, for good
                      consideration, do hereby agree to make the following
                      changes and / or additions that are outlined below. These
                      additions shall be made valid as if they are included in
                      the original stated contract.
                    </label>
                  </div>
                </div>

                <!-- JOB ORDER DETAILS: UPGRADE,ETC.. -->

                <b-card
                  bg-variant="light"
                  align="center"
                  style="
                    max-height: 100%;
                    margin-top: 30px;
                    width: 80%;
                    margin-left: 10%;
                  "
                  class="editTicket-bcard"
                >
                  <center>
                    <div style="display: flex; font-size: 12px">
                      <div style="width: 50%; float: left">
                        <label
                          style="
                            display: block;
                            text-align: left;
                            font-weight: bold;
                            color: green;
                          "
                          >UPGRADE</label
                        >
                        <label
                          style="
                            margin-top: 15px;
                            display: block;
                            text-align: left;
                            font-weight: bold;
                          "
                          >NEW PACKAGE:</label
                        >
                        <label
                          style="
                            margin-top: 15px;
                            display: block;
                            text-align: left;
                            font-weight: bold;
                          "
                          >OLD PACKAGE:</label
                        >
                      </div>
                      <div style="width: 50%; float: left"></div>

                      <br />
                    </div>
                  </center>
                </b-card>

                <!-- END OF JO DETAILS -->
                <div class="rowFields mx-auto row">
                  <div class="rowFields2 mx-auto row">
                    <label class="pull-left paragraph">
                      This contract shall take effect
                      ___________________________________________ until
                      ________________________________ .
                    </label>
                  </div>
                </div>
                <div class="rowFields mx-auto row">
                  <div class="rowFields2 mx-auto row">
                    <label class="pull-left paragraph">
                      No other terms or conditions of the above mentioned
                      contract shall be negated or changed as a result of this
                      here stated addendum.
                    </label>
                  </div>
                </div>
                <div class="rowFields mx-auto row" style="margin-top: 50px">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 10%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: center; width: 30%"
                      >
                        <h6>
                          <strong>
                            DCTECH MICRO SERVICES, INC.
                            <br />
                          </strong>
                          (DCTECH)
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: center; width: 30%"
                      >
                        <h6>
                          <strong>
                            {{ editTicket.client.name }}
                            <br />
                          </strong>
                          (CLIENT)
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 10%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: left; width: 30%"
                      >
                        <h6>
                          <strong>By:</strong>
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: left; width: 30%"
                      >
                        <h6>
                          <strong>By:</strong>
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 10%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: center; width: 30%"
                      >
                        <h6>
                          <strong>
                            RYAN SUMALINOG
                            <br />
                          </strong>
                          Vice-President for Operations
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: center; width: 30%"
                      >
                        <h6>
                          <strong>
                            {{ editTicket.client.owner_name }}
                            <br />
                          </strong>
                          Owner
                        </h6>
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 20%"
                      ></td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td
                        class="normalTbl"
                        style="vertical-align: center; width: 10%"
                      ></td>
                      <td
                        class="normalTbl"
                        style="text-align: left; width: 30%"
                      >
                        <label class="pull-left paragraph"
                          >Signed in the Presence of:</label
                        >
                        <br />
                        <label class="pull-left paragraph"
                          >______________________________________</label
                        >
                      </td>
                      <td
                        class="normalTbl"
                        style="text-align: left; width: 60%"
                      ></td>
                    </tr>
                  </table>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <template slot="modal-footer" slot-scope="{}">
        <b-button
          size="sm"
          variant="success"
          @click="printElement('printAddendum')"
          >Print</b-button
        >
      </template>
    </b-modal>
    <!-- modalPrintPreviewAddendum -->

    <!-- modalUpdateBilling ---------------------------------------------------------------------------------------->
    <b-modal
      id="modalUpdateBilling"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="Update Billings"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <label>Package Code:</label>
          <br />
          <label>Max Speed:</label>
          <br />
          <label>CIR:</label>
          <br />
          <label>MRR:</label>
        </div>
        <div class="col-lg-3">
          <label>{{ packageToUpdate.name }}</label>
          <br />
          <label>{{ packageToUpdate.max_speed }}</label>
          <br />
          <label>{{ packageToUpdate.cir }}</label>
          <br />
          <label>{{ packageToUpdate.mrr }}</label>
        </div>
      </div>
      <!-- table -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-12">
          <b-table
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="bill_modi_fields"
            :items="bill_modi_items"
            :busy="bill_modi_tblisBusy"
            thead-class="cursorPointer-th"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <template #cell(selected)="row">
              <p-check
                class="p-icon p-curve p-jelly p-bigger"
                color="success"
                v-model="row.item.isSelected"
              >
                <i slot="extra" class="icon fas fa-check"></i>
              </p-check>
            </template>

            <template v-slot:cell(description)="row">
              <span v-if="row.item.isSelected">
                <input
                  type="text"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Description to be update"
                  placeholder="Description"
                  v-model="row.item.description"
                />
              </span>
            </template>

            <template v-slot:cell(price_update)="row">
              <span v-if="row.item.isSelected">
                <input
                  type="text"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Amount to be update"
                  placeholder="Amount"
                  v-model="row.item.price_update"
                />
              </span>
            </template>

            <template v-slot:cell(balance_update)="row">
              <span v-if="row.item.isSelected">
                <input
                  type="text"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Balance to be update"
                  placeholder="Balance"
                  v-model="row.item.balance_update"
                />
              </span>
            </template>

            <template slot="table-caption"></template>
          </b-table>
        </div>
      </div>
      <!-- table -->
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="updateSOAOK()"
          >Save</b-button
        >
      </template>
    </b-modal>
    <!-- End modalUpdateBilling -->
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import PrettyCheck from "pretty-checkbox-vue/check";
import swal from "sweetalert";
import modal_fsr from "../../modal_vue/modal_fsr.vue";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    modal_fsr: modal_fsr,
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "action", label: "Action", sortable: true },
        { key: "status", label: "Status", sortable: true },
        { key: "state", label: "State", sortable: true },
        { key: "id", label: "Ticket #", sortable: true },
        { key: "ticket_type.name", label: "Ticket Type", sortable: true },
        { key: "client.acc_no", label: "Account #", sortable: true },
        { key: "client.name", label: "Client Name", sortable: true },
        { key: "client.branch.name", label: "Branch", sortable: true },
        { key: "created_by.name", label: "Created By", sortable: true },
        { key: "aging", label: "Aging", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true },
      ],
      items: [],
      items_copy: [],
      tblFilter: null,
      searchby: "clients.name",
      searchby_list: [
        {
          name: "Account Name",
          id: "clients.name",
        },
        {
          name: "Ticket type",
          id: "activity_ticket_types.name",
        },
      ],
      totalRows: 1,
      currentPage: 1,
      perPage: 50,
      pageOptions: [20, 50, 100, 250],
      showVerifyBtn: false,
      verifySelected: [],
      user: [],
      roles: [],
      state_role: "",
      fsr_data: {
        user: {
          email: "",
        },
      },
      editTicket: {
        packageToUpdate: {},
        aging: "",
        check: false,
        client: [],
        client_id: "",
        created_at: "",
        created_by: [],
        id: "",
        remarks: "",
        state: "",
        status: "",
        ticket_type: "",
        ticket_type_id: "",
        updated_at: "",
        updated_by: "",
      },
      remarksText: "",
      commentsText: [],
      bill_modi_fields: [
        { key: "selected" },
        { key: "date" },
        { key: "description" },
        { key: "price", label: "Amount" },
        { key: "balance", label: "Balance" },
        { key: "price_update", label: "Amount Update" },
        { key: "balance_update", label: "Balance Update" },
      ],
      bill_modi_items: [],
      bill_modi_tblisBusy: false,
      packageToUpdate: {},
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
  },
  mounted() {
    this.load();
    this.load_item();
  },
  updated() {},
  methods: {
    load_item() {
      if (this.roles.helpdesk) this.state_role = "helpdesk";
      if (this.roles.accounting) this.state_role = "accounting";
      if (this.roles.accounting && this.roles.helpdesk)
        this.state_role = "admin";

      this.$http
        .get("api/ActivityTicket/subIndex/" + this.state_role)
        .then((response) => {
          console.log(response.body);
          this.items = response.body.items;
          this.items_copy = response.body.items;
          this.totalRows = this.items.length;
          this.tblisBusy = false;
        });
    },
    load() {
      this.$nextTick(function () {
        this.$root.$emit("clearNav");
        setTimeout(function () {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById(
            "navbarActivityTicket"
          ).style.backgroundColor = "#2196f3";
        }, 100);
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr";
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
      console.log(item);
      this.editTicket = item;
      this.$bvModal.show("modalViewJO");
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {},
    btnUpdate() {},
    btnDelete() {},
    filter_data(by, filter) {
      this.searchby = by;
      this.tblFilter = filter;
      this.search_data();
    },
    search_data() {
      this.$root.$emit("pageLoading");
      this.tblisBusy = true;
      this.$http
        .get(
          "api/ActivityTicket/search_data/" +
            this.searchby +
            "/" +
            this.tblFilter
        )
        .then((response) => {
          console.log(response.body);
          this.items = response.body.items;
          this.totalRows = this.items.length;
          this.tblisBusy = false;
          this.$root.$emit("pageLoaded");
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: response.body.error,
            text: "",
            icon: "error",
            dangerMode: true,
          });
          this.tblisBusy = false;
          this.$root.$emit("pageLoaded");
        });
    },
    filterClear() {
      this.tblFilter = "";
      this.items = this.items_copy;
      this.totalRows = this.items.length;
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
            this.perPage = 50;
            return sa;
          }.bind(this),
          1000
        );
      });
    },
    btnChangeStatus(item, type) {
      console.log(item);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.tblisBusy = true;
          var dataa = {
            id: item.client_id,
            row: "status",
            data: type,
            user_id: this.user.id,
            user_name: this.user.name,
            activity_ticket: item,
            role: this.state_role,
          };

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/ActivityTicket/updateClientStatus", dataa)
            .then((response) => {
              swal("Updated", "", "success");
              this.$root.$emit("pageLoaded");
              this.items = response.body.items;
              this.tblisBusy = false;
            })
            .catch((response) => {
              console.log(response.body);
              swal({
                title: response.body.error,
                text: "",
                icon: "error",
                dangerMode: true,
              });
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    btnChangePackage(item, type) {
      console.log(item);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.$root.$emit("pageLoading");
          item.updated_by = this.user.id;
          item.state = "accounting";
          item.status = "Update Billing";
          item.user_name = this.user.name;
          item.user_id = this.user.id;
          item.role = this.state_role;
          this.updateActivityTicket(item, item.id);
        }
      });
    },
    getTempDiscon() {
      this.$root.$emit("pageLoading");
      this.$http.post("api/ActivityTicket/getTempDiscon").then((response) => {
        this.$root.$emit("pageLoaded");
        swal("loaded", "", "success");
      });
    },
    checkVerify() {
      this.verifySelected = [];
      this.showVerifyBtn = false;
      this.items.forEach((item) => {
        if (item.check == true) {
          this.showVerifyBtn = true;
          this.verifySelected.push(item);
        }
      });
    },
    btnVerify() {
      console.log(this.verifySelected);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.tblisBusy = false;

          var dataa = {
            user_id: this.user.id,
            user_name: this.user.name,
            role: this.state_role,
            verifySelected: this.verifySelected,
          };

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/ActivityTicket/verify", dataa)
            .then((response) => {
              swal("Updated", "", "success");
              this.$root.$emit("pageLoaded");
              this.items = response.body.items;
              this.tblisBusy = false;
            })
            .catch((response) => {
              console.log(response.body);
              swal({
                title: response.body.error,
                text: "",
                icon: "error",
                dangerMode: true,
              });
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    addRemarks_clicked() {
      if (this.remarksText != "") {
        var data = {
          ticket_id: this.editTicket.id,
          user_id: this.user.id,
          user_name: this.user.name,
          remarks: this.remarksText,
          form_type: "jobOrder",
        };

        console.log(data);
        this.$http.post("api/TicketRemarksLog", data).then((response) => {
          this.remarksText = "";
          this.editTicket.remarks_log = response.body;
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the remarks text area");
      }
    },
    addComments_clicked(id, i, replies) {
      if (this.commentsText[i] != "") {
        var data = {
          remarks_id: id,
          user_id: this.user.id,
          user: this.user,
          comments: this.commentsText[i],
        };

        replies.push(data);
        // console.log(data);
        this.$http.post("api/TicketCommentsLog", data).then((response) => {
          this.commentsText[i] = "";
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the comments text area");
      }
    },
    printElement(id) {
      var elem = document.getElementById(id);
      var domClone = elem.cloneNode(true);
      var $printSection = document.getElementById("printSection");
      if (!$printSection) {
        var $printSection = document.createElement("div");
        $printSection.id = "printSection";
        document.body.appendChild($printSection);
      }
      $printSection.innerHTML = "";
      $printSection.appendChild(domClone);
      window.print();
    },
    btnAddendumSigned() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.$root.$emit("pageLoading");
          this.editTicket.updated_by = this.user.id;
          this.editTicket.status = "Pending";
          this.editTicket.state = "helpdesk";
          this.editTicket.user_name = this.user.name;
          this.editTicket.user_id = this.user.id;
          this.editTicket.role = this.state_role;
          this.updateActivityTicket(this.editTicket, this.editTicket.id);
        }
      });
    },
    updateActivityTicket(data, id) {
      this.$http
        .put("api/ActivityTicket/" + id, data)
        .then((response) => {
          console.log(response.body);
          swal("Updated", "", "success");
          this.$root.$emit("pageLoaded");
          // this.items = response.body.items;
          // this.tblisBusy = false;
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true,
          });
          this.$root.$emit("pageLoaded");
        });
    },
    btnUpdateSoa(item) {
      this.editTicket = item;
      this.packageToUpdate = item.packageToUpdate;
      console.log(this.editTicket);
      this.$bvModal.show("modalUpdateBilling");

      this.bill_modi_tblisBusy = true;
      this.$http
        .post("api/Billing/to_pay/" + item.client_id + "/wholebill")
        .then((response) => {
          var temp = response.body;
          temp.forEach((i, index) => {
            var datenow = new Date();
            var date = new Date(i.date);
            i.price_update = item.packageToUpdate.mrr;
            i.balance_update = item.packageToUpdate.mrr;
            if (date >= datenow) {
              i.isSelected = true;
            }
          });
          this.bill_modi_items = temp;
          this.bill_modi_tblisBusy = false;
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: "Error",
            text: response.body.error + " " + response.body.message,
            icon: "error",
            dangerMode: true,
          });
          this.bill_modi_tblisBusy = false;
        });
    },
    updateSOAOK() {
      var dataa = {
        id: this.editTicket.client_id,
        data: {
          package_id: this.packageToUpdate.id,
          package_type_id: this.packageToUpdate.package_type.id,
          soa_items: this.bill_modi_items,
        },
        user_id: this.user.id,
        user_name: this.user.name,
        activity_ticket_id: this.editTicket.id,
      };
      this.$http
        .post("api/ActivityTicket/updateSOA", dataa)
        .then((response) => {
          console.log(response.body);
          swal("Updated", "", "success");

          this.$bvModal.hide("modalUpdateBilling");
          this.$root.$emit("pageLoaded");
        })
        .catch((response) => {
          console.log(response.body);
          swal({
            title: response.body.error,
            text: "",
            icon: "error",
            dangerMode: true,
          });
          this.tblisBusy = false;
          this.$root.$emit("pageLoaded");
        });
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
modal-header {
  border: 1px solid red;
}

.searchRow {
  width: 90%;
  margin-left: 5px;
  margin-top: 0;
}
.searchForm {
  height: 30px;
  margin-left: 5px;
  border-radius: 5px 0 0 5px;
}

.clearBtn {
  width: 100px;
  color: white;
  border-radius: 0 5px 5px 0;
}

.editTicket-bcard > .card-body {
  padding: 10px;
  padding-left: 13px;
  max-width: 100%;
  border: 1px solid #ced4da;
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
.paragraph {
  vertical-align: text-top;
  font-size: 14px;
  line-height: 1.3;
  margin-top: 25px;
}
</style>
