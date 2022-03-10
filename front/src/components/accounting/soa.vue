<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Statement of Account
          <button
            v-b-modal="'modalAddBill'"
            v-if="roles.create_bill"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Add Billing
          </button>
          <button
            v-b-modal="'modalGenerateBill'"
            v-if="roles.create_bill"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Generate Billing
          </button>

          <button
            @click="selectFile"
            v-if="roles.receive_payment"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Import Dragon pay
          </button>

          <input
            type="file"
            id="fileSelect"
            name="fileSelect"
            @change="previewFiles"
            style="visibility: hidden"
          />
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-2">
              <p class="textLabel">Account name:</p>
            </div>
            <div class="col-lg-4">
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
            </div>
            <div class="col-lg-2" style="margin-top: 8px">
              <p-check
                class="p-icon p-curve p-jelly p-bigger"
                color="success"
                v-model="billStateShow"
                @change="billStateChange"
                v-if="roles.accounting || roles.helpdesk"
              >
                <i slot="extra" class="icon fas fa-check"></i> </p-check
              >Billing statement list
            </div>
            <div class="col-lg-4"></div>
          </div>
          <br />
          <b-table
            v-if="billStateShow == false"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :sticky-header="true"
            :no-border-collapse="true"
            responsive
            :fields="fields"
            :items="items"
            :busy="tblisBusy"
            :tbody-tr-class="tblRowClass"
            thead-class="cursorPointer-th"
            @row-clicked="tblRowClicked"
          >
            <span slot="check" slot-scope="data" v-html="data.value"></span>

            <template v-slot:cell(check)="row">
              <p-check
                class="p-icon p-curve p-jelly p-bigger"
                color="success"
                v-model="row.item.check"
                @change="checkPreview()"
              >
                <i slot="extra" class="icon fas fa-check"></i>
              </p-check>
            </template>

            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>

          <b-table
            v-if="billStateShow == true"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :sticky-header="true"
            :no-border-collapse="true"
            responsive
            :fields="fields_billstate"
            :items="items_billstate"
            :busy="tblisBusy_billstate"
            :tbody-tr-class="tblRowClass"
            thead-class="cursorPointer-th"
            @row-clicked="tblRowClicked_billstate"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-9">
            <div v-if="billStateShow == false">
              <button
                v-if="showPrevBtn"
                type="button"
                v-b-modal="'modalSoaPrintPreview'"
                class="btn btn-success btn-labeled"
              >
                Preview
              </button>

              <button
                v-if="showPrevBtn"
                type="button"
                v-b-modal="'modalSoaSummaryOfAccount'"
                class="btn btn-success btn-labeled"
              >
                Summary
              </button>

              <button
                v-if="showPrevBtn && roles.delete_soa"
                type="button"
                class="btn btn-danger btn-labeled"
                @click="btnDelSelected"
                :hidden="billStateShow"
              >
                Delete
              </button>
            </div>

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
      <div class="elClr panel-footer">
        <div class="row" style="background-color: ; padding: 15px">
          <div class="col-md-8" style="background-color: "></div>

          <div class="col-md-4" style="background-color: "></div>
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
        title="Manage SOA"
        @ok="handleOk"
      >
        <!-- form -->

        <div
          class="rowFields mx-auto row"
          v-if="soa_edit.downtime_hours != null"
        >
          <div class="col-lg-3">
            <p class="textLabel">downtime_hours:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-model.trim="soa_edit.downtime_hours"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
            />
          </div>
        </div>

        <div v-else>
          <div class="rowFields mx-auto row" v-if="soa_edit.date != null">
            <div class="col-lg-3">
              <p class="textLabel">Date:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="soa_edit.date"
                  :config="AppliedDateoptions"
                  autocomplete="off"
                ></date-picker>
              </div>
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="soa_edit.or_number != null">
            <div class="col-lg-3">
              <p class="textLabel">OR/Reff:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-model.trim="soa_edit.or_number"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-if="soa_edit.payment_method_id != null"
          >
            <div class="col-lg-3">
              <p class="textLabel">Payment Method:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="pay_method"
                v-model="soa_edit.payment_method_id"
                option-value="id"
                option-text="name"
                placeholder="Select Payment Method..."
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="soa_edit.item != null">
            <div class="col-lg-3">
              <p class="textLabel">Item:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-model.trim="soa_edit.item"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Description:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-model.trim="soa_edit.description"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="soa_edit.price != null">
            <div class="col-lg-3">
              <p class="textLabel">Amount Charge:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-model.trim="soa_edit.price"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="soa_edit.amount != null">
            <div class="col-lg-3">
              <p class="textLabel">Amount Paid:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-model.trim="soa_edit.amount"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="soa_edit.balance != null">
            <div class="col-lg-3">
              <p class="textLabel">Balance:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                v-model.trim="soa_edit.balance"
                v-on:keyup.enter="btnUpdate"
                autocomplete="off"
              />
            </div>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_soa"
            @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_soa"
            @click="btnDelete()"
            >Delete</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!--modalAddBill-------->
      <b-modal
        id="modalAddBill"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Billing Form"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="bill.client_id"
              :custom-text="getClientDesc"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a account (name - region)"
              name="client_id"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="bill.date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Item:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="bill.item"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="bill.description"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Amount Charge:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-model.trim="bill.price"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Balance:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-model.trim="bill.balance"
              autocomplete="off"
            />
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!--modalAddBill-------->

      <!--modalGenerateBill-------->
      <b-modal
        id="modalGenerateBill"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Generate Bill"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="gen_bill.client"
              :custom-text="getClientDesc"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a account (name - region)"
              name="client_id"
            ></model-list-select>
          </div>
        </div>

        <!-- <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package Type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="pack_list"
              v-model="gen_bill.pack_type"
              option-value="name"
              option-text="name"
              placeholder="Select/Search a package"
              name="client_id"
            ></model-list-select>
          </div>
        </div>-->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Bill Date(Date of billing):</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="gen_bill.date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Coverage start date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker
                v-model="gen_bill.cover_date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>
        </div>
        <!--

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Start Month(1 is January):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              min="1"
              max="12"
              class="form-control"
              v-model.trim="gen_bill.month"
              autocomplete="off"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Start Year:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              min="2000"
              max="9999"
              class="form-control"
              v-model.trim="gen_bill.year"
              autocomplete="off"
            />
          </div>
        </div>-->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Generate Count:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              min="1"
              max="100"
              class="form-control"
              v-model.trim="gen_bill.count"
              autocomplete="off"
            />
          </div>
        </div>
        <br />
        <br />
        <table>
          <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Description</th>
            <th>Amt Chrg</th>
            <th>Balance</th>
          </tr>
          <tr
            :key="generated_bill.date"
            v-for="generated_bill in generated_bill"
          >
            <td>{{ generated_bill.date }}</td>
            <td>{{ generated_bill.item }}</td>
            <td>{{ generated_bill.description }}</td>
            <td>{{ generated_bill.price }}</td>
            <td>{{ generated_bill.balance }}</td>
          </tr>
        </table>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="generated_bill.length > 0"
            @click="btnInsertGenBill()"
            >Add to SOA</b-button
          >
          <b-button size="sm" variant="success" @click="btnGenerateBill()"
            >Generate</b-button
          >
        </template>
      </b-modal>
      <!--modalGenerateBill-------->

      <!--modalImportRP-------->
      <b-modal
        id="modalImportRP"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Import Payment"
      >
        <!--Form-------->
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="fields_import_rp"
          :items="items_import_rp"
          :busy="tblisBusy_import_rp"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>
        </b-table>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnPay('statement')"
            >Insert base statement ID</b-button
          >
        </template>
      </b-modal>
      <!--modalImportRP-------->

      <modal_soa_print_preview
        v-bind:data="checked_item_preview"
      ></modal_soa_print_preview>
      <summaryofaccount v-bind:data="checked_item_preview"></summaryofaccount>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyRadio from "pretty-checkbox-vue/radio";
