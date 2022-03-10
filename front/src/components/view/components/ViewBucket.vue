<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Bucket
          <button
            v-b-modal="'modalAdd'"
            @click="bucket.state = 'create'"
            v-if="roles.create_bucket"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Create Bucket
          </button>
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
                        >Clear</b-button
                      >
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
          </div>
        </div>

        <div style="display:flex">
          <div
            class="row marginice"
            style="margin-left:1px;float:left;width:80%"
          >
            <b>Showing {{ perPage }} out of {{ totalRows }} entries</b>
          </div>
          <div class="row marginice" style="width:8%">
            <b-row>
              <b-col style="float:right;padding-right:0"> </b-col>
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
        @ok="handleOk"
      >
        <template #modal-title>
          <span v-if="bucket.state == 'create'">Bucket Form</span>
          <span v-else>Manage Bucket</span>
        </template>
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              class="form-control"
              v-b-tooltip.hover
              title="Input Bucket Name"
              placeholder="Name"
              v-validate="'required'"
              v-model.trim="bucket.name"
              v-on:keyup.enter="btnCreate"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Name is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="description"
              class="form-control"
              v-b-tooltip.hover
              title="Type the description of the bucket server"
              placeholder="Description"
              v-model.trim="bucket.description"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="IP"
              minlength="7"
              maxlength="15"
              size="15"
              class="form-control"
              v-b-tooltip.hover
              title="Input Bucket IP"
              placeholder="Bucket IP"
              v-validate="'required|ip'"
              data-vv-as="bucket.IP"
              v-model.trim="bucket.IP"
              v-on:keyup.enter="btnCreate"
            />
            <small class="text-danger pull-left" v-show="errors.has('IP')"
              >IP is required and must be valid IP address.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">User ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="user_id"
              class="form-control"
              v-b-tooltip.hover
              title="Type the user ID of the bucket server"
              placeholder="user id"
              v-model.trim="bucket.user_id"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Role ID:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="role_id"
              class="form-control"
              v-b-tooltip.hover
              title="Type the role ID of the bucket server"
              placeholder="role_id"
              v-model.trim="bucket.role_id"
              v-on:keyup.enter="btnCreate"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Username:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="username"
              class="form-control"
              v-b-tooltip.hover
              title="Type the password of the bucket server"
              placeholder="Username"
              v-validate="'required'"
              v-model.trim="bucket.username"
              v-on:keyup.enter="btnCreate"
            />
            <small class="text-danger pull-left" v-show="errors.has('username')"
              >Username is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="password"
              class="form-control"
              v-b-tooltip.hover
              title="Type the password of the bucket server"
              placeholder="Password"
              v-validate="'required'"
              v-model.trim="bucket.password"
              v-on:keyup.enter="btnCreate"
            />
            <small class="text-danger pull-left" v-show="errors.has('password')"
              >Password is required.</small
            >
          </div>
        </div>
        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            @click="btnCreate()"
            v-if="bucket.state == 'create'"
            >Create</b-button
          >
          <b-button size="sm" variant="success" @click="btnUpdate()" v-else
            >Update</b-button
          >
        </template>
      </b-modal>
      <!--modalAdd-------->
    </div>

    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Bucket</p>
      </div>

      <div class="elClr panel-body">
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Bucket:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="items"
                v-model="ssh.bucket"
                option-value="id"
                option-text="IP"
                placeholder="Select bucket IP..."
              ></model-list-select>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Command:</p>
            </div>
            <div class="col-lg-9">
              <input
                id="ssh_command"
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="bucket command"
                placeholder="Command"
                v-model.trim="ssh.command"
                v-on:keyup.enter="submit_command"
              />
            </div>
          </div>
          <br />
          <br />

          <p>{{ ssh_output }}</p>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input
                    v-model="ssh_tblFilter"
                    placeholder="Filter"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button
                      :disabled="!ssh_tblFilter"
                      @click="ssh_tblFilter = ''"
                      >Clear</b-button
                    >
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>

            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <span class="elClr">{{ ssh_totalRows }} item/s found.</span>
            </b-col>
          </b-row>

          <b-table
            id="ssh_table"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :items="ssh_items"
            :filter="ssh_tblFilter"
            :busy="ssh_tblisBusy"
            head-variant=" elClr"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>

          <button
            @click="fnExcelReport('ssh_table')"
            type="button"
            class="btn btn-success"
            style="width:100px;color:white"
          >
            Export
          </button>

          <button
            @click="ssh_items = []"
            type="button"
            class="btn btn-success"
            style="width:100px;color:white"
          >
            Clear table
          </button>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ ssh_totalRows }} item/s found.</span>
          </div>
        </div>
      </div>
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
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "description", sortable: true },
        { key: "IP", sortable: true },
        { key: "user_id", sortable: true },
        { key: "role_id", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      bucket: {
        name: ""
      },
      ssh_items: [],
      ssh_tblFilter: "",
      ssh_tblisBusy: false,
      ssh_totalRows: 0,
      ssh_output: "",
      ssh: {
        bucket: {},
        command: ""
      },
      command_list: [
        "subscription --show ",
        "ip --show ",
        "ip --show --subscription_name ",
        "ntools --arp ",
        "bwmon ",
        "net --show ",
        "bandwidth --show",
        "package --show"
      ],
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    //load buckets
    this.user = this.$global.getUser();
  },
  mounted() {
    this.load();
    this.totalRows = this.items.length;
  },
  updated() {},
  methods: {
    load() {
      this.$http.get("api/Bucket").then(response => {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });

      this.$root.$emit("clearNav");
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarComponents").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$nextTick(function() {
        setTimeout(
          this.autocomplete(
            document.getElementById("ssh_command"),
            this.command_list,
            "command"
          ),
          500
        );
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_bucket) {
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
      if (this.roles.update_bucket) {
        this.bucket = item;
        this.bucket.state = "update";
        this.bucket.password = "";
        this.$bvModal.show("modalAdd");
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          this.bucket.user_id_log = this.user.id;
          this.bucket.user_name_log = this.user.name;
          this.$http
            .post("api/Bucket", this.bucket)
            .then(response => {
              console.log(response.body);
              swal("Created", "", "success");
              this.bucket.name = "";
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
              this.$bvModal.hide("modalAdd");
            })
            .catch(response => {
              this.$root.$emit("pageLoaded");
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
              this.bucket.user_id_log = this.user.id;
              this.bucket.user_name_log = this.user.name;

              this.$http
                .put("api/Bucket/" + this.bucket.id, this.bucket)
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
      if (this.roles.delete_bucket) {
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
              .delete("api/Bucket/" + this.bucket.id)
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
          text: "You are not allow to delete a Bucket",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    submit_command() {
      //console.log(this.ssh);

      this.$root.$emit("pageLoading");
      this.$http
        .post("api/Bucket/ssh_command", this.ssh)
        .then(response => {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          this.ssh_items = this.ssh_items.concat(response.body.table);
          this.ssh_items = this.uniq_fast(this.ssh_items);
          this.ssh_totalRows = this.ssh_items.length;
          this.ssh_output = response.body.message;
        })
        .catch(response => {
          this.$root.$emit("pageLoaded");
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    autocomplete(inp, arr, dat) {
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      var thisisthis = this;
      inp.addEventListener("input", function(e) {
        var a,
          b,
          i,
          val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
          return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.style = "color: black";
            b.innerHTML =
              "<strong>" + arr[i].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              if (dat == "command")
                thisisthis.ssh.command = this.getElementsByTagName(
                  "input"
                )[0].value;

              closeAllLists();
            });
            a.appendChild(b);
          }
        }
      });

      inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          currentFocus++;

          addActive(x);
        } else if (e.keyCode == 38) {
          currentFocus--;

          addActive(x);
        } else if (e.keyCode == 13) {
          /*If the ENTER key is pressed, prevent the form from being submitted,*/
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }
        }
      });
      function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = x.length - 1;
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
      }
      function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
          x[i].classList.remove("autocomplete-active");
        }
      }
      function closeAllLists(elmnt) {
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
          if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }

      document.addEventListener("click", function(e) {
        closeAllLists(e.target);
      });
    },
    fnExcelReport(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table

            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
              //tab_text=tab_text+"</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
              // If Internet Explorer
              txtArea1.document.open("txt/html", "replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus();
              var sa = txtArea1.document.execCommand(
                "SaveAs",
                true,
                "Say Thanks to Sumit.xls"
              );
            } //other browser not tested on IE 11
            else
              var sa = window.open(
                "data:application/vnd.ms-excel," + encodeURIComponent(tab_text)
              );
            return sa;
          }.bind(this),
          1000
        );
      });

      // this.$nextTick(function() {
      //   setTimeout(this.changeColDisplay(""), 3000);
      // });
    },
    uniq_fast(a) {
      var seen = {};
      var out = [];
      var len = a.length;
      var j = 0;
      for (var i = 0; i < len; i++) {
        var item = a[i].name;
        if (seen[item] !== 1) {
          seen[item] = 1;
          out[j++] = a[i];
        }
      }
      return out;
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
