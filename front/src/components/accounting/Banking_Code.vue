<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Banking Payment Code
          <button
            @click="OpenModalAdd"
            v-if="roles.create_bankcode"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Code</button>

          <button
            @click="selectFile"
            v-if="roles.create_bankcode"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Import Code</button>

          <input
            type="file"
            id="fileSelect"
            name="fileSelect"
            @change="previewFiles"
            style=" visibility: hidden;"
          />
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input v-model="tblFilter" placeholder="Filter"></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-table
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
          </b-table>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color:;">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              class="my-0 pull-right"
            ></b-pagination>
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
        title="Manage Banking Code"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Code:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="code"
              class="form-control"
              v-b-tooltip.hover
              title="Input Code"
              placeholder="Type..."
              v-validate="'required'"
              v-model.trim="banking_payment_code.code"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('code')">Code is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Amount:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="amount"
              class="form-control"
              v-b-tooltip.hover
              title="Input amount..."
              placeholder="Type..."
              v-validate="'required'"
              v-model.trim="banking_payment_code.amount"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('amount')">Amount is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date:</p>
          </div>
          <div class="col-lg-9">
            <b-form-datepicker
              name="date"
              class="form-control"
              v-b-tooltip.hover
              title="Select date..."
              v-model.trim="banking_payment_code.date"
              v-validate="'required'"
              autocomplete="off"
              autofocus="on"
            ></b-form-datepicker>
            <small class="text-danger pull-left" v-show="errors.has('date')">Amount is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_bankcode"
            @click="btnUpdate()"
          >Update</b-button>
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_bankcode"
            @click="btnDelete()"
          >Delete</b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!--modalAdd-------->
      <b-modal
        id="modalAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title=" Banking Payment Code Form"
        @ok="handleOk"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Code:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="code"
              class="form-control"
              v-b-tooltip.hover
              title="Input Code"
              placeholder="Type..."
              v-validate="'required'"
              v-model.trim="banking_payment_code.code"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('code')">Code is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Amount:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="amount"
              class="form-control"
              v-b-tooltip.hover
              title="Input amount..."
              placeholder="Type..."
              v-validate="'required'"
              v-model.trim="banking_payment_code.amount"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('amount')">Amount is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date:</p>
          </div>
          <div class="col-lg-9">
            <b-form-datepicker
              name="date"
              class="form-control"
              v-b-tooltip.hover
              title="Select date..."
              v-model.trim="banking_payment_code.date"
              v-validate="'required'"
              autocomplete="off"
              autofocus="on"
            ></b-form-datepicker>
            <!-- <small class="text-danger pull-left" v-show="errors.has('amount')">Amount is required.</small> -->
          </div>
        </div>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button size="sm" variant="success" @click="btnCreate()">Create</b-button>
        </template>
      </b-modal>
      <!--modalAdd-------->

      <!--modalAddMultiple-------->
      <b-modal
        id="modalAddMultiple"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Banking Payment Code Form"
      >
        <!--Form-------->
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="fields_multi_add"
          :items="items_multi_add"
          :busy="tblisBusy_multi_add"
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
        <template slot="modal-footer" slot-scope="{  }">
          <b-button size="sm" variant="success" @click="btnCreateMultiple()">Insert</b-button>
        </template>
      </b-modal>
      <!--modalAddMultiple-------->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: false,
      tblisBusy_multi_add: false,
      fields_multi_add: [
        { key: "Code", sortable: true },
        { key: "Amount", sortable: true },
        { key: "Date", sortable: true }
      ],
      items_multi_add: [],
      fields: [
        { key: "code", sortable: true },
        { key: "amount", sortable: true },
        { key: "date", sortable: true },
        { key: "status", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      banking_payment_code: {
        code: "",
        amount: "",
        date: ""
      },
      myFile: null,
      user: [],
      roles: []
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
    this.totalRows = this.items.length;
  },
  updated() {},
  methods: {
    load() {
      this.$root.$emit("clearNav");
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("accountingMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarAccounting").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http.post("api/BankCode/getall").then(response => {
        this.items = response.body;
        this.totalRows = this.items.length;
        console.log(this.items);
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
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      if (this.roles.accounting) {
        this.$bvModal.show("modalEdit");
        this.banking_payment_code.id = item.id;
        this.banking_payment_code.code = item.code;
        this.banking_payment_code.amount = item.amount;
        this.banking_payment_code.date = item.date;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    OpenModalAdd() {
      this.banking_payment_code = {
        code: "",
        amount: "",
        date: ""
      };
      this.$bvModal.show("modalAdd");
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;

          this.banking_payment_code.user_id = this.user.id;
          this.banking_payment_code.user_name = this.user.name;
          this.$http
            .post("api/BankCode", this.banking_payment_code)
            .then(response => {
              swal("Created", "", "success");
              this.banking_payment_code.code = "";
              this.banking_payment_code.amount = "";
              this.banking_payment_code.date = "";
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$bvModal.hide("modalAdd");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                  this.$refs.name.focus();
                }
              });
            });
        }
      });
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "",
            icon: "warning",
            buttons: ["No", "Yes"],
            dangerMode: true
          }).then(update => {
            if (update) {
              this.banking_payment_code.user_id = this.user.id;
              this.banking_payment_code.user_name = this.user.name;

              this.$http
                .put(
                  "api/BankCode/" + this.banking_payment_code.id,
                  this.banking_payment_code
                )
                .then(response => {
                  this.items = response.body;
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalEdit");
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
      if (this.roles.accounting) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.items = [];
            this.tblisBusy = true;
            this.$http
              .delete("api/BankCode/" + this.banking_payment_code.id)
              .then(response => {
                this.$bvModal.hide("modalEdit");
                swal("Deleted", "", "success").then(value => {
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  this.tblisBusy = false;
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
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a Modem",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    previewFiles(e) {
      //DARI KO
      var files = e.target.files,
        f = files[0];
      var reader = new FileReader();

      reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: "array" });
        let sheetName = workbook.SheetNames[0];
        /* DO SOMETHING WITH workbook HERE */

        let worksheet = workbook.Sheets[sheetName];
        console.log(XLSX.utils.sheet_to_json(worksheet));
        this.items_multi_add = XLSX.utils.sheet_to_json(worksheet);
        this.$bvModal.show("modalAddMultiple");
        document.getElementById("fileSelect").value = null;
      }.bind(this);

      reader.readAsArrayBuffer(f);
    },
    btnCreateMultiple() {
      this.banking_payment_code.user_id = this.user.id;
      this.banking_payment_code.user_name = this.user.name;
      var data = {
        data: [],
        user_id: this.user.id,
        user_name: this.user.name
      };
      this.items_multi_add.forEach(item => {
        var temp = {
          code: item.Code,
          amount: item.Amount,
          date: item.Date
        };
        data.data.push(temp);
      });
      // console.log(data.data);
      this.$http
        .post("api/BankCode/store_multi", data)
        .then(response => {
          swal("Created", "", "success");
          console.log(response.body);
          this.items = response.body;
          this.totalRows = this.items.length;
          this.tblisBusy = false;
          this.$bvModal.hide("modalAddMultiple");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          }).then(value => {
            if (value) {
              this.$refs.name.focus();
            }
          });
        });
    },
    selectFile() {
      document.getElementById("fileSelect").click();
    }
  }
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
</style>
