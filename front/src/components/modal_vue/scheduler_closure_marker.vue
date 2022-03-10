<template>
  <div>
    <!-- modal to display and attach to splitters -->
    <b-modal
      id="scheduler_closure_marker"
      size="lg"
      :title="data.name"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      no-close-on-backdrop
      @hidden="reset"
    >
      <!-- //table for splitters here -->
      <div>
        <h5>Splitters List</h5>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="splitters_field"
          :items="data.splitter_closure"
          :busy="splitters_tblisBusy"
          :tbody-tr-class="tblRowClass"
          thead-class="cursorPointer-th"
          head-variant=" elClr"
          @row-clicked="splitters_tblRowClicked"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>

          <template v-slot:cell(status)="row">
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Not Active'"
              disabled
              >Not Active</b-button
            >
            <b-button variant="success" size="sm" v-else disabled
              >Active</b-button
            >
          </template>
        </b-table>
      </div>
      <!-- //table for ports here -->
      <div v-if="napTable">
        <h5>Splitter Port List(NAP)</h5>
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
                >Patch</b-button
              >

              <b-button
                variant="warning"
                size="sm"
                v-else
                :hidden="row.item.going_id != data.client_id"
                @click="unpatchClient(row.item.id)"
                >Unpatch</b-button
              >

              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PU' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PC')"
                :hidden="row.item.going_id != data.client_id"
                >Confirm port</b-button
              >
              <b-button
                variant="success"
                size="sm"
                v-if="
                  row.item.port_status == 'PC' && row.item.port_status != null
                "
                @click="change_port_status(row, 'PU')"
                :hidden="row.item.going_id != data.client_id"
                >Unconfirm port</b-button
              >
              <b-button
                variant="success"
                size="sm"
                disabled
                v-if="
                  row.item.port_status == 'PU' && row.item.port_status != null
                "
                :hidden="row.item.going_id == data.client_id"
                >OCCUPIED</b-button
              >
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
              >{{ row.item.status }}</b-button
            >
            <b-button
              variant="danger"
              size="sm"
              v-if="row.item.status == 'Disconnected'"
              disabled
              >{{ row.item.status }}</b-button
            >
            <b-button
              variant="info"
              size="sm"
              v-if="row.item.status == 'VACANT'"
              disabled
              >{{ row.item.status }}</b-button
            >
          </template>

          <template slot="table-caption"></template>
        </b-table>
      </div>
    </b-modal>
    <!-- modal to patch client to port -->
    <b-modal
      id="modal_patch_port"
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
          <p class="textLabel">Account name:</p>
        </div>
        <div class="col-lg-9">
          <b
            ><p class="text-success textLabel">{{ data.client_name }}</p></b
          >
        </div>
      </div>
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">LOS:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            placeholder="LOS"
            v-model.trim="los"
          />
        </div>
      </div>
      <div slot="modal-footer" slot-scope="{}">
        <b-button variant="success" size="sm" @click="btnPatch_OK()"
          >Patch</b-button
        >
      </div>
    </b-modal>
    <modal_add_splitter v-bind:data="data"></modal_add_splitter>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

//import modal_splitter_port_list from "./modal_splitter_port_list.vue";
import modal_add_splitter from "./modal_add_splitter.vue";
import VueRangedatePicker from "vue-rangedate-picker";

export default {
  props: ["data"],
  components: {
    modal_add_splitter: modal_add_splitter,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
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
      splitters_field: [
        { key: "splitter_type.name", label: "Splitter Type" },
        { key: "port_type" },
        { key: "status" }
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
      splitters_item: [],
      item: {},
      splitters_tblisBusy: false,
      splitters_port_nap_tblisBusy: false,
      closure_types: [],
      latlng: "",
      user: [],
      roles: [],
      napTable: false,
      naps: [],
      port_clicked: "",
      los: 0,
      patch_type_selected: ""
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
    this.splitters_item = this.data.splitter_closure;
  },
  mounted() {
    this.$root.$on("update_naps", naps => {
      this.naps = naps;
    });
    console.log(this.data);
  },
  methods: {
    load() {
      this.$http.get("api/ClosureType").then(response => {
        this.closure_types = response.body;
      });
    },

    splitters_tblRowClicked(item, index, event) {
      console.log(item);

      if (item.splitter_type.name == "LCP") {
        this.$root.$emit("pageLoading");
        this.$http
          .get("api/SplitterPort/" + item.id)
          .then(response => {
            console.log(response.body);
            console.log("function splitters_tblRowClicked in LCP");
            this.item = response.body;
            this.item.item = item;
            this.item.splitters_field = [
              { key: "port_number", label: "Port#", sortable: true },
              { key: "los", label: "LOS", sortable: true },
              { key: "status", label: "Status", sortable: true },
              { key: "port_status", label: "Port", sortable: true },
              { key: "action", label: "Action", sortable: true }
            ];
            this.$root.$emit("update_lcp", this.item);
            // this.$bvModal.show("modal_splitter_port_list");
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
      if (item.splitter_type.name == "NAP") {
        this.$root.$emit("pageLoading");
        this.$http
          .get("api/SplitterPort/" + item.id)
          .then(response => {
            console.log(response.body);
            console.log("function splitters_tblRowClicked in NAP");
            this.item = response.body;
            this.item.item = item;
            this.$root.$emit("update_naps", this.item);
            // this.$bvModal.show("modal_splitter_port_list_nap");
            this.$root.$emit("pageLoaded");
            this.napTable = true;
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
      this.napTable = false;
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.rm) {
        return "elClr cursorPointer";
      } else return "elClr";
    },

    reset() {
      this.napTable = false;
      this.los = 0;
    },
    btnPatch(item) {
      this.port_clicked = item;
      this.$bvModal.show("modal_patch_port");
    },
    btnPatch_OK() {
      var val = {
        splitter_id: this.port_clicked.splitter_id,
        port_number: this.port_clicked.port_number,
        going: "Client",
        going_id: this.data.client_id,
        los: this.los
      };
      console.log(val);
      this.$http.post("api/SplitterPort", val).then(response => {
        console.log(response.body);
        if (response.body == "Error") {
          swal(
            "Client is patched to another port!",
            "Please unpatched client to it's existing port.",
            "warning"
          );
          this.$bvModal.hide("modal_patch_port");
        } else {
          swal("Patched", "", "success");
          this.$bvModal.hide("modal_patch_port");
          this.$bvModal.hide("scheduler_closure_marker");
        }
      });
    },
    unpatchClient(id) {
      console.log(id);
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
              this.$bvModal.hide("modal_patch_port");
              this.$bvModal.hide("scheduler_closure_marker");
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
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
</style>
