<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Rebates Report
          <button
            @click="fnExcelReport('tableReport')"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-left-10"
          >Export</button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Area:</p>
            </div>
            <div class="col-lg-9">
              <model-list-select
                :list="areas"
                v-model="report_details.area_id"
                @input="date_selected_change"
                option-value="id"
                option-text="name"
                name="area"
                ref="area"
                placeholder="Select/Search a Area..."
                v-validate="'required'"
              ></model-list-select>
              <small class="text-danger pull-left" v-show="errors.has('area')">Area is required.</small>
            </div>
          </div>
          <div class="rowFields mx-auto row" style>
            <div class="col-lg-3">
              <p class="textLabel">Select Month:</p>
            </div>
            <div class="col-lg-9">
              <date-picker
                v-model="report_details.date"
                :config="AppliedDateoptions"
                @input="date_selected_change"
                autocomplete="off"
              ></date-picker>
            </div>
          </div>

          <div class="rowFields mx-auto row" style>
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

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        {
          key: "client_id",
          label: "ID"
        },
        {
          key: "client.name",
          label: "Client Name"
        },
        {
          key: "amount",
          label: "Total Rebates"
        }
      ],
      items: [],
      tblFilter: null,
      report_details: {
        area_id: "",
        date: ""
      },
      AppliedDateoptions: {
        format: "YYYY-MM",
        useCurrent: false
      },

      user: [],
      areas: [],
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
          document.getElementById("accountingMenu").className =
            "customeDropDown dropdown-menu";
          document.getElementById("navbarAccounting").style.backgroundColor =
            "#2196f3";
        }, 100);
      });

      this.$http.get("api/area").then(response => {
        this.areas = response.body;
        var temp = {
          id: 0,
          name: "All Areas"
        };
        this.areas.unshift(temp);
      });
    },
    date_selected_change() {
      if (this.report_details.date_to != "") {
        // this.tblisBusy = true;
        this.$http
          .post("api/Rebates/report", this.report_details)
          .then(response => {
            console.log(response.body);
            this.items = response.body;
            // this.tblisBusy = false;
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
    tblRowClicked(item, index, event) {
      console.log(item);
    },
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
