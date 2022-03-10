<template>
  <div>
    <b-modal
      id="modal_node_marker_click"
      size="lg"
      title="Node"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Name:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            v-b-tooltip.hover
            title="Node name"
            placeholder="Node name"
            v-model.trim="data.name"
          />
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
            v-model="coor_type"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row" v-if="coor_type == 1">
        <div class="col-lg-3">
          <p class="textLabel">Latitude:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            placeholder="Latitude"
            v-model.trim="data.position.lat"
          />
        </div>
      </div>

      <div class="rowFields mx-auto row" v-if="coor_type == 1">
        <div class="col-lg-3">
          <p class="textLabel">Longitude:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            placeholder="Longitude"
            v-model.trim="data.position.lng"
          />
        </div>
      </div>

      <div class="rowFields mx-auto row" v-if="coor_type == 2">
        <div class="col-lg-3">
          <p class="textLabel">(lat,lng):</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            placeholder="Latitude,Longitude"
            v-model="latlng"
            v-on:keyup.enter="updateMarker_add('latlng', 'node')"
          />
        </div>
      </div>
      <div class="rowFields mx-auto row" v-if="coor_type == 3">
        <div class="col-lg-3">
          <p class="textLabel">(lng,lat):</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            placeholder="Longitude,Latitude"
            v-model="lnglat"
            v-on:keyup.enter="updateMarker_add('lnglat', 'node')"
          />
        </div>
      </div>

      <br />
      <br />
      <!-- table here -->
      <div v-if="data.save">
        <h5>OLT List</h5>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="olt_field"
          :items="data.olts"
          :busy="olt_tblisBusy"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
          @row-clicked="olt_tblRowClicked"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template v-slot:cell(action)="row">
            <b-button variant="success" size="sm" v-if="roles.rm" @click="deleteOLT(row)">Delete</b-button>
          </template>

          <template v-slot:cell(status)="row">
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Not Active'"
              disabled
            >Not Active</b-button>
            <b-button variant="success" size="sm" v-else disabled>Active</b-button>
          </template>
        </b-table>
      </div>

      <div slot="modal-footer" slot-scope="{}">
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator && data.isMap"
          @click="insert_hardfiber"
        >Insert Hard Fiber</button>
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator"
          @click="openModal('modalAddOLT')"
        >Insert OLT</button>
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator"
          @click="update_node"
        >Update</button>
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.rm"
          @click="delete_node"
        >Delete</button>
        <button class="btn btn-success btn-labeled" v-if="!data.save" @click="save_data">Save</button>
      </div>
    </b-modal>

    <!--modalAddOLT-------->
    <b-modal
      id="modalAddOLT"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="lg"
    >
      <div slot="modal-header">
        <h5 v-if="olt.isNew">OLT Form</h5>
        <h5 v-else>Manage OLT</h5>
      </div>
      <!--Form-------->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Description:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="description"
            class="form-control"
            placeholder="Description"
            v-validate="'required'"
            v-model.trim="olt.name"
          />
          <small
            class="text-danger pull-left"
            v-show="errors.has('description')"
          >Description is required.</small>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">IP Address:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            name="ip_field"
            class="form-control"
            placeholder="IP Address"
            v-validate="'ip'"
            data-vv-as="ip"
            v-model.trim="olt.ip"
          />
          <small class="text-danger pull-left" v-show="errors.has('ip_field')">Invalid IP Address.</small>
        </div>
      </div>

      <!-- table here -->
      <div v-if="!olt.isNew">
        <h5>LCP List</h5>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="lcp_field"
          :items="olt.splitter_lcp"
          :busy="lcp_tblisBusy"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
          @row-clicked="lcp_tblRowClicked"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template v-slot:cell(action)="row">
            <b-button
              variant="success"
              size="sm"
              v-if="roles.rm"
              @click="deleteSplitter(row)"
            >Delete</b-button>

            <b-button
              variant="success"
              size="sm"
              v-if="roles.operator && row.item.status == 'Not Active'"
              @click="activate_splitter(row)"
            >Activate</b-button>
          </template>

          <template v-slot:cell(status)="row">
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Not Active'"
              disabled
            >Not Active</b-button>
            <b-button variant="success" size="sm" v-else disabled>Active</b-button>
          </template>
        </b-table>
      </div>

      <!--Form-------->
      <template slot="modal-footer" slot-scope="{}">
        <b-button
          size="sm"
          variant="success"
          v-if="olt.isNew && roles.operator"
          @click="btnCreateOLT()"
        >Create</b-button>
        <b-button
          size="sm"
          variant="success"
          v-if="!olt.isNew && roles.operator"
          @click="openModal('modalAddLCP')"
        >Insert LCP</b-button>
        <b-button
          size="sm"
          variant="success"
          v-if="!olt.isNew && roles.operator"
          @click="btnUpdateOLT()"
        >Update</b-button>
      </template>
    </b-modal>
    <!--modalAddOLT-------->

    <!--modalAddLCP-------->
    <b-modal
      id="modalAddLCP"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="lg"
    >
      <div slot="modal-header">
        <h5 v-if="lcp.isNew">LCP Form</h5>
        <h5 v-else>Manage LCP</h5>
      </div>
      <!--Form-------->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Select where to attach the splitter(LCP):</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            style="min-width: 150px; max-width: 150px;"
            :list="lcp_attach_to_list"
            v-model="lcp.attach_to"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>
      <div v-if="lcp.attach_to == 'closure'">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">New Closure or Existing?:</p>
          </div>
          <div class="col-lg-9">
            <p-radio
              class="textLabel p-default p-curve"
              v-model="lcp.closure.isNew"
              value="0"
              name="isNew"
              color="success-o"
            >New</p-radio>

            <p-radio
              class="textLabel p-default p-curve"
              v-model="lcp.closure.isNew"
              value="1"
              name="isNew"
              color="success-o"
            >Existing</p-radio>
          </div>
        </div>
        <div v-if="lcp.closure.isNew == '0'">
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
                v-model.trim="lcp.closure.name"
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
                v-model="lcp.closure.closure_type_id"
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
                v-model="lcp.closure.coor_type"
                option-value="id"
                option-text="name"
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="lcp.closure.coor_type == 1">
            <div class="col-lg-3">
              <p class="textLabel">Latitude:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Latitude"
                v-model.trim="lcp.closure.lat"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="lcp.closure.coor_type == 1">
            <div class="col-lg-3">
              <p class="textLabel">Longitude:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Longitude"
                v-model.trim="lcp.closure.lng"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row" v-if="lcp.closure.coor_type == 2">
            <div class="col-lg-3">
              <p class="textLabel">(lat,lng):</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Latitude,Longitude"
                v-model="latlng"
                v-on:keyup.enter="updateMarker_add('latlng', 'lcp')"
              />
            </div>
          </div>
          <div class="rowFields mx-auto row" v-if="lcp.closure.coor_type == 3">
            <div class="col-lg-3">
              <p class="textLabel">(lng,lat):</p>
            </div>
            <div class="col-lg-9">
              <input
                type="text"
                class="form-control"
                placeholder="Longitude,Latitude"
                v-model="lnglat"
                v-on:keyup.enter="updateMarker_add('lnglat', 'lcp')"
              />
            </div>
          </div>
        </div>
        <div v-if="lcp.closure.isNew == '1'">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Select Closure Name:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="closures"
                v-model="lcp.attach_id"
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
            v-model="lcp.port_type"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Attached PON:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="number"
            class="form-control"
            v-b-tooltip.hover
            title="PON number/port of the OLT / uplink of the splitter"
            placeholder="Attached PON"
            v-model.trim="lcp.attached_port"
          />
        </div>
      </div>
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Status:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="status_list"
            v-model="lcp.status"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>
      <!--Form-------->
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" v-if="lcp.isNew" @click="btnCreateLCP()">Create</b-button>
        <b-button size="sm" variant="success" v-if="!lcp.isNew" @click="openModal()">Add LCP</b-button>
        <b-button size="sm" variant="success" v-if="!lcp.isNew" @click="btnUpdateLCP()">Update</b-button>
      </template>
    </b-modal>
    <!--modalAddLCP-------->
    <modal_splitter_port_list v-bind:data="lcp" v-bind:splitters_field="lcp.splitters_field"></modal_splitter_port_list>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import modal_splitter_port_list from "./modal_splitter_port_list.vue";
