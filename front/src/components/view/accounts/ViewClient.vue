<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          <label v-if="!deletedAcc">Accounts</label>
          <label v-else>Deleted Accounts</label>

          <button
            @click="openModalAdd"
            v-if="roles.create_client"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-show="!deletedAcc"
          >
            <i class="fas fa-plus"></i> Create Account
          </button>

          <button
            v-if="roles.admin"
            @click="exportCSVclient"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-show="!deletedAcc"
          >
            <i class></i> Export Client
          </button>

          <button
            v-if="roles.admin"
            @click="exportCSVbill"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-show="!deletedAcc"
          >
            <i class></i> Export Bill
          </button>
        </p>
      </div>
      <div class="elClr panel-body">
        <!-- filter panel -->
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
                      class="searchForm"
                      v-model="tblFilter_copy"
                      v-on:keyup.enter="search_data"
                      placeholder="Search"
                    ></b-form-input>

                    <b-input-group-append>
                      <b-button @click="clearFilter" class="clearBtn"
                        >Clear</b-button
                      >
                    </b-input-group-append>
                    <button
                      @click="fnExcelReport('accountsTable')"
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
            <b-row class="buttonRow" v-show="!deletedAcc">
              <button
                @click="filterClick('dop')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                No DOP
                <b-badge v-if="totalNoDOP > 0" variant="info">
                  {{ totalNoDOP }}
                </b-badge>
              </button>

              <button
                @click="filterClick('contract')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                No Contract
                <b-badge v-if="totalNoContract > 0" variant="info">
                  {{ totalNoContract }}
                </b-badge>
              </button>
              <button
                v-if="roles.helpdesk"
                @click="filterClick('wfs')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
                style="width: 80px"
              >
                WFS
                <b-badge v-if="totalWFS > 0" variant="info">
                  {{ totalWFS }}
                </b-badge>
              </button>

              <button
                @click="filterClick('wfc')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
                style="width: 80px"
              >
                WFC
                <b-badge v-if="totalWFC > 0" variant="info">
                  {{ totalWFC }}
                </b-badge>
              </button>

              <button
                @click="filterClick('expired')"
                type="button"
                class="
                  btn btn-danger btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
              >
                Expired
                <b-badge v-if="totalExpired > 0" variant="info">
                  {{ totalExpired }}
                </b-badge>
              </button>

              <button
                @click="filterClick('cease')"
                type="button"
                class="
                  btn btn-warning btn-labeled
                  pull-right
                  margin-left-10
                  statusBtn
                "
                style="width: 85px"
              >
                Cease
                <b-badge v-if="totalNearToExpire > 0" variant="info">
                  {{ totalNearToExpire }}
                </b-badge>
              </button>
              <button
                v-b-modal="'modalMultipleFilterClient'"
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
            </b-row>
          </div>
        </div>
        <br />
        <!-- end of filter panel -->

        <div class="breadcrumb flat" style="display: none">
          <a href="#" class="active">Client Entry</a>
          <a href="#">For Billing</a>
          <a href="#">Account Management</a>
          <a href="#">Ready for Installation</a>
          <a href="#">Ready for Installation</a>
        </div>
        <!-- body -->
        <div id="dataPanel">
          <!-- switch -->
          <div style="display: flex">
            <div
              class="row marginice"
              style="margin-left: 1px; float: left; flex: 6"
            >
              <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
            </div>
            <div class="row marginice" style="flex: 1">
              <b-row>
                <b-col style="float: right; display: flex">
                  <span v-if="roles.rm" class="span-btn-group">
                    <button
                      class="btn btn-labeled box-deleted"
                      v-b-tooltip.hover
                      title="Show Deleted Clients"
                      v-if="!deletedAcc"
                      @click="showDeleted"
                    >
                      <i class="fas fa-trash-restore-alt"></i>
                    </button>
                    <button
                      class="btn btn-labeled box-current"
                      v-b-tooltip.hover
                      v-else
                      title="Show Clients"
                      @click="reload_data"
                    >
                      <i class="fas fa-trash-restore-alt"></i>
                    </button>
                  </span>

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
                  <b-form-group class="mb-0" style="flex: 2">
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
          <!-- table -->
          <div
            class="scrollmenu"
            id="scrollmenuContainer"
            @mousemove="mousemove"
          >
            <b-table
              id="accountsTable"
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
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
              <template slot="table-caption"></template>

              <span slot="status" slot-scope="data" v-html="data.value"></span>

              <template v-slot:cell(status)="row">
                <b-button
                  class="btn-status"
                  variant="success"
                  v-if="row.item.status == 'Active'"
                  size="sm"
                  :disabled="true"
                  >Active</b-button
                >
                <b-button
                  class="btn-status"
                  variant="dark"
                  v-if="row.item.status == 'Disconnected'"
                  size="sm"
                  :disabled="true"
                  >Disconnected</b-button
                >
                <b-button
                  class="btn-status"
                  variant="warning"
                  v-if="row.item.status == 'Temp Discon'"
                  size="sm"
                  :disabled="true"
                  >Temp Disconn</b-button
                >
                <b-button
                  class="btn-status"
                  variant="danger"
                  v-if="row.item.status == 'Cancelled'"
                  size="sm"
                  :disabled="true"
                  >Cancelled</b-button
                >
              </template>

              <template v-slot:cell(sched)="row">
                <b-button
                  variant="info"
                  @click="openModalSched(row.item)"
                  size="sm"
                  >schedule</b-button
                >
              </template>

              <template v-slot:cell(action)="row">
                <div class="action-col">
                  <b-button
                    v-if="user.id == 1 && row.item.autoBill == 'No'"
                    variant="info"
                    size="sm"
                    @click="autoBill(row.item.id)"
                    >Automate Bill</b-button
                  >
                  <b-button
                    v-if="user.id == 1 && row.item.autoBill == 'Yes'"
                    variant="danger"
                    size="sm"
                    @click="stopAutoBill(row.item.id)"
                    >Stop Auto-Bill</b-button
                  >
                  <b-button
                    v-if="user.id == 1"
                    variant="warning"
                    @click="verifiedClient(row.item)"
                    size="sm"
                    >Verify</b-button
                  >

                  <b-button
                    v-if="
                      roles.account_management && row.item.status == 'Active'
                    "
                    variant="warning"
                    @click="RequestActivity(row.item, 'Change Package')"
                    size="sm"
                    >CP</b-button
                  >

                  <b-button
                    v-if="
                      (roles.accounting || roles.account_management) &&
                      row.item.status != 'Temp Discon'
                    "
                    variant="warning"
                    @click="RequestActivity(row.item, 'Temp Discon')"
                    size="sm"
                    >TD</b-button
                  >

                  <b-button
                    v-if="
                      (roles.accounting || roles.account_management) &&
                      row.item.status != 'Active'
                    "
                    variant="info"
                    @click="RequestActivity(row.item, 'Activate')"
                    size="sm"
                    >Re-Activate</b-button
                  >

                  <b-button
                    v-if="
                      (roles.accounting || roles.account_management) &&
                      row.item.status != 'Disconnected'
                    "
                    variant="danger"
                    @click="RequestActivity(row.item, 'Discon')"
                    size="sm"
                    >Discon</b-button
                  >

                  <b-button
                    v-if="
                      row.item.aging == null &&
                      roles.accounting &&
                      row.item.client_detail != null
                    "
                    variant="success"
                    @click="openModalDOP(row.item)"
                    size="sm"
                    >update DOP</b-button
                  >

                  <b-button
                    v-if="
                      (row.item.aging != null && roles.receive_payment) ||
                      (roles.receive_payment && row.item.client_detail == null)
                    "
                    variant="success"
                    @click="openModalReceivePayment(row.item)"
                    size="sm"
                    >RP</b-button
                  >

                  <b-button
                    v-if="
                      (row.item.aging != null && roles.create_wht) ||
                      (roles.receive_payment && row.item.client_detail == null)
                    "
                    variant="success"
                    @click="openModalWHT(row.item)"
                    size="sm"
                    >WHT</b-button
                  >

                  <b-button
                    v-if="
                      row.item.contract_status == false &&
                      roles.account_management
                    "
                    variant="success"
                    @click="updateContract(row.item)"
                    size="sm"
                    >Contract</b-button
                  >
                </div>
              </template>
            </b-table>
          </div>
          <!-- footer -->

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

      <!-- modalDOP ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalDOP"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Update Account"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Send To:</p>
          </div>
          <div class="col-lg-9 multiselectemail">
            <multiselect
              v-model="selectedEmails_DOP"
              :options="Emails"
              label="name"
              track-by="email"
              variant="primary"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>

        <!-- CC To -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">CC To:</p>
          </div>
          <div class="col-lg-9">
            <multiselect
              v-model="selectedEmailsCC_DOP"
              :options="Emails"
              label="name"
              track-by="email"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account #:(leave empty to auto set)</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="account_no"
              class="form-control"
              v-b-tooltip.hover
              title="Input the account number"
              placeholder="Account Number"
              v-model="editClient.account_no"
            />
          </div>
        </div>-->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With VAT:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              @change="onChangeWithVat"
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="withVat"
            ></p-check>
            {{ withVat }}
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3" v-if="withVat">
            <p class="textLabel">OTC + (MRR / 1.12) = FP:</p>
          </div>
          <div class="col-lg-3" v-else>
            <p class="textLabel">OTC + MRR = FP:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">
              {{ editClient.OTC }} + {{ editClient.cashBond }} =
              {{ editClient.fp }}
            </p>
          </div>
        </div>
        <!-- Payment -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Payment:</p>
          </div>
          <div class="col-lg-9">
            <p-radio
              class="textLabel p-default p-curve"
              v-model="editClient.staggered"
              value="0"
              name="RDstaggered"
              color="success-o"
              >Full</p-radio
            >

            <p-radio
              class="textLabel p-default p-curve"
              v-model="editClient.staggered"
              value="1"
              name="RDstaggered"
              color="success-o"
              >Staggered</p-radio
            >
          </div>
        </div>
        <!-- Month to pay -->
        <div class="rowFields mx-auto row" v-if="editClient.staggered == '1'">
          <div class="col-lg-3">
            <p class="textLabel">Months to pay:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              max="24"
              min="1"
              class="form-control"
              v-b-tooltip.hover
              title="number of months to pay"
              placeholder="Months to pay"
              v-model="editClient.numMonths"
            />
          </div>
        </div>
        <!-- // or num -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">OR Number #:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input OR number"
              placeholder="O.R. Number/Ref. number."
              v-model="editClient.or_number"
            />
          </div>
        </div>
        <!-- Payment Method -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Payment Method:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="pay_method"
              v-model="pay_method_Selected"
              option-value="id"
              option-text="name"
              placeholder="Select Payment Method..."
              @input="onChangeSelectPayMethod"
            ></model-list-select>
          </div>
        </div>

        <div v-if="editClient.isPayOnline">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Bank Code:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="bank_code"
                :custom-text="getBankCode"
                v-model="bank_code_selected"
                @input="onChangeBankCode"
                option-value="id"
                option-text="name"
                placeholder="Select Bank Code(code - date - amount)..."
              ></model-list-select>
            </div>
          </div>
          <!-- Date of payment -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date of Payment:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">{{ bank_code_selected.date }}</div>
            </div>
          </div>
          <!-- Amount -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Amount Pay:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">{{ editClient.amount_pay }}</div>
            </div>
          </div>
        </div>
        <!-- //
        //
        //
        //
        //-->
        <div v-else>
          <!-- Date of payment -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date of Payment:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="editClient.billing_date"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <!-- Amount -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Amount Pay:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                placeholder="Amount Pay"
                v-model="editClient.amount_pay"
              />
            </div>
          </div>
        </div>

        <!-- OTCPay -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3" v-if="editClient.package_type_name != 'RES'">
            <p class="textLabel">AmountPay - (MRR / 1.12)= OTCPay:</p>
          </div>
          <div class="col-lg-3" v-else>
            <p class="textLabel">AmountPay - MRR= OTC Pay:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              {{ editClient.amount_pay }} - {{ editClient.cashBond }} =
              {{ OTCPay }}
            </div>
          </div>
        </div>

        <!-- Balance -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">OTC - OTCPay = Balance:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              {{ editClient.OTC }} - {{ OTCPay }} = {{ balance }}
            </div>
          </div>
        </div>

        <!-- OTC per month -->
        <div class="rowFields mx-auto row" v-if="editClient.staggered == '1'">
          <div class="col-lg-3">
            <p class="textLabel">OTC per month({{ editClient.numMonths }}):</p>
          </div>
          <div class="col-lg-9">
            {{ balance }} / {{ editClient.numMonths }} = {{ permonth }}
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks/Memo:</p>
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
              placeholder="Remarks/Memo"
              v-model.lazy="editClient.remarks"
            ></textarea>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks/Note(EMAIL):</p>
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
              placeholder="Remarks/Note to your email"
              v-model.lazy="editClient.email_note"
            ></textarea>
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="updateDOP()"
            >Update</b-button
          >
        </template>
      </b-modal>
      <!-- End modalDOP -->

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
      >
        <div slot="modal-header" style="width: 100%">
          <h5 style="float: left">Manage Client</h5>
          <p style="float: right">
            <i
              class="fas fa-chevron-right"
              style="font-size: 22px; color: silver"
            ></i>
          </p>
          <!-- <div class="rowFields mx-auto row">
            <div class="col-lg-6">
              <h5>Manage Client</h5>
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="client.isNew"
                value="0"
                name="isNew"
                color="success-o"
                >New</p-radio
              >
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="client.isNew"
                value="1"
                name="isNew"
                color="success-o"
                >Existing</p-radio
              >
            </div>
          </div> -->
        </div>

        <!-- <div class="rowFields mx-auto row" v-if="updateClientList.re_email">
          <div class="col-lg-3">
            <p class>Re-Email:</p>
          </div>
          <div class="col-lg-9">
            <p-check class="p-switch p-fill" color="success" v-model="editClient.re_email"></p-check>
          </div>
        </div>-->

        <!-- Send To -->
        <!-- <div class="rowFields mx-auto row" v-if="editClient.re_email">
          <div class="col-lg-3">
            <p class>Send To:</p>
          </div>
          <div class="col-lg-9 multiselectemail">
            <multiselect
              v-model="selectedEmails"
              :options="Emails"
              label="name"
              track-by="email"
              variant="primary"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>-->

        <!-- CC To -->
        <!-- <div class="rowFields mx-auto row" v-if="editClient.re_email">
          <div class="col-lg-3">
            <p class>CC To:</p>
          </div>
          <div class="col-lg-9">
            <multiselect
              v-model="selectedEmailsCC"
              :options="Emails"
              label="name"
              track-by="email"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>-->
        <!-- Account No -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Account No:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="account_no"
              class="form-control"
              v-b-tooltip.hover
              title="Input the account no"
              placeholder="Account Number"
              v-on:keyup.enter="
                updateClientPerRow('account_no', $event.target.value),
                  (editPerRow.account_no = false)
              "
              v-model.lazy="editClient.account_no"
              v-if="editPerRow.account_no"
            />
            <span v-else>
              {{ editClient.account_no }}
              <span
                class="buttonEdit"
                v-if="user.id == 1"
                @click="editPerRow.account_no = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Account Name -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label>Account Name:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the account name"
              placeholder="Account name"
              v-on:keyup.enter="
                updateClientPerRow('name', $event.target.value),
                  (editPerRow.name = false)
              "
              v-model.lazy="editClient.name"
              v-validate="'required'"
              v-if="editPerRow.name"
            />
            <span v-else>
              {{ editClient.name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.name"
                @click="editPerRow.name = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Owner's Name -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Owner's Name:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="owner_name"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the owner's name"
              placeholder="Owner's name"
              v-model.lazy="editClient.owner_name"
              v-on:keyup.enter="
                updateClientPerRow('owner_name', $event.target.value),
                  (editPerRow.owner_name = false)
              "
              v-if="editPerRow.owner_name"
            />
            <span v-else>
              {{ editClient.owner_name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.owner_name"
                @click="editPerRow.owner_name = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Contact person -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Contact person:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="contact_person"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Contact person"
              placeholder="Contact person"
              v-model.lazy="editClient.contact_person"
              v-on:keyup.enter="
                updateClientPerRow('contact_person', $event.target.value),
                  (editPerRow.contact_person = false)
              "
              v-if="editPerRow.contact_person"
            />
            <span v-else>
              {{ editClient.contact_person }}
              <span
                class="buttonEdit"
                v-if="updateClientList.contact_person"
                @click="editPerRow.contact_person = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- contact number -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Contact number:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="contact1"
              class="form-control"
              v-b-tooltip.hover
              title="For multiple contact number it must be separated by comma
              followed by space and must be 11digits per number E.g.(09123456789, 09987654321)"
              placeholder="Contact number"
              v-model.lazy="editClient.contact"
              v-on:keyup.enter="
                updateClientPerRow('contact', $event.target.value),
                  (editPerRow.contact = false)
              "
              v-if="editPerRow.contact"
            />
            <span v-else>
              {{ editClient.contact }}
              <span
                class="buttonEdit"
                v-if="updateClientList.contact"
                @click="editPerRow.contact = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- email address -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Email address:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="Email_address"
              class="form-control"
              v-b-tooltip.hover
              title="For multiple Email address it must be separated by comma
              followed by space E.g.(test@test.com, test2@test.com)"
              placeholder="Email address"
              v-model.lazy="editClient.email_add"
              v-on:keyup.enter="
                updateClientPerRow('email_add', $event.target.value),
                  (editPerRow.email_add = false)
              "
              v-if="editPerRow.email_add"
            />
            <span v-else>
              {{ editClient.email_add }}
              <span
                class="buttonEdit"
                v-if="updateClientList.email_add"
                @click="editPerRow.email_add = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Address -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Address:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="location"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the address"
              placeholder="Address"
              v-model.lazy="editClient.location"
              v-on:keyup.enter="
                updateClientPerRow('location', $event.target.value),
                  (editPerRow.location = false)
              "
              v-if="editPerRow.location"
            />
            <span v-else>
              {{ editClient.location }}
              <span
                class="buttonEdit"
                v-if="updateClientList.location"
                @click="editPerRow.location = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Package -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Package:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packages"
              v-model="packEdit"
              option-value="id"
              option-text="name"
              placeholder="Select a package code..."
              @input="onChangeEditclient()"
              v-if="editPerRow.package"
            ></model-list-select>
            <span v-else>
              {{ editClient.package.name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.package"
                @click="editPerRow.package = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Max Speed -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Max Speed:</label>
          </div>
          <div class="col-lg-9">
            {{ editClient.package.max_speed }}
            <!-- <input
              type="text"
              class="form-control"
              placeholder="Max Speed"
              v-model.lazy="packEdit.max_speed"
              disabled
              v-if="updateClientList.package"
            />
            <label class="" v-else>{{editClient.package.max_speed}}</label>-->
          </div>
        </div>
        <!-- CIR -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>CIR:</label>
          </div>
          <div class="col-lg-9">{{ editClient.package.cir }}</div>
        </div>
        <!-- MRR -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>MRR:</label>
          </div>
          <div class="col-lg-9">{{ editClient.package.mrr }}</div>
        </div>
        <!-- Package type -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Package type:</label>
          </div>
          <div class="col-lg-9">
            {{ editClient.package_type.name }}
            <!-- <model-list-select
              :list="packageTypes"
              v-model="editClient.package_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
              v-on:keyup.enter="updateClient"
              isDisabled
              v-if="updateClientList.package"
            ></model-list-select>

            <label class="" v-else>{{editClient.package_type.name}}</label>-->
          </div>
        </div>
        <!-- OTC -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>OTC:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="OTC"
              class="form-control"
              v-b-tooltip.hover
              title="Input the OTC"
              placeholder="OTC"
              v-model.lazy="editClient.OTC"
              v-on:keyup.enter="
                updateClientPerRow('OTC', $event.target.value),
                  (editPerRow.OTC = false)
              "
              v-if="editPerRow.OTC"
            />
            <span v-else>
              {{ editClient.OTC }}
              <span
                class="buttonEdit"
                v-if="updateClientList.OTC"
                @click="editPerRow.OTC = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Term -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Term:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="Term (number of months)"
              v-model.lazy="editClient.term"
              v-on:keyup.enter="
                updateClientPerRow('term', $event.target.value),
                  (editPerRow.term = false)
              "
              v-if="editPerRow.term"
            />
            <span v-else>
              {{ editClient.term }}
              <span
                class="buttonEdit"
                v-if="updateClientList.term"
                @click="editPerRow.term = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Sales in charge -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Sales in charge:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales"
              v-model="editClient._sales"
              option-value="id"
              option-text="name"
              placeholder="Select a seller's name..."
              @input="
                updateClientPerRow('sales_id', editClient._sales),
                  (editPerRow.sales = false)
              "
              v-if="editPerRow.sales"
            ></model-list-select>
            <span v-else>
              {{ editClient.sales.user.name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.sales"
                @click="editPerRow.sales = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Sales Agent -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class>Sales Agent:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales_agent"
              v-model="editClient.sales_agent"
              name="sale_agent"
              option-value="id"
              option-text="name"
              placeholder="Select sales agent if this client deal by agent..."
              @input="
                updateClientPerRow('sales_agent_id', editClient.sales_agent),
                  (editPerRow.agent = false)
              "
              v-if="editPerRow.agent"
            ></model-list-select>
            <span v-else>
              {{ editClient.sales_agent.name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.agent"
                @click="editPerRow.agent = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Referral -->
       <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Referred By:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="referral"
              class="form-control"
              v-b-tooltip.hover
              title="Input the referral"
              placeholder="Name of  the referral"
              v-model.lazy="editClient.referral"
              v-on:keyup.enter="
                updateClientPerRow('referral', $event.target.value),
                  (editPerRow.referral = false)
              "
              v-if="editPerRow.referral"
            />
            <span v-else>
              {{ editClient.referral }}
              <span
                class="buttonEdit"
                v-if="updateClientList.referral"
                @click="editPerRow.referral = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>

        <!-- Tech Sales In-Charge -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Tech Sales In-Charge:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="techsales"
              v-model="editClient.tech"
              option-value="id"
              option-text="name"
              placeholder="Select a engineer's name..."
              @input="
                updateClientPerRow('engineers_id', editClient.tech),
                  (editPerRow.engineers = false)
              "
              v-if="editPerRow.engineers"
            ></model-list-select>
            <span v-else>
              {{ editClient.engineer.name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.engineers"
                @click="editPerRow.engineers = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Branch -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Branch:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="editBranchSelected"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a Branch..."
              @input="
                updateClientPerRow('branch', editBranchSelected),
                  (editPerRow.branch = false)
              "
              v-if="editPerRow.branch"
            ></model-list-select>
            <span v-else>
              <span v-if="editClient.branch != null">{{
                editClient.branch.name
              }}</span>
              <span
                class="buttonEdit"
                v-if="updateClientList.branch"
                @click="editPerRow.branch = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Area -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Area:</label>
          </div>
          <div class="col-lg-9">
            <span v-if="editClient.branch != null">{{
              editClient.branch.area.name
            }}</span>
          </div>
        </div>
        <!-- Region -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Region:</label>
          </div>
          <div class="col-lg-9">
            <span v-if="editClient.branch != null">{{
              editClient.branch.area.region.name
            }}</span>
          </div>
        </div>
        <!-- Bucket -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Bucket:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="buckets"
              v-model="editClient.bucket"
              :custom-text="getBucketDesc"
              option-value="id"
              option-text="name"
              name="bucket"
              placeholder="Select/Search a Bucket..."
              @input="
                updateClientPerRow('bucket_id', editClient.bucket.id),
                  (editPerRow.bucket = false)
              "
              v-if="editPerRow.bucket"
            ></model-list-select>
            <span v-else>
              {{ editClient.bucket.IP }}
              <span
                class="buttonEdit"
                v-if="updateClientList.bucket"
                @click="editPerRow.bucket = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Subscription Name -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Subscription Name:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="subscription_name"
              class="form-control"
              v-b-tooltip.hover
              title="subscription name in the bucket"
              placeholder="subscription name"
              v-model.lazy="editClient.subscription_name"
              v-on:keyup.enter="
                updateClientPerRow('subscription_name', $event.target.value),
                  (editPerRow.subscription = false)
              "
              v-if="editPerRow.subscription"
            />
            <span v-else>
              {{ editClient.subscription_name }}
              <span
                class="buttonEdit"
                v-if="updateClientList.subscription"
                @click="editPerRow.subscription = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>
        <!-- Status -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Status:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="statusOption"
              v-model="editClient.status"
              option-value="name"
              option-text="name"
              placeholder="Select status"
              @input="
                updateClientPerRow('status', editClient.status),
                  (editPerRow.status = false)
              "
              v-if="editPerRow.status"
            ></model-list-select>
            <span v-else>
              {{ editClient.status }}
              <span
                class="buttonEdit"
                v-if="updateClientList.status"
                @click="editPerRow.status = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
              <span
                class="pull-right mr-3 btn-history buttonEdit"
                v-if="updateClientList.status"
                v-b-toggle.history-collapse
              >
                <i class="fas fa-history"></i> History
              </span>
            </span>
            <b-collapse id="history-collapse">
              <b-card>
                <div>
                  <table class="table table-condensed table-hover">
                    <thead>
                      <tr>
                        <th>Status</th>
                        <th>Date Changed</th>
                        <th>Changed By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="status in editClient.status_log"
                        :key="status.id"
                      >
                        <td>{{ status.status }}</td>
                        <td>{{ status.date_change }}</td>
                        <td>{{ status.user.name }}</td>
                        <td>{{ status.created_at }}</td>
                        <td>{{ status.updated_at }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </b-card>
            </b-collapse>
          </div>
        </div>
        <!-- Date Activated -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Date Activated:</label>
          </div>
          <div class="col-lg-9">{{ editClient.date_activated }}</div>
        </div>
        <!-- Remarks / note -->
        <div class="rowFields mx-auto row stripe-modi">
          <div class="col-lg-3">
            <label class>Remarks/Note:</label>
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
              placeholder="Remarks/Note"
              v-model.lazy="editClient.remarks"
              @keydown.enter.exact.prevent
              @keyup.enter.exact="
                updateClientPerRow('remarks', $event.target.value),
                  (editPerRow.remarks = false)
              "
              v-if="editPerRow.remarks"
            ></textarea>
            <span v-else>
              <pre>{{ editClient.remarks }}</pre>
              <span
                class="buttonEdit"
                v-if="updateClientList.remarks"
                @click="editPerRow.remarks = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="editClient.client_detail != null"
        >
          <div class="col-lg-3">
            <label class>Wait for client confirmation:</label>
          </div>
          <div class="col-lg-9">
            <span v-if="editPerRow.wfc">
              <p-check
                class="p-switch p-fill"
                color="success"
                v-model="editClient.wfc"
                @change="
                  updateClientDetailsPerRow('wfc', editClient.wfc),
                    (editPerRow.wfc = false)
                "
              >
                <i class="fas fa-check" v-show="editClient.wfc" />
                <i class="fas fa-times" v-show="!editClient.wfc" />
              </p-check>
            </span>
            <span v-else>
              <i class="fas fa-check" v-show="editClient.wfc" />
              <i class="fas fa-times" v-show="!editClient.wfc" />
              <span
                class="buttonEdit"
                v-if="
                  updateClientList.wfc &&
                  editClient.client_detail.status != 'finished'
                "
                @click="editPerRow.wfc = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-2">
            <label class="">Latitude:</label>
          </div>
          <div class="col-lg-4">
            <input
              type="number"
              class="form-control"
              v-model="editClient.lat"
              v-on:keyup.enter="updateMarker_edit('mm')"
            />
          </div>
          <div class="col-lg-2">
            <label class="">Longitude:</label>
          </div>
          <div class="col-lg-4">
            <input
              type="number"
              class="form-control"
              v-model="editClient.lng"
              v-on:keyup.enter="updateMarker_edit('mm')"
            />
          </div>
        </div>-->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <label class>Coordinates (lat,lng):</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model="latlng"
              v-on:keyup.enter="
                updateClientPerRow('coordinates', $event.target.value),
                  (editPerRow.coordinates = false)
              "
              v-if="editPerRow.coordinates"
            />
            <span v-else>
              {{ editClient.lat }}, {{ editClient.lng }}
              <span
                class="buttonEdit"
                v-if="updateClientList.coordinates"
                @click="editPerRow.coordinates = true"
              >
                <i class="fas fa-pen"></i> Edit
              </span>
            </span>
          </div>
          <!-- <div class="col-lg-2">
            <label class="">(lng,lat):</label>
          </div>
          <div class="col-lg-4">
            <input
              type="text"
              class="form-control"
              v-model="lnglat"
              v-on:keyup.enter="updateMarker_edit('lnglat')"
            />
          </div>-->
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-1"></div>
          <div class="col-lg-10">
            <GmapMap
              ref="gmapclient"
              :center="coordinates"
              :zoom="17"
              map-type-id="satellite"
              style="width: 100%; height: 500px"
            >
              <GmapMarker
                :key="index"
                v-for="(m, index) in markers_edit"
                :position.sync="m.position"
                :clickable="true"
                :draggable="true"
                :animation="2"
                :label="m.label"
                @dragend="updateCoordinates_edit($event.latLng, index)"
              />
            </GmapMap>
          </div>
          <div class="col-lg-1"></div>
        </div>-->

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
              <div
                style="margin-top: 20px"
                v-for="(remarks, index) in editClient.remarks_log"
                :key="index"
                v-show="remarks.form_type == 'client'"
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
            size="sm"
            variant="warning"
            v-if="roles.account_management && !deletedAcc"
            v-b-modal="'modalAddAttachment'"
            >Attach File</b-button
          >

          <b-button
            size="sm"
            variant="warning"
            v-if="roles.rm && editClient.date_activated != null && !deletedAcc"
            v-b-modal="'modalChangeDateActivated'"
            >Change Date Activated</b-button
          >
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_client && editClient.re_email && !deletedAcc"
            @click="updateClient()"
            >Update and Send(Email)</b-button
          >
          <!-- <b-button
            size="sm"
            variant="success"
            v-if="roles.update_client && !editClient.re_email && !deletedAcc"
            @click="updateClient()"
          >Update</b-button>-->
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_client && !deletedAcc"
            @click="cancelClient()"
            >Delete</b-button
          >
          <!-- <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_client"
            @click="deleteClient()"
          >Delete</b-button>-->

          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_client"
            :hidden="deletedAcc == false"
            @click="retrieveClient()"
          >
            <i class="fas fa-sync-alt"></i>Retrieve
          </b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!--modalAdd---------------------------------------------------------------------->
      <b-modal
        id="modalAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
      >
        <div slot="modal-header">
          <div class="rowFields mx-auto row">
            <div class="col-lg-6">
              <h5>Accounts Form</h5>
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="client.isNew"
                value="0"
                name="isNew"
                color="success-o"
                >New</p-radio
              >
            </div>
            <div class="col-lg-2">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="client.isNew"
                value="1"
                name="isNew"
                color="success-o"
                >Existing</p-radio
              >
            </div>
          </div>
        </div>
        <!--Form-------->

        <!-- Send To -->
        <!-- <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
          <div class="col-lg-3">
            <p class="textLabel">Send To:</p>
          </div>
          <div class="col-lg-9 multiselectemail">
            <multiselect
              v-model="selectedEmails"
              :options="Emails"
              label="name"
              track-by="email"
              variant="primary"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>-->

        <!-- CC To -->
        <!-- <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
          <div class="col-lg-3">
            <p class="textLabel">CC To:</p>
          </div>
          <div class="col-lg-9">
            <multiselect
              v-model="selectedEmailsCC"
              :options="Emails"
              label="name"
              track-by="email"
              multiple
              open-direction="bottom"
              placeholder="Type to search"
              :hide-selected="true"
              :clear-on-select="false"
              :close-on-select="false"
            ></multiselect>
          </div>
        </div>-->

        <!-- Account # -->
        <!-- <div class="rowFields mx-auto row" v-if="client.isNew != '0'">
          <div class="col-lg-3">
            <p class="textLabel">Account #(from QB):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="account_no"
              class="form-control"
              v-b-tooltip.hover
              title="Input the account number"
              placeholder="Account Number"
              v-model.lazy="client.account_no"
            />
          </div>
        </div>-->

        <!-- Account Name -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the account name"
              placeholder="Name of Client"
              v-model.lazy="client.name"
              v-validate="'required'"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Account name is required.</small
            >
          </div>
        </div>
        <!-- Owner's Name -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Owner's Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="owner_name"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the owner's name"
              placeholder="Owner's name"
              v-model.lazy="client.owner_name"
              v-validate="'required'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('owner_name')"
              >Owner's name is required.</small
            >
          </div>
        </div>
        <!-- business_type -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Business Type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="business_types"
              v-model="client.business_type"
              option-value="name"
              option-text="name"
              placeholder="Select Business Type..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Contact person:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="contact_person"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Contact person"
              placeholder="Contact person"
              v-model.lazy="client.contact_person"
            />
          </div>
        </div>
        <!-- Contact number -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">
              Contact number(cellphone):
              <b-button
                @click="addContactField"
                type="button"
                v-b-tooltip.hover
                title="Add contact"
                class="btn btn-success btn-labeled"
              >
                <i class="fas fa-plus-square"></i>
              </b-button>
            </p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="contact"
              class="form-control"
              v-b-tooltip.hover
              title="Input Cellphone number of the client
              (The system will use this number to text the client)"
              placeholder="Contact number"
              v-model="client.contact"
            />
            <small class="text-danger pull-left" v-show="errors.has('contact')"
              >contact is required and contains exactly 11 digits .</small
            >
          </div>
        </div>
        <div v-if="client_contacts.length > 0">
          <div
            class="rowFields mx-auto row"
            v-for="(item, index) in client_contacts"
            :key="index"
          >
            <div class="col-lg-3">
              <b-button
                @click="removeContactField(index)"
                type="button"
                v-b-tooltip.hover
                title="Remove Contact Field"
                class="btn btn-success btn-labeled pull-right margin-right-10"
              >
                <i class="fas fa-times" />
              </b-button>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                :name="'contact[' + index + ']'"
                class="form-control"
                v-b-tooltip.hover
                title="Input Cellphone number of the client
              (The system will use this number to text the client)"
                placeholder="Contact number"
                v-model="item.contact"
              />
              <small
                class="text-danger pull-left"
                v-show="errors.has('contact[' + index + ']')"
                >contact is required and contains exactly 11 digits .</small
              >
            </div>
          </div>
        </div>
        <!-- Email Address -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">
              Email address:
              <b-button
                @click="addEmailField"
                type="button"
                v-b-tooltip.hover
                title="Add Email address"
                class="btn btn-success btn-labeled"
              >
                <i class="fas fa-plus-square"></i>
              </b-button>
            </p>
          </div>
          <div class="col-lg-9">
            <input
              type="email"
              name="Email_address"
              class="form-control"
              v-b-tooltip.hover
              title="Input Email address (The system will use this email address to email the client)"
              placeholder="Email address"
              v-model.lazy="client.email_add"
              v-validate="{ required: true, email: true }"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('Email_address')"
              >Email address is required.</small
            >
          </div>
        </div>
        <div v-if="client_emailadresses.length > 0">
          <div
            class="rowFields mx-auto row"
            v-for="(item, index) in client_emailadresses"
            :key="index"
          >
            <div class="col-lg-3">
              <b-button
                @click="removeEmailField(index)"
                type="button"
                v-b-tooltip.hover
                title="Remove Email Field"
                class="btn btn-success btn-labeled pull-right margin-right-10"
              >
                <i class="fas fa-times" />
              </b-button>
            </div>
            <div class="col-lg-9">
              <input
                type="email"
                :name="'client_emailadresses[' + index + ']'"
                class="form-control"
                v-b-tooltip.hover
                title="Input Email address (The system will use this email address to email the client)"
                placeholder="Email address"
                v-model="item.email"
                v-validate="{ required: true, email: true }"
              />
              <small
                class="text-danger pull-left"
                v-show="errors.has('client_emailadresses[' + index + ']')"
                >Email address is required.</small
              >
            </div>
          </div>
        </div>
        <!-- Address -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Address:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="location"
              class="form-control text-upper"
              v-b-tooltip.hover
              title="Input the address"
              placeholder="Address"
              v-model.lazy="client.location"
              v-validate="'required'"
            />
            <small class="text-danger pull-left" v-show="errors.has('location')"
              >Address is required.</small
            >
          </div>
        </div>
        <!-- Date of Payment -->
        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Date of Payment:</p>
          </div>
          <div class="col-lg-9">
            <!-- <date-picker
              @dp-change="inputDatePrevOnly(client.aging)"
              name="dop"
              placeholder="Date of Payment"
              v-b-tooltip.hover
              title="Select Date of Payment"
              v-model.lazy="client.aging"
              :config="AppliedDateoptions"
              autocomplete="off"
              v-validate="'required'"
            ></date-picker>
            <small
              class="text-danger pull-left"
              v-show="errors.has('dop')"
            >Date of Payment is required.</small>-->
          </div>
        </div>
        <!-- Package -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packages"
              v-model="packAdd"
              option-value="id"
              option-text="name"
              placeholder="Select a package code..."
              @input="onChangeAddclient"
            ></model-list-select>
          </div>
        </div>
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Max Speed:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="Max Speed"
              v-model.lazy="packAdd.max_speed"
              disabled
            />
          </div>
        </div>
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">CIR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="CIR"
              v-model.lazy="packAdd.cir"
              disabled
            />
          </div>
        </div>
        <!-- MRR -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">MRR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="MRR"
              v-model.lazy="packAdd.mrr"
              disabled
            />
          </div>
        </div>
        <!-- Package type -->
        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Package type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packageTypes"
              v-model="client.package_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
              isDisabled
            ></model-list-select>
          </div>
        </div>
        <!-- Length -->
        <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
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
              placeholder="Length in meter"
              @input="calculateOTC"
              v-model.trim="client.foc_length"
              v-on:keyup.enter="btnCreate"
              v-validate="'required|between:1,9009'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('foc_length')"
              >Length is required and must be a number from 0 to 2000.</small
            >
          </div>
        </div>
        <!-- Cable Category -->
        <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
          <div class="col-lg-3">
            <p class="textLabel">Cable Category:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="cableCategoryOption"
              name="cableCat"
              v-model="client.cable_category"
              @input="calculateOTC"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('cableCat')"
              >Cable Category is required.</small
            >
          </div>
        </div>
        <!-- OTC -->
        <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
          <div class="col-lg-3">
            <p class="textLabel">OTC:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="OTC"
              class="form-control"
              v-b-tooltip.hover
              title="Input the OTC"
              placeholder="OTC"
              v-model.lazy="client.OTC"
            />
          </div>
        </div>
        <!-- Term -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Term:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="termnummonths"
              class="form-control"
              placeholder="Term (number of months)"
              v-model.lazy="client.term"
              v-validate="'required|numeric'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('termnummonths')"
              >Term is required.</small
            >
          </div>
        </div>
        <!-- Sales in charge -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Sales in charge:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales"
              name="saleincharge"
              v-model="salesInCharge"
              @input="onChangeSalesInCharge"
              option-value="id"
              option-text="name"
              placeholder="Select a seller's name..."
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('saleincharge')"
              >Sales in charge is required.</small
            >
          </div>
        </div>
        <!-- Sales Agent -->
        <div class="rowFields mx-auto row" v-if="sales_agent.length > 0">
          <div class="col-lg-3">
            <p class="textLabel">Sales Agent:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales_agent"
              v-model="client.sales_agent_id"
              name="sale_agent"
              option-value="id"
              option-text="name"
              placeholder="Select sales agent if this client deal by agent..."
            ></model-list-select>
          </div>
        </div>
                <!-- Referral -->
          <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Referred By:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="referral"
              class="form-control"
              v-b-tooltip.hover
              title="Input the referral"
              placeholder="Name of referral"
              v-model.lazy="client.referral"
            />

          </div>
        </div>
        <!-- Tech sales in charge -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Tech sales in charge:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="techsales"
              name="techincharge"
              v-model="engineer"
              option-value="id"
              option-text="name"
              placeholder="Select a engineer's name..."
              @input="onChangeAddEngineer"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('techincharge')"
              >Tech sales in charge is required.</small
            >
          </div>
        </div>
        <!-- Branch -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="client.branch"
              option-value="id"
              option-text="name"
              name="branch"
              ref="branch"
              placeholder="Select/Search a branch..."
              v-validate="'required'"
              @input="addClientChangeBranch()"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('branch')"
              >Branch is required.</small
            >
          </div>
        </div>
        <!-- Area -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Area:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="Area"
              v-model.lazy="areaSelected.name"
              disabled
            />
          </div>
        </div>
        <!-- Region -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Region:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              placeholder="Region"
              v-model.lazy="regionSelected.name"
              disabled
            />
          </div>
        </div>

        <!-- Bucket -->
        <div class="rowFields mx-auto row" v-if="client.isNew != '0'">
          <div class="col-lg-3">
            <label class>Bucket:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="buckets"
              v-model="client.bucket_id"
              :custom-text="getBucketDesc"
              option-value="id"
              option-text="name"
              name="bucket"
              placeholder="Select/Search a Bucket..."
            ></model-list-select>
          </div>
        </div>
        <!-- Subscription Name -->
        <div class="rowFields mx-auto row" v-if="client.isNew != '0'">
          <div class="col-lg-3">
            <label class>Subscription Name:</label>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="subscription_name"
              class="form-control"
              v-b-tooltip.hover
              title="subscription name in the bucket"
              placeholder="subscription name"
              v-model.lazy="client.subscription_name"
            />
          </div>
        </div>
        <!-- Status -->
        <div class="rowFields mx-auto row" v-if="client.isNew != '0'">
          <div class="col-lg-3">
            <label class>Status:</label>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="statusOption"
              v-model="client.status"
              option-value="name"
              option-text="name"
              placeholder="Select status"
            ></model-list-select>
          </div>
        </div>
        <!-- Date Activated -->
        <div class="rowFields mx-auto row" v-if="client.isNew != '0'">
          <div class="col-lg-3">
            <label class>Date Activated:</label>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="client.date_activated"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>
        <!-- Protocol -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Protocol:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="protocolOption"
              v-model="client.communication_protocol"
              option-value="id"
              option-text="name"
              placeholder="Select a communication protocol..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks/Note(client):</p>
          </div>
          <div class="col-lg-9">
            <textarea
              autocomplete="off"
              autocorrect="off"
              autocapitalize="off"
              spellcheck="false"
              rows="3"
              name="remarks"
              class="form-control"
              v-b-tooltip.hover
              title="Input Remarks "
              placeholder="Remarks/Note"
              v-model.lazy="client.remarks"
            ></textarea>
          </div>
        </div>

        <div class="rowFields mx-auto row" v-if="client.isNew == '0'">
          <div class="col-lg-3">
            <p class="textLabel">Remarks/Note(EMAIL):</p>
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
              placeholder="Remarks/Note to you email"
              v-model.lazy="client.email_note"
            ></textarea>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Waiting for client confirmation:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client.wfc"
            >
              <i class="fas fa-check" v-show="client.wfc" />
              <i class="fas fa-times" v-show="!client.wfc" />
            </p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-2">
            <p class="textLabel">Latitude:</p>
          </div>
          <div class="col-lg-4">
            <input
              type="number"
              class="form-control"
              v-model="client.lat"
              v-on:keyup.enter="updateMarker"
            />
          </div>
          <div class="col-lg-2">
            <p class="textLabel">Longitude:</p>
          </div>
          <div class="col-lg-4">
            <input
              type="number"
              class="form-control"
              v-model="client.lng"
              v-on:keyup.enter="updateMarker"
            />
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-1"></div>
          <div class="col-lg-10">
            <GmapMap
              ref="gmapclient"
              :center="coordinates"
              :zoom="17"
              map-type-id="satellite"
              style="width: 100%; height: 500px"
            >
              <GmapMarker
                :key="index"
                v-for="(m, index) in markers"
                :position.sync="m.position"
                :clickable="true"
                :draggable="true"
                :animation="2"
                :label="m.label"
                @dragend="updateCoordinates($event.latLng, index)"
              />
            </GmapMap>
          </div>
          <div class="col-lg-1"></div>
        </div>-->

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!-- end modalAdd-------->

      <!--modalManageSched--------------------------------------------------------------------------->
      <b-modal
        id="modalManageSched"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Manage schedule"
      >
        <!--Form-------->
        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">Schedule</p>
          </div>
          <div class="elClr panel-body">
            <div>
              <b-table
                class="elClr"
                striped
                show-empty
                hover
                outlined
                :fields="sched_field"
                :items="sched_items"
                :busy="tblisBusy"
                head-variant=" elClr"
                thead-class="cursorPointer-th"
              >
                <div slot="table-busy" class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>

                <template v-slot:cell(act)="row">
                  <b-button
                    variant="success"
                    @click="openModalEditSched(row.item)"
                    size="sm"
                    >update</b-button
                  >
                  <b-button
                    variant="danger"
                    @click="deleteSched(row.item)"
                    size="sm"
                    >delete</b-button
                  >
                </template>
              </b-table>
            </div>
          </div>
          <div class="elClr panel-footer"></div>
        </div>
        <!--Form-------->

        <template slot="modal-footer" slot-scope="{}"></template>
      </b-modal>
      <!-- end modalManageSched-------->

      <!-- modalEditSched ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalEditSched"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Manage Schedule"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Client Detail ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model="editSched.client_detail_id"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Ticket ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model="editSched.ticket_id"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Team ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model="editSched.team_id"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Target Date:</p>
          </div>
          <div class="col-lg-9">
            <date-picker
              v-model="editSched.target_date"
              :config="AppliedDateoptions"
              autocomplete="off"
            ></date-picker>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Status:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model="editSched.status"
            />
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdateSched()"
            >Update</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEditSched -->

      <!-- modalChangeDateActivated --------------------------------------------------------------------->
      <b-modal
        id="modalChangeDateActivated"
        centered
        title="Change Activated Date"
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
            <p class="textLabel">{{ editClient.name }}</p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Select date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="editClient.date_activated"
                :config="AppliedDateoptions"
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
            <input
              type="text"
              name="name"
              class="form-control"
              v-b-tooltip.hover
              title="Input remarks"
              placeholder="Remarks"
              v-on:keyup.enter="updateActivatedDateClicked"
              v-model.lazy="editClient.rm_remarks"
            />
          </div>
        </div>
      </b-modal>
      <!--End modalChangeDateActivated -->

      <!-- modalReceivePayment ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalReceivePayment"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Receive Payment"
      >
        <div>
          <!-- // or num -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">OR Number #:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Input OR number"
                placeholder="O.R. Number/Ref. number."
                v-model="receive_pay_data.or_number"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Payment Method:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="pay_method"
                v-model="pay_method_Selected"
                option-value="id"
                option-text="name"
                placeholder="Select Payment Method..."
                @input="onChangeSelectPayMethod"
              ></model-list-select>
            </div>
          </div>

          <!-- Date of payment -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date of Payment:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  @dp-change="inputDatePrevOnly(receive_pay_data.date)"
                  v-model="receive_pay_data.date"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <div v-if="receive_pay_data.isPayOnline">
            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Bank Code:</p>
              </div>
              <div class="col-lg-9">
                <model-list-select
                  :list="bank_code"
                  :custom-text="getBankCode"
                  v-model="bank_code_selected"
                  @input="onChangeBankCode_RP"
                  option-value="id"
                  option-text="name"
                  placeholder="Select Bank Code(code - date - amount)..."
                ></model-list-select>
              </div>
            </div>

            <!-- Amount -->
            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Amount Pay:</p>
              </div>
              <div class="col-lg-9">
                <div class="input-group">{{ receive_pay_data.amount }}</div>
              </div>
            </div>
          </div>
          <!-- //
        //
        //
        //
          //-->
          <div v-else>
            <!-- Amount -->
            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Amount Pay:</p>
              </div>
              <div class="col-lg-9">
                <input
                  type="number"
                  class="form-control"
                  placeholder="Amount Pay"
                  v-model="receive_pay_data.amount"
                  @input="calculateToPay"
                />
              </div>
            </div>
          </div>

          <!-- table -->
          <div class="rowFields mx-auto row">
            <b-table
              class="elClr"
              show-empty
              striped
              hover
              outlined
              :sticky-header="true"
              :no-border-collapse="true"
              responsive
              :fields="rp_fields"
              :items="rp_items"
              :busy="rp_tblisBusy"
              thead-class="cursorPointer-th"
              ref="selectableTableToPay"
              selectable
              select-mode="multi"
              @row-selected="onRowSelectedToPay"
              @row-clicked="tblToPayRowClicked"
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>

              <template #cell(selected)="{ rowSelected }">
                <template v-if="rowSelected">
                  <span aria-hidden="true">&check;</span>
                  <span class="sr-only">Selected</span>
                </template>
                <template v-else>
                  <span aria-hidden="true">&nbsp;</span>
                  <span class="sr-only">Not selected</span>
                </template>
              </template>

              <template slot="table-caption"></template>
            </b-table>
            <div class="rowFields mx-auto row">
              <div class="col-lg-9">
                <p-radio
                  class="textLabel p-default p-curve"
                  v-model="billRange"
                  @change="billRangeChange"
                  value="month"
                  name="billRange"
                  color="success-o"
                  >To this Month</p-radio
                >

                <p-radio
                  class="textLabel p-default p-curve"
                  v-model="billRange"
                  @change="billRangeChange"
                  value="nextmonth"
                  name="billRange"
                  color="success-o"
                  >To Next Month</p-radio
                >

                <p-radio
                  class="textLabel p-default p-curve"
                  v-model="billRange"
                  @change="billRangeChange"
                  value="wholebill"
                  name="billRange"
                  color="success-o"
                  >Whole term</p-radio
                >
              </div>
            </div>
          </div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-9"></div>
            <div class="col-lg-1">
              <span>Amount Due:</span>
            </div>
            <div class="col-lg-1">
              <span>{{ this.total_to_pay }}</span>
            </div>
          </div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-9"></div>
            <div class="col-lg-1">
              <span>Applied:</span>
            </div>
            <div class="col-lg-1">
              <span>{{ this.amount_applied }}</span>
            </div>
          </div>
          <!-- table -->
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Remarks/Memo:</p>
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
                v-model.lazy="receive_pay_data.remarks"
              ></textarea>
            </div>
          </div>
        </div>
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="receivePayment_OK()"
            >Save</b-button
          >
          <b-button size="sm" variant="success" @click="openModalPrintOR"
            >Print Preview</b-button
          >
        </template>
      </b-modal>
      <!-- End modalReceivePayment -->

      <!-- modalWHT ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalWHT"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="With Holding Tax Form"
      >
        <!-- Date of payment -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date of Payment:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <!-- @dp-change="inputDatePrevOnly(receive_pay_data.date)"  select only prev date-->
              <date-picker
                v-model="receive_pay_data.date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <!-- Amount -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Tax Amount:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              placeholder="Amount Pay"
              v-model="receive_pay_data.val"
              @input="calculateToPayTax"
            />
          </div>
        </div>

        <!-- table -->
        <div class="rowFields mx-auto row">
          <b-table
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :sticky-header="true"
            :no-border-collapse="true"
            responsive
            :fields="rp_fields"
            :items="rp_items"
            :busy="rp_tblisBusy"
            thead-class="cursorPointer-th"
            ref="selectableTableToPay"
            selectable
            select-mode="multi"
            @row-selected="onRowSelectedToPayTax"
            @row-clicked="tblToPayRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <template #cell(selected)="{ rowSelected }">
              <template v-if="rowSelected">
                <span aria-hidden="true">&check;</span>
                <span class="sr-only">Selected</span>
              </template>
              <template v-else>
                <span aria-hidden="true">&nbsp;</span>
                <span class="sr-only">Not selected</span>
              </template>
            </template>

            <template slot="table-caption"></template>
          </b-table>
          <div class="rowFields mx-auto row">
            <div class="col-lg-9">
              <p-radio
                class="textLabel p-default p-curve"
                v-model="billRange"
                @change="billRangeChange"
                value="month"
                name="billRange"
                color="success-o"
                >To this Month</p-radio
              >

              <p-radio
                class="textLabel p-default p-curve"
                v-model="billRange"
                @change="billRangeChange"
                value="nextmonth"
                name="billRange"
                color="success-o"
                >To Next Month</p-radio
              >

              <p-radio
                class="textLabel p-default p-curve"
                v-model="billRange"
                @change="billRangeChange"
                value="wholebill"
                name="billRange"
                color="success-o"
                >Whole term</p-radio
              >
            </div>
          </div>
        </div>
        <div class="rowFields mx-auto row">
          <div class="col-lg-9"></div>
          <div class="col-lg-1">
            <span>Amount Due:</span>
          </div>
          <div class="col-lg-1">
            <span>{{ this.total_to_pay }}</span>
          </div>
        </div>
        <!-- table -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks/Memo:</p>
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
              v-model.lazy="receive_pay_data.description"
            ></textarea>
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="wht_OK()"
            >Save</b-button
          >
        </template>
      </b-modal>
      <!-- End modalWHT -->

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
            <br />
            <label>Package type:</label>
          </div>
          <div class="col-lg-3">
            <label>{{ editClient.package.name }}</label>
            <br />
            <label>{{ editClient.package.max_speed }}</label>
            <br />
            <label>{{ editClient.package.cir }}</label>
            <br />
            <label>{{ editClient.package.mrr }}</label>
            <br />
            <label>{{ editClient.package_type.name }}</label>
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
          <b-button
            size="sm"
            variant="success"
            @click="
              updateClientPerRow('package_code', ''),
                (editPerRow.package = false)
            "
            >Save</b-button
          >
        </template>
      </b-modal>
      <!-- End modalUpdateBilling -->

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
        <div v-on:click="changeColDisplay('colOldAccount')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colOldAccount"
          ></p-check
          >Old Account No.
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
        <div v-on:click="changeColDisplay('colMacAdd')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colMacAdd"
          ></p-check
          >Mac add.
        </div>
        <br />
        <div v-on:click="changeColDisplay('colIpAssigned')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colIpAssigned"
          ></p-check
          >Ip assigned
        </div>
        <br />
        <div v-on:click="changeColDisplay('colDateAssigned')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colDateAssigned"
          ></p-check
          >Date assigned
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
        <div v-on:click="changeColDisplay('colProtocol')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colProtocol"
          ></p-check
          >Protocol
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
        <div v-on:click="changeColDisplay('colSales')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colSales"
          ></p-check
          >Sales
        </div>
        <br />
        <div v-on:click="changeColDisplay('colSalesAgent')">
          <p-check
            class="checkboxStyle p-switch p-slim"
            color="success"
            v-model="colSalesAgent"
          ></p-check
          >Sales Agent
        </div>
        <br />

        <div slot="modal-footer" slot-scope="{}"></div>
      </b-modal>

      <modal_print_or v-bind:data="print_or_data"></modal_print_or>
      <modal_client_filter v-bind:data="'table'"></modal_client_filter>
      <client_modals
        v-bind:data="cModalData"
        v-bind:packages="packages"
        v-bind:client="editClient"
      ></client_modals>
      <modal_fsr v-bind:data="item_row_click"></modal_fsr>
    </div>
  </div>
</template>
<script>
import { gmapApi } from "vue2-google-maps";
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import Swal2 from "sweetalert2";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";
import Multiselect from "vue-multiselect";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import VueRangedatePicker from "vue-rangedate-picker";
import modal_print_or from "../../modal_vue/modal_print_or.vue";
import client_modals from "../../modal_vue/client_modals.vue";

import modal_client_filter from "../../modal_vue/modal_client_filter.vue";
import modal_fsr from "../../modal_vue/modal_fsr.vue";

export default {
  components: {
    multiselect: Multiselect,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio,
    "rangedate-picker": VueRangedatePicker,
    modal_print_or: modal_print_or,
    modal_client_filter: modal_client_filter,
    client_modals: client_modals,
    modal_fsr: modal_fsr,
  },
  data() {
    return {
      cModalData: {
        field: 0,
        items: [],
      },
      editBranchSelected: {},
      areaSelected: {},
      regionSelected: {},
      branches: [],
      tblisBusy: true,
      fields: [],
      items: [],
      items_copy: [],
      tblFilter: null,
      tblFilter_copy: null,
      totalRows: "",
      currentPage: 1,
      perPage: 10,
      pageOptions: [5, 10, 20, 50, 100],

      sched_field: [
        { key: "act", label: "Action", sortable: true },
        { key: "id", sortable: true },
        { key: "client_detail_id", sortable: true },
        { key: "ticket_id", sortable: true },
        { key: "team_id", sortable: true },
        { key: "target_date", sortable: true },
        { key: "type", sortable: true },
        { key: "status", sortable: true },
      ],
      sched_items: [],
      editSched: {
        id: "",
        client_detail_id: "",
        ticekt_id: "",
        team_id: "",
        target_date: "",
        type: "",
        status: "",
      },
      colOldAccount: false,
      colAdd: false,
      colContact: false,
      colOLT: false,
      colSales: false,
      colSalesAgent: false,
      colPON: false,
      colModem: false,
      colMacAdd: false,
      colVlan: false,
      colIpAssigned: false,
      colDateAssigned: false,
      colPackage: false,
      colProtocol: false,
      colRegion: false,
      cableCategoryOption: [
        { id: "Drop Fiber", name: "Drop Fiber" },
        { id: "Hard Fiber", name: "Hard Fiber" },
        { id: "UTP", name: "Unshielded twisted pair (UTP)" },
      ],
      otcOption: [
        { id: "Paid", name: "Paid" },
        { id: "", name: "Not Paid" },
      ],
      editClient: {
        package: {
          name: "",
          max_speed: "",
          cir: "",
          mrr: "",
        },
        remarks: "",
        package_type: {
          name: "",
        },
        sales: {
          user: {
            name: "",
          },
        },
        sales_agent: {
          name: "",
        },
        engineer: {
          name: "",
        },
        _area: {
          name: "",
        },
        branch: {
          name: "",
          area: {
            name: "",
            region: {
              name: "",
            },
          },
        },
        bucket: {
          IP: "",
        },
        staggered: 0,
        re_email: false,
        id: "",
        account_no: "",
        name: "",
        owner_name: "",
        contact_person: "",
        email_add: "",
        sales_id: {},
        referral:"",
        engineers_id: {},
        location: "",
        contact: "",
        package_id: "",
        OTC: 0,
        aging: null,
        term: "",
        modem_id: "",
        package_type_id: "",
        communication_protocol: "",
        olt_id: "",
        date_activated: "",
        remarks: "",
        OTCPay: 0,
        amount_pay: 0,
        cashBond: 0,
        balance: 0,
      },
      client: {
        isNew: "0",
        re_email: false,
        account_no: "",
        name: "",
        owner_name: "",
        contact_person: "",
        business_type: "",
        email_add: "",
        OTC: 0,
        foc_length: 0,
        sales_agent_id: 0,
        referral:"",
        aging: null,
        term: "",
        sales_id: "",
        engineers_id: "",
        bucket_id: null,
        subscription_name: null,
        status: null,
        date_activated: null,
        location: "",
        contact: "",
        package_id: null,
        communication_protocol: "Internet",
        package_type_id: "",
        wfc: false,
        email_note: "",
        branch: {},
      },
      engineer: {
        id: "",
        name: "",
        position: "",
      },
      salesInCharge: {
        id: "",
        name: "",
        email: "",
      },
      packAdd: {
        package_id: "",
        package_type_id: "",
        max_speed: "",
        cir: "",
        mrr: 0,
      },
      packEdit: {
        name: "",
        package_id: "",
        package_type_id: "",
        max_speed: "",
        cir: "",
      },
      updateClientList: {
        re_email: false,
        account_no: false,
        name: false,
        owner_name: false,
        contact_person: false,
        contact: false,
        email_add: false,
        location: false,
        package: false,
        OTC: false,
        term: false,
        sales: false,
        engineers: false,
        modem: false,
        modem_mac_address: false,
        branch: false,
        ip: false,
        date_assign: false,
        status: false,
        wfc: false,
      },
      protocolOption: [
        { id: "Internet", name: "Internet" },
        { id: "Intranet", name: "Intranet" },
      ],
      statusOption: [
        { name: "Active" },
        { name: "Disconnected" },
        { name: "Temp Discon" },
        { name: "Cancelled" },
      ],
      sales_agent: [],
      packages: [],
      packageTypes: [],
      sales: [],
      techsales: [],
      modems: [],
      business_types: [],
      user: [],
      client_contacts: [],
      client_emailadresses: [],
      olts: {
        name: "",
        ip: "",
      },
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false,
      },
      totalNearToExpire: 0,
      totalExpired: 0,
      totalWFC: 0,
      totalWFS: 0,
      totalNoContract: 0,
      totalNoDOP: 0,
      datenow: new Date(),
      selectedEmails: [
        {
          email: "cnc@dctechmicro.com",
          name: "Credit and Collection",
        },
        {
          email: "amgt@dctechmicro.com",
          name: "Account Management",
        },
        {
          email: "r11-tech@dctechmicro.com",
          name: "Region 11 Technical ",
        },
      ],
      selectedEmailsCC: [
        {
          email: "srevilla@dctechmicro.com",
          name: "Sandra Revilla",
        },
      ],
      selectedEmails_DOP: [
        {
          email: "amgt@dctechmicro.com",
          name: "Account Management",
        },
      ],
      selectedEmailsCC_DOP: [
        {
          email: "r11-cnc@dctechmicro.com",
          name: "R11 Credit and Collection",
        },
      ],
      Emails: [],
      map: null,
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
      markers_edit: [
        {
          position: {
            lat: 0,
            lng: 0,
          },
        },
      ],
      latitude: 0,
      longitude: 0,
      latlng: "",
      lnglat: "",
      searchby_list: [
        {
          name: "Old Account No",
          id: "account_no",
        },
        {
          name: "New Account No",
          id: "acc_no",
        },
        {
          name: "Account Name",
          id: "name",
        },
        {
          name: "Address",
          id: "location",
        },
        {
          name: "Contact",
          id: "contact",
        },
        {
          name: "Package Type",
          id: "package_type_id",
        },
        {
          name: "Status",
          id: "status",
        },
        {
          name: "Date Activated",
          id: "date_activated",
        },

        {
          name: "ID",
          id: "id",
        },
      ],
      searchby: "name",
      isLoading: false,
      pay_method: [],
      bank_code: [],
      buckets: [],
      bank_code_selected: {
        id: 0,
        code: "",
        amount: 0,
        date: "",
      },
      pay_method_Selected: {
        id: "",
        name: "",
      },
      receive_pay_data: {
        client_id: "",
        payment_method_id: "",
        banking_payment_code_id: "",
        amount: 0,
        val: 0,
        date: "",
        or_number: "",
        remarks: "",
        isPayOnline: false,
      },
      withVat: false,
      rp_fields: [
        { key: "selected" },
        { key: "date" },
        { key: "description" },
        { key: "price", label: "ORIG AMT" },
        { key: "balance", label: "AMT DUE." },
        { key: "payment", label: "PAYMENT" },
      ],
      total_to_pay: 0,
      amount_applied: 0,
      rp_items: [],
      rp_tblisBusy: false,
      billRange: "month",
      selectedToPay: [],
      print_or_data: {
        item: [],
        client_name: "",
        client_location: "",
        amountInWord: "",
        amount: 0,
        totalSales: 0,
        wht: 0,
        vatable: 0,
        vat: 0,
      },
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
      editPerRow: {
        account_no: false,
        name: false,
        owner_name: false,
        contact_person: false,
        contact: false,
        email_add: false,
        location: false,
        package: false,
        OTC: false,
        term: false,
        sales: false,
        agent: false,
        referral:false,
        engineers: false,
        branch: false,
        bucket: false,
        ip: false,
        subscription: false,
        status: false,
        remarks: false,
        wfc: false,
        coordinates: false,
      },
      roles: [],
      filterIn: null,
      cbFilter: null,
      deletedAcc: false,
      remarksText: "",
      commentsText: [],
      history_fields: [
        {
          key: "id",
          label: "ID",
        },
        {
          key: "status",
          label: "Status",
        },
        {
          key: "date_changed",
          label: "Date Changed",
        },
      ],
      history: [],
      billings_to_export: [],
      item_row_click: {
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
    this.changeColDisplay("colAdd");
    this.roles = this.$global.getRoles();
    this.packageTypes = this.$global.getPackageTypes();

    this.sales = this.$global.getSales();
    this.techsales = this.$global.getEngineer();
    this.user = this.$global.getUser();
    if (this.roles.admin) {
      var temp = {
        key: "sched",
        label: "Schedule",
      };
      this.fields.push(temp);

      var temp = {
        key: "id",
        label: "ID",
        sortable: true,
      };
      this.fields.push(temp);
    }

    this.$getLocation({})
      .then((coordinates) => {
        this.coordinates = coordinates;
        this.markers[0].position = coordinates;
        this.client.lat = coordinates.lat;
        this.client.lng = coordinates.lng;
      })
      .catch((error) => {
        var coor = {
          lat: 7.040641,
          lng: 125.577053,
        };
        this.coordinates = coor;
        this.markers[0].position = coor;
        this.client.lat = this.coordinates.lat;
        this.client.lng = this.coordinates.lng;
      });
  },
  mounted() {
    this.changeColDisplay("");
    this.load();

    this.$root.$on("update_item", (item, filt) => {
      this.items = item.items;
      this.billings_to_export = item.billings;
      this.data = item.data;
      this.tblisBusy = false;
      this.totalRows = this.items.length;
      this.filterIn = "multi";
      this.cbFilter = filt;

      this.updateCounts(item);
    });
  },
  updated() {},
  computed: {
    // mapCoordinates() {
    //   if (!this.map) {
    //     return {
    //       lat: 0,
    //       lng: 0
    //     };
    //   }
    //   return {
    //     lat: this.map.getCenter().lat(),
    //     lng: this.map.getCenter().lng()
    //   };
    // },
    OTCPay() {
      this.editClient.OTCPay =
        this.editClient.amount_pay - this.editClient.cashBond;
      console.log(
        this.editClient.OTCPay +
          " asd " +
          this.editClient.cashBond +
          " asd " +
          this.editClient.amount_pay
      );
      return this.editClient.OTCPay;
    },
    balance() {
      if (this.OTCPay != null && this.editClient.OTC != null) {
        this.editClient.balance = this.editClient.OTC - this.OTCPay;
        return this.editClient.balance;
      } else return 0;
    },
    permonth() {
      if (this.editClient.staggered == "0") this.editClient.numMonths = 1;

      this.editClient.permonth = Math.round(
        this.balance / this.editClient.numMonths
      );
      return this.editClient.permonth;
    },
  },
  methods: {
    load() {
      this.deletedAcc = false;

      this.$root.$emit("clearNav");
      this.$nextTick(function () {
        setTimeout(function () {
          document.getElementById("accountsMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarAccounts").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http
        .get("api/Client/subIndex/" + this.user.region_id)
        .then(function (response) {
          // console.log(response.body);
          this.items = response.body.items;
          this.billings_to_export = response.body.billings;
          this.items_copy = this.items;
          this.updateCounts(response.body);
          this.$root.$emit("pageLoaded");
        });

      this.$http.get("api/getEmails").then(function (response) {
        this.Emails = response.body;
      });

      this.$http.get("api/BusinessType").then((response) => {
        this.business_types = response.body;
      });
      this.$http.get("api/Branch").then((response) => {
        this.branches = response.body;
      });

      this.$http.get("api/Paymethod").then((response) => {
        this.pay_method = response.body;
      });

      this.$http.get("api/BankCode").then((response) => {
        this.bank_code = response.body;
      });

      this.$http.get("api/Bucket").then((response) => {
        this.buckets = response.body;
      });
      this.$http.get("api/Package").then(function (response) {
        this.packages = response.body;
      });
      this.$http.get("api/getStatusHistory").then(function (response) {
        this.history = response.body;
      });

      // this.$http.get("api/Modem").then(function(response) {
      //   this.modems = response.body;
      // });
    },
    getBankCode(code) {
      return `${code.code} - ${code.date} - ${code.amount}`;
    },
    onChangeBankCode() {
      this.editClient.amount_pay = this.bank_code_selected.amount;
      this.editClient.banking_payment_code_id = this.bank_code_selected.id;
      //this.editClient.balance =
      // this.editClient.fp - this.bank_code_selected.amount;

      this.editClient.billing_date = this.bank_code_selected.date;

      // this.editClient.OTCPay =
      //   this.editClient.amount_pay - this.editClient.cashBond;

      //this.OTCPay = this.editClient.OTCPay;

      this.editClient.balance = this.editClient.OTC - this.OTCPay;

      if (this.balance > 0) {
        this.editClient.permonth = Math.round(
          this.balance / this.editClient.numMonths
        );
        //this.permonth = this.editClient.permonth;
      }
    },
    onChangeBankCode_RP() {
      this.receive_pay_data.date = this.bank_code_selected.date;
      this.receive_pay_data.amount = this.bank_code_selected.amount;
      this.receive_pay_data.banking_payment_code_id =
        this.bank_code_selected.id;
      this.calculateToPay();
    },
    updateCounts(data) {
      this.totalNearToExpire = data.countNearExpire;
      this.totalExpired = data.countExpire;
      this.totalWFC = data.countWFC;
      this.totalWFS = data.countWFS;
      this.totalNoContract = data.countNoContract;
      this.totalNoDOP = data.countNoDOP;
      this.totalRows = this.items.length;
      this.tblisBusy = false;
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
      //if (item.status === 'awesome') return 'table-success'
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    onFiltered(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      //this.$root.$emit("pageLoading");
      console.log(item);
      this.sales_agent = item.sales.agent;
      this.markers_edit[0].position = this.coordinates;
      this.packEdit = {
        package_id: "",
        package_type_id: "",
        max_speed: "",
        cir: "",
      };

      item.modem_id = parseInt(item.modem_id);
      item.package_id = parseInt(item.package_id);
      item.package_type_id = parseInt(item.package_type_id);
      if (item.package != null) {
        this.packEdit = item.package;
        if (this.packEdit.max_speed == null)
          this.packEdit.max_speed = "(Package assigned has no max speed)";
        if (this.packEdit.cir == null)
          this.packEdit.cir = "(Package assigned has no cir)";
      } else {
        item.package = {
          name: "",
        };
      }

      this.setEditClient(item);

      if (item.lat != null) {
        this.markers_edit[0].position.lat = parseFloat(item.lat);
        this.markers_edit[0].position.lng = parseFloat(item.lng);
        this.latlng = this.editClient.lat + "," + this.editClient.lng;
        this.lnglat = this.editClient.lng + "," + this.editClient.lat;
      }

      if (!this.roles.admin) {
        if (this.roles.helpdesk) {
          this.updateClientList.location = true;
          this.updateClientList.modem = true;
          this.updateClientList.modem_mac_address = true;
          this.updateClientList.package = true;
          this.updateClientList.subscription = true;
          this.updateClientList.ip = true;
          this.updateClientList.date_assign = true;
          this.updateClientList.status = true;
          this.updateClientList.branch = true;
          this.updateClientList.coordinates = true;
        }
        if (this.roles.accounting) {
          this.updateClientList.account_no = true;
          this.updateClientList.contact = true;
          this.updateClientList.location = true;
          this.updateClientList.email_add = true;
          this.updateClientList.package = true;
          this.updateClientList.OTC = true;
          this.updateClientList.branch = true;
        }
        if (this.roles.account_management) {
          this.updateClientList.term = true;
          this.updateClientList.package = true;
          this.updateClientList.OTC = true;
          this.updateClientList.location = true;
          this.updateClientList.email_add = true;
          this.updateClientList.name = true;
          this.updateClientList.owner_name = true;
          this.updateClientList.contact_person = true;
          this.updateClientList.status = true;
          this.updateClientList.branch = true;
        }
        if (this.roles.sales) {
          this.updateClientList = {
            re_email: true,
            account_no: true,
            name: true,
            owner_name: true,
            contact_person: true,
            contact: true,
            email_add: true,
            location: true,
            package: true,
            OTC: true,
            term: true,
            sales: true,
            agent: true,
            referral:true,
            engineers: true,
            modem: true,
            modem_mac_address: true,
            branch: true,
            bucket: true,
            subscription: true,
            status: true,
            remarks: true,
            coordinates: true,
            wfc: true,
          };
        }
      } else {
        this.updateClientList = {
          re_email: true,
          account_no: true,
          name: true,
          owner_name: true,
          contact_person: true,
          contact: true,
          email_add: true,
          location: true,
          package: true,
          OTC: true,
          term: true,
          sales: true,
          agent: true,
           referral:true,
          engineers: true,
          modem: true,
          modem_mac_address: true,
          branch: true,
          bucket: true,
          subscription: true,
          status: true,
          remarks: true,
          coordinates: true,
          wfc: true,
          coordinates: true,
        };
      }
      this.selectedEmails = [];

      this.selectedEmails.push({
        email: "cnc@dctechmicro.com",
        name: "Credit and Collection",
      });

      this.selectedEmails.push({
        email: "amgt@dctechmicro.com",
        name: "Account Management",
      });

      this.selectedEmails.push({
        email: item.sales.user.email,
        name: item.sales.user.name,
      });

      this.$bvModal.show("modalEdit");
    },
    setEditClient(item) {
      if (item.bucket == null) item.bucket = {};
      if (item.sales_agent == null) item.sales_agent = {};

      this.editClient = item;
      this.editClient.rm_remarks = "";
      if (item.sales != null) this.editClient._sales = item.sales;
      else this.editClient._sales = {};
      if (item.engineer != null) this.editClient.tech = item.engineer;
      else this.editClient.tech = {};

      if (item.area != null) this.editClient._area = item.area;
      else this.editClient._area = {};

      if (item.region != null) this.editClient._region = item.region;
      else this.editClient._region = {};
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    onChangeEditclient() {
      this.editClient.package = this.packEdit;

      this.editClient.package_id = this.packEdit.id;
      this.editClient.package_type_id = this.packEdit.package_type.id;
      if (this.packEdit != null) {
        if (this.packEdit.max_speed == null)
          this.packEdit.max_speed = "(The Package you select has no max speed)";
        if (this.packEdit.cir == null)
          this.packEdit.cir = "(The Package you select has no cir)";
      }

      this.$bvModal.show("modalUpdateBilling");

      this.bill_modi_tblisBusy = true;
      this.$http
        .post("api/Billing/to_pay/" + this.editClient.id + "/wholebill")
        .then((response) => {
          var temp = response.body;
          temp.forEach((item, index) => {
            var datenow = new Date();
            var date = new Date(item.date);
            item.price_update = this.packEdit.mrr;
            item.balance_update = this.packEdit.mrr;
            if (date >= datenow) {
              item.isSelected = true;
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
    btnCreate() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          if (this.client.package_id != null) {
            if (this.client.salesInCharge_email != null) {
              if (this.client.engineer_name != null) {
                swal({
                  title: "Are you sure do you want to create this account?",
                  text: "",
                  icon: "warning",
                  buttons: ["No", "Yes"],
                  dangerMode: true,
                }).then((update) => {
                  if (update) {
                    this.tblisBusy = true;
                    this.$root.$emit("pageLoading");
                    this.client.user_id = this.user.id;
                    this.client.user_name = this.user.name;
                    this.client.user_email = this.user.email;
                    this.client.name = this.client.name.toUpperCase();
                    this.client.owner_name = this.client.owner_name.toUpperCase();
                    this.client.location = this.client.location.toUpperCase();
                    this.selectedEmails.push({
                      email: this.client.salesInCharge_email,
                      name: this.client.salesInCharge_name,
                    });

                    // this.client.sendTo = this.selectedEmails;
                    // this.client.CCTO = this.selectedEmailsCC;

                    if (this.client_contacts.length > 0) {
                      for (let x = 0; x < this.client_contacts.length; x++) {
                        this.client.contact +=
                          ", " + this.client_contacts[x].contact;
                      }
                    }
                    if (this.client_emailadresses.length > 0) {
                      for (
                        let x = 0;
                        x < this.client_emailadresses.length;
                        x++
                      ) {
                        this.client.email_add +=
                          ", " + this.client_emailadresses[x].email;
                      }
                    }

                    if (this.client.isNew == 1)
                      this.client.contract = this.formatDate(new Date());
                    this.$http
                      .post("api/Client", this.client)
                      .then((response) => {

                        // console.log(response.body);
                        swal("Created", "", "success");
                        this.salesInCharge = {
                          id: "",
                          name: "",
                          email: "",
                        };
                        this.client = {
                          isNew: "0",
                          re_email: false,
                          account_no: "",
                          name: "",
                          owner_name: "",
                          contact_person: "",
                          business_type: "",
                          email_add: "",
                          OTC: 0,
                          foc_length: 0,
                          sales_agent_id: 0,
                          referral:"",
                          aging: null,
                          term: "",
                          sales_id: "",
                          engineers_id: "",
                          bucket_id: null,
                          subscription_name: null,
                          status: null,
                          date_activated: null,
                          location: "",
                          contact: "",
                          package_id: null,
                          communication_protocol: "Internet",
                          package_type_id: "",
                          wfc: false,
                          email_note: "",
                          branch: {},
                        };
                        this.engineer = {
                          id: "",
                          name: "",
                          position: "",
                        };
                        this.packAdd = {
                          package_id: "",
                          package_type_id: "",
                        };
                        this.items = response.body.items;
                        this.totalRows = response.body.items.length;
                        this.tblisBusy = false;
                        this.$root.$emit("pageLoaded");
                        this.$bvModal.hide("modalAdd");

                        this.updateCounts(response.body);
                      })
                      .catch((response) => {
                        console.log(response);
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
              } else {
                swal("Please select tech sales in-charge");
              }
            } else {
              swal("Please select sales in-charge");
            }
          } else {
            swal("Please select package");
          }
        }
      });
    },
    updateClient() {
      console.log(this.editClient);

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
              this.editClient.user_region_id = this.user.region_id;
              this.editClient.user_id = this.user.id;
              this.editClient.user_name = this.user.name;
              this.editClient.user_email = this.user.email;

              this.editClient.sendTo = this.selectedEmails;
              this.editClient.CCTO = this.selectedEmailsCC;

              this.editClient.salesInCharge_name =
                this.editClient.sales.user.name;
              this.editClient.packagetype = this.editClient.package_type.name;
              this.editClient.packCode = this.editClient.package.name;
              this.editClient.packSpeed = this.editClient.package.max_speed;
              this.editClient.packCIR = this.editClient.package.cir;
              this.editClient.packMRR = this.editClient.package.mrr;

              this.editClient.engineer_name = this.editClient.engineer.name;
              this.editClient.email_note = this.editClient.remarks;

              this.$http
                .put("api/Client/" + this.editClient.id, this.editClient)
                .then((response) => {
                  //this.items = response.body.items; comment dyud dapat ni siya
                  console.log(response.body);

                  this.updateCounts(response.body);
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalEdit");
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
                  this.$root.$emit("pageLoaded");
                });
            }
          });
        } else {
          swal("Please complete all the input requirements", "", "info");
        }
      });
    },
    deleteClient() {
      if (this.roles.delete_client) {
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
              user_id: this.user.id,
              user_name: this.user.name,
              id: this.editClient.id,
              region_id: this.user.region_id,
              filterIn: this.filterIn,
              cbFilter: this.cbFilter,
              searchby: this.searchby,
              tblFilter: this.tblFilter_copy,
            };
            this.$http
              .post("api/Client/destroy1", data)
              .then((response) => {
                console.log(response.body);
                this.$bvModal.hide("modalEdit");

                this.data = response.body.data;
                swal("Deleted", "", "success");
                this.items = response.body.items;
                this.totalRows = response.body.items.length;
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
          }
        });
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a client",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        });
      }
    },
    openModalDOP(item) {
      console.log(item);
      this.selectedEmails_DOP = [];
      this.setEditClient(item);
      this.selectedEmails_DOP.push({
        email: "amgt@dctechmicro.com",
        name: "Account Management",
      });

      this.selectedEmails_DOP.push({
        email: item.sales.user.email,
        name: item.sales.user.name,
      });
      this.selectedEmails_DOP.push({
        email: item.region.user.email,
        name: item.region.user.name,
      });
      if (item.OTC == null) item.OTC = 0;
      if (item.package == null) item.package = {};
      if (item.package.mrr == null) item.package.mrr = 0;
      if (item.payment_method_id == null) item.payment_method_id = "";
      if (item.or_number == null) item.or_number = "";
      if (item.billing_date == null) item.billing_date = "";
      if (item.amount_pay == null) item.amount_pay = 0;

      if (this.withVat) {
        item.cashBond = (item.package.mrr / 1.12).toFixed(2);
        item.fp = parseFloat(item.OTC) + parseFloat(item.cashBond);
      } else {
        item.cashBond = item.package.mrr;
        item.fp = item.OTC + item.package.mrr;
      }

      this.$http.get("api/BankCode").then((response) => {
        this.bank_code = response.body;
      });
      this.$bvModal.show("modalDOP");
    },
    openModalReceivePayment(item) {
      this.receive_pay_data.client_id = item.id;
      this.$bvModal.show("modalReceivePayment");
      this.total_to_pay = 0;
      this.amount_applied = 0;
      this.print_or_data = {
        item: [],
        client_name: item.name,
        client_location: item.location,
        amountInWord: "",
        amount: 0,
        totalSales: 0,
        wht: 0,
        vatable: 0,
        vat: 0,
        date: null,
      };

      this.billRangeChange();
    },
    openModalWHT(item) {
      this.receive_pay_data.client_id = item.id;
      this.$bvModal.show("modalWHT");
      this.total_to_pay = 0;
      this.amount_applied = 0;
      this.billRangeChange();
    },
    updateDOP() {
      var chk = 0;
      if (
        this.editClient.payment_method_id == "" ||
        this.editClient.payment_method_id == null
      ) {
        chk++;
        swal("Please select/reselect payment method");
        return 0;
      }
      if (this.editClient.or_number == "") {
        chk++;
        swal("OR Number is Required");
        return 0;
      }
      if (this.editClient.billing_date == "") {
        chk++;
        swal("Date of payment is Required");
        return 0;
      }
      if (this.editClient.amount_pay == 0) {
        chk++;
        swal("Amount must be greater than zero");
        return 0;
      }
      if (this.editClient.staggered == 1 && this.editClient.numMonths == null) {
        chk++;
        swal("Months to pay is required.");
        return 0;
      }

      if (chk == 0)
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then((update) => {
          if (update) {
            this.$root.$emit("pageLoading");
            this.editClient.user_region_id = this.user.region_id;
            this.editClient.user_id = this.user.id;
            this.editClient.user_name = this.user.name;
            this.editClient.user_email = this.user.email;

            this.editClient.sendTo = this.selectedEmails_DOP;
            this.editClient.CCTO = this.selectedEmailsCC_DOP;
            this.$http
              .post("api/Client/updateDOP", this.editClient)
              .then((response) => {
                this.items = response.body.items; //comment dyud dapat ni siya
                this.totalNoDOP--;
                swal("Updated", "", "success");
                this.$bvModal.hide("modalDOP");
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
                this.$root.$emit("pageLoaded");
              });
          }
        });
    },
    updateContract(item) {
      Swal2.fire({
        title: "<strong>Update Contract</strong>",
        type: "info",
        html: "",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: "Thumbs up, great!",
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: "Thumbs down",
      }).then((update) => {
        if (update.value) {
          Swal2.fire({
            title: "<strong>do you want to send email?</strong>",
            type: "info",
            html: "",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
            confirmButtonAriaLabel: "Thumbs up, great!",
            cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
            cancelButtonAriaLabel: "Thumbs down",
          }).then((update) => {
            if (update.value) {
              item.sendEmail = true;
            } else {
              item.sendEmail = false;
            }

            this.$root.$emit("pageLoading");
            item.user_region_id = this.user.region_id;
            item.user_id = this.user.id;
            item.user_name = this.user.name;
            item.user_email = this.user.email;

            item.sendTo = [
              {
                email: "cnc@dctechmicro.com",
                name: "Credits and Collection",
              },
            ];

            item.CCTO = [
              {
                email: this.user.email,
                name: this.user.name,
              },
              {
                email: item.sales.user.email,
                name: item.sales.user.name,
              },
            ];

            this.$http
              .post("api/Client/updateContract", item)
              .then((response) => {
                this.items = response.body.items;
                this.totalNoContract--;
                this.$bvModal.hide("modalEdit");
                this.$root.$emit("pageLoaded");
                swal("Updated", "", "success");
              });
          });
        }
      });
    },
    updateSubscription(item, index) {
      Swal2.fire({
        title: "<strong>Update Subscription</strong>",
        type: "info",
        html: "",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: "Thumbs up, great!",
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: "Thumbs down",
      }).then((update) => {
        if (update.value) {
          this.$root.$emit("pageLoading");
          item.user_region_id = this.user.region_id;
          item.user_id = this.user.id;
          item.user_name = this.user.name;

          this.$http
            .post("api/Client/updateSubscription", item)
            .then((response) => {
              this.items = response.body.items;
              this.totalWFS--;
              this.$bvModal.hide("modalEdit");
              this.$root.$emit("pageLoaded");
              swal("Updated", "", "success");
            });
        }
      });
    },
    openModalAdd() {
      this.$bvModal.show("modalAdd");
      // this.$refs.gmapclient.$mapPromise.then(map => {
      //   this.map = map;
      // });
    },
    openModalSched(item) {
      this.sched_items = [];
      this.$bvModal.show("modalManageSched");
      this.$http.post("api/getSched", item).then((response) => {
        this.sched_items = response.body.installation;
        this.sched_items = this.sched_items.concat(response.body.ticket);
      });
    },
    openModalEditSched(item) {
      this.editSched = item;
      this.$bvModal.show("modalEditSched");
    },
    openModalPrintOR() {
      const options = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      };

      this.print_or_data.totalSales = this.amount_applied;
      this.print_or_data.totalSales_copy = this.amount_applied;
      this.print_or_data.item = this.selectedToPay;
      this.print_or_data.amount = this.amount_applied;
      this.print_or_data.amount_copy = this.amount_applied;
      this.print_or_data.date = this.receive_pay_data.date;

      var converter = require("number-to-words");

      this.print_or_data.amountInWord = converter.toWords(this.amount_applied);
      this.print_or_data.vatable = Math.round(this.print_or_data.amount / 1.12);

      this.print_or_data.vat = Number(
        Math.round(this.print_or_data.vatable * 0.12)
      ).toLocaleString("en", options);

      this.print_or_data.vatable = Number(
        this.print_or_data.vatable
      ).toLocaleString("en", options);

      this.print_or_data.amount = Number(
        this.print_or_data.amount
      ).toLocaleString("en", options);

      this.print_or_data.totalSales = Number(
        this.print_or_data.totalSales
      ).toLocaleString("en", options);
      this.$bvModal.show("modalPreviewOR");
    },
    deleteSched(item) {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.$http.delete("api/Schedule/" + item.id).then((response) => {
            this.$bvModal.hide("modalManageSched");
            swal("Deleted", "", "success");
          });
        }
      });
    },
    btnUpdateSched() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.$http
            .put("api/Schedule/" + this.editSched.id, this.editSched)
            .then((response) => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modalManageSched");
            });
        }
      });
    },
    getBucketDesc(bucket) {
      return `${bucket.name} - ${bucket.IP}`;
    },
    onChangeAddclient() {
      this.client.package_id = this.packAdd.id;
      this.client.package_type_id = this.packAdd.package_type.id;
      if (this.packAdd.max_speed == null)
        this.packAdd.max_speed = "(The Package you select has no max speed)";
      if (this.packAdd.cir == null)
        this.packAdd.cir = "(The Package you select has no cir)";

      this.client.packCode = this.packAdd.name;
      this.client.packMRR = this.packAdd.mrr;
      this.client.packSpeed = this.packAdd.max_speed;
      this.client.packCIR = this.packAdd.cir;
      this.client.packagetype = this.packAdd.package_type.name;
    },
    onChangeAddEngineer() {
      this.client.engineers_id = this.engineer.id;
      this.client.engineer_name = this.engineer.name;
    },
    onChangeSalesInCharge() {
      this.client.sales_agent_id = null;
      this.sales_agent = this.salesInCharge.agent;
      this.client.sales_id = this.salesInCharge.id;
      this.client.salesInCharge_name = this.salesInCharge.name;
      this.client.salesInCharge_email = this.salesInCharge.email;
    },
    onDateSelected(daterange) {
      this.tblisBusy = true;
      daterange.region_id = this.user.region_id;
      this.$http.post("api/Client/filterByDate", daterange).then((response) => {
        this.items = response.body.items;
        this.billings_to_export = response.body.billings;
        this.updateCounts(response.body);
      });
    },
    changeColDisplay(check) {
      this.fields = [
        // { key: "account_no", sortable: true },
        { key: "action", label: "Action", sortable: true },
        { key: "acc_no", label: "New Account No.", sortable: true },
        { key: "name", label: "Account Name", sortable: true },
        { key: "branch.name", label: "Branch", sortable: true },
        {
          key: "location",
          label: "Address",
          formatter: (value) => {
            if (value.length > 40) return value.slice(0, 40) + "...";
            else return value;
          },
          sortable: true,
        },
        { key: "contact", sortable: true },
        { key: "email_add", label: "Email", sortable: true },
        { key: "package_type.name", label: "Type", sortable: true },
        { key: "package.mrr", label: "MRR", sortable: true },
        { key: "OTC", label: "OTC", sortable: true },
      ];

      if ("colOldAccount" == check) {
        this.colOldAccount = !this.colOldAccount;
      }
      if (this.colOldAccount) {
        var temp = {
          key: "account_no",
          label: "Old Account No.",
          sortable: true,
        };
        this.fields.unshift(temp);
      }

      if ("colSales" == check) {
        this.colSales = !this.colSales;
      }
      if (this.colSales) {
        var temp = { key: "sales.user.name", label: "Sales", sortable: true };
        this.fields.push(temp);
      }
      if ("colSalesAgent" == check) {
        this.colSalesAgent = !this.colSalesAgent;
      }
      if (this.colSalesAgent) {
        var temp = {
          key: "sales_agent.name",
          label: "Sales Agent",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colModem" == check) {
        this.colModem = !this.colModem;
      }
      if (this.colModem) {
        var temp = { key: "modem.name", label: "Modem", sortable: true };
        this.fields.push(temp);
      }

      if ("colMacAdd" == check) {
        this.colMacAdd = !this.colMacAdd;
      }
      if (this.colMacAdd) {
        var temp = {
          key: "modem_mac_address",
          label: "Mac Add.",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colIpAssigned" == check) {
        this.colIpAssigned = !this.colIpAssigned;
      }
      if (this.colIpAssigned) {
        var temp = { key: "ip_assigned", sortable: true };
        this.fields.push(temp);
      }

      if ("colDateAssigned" == check) {
        this.colDateAssigned = !this.colDateAssigned;
      }
      if (this.colDateAssigned) {
        var temp = { key: "date_assigned", sortable: true };
        this.fields.push(temp);
      }

      if ("colPackage" == check) {
        this.colPackage = !this.colPackage;
      }
      if (this.colPackage) {
        var temp = { key: "package.name", label: "Package", sortable: true };
        this.fields.push(temp);
      }

      if ("colProtocol" == check) {
        this.colProtocol = !this.colProtocol;
      }
      if (this.colProtocol) {
        var temp = {
          key: "communication_protocol",
          label: "Protocol",
          sortable: true,
        };
        this.fields.push(temp);
      }

      if ("colRegion" == check) {
        this.colRegion = !this.colRegion;
      }
      if (this.colRegion) {
        var temp = { key: "region.name", label: "Region", sortable: true };
        this.fields.push(temp);
      }

      var extra = { key: "status", label: "Status", sortable: true };
      this.fields.push(extra);
      var extra = {
        key: "date_activate",
        label: "Date Activated",
        sortable: true,
      };
      this.fields.push(extra);

      var extra = {
        key: "termExpire",
        label: "Term Expire",
        sortable: true,
      };
      this.fields.push(extra);

      var extra = {
        key: "termStatus",
        label: "Term Status",
        sortable: true,
      };
      this.fields.push(extra);

      var temp = {
        key: "created_at",
        label: "Created At",
      };
      this.fields.push(temp);
      if (this.user.id == 1) {
        var temp = {
          key: "sched",
          label: "Schedule",
        };
        this.fields.push(temp);

        var temp = {
          key: "id",
          label: "ID",
          sortable: true,
        };
        this.fields.push(temp);
      }
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
                "Say Thanks"
              );
            } //other browser not tested on IE 11
            // var sa = window.open(
            //   "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet," +
            //     encodeURIComponent(tab_text)
            // );
            else
              var sa = window.open(
                "data:application/vnd.ms-excel," + encodeURIComponent(tab_text)
              );
            this.perPage = 10;
            return sa;
          }.bind(this),
          1000
        );
      });
    },
    filterChange(txt) {
      //this.items = this.$global.getClients();
      this.tblFilter = txt;
    },
    filterClick(txt) {
      this.tblisBusy = true;
      if (txt == "dop") {
        this.$http
          .get("api/Client/filterNoDOP/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      } else if (txt == "wfc") {
        this.$http
          .get("api/Client/filterWFC/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      } else if (txt == "wfs") {
        this.$http
          .get("api/Client/filterWFS/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      } else if (txt == "contract") {
        this.$http
          .get("api/Client/filterNoContract/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      } else if (txt == "expired") {
        this.$http
          .get("api/Client/filterExpired/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      } else if (txt == "cease") {
        this.$http
          .get("api/Client/filterCease/" + this.user.region_id)
          .then(function (response) {
            this.items = response.body.items;
            this.billings_to_export = response.body.billings;
            this.totalRows = this.items.length;
            this.tblisBusy = false;
          });
      }
    },
    clearFilter() {
      this.tblFilter = "";
      this.tblFilter_copy = "";
      this.items = this.items_copy;
      this.totalRows = this.items.length;
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
    },
    inputDatePrevOnly(date) {
      var datenow = new Date();
      var date = new Date(date);
      if (date > datenow) {
        swal("Ops.", "Please select present or previous dates only", "warning");
        this.editClient.aging = null;
        this.editClient.billing_date = null;
        this.client.aging = null;
      }
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
    updateActivatedDateClicked(bvModalEvt) {
      bvModalEvt.preventDefault();
      if (this.editClient.rm_remarks != "") {
        this.editClient.user_region_id = this.user.region_id;
        this.editClient.user_id = this.user.id;
        this.editClient.user_name = this.user.name;
        this.$http
          .post("api/updateDateActivated", this.editClient)
          .then((response) => {
            swal("Updated", "", "success");
            this.$bvModal.hide("modalChangeDateActivated");
          })
          .catch((response) => {
            console.log(response.body);
            swal({
              title: response.body.error,
              text: "",
              icon: "error",
              dangerMode: true,
            });
          });
      } else swal("Remarks is required", "", "warning");
    },
    testlog() {
      this.$http
        .post("api/testlog")
        .then((response) => {})
        .catch((response) => {
          console.log(response);
        });
    },
    limitText(count) {
      return `and ${count} other countries`;
    },
    asyncFind(query) {
      this.isLoading = true;
      ajaxFindCountry(query).then((response) => {
        this.countries = response;
        this.isLoading = false;
      });
    },
    clearAll() {
      this.selectedEmails = [];
    },
    addTagEEDOP(newTag) {
      const tag = {
        email: newTag,
        name: newTag,
      };
      this.Emails.push(tag);
      this.selectedEmails_DOP.push(tag);
    },
    addTagCCDOP(newTag) {
      const tag = {
        email: newTag,
        name: newTag,
      };
      this.Emails.push(tag);
      this.selectedEmailsCC_DOP.push(tag);
    },
    addTagCreateTo(newTag) {
      const tag = {
        email: newTag,
        name: newTag,
      };
      this.Emails.push(tag);
      this.selectedEmails.push(tag);
    },
    addTagCreateCC(newTag) {
      const tag = {
        email: newTag,
        name: newTag,
      };
      this.Emails.push(tag);
      this.selectedEmailsCC.push(tag);
    },
    calculateOTC() {
      console.log(this.client.cable_category);

      if (this.client.foc_length > 0 && this.client.cable_category != "") {
        var cableadd = 0;
        var addpermeter = 35;
        var addbycat = 3360;
        var minLength = 150;
        if (this.client.cable_category == "Drop Fiber") {
          addpermeter = 35;
          addbycat = 3360;
          minLength = 150;
        }
        if (this.client.cable_category == "Hard Fiber") {
          addpermeter = 120;
          addbycat = 33600;
          minLength = 250;
        }

        if (this.client.foc_length > minLength)
          cableadd = (this.client.foc_length - minLength) * addpermeter;

        var otc = cableadd + addbycat;
        this.client.OTC = otc;
      }
    },
    addContactField() {
      if (this.client_contacts.length > 1)
        swal("Maximum contact number reached");
      else this.client_contacts.push({ contact: null });
    },
    removeContactField(index) {
      this.client_contacts.splice(index, 1);
    },
    addEmailField() {
      if (this.client_emailadresses.length > 1)
        swal("Maximum Email Address reached");
      else this.client_emailadresses.push({ contact: null });
    },
    removeEmailField(index) {
      this.client_emailadresses.splice(index, 1);
    },
    updateCoordinates(position, index) {
      //console.log(position);
      this.markers[index].position.lat = position.lat();
      this.markers[index].position.lng = position.lng();
      this.client.lat = position.lat();
      this.client.lng = position.lng();
    },
    updateCoordinates_edit(position, index) {
      this.markers_edit[index].position.lat = position.lat();
      this.markers_edit[index].position.lng = position.lng();
      this.editClient.lat = position.lat();
      this.editClient.lng = position.lng();
      this.latlng = position.lat() + "," + position.lng();
      this.lnglat = position.lng() + "," + position.lat();
    },
    updateMarker() {
      this.markers[0].position.lat = parseFloat(this.client.lat);
      this.markers[0].position.lng = parseFloat(this.client.lng);
    },
    updateMarker_edit(txt) {
      if (txt == "latlng") {
        var temp = this.latlng.split(",");
        this.editClient.lat = temp[0];
        this.editClient.lng = temp[1];
      }
      if (txt == "lnglat") {
        var temp = this.lnglat.split(",");
        this.editClient.lat = temp[1];
        this.editClient.lng = temp[0];
      }
      this.latlng = this.editClient.lat + "," + this.editClient.lng;
      this.lnglat = this.editClient.lng + "," + this.editClient.lat;

      this.markers_edit[0].position.lat = parseFloat(this.editClient.lat);
      this.markers_edit[0].position.lng = parseFloat(this.editClient.lng);
      this.updateClient();
    },
    search_data() {
      if (this.searchby == "account_no") {
        var temp = {
          key: "account_no",
          label: "Old Account No.",
          sortable: true,
        };
        this.fields.unshift(temp);
      }
      this.$http
        .get(
          "api/Client/search_data/" +
            this.searchby +
            "/" +
            this.tblFilter_copy +
            "/" +
            this.deletedAcc
        )
        .then((response) => {
          this.items = response.body.items;
          this.billings_to_export = response.body.billings;
          this.filterIn = "search";

          this.totalRows = this.items.length;
          console.log(response.body);
        })
        .catch((response) => {
          console.log(response);
        });
    },
    onChangeSelectPayMethod() {
      this.editClient.payment_method_id = this.pay_method_Selected.id;
      this.receive_pay_data.payment_method_id = this.pay_method_Selected.id;
      if (
        this.pay_method_Selected.id == 30 ||
        this.pay_method_Selected.id == 31 ||
        this.pay_method_Selected.id == 32 ||
        this.pay_method_Selected.id == 34 ||
        this.pay_method_Selected.id == 39 ||
        this.pay_method_Selected.id == 40 ||
        this.pay_method_Selected.id == 41 ||
        this.pay_method_Selected.id == 48
      ) {
        this.editClient.isPayOnline = true;
        this.receive_pay_data.isPayOnline = true;
      } else {
        this.editClient.isPayOnline = false;
        this.receive_pay_data.isPayOnline = false;
      }
    },
    onChangeWithVat() {
      if (this.withVat) {
        this.editClient.cashBond = (this.editClient.package.mrr / 1.12).toFixed(
          2
        );
        this.editClient.fp =
          parseFloat(this.editClient.OTC) +
          parseFloat(this.editClient.cashBond);
      } else {
        this.editClient.cashBond = this.editClient.package.mrr;
        this.editClient.fp = this.editClient.OTC + this.editClient.package.mrr;
      }
    },
    receivePayment_OK() {
      console.log(this.receive_pay_data);
      var chk = 0;
      if (this.receive_pay_data.payment_method_id == "") {
        chk++;
        swal("Please select/reselect payment method");
        return 0;
      }
      if (this.receive_pay_data.remarks == "") {
        chk++;
        swal("Please add remarks first.");
        return 0;
      }
      if (this.receive_pay_data.or_number == "") {
        chk++;
        swal("OR Number is Required");
        return 0;
      }
      if (this.receive_pay_data.date == "") {
        chk++;
        swal("Date is Required");
        return 0;
      }
      if (this.receive_pay_data.amount == 0) {
        chk++;
        swal("Amount must be greater than zero");
        return 0;
      }

      // if (this.amount_applied != this.receive_pay_data.amount) {
      //   chk++;
      //   swal("Amount applied must be equals to amount pay");
      //   return 0;
      // }

      if (chk == 0)
        swal({
          title: "Are you sure?",
          focusConfirm: true,
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
        }).then((update) => {
          if (update) {
            this.$root.$emit("pageLoading");
            this.receive_pay_data.user_id = this.user.id;
            this.receive_pay_data.user_name = this.user.name;
            this.receive_pay_data.selectedToPay = this.selectedToPay;
            this.$http
              .post("api/CustomerPayment", this.receive_pay_data)
              .then((response) => {
                console.log(response.body);
                swal("Saved", "", "success");
                this.$bvModal.hide("modalReceivePayment");
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
    wht_OK() {
      console.log(this.receive_pay_data);
      swal({
        title: "Are you sure?",
        focusConfirm: true,
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
      }).then((update) => {
        if (update) {
          this.$root.$emit("pageLoading");
          this.receive_pay_data.description =
            "WHT - " + this.receive_pay_data.description;
          this.receive_pay_data.user_id = this.user.id;
          this.receive_pay_data.user_name = this.user.name;
          this.receive_pay_data.selectedToPay = this.selectedToPay;
          this.$http
            .post("api/WHT", this.receive_pay_data)
            .then((response) => {
              console.log(response.body);
              swal("Saved", "", "success");
              this.$bvModal.hide("modalWHT");
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
    billRangeChange() {
      this.rp_tblisBusy = true;
      this.$http
        .post(
          "api/Billing/to_pay/" +
            this.receive_pay_data.client_id +
            "/" +
            this.billRange
        )
        .then((response) => {
          console.log(response.body);
          this.rp_items = response.body;
          this.rp_tblisBusy = false;
        })
        .catch((response) => {
          swal({
            title: "Error",
            text: response.body.error + " " + response.body.message,
            icon: "error",
            dangerMode: true,
          });
          this.rp_tblisBusy = false;
        });
    },
    onRowSelectedToPay(items) {
      this.selectedToPay = items;
      console.log(this.selectedToPay);
      this.calculateToPay();
    },
    tblToPayRowClicked(item, index, event) {
      if (item.isSelected == true)
        this.$nextTick(function () {
          setTimeout((item.isSelected = false), 400);
          setTimeout((item.payment = 0), 400);
        });
      console.log(item);
    },
    calculateToPay() {
      this.total_to_pay = 0;
      this.amount_applied = 0;
      var amount = parseFloat(this.receive_pay_data.amount);
      this.selectedToPay.forEach((item) => {
        this.total_to_pay += item.balance;
        item.isSelected = true;
        if (amount >= item.balance) {
          this.amount_applied = this.amount_applied + item.balance;
          amount = amount - item.balance;

          item.payment = 0;
        } else {
          item.payment = item.balance - amount;
          this.amount_applied = this.amount_applied + amount;
          amount = 0;
        }
      });
    },
    onRowSelectedToPayTax(items) {
      this.selectedToPay = items;
      this.calculateToPayTax();
    },
    calculateToPayTax() {
      this.total_to_pay = 0;

      var amount = parseFloat(this.receive_pay_data.val);
      this.selectedToPay.forEach((item) => {
        item.isSelected = true;
        if (amount >= item.balance) {
          this.total_to_pay = this.total_to_pay + item.balance;
          item.payment = item.balance;
        } else {
          item.payment = amount;
          this.total_to_pay = this.total_to_pay + amount;
        }
      });
      this.receive_pay_data.amount = this.total_to_pay;
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
    updateClientPerRow(row, value) {
      console.log(value);
      var data = value;
      if (row == 'name') {
        data = value.toUpperCase();
        this.editClient.name = data;
      }
      if (row == 'owner_name') {
        data = value.toUpperCase();
        this.editClient.owner_name = data;
      }
      if (row == 'location') {
        data = value.toUpperCase();
        this.editClient.location = data;
      }
      if (row == "sales_id") {
        this.editClient.sales.user.name = value.name;
        data = value.id;
        this.sales_agent = value.agent;
      }
      if (row == "engineers_id") {
        this.editClient.engineer.name = value.name;
        data = value.id;
      }
      if (row == "sales_agent_id") {
        this.editClient.sales_agent.name = value.name;
        data = value.id;
      }
      if (row == "branch") {
        //editBranchSelected
        this.editClient.branch = value;

        data = {
          branch_id: value.id,
          area_id: value.area.id,
          region_id: value.area.region.id,
        };
      }
      if (row == "package_code") {
        data = {
          package_id: this.packEdit.id,
          package_type_id: this.packEdit.package_type.id,
          soa_items: this.bill_modi_items,
        };
      }
      if (row == "coordinates") {
        var temp = this.latlng.split(",");
        this.editClient.lat = temp[0];
        this.editClient.lng = temp[1];
        this.latlng = this.editClient.lat + "," + this.editClient.lng;

        this.markers_edit[0].position.lat = parseFloat(this.editClient.lat);
        this.markers_edit[0].position.lng = parseFloat(this.editClient.lng);

        data = {
          lat: parseFloat(this.editClient.lat),
          lng: parseFloat(this.editClient.lng),
        };
      }

      var dataa = {
        id: this.editClient.id,
        row: row,
        data: data,
        user_id: this.user.id,
        user_name: this.user.name,
      };
      console.log(dataa);
      // this.$root.$emit("pageLoading");
      this.$http
        .post("api/Client/update_per_row", dataa)
        .then((response) => {
          console.log(response.body);
          swal("Updated", "", "success");

          this.$bvModal.hide("modalUpdateBilling");
          this.$root.$emit("pageLoaded");
        })
        .catch((response) => {
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
    updateClientDetailsPerRow(row, value) {
      console.log(value);
      var data = value;
      var id = value;

      if (row == "wfc") {
        data = value;
        id = this.editClient.client_detail.id;
      }

      var data = {
        id: id,
        row: row,
        data: data,
        user_id: this.user.id,
        user_name: this.user.name,
      };
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/clientDetail/update_per_row", data)
        .then((response) => {
          console.log(response.body);
          swal("Updated", "", "success");

          this.$root.$emit("pageLoaded");
        })
        .catch((response) => {
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
    UpdateBillingCheckClicked(item) {
      item.isSelected = !item.isSelected;
      console.log(item.isSelected);
      // this.$nextTick(function() {
      //   setTimeout((item.isSelected = !item.isSelected), 400);
      // });
    },
    cancelClient() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/Client/cancelClient", this.editClient)
            .then((response) => {
              this.$root.$emit("pageLoaded");
              this.items = response.body.items;
              this.totalRows = this.items.length;
              swal("Updated", "", "success");
              this.$bvModal.hide("modalEdit");
              console.log(response.body);
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
    openColumnSettings() {
      this.$bvModal.show("modalColumnSettings");
    },
    showDeleted() {
      this.deletedAcc = true;
      this.$root.$emit("pageLoading");
      if (this.deletedAcc == true) {
        this.$http
          .get("api/Client/cancelled/" + this.user.region_id)
          .then(function (response) {
            console.log(response.body);
            this.items = response.body.items;
            this.items_copy = this.items;
            this.totalRows = this.items.length;
            this.$root.$emit("pageLoaded");
          });
      }
    },
    reload_data() {
      this.deletedAcc = false;
      this.$root.$emit("pageLoading");
      this.load();
    },
    retrieveClient() {
      console.log("retrieving. . .");
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.$http
            .post("api/Client/retrieveClient", this.editClient)
            .then((response) => {
              this.items = response.body.items;
              this.totalRows = this.items.length;
              swal("Updated", "", "success");
              this.$bvModal.hide("modalEdit");

              // console.log(response.body);
            })
            .catch((response) => {
              console.log(response);
            });
        }
      });
    },
    addClientChangeBranch() {
      console.log(this.client.branch);
      this.client.branch_id = this.client.branch.id;
      this.client.area_id = this.client.branch.area.id;
      this.client.region_id = this.client.branch.area.region.id;
      this.areaSelected = this.client.branch.area;
      this.regionSelected = this.client.branch.area.region;
    },
    addRemarks_clicked() {
      if (this.remarksText != "") {
        var data = {
          ticket_id: this.editClient.id,
          user_id: this.user.id,
          user_name: this.user.name,
          remarks: this.remarksText,
          form_type: "client",
        };
        this.$http.post("api/TicketRemarksLog", data).then((response) => {
          this.remarksText = "";
          this.editClient.remarks_log = response.body;
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
    excelReportCSV(tbl, name) {
      this.$nextTick(function () {
        setTimeout(
          function () {
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
    },
    exportCSVclient() {
      this.cModalData.field = 1;
      this.cModalData.items = this.items;
      this.excelReportCSV("genTable1", "account");
    },
    exportCSVbill() {
      var temp = {};
      this.$http
        .post("api/Billing/getBillToExport", temp)
        .then((response) => {
          // this.items = response.body.items;
          // this.totalRows = this.items.length;
          // swal("Updated", "", "success");
          // this.$bvModal.hide("modalEdit");

          console.log(response.body);
          this.cModalData.field = 0;
          this.cModalData.items = response.body;
          this.excelReportCSV("genTable1", "bill");
        })
        .catch((response) => {
          console.log(response);
        });
    },
    RequestActivity(item, type) {
      var msg = "";
      var type_id = "";
      if (type == "Change Package") {
        this.setEditClient(item);
        this.$bvModal.show("modalChangePackage");
      } else {
        if (type == "Temp Discon") {
          msg = "Temporary Disconnection for this client?";
          type_id = 1;
        }
        if (type == "Activate") {
          msg = "Activation for this client?";
          type_id = 3;
        }
        if (type == "Discon") {
          msg = "Disconnection for this client?";
          type_id = 2;
        }
        Swal2.fire({
          title: "<strong>Request for " + msg + "</strong>",
          type: "warning",
          html: "",
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
          confirmButtonAriaLabel: "Thumbs up, great!",
          cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
          cancelButtonAriaLabel: "Thumbs down",
        }).then((update) => {
          if (update.value) {
            var data = {
              ticket_type_id: type_id,
              client_id: item.id,
              user_id:this.user.id,
              created_by: this.user.id,
              status: "Pending",
              state: "helpdesk",
            };

            this.$root.$emit("pageLoading");
            this.$http
              .post("api/ActivityTicket", data)
              .then((response) => {
                console.log(response.body);
                swal("Requested!", "", "success");
                this.$root.$emit("pageLoaded");
              })
              .catch((response) => {
                console.log(response.body);
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
      }
    },
    verifiedClient(item) {
      Swal2.fire({
        title: "<strong>Are you sure?</strong>",
        type: "warning",
        html: "",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: "Thumbs up, great!",
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: "Thumbs down",
      }).then((update) => {
        if (update.value) {
          var data = {
            data: [
              {
                row: "verify",
                val: "verified",
              },
            ],
            client_id: item.id,
            user_id: this.user.id,
            user_name: this.user.name,
          };

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/Client/updateRows", data)
            .then((response) => {
              console.log(response.body);
              swal("Verified!", "", "success");
              this.$root.$emit("pageLoaded");
            })
            .catch((response) => {
              console.log(response.body);
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
    },
    autoBill(id){

        Swal2.fire({

        title: "<strong>Auto-generate billing for this client?</strong>",
        type: "info",
        html: "",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: "Thumbs up, great!",
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: "Thumbs down",
      }).then((update) => {
        if (update.value) {
           this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          var item = {
                user_region_id : this.user.region_id,
                user_id : this.user.id,
                user_name : this.user.name,
                client_id : id,
                filterIn: this.filterIn,
                cbFilter: this.cbFilter,
                searchby: this.searchby,
                tblFilter: this.tblFilter_copy,

          };

          this.$http
          .post("api/Client/updateAutoBill", item)
          .then(response => {
              console.log(response.body);

               swal("Updated", "", "success");
                this.items = response.body.items;
                this.totalRows = response.body.items.length;
                this.tblisBusy = false;
                this.$root.$emit("pageLoaded");
          })
          .catch(response => {
          console.log(response);

          });

        }
      });
    },
    stopAutoBill(id){
       Swal2.fire({

        title: "<strong>Stop auto-generate billing for this client?</strong>",
        type: "info",
        html: "",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: "Thumbs up, great!",
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: "Thumbs down",
      }).then((update) => {
        if (update.value) {
           this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          var item = {
                user_region_id : this.user.region_id,
                user_id : this.user.id,
                user_name : this.user.name,
                client_id : id,
                filterIn: this.filterIn,
                cbFilter: this.cbFilter,
                searchby: this.searchby,
                tblFilter: this.tblFilter_copy,

          };

          this.$http
          .post("api/Client/updateAutoBill", item)
          .then(response => {
              console.log(response.body);

               swal("Updated", "", "success");
                this.items = response.body.items;
                this.totalRows = response.body.items.length;
                this.tblisBusy = false;
                this.$root.$emit("pageLoaded");
          })
          .catch(response => {
          console.log(response);

          });

        }
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
  font-size: 12px;
}
.rowFields {
  margin-top: 0;
}
.modal-content,
.modal-header {
  border: 1px solid red;
}

.buttonEdit {
  color: #5886ca;
  float: right;
}
.buttonEdit:hover {
  /* background-color: silver; */
  background-color: transparent;
  text-decoration: underline;
  cursor: pointer;
}

.btn-history {
  color: green;
}

.form-control {
  margin-top: 1px;
}

.col-lg-3 {
  padding-bottom: 0;
}
.stripe-modi {
  background: #f5f9ff;
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
.buttonRow {
  margin-top: 20px;
  margin-left: 0;
  width: 90%;
  height: 30px;
  margin-bottom: 10px;
}

/* breadcrumbs */
.breadcrumb {
  /*centering*/
  display: inline-block;
  box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.35);
  overflow: hidden;
  border-radius: 5px;
  /*Lets add the numbers for each link using CSS counters. flag is the name of the counter. to be defined using counter-reset in the parent element of the links*/
  counter-reset: flag;
}

.breadcrumb a {
  text-decoration: none;
  outline: none;
  display: block;
  float: left;
  font-size: 12px;
  line-height: 36px;
  color: white;
  /*need more margin on the left of links to accomodate the numbers*/
  padding: 0 10px 0 60px;
  background: #666;
  background: linear-gradient(#666, #333);
  position: relative;
}
/*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
.breadcrumb a:first-child {
  padding-left: 46px;
  border-radius: 5px 0 0 5px; /*to match with the parent's radius*/
}
.breadcrumb a:first-child:before {
  left: 14px;
}
.breadcrumb a:last-child {
  border-radius: 0 5px 5px 0; /*this was to prevent glitches on hover*/
  padding-right: 20px;
}

/*hover/active styles*/
.breadcrumb a.active,
.breadcrumb a:hover {
  background: #333;
  background: linear-gradient(#333, #000);
}
.breadcrumb a.active:after,
.breadcrumb a:hover:after {
  background: #333;
  background: linear-gradient(135deg, #333, #000);
}

/*adding the arrows for the breadcrumbs using rotated pseudo elements*/
.breadcrumb a:after {
  content: "";
  position: absolute;
  top: 0;
  right: -18px; /*half of square's length*/
  /*same dimension as the line-height of .breadcrumb a */
  width: 36px;
  height: 36px;
  /*as you see the rotated square takes a larger height. which makes it tough to position it properly. So we are going to scale it down so that the diagonals become equal to the line-height of the link. We scale it to 70.7% because if square's:
	length = 1; diagonal = (1^2 + 1^2)^0.5 = 1.414 (pythagoras theorem)
	if diagonal required = 1; length = 1/1.414 = 0.707*/
  transform: scale(0.707) rotate(45deg);
  /*we need to prevent the arrows from getting buried under the next link*/
  z-index: 1;
  /*background same as links but the gradient will be rotated to compensate with the transform applied*/
  background: #666;
  background: linear-gradient(135deg, #666, #333);
  /*stylish arrow design using box shadow*/
  box-shadow: 2px -2px 0 2px rgba(0, 0, 0, 0.4),
    3px -3px 0 2px rgba(255, 255, 255, 0.1);
  /*
		5px - for rounded arrows and
		50px - to prevent hover glitches on the border created using shadows*/
  border-radius: 0 5px 0 50px;
}
/*we dont need an arrow after the last link*/
.breadcrumb a:last-child:after {
  content: none;
}
/*we will use the :before element to show numbers*/
.breadcrumb a:before {
  content: counter(flag);
  counter-increment: flag;
  /*some styles now*/
  border-radius: 100%;
  width: 20px;
  height: 20px;
  line-height: 20px;
  margin: 8px 0;
  position: absolute;
  top: 0;
  left: 30px;
  background: #444;
  background: linear-gradient(#444, #222);
  font-weight: bold;
}

.flat a,
.flat a:after {
  background: white;
  color: black;
  transition: all 0.5s;
}
.flat a:before {
  background: white;
  box-shadow: 0 0 0 1px #ccc;
}
.flat a:hover,
.flat a.active,
.flat a:hover:after,
.flat a.active:after {
  background: #9eeb62;
}

.btn-set {
  color: #000000;
  /* border: none; */
  background: #f8f9fa;
  flex: 1;
  margin-right: 5px;
}
.btn-set:focus {
  box-shadow: none;
}
.btn-set:hover {
  background: #dfdfdf;
}
.delChckBox {
  display: flex;
  width: 21%;
  float: right;
  margin-top: -10px;
  font-size: 12px;
}
.deleted {
  background: none;
  border: none;
  float: right;
  font-size: 12px;
  margin-top: 5px;
  box-shadow: none;
}
.box-deleted {
  font-size: 15px;
  margin-top: 5px;
  color: orange;
}

.box-deleted:focus {
  box-shadow: none;
  outline: 0;
}

.box-current {
  font-size: 15px;
  margin-top: 5px;
  color: green;
}

.box-current:focus {
  box-shadow: none;
  outline: 0;
}

.span-btn-group {
  flex: 1;
  text-align: center;
}
.btn-status {
  width: 100%;
  /* flex: 1;
  padding-bottom: 4px;
  padding-top: 4px;
  font-weight: bold; */
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
.action-col {
  display: flex;
}
.action-col > button {
  flex: 1;
  margin-left: 5px;
}

.text-upper{
  text-transform: uppercase;
}

</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css" />;

