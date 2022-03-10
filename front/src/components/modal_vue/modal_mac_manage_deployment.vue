<template>
  <div>
    <button
      v-b-modal="'mainModalDeploymentIdentifier'"
      type="button"
      style="margin-top:-20px;"
      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Manage Deployment Identifier</button>
    <b-modal
      id="mainModalDeploymentIdentifier"
      size="xl"
      title="Manage Deployment Identifier"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <!-- ADD Identifier-------------------------------------------------------------------------------------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">Add Identifier</p>
        </div>
        <div class="elClr panel-body">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Identifier name:</p>
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
      <!-- END ADD Identifier------------------------------->
      <hr />
      <!--Form-------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">View Identifier</p>
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

    <!--ModalEditDeploy------------------------------------------------------------------------------------>
    <b-modal
      id="ModalEditDeploy"
      centered
      title="Modify Deployment Identifier"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Identifier name:</p>
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
    <!--End ModalEditDeploy-->
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
      this.$http.get("api/MacDeploymentIdentifier").then(function(response) {
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
            .post("api/MacDeploymentIdentifier", this.addDevice)
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
      console.log(this.editDevice);

      swal({
        title: "Are you sure?",
        text: "Do you want to Update this Identifier?",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.tblisBusy = true;
          this.editDevice.user_id = this.user.id;
          this.editDevice.user_name = this.user.name;
          this.$http
            .put(
              "api/MacDeploymentIdentifier/" + this.editDevice.id,
              this.editDevice
            )
            .then(response => {
              this.device_items = response.body;
              swal("Update!", "", "success");
              this.$bvModal.hide("ModalEditDeploy");
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
      this.$bvModal.show("ModalEditDeploy");
    },
    delDevice(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this Identifier",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .delete("api/MacDeploymentIdentifier/" + item.id)
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