import VueRangedatePicker from "vue-rangedate-picker";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";

export default {
  props: ["data"],
  components: {
    modal_splitter_port_list: modal_splitter_port_list,
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
      olt_field: [{ key: "name" }, { key: "ip" }, { key: "action" }],
      olt_item: [],
      olt: {
        isNew: true,
        name: "",
        ip: "",
        node_id: ""
      },
      item: {},
      olt_tblisBusy: false,
      closure_types: [],
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
      lcp_field: [
        {
          key: "name",
          label: "Attached To"
        },
        {
          key: "splitter_type_id",
          formatter: value => {
            if (value == "3") return "NAP";
            else if (value == "4") return "LCP";
            else return value;
          },
          label: "Splitter Type"
        },
        {
          key: "attached_port",
          label: "PON #",
          sortable: true
        },
        { key: "port_type" },
        { key: "status" },
        { key: "action" }
      ],
      lcps: [],
      lcp_tblisBusy: false,
      lcp: {
        closure: {
          isNew: null,
          name: "",
          closure_type_id: "",
          lat: "",
          lng: "",
          coor_type: 1
        },
        isNew: true,
        attach_to: "",
        attach_id: "",
        splitter_type_id: "4",
        port_type: "1x8",
        parent: "olt",
        parent_id: "",
        attached_port: 0,
        status: ""
      },
      lcp_attach_to_list: [
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
      lcp_port_type_list: [
        {
          id: "1x4",
          name: "1x4"
        },
        {
          id: "1x8",
          name: "1x8"
        },
        {
          id: "1x16",
          name: "1x16"
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
      closures: [],
      user: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
    this.olt_item = this.data.olts;
  },
  mounted() {
    this.$root.$on("update_lcp", lcp => {
      this.lcp = lcp;
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
    save_data() {
      this.data.user_id = this.user.id;
      this.data.user_name = this.user.name;
      this.data.lat = this.data.position.lat;
      this.data.lng = this.data.position.lng;
      this.$http
        .post("api/Node", this.data)
        .then(response => {
          swal("Created", "", "success");
          this.data.save = true;

          this.$bvModal.hide("modal_node_marker_click");
          console.log(response.body);
          this.$root.$emit("update_node", response.body, this.data.index);
        })
        .catch(response => {
          console.log(response.body);
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    update_node() {
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
            .put("api/Node/" + this.data.id, this.data)
            .then(response => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modal_node_marker_click");
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
    delete_node() {
      console.log(this.data);
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
              .delete("api/Node/" + this.data.id)
              .then(response => {
                this.$bvModal.hide("modal_node_marker_click");
                this.$root.$emit("delete_node", this.data.index);
                swal("Deleted", "", "success");
              })
              .catch(response => {
                swal({
                  title: "Error",
                  text: response.body.error,
                  icon: "error",
                  dangerMode: true
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
    insert_hardfiber() {
      this.$root.$emit("draw_line", this.data);
      this.$bvModal.hide("modal_closure_marker_click");
    },
    olt_tblRowClicked(item, index, event) {
      this.$bvModal.show("modalAddOLT");
      this.olt = item;
      this.olt.isNew = false;
      console.log(this.olt);
    },
    lcp_tblRowClicked(item, index, event) {
      console.log(item);
      this.$root.$emit("pageLoading");
      this.$http.get("api/SplitterPort/" + item.id).then(response => {
        this.lcp = response.body;
        this.lcp.item = item;
        this.lcp.splitters_field = [
          { key: "port_number", label: "Port#", sortable: true },
          { key: "attach_to", label: "Attached To", sortable: true },
          { key: "los", label: "LOS", sortable: true },
          { key: "status", label: "Status", sortable: true },
          { key: "port_status", label: "Port", sortable: true },
          { key: "action", label: "Action", sortable: true }
        ];
        console.log(this.lcp);
        this.$bvModal.show("modal_splitter_port_list");
        this.$root.$emit("pageLoaded");
      });
      // .catch(response => {
      //   swal({
      //     title: "Error",
      //     text: response.body.error,
      //     icon: "error",
      //     dangerMode: true
      //   });
      //   this.$root.$emit("pageLoaded");
      // });
      this.$root.$emit("pageLoaded");
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.operator) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    deleteOLT(row) {
      swal({
        title: "Do you really want to delete this item?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$http
            .delete("api/olt/" + row.item.id)
            .then(response => {
              this.data.olts.splice(row.index, 1);
              swal("Deleted", "", "success");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
            });
        }
      });
    },
    activate_splitter(row) {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "info",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          row.item.status = "Active";
          this.$http
            .put("api/Splitter/" + row.item.id, row.item)
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
    updateMarker_add(txt, caller) {
      if (caller == "node") {
        if (txt == "latlng") {
          var temp = this.latlng.split(",");
          this.data.position.lat = parseFloat(temp[0]);
          this.data.position.lng = parseFloat(temp[1]);
        }
        if (txt == "lnglat") {
          var temp = this.lnglat.split(",");
          this.data.position.lat = parseFloat(temp[1]);
          this.data.position.lng = parseFloat(temp[0]);
        }
      }
      if (caller == "lcp") {
        if (txt == "latlng") {
          var temp = this.latlng.split(",");
          this.lcp.closure.lat = parseFloat(temp[0]);
          this.lcp.closure.lng = parseFloat(temp[1]);
        }
        if (txt == "lnglat") {
          var temp = this.lnglat.split(",");
          this.lcp.closure.lat = parseFloat(temp[1]);
          this.lcp.closure.lng = parseFloat(temp[0]);
        }
      }
    },
    clearData(txt) {
      if (txt == "olt") {
        this.olt = {
          isNew: true,
          name: "",
          ip: "",
          node_id: ""
        };
      }
      if (txt == "lcp") {
        this.lcp = {
          closure: {
            isNew: null,
            name: "",
            closure_type_id: "",
            lat: "",
            lng: "",
            coor_type: 1
          },
          isNew: true,
          attach_to: "",
          attach_id: "",
          splitter_type_id: "4",
          port_type: "1x8",
          parent: "olt",
          parent_id: "",
          attached_port: 0,
          status: "Active"
        };
      }
    },
    openModal(modal) {
      if (modal == "modalAddOLT") this.clearData("olt");
      if (modal == "modalAddLCP") this.clearData("lcp");
      this.$bvModal.show(modal);
    },
    btnCreateOLT() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.olt_tblisBusy = true;
          this.olt.user_id = this.user.id;
          this.olt.user_name = this.user.name;
          this.olt.node_id = this.data.id;
          this.$http
            .post("api/olt", this.olt)
            .then(response => {
              swal("Created", "", "success");
              this.data.olts = response.body;
              this.clearData("olt");
              this.olt_tblisBusy = false;
              this.$root.$emit("pageLoaded");
              this.$bvModal.hide("modalAddOLT");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.olt_tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    btnUpdateOLT() {
      console.log(this.olt);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.olt.user_id = this.user.id;
          this.olt.user_name = this.user.name;
          this.$http
            .put("api/olt/" + this.olt.id, this.olt)
            .then(response => {
              swal("Updated", "", "success");
              this.$bvModal.hide("modalAddOLT");
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
    btnCreateLCP() {
      console.log(this.lcp);
      this.lcp.parent_id = this.olt.id;
      var insertOK = "no";
      if (this.lcp.attach_to == "closure") {
        if (this.lcp.closure.isNew != null) {
          if (this.lcp.closure.isNew == 0) {
            if (
              this.lcp.closure.name != "" &&
              this.lcp.closure.closure_type_id != "" &&
              this.lcp.closure.lat != "" &&
              this.lcp.closure.lng != ""
            )
              insertOK = "yes";
            else swal("Please fill up all the necessary fields");
          } else {
            if (this.lcp.attach_id != "") insertOK = "yes";
            else swal("Please select closure name");
          }
        } else swal("Please select new or existing closure");
      } else swal("for now the available attach lcp is in the closure/DP.");

      if (insertOK == "yes") {
        this.$root.$emit("pageLoading");
        this.lcp.user_id = this.user.id;
        this.lcp.user_name = this.user.name;
        this.$http
          .post("api/Splitter/modiStore", this.lcp)
          .then(response => {
            swal("Created", "", "success");
            this.olt.splitter_lcp = response.body;
            this.$root.$emit("pageLoaded");
            this.$bvModal.hide("modalAddLCP");
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
    btnUpdateLCP() {},
    deleteSplitter(row) {
      swal({
        title: "Do you really want to delete this item?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.items = [];
          this.tblisBusy = true;
          this.$http
            .delete("api/Splitter/" + row.item.id)
            .then(response => {
              this.data.splitter_closure.splice(row.index, 1);
              swal("Deleted", "", "success");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
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
