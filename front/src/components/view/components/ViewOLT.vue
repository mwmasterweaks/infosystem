<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          OLT's
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_olt"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create OLT</button>
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
        title="Manage OLT"
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
              title="Input the name of the olt"
              placeholder="Name of the olt"
              v-validate="'required'"
              v-model.trim="olt.name"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="ip"
              ref="ip"
              class="form-control"
              v-b-tooltip.hover
              title="Input the ip of the olt"
              placeholder="IP of the OLT"
              v-validate="'required'"
              v-model.trim="olt.ip"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('ip')">IP is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdate()">Update</b-button>
          <b-button size="sm" variant="danger" v-if="roles.delete_olt" @click="btnDelete()">Delete</b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!-- modalPON ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalPON"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Manage PON"
        @ok="handleOk"
      >
        <!-- ADD PON-------------------------------------------------------------------------------------->
        <div class="elBG panel" v-if="roles.create_pon">
          <div class="panel-heading">
            <p class="elClr panel-title">Add PON</p>
          </div>
          <div class="elClr panel-body">
            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">PON #:</p>
              </div>
              <div class="col-lg-9">
                <input
                  type="text"
                  name="pon"
                  ref="pon"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Input the new PON number"
                  placeholder="PON"
                  v-validate="'required'"
                  v-model.trim="create_PON.pon"
                  autocomplete="off"
                  autofocus="on"
                />
                <small class="text-danger pull-left" v-show="errors.has('pon')">PON is required.</small>
              </div>
            </div>

            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Area:</p>
              </div>
              <div class="col-lg-9">
                <input
                  type="text"
                  name="area"
                  ref="area"
                  class="form-control"
                  v-b-tooltip.hover
                  title="Input the Area of the PON"
                  placeholder="Area"
                  v-validate="'required'"
                  v-model.trim="create_PON.area"
                  autocomplete="off"
                  autofocus="on"
                />
              </div>
            </div>
          </div>

          <div class="elClr panel-footer">
            <div class="heading-elements">
              <button
                type="button"
                class="btn btn-success btn-labeled pull-right"
                v-on:click="btnAddPON"
              >ADD</button>
            </div>
          </div>
        </div>
        <!-- END ADD PON------------------------------->

        <hr v-if="roles.create_pon" />

        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">View PON</p>
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
                striped
                show-empty
                hover
                outlined
                :fields="ponFields"
                :items="ponItems"
                :filter="tblFilter"
                :busy="tblisBusy"
                :current-page="currentPage"
                :per-page="perPage"
                :tbody-tr-class="tblRowClassPON"
                head-variant=" elClr"
                thead-class="cursorPointer-th"
                @filtered="onFiltered"
              >
                <!-- <template slot="" :title="row.title" slot-scope="row"> -->
                <template v-slot:cell(actions)="row">
                  <b-button
                    size="sm"
                    variant="info"
                    @click="
                      OpenModalEditPON(row.item, row.index, $event.target)
                    "
                    class="col-5 pull-right margin-right-10"
                    v-if="roles.update_pon"
                  >Update</b-button>
                  <b-button
                    size="sm"
                    variant="danger"
                    @click="deletePON(row.item, row.index, $event.target)"
                    v-if="roles.delete_pon"
                    class="col-5 pull-right margin-right-10"
                  >Delete</b-button>
                </template>
                <div slot="table-busy" class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>
              </b-table>
            </div>
          </div>
          <div class="elClr panel-footer">
            <div class="row" style="background-color:; padding:15px;">
              <div class="col-md-8" style="background-color:;">
                <span class="elClr">{{ ponTotalRows }} item/s found.</span>
              </div>

              <div class="col-md-4" style="background-color:;">
                <b-pagination
                  v-model="currentPage"
                  :total-rows="ponTotalRows"
                  :per-page="perPage"
                  class="my-0 pull-right"
                ></b-pagination>
              </div>
            </div>
          </div>
        </div>

        <div slot="modal-footer" slot-scope="{}"></div>
      </b-modal>
      <!-- End modalPON -->

      <!--editPONMODAL------------------------------------------------------------------------------------>
      <b-modal
        id="editPONMODAL"
        centered
        title="Update PON"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">PON #:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="pon1"
              class="form-control"
              v-b-tooltip.hover
              title="Input the new PON number"
              placeholder="PON Number"
              v-validate="'required'"
              v-model.trim="PONEdit.pon"
              autocomplete="off"
              autofocus="on"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Area:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="area"
              ref="area"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Area of the PON"
              placeholder="Area"
              v-validate="'required'"
              v-model.trim="PONEdit.area"
              autocomplete="off"
              autofocus="on"
            />
          </div>
        </div>

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdatePON()">Update</b-button>
        </template>
      </b-modal>
      <!--editPONMODAL end-->

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
        title="OLT Form"
        @ok="handleOk"
      >
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
              title="Input the name of the olt"
              placeholder="Name of the olt"
              v-validate="'required'"
              v-model.trim="olt.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="ip"
              ref="ip"
              class="form-control"
              v-b-tooltip.hover
              title="Input the ip of the olt"
              placeholder="IP of the OLT"
              v-validate="'required'"
              v-model.trim="olt.ip"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('ip')">IP is required.</small>
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
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
        {
          key: "PONS",
          label: "PONS",
          sortable: true
        },
        { key: "name", sortable: true },
        { key: "ip", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      ponFields: [
        { key: "pon", sortable: true },
        { key: "area", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true },
        { key: "actions" }
      ],
      items: [],
      ponItems: [],
      tblFilter: null,
      totalRows: 1,
      ponTotalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      olt: {
        name: "",
        ip: ""
      },
      OLTSelected: {},
      PONEdit: {
        pon: "",
        area: ""
      },
      user: [],
      roles: [],
      create_PON: {
        olt_id: "",
        pon: "",
        area: ""
      }
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.items = this.$global.getOLT();
    this.user = this.$global.getUser();
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
    },
    tblRowClassPON(item, type) {
      if (!item) return;
      else return "elClr";
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_olt) {
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
      if (this.roles.update_olt) {
        this.$bvModal.show("modalEdit");
        this.olt.id = item.id;
        this.olt.name = item.name;
        this.olt.ip = item.ip;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    openModalViewPONS(items) {
      this.ponTotalRows = 0;
      this.tblisBusy = true;
      this.OLTSelected = items;
      this.$bvModal.show("modalPON");
      this.loadPONS();
    },
    OpenModalEditPON(item, index, button) {
      this.PONEdit = item;
      this.$bvModal.show("editPONMODAL");
    },
    btnUpdatePON() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.PONEdit.user_id = this.user.id;
          this.PONEdit.user_name = this.user.name;
          this.$http
            .put("api/pon/" + this.PONEdit.id, this.PONEdit)
            .then(response => {
              this.ponItems = response.body;
              swal("Updated", "", "success");
              this.$bvModal.hide("editPONMODAL");
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
    deletePON(item, index, button) {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$http
            .get("api/pon/" + item.id + "/" + item.olt_id)
            .then(response => {
              swal("Deleted", "", "success").then(value => {
                this.ponItems = response.body;
                this.ponTotalRows = response.body.length;
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
    },
    loadPONS() {
      this.$http.get("api/pon/" + this.OLTSelected.id).then(response => {
        this.ponItems = response.body;
        this.ponTotalRows = response.body.length;
        this.tblisBusy = false;
      });
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.olt.user_id = this.user.id;
          this.olt.user_name = this.user.name;
          this.$http
            .post("api/olt", this.olt)
            .then(response => {
              swal("Created", "", "success");
              this.olt.name = "";
              this.olt.ip = "";
              this.$global.setOLT(response.body);
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
              this.olt.user_id = this.user.id;
              this.olt.user_name = this.user.name;
              this.$http
                .put("api/olt/" + this.olt.id, this.olt)
                .then(response => {
                  this.items = response.body;
                  this.$global.setOLT(response.body);
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
              .delete("api/olt/" + this.olt.id)
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
          text: "You are not allow to delete an OLT",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    btnAddPON() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.create_PON.olt_id = this.OLTSelected.id;
          this.create_PON.user_id = this.user.id;
          this.create_PON.user_name = this.user.name;
          this.$http
            .post("api/pon", this.create_PON)
            .then(response => {
              this.ponItems = response.body;
              this.ponTotalRows = response.body.length;

              swal("PON", "Added successfully", "success");
              this.create_PON = {
                olt_id: "",
                pon: "",
                area: ""
              };
              this.tblisBusy = false;
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
.margin-right-10 {
  margin-right: 10px;
}
.margin-left-10 {
  margin-left: 10px;
}
</style>
