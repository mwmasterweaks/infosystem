<template>
  <div>
    <b-modal
      id="modalFSRPrintPreview"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      :no-close-on-backdrop="true"
      :show-close="false"
      ok-only
    >
      <template v-slot:modal-title>
        <label>Print Preview</label>
      </template>
      <div id="printFSR" class="printFSR" style="color: black">
        <table class="parent">
          <tr style="background-color: rgba(255, 255, 255, 0.8)">
            <td>
              <div>
                <div class="rowFields mx-auto row">
                  <table style="width: 100%; border-collapse: collapse" class="normalTbl">
                    <tr class>
                      <td class="normalTbl" style="width: 30%">
                        <img src="https://i.ibb.co/CW69PnT/email-logo.png" style="width: 80%" />
                      </td>
                      <td class="normalTbl" style="vertical-align: center; width: 40%">
                        <center>
                          <h4>
                            <strong>FIELD SERVICE REPORT</strong>
                          </h4>
                        </center>
                      </td>
                      <td class="normalTbl" style="vertical-align: top; width: 30%">
                        <p
                          style="
                            vertical-align: text-top;
                            font-size: 12px;
                            line-height: 1.3;
                            float:right
                          "
                        >
                          <label>
                            <strong>FORM NO. {{data.id}}</strong>
                          </label>
                          <br />
                          <b>
                            <br />Dctech Building, Shanghai Street,
                            <br />Matina
                            Aplaya, Davao City
                            <br />Davao Del Sur 8000,
                            Philippines
                            <br />Tel #: (082) 221-2380
                            <br />VAT
                            Registered TIN: 003-375-571-000
                          </b>
                        </p>
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="rowFields mx-auto row">
                  <b-card bg-variant="light" style="width:100%">
                    <center>
                      <div class="rowFields2 mx-auto row">
                        <div>
                          <p class="textLabel-2">CLIENT'S INFORMATION</p>
                        </div>
                      </div>

                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Customer:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="data.name"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Contact Person:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="data.contact_person"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Address:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="data.location"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx--auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Contact No.:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="data.contact"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Date & Time Called:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Client's Complaint:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input
                              type="text"
                              class="form-control ticketInputLine"
                              v-model="data.complaint"
                            />
                          </div>
                        </div>
                      </div>
                    </center>
                  </b-card>
                </div>
                <div class="rowFields mx-auto row">
                  <b-card bg-variant="light" style="width:100%">
                    <center>
                      <div class="rowFields2 mx-auto row">
                        <div class="col-lg-5">
                          <p class="textLabel-2">FIELD SERVICE REPORT</p>
                        </div>
                      </div>

                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Engineer's Name:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Date</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Time Started:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Time Finished:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Equipment:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Brand:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                      <div class="rowFields2 mx-auto row">
                        <div style="width:20%">
                          <p class="textLabel-1">Serial Number:</p>
                        </div>
                        <div style="width:80%">
                          <div class="input-group">
                            <input type="text" class="form-control ticketInputLine" />
                          </div>
                        </div>
                      </div>
                    </center>
                  </b-card>
                </div>
                <div class="rowFields mx-auto row">
                  <b-card bg-variant="light" style="width:100%">
                    <table class="table table-bordered">
                      <thead class="thead-dark">
                        <tr>
                          <th scope="col">Tests to be Done</th>
                          <th scope="col">
                            <i slot="extra" class="icon fas fa-check"></i> If
                            Done
                          </th>
                          <th scope="col">Diagnostics/Findings</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="rowFields2 mx-auto row">
                      <div style="width:20%">
                        <p class="textLabel-1">Things to Bring:</p>
                      </div>
                      <div style="width:80%">
                        <div class="input-group">
                          <input type="text" class="form-control ticketInputLine" />
                        </div>
                      </div>
                    </div>
                    <div class="rowFields2 mx-auto row">
                      <div style="width:35%">
                        <p class="textLabel-1">Diagnosis/Recommended Solution:</p>
                      </div>
                      <div style="width:65%">
                        <div class="input-group">
                          <input type="text" class="form-control ticketInputLine" />
                        </div>
                      </div>
                    </div>
                    <div class="rowFields2 mx-auto row">
                      <div style="width:20%">
                        <p class="textLabel-1">Action Taken/Date:</p>
                      </div>
                      <div style="width:80%">
                        <div class="input-group">
                          <input type="text" class="form-control ticketInputLine" />
                        </div>
                      </div>
                    </div>
                  </b-card>
                </div>
                <div class="rowFields mx-auto row">
                  <b-card bg-variant="light" style="width:100%">
                    <div class="rowFields2 mx-auto row">
                      <label class="pull-left">
                        <strong>
                          Customer (end user) Acknowledgement: (to be completed
                          by customer)
                        </strong>
                      </label>
                    </div>
                    <div class="rowFields3 mx-auto row">
                      <label class="pull-left">We/I Acknowledge that:</label>
                    </div>
                    <div class="rowFields3 mx-auto row">
                      <div class="pretty p-icon p-curve p-smooth checkTest">
                        <input type="checkbox" />
                        <div class="state p-success">
                          <i class="icon mdi mdi-check"></i>
                          <label>
                            1. The above-stated services, installation, repair
                            and/or replacement parts were delivered, installed
                            and fully operational.
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="rowFields3 mx-auto row">
                      <div class="pretty p-icon p-curve p-smooth checkTest">
                        <input type="checkbox" />
                        <div class="state p-success">
                          <i class="icon mdi mdi-check"></i>
                          <label>2. The above-described unit/s was/were pulled out.</label>
                        </div>
                      </div>
                    </div>
                    <div class="rowFields3 mx-auto row">
                      <div class="pretty p-icon p-curve p-smooth checkTest">
                        <input type="checkbox" />
                        <div class="state p-success">
                          <i class="icon mdi mdi-check"></i>
                          <label>3. Others:</label>
                        </div>
                      </div>
                      <div class="input-group" hidden>
                        <input type="text" class="form-control ticketInputLine2" />
                      </div>
                    </div>
                    <div class="rowFields3 mx-auto row">
                      <div style="width:60%">
                        <label class="pull-left">
                          How satisfied are you with this service call?
                          <br />(please circle the rating most appropriately reflect
                          your satisfaction level)
                        </label>

                        <center style="margin-top:35px">
                          <strong>RATING:</strong>
                        </center>
                        <hr />
                        <div style="display:flex;width:100%">
                          <label style="width:25%;flex-grow:1">
                            <center>
                              1
                              <br />Most Dissatisfied
                            </center>
                          </label>
                          <label style="width:25%;flex-grow:1">
                            <center>
                              <br />Dissatisfied
                            </center>
                          </label>
                          <label style="width:25%;flex-grow:1">
                            <center>
                              3
                              <br />Neutral
                            </center>
                          </label>
                          <label style="width:25%;flex-grow:1">
                            <center>
                              4
                              <br />Satisfied
                            </center>
                          </label>
                          <label style="width:25%;flex-grow:1">
                            <center>
                              5
                              <br />Most Satisfied
                            </center>
                          </label>
                        </div>

                        <center>
                          <div style="width:60%;margin-top:40px">
                            <hr />
                            <strong>Client's Signature over Printed Name/Date</strong>
                          </div>
                        </center>
                      </div>
                      <div style="width:5%"></div>
                      <div style="width:35%">
                        <center style="margin-top:30px">
                          <strong>COMMENTS:</strong>
                          <br />
                          <hr />
                          <hr />
                          <hr />
                          <hr />
                          <hr />
                          <hr />
                          <hr />
                        </center>
                      </div>
                    </div>
                  </b-card>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="printElement('printFSR')">Print</b-button>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import datePicker from "vue-bootstrap-datetimepicker";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";

