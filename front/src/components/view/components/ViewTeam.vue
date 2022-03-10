<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Team
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_team"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Create Team
          </button>

          <button
            v-b-modal="'modalSelectDateForAccount'"
            v-if="roles.operator"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >
            Filter Account Activate in date
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

          <template v-slot:cell(action)="row">
            <b-button variant="info" @click="openModalSummary(row.item)"
              >Summary</b-button
            >
          </template>

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
        title="Manage Team"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Team name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              placeholder="Team name"
              v-validate="'required'"
              v-model.trim="team.name"
              v-on:keyup.enter="btnUpdate"
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
            <p class="textLabel">Select Team Leader:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              name="tl"
              ref="tl"
              :list="users"
              v-model="team.user_id"
              v-validate="'required'"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('tl')"
              >Team Leader is required.</small
            >
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_team"
            @click="btnDelete()"
            >Delete</b-button
          >
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
        title="Team Form"
      >
        <!--Form-------->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Team name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              placeholder="Team name"
              v-validate="'required'"
              v-model.trim="team.name"
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
            <p class="textLabel">Select Team Leader:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              name="tl"
              ref="tl"
              :list="users"
              v-model="team.user_id"
              v-validate="'required'"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('tl')"
              >Team Leader is required.</small
            >
          </div>
        </div>

        <!--Form-------->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnCreate()"
            >Create</b-button
          >
        </template>
      </b-modal>
      <!--modalAdd-------->

      <!-- modalSelectDate ----------------------------------------------------------------------->
      <b-modal
        id="modalSelectDate"
        centered
        title="Select Date Range"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Team Name:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel">{{ selectedTeam.name }}</p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Select date range:</p>
          </div>
          <div class="col-lg-9">
            <rangedate-picker
              class="dateRangePicker"
              @selected="onDateSelected"
              i18n="EN"
            ></rangedate-picker>
          </div>
        </div>

        <template v-slot:modal-footer>
          <div class="w-100"></div>
        </template>
      </b-modal>
      <!-- End modalSelectDate -->

      <!--modalSummary-------->
      <b-modal
        id="modalSummary"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
      >
        <div slot="modal-header">
          <h5>
            {{ selectedTeam.name }} Summary, From: {{ selected_date_from }} To:
            {{ selected_date_to }}
          </h5>
        </div>
        <b-table
          id="summaryTable"
          class="elClr"
          tbody-tr-class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="summary_fields"
          :items="summary_items"
          :busy="summary_tblisBusy"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>
        </b-table>
        <template v-slot:modal-footer>
          <b-button
            size="sm"
            variant="success"
            @click="fnExcelReport('summaryTable')"
            >Export</b-button
          >
        </template>
      </b-modal>

      <!--modalSummary-------->

      <!-- modalSelectDateForAccount ----------------------------------------------------------------------->
      <b-modal
        id="modalSelectDateForAccount"
        centered
        title="Select Date Range"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Select date range:</p>
          </div>
          <div class="col-lg-9">
            <rangedate-picker
              class="dateRangePicker"
              @selected="onDateSelectedAccounts"
              i18n="EN"
            ></rangedate-picker>
          </div>
        </div>

        <template v-slot:modal-footer>
          <div class="w-100"></div>
        </template>
      </b-modal>
      <!-- End modalSelectDateForAccount -->

      <!--modalSummaryAccounts-------->
      <b-modal
        id="modalSummaryAccounts"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
      >
        <div slot="modal-header">
          <h5>
            Summary, From: {{ selected_date_from1 }} To: {{ selected_date_to1 }}
          </h5>
        </div>
        <b-table
          id="summaryTable1"
          class="elClr"
          tbody-tr-class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="summary_fields1"
          :items="summary_items1"
          :busy="summary_tblisBusy1"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>
        </b-table>
        <template v-slot:modal-footer>
          <b-button
            size="sm"
            variant="success"
            @click="fnExcelReport('summaryTable1')"
            >Export</b-button
          >
        </template>
      </b-modal>

      <!--modalSummaryAccounts-------->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "user.name", label: "Team Leader", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true },
        { key: "action", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      team: {
        name: "",
        user_id: ""
      },
      selectedTeam: {},
      selected_date_from: "",
      selected_date_to: "",
      summary_tblisBusy: false,
      summary_fields: [
        { key: "date" },
        { key: "plan" },
        { key: "done" },
        { key: "done_percent" },
        { key: "pending" },
        { key: "pending_percent" }
      ],
      summary_items: [],

      selected_date_from1: "",
      selected_date_to1: "",
      summary_tblisBusy1: false,
      summary_fields1: [
        { key: "id" },
        { key: "name" },
        { key: "TDATE" },
        { key: "TYPE" }
      ],
      summary_items1: [],
      users: [],
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.getItem();
    this.user = this.$global.getUser();
    //this.items = this.$global.getPackageTypes();
  },
  mounted() {
    this.load();
    this.totalRows = this.items.length;
  },
  updated() {},
  methods: {
    getItem() {
      this.$http.get("api/team").then(function(response) {
        console.log(response.body);

        this.items = response.body;
        this.tblisBusy = false;
        this.totalRows = this.items.length;
      });

      this.$http.get("api/user").then(function(response) {
        this.users = response.body;
      });
    },
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
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_team) {
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
      if (this.roles.update_team) {
        this.$bvModal.show("modalEdit");
        this.team = item;
      }
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.$http
            .post("api/team", this.team)
            .then(response => {
              swal("Created", "", "success");
              this.team.name = "";
              this.team.user_id = "";
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
              this.$http
                .put("api/team/" + this.team.id, this.team)
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
      if (this.roles.delete_package_type) {
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
              .delete("api/team/" + this.team.id)
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
                    this.tblisBusy = false;
                  }
                });
              });
          }
        });
      } else {
        swal({
          title: "Opss.",
          text: "You are not allow to delete a Package type",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    openModalSummary(item) {
      this.selectedTeam = item;
      this.$bvModal.show("modalSelectDate");
    },
    onDateSelected(daterange) {
      this.selected_date_from = this.formatDate(daterange.start);
      if (daterange.end == null) daterange.end = daterange.start;
      this.selected_date_to = this.formatDate(daterange.end);
      this.$http
        .put("api/team/summary/" + this.selectedTeam.id, daterange)
        .then(response => {
          this.summary_items = response.body;

          this.$bvModal.show("modalSummary");
          this.$bvModal.hide("modalSelectDate");
        });
    },
    onDateSelectedAccounts(daterange) {
      this.selected_date_from1 = this.formatDate(daterange.start);
      if (daterange.end == null) daterange.end = daterange.start;
      this.selected_date_to1 = this.formatDate(daterange.end);

      this.$http.post("api/team/summary_accounts", daterange).then(response => {
        this.summary_items1 = response.body.inst;
        this.summary_items1 = this.summary_items1.concat(response.body.ticket);

        this.$bvModal.show("modalSummaryAccounts");
        this.$bvModal.hide("modalSelectDateForAccount");
      });
    },
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    },
    fnExcelReport(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text =
              "<table border='2px'>" +
              this.selectedTeam.name +
              " Summary, From: " +
              this.selected_date_from +
              "To: " +
              this.selected_date_to +
              "<tr bgcolor='#87AFC6'>";
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
