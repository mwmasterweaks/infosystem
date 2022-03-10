<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          MAC GENERATOR
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.network"
            type="button"
            @click="loadlist"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Generate Mac
          </button>

          <BtnManageLocation v-if="roles.network"></BtnManageLocation>
          <BtnManageDeploy v-if="roles.network"></BtnManageDeploy>
          <BtnManageDevice v-if="roles.network"></BtnManageDevice>
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
                      <b-button class="clearBtn" @click="clearFilter"
                        >Clear</b-button
                      >
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row style="margin-left:10px">
              <b-col>
                <b-form-group>
                  <b-input-group>
                    <model-list-select
                      style="width:30%;"
                      :list="location_list"
                      v-model="filter_location"
                      @input="filterChange"
                      option-value="hex_number"
                      option-text="name"
                      placeholder="Filter location..."
                    ></model-list-select>

                    <model-list-select
                      v-if="filter_location != ''"
                      style="width:30%;"
                      :list="deploy_list"
                      v-model="filter_deploy"
                      @input="filterChange"
                      option-value="hex_number"
                      option-text="name"
                      placeholder="Filter deploy..."
                    ></model-list-select>

                    <model-list-select
                      v-if="filter_location != '' && filter_deploy != ''"
                      style="width:30%;"
                      :list="device_list"
                      v-model="filter_device"
                      @input="filterChange"
                      option-value="hex_number"
                      option-text="name"
                      placeholder="Filter deploy..."
                    ></model-list-select>
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
        title="Manage MAC"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">PMX IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="editItem.pmx_ip"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="editItem.ip"
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
              v-model.trim="editItem.mac_desc"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Port Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="editItem.port_desc"
            />
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdate()"
            >Update</b-button
          >
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
        title="Generate Mac"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">PMX IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="addItem.pmx_ip"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="addItem.ip" />
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
              v-model.trim="addItem.mac_desc"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Port Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-model.trim="addItem.port_desc"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Location:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="location_list"
              v-model="addItem.location"
              option-value="id"
              option-text="name"
              placeholder="Select location..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Deplomeny Identifier:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="deploy_list"
              v-model="addItem.deploy"
              option-value="id"
              option-text="name"
              placeholder="Select identifier..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Device type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="device_list"
              v-model="addItem.device"
              option-value="id"
              option-text="name"
              placeholder="Select device type..."
            ></model-list-select>
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!--modalAdd-------->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import BtnManageDevice from "./../../modal_vue/modal_mac_manage_device.vue";
import BtnManageDeploy from "./../../modal_vue/modal_mac_manage_deployment.vue";
import BtnManageLocation from "./../../modal_vue/modal_mac_manage_location.vue";

export default {
  components: {
    BtnManageDevice,
    BtnManageDeploy,
    BtnManageLocation,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "pmx_ip", label: "PMX IP", sortable: true },
        { key: "ip", label: "IP", sortable: true },
        { key: "mac_desc", label: "Description", sortable: true },
        { key: "port_desc", label: "Port Descrption", sortable: true },
        { key: "mac_address_main", label: "MAC Address", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 20,
      pageOptions: [10, 20, 50, 100],
      addItem: {
        pmx_ip: "",
        ip: "",
        mac_desc: "",
        port_desc: "",
        location: {},
        deploy: {},
        device: {}
      },
      editItem: {
        pmx_ip: "",
        ip: "",
        mac_desc: "",
        port_desc: ""
      },
      location_list: [],
      deploy_list: [],
      device_list: [],
      filter_location: "",
      filter_deploy: "",
      filter_device: "",
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.load();
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.loadlist();
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
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarComponents").style.backgroundColor =
            "#2196f3";
        }, 100);
      });
      this.$http.get("api/MacTable").then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
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
      this.$bvModal.show("modalEdit");
      this.editItem = item;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      console.log(this.addItem);

      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.$http
            .post("api/MacTable", this.addItem)
            .then(response => {
              swal("Created", "", "success");
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
                  this.tblisBusy = false;
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
        dangerMode: true
      }).then(update => {
        if (update) {
          this.$http
            .put("api/MacTable/" + this.editItem.id, this.editItem)
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
    },
    clearFilter() {
      this.tblFilter = "";
      this.filter_location = "";
      this.filter_deploy = "";
      this.filter_device = "";
    },
    filterChange() {
      this.tblFilter = "D6:7E:C" + this.filter_location;
      if (this.filter_deploy != "") this.tblFilter += ":" + this.filter_deploy;
      if (this.filter_device != "") this.tblFilter += this.filter_device;
    },
    loadlist() {
      this.$http.get("api/MacLocation").then(function(response) {
        this.location_list = response.body;
      });

      this.$http.get("api/DeviceType").then(function(response) {
        this.device_list = response.body;
      });

      this.$http.get("api/MacDeploymentIdentifier").then(function(response) {
        this.deploy_list = response.body;
      });
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