export default {
  props: ["data"],
  components: {
    "model-list-select": ModelListSelect,
    "date-picker": datePicker,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      user: {},
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
  },

  mounted() {},
  methods: {
    printElement(id) {
      // window.print();
      // this.$htmlToPaper("printFSR");
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
    }
  }
};
</script>
<style scoped>
@media print {
  .logo {
    width: 25%;
  }
  @page {
    size: 8.5in 13in;
    orientation: portrait;
  }
  #printFSR {
    visibility: visible;
  }
}
@media screen {
  .logo {
    width: 85%;
  }
}
.trouble-bcard > .card-body {
  padding: 10px;
  padding-left: 13px;
  max-width: 100%;
  background: rgb(255, 255, 255);
  border: none;
  margin-top: 10px;
  margin-bottom: 20px;
}
.trouble-bcard2 {
  background: none;
  margin-top: -20px;
  border: none;
}
.rowFields2 {
  margin-top: 1px;
  font-size: 12px;
  text-align: left;
  display: flex;
  width: 100%;
}
.rowFields3 {
  margin-top: 18px;
  font-size: 12px;
  text-align: left;
  flex: 1;
}
.textLabel {
  margin-top: 1px;
  font-size: 14px;
}
.textLabel-2 {
  margin-top: 2px;
  font-weight: bold;
  font-size: 16px;
  color: darkgreen;
}
.textLabel-1 {
  margin-top: 6px;
  font-weight: bold;
  font-size: 12px;
}
.text-area {
  /* flex-grow: 1; */
  width: 25%;
  font-size: 13px;
  text-align: center;
  justify-content: left;
}
.troubleCheckbox {
  display: flex;
  width: 17%;
  float: left;
  margin-top: 7px;
  font-size: 12px;
  float: left;
}
.recommendation {
  display: flex;
  width: auto;
  float: left;
  margin-top: 7px;
  font-size: 12px;
  float: left;
}
.textLabel {
  margin-top: 6px;
  float: left;
}
.font-header {
  font-family: Arial, Helvetica, sans-serif;

  line-height: 20px;
}
.bold {
  font-weight: bold;
}
.ticketInputLine {
  box-shadow: none;
  border: none;
  border-bottom: 1px solid black;
  margin-top: -5px;
  width: 100%;
  float: left;
  margin-left: -50px;
  background: none;
  border-radius: 0 0 0 0;
  font-family: "Montserrat", "Helvetica Neue", Arial, sans-serif;
  font-size: 12px;
}
.ticketInputLine:focus {
  box-shadow: none;
  outline: none;
}
.ticketInputLine2 {
  box-shadow: none;
  border: none;
  border-bottom: 1px solid black;
  margin-top: -5px;
  width: 150px;
  float: right;
  background: none;
  border-radius: 0 0 0 0;
  font-family: "Montserrat", "Helvetica Neue", Arial, sans-serif;
  font-size: 12px;
}
.ticketInputLine2:focus {
  box-shadow: none;
  outline: none;
}
.body-font {
  font-family: "Montserrat", "Helvetica Neue", Arial, sans-serif;
  font-size: 13px;
  line-height: 1.5em;
  font-weight: bold;
}
</style>
