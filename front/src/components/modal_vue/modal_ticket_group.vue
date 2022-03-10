<template>
  <div>
    <b-modal
      id="modalEditTicketGroup"
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
            <h5>Ticket Group Info</h5>
          </div>
          <div class="col-lg-6">
            <i>
              <p class="float-right textLabel1">
                Created By: {{ data.user.name }}
                <br />
                <br />
                Created On: {{ data.created_at_formated }}
              </p>
            </i>
          </div>
        </div>
      </div>
      <div id="newEditTicket">
        <b-card align="center" style="height:20px;border:none">
          <center>
            <div style="float:left;margin-top:-10px;margin-left:-15px">
              <label class="ticketNo">Ticket No.: {{ data.mTicket_id }}</label>
            </div>
            <div style="float:right;margin-top:-20px;margin-right:-15px">
              <h6>
                Status
                <b-badge v-if="data.Status_Ticket_id == '1'" variant="warning"
                  >Pending</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '9'" variant="dark"
                  >Modem/Line Transfer</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '2'" variant="danger"
                  >Urgent</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '3'" variant="success"
                  >Fixed</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '4'" variant="primary"
                  >For Tech Visit</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '6'" variant="secondary"
                  >For Observation</b-badge
                >
                <b-badge v-if="data.Status_Ticket_id == '7'" variant="info"
                  >For ITND</b-badge
                >
              </h6>
            </div>
          </center>
        </b-card>
        <br />

        <b-card bg-variant="light" align="center" class="b-card-ticket">
          <div style="float:right;margin-top:-10px">
            <p
              class="textbtnedit"
              @click="editChangeClient = !editChangeClient"
            >
              + Add More Clients
            </p>
          </div>
          <br />
          <center>
            <div style="display:flex;width:95%">
              <div style="width:100%;height:50%;margin-top:-15px">
                <div
                  class="rowFields mx-auto row multipleClient"
                  v-show="editChangeClient"
                >
                  <div style="width:100%;margin-left:1.5px;">
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
                      @input="addClient"
                    ></multiselect>
                  </div>
                </div>
                <br />
                <b-table
                  :items="data.ticket_group_client"
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
                      ><i class="fas fa-trash-restore-alt"></i
                    ></b-button>
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
            <div style="display:flex;width:95%">
              <div style="width:15%;height:20%">
                <h6 style="text-align:left">Complaint Details</h6>
              </div>
              <br />

              <div style="width:85%;height:80%">
                <b-row class="findingsRow">
                  <b-col class="clientCol">
                    <label style="float:left;padding-left:7px;margin-top:5px;"
                      >Complaint</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;max-width:80%">
                    <b-form-group>
                      <b-input-group>
                        <model-list-select
                          :list="complaints_new"
                          v-model="data.complaint_new"
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
                    <label style="float:left;padding-left:7px;margin-top:5px;"
                      >Connection Status</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;max-width:80%">
                    <b-form-group>
                      <b-input-group>
                        <model-list-select
                          :list="connection_status_list"
                          v-model="data.connection_status"
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
                    <label style="float:left;padding-left:7px;margin-top:5px;"
                      >Ticket Status</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;max-width:80%">
                    <b-form-group>
                      <b-input-group>
                        <model-list-select
                          :list="Status_Ticket"
                          v-model="data.Status_Ticket_id"
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
                  <b-col style="float:left;margin-top:-5px;width:100%">
                    <b>NOTE:</b>
                    <b-form-textarea
                      id="textarea-small"
                      size="sm"
                      v-model.lazy="data.cacti"
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
                <div id="bucket" style="display:flex;margin-top:0; width: 100%">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes">BUCKET SERVER STATUS :</label>
                    </b-form-group>
                  </div>
                  <div style="width:70%;">
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
                        value="8"
                      />
                      <div class="state p-success">
                        <i class="icon mdi mdi-check"></i>
                        <label>Can arp-ed</label>
                      </div>
                    </div>
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
                        value="9"
                      />
                      <div class="state p-success">
                        <i class="icon mdi mdi-check"></i>
                        <label>Can't be arp-ed</label>
                      </div>
                    </div>
                    <div
                      class="input-group"
                      style="width:53%;margin-right:30px;float:right"
                    >
                      <label class="tsCheckboxes">Usage</label>
                      <input
                        id="bwmon"
                        type="text"
                        class="form-control usageLine"
                        v-model="data.bwmon"
                        name="msg"
                      />
                    </div>
                  </div>
                </div>
                <div id="device" style="width:100%;display:flex;margin-top:0">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes">DEVICE STATUS :</label>
                    </b-form-group>
                  </div>
                  <div style="width:70%;">
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
                        value="10"
                      />
                      <div class="state p-success">
                        <i class="icon mdi mdi-check"></i>
                        <label>Blinking LOS</label>
                      </div>
                    </div>
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
                        value="11"
                      />
                      <div class="state p-success">
                        <i class="icon mdi mdi-check"></i>
                        <label>Blinking PON</label>
                      </div>
                    </div>
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
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
                  style="width:100%;display:flex;margin-top:-15px;"
                >
                  <div style="width:30%"></div>
                  <div style="width:70%">
                    <div class="input-group" style="width:95%">
                      <label class="tsCheckboxes">Others</label>
                      <input
                        id="loss"
                        type="text"
                        class="form-control ticketInputLine"
                        v-model="data.device"
                        name="msg"
                      />
                    </div>
                  </div>
                </div>
                <div id="loss" style="width:100%;display:flex;margin-top:5px">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes">OPTICAL POWER :</label>
                    </b-form-group>
                  </div>
                  <div style="width:70%">
                    <div class="input-group" style="width:95%">
                      <label class="tsCheckboxes">Loss</label>
                      <input
                        id="loss"
                        type="text"
                        class="form-control ticketInputLine"
                        v-model="data.loss"
                        name="msg"
                      />
                    </div>
                  </div>
                </div>
                <div id="downtime" style="width:100%;display:flex;margin-top:0">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes">AFFECTED BY DOWNTIME :</label>
                    </b-form-group>
                  </div>
                  <div style="width:70%">
                    <div class="input-group" style="width:95%">
                      <label class="tsCheckboxes">Details</label>
                      <input
                        id="downtime"
                        type="text"
                        class="form-control ticketInputLine"
                        v-model="data.downtime"
                        name="msg"
                      />
                    </div>
                  </div>
                </div>
                <div id="ping" style="width:100%;display:flex;margin-top:0">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes"
                        >PING & TRACEROUTE TEST :</label
                      >
                    </b-form-group>
                  </div>
                  <div style="width:70%">
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
                        value="6"
                      />
                      <div class="state p-success">
                        <i class="icon mdi mdi-check"></i>
                        <label>Attached Results</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="speed" style="width:100%;display:flex;margin-top:0">
                  <div style="width:30%">
                    <b-form-group style="float:left;text-align:left">
                      <label class="tsCheckboxes">SPEED TEST :</label>
                    </b-form-group>
                  </div>
                  <div style="width:70%">
                    <div class="pretty p-icon p-jelly p-bigger troubleCheckbox">
                      <input
                        type="checkbox"
                        v-model="data.selected_trouble"
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

        <!-- report bcard -->
        <b-card
          id="report"
          border-variant="dark"
          bg-variant="light"
          align="center"
          style="max-height:100%"
          v-if="data.Status_Ticket_id == '3'"
        >
          <center>
            <div style="display:flex;width:95%">
              <div style="width:15%;height:20%">
                <h6 style="float:left">Report</h6>
              </div>
              <br />

              <div style="width:85%;height:80%">
                <b-row class="reportRow">
                  <b-col class="reportCol">
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Findings</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px">
                    <b-form-group>
                      <b-form-textarea
                        id="textarea-small"
                        size="sm"
                        style="width:100%"
                        v-model="data.rep_findings"
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
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Action</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px">
                    <b-form-group>
                      <b-form-textarea
                        id="textarea-small"
                        size="sm"
                        v-model="data.rep_action"
                        style="width:100%;height:100%"
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
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Consolidated Tech</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;">
                    <b-form-group>
                      <b-form-input
                        id="input-default"
                        style="max-width:100%;height:30px"
                        v-model.lazy="data.technical_assigned"
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
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Date Time Fixed</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;">
                    <b-form-group>
                      <date-picker
                        v-model="data.date_time_fixed"
                        :config="DateTimeOptions"
                        @input="datetimeChange"
                        autocomplete="off"
                        style="max-width:100%;height:30px"
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
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Hrs of Downtime</label
                    >
                  </b-col>
                  <b-col style="margin-left:-4px;margin-top:-5px;">
                    <b-form-group>
                      <b-form-input
                        id="input-default"
                        style="max-width:100%;height:30px"
                        placeholder="Hours"
                        v-model="data.downtime_hours"
                      ></b-form-input>
                    </b-form-group>
                  </b-col>
                </b-row>
                <b-row class="clientRow">
                  <b-col class="clientCol">
                    <label style="float:left;padding-left:7px;margin-top:3px"
                      >Rebatable</label
                    >
                  </b-col>
                  <b-col style="margin-left:10px;margin-top:-5px;">
                    <b-form-group>
                      <b-form-checkbox
                        style="float:left"
                        value="1"
                        v-model="data.rebatable"
                      >
                        <label class="tsCheckboxes">Yes</label>
                      </b-form-checkbox>
                      <b-form-checkbox
                        style="float:left"
                        value="2"
                        v-model="data.rebatable"
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
          style="max-height:100%;border:none;background:none"
          :body-text-variant="' elClr'"
        >
          <center>
            <div style="width:100%">
              <div
                style="width:100%;float:left;font-size:13px;font-weight:bold"
              >
                <label style="float:left">Remarks</label>
              </div>
              <br />
              <div
                style="width:100%;float:left;margin-top:2px;margin-bottom:12px;display:flex;"
              >
                <div style="width:80%;flex-grow:1">
                  <b-form-textarea
                    id="textarea-small"
                    size="sm"
                    v-model.lazy="remarksText"
                    style="width:100%;height:90%"
                    placeholder="Type remarks here. . ."
                  ></b-form-textarea>
                </div>
                <div
                  style="width:12%;height:100%;flex-grow:1;margin-left:15px;"
                >
                  <b-button
                    squared
                    variant="success"
                    @click="addRemarks_clicked"
                    style="width:100%;height:32px;float:right;color:white"
                    >ADD REMARKS</b-button
                  >
                </div>
              </div>
              <br />
              <!-- remarks display -->
              <div
                style="margin-top:20px;"
                v-for="(remarks, index) in data.remarks_log"
                :key="index"
                v-show="remarks.form_type == 'ticket_group'"
              >
                <div id="title" style="display:flex;width:100%">
                  <label
                    style="float:left;text-align:left;font-size:12px;font-weight:bold;width:70%"
                  >
                    <i
                      class="fa fa-user-circle"
                      aria-hidden="true"
                      style="margin-right:5px"
                    ></i>
                    {{ remarks.user.name }}
                  </label>

                  <label
                    style="float:right;text-align:right;font-size:10px;width:30%"
                    >{{ remarks.created_at }}</label
                  >
                </div>

                <br />
                <div id="body" style="display:flex;width:100%; margin-top:-8px">
                  <div style="width:82%;margin-left:5px;" class="wrapper">
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
                  <div style="width:18%;">
                    <label
                      style="float:right;cursor: pointer;color:blue"
                      @click="remarks.commentVisibility = 'show'"
                      v-show="remarks.commentVisibility == 'hide'"
                    >
                      Reply
                      <i class="fas fa-caret-down"></i>
                    </label>
                    <label
                      style="float:right;cursor: pointer;color:blue"
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
                  style="margin-top:0;background:none;width:95%;float:right"
                  v-for="reply in remarks.replies"
                  :key="reply.id"
                >
                  <div id="title" style="display:flex;width:100%">
                    <label
                      style="float:left;text-align:left;font-size:12px;font-weight:bold;width:70%"
                    >
                      <i
                        class="fa fa-user-circle"
                        aria-hidden="true"
                        style="margin-right:5px"
                      ></i>
                      {{ reply.user.name }}
                    </label>

                    <label
                      style="float:right;text-align:right;font-size:10px;width:30%"
                      >{{ reply.created_at }}</label
                    >
                  </div>

                  <br />
                  <div
                    id="body"
                    style="display:flex;width:95%; margin-top:-8px"
                  >
                    <label style="float:left;text-align:left;">
                      {{ reply.comments }}
                    </label>
                  </div>
                </div>

                <!-- new comment -->
                <div
                  style="width:90%;float:right;margin-top:2px;margin-bottom:12px;display:flex;"
                  v-show="remarks.commentVisibility == 'show'"
                >
                  <div style="width:80%;flex-grow:1">
                    <b-form-textarea
                      id="textarea - small"
                      size="sm"
                      v-model="commentsText[index]"
                      style="width:100%;height:90%"
                      placeholder="Type comment here. . ."
                    ></b-form-textarea>
                  </div>
                  <div
                    style="width:12%;height:100%;flex-grow:1;margin-left:15px;"
                  >
                    <b-button
                      squared
                      variant="success"
                      @click="
                        addComments_clicked(remarks.id, index, remarks.replies)
                      "
                      style="width:100%;height:32px;float:right;color:white"
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

      <div slot="modal-footer" class="w-100">
        <i>
          <p class="float-left textLabel1">Updated By: {{ data.updated_by }}</p>
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

        <b-button
          class="float-right margin-right-10"
          size="sm"
          v-if="roles.update_ticket"
          variant="primary"
          v-b-modal="'viewAttachments'"
          >View attachments</b-button
        >
      </div>
    </b-modal>

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
      <div style="float:right;margin-top:-10px;margin-bottom:10px">
        <b-button squared variant="outline-dark" @click="isShowing ^= true">
          <i class="material-icons">add_photo_alternate</i>
        </b-button>
      </div>
      <div style="width:100%;display:flex" v-if="preview">
        <b-img
          thumbnail
          fluid
          :src="$attachment_path + preview"
          style="width:100%;height:500px!important"
        ></b-img>
      </div>
      <br />
      <div style="width:100%;display:flex">
        <b-container class="bv-example-row">
          <b-row>
            <b-col
              cols="2"
              v-for="(item, i) in data.attachments"
              :key="i"
              style="background:none"
            >
              <b-img
                thumbnail
                fluid
                v-b-tooltip="'Preview Image'"
                :v-for="(item, i) in data.attachments"
                :src="$attachment_path + item"
                @click="previewImg(item)"
                style="height:80px!important;width:100px!important;cursor: pointer;"
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
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import Multiselect from "vue-multiselect";
import UploadImages from "vue-upload-drop-images";
import VueRangedatePicker from "vue-rangedate-picker";

