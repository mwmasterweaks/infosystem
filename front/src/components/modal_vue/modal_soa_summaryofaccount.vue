<template>
  <div>
    <b-modal
      id="modalSoaSummaryOfAccount"
      size="xl"
      title="Print Preview"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div id="printSec" style="width: 90%; color: black">
        <table
          class="parent"
          style="
            background-repeat: no-repeat;
            background-position: center;
            background-size: 800px;
          "
        >
          <tr style="background-color: rgba(255, 255, 255, 0.8)">
            <td>
              <div>
                <div class="rowFields mx-auto row">
                  <table
                    style="width: 100%; border-collapse: collapse"
                    class="normalTbl"
                  >
                    <tr class>
                      <td class="normalTbl" style="width: 40%">
                        <img
                          src="https://i.ibb.co/CW69PnT/email-logo.png"
                          style="width: 100%"
                        />
                      </td>
                      <td
                        class="normalTbl"
                        style="vertical-align: top; width: 60%"
                      >
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 10"
                        >
                          <b>
                            <br />Dctech Building, Shanghai Street, <br />Matina
                            Aplaya, Davao City <br />Davao Del Sur 8000,
                            Philippines <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-000
                          </b>
                        </p>
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 11"
                        >
                          <b>
                            <br />2nd floor Mervin Abella Building, <br />Mabini
                            Magsaysay Street, Digos City, <br />Davao del Sur
                            8002, Philippines <br />Tel #: (082) 221-2380 local
                            1129 <br />VAT Registered TIN: 003-375-571-018
                          </b>
                        </p>
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 12"
                        >
                          <b>
                            <br />Prk. Talisay, Visayan Village, <br />Tagum
                            City(Infront Emcor Highway), <br />Davao Del Norte
                            8100, Philippines <br />Tel #: 0922-817-2350 /
                            0922-817-2337 <br />VAT Registered TIN:
                            003-375-571-007
                          </b>
                        </p>
                        <!-- cagayan -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 13"
                        >
                          <b>
                            <br />2nd floor Tres Montañas Building <br />Room
                            203 J.R Borja Extension Gusa <br />Cagayan De Oro
                            City, Philippines <br />Tel #: (082) 221-2380
                            <br />VAT Registered TIN: 003-375-571-009
                          </b>
                        </p>
                        <!-- Gensan -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 14"
                        >
                          <b>
                            <br />GF Door3 Maduramente Building, <br />National
                            Highway GSC <br />General Santos City 9500,
                            Philippines <br />Tel #: (083) 554-7373 <br />VAT
                            Registered TIN: 003-375-571-001
                          </b>
                        </p>
                        <!-- Panabo -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 17"
                        >
                          <b>
                            <br />UNIT 2C GREATSUN VENTURES CO., <br />PUROK
                            ATIK STO. NINO, <br />PANABO CITY, DDN, Philippines
                            <br />Tel #: (082) 221-2380 - local 1930 <br />VAT
                            Registered TIN: 003-375-571-020
                          </b>
                        </p>
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 18"
                        >
                          <b>
                            <br />2nd Floor LifeScape Building, <br />JC Aquino
                            Avenue, Butuan city <br />Contact Nos. 09079475047
                            <br />VAT Registered TIN: 003-375-571-011
                          </b>
                        </p>
                        <!-- valencia -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 19"
                        >
                          <b>
                            <br />Tamay Lang Parklane Lavina Ave,
                            <br />Poblacion Valencia City <br />Bukidnon,
                            Philippines <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-015
                          </b>
                        </p>
                        <!-- Samal -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 21"
                        >
                          <b>
                            <br />Ibo Building, Purok 1, Brgy. Miranda-Pichon,
                            <br />Babak, IGCS, <br />Davao del Norte 8119,
                            Philippines <br />Contact #: 0915-555-79-25
                            <br />VAT Registered TIN: 003-375-571-012
                          </b>
                        </p>
                        <!-- Koronadal -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 22"
                        >
                          <b>
                            <br />Unit 3 G/F, JYC building, <br />Arellano
                            Street Zone 3, <br />Koronadal City, Philippines
                            <br />Tel #: (082) 221-2380 <br />VAT Registered
                            TIN: 003-375-571-001
                          </b>
                        </p>
                        <!-- malaybalay -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 23"
                        >
                          <b>
                            <br />BRGY 2, MAGSAYSAY STREET , <br />MALAYBALAY
                            CITY, <br />BUKIDNON, Philippines <br />Tel #: (082)
                            221-2380 <br />VAT Registered TIN: 003-375-571-000
                          </b>
                        </p>
                        <!-- malita -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 24"
                        >
                          <b>
                            <br />2nd FLOOR ANNIE DIGAN BUILDING, <br />SAN
                            ROQUE ST. POBLACION <br />MALITA DAVAO OCCIDENTAL,
                            PHILIPPINES <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-024
                          </b>
                        </p>
                        <!-- Sto Thomas -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 25"
                        >
                          <b>
                            <br />2nd Floor Melecio Building, <br />Fd. Rd. 2,
                            Tibal-og, <br />Sto. Tomas Davao del Norte,
                            Philippines <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-019
                          </b>
                        </p>
                        <!-- Nabunturan -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 26"
                        >
                          <b>
                            <br />Purok 16 ,Poblacion Nabunturan <br />Davao De
                            Oro, Philippines <br />Tel #: (082) 221-2380
                            <br />VAT Registered TIN: 003-375-571-000
                          </b>
                        </p>
                        <!-- Cabadbaran -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 27"
                        >
                          <b>
                            <br />DOOR #3 P.MLBAS ST., <br />CABADBARAN CITY,
                            Philippines <br />Contact #: 09518794355 (accounting
                            staff) <br />Contact #: 09267395669 (Technical
                            staff) <br />Sales #: 09100161204/09464363686
                            <br />VAT Registered TIN: 003-375-571-013
                          </b>
                        </p>
                        <!-- Nasipit -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 28"
                        >
                          <b>
                            <br />J&V Felias Building Curato St., <br />District
                            2, Barangay 3, Nasipit <br />Agusan del Norte,
                            Philippines <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-017
                          </b>
                        </p>
                        <!-- Bukidnon -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 29"
                        >
                          <b>
                            <br />BRGY 2, Magsaysay Street, <br />Malaybalay
                            City <br />Bukidnon, Philippines <br />Tel #: (082)
                            221-2380 <br />VAT Registered TIN: 003-375-571-016
                          </b>
                        </p>
                        <!-- Iligan -->
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 20px;
                            line-height: 1.3;
                          "
                          v-if="data.client.region_id == 31"
                        >
                          <b>
                            <br />Door 2, G/F Deleste Bldg. <br />Sabayle St.,
                            Poblacion, Iligan City <br />Lanao del Norte, 9200,
                            Philippines <br />Tel #: (082) 221-2380 <br />VAT
                            Registered TIN: 003-375-571-022
                          </b>
                        </p>
                      </td>
                    </tr>
                  </table>
                </div>
                <br />
                <br />
                <br />
                <br />
                <div class="rowFields mx-auto row">
                  <table
                    style="
                      width: 60%;
                      border-collapse: collapse;
                      color: black;
                      border: 1px solid black;
                      padding: 10px;
                    "
                  >
                    <tr>
                      <th
                        colspan="3"
                        style="
                          width: 50%;
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        SUMMARY OF ACCOUNT
                      </th>
                    </tr>

                    <tr>
                      <td
                        rowspan="3"
                        style="border: 1px solid black; padding: 10px"
                      >
                        {{ data.client.name }}
                        <br />
                        {{ data.client.address }}
                        <br />
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid none solid solid;
                        "
                      >
                        Date:
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid solid solid none;
                        "
                      >
                        {{ datenow }}
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid none solid solid;
                        "
                      >
                        Account No.
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid solid solid none;
                        "
                      >
                        {{ data.client.acc_no }}
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid none solid solid;
                        "
                      >
                        Amount Due:
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: solid solid solid none;
                        "
                      >
                        PHP {{ data.summary_balance_total }}
                      </td>
                    </tr>
                  </table>
                </div>
                <br />
                <br />
                <br />
                <br />
                <div class="rowFields mx-auto row">
                  <table
                    class="center"
                    id="table2"
                    style="
                      width: 100%;
                      border-collapse: collapse;
                      border: 1px solid black;
                      padding: 10px;
                    "
                  >
                    <tr>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        Date
                      </th>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        OR/Reff.
                      </th>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        Transactions
                      </th>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        AMT CHRG
                      </th>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        AMT PAID
                      </th>
                      <th
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          text-align: center;
                        "
                      >
                        BALANCE
                      </th>
                    </tr>
                    <tr v-for="item in data.data" :key="item.id">
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none solid none none;
                        "
                      >
                        {{ item.date }}
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none solid none none;
                        "
                      >
                        {{ item.or_number }}
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none;
                        "
                      >
                        {{ item.description }}
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none solid none solid;
                        "
                      >
                        <span>{{ item.priceFormated }}</span>
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none solid none solid;
                        "
                      >
                        {{ item.amountFormated }}
                      </td>
                      <td
                        style="
                          border: 1px solid black;
                          padding: 10px;
                          border-style: none solid none solid;
                        "
                      >
                        {{ item.summary_balance }}
                      </td>
                    </tr>

                    <tr>
                      <td
                        style="border: 1px solid black; padding: 10px"
                        colspan="3"
                      >
                        PROMPT PAYMENT IS HIGHLY APPRECIATED
                      </td>

                      <td
                        style="border: 1px solid black; padding: 10px"
                        colspan="2"
                      >
                        Total Amount Due
                      </td>
                      <td style="border: 1px solid black; padding: 10px">
                        PHP {{ data.summary_balance_total }}
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </td>
          </tr>
        </table>

        <!-- <div style="width: 800px; opacity: 0.2;">
          <img
            src="https://i.ibb.co/7WS6qS7/dctech-logo.png"
            class="watermarkImg"
          />
        </div>-->
      </div>
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="printElement('printSec')"
          >Print</b-button
        >
        <b-button
          size="sm"
          variant="success"
          @click="readyEmailData('printSec', 'yes')"
          >Email</b-button
        >
      </template>
    </b-modal>

    <!-- sendmail ----------------------------------------------------------------------->
    <b-modal
      id="modal_select_recipient"
      centered
      title="Send To"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      @ok="EmailElement('printSec', 'yes')"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Email Sender:</p>
        </div>
        <div class="col-lg-9">
          <p-radio
            class="textLabel p-default p-curve p-jelly"
            v-model="email_data.emailSender"
            value="default"
            name="emailSender"
            color="success-o"
            >Default</p-radio
          >

          <p-radio
            class="textLabel p-default p-curve p-jelly"
            v-model="email_data.emailSender"
            value="myEmail"
            name="emailSender"
            color="success-o"
            >My Email</p-radio
          >
        </div>
      </div>

      <div
        class="rowFields mx-auto row"
        v-if="email_data.emailSender == 'myEmail'"
      >
        <div class="col-lg-3">
          <p class="textLabel">Email:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="text"
            class="form-control"
            v-b-tooltip.hover
            title="Email Address"
            placeholder="Complete Email address (myemail@gmail.com)"
            v-model="emailSenderData.email"
          />
        </div>
      </div>

      <div
        class="rowFields mx-auto row"
        v-if="email_data.emailSender == 'myEmail'"
      >
        <div class="col-lg-3">
          <p class="textLabel">Email Password:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="password"
            class="form-control"
            v-b-tooltip.hover
            title="Email Password"
            placeholder="Email Password"
            v-model="emailSenderData.pass"
          />
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Send To:</p>
        </div>
        <div class="col-lg-9 multiselectemail">
          <multiselect
            v-model="email_data.sendTo"
            :options="contacts"
            label="email"
            track-by="email"
            variant="success"
            multiple
            tag-placeholder="Add this Email Address"
            open-direction="bottom"
            placeholder="Type to search or type Email Address"
            :hide-selected="true"
            :clear-on-select="false"
            :close-on-select="false"
            :taggable="true"
            @tag="addTagInSendTo"
          ></multiselect>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">CC To:</p>
        </div>
        <div class="col-lg-9 multiselectemail">
          <multiselect
            v-model="email_data.CCTO"
            :options="contacts"
            label="email"
            track-by="email"
            variant="success"
            multiple
            tag-placeholder="Add this Email Address"
            open-direction="bottom"
            placeholder="Type to search or type Email Address"
            :hide-selected="true"
            :clear-on-select="false"
            :close-on-select="false"
            :taggable="true"
            @tag="addTagInCCTo"
          ></multiselect>
        </div>
      </div>
    </b-modal>
    <!-- End sendmail -->
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import Multiselect from "vue-multiselect";
import PrettyRadio from "pretty-checkbox-vue/radio";
import PrettyCheck from "pretty-checkbox-vue/check";
import datePicker from "vue-bootstrap-datetimepicker";
import VueRangedatePicker from "vue-rangedate-picker";

