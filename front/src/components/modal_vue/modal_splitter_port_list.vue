<template>
  <div>
    <b-modal
      id="modal_splitter_port_list"
      size="lg"
      title="Splitter Port List(LCP)"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <br />
      <!-- //table here -->
      <div>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="splitters_field"
          :items="data"
          :busy="splitters_port_tblisBusy"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
          @row-clicked="splitters_port_tblRowClicked"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>

          <span slot="status" slot-scope="data" v-html="data.value"></span>
          <span slot="action" slot-scope="data" v-html="data.value"></span>

          <template v-slot:cell(action)="row">
            <div v-if="roles.operator">
              <span v-if="data.item.parent == 'olt'">
                <b-button
                  variant="success"
                  size="sm"
                  v-if="row.item.going == 'VACANT'"
                  @click="btnInsertNap(row.item)"
                >Insert NAP</b-button>

                <b-button
                  variant="warning"
                  size="sm"
                  v-else
                  @click="unpatch(row.item.going_id)"
                >Delete NAP</b-button>
              </span>
              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PU' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PC')"
              >Confirm port</b-button>
              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PC' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PU')"
              >Unconfirm port</b-button>
            </div>
          </template>

          <template v-slot:cell(port_number)="row">
            <span>{{ row.item.port_number }}</span>
          </template>

          <template v-slot:cell(status)="row">
            <b-button
              variant="success"
              size="sm"
              v-if="row.item.status == 'Active'"
              disabled
            >{{ row.item.status }}</b-button>
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Disconnected'"
              disabled
            >{{ row.item.status }}</b-button>
            <b-button
              variant="info"
              size="sm"
              v-if="row.item.status == 'VACANT'"
              disabled
            >{{ row.item.status }}</b-button>
          </template>

          <template slot="table-caption"></template>
        </b-table>
      </div>

      <div slot="modal-footer" slot-scope="{}"></div>
    </b-modal>

    <b-modal
      id="modal_attach_port"
      size="lg"
      title="Attach port"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="patch_type"
            v-model="patch_type_selected"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div v-if="patch_type_selected == 'Client'">
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
      </div>
      <div v-if="patch_type_selected != ''">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">LOS:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" placeholder="LOS" v-model.trim="los" />
          </div>
        </div>
      </div>
      <div slot="modal-footer" slot-scope="{}">
        <b-button variant="success" size="sm" @click="btnPatch_OK()">Patch</b-button>
      </div>
    </b-modal>

    <b-modal
      id="modal_attach_nap"
      size="lg"
      title="Attach NAP"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Select where to attach the splitter(NAP):</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            style="min-width: 150px; max-width: 150px;"
            :list="nap_attach_to_list"
            v-model="nap.attach_to"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>
      <div v-if="nap.attach_to == 'closure'">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">New Closure or Existing?:</p>
          </div>
          <div class="col-lg-9">
            <p-radio
              class="textLabel p-default p-curve"
              v-model="nap.closure.isNew"
              value="0"
              name="isNew"
              color="success-o"
            >New</p-radio>

            <p-radio
              class="textLabel p-default p-curve"
              v-model="nap.closure.isNew"
              value="1"
              name="isNew"
              color="success-o"
            >Existing</p-radio>
          </div>
        </div>
        <div v-if="nap.closure.isNew == '0'">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Name:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Closure name"
                placeholder="Closure name"
                v-model.trim="nap.closure.name"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Type:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="closure_types"
                v-model="nap.closure.closure_type_id"
                option-value="id"
                option-text="name"
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Coordinate type:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                style="min-width: 150px; max-width: 150px;"
                :list="coor_type_list"
                v-model="nap.closure.coor_type"
                option-value="id"
                option-text="name"
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="nap.closure.coor_type == 1">
            <div class="col-lg-3">
              <p class="textLabel">Latitude:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Latitude"
                v-model.trim="nap.closure.lat"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="nap.closure.coor_type == 1">
            <div class="col-lg-3">
              <p class="textLabel">Longitude:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Longitude"
                v-model.trim="nap.closure.lng"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="nap.closure.coor_type == 2">
            <div class="col-lg-3">
              <p class="textLabel">(lat,lng):</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Latitude,Longitude"
                v-model="latlng"
                v-on:keyup.enter="updateMarker_add('latlng', 'nap')"
              />
            </div>
          </div>
          <div class="rowFields mx-auto row" v-if="nap.closure.coor_type == 3">
            <div class="col-lg-3">
              <p class="textLabel">(lng,lat):</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Longitude,Latitude"
                v-model="lnglat"
                v-on:keyup.enter="updateMarker_add('lnglat', 'nap')"
              />
            </div>
          </div>
        </div>
        <div v-if="nap.closure.isNew == '1'">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Select Closure Name:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="closures"
                v-model="nap.attach_id"
                option-value="id"
                option-text="name"
              ></model-list-select>
            </div>
          </div>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Port type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="port_types"
            v-model="nap.port_type"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Status:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="status_list"
            v-model="nap.status"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div>
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">LOS:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" placeholder="LOS" v-model.trim="los" />
          </div>
        </div>
      </div>
      <div slot="modal-footer" slot-scope="{}">
        <b-button variant="success" size="sm" @click="btnCreateNap()">Create</b-button>
      </div>
    </b-modal>

    <!-- // -->
    <!-- // -->
    <!-- // -->
    <!-- // -->
    <!-- // -->
    <!-- // -->

    <b-modal
      id="modal_splitter_port_list_nap"
      size="lg"
      title="Splitter Port List(NAP)"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <br />
      <!-- //table here -->
      <div>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="splitters_field_nap"
          :items="naps"
          :busy="splitters_port_nap_tblisBusy"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>

          <span slot="status" slot-scope="data" v-html="data.value"></span>
          <span slot="action" slot-scope="data" v-html="data.value"></span>

          <template v-slot:cell(action)="row">
            <div v-if="roles.operator">
              <b-button
                variant="success"
                size="sm"
                v-if="row.item.going == 'VACANT'"
                @click="btnPatch(row.item)"
              >Patch</b-button>

              <b-button
                variant="warning"
                size="sm"
                v-else
                @click="unpatchClient(row.item.id)"
              >Unpatch</b-button>

              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PU' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PC')"
              >Confirm port</b-button>
              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PC' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PU')"
              >Unconfirm port</b-button>
            </div>
          </template>

          <template v-slot:cell(port_number)="row">
            <span>{{ row.item.port_number }}</span>
          </template>

          <template v-slot:cell(status)="row">
            <b-button
              variant="success"
              size="sm"
              v-if="row.item.status == 'Active'"
              disabled
            >{{ row.item.status }}</b-button>
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Disconnected'"
              disabled
            >{{ row.item.status }}</b-button>
            <b-button
              variant="info"
              size="sm"
              v-if="row.item.status == 'VACANT'"
              disabled
            >{{ row.item.status }}</b-button>
          </template>

          <template slot="table-caption"></template>
        </b-table>
      </div>

      <div slot="modal-footer" slot-scope="{}"></div>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";

