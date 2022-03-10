<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Packages
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_package"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Package</button>
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
        title="Manage Package"
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
              title="Input the name of the Package"
              placeholder="Package Code"
              v-validate="'required'"
              v-model.trim="pack.name"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Max Speed:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the max speed of this package"
              placeholder="Max Speed"
              v-model.trim="pack.max_speed"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">CIR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the CIR of this package"
              placeholder="CIR"
              v-model.trim="pack.cir"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">MRR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-b-tooltip.hover
              title="Input the MRR of this package"
              placeholder="MRR"
              v-model.trim="pack.mrr"
              v-on:keyup.enter="btnUpdate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packageTypes"
              v-model="pack.package_type_id"
              v-on:keyup.enter="btnUpdate"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
            ></model-list-select>
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdate()">Update</b-button>
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_package"
            @click="btnDelete()"
          >Delete</b-button>
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
        title="Package Form"
        @ok="handleOk"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package Code:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Input the name of the Package"
              placeholder="Package Code"
              v-validate="'required'"
              v-model.trim="pack.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Max Speed:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the max speed of this package"
              placeholder="Max Speed"
              v-model.trim="pack.max_speed"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">CIR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              class="form-control"
              v-b-tooltip.hover
              title="Input the CIR of this package"
              placeholder="CIR"
              v-model.trim="pack.cir"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">MRR:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-b-tooltip.hover
              title="Input the MRR of this package"
              placeholder="MRR"
              v-model.trim="pack.mrr"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packageTypes"
              v-model="pack.package_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
              v-validate="'required'"
              name="packageTypes"
              v-on:keyup.enter="btnCreate"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('packageTypes')"
            >Please select package type.</small>
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
        { key: "package_type.name", label: "Package type", sortable: true },
        { key: "max_speed", sortable: true },
        { key: "cir", label: "CIR", sortable: true },
        { key: "mrr", label: "MRR", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      pack: {
        name: "",
        package_type_id: "",
        max_speed: "",
        cir: "",
        mrr: ""
      },
      packageTypes: [],
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.packageTypes = this.$global.getPackageTypes();
    this.user = this.$global.getUser();

    if (this.user.id == 1) {
      var temp = {
        key: "id",
        label: "ID",
        sortable: true
      };
      this.fields.push(temp);
    }
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

      this.$http.get("api/Package").then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_package) {
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
      if (this.roles.update_package) {
        this.$bvModal.show("modalEdit");
        this.pack = item;
        this.pack.package_type_id = item.package_type.id;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.pack.user_id = this.user.id;
          this.pack.user_name = this.user.name;
          this.$http
            .post("api/Package", this.pack)
            .then(response => {
              swal("Created", "", "success");
              this.pack = {
                name: "",
                package_type_id: "",
                max_speed: "",
                cir: "",
                mrr: ""
              };
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
              this.pack.user_id = this.user.id;
              this.pack.user_name = this.user.name;
              this.$http
                .put("api/Package/" + this.pack.id, this.pack)
                .then(response => {
                  this.pack = {
                    name: "",
                    package_type_id: "",
                    max_speed: "",
                    cir: "",
                    mrr: ""
                  };
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
      if (this.roles.delete_package) {
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
              .delete("api/Package/" + this.pack.id)
              .then(response => {
                this.$bvModal.hide("modalEdit");
                swal("Deleted", "", "success").then(value => {
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  this.tblisBusy = false;
                  this.pack = {
                    name: "",
                    package_type_id: "",
                    max_speed: "",
                    cir: "",
                    mrr: ""
                  };
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
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a Package",
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
