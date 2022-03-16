<template>
  <div>
    <div style="display: none">
      <b-table
        id="genTable1"
        class="elClr"
        show-empty
        striped
        hover
        outlined
        :fields="fields[data.field]"
        :items="data.items"
      ></b-table>
    </div>

    <!-- modalChangePackage ---------------------------------------------------------------------------------------->
    <b-modal
      id="modalChangePackage"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="lg"
      title="Job Order - Change Package"
    >
      <!-- form -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3"></div>
        <div class="col-lg-3">
          <p-radio
            class="textLabel p-default p-curve"
            v-model="cp.type"
            value="4"
            name="isNew"
            color="success-o"
            >Upgrade</p-radio
          >
        </div>
        <div class="col-lg-3">
          <p-radio
            class="textLabel p-default p-curve"
            v-model="cp.type"
            value="5"
            name="isNew"
            color="success-o"
            >Downgrade</p-radio
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <label class>Package:</label>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="packages"
            v-model="cp.pack"
            option-value="id"
            option-text="name"
            placeholder="Select a package code..."
          ></model-list-select>
        </div>
      </div>

      <!-- /form -->
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="btnChangePackage()"
          >Create Job Order</b-button
        >
      </template>
    </b-modal>
    <!-- End modalChangePackage -->
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";
import PrettyRadio from "pretty-checkbox-vue/radio";

export default {
  props: ["data", "packages", "client"],
  components: {
    "p-radio": PrettyRadio,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
  },
  data() {
    return {
      fields: [
        [
          { key: "client.acc_no", label: "account_number" },
          {
            key: "balance1",
            label: "balance",
            formatter: (value) => {
              return 0;
            },
          },
          { key: "id", label: "billing_number" },
          {
            key: "date",
            label: "due_date",
            formatter: (value, key, item) => {
              var today = new Date();
              var lastDayOfMonth = new Date(
                today.getFullYear(),
                today.getMonth() + 1,
                0
              );
              return this.formatDate_mmddyyyy(lastDayOfMonth);
            },
          },
          {
            key: "date1",
            label: "issue_date",
            formatter: (value, key, item) => {
              return this.formatDate_mmddyyyy(Date.now());
            },
          },
          { key: "service_datefrom", label: "service_datefrom" },
          { key: "service_dateto", label: "service_dateto" },
          { key: "sum_bal", label: "bill_amount" },
          {
            key: "status",
            label: "status",
            formatter: (value, key, item) => {
              return "PENDING";
            },
          },
        ],
        [
          {
            key: "biller_code",
            label: "biller_code",
            formatter: (value) => {
              return "11940655";
            },
          },
          { key: "acc_no", label: "account_number" },
          {
            key: "name",
            label: "account_name",
            formatter: (value, key, item) => {
              return value.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            },
          },
          {
            key: "date_activated",
            label: "connection_date",
            formatter: (value, key, item) => {
              if (value == null) return this.formatDate_mmddyyyy("01-01-1970");
              else return this.formatDate_mmddyyyy(value);
            },
          },
          {
            key: "status",
            label: "status",
            formatter: (value, key, item) => {
              return "Active";
            },
          },
          { key: "package.name", label: "subscription_plan" },
          { key: "id", label: "reference_number" },
        ],
        [
          {
            key: "bucket_ip",
            label: "bucket_ip",
            formatter: (value) => {
              return "202.137.112.10";
            },
          },
          { key: "pk_subscription_id", label: "pk_subscription_id" },
          { key: "info_client.id", label: "IIS_ID" },
          { key: "subscription_name", label: "subscription_name" },
          {
            key: "info_client.subscription_name",
            label: "IIS_subscription_name",
          },
          { key: "package_name", label: "package_name" },
          { key: "info_client.pack_name", label: "IIS_package_name" },
          { key: "status_name", label: "status_name" },
          { key: "info_client.status", label: "IIS_status" },
          { key: "client_name", label: "client_name" },
          { key: "info_client.name", label: "IIS_client_name" },
        ],
        [
          { key: "pk_subscription_id", label: "pk_subscription_id" },
          { key: "subscription_name", label: "subscription_name" },
          {
            key: "subscription_desc",
            label: "subscription_desc",
          },
          { key: "package_name", label: "package_name" },
          { key: "client_name", label: "client_name" },
          { key: "status_name", label: "status_name" },
        ],
        [
          {
            key: "bucket_ip",
            label: "bucket_ip",
            formatter: (value) => {
              return "202.137.112.14";
            },
          },
          { key: "pk_subscription_id", label: "pk_subscription_id" },
          { key: "subscription_name", label: "subscription_name" },
          { key: "package_name", label: "package_name" },
          { key: "status_name", label: "status_name" },
          { key: "info_client.id", label: "is_id" },
          { key: "info_client.name", label: "is_name" },
          { key: "info_client.pack_name", label: "is_package" },
          { key: "info_client.mrr", label: "is_mrr" },
          { key: "info_client.status", label: "is_status" },
        ],
      ],
      user: [],
      cp: {
        type: 4,
        pack: {},
      },
      roles: [],
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
  },
  methods: {
    load() {},

    close_modal() {
      this.$bvModal.hide("modal_add_hardfiber");
      this.$root.$emit("call_undo");
    },
    excelReportCSV(tbl) {
      this.$nextTick(function () {
        setTimeout(
          function () {
            var tab_text = "<table>";
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
                "Say Thanks"
              );
            } //other browser not tested on IE 11
            // var sa = window.open(
            //   "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet," +
            //     encodeURIComponent(tab_text)
            // );
            else
              var sa = window.open(
                "data:text/csv," + encodeURIComponent(tab_text)
              );
            return sa;
          }.bind(this),
          1000
        );
      });
    },
    formatDate_mmddyyyy(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [month, day, year].join("-");
    },
    btnChangePackage() {
      console.log(this.cp.pack);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true,
      }).then((update) => {
        if (update) {
          var data = {
            ticket_type_id: this.cp.type,
            client_id: this.client.id,
            created_by: this.user.id,
            user_id: this.user.id,
            status: "Check Balance",
            state: "accounting",
            remarks: "Change package:" + this.cp.pack.name,
          };

          this.$root.$emit("pageLoading");
          this.$http
            .post("api/ActivityTicket", data)
            .then((response) => {
              console.log(response.body);
              swal("Requested!", "", "success");
              this.$root.$emit("pageLoaded");
            })
            .catch((response) => {
              console.log(response.body);
              this.$root.$emit("pageLoaded");
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true,
              });
            });
        }
      });
    },
  },
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
</style>


