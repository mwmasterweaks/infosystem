<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Daily Collection Report
          <button
            @click="fnExcelReport('tableReport')"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-left-10"
          >Export</button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="8 " class="my-1">
              <b-form-group label-cols-sm="2" label="Region" class="mb-0">
                <model-list-select
                  :list="regions"
                  v-model="region_selected"
                  option-value="id"
                  option-text="name"
                  placeholder="Select/Search a Region..."
                  @input="onChangeDate('region')"
                ></model-list-select>
              </b-form-group>

              <b-form-group label-cols-sm="2" label="Area" class="mb-0" v-if="areas.length > 0">
                <model-list-select
                  :list="areas"
                  v-model="area_selected"
                  option-value="id"
                  option-text="name"
                  placeholder="Select/Search a area..."
                  @input="onChangeDate('area')"
                ></model-list-select>
              </b-form-group>

              <b-form-group
                label-cols-sm="2"
                label="Branch"
                class="mb-0"
                v-if="branches.length > 0"
              >
                <multiselect
                  v-model="branch_selected"
                  :options="branches"
                  label="name"
                  track-by="id"
                  variant="success"
                  multiple
                  open-direction="bottom"
                  placeholder="Type to search"
                  :hide-selected="true"
                  :clear-on-select="false"
                  :close-on-select="false"
                  :taggable="true"
                ></multiselect>
              </b-form-group>

              <b-form-group
                label-cols-sm="2"
                label="Date From"
                class="mb-0"
                v-if="branch_selected.length > 0"
              >
                <b-input-group>
                  <date-picker
                    v-model="dateSelected_from"
                    :config="AppliedDateoptions"
                    @input="onChangeDate('f')"
                    autocomplete="off"
                  ></date-picker>
                </b-input-group>
              </b-form-group>

              <b-form-group
                label-cols-sm="2"
                label="Date To"
                class="mb-0"
                v-if="branch_selected.length > 0"
              >
                <b-input-group>
                  <date-picker
                    v-model="dateSelected_to"
                    :config="AppliedDateoptions"
                    @input="onChangeDate('t')"
                    autocomplete="off"
                  ></date-picker>
                </b-input-group>
              </b-form-group>
            </b-col>
          </b-row>

          <b-table
            id="tableReport"
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :sticky-header="true"
            :no-border-collapse="true"
            responsive
            :fields="fields"
            :items="items"
            :busy="tblisBusy"
            :tbody-tr-class="tblRowClass"
            thead-class="cursorPointer-th"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;"></div>

          <div class="col-md-4" style="background-color:;"></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyRadio from "pretty-checkbox-vue/radio";
import datePicker from "vue-bootstrap-datetimepicker";
import Multiselect from "vue-multiselect";

export default {
  components: {
    multiselect: Multiselect,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "date" },
        { key: "or_number", label: "OR/Reff." },
        { key: "client.name", label: "Name" },
        {
          key: "client.branch",
          label: "Branch",
          formatter: value => {
            if (value != null) return value.name;
            else return "NO_BRANCH";
          }
        },
        { key: "remarks", label: "Remarks" },
        { key: "payment_method_name", label: "Pay Method" },
        { key: "amount", label: "Amount" }
      ],
      items: [],
      regions: [],
      region_selected: {},
      tblFilter: null,
      dateSelected_from: null,
      dateSelected_to: null,
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      areas: [],
      area_selected: {},
      branches: [],
      branch_selected: [],
      user: [],
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.regions = this.$global.getRegion();
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
          document.getElementById("accountingMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarAccounting").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      // this.$http.get("api/area").then(response => {
      //   this.areas = response.body;
      // });
    },
    onChangeDate(a) {
      if (a == "region") {
        this.areas = this.region_selected.area;
        this.branch_selected = [];
        this.branches = [];
      }
      if (a == "area") {
        console.log(this.area_selected);
        this.branches = this.area_selected.branches;
        this.branch_selected = this.area_selected.branches;
      }
      if (
        this.region_selected != null &&
        this.area_selected != null &&
        this.branch_selected != null &&
        this.dateSelected_from != null &&
        this.dateSelected_to != null
      ) {
        this.tblisBusy = true;
        var dddd = {
          date_from: this.dateSelected_from,
          date_to: this.dateSelected_to,
          region_id: this.region_selected.id,
          area_id: this.area_selected.id,
          branch_selected: this.branch_selected
        };
        this.$http
          .post("api/CustomerPayment/dailyReport", dddd)
          .then(response => {
            console.log(response.body);
            this.items = response.body;
            this.tblisBusy = false;
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error + " " + response.body.message,
              icon: "error",
              dangerMode: true
            });
            this.tblisBusy = false;
          });
      }
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.accounting) {
        return "elClr cursorPointer";
      } else return "elClr";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    tblRowClicked(item, index, event) {},
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
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
</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