export default {
  props: ["data", "splitters_field"],
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      buffer_color: [
        {
          no: 1,
          color: "blue"
        },
        {
          no: 2,
          color: "Orange"
        },
        {
          no: 3,
          color: "Green"
        },
        {
          no: 4,
          color: "Brown"
        }
      ],
      core_color: [
        {
          no: 1,
          color: "Blue"
        },
        {
          no: 2,
          color: "Orange"
        },
        {
          no: 3,
          color: "Green"
        },
        {
          no: 4,
          color: "Brown"
        },
        {
          no: 5,
          color: "Slate"
        },
        {
          no: 6,
          color: "White"
        },
        {
          no: 7,
          color: "Red"
        },
        {
          no: 8,
          color: "Black"
        },
        {
          no: 9,
          color: "Yellow"
        },
        {
          no: 10,
          color: "Violet"
        },
        {
          no: 11,
          color: "Rose"
        },
        {
          no: 12,
          color: "Aqua"
        }
      ],
      patch_type: [
        {
          name: "Hard fiber core",
          id: "hardfiber_core"
        },
        {
          name: "Drop Fiber (Client)",
          id: "Client"
        }
      ],
      clients: [],
      clientSelected: {
        id: "",
        region_id: "",
        package_type: {
          name: ""
        }
      },
      naps: {},
      nap: {
        attach_to: "",
        attach_id: "",
        splitter_type_id: "3",
        port_type: "1x8",
        parent: "splitter",
        parent_id: "",
        status: "Active"
      },
      nap_attach_to_list: [
        {
          id: "closure",
          name: "Closure/DP"
        },
        {
          id: "Cabinet",
          name: "cabinet"
        },
        {
          id: "node",
          name: "Node"
        }
      ],
      port_types: [
        {
          name: "1x16",
          id: "1x16"
        },
        {
          name: "1x8",
          id: "1x8"
        },
        {
          name: "1x4",
          id: "1x4"
        }
      ],
      status_list: [
        {
          name: "Active",
          id: "Active"
        },
        {
          name: "Not Active",
          id: "Not Active"
        }
      ],
      latlng: "",
      lnglat: "",
      coor_type: 3,
      coor_type_list: [
        {
          id: 1,
          name: "lat and lng"
        },
        {
          id: 2,
          name: "(lat,lng):"
        },
        {
          id: 3,
          name: "(lng,lat):"
        }
      ],
      splitters_field_nap: [
        { key: "port_number", label: "Port#", sortable: true },
        { key: "going", label: "Going To", sortable: true },
        { key: "name", label: "Name", sortable: true },
        { key: "los", label: "LOS", sortable: true },
        { key: "status", label: "Status", sortable: true },
        { key: "port_status", label: "Port", sortable: true },
        { key: "action", label: "Action", sortable: true }
      ],
      closures: [],
      los: "",
      port_clicked: "",
      patch_type_selected: "",
      splitters_port_item: [],
      splitters_port_tblisBusy: false,
      splitters_port_nap_tblisBusy: false,
      closure_types: [],
      user: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
    this.loadClients();
    this.splitters_item = this.data.splitter_closure;
  },
  mounted() {
    this.$root.$on("update_naps", naps => {
      this.naps = naps;
    });
  },
  methods: {
    load() {
      this.$http.get("api/ClosureType").then(response => {
        this.closure_types = response.body;
      });
      this.$http.get("api/Closure").then(response => {
        this.closures = response.body;
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
    save_data() {
      this.data.user_id = this.user.id;
      this.data.user_name = this.user.name;
      this.data.lat = this.data.position.lat;
      this.data.lng = this.data.position.lng;
      this.$http
        .post("api/Closure", this.data)
        .then(response => {
          swal("Created", "", "success");
          this.data.save = true;

          this.$bvModal.hide("modal_closure_marker_click");
          console.log(response.body);
          this.$root.$emit("update_closure", response.body, this.data.index);
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    update_closure() {
      console.log(this.data);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.data.user_id = this.user.id;
          this.data.user_name = this.user.name;
          this.data.lat = this.data.position.lat;
          this.data.lng = this.data.position.lng;
          this.$http
            .put("api/Closure/" + this.data.id, this.data)
            .then(response => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modal_closure_marker_click");
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
    delete_closure() {
      if (this.roles.rm) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.$http
              .delete("api/Closure/" + this.data.id)
              .then(response => {
                this.$bvModal.hide("modal_closure_marker_click");
                this.$root.$emit("delete_closure", this.data.index);
                swal("Deleted", "", "success");
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
          text: "You are not allow to delete a Closure Type",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    splitters_port_tblRowClicked(item, index, event) {
      console.log(item);

      if (item.name != "VACANT") {
        this.$root.$emit("pageLoading");
        this.$http
          .get("api/SplitterPort/" + item.going_id)
          .then(response => {
            console.log(response.body);
            this.naps = response.body;
            this.naps.item = item;
            this.$bvModal.show("modal_splitter_port_list_nap");
            this.$root.$emit("pageLoaded");
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            });
            this.$root.$emit("pageLoaded");
          });
      }
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.rm) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    btnPatch(item) {
      this.port_clicked = item;
      this.$bvModal.show("modal_attach_port");
    },
    btnPatch_OK() {
      var going_id = "0";
      if (this.patch_type_selected == "Client")
        going_id = this.clientSelected.id;
      var val = {
        splitter_id: this.port_clicked.splitter_id,
        port_number: this.port_clicked.port_number,
        going: this.patch_type_selected,
        going_id: going_id,
        los: this.los
      };
      // console.log(val);
      this.$http.post("api/SplitterPort", val).then(response => {
        if (response.body == "Error") {
          swal(
            "Client is patched to another port!",
            "Please unpatched client to it's existing port.",
            "warning"
          );
          this.$bvModal.hide("modal_attach_port");
        } else {
          swal("Patched", "", "success");
          this.$bvModal.hide("modal_attach_port");
          this.$bvModal.hide("modal_splitter_port_list_nap");
        }
      });
    },
    unpatch(id) {
      if (this.roles.rm) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.$http.delete("api/Splitter/" + id).then(response => {
              swal("Deleted", "", "success");
              this.$bvModal.hide("modal_attach_port");
              this.$bvModal.hide("modal_splitter_port_list_nap");
            });
          }
        });
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    unpatchClient(id) {
      if (this.roles.rm) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.$http.delete("api/SplitterPort/" + id).then(response => {
              swal("Deleted", "", "success");
              this.$bvModal.hide("modal_attach_port");
              this.$bvModal.hide("modal_splitter_port_list_nap");
            });
          }
        });
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    change_port_status(row, stat) {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "info",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          row.item.port_status = stat;
          this.$http
            .put("api/SplitterPort/" + row.item.id, row.item)
            .then(response => {
              swal("Updated", "", "success");
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
    btnInsertNap(item) {
      this.port_clicked = item;
      this.nap = {
        closure: {
          isNew: null,
          name: "",
          closure_type_id: "",
          lat: "",
          lng: "",
          coor_type: 1
        },
        splitter_port: {
          splitter_id: "",
          port_number: 0,
          going: "splitter",
          going_id: 0,
          los: 0
        },
        attach_to: "",
        attach_id: "",
        splitter_type_id: "3",
        port_type: "1x8",
        parent: "splitter",
        parent_id: "",
        status: "Active"
      };
      this.$bvModal.show("modal_attach_nap");
    },
    btnCreateNap() {
      this.nap.parent_id = this.data.item.id;
      this.nap.attached_port = this.port_clicked.port_number;
      this.nap.splitter_port.splitter_id = this.data.item.id;
      this.nap.splitter_port.port_number = this.port_clicked.port_number;
      this.nap.splitter_port.going_id = 0; //get after insert splitter
      this.nap.splitter_port.los = this.los;

      var insertOK = "no";
      if (this.nap.attach_to == "closure") {
        if (this.nap.closure.isNew != null) {
          if (this.nap.closure.isNew == 0) {
            if (
              this.nap.closure.name != "" &&
              this.nap.closure.closure_type_id != "" &&
              this.nap.closure.lat != "" &&
              this.nap.closure.lng != ""
            )
              insertOK = "yes";
            else swal("Please fill up all the necessary fields");
          } else {
            if (this.nap.attach_id != "") insertOK = "yes";
            else swal("Please select closure name");
          }
        } else swal("Please select new or existing closure");
      } else swal("for now the available attach nap is in the closure/DP.");

      if (insertOK == "yes") {
        this.$root.$emit("pageLoading");
        this.nap.user_id = this.user.id;
        this.nap.user_name = this.user.name;
        this.$http
          .post("api/Splitter/modiStore", this.nap)
          .then(response => {
            swal("Created", "", "success");
            this.$bvModal.hide("modal_splitter_port_list");
            this.$bvModal.hide("modal_attach_nap");
            this.$root.$emit("pageLoaded");
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            });
            this.$root.$emit("pageLoaded");
          });
      }
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
</style>
