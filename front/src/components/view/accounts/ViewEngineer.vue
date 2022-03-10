<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Technical Sales
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_engineer"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create Tech Sales</button>
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
          <!--        link in a row
            <template slot="name" slot-scope="data">
              <a :href="`#${data.value.replace(/[^a-z]+/i,'-').toLowerCase()}`">{{ data.value }}</a>
          </template>-->
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
        title="Manage Engineer"
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
              title="Input the name of the Engineer"
              placeholder="Name of the Engineer"
              v-validate="'required'"
              v-model.trim="engineer.name"
              autocomplete="off"
              autofocus="on"
              disabled
            />

            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Position:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="position"
              class="form-control"
              v-b-tooltip.hover
              title="Input the position of engineer"
              placeholder="Engineer Position"
              v-validate="'required'"
              v-model.trim="engineer.position"
              v-on:keyup.enter="btnUpdate"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('position')"
            >Position is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Signature:</p>
          </div>
          <div class="col-lg-9">
            <b-form-file
              accept="image/*"
              @change="imageChange"
              placeholder="Choose a file or drop it here..."
              drop-placeholder="Drop file here..."
              ref="userimg"
            ></b-form-file>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            id="btnupdate"
            size="sm"
            variant="success"
            v-if="roles.update_engineer"
            @click="btnUpdate"
          >Update</b-button>
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_engineer"
            @click="btnDelete"
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
        title="Tech Sales Form"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <!-- <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Input the name of the Engineer"
              placeholder="Name of the Engineer"
              v-validate="'required'"
              v-model.trim="engineer.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />-->
            <model-list-select
              :list="accounts"
              v-model.trim="engineer.engineer_id"
              option-value="id"
              option-text="name"
              placeholder="Select/Search engineer"
              name="name"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Position:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="position"
              class="form-control"
              v-b-tooltip.hover
              title="Input the position of engineer"
              placeholder="Engineer Position"
              v-validate="'required'"
              v-model.trim="engineer.position"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('position')"
            >Position is required.</small>
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
import Swal from "sweetalert2";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "name", sortable: true },
        { key: "position", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      engineer: {
        engineer_id: "",
        name: "",
        position: "",
        signature: ""
      },
      user: [],
      roles: [],
      accounts: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.items = this.$global.getEngineer();

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
    this.totalRows = this.items.length;
  },
  updated() {},
  methods: {
    load() {
      this.$root.$emit("clearNav");
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("accountsMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarAccounts").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http.get("api/user").then(function(response) {
        this.accounts = response.body;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_engineer) {
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
      if (this.roles.update_engineer) {
        this.$bvModal.show("modalEdit");
        this.engineer = item;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.engineer.user_id = this.user.id;
          this.engineer.user_name = this.user.name;
          this.$http
            .post("api/Engineer", this.engineer)
            .then(response => {
              swal("Created", "", "success");
              console.log(response.body);
              this.engineer = {
                engineer_id: "",
                name: "",
                position: "",
                signature: ""
              };
              this.$global.setEngineer(response.body);
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
            focusConfirm: true,
            text: "",
            icon: "warning",
            buttons: ["No", "Yes"]
          }).then(update => {
            if (update) {
              this.engineer.user_id = this.user.id;
              this.engineer.user_name = this.user.name;
              this.$http
                .put("api/Engineer/" + this.engineer.id, this.engineer)
                .then(response => {
                  this.$global.setEngineer(response.bod);
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
      if (this.roles.delete_engineer) {
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
              .delete("api/Engineer/" + this.engineer.id)
              .then(response => {
                this.$bvModal.hide("modalEdit");
                this.$global.setEngineer(response.body);
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
          text: "You are not allow to delete a Engineer",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    imageChange(e) {
      if (e != null)
        if (e.target.files[0].size > 5000000) {
          swal("File selected are too large", "File must be less than 5MB");
          this.engineer.signature = "";
          this.$refs["userimg"].reset();
        } else {
          var fileReader = new FileReader();
          fileReader.readAsDataURL(e.target.files[0]);

          fileReader.onload = e => {
            this.engineer.signature = e.target.result;
          };
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
