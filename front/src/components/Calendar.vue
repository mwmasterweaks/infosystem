<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <div class="elClr panel-title">
          Calendar
          <div>
            <button
              v-if="roles.create_event"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              v-b-modal="'ModelAddEvent'"
            >
              Create Event
            </button>

            <SummaryReportButton v-if="roles.rm"></SummaryReportButton>
            <PerformanceButton v-if="roles.rm"></PerformanceButton>
            <miscGraph v-if="roles.rm"></miscGraph>
            <tempGraph v-if="roles.rm || roles.sales"></tempGraph>
            <troubleGraph v-if="roles.rm"></troubleGraph>
            <salesGraph v-if="roles.rm"></salesGraph>

            <button
              v-if="user.id == 1"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              @click="testSSH"
            >
              TEST SSH
            </button>

            <button
              v-if="user.id == 1"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              @click="testMail('gmail')"
            >
              TEST gmail
            </button>

            <button
              v-if="user.id == 1"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              @click="testMail('zimbra')"
            >
              TEST zimbra
            </button>

            <button
              v-if="user.id == 1"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              @click="download_database"
            >
              DL DB
            </button>
          </div>
          <div>
            <button
              v-if="user.id == 1"
              type="button"
              style="margin-top:-20px;"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              @click="test"
            >
              test
            </button>
          </div>
        </div>
      </div>

      <div class="elClr panel-body">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <Fullcalendar
              @eventClick="showEvent"
              :plugins="calendarPlugins"
              :events="events"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- modalEdit ---------------------------------------------------------------------------------------->
    <b-modal
      id="modalEdit"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
    >
      <div slot="modal-header" class="w-100">
        <div class="rowFields mx-auto row">
          <div class="col-lg-6">
            <h5>
              <p class="text-success">
                {{ client_details.name }}
                <span v-if="client_details.line_transfer == '1'"
                  >- Line Transfer</span
                >
              </p>
            </h5>
          </div>
        </div>
      </div>
      <!-- form -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Contact person:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.contact_person }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Contact No.:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.contact }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Address:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.location }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Sales In-Charge:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.client1.sales.name }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Tech In-Charge:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.client1.engineer.name }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Cable Category:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.cable_category }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">FOC Layout:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.foc_layout }}</b>
          </p>
        </div>
      </div>

      <div
        class="rowFields mx-auto row"
        v-if="client_details.foc_layout != 'Completed'"
      >
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Layout Remarks:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.layout_remarks }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">OTC:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.otc }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Aging:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.agingCount }}</b>
          </p>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Mapping Status:</p>
        </div>
        <div class="col-lg-9">
          <i class="fas fa-check" v-show="client_details.contract_status" />
          <i class="fas fa-times" v-show="!client_details.contract_status" />
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Modem Status:</p>
        </div>
        <div class="col-lg-9">
          <i class="fas fa-check" v-show="client_details.modem_status" />
          <i class="fas fa-times" v-show="!client_details.modem_status" />
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel" style="margin: 0px;">Applied date:</p>
        </div>
        <div class="col-lg-9">
          <p class="textLabel" style="margin: 0px;">
            <b>{{ client_details.applied_date }}</b>
          </p>
        </div>
      </div>
      <!-- /form -->
      <div slot="modal-footer" class="w-100"></div>
    </b-modal>
    <!-- End modalEdit -->

    <!-- modalEditTicket -->
    <b-modal
      id="modalEditTicket"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
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
      <!-- form -->
      <!-- <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Select Ticket no.:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="ticket_select_list"
            v-model="ticket_selected"
            option-value="id"
            option-text="mTicket_id"
            placeholder="select Ticket no."
            @input="onChangeSelectTicket"
          ></model-list-select>
        </div>
      </div>-->
      <div>
        <div class="rowFields mx-auto row">
          <div class="col-lg-4">
            <b>
              <p class="text-success">
                Ticket No.: {{ editTicket.mTicket_id }}
              </p>
            </b>
            <p>Account Name: {{ editTicket.client.name }}</p>
            <p>Address: {{ editTicket.client.location }}</p>
            <p>Contact No.: {{ editTicket.client.contact }}</p>
          </div>

          <div class="col-lg-4">
            <p>IP Assigned: {{ editTicket.client.ip_assigned }}</p>
            <p>VLAN: {{ editTicket.vlan }}</p>
            <p>OLT: {{ editTicket.ip }}</p>
            <p>PON: {{ editTicket.pon }} - {{ editTicket.onu }}</p>
          </div>
        </div>
        <!-- complain EditTicket -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Complaint:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="2"
              name="Findings"
              class="form-control"
              v-b-tooltip.hover
              title="Input your Complaint"
              placeholder="Complaint"
              v-validate="'required'"
              v-model.trim="editTicket.complain"
            ></textarea>
            <small
              class="text-danger pull-left"
              v-show="errors.has('Complaint')"
              >Complaint is required.</small
            >
          </div>
        </div>
        <!-- findings EditTicket -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Findings:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="5"
              name="Findings"
              class="form-control"
              v-b-tooltip.hover
              title="Input findings"
              placeholder="Findings"
              v-validate="'required'"
              v-model.trim="editTicket.findings"
            ></textarea>
            <small class="text-danger pull-left" v-show="errors.has('findings')"
              >findings is required</small
            >
          </div>
        </div>
        <!-- action EditTicket -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Action:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="5"
              name="Action"
              class="form-control"
              v-b-tooltip.hover
              title="Input Action "
              placeholder="Action"
              v-validate="'required'"
              v-model.trim="editTicket.action"
            ></textarea>
            <small class="text-danger pull-left" v-show="errors.has('action')"
              >action is required</small
            >
          </div>
        </div>
        <!-- status EditTicket -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Status:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="Status_Ticket"
              v-model="editTicket.Status_Ticket_id"
              option-value="id"
              option-text="name"
              placeholder="select status"
            ></model-list-select>
          </div>
        </div>
        <!-- targetdate EditTicket -->
        <div
          class="rowFields mx-auto row"
          v-if="
            editTicket.Status_Ticket_id == '1' ||
              editTicket.Status_Ticket_id == '2' ||
              editTicket.Status_Ticket_id == '4'
          "
        >
          <div class="col-lg-3">
            <p class="textLabel">Target Date:</p>
          </div>
          <div class="col-lg-9">
            <date-picker
              v-model="editTicket.target_date"
              :config="AppliedDateoptions"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="editTicket.Status_Ticket_id == '3'"
        >
          <div class="col-lg-3">
            <p class="textLabel">Report:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="2"
              name="repot"
              class="form-control"
              v-b-tooltip.hover
              title="Input Report "
              placeholder="Report"
              v-model.trim="editTicket.report"
            ></textarea>
          </div>
        </div>

        <!-- tech assign EditTicket -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Technical Assigned:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="3"
              name="Technical Assigned"
              class="form-control"
              v-b-tooltip.hover
              title="Input Action "
              placeholder="Name of technical assigned"
              v-model.trim="editTicket.technical_assigned"
            ></textarea>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks</p>
          </div>
          <div class="col-lg-9">
            <div
              class="elClr panel"
              v-for="(item, index) in editTicket.remarks_log"
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
          @click="btnUpdateTicket()"
          v-if="roles.update_ticket"
          >Save changes</b-button
        >
      </div>
    </b-modal>
    <!-- End modalEditTicket -->

    <!-- ModelAddEvent ---------------------------------------------------------------------------------------->
    <b-modal
      id="ModelAddEvent"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="Create Calendar Event"
    >
      <!-- form -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Event Name:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="name"
            ref="name"
            class="form-control"
            v-b-tooltip.hover
            title="Name of the Event"
            placeholder="Event name"
            v-validate="'required'"
            v-model.trim="item_add.name"
            autocomplete="off"
            autofocus="on"
          />
          <small class="text-danger pull-left" v-show="errors.has('name')"
            >Event name is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Location(Where):</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="type"
            class="form-control"
            v-b-tooltip.hover
            title="Input Location"
            placeholder="Location"
            v-validate="'required'"
            v-model.trim="item_add.location"
            autocomplete="off"
            autofocus="on"
          />
          <small class="text-danger pull-left" v-show="errors.has('type')"
            >location is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Date & time(when):</p>
        </div>
        <div class="col-lg-4">
          <div class="input-group">
            <date-picker
              v-model="item_add.from"
              :config="DateTimeOptions"
              placeholder="From"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="input-group">
            <date-picker
              v-model="item_add.to"
              :config="DateTimeOptions"
              placeholder="To"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Remarks:</p>
        </div>
        <div class="col-lg-9">
          <textarea
            rows="2"
            name="remarks"
            class="form-control"
            v-b-tooltip.hover
            title="Input Remarks"
            placeholder="Remarks"
            v-model.trim="item_add.remarks"
          ></textarea>
        </div>
      </div>

      <!-- /form -->
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="btnAdd()"
          >Create</b-button
        >
      </template>
    </b-modal>
    <!-- End ModelAddEvent -->

    <!-- modalEditEvent ---------------------------------------------------------------------------------------->
    <b-modal
      id="modalEditEvent"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="Update Calendar Event"
    >
      <!-- form -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Event Name:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="name"
            ref="name"
            class="form-control"
            v-b-tooltip.hover
            title="Name of the Event"
            placeholder="Event name"
            v-validate="'required'"
            v-model.trim="item_edit.name"
            autocomplete="off"
            autofocus="on"
          />
          <small class="text-danger pull-left" v-show="errors.has('name')"
            >Event name is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Location(Where):</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="type"
            class="form-control"
            v-b-tooltip.hover
            title="Input Location"
            placeholder="Location"
            v-validate="'required'"
            v-model.trim="item_edit.location"
            autocomplete="off"
            autofocus="on"
          />
          <small class="text-danger pull-left" v-show="errors.has('type')"
            >location is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Date & time(when):</p>
        </div>
        <div class="col-lg-4">
          <div class="input-group">
            <date-picker
              v-model="item_edit.from"
              :config="DateTimeOptions"
              placeholder="From"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="input-group">
            <date-picker
              v-model="item_edit.to"
              :config="DateTimeOptions"
              placeholder="To"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Remarks:</p>
        </div>
        <div class="col-lg-9">
          <textarea
            rows="2"
            name="remarks"
            class="form-control"
            v-b-tooltip.hover
            title="Input Remarks"
            placeholder="Remarks"
            v-model.trim="item_edit.remarks"
          ></textarea>
        </div>
      </div>
      <!-- /form -->
      <template slot="modal-footer" slot-scope="{}">
        <b-button
          v-if="roles.update_event"
          size="sm"
          variant="success"
          @click="btnUpdate()"
          >Update</b-button
        >
        <b-button
          v-if="roles.delete_event"
          size="sm"
          variant="danger"
          @click="btnDelete()"
          >Delete</b-button
        >
      </template>
    </b-modal>
    <!-- End modalEditEvent -->

    <!--modalTicketList-------->
    <b-modal
      id="modalTicketList"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="Ticket List"
    >
      <!--Form-------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title"></p>
        </div>
        <div class="elClr panel-body">
          <div class="scrollmenu">
            <b-table
              id="ticketTable"
              class="elClr"
              striped
              show-empty
              hover
              outlined
              :fields="fields"
              :items="items"
              :busy="tblisBusy"
              :tbody-tr-class="tblRowClass"
              head-variant=" elClr"
              thead-class="cursorPointer-th"
              @row-clicked="tblRowClicked"
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
            >
              <template v-slot:cell(statname)="data">
                <span v-html="data.value"></span>
              </template>
              <template v-slot:cell(mTicket_id)="data">
                <span v-html="data.value"></span>
              </template>
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>

              <template slot="target_date" slot-scope="row">
                <!-- <span
                v-if="row.item.target_date == null &&
                row.item.statname == 'Pending'"
                >None</span>-->
                <center>
                  <span v-if="row.item.statname == 'Close'">Fixed</span>
                </center>
                <center>
                  <span v-if="row.item.statname == 'For Rebates'">Fixed</span>
                </center>

                <span
                  class="btn btn-success disabled"
                  style="width:100%;"
                  v-if="
                    row.item.target_date != null &&
                      row.item.target_date != datenow &&
                      row.item.target_date != dateTomorrow &&
                      row.item.target_date != dateYesterday &&
                      1 > dateDiffInDays(datenow, row.item.target_date)
                  "
                  >{{ row.item.target_date }}</span
                >

                <span
                  class="btn btn-success disabled"
                  style="width:100%;"
                  v-if="row.item.target_date == datenow"
                  >Today</span
                >
                <span
                  class="btn btn-success disabled"
                  style="width:100%;"
                  v-if="row.item.target_date == dateTomorrow"
                  >Tomorrow</span
                >

                <span
                  class="btn btn-warning disabled"
                  style="width:100%;"
                  v-if="row.item.target_date == dateYesterday"
                  >Yesterday</span
                >

                <span
                  class="btn btn-danger disabled"
                  style="width:100%;"
                  v-if="
                    1 < dateDiffInDays(datenow, row.item.target_date) &&
                      row.item.target_date != null
                  "
                >
                  {{ dateDiffInDays(datenow, row.item.target_date) }} Days delay
                </span>
              </template>

              <template slot="table-caption"></template>
            </b-table>
          </div>
        </div>
      </div>
      <!--Form-------->

      <div slot="modal-footer" class="w-100"></div>
    </b-modal>
    <!--modalTicketList-------->

    <client_modals v-bind:data="cModalData"></client_modals>
  </div>
