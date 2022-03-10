<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Sales Agent
          <agentReport v-if="roles.rm"></agentReport>
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_sales"
            type="button"
            @click="item.state = 'create'"
            style="margin-top:-20px;"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Create Sales Agent
          </button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div style="display:flex;">
          <div style="width:60%;">
            <!-- search -->
            <b-row class="searchRow">
              <b-col>
                <b-form-group>
                  <b-input-group>
                    <b-form-input
                      class="searchForm"
                      id="txtbox_filter"
                      v-model="tblFilter"
                      placeholder="Search"
                    ></b-form-input>

                    <b-input-group-append>
                      <b-button
                        class="clearBtn"
                        :disabled="!tblFilter"
                        @click="tblFilter = ''"
                        >Clear</b-button
                      >
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
        </div>

        <div style="display:flex">
          <div
            class="row marginice"
            style="margin-left:1px;float:left;width:80%"
          >
            <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
          </div>
          <div class="row marginice" style="width:8%">
            <b-row>
              <b-col style="float:right;padding-right:0"> </b-col>
              <b-col style="float:right">
                <b-form-group class="mb-0">
                  <b-form-select
                    v-b-tooltip.hover
                    title="Show Pages"
                    style="height:30px;font-size:12px"
                    v-model="perPage"
                    :options="pageOptions"
                  ></b-form-select>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
        </div>

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

      <!--modalAdd-------->
      <b-modal
        id="modalAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="lg"
      >
        <template #modal-title>
          <span v-if="item.state == 'create'">Sales Agent Form</span>
          <span v-else>Manage Sales Agent</span>
        </template>
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Sales Employee:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales"
              v-model="item.sales_id"
              option-value="id"
              option-text="name"
              name="sales_id"
              placeholder="Select/Search a user..."
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('sales_id')"
              >Sales is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Agent Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the agent name"
              placeholder="Name"
              v-model.trim="item.name"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Quota:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Sales Quota"
              placeholder="Quota"
              v-model.trim="item.quota"
            />
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
              v-b-tooltip.hover
              title="Input Status (active, inactive)"
              placeholder="Status"
              v-model.trim="item.status"
            />
          </div>
        </div>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <span v-if="item.state == 'create'">
            <b-button size="sm" variant="success" @click="btnCreate()"
              >Create</b-button
            >
          </span>
          <span v-else>
            <b-button size="sm" variant="success" @click="btnUpdate()"
              >Update</b-button
            >
            <b-button
              size="sm"
              variant="danger"
              @click="btnDelete()"
              v-if="roles.delete_sales"
              >Delete</b-button
            >
          </span>
        </template>
      </b-modal>
      <!--modalAdd-------->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import agentReport from "../../modal_vue/modal_agent_report.vue";
export default {
  components: {
    "model-list-select": ModelListSelect,
    agentReport: agentReport
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "sales_name", sortable: true },
        { key: "name", sortable: true },
        { key: "quota", sortable: true },
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
      item: {},
      statuses: [
        {
          name: "active"
        },
        {
          name: "inactive"
        }
      ],
      sales: [],
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.sales = this.$global.getSales();
    this.roles = this.$global.getRoles();
    console.log(this.sales);
    this.user = this.$global.getUser();

    if (this.user.id == 1) {
      var temp = {
        key: "id",
        label: "ID",
        sortable: true
      };
      this.fields.push(temp);
    }
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
          document.getElementById("accountsMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarAccounts").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http.get("api/sales_agent").then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_sales) {
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
      if (this.roles.update_sales) {
        this.$bvModal.show("modalAdd");
        this.item = item;
        this.item.state = "update";
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.item.user_id_log = this.user.id;
          this.item.user_name_log = this.user.name;
          this.$http
            .post("api/sales_agent", this.item)
            .then(response => {
              console.log(response.body);

              swal("Created", "", "success");
              this.item = {};
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
              });
            });
          this.tblisBusy = false;
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
              this.item.user_id_update = this.user.id;
              this.item.user_name_update = this.user.name;
              this.$http
                .put("api/sales_agent/" + this.item.id, this.item)
                .then(response => {
                  swal("Updated", "", "success");
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
                    }
                  });
                });
            }
          });
        }
      });
    },
    btnDelete() {
      if (this.roles.delete_sales) {
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
              .delete("api/sales_agent/" + this.item.id)
              .then(response => {
                this.$bvModal.hide("modalAdd");
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
          text: "You are not allow to delete a sales agent",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
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
</style>