export default {
  props: ["data"],
  components: {
    multiselect: Multiselect,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck,
    UploadImages
  },
  data() {
    return {
      isShowing: false,
      preview: "",
      complaints_new: [],
      ticket_clients: [],
      commentsText: [],
      ComptotalRows: 1,
      StattotalRows: 1,
      filterData: "",
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
          id: "slow",
          name: "Slow"
        },
        {
          id: "intermittent",
          name: "Intermittent"
        }
      ],
      Status_Ticket: [],
      DateTimeOptions: {
        format: "YYYY-MM-DD HH:mm",
        useCurrent: false,
        showClear: true,
        showClose: true
      },
      clientField: [
        { key: "client.name", label: "Name", sortable: true },
        { key: "actions", label: "Actions" }
      ],
      remarksText: "",
      tblFilter: null,
      tblisBusy: true,
      perPage: 10,
      currentPage: 1,
      editChangeClient: false,
      addedClients: [],
      clients: [],
      users: [],
      roles: [],
      attachments: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.regions = this.$global.getRegion();
    this.Status_Ticket = this.$global.getTicketStatus();
    this.loadClients();
    console.log(this.data.ticket_group_client);
  },

  mounted() {
    this.loadComplaints();
    this.StattotalRows = this.Status_Ticket.length;
  },
  methods: {
    previewImg(item) {
      console.log(item);
      this.preview = item;
    },
    loadClients() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function(response) {
          this.clients = response.body;
        });
    },
    handleImages(e) {
      this.data.attachments = [];
      e.forEach(item => {
        var fileReader = new FileReader();
        fileReader.readAsDataURL(item);
        fileReader.onload = item => {
          this.data.attachments.push(item.target.result);
        };
      });
    },
    addClient() {
      this.addedClients.forEach(item => {
        var data = {
          ticket_group_id: this.data.id,
          client_id: item.id,
          client: item
        };
        this.data.ticket_group_client.push(data);
      });
    },

    loadComplaints() {
      this.$http.get("api/ComplaintList").then(response => {
        this.complaints_new = response.body;
        this.ComptotalRows = this.complaints_new.length;
      });
    },
    datetimeChange() {
      var hours =
        Math.abs(new Date(data.created_at) - new Date(data.date_time_fixed)) /
        36e5;
      data.downtime_hours = hours;
    },
    addRemarks_clicked() {
      if (this.remarksText != "") {
        var data = {
          ticket_id: this.data.id,
          user_id: this.user.id,
          remarks: this.remarksText,
          form_type: "ticket_group"
        };
        this.$http.post("api/TicketRemarksLog", data).then(response => {
          this.remarksText = "";
          this.data.remarks_log = response.body;
          console.log(response.body);
        });
      } else {
        swal("Please add some text in the remarks text area");
      }
    },
    addComments_clicked(id, i, stor) {
      console.log(id);
      // if (this.commentsText[i] != "") {
      //   var data = {
      //     remarks_id: id,
      //     user_id: this.user.id,
      //     user: this.user,
      //     comments: this.commentsText[i]
      //   };
      //   stor.push(data);
      //   // console.log(data);
      //   this.$http.post("api/TicketCommentsLog", data).then(response => {
      //     this.commentsText[i] = "";
      //     console.log(response.body);
      //   });
      // } else {
      //   swal("Please add some text in the comments text area");
      // }
    },
    removeClient(id) {
      // console.log(id);
      for (
        var index = 0;
        index < this.data.ticket_group_client.length;
        index++
      ) {
        if (this.data.ticket_group_client[index].client_id == id) {
          this.data.ticket_group_client.splice(index, 1);
        }
      }
    },
    btnUpdate() {
      this.data.ticketType = "3";
      this.data.updated_by = this.user.name;
      this.data.attachments = this.data.attachments;

      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this Ticket?",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true
          }).then(update => {
            if (update) {
              this.$http
                .put("api/Ticket/" + this.data.id, this.data)
                .then(response => {
                  console.log(response.body);
                  // this.$root.$emit(
                  //   "update_ticket_list",
                  //   response.body,
                  //   this.data.cbFilter
                  // );
                });
            }
          });
        }
      });
    }
  }
};
</script>
<style scoped>
body {
  font-family: Raleway;
}
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