import PrettyCheck from "pretty-checkbox-vue/check";
import datePicker from "vue-bootstrap-datetimepicker";
import modal_soa_print_preview from "../modal_vue/modal_soa_print_preview.vue";
import summaryofaccount from "../modal_vue/modal_soa_summaryofaccount.vue";

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-radio": PrettyRadio,
    "p-check": PrettyCheck,
    modal_soa_print_preview: modal_soa_print_preview,
    summaryofaccount: summaryofaccount,
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "date", sortable: true },
        { key: "or_number", label: "OR/Reff.", sortable: true },
        { key: "item", sortable: true },
        { key: "description", sortable: true },
        { key: "priceFormated", label: "AMT CHRG", sortable: true },
        { key: "amountFormated", label: "AMT PAID", sortable: true },
        { key: "balanceFormated", label: "Balance", sortable: true },
        { key: "balanceSum", sortable: true },
      ],
      items: [],
      soa_edit: {
        name: "",
      },
      clientSelected: {
        id: "",
        region_id: "",
        package_type: {
          name: "",
        },
      },
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false,
      },
      billRange: "month",
      bill: {
        client_id: "",
        date: new Date(),
        item: "",
        description: "",
        price: 0,
        balance: 0,
      },
      checked_item_preview: {
        client: {
          name: "",
          address: "",
          account_no: "",
        },
        data: [],
        amount_due: 0,
        vatable: 0,
        ewt: 0,
        netOfVat: 0,
        vat: 0,
        due_date: null,
      },
      showPrevBtn: false,
      gen_bill: {
        client: {},
      },
      pack_list: [{ name: "SME" }, { name: "RES" }],
      generated_bill: [],
      billStateShow: false,
      fields_billstate: [
        { key: "id", label: "No.", sortable: true },
        { key: "date", sortable: true },
        { key: "due_date", sortable: true },
        { key: "amount_due", sortable: true },
      ],

      items_billstate: [],
      tblisBusy_billstate: false,
      clients: [],
      pay_method: [],
      user: [],
      roles: [],
      delSelected: [],
      items_import_rp: [],
      fields_import_rp: [
        { key: "Date", sortable: true },
        { key: "OR", sortable: true },
        { key: "Amount", sortable: true },
        { key: "Remarks", sortable: true },
        { key: "Account_name", sortable: true },
        { key: "Account_no", sortable: true },
        { key: "Statement_id", sortable: true },
        { key: "Action" },
      ],
      tblisBusy_import_rp: false,
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.load();
    this.loadClients();

    if (this.roles.accounting) {
      this.fields.unshift({ key: "check", sortable: true });
    }
  },
  mounted() {
    this.$root.$on("reloadBillStatement", () => {
      this.billStateChange();
    });
  },
  updated() {},
  methods: {
    load() {
      this.$root.$emit("clearNav");
      this.$nextTick(function () {
        setTimeout(function () {
          document.getElementById("accountingMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarAccounting").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http.get("api/Paymethod").then((response) => {
        this.pay_method = response.body;
      });
    },
    loadClients() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function (response) {
          this.clients = response.body;
        });
    },
    getClientDesc(client) {
      return `${client.name} - ${client.region_name}`;
    },
    onChangeSelectClient() {
      this.tblisBusy = true;
      this.$http
        .post(
          "api/Billing/soa/" + this.clientSelected.id + "/" + this.billRange
        )
        .then((response) => {
          this.items = response.body;
          this.tblisBusy = false;
          if (this.billStateShow == true) this.billStateChange();
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
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.accounting) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    tblRowClicked(item, index, event) {
      if (this.roles.accounting) {
        //if (item.tbl_name != "ticket") {
        this.$bvModal.show("modalEdit");
        this.soa_edit = item;

        //}
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.tblisBusy = true;

          this.bill.user_id = this.user.id;
          this.bill.user_name = this.user.name;
          this.$http
            .post("api/Billing", this.bill)
            .then((response) => {
              swal("Created", "", "success");
              var idtemp = this.bill.client_id;
              this.bill = {
                client_id: idtemp,
                date: new Date(),
                item: "",
                description: "",
                price: 0,
                balance: 0,
              };
              this.items = [];

              this.tblisBusy = false;
              this.$bvModal.hide("modalAddBill");
            })
            .catch((response) => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              }).then((value) => {
                if (value) {
                  this.$refs.name.focus();
                }
              });
            });
        }
      });
    },
    btnUpdate() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          this.soa_edit.user_id = this.user.id;
          this.soa_edit.user_name = this.user.name;

          if (this.soa_edit.tbl_name == "customer_payment") {
            this.soa_edit.remarks = this.soa_edit.description;
          }

          this.$http
            .post("api/Billing/updateSOA", this.soa_edit)
            .then((response) => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modalEdit");
              this.onChangeSelectClient();
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
    btnDelete() {
      if (this.roles.accounting) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            this.soa_edit.user_id = this.user.id;
            this.soa_edit.user_name = this.user.name;
            this.$http
              .post("api/Billing/deleteSOA", this.soa_edit)
              .then((response) => {
                swal("Deleted", "", "success").then((value) => {
                  this.onChangeSelectClient();
                  this.$bvModal.hide("modalEdit");
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
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a method",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        });
      }
    },
    btnDelSelected() {
      // console.log(this.delSelected);
      if (this.roles.accounting) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            this.delSelected.user_id = this.user.id;
            this.delSelected.user_name = this.user.name;
            this.$http
              .post("api/Billing/deleteMultiSOA", this.delSelected)
              .then((response) => {
                console.log(response.body);
                swal("Deleted", "", "success").then((value) => {
                  this.onChangeSelectClient();
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
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a method",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        });
      }
    },

    billRangeChange() {
      if (this.clientSelected.id != "") {
        this.onChangeSelectClient();
      }
    },
    checkPreview() {
      this.delSelected = [];
      this.checked_item_preview = {
        client: this.clientSelected,
        data: [],
        amount_due: 0,
        vatable: 0,
        vat: 0,
        ewt: 0,
        netOfVat: 0,
        due_date: null,
        id: "(save to get number)",
        saved: false,
      };
      this.showPrevBtn = false;
      var due_date;
      var sub_balance = 0;
      var summary_balance = 0;

      const options = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      };

      this.checked_item_preview.amount_due = 0;
      this.items.forEach((item) => {
        if (item.check == true) {
          if (item.balance == null) item.balance = 0;
          if (item.amountFormated != null) {
            var temp = parseFloat(item.amountFormated.replace(/,/g, ""));
            temp = temp * (0 - 1);
            item.sub_amount = temp.toLocaleString("en", options);
          }
          if (item.price != null) {
            sub_balance += item.balance;
            summary_balance += item.price;
          }
          if (item.amount != null) {
            sub_balance -= item.amount;
            summary_balance -= item.amount;
          }

          item.sub_balance = Number(sub_balance).toLocaleString("en", options);
          item.summary_balance = Number(summary_balance).toLocaleString(
            "en",
            options
          );
          this.checked_item_preview.data.push(item);
          this.showPrevBtn = true;

          var selected = {
            id: item.id,
            tbl_name: item.tbl_name,
          };
          this.delSelected.push(selected);

          due_date = item.date;
        }
      });

      this.checked_item_preview.amount_due = sub_balance;
      this.checked_item_preview.summary_balance_total = summary_balance;
      //this.checked_item_preview.id = new Date().valueOf();
      this.checked_item_preview.due_date = this.formatDateMDY(due_date);

      this.checked_item_preview.vatable =
        this.checked_item_preview.amount_due / 1.12;

      this.checked_item_preview.ewt = this.checked_item_preview.vatable * 0.02;
      this.checked_item_preview.netOfVat =
        this.checked_item_preview.amount_due - this.checked_item_preview.ewt;

      this.checked_item_preview.vat = Number(
        this.checked_item_preview.vatable * 0.12
      ).toLocaleString("en", options);

      this.checked_item_preview.vatable = Number(
        this.checked_item_preview.vatable
      ).toLocaleString("en", options);

      this.checked_item_preview.amount_due = Number(
        this.checked_item_preview.amount_due
      ).toLocaleString("en", options);

      this.checked_item_preview.summary_balance_total = Number(
        this.checked_item_preview.summary_balance_total
      ).toLocaleString("en", options);

      this.checked_item_preview.ewt = Number(
        this.checked_item_preview.ewt
      ).toLocaleString("en", options);

      this.checked_item_preview.netOfVat = Number(
        this.checked_item_preview.netOfVat
      ).toLocaleString("en", options);

      console.log(this.checked_item_preview);
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
    billStateChange() {
      this.tblisBusy_billstate = true;
      if (this.billStateShow == true) {
        this.$http
          .post("api/Billing/getBillStateList", this.clientSelected)
          .then((response) => {
            this.items_billstate = response.body;
            this.tblisBusy_billstate = false;
          })
          .catch((response) => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true,
            }).then((value) => {
              if (value) {
                this.$refs.name.focus();
              }
            });
          });
      }
    },
    tblRowClicked_billstate(item, index, event) {
      item.client.address = item.client.location;
      item.saved = true;
      const options = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      };
      item.vatable = parseFloat(item.amount_due.replace(/,/g, "")) / 1.12;

      item.ewt = item.vatable * 0.02;
      item.netOfVat = parseFloat(item.amount_due.replace(/,/g, "")) - item.ewt;

      item.vat = Number(item.vatable * 0.12).toLocaleString("en", options);

      item.vatable = Number(item.vatable).toLocaleString("en", options);

      item.ewt = Number(item.ewt).toLocaleString("en", options);

      item.netOfVat = Number(item.netOfVat).toLocaleString("en", options);

      var sub_balance = 0;
      item.data.forEach((item) => {
        if (item.balanceFormated == " ")
          sub_balance += parseFloat(item.priceFormated.replace(/,/g, ""));
        else sub_balance += parseFloat(item.balanceFormated.replace(/,/g, ""));
        item.sub_balance = Number(sub_balance).toLocaleString("en", options);
      });

      this.checked_item_preview = item;
      this.$bvModal.show("modalSoaPrintPreview");
      //
    },
    btnGenerateBill() {
      this.$http
        .post("api/Billing/generateBill", this.gen_bill)
        .then((response) => {
          this.generated_bill = response.body;
        });
    },
    btnInsertGenBill() {
      var data = {
        bill: this.generated_bill,
      };
      this.$http
        .post("api/Billing/insertGeneratedBill", data)
        .then((response) => {
          this.$bvModal.hide("modalGenerateBill");
          swal("Done");
        });
    },
    selectFile() {
      document.getElementById("fileSelect").click();
    },
    previewFiles(e) {
      //DARI KO
      var files = e.target.files,
        f = files[0];
      var reader = new FileReader();

      reader.onload = function (e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: "array" });
        let sheetName = workbook.SheetNames[0];
        /* DO SOMETHING WITH workbook HERE */

        let worksheet = workbook.Sheets[sheetName];
        console.log(XLSX.utils.sheet_to_json(worksheet));
        this.items_import_rp = XLSX.utils.sheet_to_json(worksheet);

        this.items_import_rp.forEach((element) => {
          element.Date = this.$global.excelDateToJSDate(element.Date);

          // const temp = element.Description.split("/");
          // element.Remarks = temp[2];
          // element.account_name = temp[1];
          // element.bill_statement_id = temp[0];
        });

        this.$bvModal.show("modalImportRP");
        document.getElementById("fileSelect").value = null;
      }.bind(this);

      reader.readAsArrayBuffer(f);
    },
    btnPay(base_ref) {
      var data = {
        payments: this.items_import_rp,
        base_ref: base_ref,
        user_id: this.user.id,
      };

      this.$root.$emit("pageLoading");
      this.$http
        .post("api/CustomerPayment/storePayment", data)
        .then((response) => {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          swal("Done");
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

table,
td,
th {
  border: 1px solid black;
  padding: 3px;
}

table {
  width: 100%;
  border-collapse: collapse;
}
</style>