</template>

<script>
import Fullcalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";
import SummaryReportButton from "./modal_vue/modal_summay_report.vue";
import PerformanceButton from "./modal_vue/model_inst_perform_shet.vue";
import miscGraph from "./modal_vue/model_misc_graph.vue";
import tempGraph from "./modal_vue/model_temp_graph.vue";
import troubleGraph from "./modal_vue/model_trouble_graph.vue";
import salesGraph from "./modal_vue/modal_sales_graph.vue";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import client_modals from "./modal_vue/client_modals.vue";

export default {
  components: {
    SummaryReportButton: SummaryReportButton,
    PerformanceButton: PerformanceButton,
    miscGraph: miscGraph,
    tempGraph: tempGraph,
    troubleGraph: troubleGraph,
    salesGraph: salesGraph,
    Fullcalendar,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    client_modals: client_modals
  },
  data() {
    return {
      cModalData: {
        field: 0,
        items: []
      },
      isDestroy: false,
      tblisBusy: false,
      sortBy: "updated_at",
      sortDesc: true,
      fields: [
        {
          key: "mTicket_id",
          label: "Ticket No.",
          sortable: true,
          formatter: value => {
            return "<i><b>" + value + "</b></i>";
          }
        },
        {
          key: "statname",
          label: "Status",
          sortable: true,
          formatter: value => {
            if (value == "Close")
              return '<span class="btn btn-success disabled" style="width:100%;"> Fixed </span>';
            else if (value == "For Rebates")
              return '<span class="btn btn-success disabled" style="width:100%;"> For Rebates </span>';
            else if (value == "Pending")
              return '<span class="btn btn-warning disabled" style="width:100%;"> Pending </span>';
            else if (value == "Urgent")
              return '<span class="btn btn-danger disabled" style="width:100%;"> Urgent </span>';
            else if (value == "For Tech Visit")
              return '<span class="btn btn-primary disabled" style="width:100%;"> For tech visit </span>';
            else return value;
          }
        },
        {
          key: "target_date",
          label: "Target Date",
          sortable: true
        },
        { key: "client.name", label: "Account name", sortable: true },
        {
          key: "complain",
          label: "Complaint",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 50) temp = "...";
              return value.slice(0, 50) + temp;
            } else return "";
          }
        },
        {
          key: "findings",
          label: "Findings",

          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 50) temp = "...";
              return value.slice(0, 50) + temp;
            } else return "";
          }
        },
        {
          key: "action",
          label: "Action",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 50) temp = "...";
              return value.slice(0, 50) + temp;
            } else return "";
          }
        },
        {
          key: "remarks",
          label: "Remarks",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 50) temp = "...";
              return value.slice(0, 50) + temp;
            } else return "";
          }
        }
      ],
      datenow: new Date(),
      dateTomorrow: new Date(),
      dateYesterday: new Date(),
      items: [],
      tblFilter: null,
      totalRows: 0,
      calendarPlugins: [dayGridPlugin, interactionPlugin],
      tickets: {},
      schedules: {},
      events: [],
      roles: [],
      client_details: {
        client_id: "",
        sales_id: "",
        otc: "",
        mapping_status: false,
        line_transfer: "",
        cable_category: "",
        foc_length: "",
        foc_layout: "",
        layout_remarks: "",
        modem_status: false,
        applied_date: "",
        target_date: "",
        status: "",
        aging: null,
        client1: {
          name: "",
          location: "",
          contact: "",
          sales: {
            name: ""
          },
          engineer: {
            name: ""
          }
        },
        client: {
          name: "",
          location: "",
          contact: ""
        }
      },
      sales: [],
      focLayoutOption: [
        { id: "Done", name: "Done" },
        { id: "Done outside only", name: "Done outside only" },
        { id: "Done inside only", name: "Done inside only" },
        { id: "Pending", name: "Pending" }
      ],
      otcOption: [
        { id: "Paid", name: "Paid" },
        { id: "Promo", name: "Promo" },
        { id: "Billing", name: "Billing" },
        { id: "Waived", name: "Waived" },
        { id: "Waiting for C&C advisory", name: "Waiting for C&C advisory" },
        { id: "NTP", name: "NTP" }
      ],
      cableCategoryOption: [
        { id: "Drop Fiber", name: "Drop Fiber" },
        { id: "Hard Fiber", name: "Hard Fiber" },
        { id: "UTP", name: "Unshielded twisted pair (UTP)" }
      ],
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      editTicket: {
        client_id: "",
        complain: "",
        findings: "",
        action: "",
        remarks: "",
        report: "",
        Status_Ticket_id: "",
        technical_assigned: "",
        client: {
          name: "",
          location: "",
          contact: ""
        },
        user: {
          name: ""
        }
      },
      user: [],
      ticket_select_list: [],
      ticket_selected: {},
      calendar_events: {},
      Status_Ticket: [],
      frequency_list: [
        {
          name: "Yearly"
        },
        {
          name: "This year only"
        }
      ],
      item_add: {
        name: "",
        remarks: "",
        location: "",
        frequency: "frequency",
        from: "",
        to: ""
      },
      item_edit: {
        name: "",
        remarks: "",
        location: "",
        frequency: "frequency",
        from: "",
        to: ""
      },
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      DateTimeOptions: {
        // https://momentjs.com/docs/#/displaying/ YYYY-MM-DD h:mm
        format: "YYYY-MM-DD HH:mm",
        useCurrent: false,
        showClear: true,
        showClose: true
      }
    };
  },
  created() {
    this.sales = this.$global.getSales();
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.events = [
      // {
      //   id: 55,
      //   title: this.tickets[0].statname,
      //   backgroundColor: "red",
      //   start: new Date(),
      //   end: new Date()
      // },
      // {
      //   id: 2,
      //   title: "2",
      //   backgroundColor: "yellow",
      //   start: new Date(),
      //   end: new Date()
      // }
    ];
    this.loadpermin();

    this.Status_Ticket = this.$global.getTicketStatus();
  },

  destroyed() {
    this.isDestroy = true;
  },
  methods: {
    loadpermin() {
      if (this.isDestroy == false) {
        this.events = [];
        var tempStat = "";
        var tempDate = "";
        var data = {
          num: 0,
          name: "",
          date: "",
          data: []
        };

        this.$http
          .get("api/Ticket/subIndex/" + this.user.region_id)
          .then(function(response) {
            this.tickets = response.body.items;
            var c = -1;
            var bgcolor = "";
            this.tickets.forEach(ticket => {
              if (
                ticket.statname == "Pending" ||
                ticket.statname == "Urgent" ||
                ticket.statname == "For Tech Visit"
              ) {
                if (
                  tempStat != ticket.statname ||
                  tempDate != ticket.target_date
                ) {
                  tempStat = ticket.statname;
                  tempDate = ticket.target_date;

                  c++;
                  if (c != 0) {
                    if (data.name == "For Tech Visit") bgcolor = "#007bff";
                    if (data.name == "Pending") bgcolor = "#ffc107";
                    if (data.name == "Urgent") bgcolor = "#dc3545";

                    var temp = {
                      id: data.num,
                      backgroundColor: bgcolor,
                      borderColor: bgcolor,
                      title: data.num + " " + data.name,
                      start: data.date,
                      end: data.date,
                      data: data.data
                    };
                    this.events.push(temp);
                  }
                  data.data = [];
                  data.num = 1;
                  data.name = ticket.statname;
                  data.date = ticket.target_date;
                  data.data.push(ticket);
                } else {
                  data.num++;
                  data.data.push(ticket);
                }
              }
            });
            if (data.name == "For Tech Visit") bgcolor = "#007bff";
            if (data.name == "Pending") bgcolor = "#ffc107";
            if (data.name == "Urgent") bgcolor = "#dc3545";

            var temp = {
              id: data.num,
              backgroundColor: bgcolor,
              borderColor: bgcolor,
              title: data.num + " " + data.name,
              start: data.date,
              end: data.date,
              data: data.data
            };
            this.events.push(temp);
          });

        this.$http
          .get("api/clientDetail/subIndex/" + this.user.region_id)
          .then(function(response) {
            this.schedules = response.body;

            this.schedules.forEach(schedule => {
              if (schedule.status == "done") {
                var temp = {
                  id: schedule.client_id,
                  backgroundColor: "green",
                  borderColor: "green",
                  title: schedule.client1.name,
                  start: schedule.target_date,
                  end: schedule.target_date,
                  data: schedule
                };
                this.events.push(temp);
              }
            });
          });

        this.$http
          .get("api/calendar_events/subIndex/" + this.user.region_id)
          .then(function(response) {
            this.calendar_events = response.body;

            this.calendar_events.forEach(cal_event => {
              var temp = {
                id: cal_event.id,
                backgroundColor: "grey",
                borderColor: "grey",
                title: cal_event.name,
                start: cal_event.from,
                end: cal_event.to,
                data: cal_event
              };
              this.events.push(temp);
            });
          });

        //this.getEvents();
        // this.$nextTick(function() {
        //   setTimeout(this.loadpermin, 50000);
        // });
      }
    },
    addNewEvent() {
      axios
        .post("/api/calendar", {
          ...this.newEvent
        })
        .then(data => {
          this.getEvents(); // update our list of events
          this.resetForm(); // clear newEvent properties (e.g. title, start_date and end_date)
        })
        .catch(err =>
          console.log("Unable to add new event!", err.response.data)
        );
    },
    showEvent(arg) {
      if (arg.event.backgroundColor == "green") {
        if (arg.event.extendedProps.data.client1.sales == null)
          arg.event.extendedProps.data.client1.sales = {
            name: ""
          };
        if (arg.event.extendedProps.data.client1.engineer == null)
          arg.event.extendedProps.data.client1.engineer = {
            name: ""
          };
        this.client_details = arg.event.extendedProps.data;
        this.$bvModal.show("modalEdit");
      } else if (arg.event.backgroundColor == "grey") {
        this.item_edit = arg.event.extendedProps.data;
        this.$bvModal.show("modalEditEvent");
      } else {
        this.items = arg.event.extendedProps.data;
        this.editTicket = {
          client_id: "",
          complain: "",
          findings: "",
          action: "",
          remarks: "",
          report: "",
          Status_Ticket_id: "",
          technical_assigned: "",
          client: {
            name: "",
            location: "",
            contact: ""
          },
          user: {
            name: ""
          }
        };
        this.ticket_selected = {};
        this.$bvModal.show("modalTicketList");
      }
    },
    onChangeSelectTicket() {
      this.editTicket = this.ticket_selected;
    },
    updateEvent() {},

    deleteEvent() {
      axios
        .delete("/api/calendar/" + this.indexToUpdate)
        .then(resp => {
          this.resetForm();
          this.getEvents();
          this.addingMode = !this.addingMode;
        })
        .catch(err =>
          console.log("Unable to delete event!", err.response.data)
        );
    },

    getEvents() {
      this.events = [];
      var tempStat = "";
      var tempDate = "";
      var data = {
        num: 0,
        name: "",
        date: "",
        data: []
      };

      var c = -1;
      var bgcolor = "";
      this.tickets.forEach(ticket => {
        if (
          ticket.statname == "Pending" ||
          ticket.statname == "Urgent" ||
          ticket.statname == "For Tech Visit"
        ) {
          if (tempStat != ticket.statname || tempDate != ticket.target_date) {
            tempStat = ticket.statname;
            tempDate = ticket.target_date;

            c++;
            if (c != 0) {
              if (data.name == "For Tech Visit") bgcolor = "#007bff";
              if (data.name == "Pending") bgcolor = "#ffc107";
              if (data.name == "Urgent") bgcolor = "#dc3545";

              var temp = {
                id: data.num,
                backgroundColor: bgcolor,
                borderColor: bgcolor,
                title: data.num + " " + data.name,
                start: data.date,
                end: data.date,
                data: data.data
              };
              this.events.push(temp);
            }
            data.data = [];
            data.num = 1;
            data.name = ticket.statname;
            data.date = ticket.target_date;
            data.data.push(ticket);
          } else {
            data.num++;
            data.data.push(ticket);
          }
        }
      });
      if (data.name == "For Tech Visit") bgcolor = "#007bff";
      if (data.name == "Pending") bgcolor = "#ffc107";
      if (data.name == "Urgent") bgcolor = "#dc3545";

      var temp = {
        id: data.num,
        backgroundColor: bgcolor,
        borderColor: bgcolor,
        title: data.num + " " + data.name,
        start: data.date,
        end: data.date,
        data: data.data
      };
      this.events.push(temp);

      this.schedules.forEach(schedule => {
        if (schedule.status == "done") {
          var temp = {
            id: schedule.client_id,
            backgroundColor: "green",
            borderColor: "green",
            title: schedule.client1.name,
            start: schedule.target_date,
            end: schedule.target_date,
            data: schedule
          };
          this.events.push(temp);
        }
      });
      this.calendar_events.forEach(cal_event => {
        var temp = {
          id: cal_event.id,
          backgroundColor: "grey",
          borderColor: "grey",
          title: cal_event.name,
          start: cal_event.from,
          end: cal_event.to,
          data: cal_event
        };
        this.events.push(temp);
      });
    },
    activateClicked() {
      swal({
        title: "Confirmation",
        text: "Do you really want to ACTIVATE this client?",
        icon: "warning",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          //this.tblisBusy = true;
          this.$http
            .get(
              "api/clientDetail/activateClient/" + this.client_details.client_id
            )
            .then(response => {
              swal("Activated!", "activate successfully", "success").then(
                value => {
                  this.schedules = response.body;

                  this.getEvents();
                  this.$bvModal.hide("modalEdit");
                  //this.totalRows = this.items.length;
                  //  this.tblisBusy = false;
                }
              );
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },
    btnUpdateTicket() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this Ticket?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              this.editTicket.region_id = this.user.region_id;
              this.$http
                .put("api/Ticket/" + this.editTicket.id, this.editTicket)
                .then(response => {
                  this.tickets = response.body;
                  swal("", "Update successfully", "success");
                  this.getEvents();
                  this.$bvModal.hide("modalEditTicket");
                })
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    if (value) {
                    }
                  });
                });
            }
          });
        }
      });
    },
    btnAdd() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.item_add.region_id = this.user.region_id;
          this.item_add.user_id = this.user.id;
          this.item_add.user_name = this.user.name;
          this.$http
            .post("api/calendar_events", this.item_add)
            .then(response => {
              swal("Notification", "Added successfully", "success");
              this.calendar_events = response.body;
              this.item_add = {
                name: "",
                remarks: "",
                location: "",
                frequency: "frequency",
                from: "",
                to: ""
              };

              this.$bvModal.hide("ModelAddEvent");
              this.getEvents();
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              this.item_edit.user_id = this.user.id;
              this.item_edit.user_name = this.user.name;
              this.$http
                .put("api/calendar_events/" + this.item_edit.id, this.item_edit)
                .then(response => {
                  this.calendar_events = response.body;
                  swal("", "Update successfully", "success");
                  this.$bvModal.hide("modalEditEvent");

                  this.getEvents();
                })
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    if (value) {
                    }
                  });
                });
            }
          });
        }
      });
    },
    btnDelete() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this item permanently",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.items = [];
          this.tblisBusy = true;
          var data = {
            id: this.item_edit.id,
            region_id: this.user.region_id,
            user_id: this.user.id,
            user_name: this.user.name
          };
          this.$http
            .post("api/calendar_events/destroy1", data)
            .then(response => {
              this.$bvModal.hide("modalEditEvent");
              swal("", "Item has been deleted", "success").then(value => {
                this.calendar_events = response.body;
                this.getEvents();
              });
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
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
    tblRowClicked(item, index, event) {
      if (this.roles.update_ticket) {
        this.$bvModal.show("modalEditTicket");
        this.editTicket = item;
      }
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
    testSSH() {
      swal({
        title: "Confirmation",
        text: "Do you really want to ACTIVATE this client?",
        icon: "warning",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          this.$root.$emit("pageLoading");
          var data = {
            command: "asdf"
          };
          this.$http
            .post("api/calendar_events/testSSH", data)
            .then(response => {
              this.$root.$emit("pageLoaded");
              console.log(response.body);
            });
          // this.$http.put("api/Bucket/encryptIt/infosytem").then(response => {
          //   console.log(response.body);
          // });
          //  this.$http.put("api/Bucket/decryptIt/infosytem").then(response => {
          //   console.log(response.body);
          // });
        }
      });
    },
    testMail(mail) {
      this.$root.$emit("pageLoading");
      this.$http
        .get("api/calendar_events/testAutoCommand")
        .then(response => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
        })
        .catch(response => {
          console.log(response);
          this.$root.$emit("pageLoaded");
        });
      /*  swal({
        title: "Confirmation",
        text: "Do you really want to ACTIVATE this client?",
        icon: "warning",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          this.$root.$emit("pageLoading");

          var sendTO = [
            {
              email: "mwmasterweaks@gmail.com",
              name: "Masterweak"
            },
            {
              email: "pbismonte@dctechmicro.com",
              name: "Peter Pogi"
            }
          ];
          var ccto = [
            {
              email: "rnd@dctechmicro.com",
              name: "rnd"
            }
          ];
          var data = {
            email: mail,
            user_email: this.user.email,
            user_name: this.user.name,
            sendTo: sendTO,
            CCTO: ccto
          };

          this.$http
            .post("api/calendar_events/testEmail", data)
            .then(response => {
              console.log(response.body);
              this.$root.$emit("pageLoaded");
            });
        }
      }); */
    },
    test() {
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/calendar_events/testfunction")
        .then(response => {
          console.log(response.body);

          var bucketIP = "202.137.112.14";
          //BUCKET TO IS
          this.cModalData.field = 4;
          this.cModalData.items = response.body.oneIsToOne;
          this.excelReportCSV("genTable1", bucketIP + " BUCKET TO IS");

          //BUCKET LIST NA WALA SA IS BASE SUBS_NAME
          this.$nextTick(function() {
            setTimeout(
              function() {
                this.cModalData.field = 3;
                this.cModalData.items = response.body.oneIsToZero;
                this.excelReportCSV(
                  "genTable1",
                  bucketIP + " BUCKET ZERO IN IS"
                );
              }.bind(this),
              2000
            );
          });
          //BUCKET LIST NA WALA SA IS BASE SUBS_NAME
          this.$nextTick(function() {
            setTimeout(
              function() {
                this.cModalData.field = 3;
                this.cModalData.items = response.body.oneIsToMany;
                this.excelReportCSV(
                  "genTable1",
                  bucketIP + " BUCKET MANY TO IS"
                );

                this.$root.$emit("pageLoaded");
              }.bind(this),
              4000
            );
          });
        })
        .catch(response => {
          swal("error");
          console.log(response);
          this.$root.$emit("pageLoaded");
        });
    },

    download_database() {
      swal({
        title: "Confirmation",
        text: "Do you really want to ACTIVATE this client?",
        icon: "warning",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/calendar_events/download_database")
            .then(response => {
              var localDate = new Date();
              var month = localDate.getMonth();
              var day = localDate.getDate();
              var year = localDate.getFullYear();
              var element = document.createElement("a");

              if (month <= 8) month = "0" + (localDate.getMonth() + 1);
              else if (month >= 9) month = localDate.getMonth() + 1;

              element.setAttribute(
                "href",
                "data:text/plain;charset=utf-8, " +
                  encodeURIComponent(response.body)
              );
              element.setAttribute(
                "download",
                "infosystemDBBAKUP " +
                  year +
                  "-" +
                  month +
                  "-" +
                  day +
                  " " +
                  localDate.toLocaleTimeString() +
                  ".sql"
              );
              document.body.appendChild(element);
              element.click();
              document.body.removeChild(element);
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    excelReportCSV(tbl, name) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var csv_data = [];
            var tab = document.getElementById(tbl);

            //var rows = document.getElementsByTagName("tr");
            for (var i = 0; i < tab.rows.length; i++) {
              // Get each column data
              var cols = tab.rows[i].querySelectorAll("td,th");

              // Stores each csv row data
              var csvrow = [];
              for (var j = 0; j < cols.length; j++) {
                // Get the text data of each cell of
                // a row and push it to csvrow
                var temp = cols[j].innerHTML.replace(
                  /<div[^>]*>|<\/div>/gi,
                  ""
                );
                if (temp.includes(",")) temp = '"' + temp + '"';
                csvrow.push(temp);
              }

              // Combine each column value with comma
              csv_data.push(csvrow.join(","));
            }
            // combine each row data with new line character
            csv_data = csv_data.join("\n");
            this.downloadCSVFile(csv_data, name);
            /* We will use this function later to download
            the data in a csv file downloadCSVFile(csv_data);
            */
          }.bind(this),
          1000
        );
      });
    },
    downloadCSVFile(csv_data, name) {
      // Create CSV file object and feed our
      // csv_data into it
      var CSVFile = new Blob([csv_data], { type: "text/csv" });

      // Create to temporary link to initiate
      // download process
      var temp_link = document.createElement("a");

      // Download csv file
      temp_link.download = name + ".csv";
      var url = window.URL.createObjectURL(CSVFile);
      temp_link.href = url;

      // This link should not be displayed
      temp_link.style.display = "none";
      document.body.appendChild(temp_link);

      // Automatically click the link to trigger download
      temp_link.click();
      document.body.removeChild(temp_link);
    }
  },
  watch: {
    indexToUpdate() {
      return this.indexToUpdate;
    }
  }
};
</script>

<style lang="css">
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";

.fc-title {
  color: #fff;
}

.fc-title:hover {
  cursor: pointer;
}

.textLabel {
  padding: 0%;
}
.pre-formatted {
  white-space: pre;
}
</style>
