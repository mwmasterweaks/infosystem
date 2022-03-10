<template>
  <div>
    <button
      v-b-modal="'mainModalDevices'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Manage Devices</button>
    <b-modal
      id="mainModalDevices"
      size="xl"
      title="Manage Devices"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <!-- ADD Device-------------------------------------------------------------------------------------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">Add Device</p>
        </div>
        <div class="elClr panel-body">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Device name:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                name="name"
                class="form-control"
                v-validate="'required'"
                v-model.trim="addDevice.name"
              />
              <small
                class="text-danger pull-left"
                v-show="errors.has('name')"
              >Device name is required.</small>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Hex Number:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                name="hex_number"
                class="form-control"
                v-validate="'required'"
                :maxlength="'1'"
                v-model.trim="addDevice.hex_number"
              />
              <small
                class="text-danger pull-left"
                v-show="errors.has('hex_number')"
              >Hex Number is required.</small>
            </div>
          </div>
        </div>
        <div class="elClr panel-footer">
          <div class="heading-elements">
            <button
              type="button"
              class="btn btn-success btn-labeled pull-right"
              v-on:click="btnAddDevice"
            >ADD</button>
          </div>
        </div>
      </div>
      <!-- END ADD Device------------------------------->
      <hr />
      <!--Form-------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">View Devices</p>
        </div>
        <div class="elClr panel-body">
          <div>
            <b-table
              class="elClr"
              striped
              show-empty
              hover
              outlined
              :fields="device_field"
              :items="device_items"
              :busy="tblisBusy"
              head-variant=" elClr"
            >
              <template v-slot:cell(actions)="row">
                <b-button
                  size="sm"
                  variant="info"
                  @click="OpenModalEditDevice(row.item, row.index, $event.target)"
                  class="col-2 pull-right margin-right-10"
                >Update</b-button>
                <b-button
                  size="sm"
                  variant="danger"
                  @click="delDevice(row.item, row.index, $event.target)"
                  class="col-2 pull-right margin-right-10"
                >Delete</b-button>
              </template>

              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
            </b-table>
          </div>
        </div>
        <div class="elClr panel-footer"></div>
      </div>
      <!--Form-------->

      <div slot="modal-footer" slot-scope="{  }"></div>
    </b-modal>

    <!--ModalEditDevice------------------------------------------------------------------------------------>
    <b-modal
      id="ModalEditDevice"
      centered
      title="Modify Device"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Device name:</p>
        </div>
        <div class="col-lg-9">
          <input type="text" name="editname" class="form-control" v-model.trim="editDevice.name" />
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Hex Number:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="edithex_number"
            class="form-control"
            :maxlength="'1'"
            v-model.trim="editDevice.hex_number"
          />
        </div>
      </div>

      <template slot="modal-footer" slot-scope="{  }">
        <b-button size="sm" variant="success" @click="UpdateDevice()">Update</b-button>
      </template>
    </b-modal>
    <!--End ModalEditDevice-->
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      tblisBusy: false,
      device_field: [
        { key: "id", sortable: true },
        { key: "name", sortable: true },
        { key: "hex_number" },
        { key: "actions" }
      ],
      device_items: [],
      addDevice: {
        name: "",
        hex_number: ""
      },
      editDevice: {
        name: "",
        hex_number: ""
      },
      user: [],
      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/DeviceType").then(function(response) {
        this.device_items = response.body;
      });
    },
    btnAddDevice() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.addDevice.user_id = this.user.id;
          this.addDevice.user_name = this.user.name;
          this.$http
            .post("api/DeviceType", this.addDevice)
            .then(response => {
              this.device_items = response.body;
              swal("Added", "", "success");
              this.addDevice.name = "";
              this.addDevice.hex_number = "";
              this.tblisBusy = false;
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.tblisBusy = false;
            });
        }
      });
    },
    UpdateDevice() {
      swal({
        title: "Are you sure?",
        text: "Do you want to Update this Device?",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.tblisBusy = true;
          this.editDevice.user_id = this.user.id;
          this.editDevice.user_name = this.user.name;
          this.$http
            .put("api/DeviceType/" + this.editDevice.id, this.editDevice)
            .then(response => {
              this.device_items = response.body;
              swal("Update!", "", "success");
              this.$bvModal.hide("ModalEditDevice");
              this.tblisBusy = false;
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.tblisBusy = false;
            });
        }
      });
    },
    OpenModalEditDevice(item, index, button) {
      this.editDevice = item;
      this.$bvModal.show("ModalEditDevice");
    },
    delDevice(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this Device",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .delete("api/DeviceType/" + item.id)
            .then(response => {
              swal("Deleted!", "", "success").then(value => {
                this.device_items = response.body;
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
                  this.tblisBusy = false;
                }
              });
            });
        }
      });
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
</style>


