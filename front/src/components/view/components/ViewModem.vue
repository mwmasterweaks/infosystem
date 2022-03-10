<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Modem
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_modem"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Modem</button>
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
                      <b-button
                        class="clearBtn"
                        :disabled="!tblFilter"
                        @click="tblFilter = ''"
                      >Clear</b-button>
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
        </div>

        <div style="display:flex">
          <div class="row marginice" style="margin-left:1px;float:left;width:80%">
            <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
          </div>
          <div class="row marginice" style="width:8%">
            <b-row>
              <b-col style="float:right;padding-right:0"></b-col>
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
        title="Manage Modem"
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
              v-model.trim="modem.name"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
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
        size="lg"
        title="Modem Form"
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
              title="Input the name of the modem"
              placeholder="Name of the modem"
              v-validate="'required'"
              v-model.trim="modem.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
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
        { key: "name", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      modem: {
        name: ""
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

      this.tblisBusy = true;
      this.$http.get("api/Modem").then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_modem) {
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
        this.modem.id = item.id;
        this.modem.name = item.name;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;

          this.modem.user_id = this.user.id;
          this.modem.user_name = this.user.name;
          this.$http
            .post("api/Modem", this.modem)
            .then(response => {
              swal("Created", "", "success");
              this.modem.name = "";
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
              this.modem.user_id = this.user.id;
              this.modem.user_name = this.user.name;

              this.$http
                .put("api/Modem/" + this.modem.id, this.modem)
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
      if (this.roles.delete_modem) {
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
              .delete("api/Modem/" + this.modem.id)
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
