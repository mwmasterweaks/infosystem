<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Withhodling Tax
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.accounting"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Value Tax</button>
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
        title="Manage Tax"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Input the name of the modem"
              placeholder="Name of the modem"
              v-validate="'required'"
              v-model.trim="whts.tax_value"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button size="sm" variant="success" @click="btnUpdate()">Update</b-button>
          <b-button size="sm" variant="danger" v-if="roles.delete_modem" @click="btnDelete()">Delete</b-button>
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
        title="Create Tax Form"
        @ok="handleOk"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Account name:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="clientSelected"
              :custom-text="getClientDesc"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a account (name - region)"
              name="client_id"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('client_id')"
            >Account name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Value:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="value"
              class="form-control"
              v-b-tooltip.hover
              title="Input the value"
              placeholder="Value"
              v-validate="'required'"
              v-model.trim="whts.tax_value"
              v-on:keyup.enter="btnCreate"
            />
            <small class="text-danger pull-left" v-show="errors.has('value')">Value is required.</small>
          </div>
        </div>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button size="sm" variant="success" @click="btnCreate()">Create</b-button>
        </template>
      </b-modal>
      <!--modalAdd-------->
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
      fields: [
        { key: "name", sortable: true },
        { key: "whts.tax_value", label: "Value", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      clients: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      clientSelected: {
        id: "",
        region_id: "",
        package_type: {
          name: ""
        }
      },
      whts: {
        tax_value: ""
      },
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
    this.loadClients();
  },
  mounted() {
    this.load();
    this.totalRows = this.items.length;
  },
  updated() {},
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarCalendar").style.backgroundColor = "";
          document.getElementById("navbarTicket").style.backgroundColor = "";
          document.getElementById("navbarInstallation").style.backgroundColor =
            "";
          document.getElementById("navbarMap").style.backgroundColor = "";
          document.getElementById("navbarComponents").style.backgroundColor =
            "#2196f3";
          document.getElementById("navbarAccounts").style.backgroundColor = "";
        }, 100);
      });
      this.$http.get("api/Wht").then(response => {
        this.items = response.body;
        this.totalRows = this.items.length;
      });
    },

    loadClients() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function(response) {
          this.clients = response.body;
        });
    },
    getClientDesc(client) {
      return `${client.name} - ${client.region_name}`;
    },
    reload_data() {
      this.$http
        .get("api/getClients/" + this.user.region_id)
        .then(function(response) {
          this.clients = response.body;
          this.reloader_counter++;
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
      if (this.roles.update_modem) {
        this.$bvModal.show("modalEdit");
        this.whts.id = item.id;
        this.whts.client_id = item.client_id;
        this.whts.tax_value = item.tax_value;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;

          this.whts.user_id = this.user.id;
          this.whts.user_name = this.user.name;
          this.$http
            .post("api/Wht", this.Wht)
            .then(response => {
              swal("Created", "", "success");
              this.whts.client_id = "";
              this.whts.tax_value = "";

              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$bvModal.hide("modalAdd");
              this.clientSelected = {
                id: "",
                region_id: "",
                package_type: {
                  name: ""
                }
              };
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
              this.whts.client_id = "";
              this.whts.tax_value = "";

              this.$http
                .put("api/Wht/" + this.Wht.id, this.Wht)
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
              .delete("api/Wht/" + this.Wht.id)
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