export default {
  props: ["data"],
  components: {
    "date-picker": datePicker,
    "p-radio": PrettyRadio,
    "p-check": PrettyCheck,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    multiselect: Multiselect
  },
  data() {
    return {
      items: [],
      datenow: new Date(),
      user: {},
      contacts: [],
      email_data: {
        subject: "Statement of Account as of ",
        msg: "",
        user_email: "",
        user_name: "",
        sendTo: [],
        CCTO: [],
        togmail: "yes",
        emailSender: "default"
      },
      modiView: {
        data: {
          due_date: ""
        },
        due_date: false
      },
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      emailSenderData: {
        email: "",
        pass: ""
      },
      roles: []
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.datenow = this.formatDateMDY(this.datenow);
    this.user = this.$global.getUser();
    if (this.$global.getSoaEmail() != null) {
      this.emailSenderData.email = this.$global.getSoaEmail();
      this.emailSenderData.pass = this.$global.getSoaEmailPass();
    }

    this.$http.get("api/getClientEmail").then(response => {
      this.contacts = response.body;
      // console.log(this.contacts);
      // console.log(response.body);
    });
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
    readyEmailData(id, gmail) {
      console.log(id);
      var email = this.data.client.email;
      if (email == null) email = this.data.client.email_add;
      this.email_data.subject = "Billing Statement No. " + this.data.id;
      this.email_data.msg = document.getElementById(id).innerHTML;
      this.email_data.user_email = this.user.name;
      this.email_data.user_name = this.user.email;
      this.email_data.CCTO = [
        {
          email: this.user.email,
          name: this.user.name
        }
      ];
      this.email_data.sendTo = [];
      var tempEmail = email.split(", ");

      for (var i = 0; i < tempEmail.length; ++i) {
        this.email_data.sendTo.push({
          email: tempEmail[i],
          name: this.data.client.name
        });
      }
      this.$bvModal.show("modal_select_recipient");
    },
    EmailElement(id, gmail) {
      this.email_data.emailSenderDataE = this.emailSenderData.email;
      this.email_data.emailSenderDataP = this.emailSenderData.pass;
      console.log(this.emailSenderData);
      this.$root.$emit("pageLoading");
      console.log(this.email_data);
      this.$http
        .post("api/Billing/emailSOA", this.email_data)
        .then(response => {
          console.log(response.body);
          this.$root.$emit("pageLoaded");
          this.$global.setSoaEmail(this.emailSenderData.email);
          this.$global.setSoaEmailPass(this.emailSenderData.pass);

          if (response.body.includes("ok")) swal("Email Sent!");
          else swal("Send Failed");
        })
        .catch(response => {
          this.$root.$emit("pageLoaded");
          swal({
            title: "Error",
            text: response.body.error + " " + response.body.message,
            icon: "error",
            dangerMode: true
          });
          this.tblisBusy = false;
        });
    },
    saveStatement() {
      this.data.user_id = this.user.id;
      this.data.user_name = this.user.name;
      console.log(this.data);
      this.$http
        .post("api/Billing/storeBillStatement", this.data)
        .then(response => {
          console.log(response.body);
          this.data.saved = true;
          this.data.id = response.body.id;
          swal("Saved! you can now print or email this statement now.");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error + " " + response.body.message,
            icon: "error",
            dangerMode: true
          });
        });
    },
    addTagInSendTo(newTag) {
      const tag = {
        name: newTag,
        email: newTag
      };
      this.contacts.push(tag);
      this.email_data.sendTo.push(tag);
    },
    addTagInCCTo(newTag) {
      const tag = {
        name: newTag,
        email: newTag
      };
      this.contacts.push(tag);
      this.email_data.CCTO.push(tag);
    },
    changeDueDate() {
      this.modiView.due_date = !this.modiView.due_date;
    },
    changeDueDateOK() {
      this.modiView.due_date = !this.modiView.due_date;
      this.data.due_date = this.formatDateMDY(this.modiView.data.due_date);
      console.log(this.modiView.data.due_date);
    },
    btnDelState() {
      if (this.roles.accounting) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.data.user_id = this.user.id;
            this.data.user_name = this.user.name;
            this.data.bill = this.data.id;

            this.$http
              .post("api/Billing/deleteBillState", this.data)
              .then(response => {
                console.log(response.body);
                swal("Deleted", "", "success").then(value => {});
                this.$bvModal.hide("modalSoaPrintPreview");
                this.$root.$emit("reloadBillStatement");
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
          text: "You are not allow to delete a method",
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
.centerText {
  text-align: center;
}

@media print {
  @page {
    size: A4;
    size: portrait;
  }
}

/* table.parent > tr:not(.child1) {
  opacity: 0.2;
  box-shadow: inset 0 0 0 2000px #ffffffcc;
} */

/* .watermark {
  content: "";
  display: block;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 50px;
  left: 0px;
  background-image: url(https://i.ibb.co/7WS6qS7/dctech-logo.png);
  background-size: 800px 450px;
  background-position: center;
  background-repeat: no-repeat;
  opacity: 0.2;
  z-index: -1;
}

.watermarkImg {
  position: absolute;
  width: 800px;
  opacity: 0.2;
  z-index: -1;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
} */
</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css" />
;
