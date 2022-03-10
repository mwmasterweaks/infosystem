<template>
  <div>
    <b-modal
      id="modalPreviewOR"
      size="xl"
      title="Print Preview"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div id="printOR" style="width: 100%; color: black;">
        <center>
          <div style="width: 90%">
            <br />
            <br />
            <br />
            <table>
              <tr>
                <td style="width: 40%"></td>
                <td style="width: 20%"></td>
                <td style="width: 20%"></td>
                <td
                  style="width: 20%; height: 50px; vertical-align: top; text-align: left;"
                >{{data.date}}</td>
              </tr>

              <tr>
                <td style="width: 40%">{{data.client_name}}</td>
                <td style="width: 20%;" @dblclick="changeTin">
                  <span v-if="item.changeTin">
                    <input
                      type="text"
                      class="form-control"
                      v-b-tooltip.hover
                      title="Input the TIN"
                      placeholder="TIN Number"
                      v-on:keyup.enter="changeTin"
                      v-model="item.tin"
                    />
                  </span>
                  <span v-else>{{item.tin}}</span>
                </td>
                <td style="width: 40%" colspan="2">{{data.client_location}}</td>
              </tr>
              <tr>
                <td style="width: 80%; vertical-align: top;" colspan="3">
                  <table>
                    <tr>
                      <td style=" vertical-align: top; height: 30px;" @dblclick="changeBstyle">
                        <span v-if="item.changeBstyle">
                          <input
                            type="text"
                            class="form-control"
                            v-b-tooltip.hover
                            title="Input the Business Style"
                            placeholder="Business Style"
                            v-on:keyup.enter="changeBstyle"
                            v-model="item.bstyle"
                          />
                        </span>
                        <span v-else>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{item.bstyle}}
                        </span>
                      </td>
                    </tr>
                    <tr :key="index" v-for="(m, index) in data.item">
                      <td style="padding: 3px;">{{m.description}}</td>
                    </tr>

                    <tr>
                      <td style="vertical-align: bottom; height: 20px;">
                        {{data.amountInWord}}
                        <span style="float: right;">&#8369; {{data.amount}}</span>
                      </td>
                    </tr>
                  </table>
                </td>
                <td style="width: 20%">
                  <table>
                    <tr>
                      <td
                        style=" text-align: right; vertical-align: top; height: 80px;"
                        @dblclick="changeTotalSales"
                      >
                        <span v-if="item.changeTotalSales">
                          <input
                            type="number"
                            class="form-control"
                            v-b-tooltip.hover
                            title="Input the with holding tax"
                            placeholder="With Holding Tax"
                            v-on:keyup.enter="changeTotalSales"
                            v-model="data.totalSales_copy"
                          />
                        </span>
                        <span v-else>{{data.totalSales}}</span>
                      </td>
                    </tr>

                    <tr>
                      <td style=" text-align: right;" @dblclick="changeWHT">
                        <span v-if="item.changeWHT">
                          <input
                            type="number"
                            class="form-control"
                            v-b-tooltip.hover
                            title="Input the with holding tax"
                            placeholder="With Holding Tax"
                            v-on:keyup.enter="changeWHT"
                            v-model="item.wht"
                          />
                        </span>
                        <span v-else>{{item.wht_copy}}</span>
                      </td>
                    </tr>
                    <tr>
                      <td
                        style=" text-align: right; vertical-align: top; height: 25px;"
                      >{{data.amount}}</td>
                    </tr>
                    <tr>
                      <td
                        style=" text-align: right; vertical-align: top; height: 40px;"
                      >{{data.vatable}}</td>
                    </tr>
                    <tr>
                      <td style=" text-align: right;">{{data.vat}}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </center>
      </div>
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="printElement('printOR')">Print</b-button>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  props: ["data"],
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      items: [],
      datenow: new Date(),
      user: {},
      item: {
        changeTin: false,
        tin: "(TIN)",
        changeBstyle: false,
        bstyle: "(Business Style)",
        changeWHT: false,
        wht: 0,
        wht_copy: "0.00",
        changeTotalSales: false
      },

      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.datenow = this.formatDateMDY(this.datenow);
    this.user = this.$global.getUser();
  },
  methods: {
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
            var tab_text = "<table border='2px'><tr>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table

            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
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
    },
    formatDateMDY(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;
      var mstring = [
        "Jan.",
        "Feb.",
        "Mar.",
        "Apr.",
        "May",
        "Jun.",
        "Jul.",
        "Aug.",
        "Sept.",
        "Oct.",
        "Nov.",
        "Dec."
      ];
      return [mstring[month - 1], day, year].join(" ");
    },
    printElement(id) {
      var elem = document.getElementById(id);
      var domClone = elem.cloneNode(true);

      var $printSection = document.getElementById("printSection");

      if (!$printSection) {
        var $printSection = document.createElement("div");
        $printSection.id = "printSection";
        document.body.appendChild($printSection);
      }

      $printSection.innerHTML = "";
      $printSection.appendChild(domClone);
      window.print();
    },
    EmailElement(id) {
      console.log(this.data);

      var email = this.data.client.email;
      if (email == null) email = this.data.client.email_add;
      var data = {
        subject: "Statement of Account as of " + this.data.due_date,
        msg: document.getElementById(id).innerHTML,
        user_email: this.user.name,
        user_name: this.user.email,
        sendTo: [],
        CCTO: [
          {
            email: this.user.email,
            name: this.user.name
          },
          {
            email: "r11cnc.dctech@gmail.com",
            name: "CNC R11"
          }
        ]
      };

      var tempEmail = email.split(", ");

      for (var i = 0; i < tempEmail.length; ++i) {
        data.sendTo.push({
          email: tempEmail[i],
          name: this.data.client.name
        });
      }

      this.$http
        .post("api/Billing/emailSOA", data)
        .then(response => {
          if (response.body == "ok") swal("Email Sent!");
          else swal("Send Failed");
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
    },
    changeTin() {
      this.item.changeTin = !this.item.changeTin;
    },
    changeBstyle() {
      this.item.changeBstyle = !this.item.changeBstyle;
    },
    changeWHT() {
      this.item.changeWHT = !this.item.changeWHT;
      this.calculateAmount();
    },
    changeTotalSales() {
      this.item.changeTotalSales = !this.item.changeTotalSales;
      this.calculateAmount();
    },
    calculateAmount() {
      const options = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      };
      this.data.amount_copy = this.data.totalSales_copy - this.item.wht;

      var converter = require("number-to-words");

      this.data.amountInWord = converter.toWords(this.data.amount_copy);
      this.data.vatable = Math.round(this.data.amount_copy / 1.12);

      this.data.vat = Number(
        Math.round(this.data.vatable * 0.12)
      ).toLocaleString("en", options);

      this.data.vatable = Number(this.data.vatable).toLocaleString(
        "en",
        options
      );

      this.data.amount = Number(this.data.amount_copy).toLocaleString(
        "en",
        options
      );
      this.data.totalSales = Number(this.data.totalSales_copy).toLocaleString(
        "en",
        options
      );

      this.item.wht_copy = Number(this.item.wht).toLocaleString("en", options);
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
}

@media print {
  @page {
    size: 8.5in 5in;
    size: landscape;
  }
}
</style>
