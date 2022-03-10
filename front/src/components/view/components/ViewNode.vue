<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          NODE
          <button
            v-if="roles.operator"
            @click="openModal('modalAdd')"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Node</button>
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
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            :tbody-tr-class="tblRowClass"
            thead-class="cursorPointer-th"
            head-variant=" elClr"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>

            <template v-slot:cell(PONS)="row">
              <b-button variant="success" @click="openModalViewPONS(row.item)">PONS</b-button>
            </template>
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
      >
        <div slot="modal-header">
          <h5 v-if="item.id == null">Node Form</h5>
          <h5 v-else>Manage Node</h5>
        </div>
        <!--Form-------->
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
              title="Input the name of the node"
              placeholder="Name of the node"
              v-validate="'required'"
              v-model.trim="item.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Latitude:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="Latitude"
              class="form-control"
              v-b-tooltip.hover
              title="Input Latitude"
              placeholder="Latitude"
              v-validate="'required'"
              v-model.trim="item.lat"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('Latitude')"
            >Latitude is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Longitude:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="Longitude"
              class="form-control"
              v-b-tooltip.hover
              title="Input Longitude"
              placeholder="Longitude"
              v-validate="'required'"
              v-model.trim="item.lng"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('Longitude')"
            >Longitude is required.</small>
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()" v-if="item.id == null">Create</b-button>
          <b-button size="sm" variant="success" @click="btnUpdate()" v-else>Update</b-button>
        </template>
      </b-modal>
      <!--modalAdd-------->

      <modal_node_marker_click v-bind:data="item"></modal_node_marker_click>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import modal_node_marker_click from "../../modal_vue/node_marker_click.vue";

export default {
  components: {
    "model-list-select": ModelListSelect,
    modal_node_marker_click: modal_node_marker_click
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "lat", sortable: true },
        { key: "lng", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [20, 50, 100],
      item: {
        name: "",
        lat: "",
        lng: ""
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
  },
  mounted() {
    this.load();

    this.$root.$on("delete_node", index => {
      this.items.splice(index, 1);
    });
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
      this.$http.get("api/Node").then(response => {
        console.log(response.body);
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.operator) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    tblRowClicked(item, index, event) {
      if (this.roles.operator) {
        item.index = index;
        item.wit = item.marker_type;
        item.isMap = false;
        this.item = item;
        console.log(this.item);
        if (this.item.wit == "node")
          this.$bvModal.show("modal_node_marker_click");
      }
    },
    openModal(modal) {
      this.$bvModal.show(modal);
      this.clearData();
    },
    clearData() {
      this.item = {
        id: null,
        name: "",
        lat: "",
        lng: ""
      };
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          this.item.user_id = this.user.id;
          this.item.user_name = this.user.name;
          this.$http
            .post("api/Node", this.item)
            .then(response => {
              swal("Created", "", "success");
              this.clearData();

              this.items.push(response.body);
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$bvModal.hide("modalAdd");
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
              this.$root.$emit("pageLoading");
              this.item.user_id = this.user.id;
              this.item.user_name = this.user.name;
              this.$http
                .put("api/Node/" + this.item.id, this.item)
                .then(response => {
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalAdd");
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
          });
        }
      });
    },
    btnDelete() {
      if (this.roles.delete_olt) {
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
              .delete("api/Node/" + this.item.id)
              .then(response => {
                this.$bvModal.hide("modalEdit");
                this.$global.setRegion(response.body);
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
          text: "You are not allow to delete an Node",
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
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
.modal-content,
.modal-header {
  border: 1px solid red;
}
.margin-right-10 {
  margin-right: 10px;
}
.margin-left-10 {
  margin-left: 10px;
}
</style>
