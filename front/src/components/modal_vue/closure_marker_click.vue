<template>
  <div>
    <b-modal
      id="modal_closure_marker_click"
      size="lg"
      title="Closure"
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
            title="Closure name"
            placeholder="Closure name"
            v-model.trim="data.name"
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
            v-model="data.closure_type_id"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-2">
          <p class="textLabel">(lat,lng):</p>
        </div>
        <div class="col-lg-4">
          <input
            type="text"
            class="form-control"
            v-model="latlng"
            v-on:keyup.enter="updateMarker_add('latlng')"
          />
        </div>
        <div class="col-lg-2">
          <p class="textLabel">(lng,lat):</p>
        </div>
        <div class="col-lg-4">
          <input
            type="text"
            class="form-control"
            v-model="latlng"
            v-on:keyup.enter="updateMarker_add('lnglat')"
          />
        </div>
      </div>

      <br />
      <br />
      <!-- //table here -->

      <div v-if="data.save">
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

      <div slot="modal-footer" slot-scope="{}">
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator"
          @click="insert_hardfiber"
        >Insert Hard Fiber</button>
        <!-- <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator"
          v-b-modal="'modal_add_splitter'"
        >
          Insert Splitter
        </button>-->
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.operator"
          @click="update_closure"
        >Update</button>
        <button
          class="btn btn-success btn-labeled"
          v-if="data.save && roles.rm"
          @click="delete_closure"
        >Delete</button>
        <button class="btn btn-success btn-labeled" v-if="!data.save" @click="save_data">Save</button>
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
        { key: "status" },
        { key: "action" }
      ],
      splitters_item: [],
      item: {},
      splitters_tblisBusy: false,
      closure_types: [],
      latlng: "",
      user: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
    this.splitters_item = this.data.splitter_closure;
  },
  methods: {
    load() {
      this.$http.get("api/ClosureType").then(response => {
        this.closure_types = response.body;
      });
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
          console.log(response.body);
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    update_closure() {
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
            this.$root.$emit("pageLoading");
            this.$http
              .delete("api/Closure/" + this.data.id)
              .then(response => {
                this.$root.$emit("pageLoaded");
                this.$root.$emit("delete_closure", response.body);
                swal("Deleted", "", "success");
                this.$bvModal.hide("modal_closure_marker_click");
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
    splitters_tblRowClicked(item, index, event) {
      console.log(item);
      if (item.splitter_type.name == "LCP") {
        this.$root.$emit("pageLoading");
        this.$http
          .get("api/SplitterPort/" + item.id)
          .then(response => {
            console.log(response.body);
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
            this.$bvModal.show("modal_splitter_port_list");
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
    insert_hardfiber() {
      this.$root.$emit("draw_line", this.data);
      this.$bvModal.hide("modal_closure_marker_click");
    },
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
    updateMarker_add(txt) {
      if (txt == "latlng") {
        var temp = this.latlng.split(",");
        this.data.position.lat = parseFloat(temp[0]);
        this.data.position.lng = parseFloat(temp[1]);
      }
      if (txt == "lnglat") {
        var temp = this.latlng.split(",");
        this.data.position.lat = parseFloat(temp[1]);
        this.data.position.lng = parseFloat(temp[0]);
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