.b-card-ticket {
  max-height: 100%;
  border: 1px solid #ced4da;
}
.trouble-bcard > .card-body {
  padding: 10px;
  padding-left: 13px;
  max-width: 100%;
  background: #fff;
  border: none;
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
.tsCheckboxes {
  font-size: 11px;
  margin-top: 7px;
  margin-right: 14px;
}

.troubleCheckbox {
  display: flex;
  width: 17%;
  float: left;
  margin-top: 7px;
  font-size: 11px;
}
.usageLine {
  border: none;
  border-bottom: 1px solid black;
  margin-left: 0;
  width: 20%;
  box-shadow: none;
  border-radius: 0 0 0 0;
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

.ticketNo {
  display: block;
  text-align: left;
  font-weight: bold;
  color: green;
  font-size: 14px;
}
.textbtnedit {
  position: absolute;
  left: 85%;
  color: cornflowerblue;
  cursor: pointer;
}
.textbtnedit:hover {
  text-decoration: underline;
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
.group-clients {
  overflow-y: scroll;
}
.group-clients::-webkit-scrollbar {
  width: 8px;
  background-color: #f5f5f5;
}

.group-clients::-webkit-scrollbar-thumb {
  border-radius: 10px;
  /* -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1); */
  background-color: #068d0f;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
