<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Users
          <button
            v-b-modal="'modalAdd'"
            v-if="roles.create_account"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
          >Create User</button>
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
      <!-- modalResetPass ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalResetPass"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Reset Password"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="password"
              ref="password"
              class="form-control"
              v-b-tooltip.hover
              title="Input the password of the user"
              placeholder="Password"
              autocomplete="off"
              v-validate="{ required: true, min: 8 }"
              v-model.trim="user.password"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('password')"
            >{{ errors.first("password") }}</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Re-type Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="retype"
              class="form-control"
              v-b-tooltip.hover
              title="Please re-type the pasword of the user"
              placeholder="Re-type password"
              v-validate="'required|confirmed:password'"
              v-model.trim="user.password2"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('retype')"
            >The password confirmation does not match.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_account"
            @click="updateUser()"
          >Update</b-button>
        </template>
      </b-modal>
      <!-- End modalResetPass -->

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
        title="Manage User"
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
              title="Input the name of the user"
              placeholder="Name of the user"
              v-validate="'required'"
              v-model.trim="user.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="user.branch_id"
              option-value="id"
              option-text="name"
              name="branch"
              ref="branch"
              placeholder="Select/Search a Branch..."
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('branch')">Branch is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Email:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="email"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Email address of the user"
              placeholder="Email Address"
              v-validate="{ required: true, email: true }"
              v-model.trim="user.email"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('email')">Email is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Background Color:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="bgcolor"
              class="form-control"
              v-b-tooltip.hover
              title="Input Background Color"
              placeholder="Background Color"
              v-validate="'required'"
              v-model.trim="user.elBG"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('bgcolor')"
            >Background Color is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Text Color:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="txcolor"
              class="form-control"
              v-b-tooltip.hover
              title="Input Text Color"
              placeholder="Text Color"
              v-validate="'required'"
              v-model.trim="user.elClr"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('txcolor')"
            >Text Color is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Status:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="txcolor"
              class="form-control"
              v-b-tooltip.hover
              title="Input status (Active, Inactive)"
              placeholder="Status"
              v-validate="'required'"
              v-model.trim="user.status"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('txcolor')"
            >Text Color is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="warning"
            v-if="user_login.id == 1"
            v-b-modal.modalResetPass
          >Reset password</b-button>

          <b-button size="sm" variant="warning" v-if="roles.role" v-b-modal.modalRoles>Role</b-button>

          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_account"
            @click="updateUser()"
          >Update</b-button>
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_account"
            @click="deleteUser()"
          >Delete</b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->

      <!-- modalRoles ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalRoles"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Manage Roles"
      >
        <!-- form -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>USER:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_account"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_account"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_account"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- ACCOUNT -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>ACCOUNT:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_client"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_client"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_client"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- SCHEDULER -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>SCHEDULER:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_client_details"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_client_details"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_client_details"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- PACKAGE TYPE -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>PACKAGE TYPE:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_package_type"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_package_type"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_package_type"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>PACKAGE:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_package"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_package"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_package"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- BUSINESS TYPE -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>BUSINESS TYPE:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_business_type"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_business_type"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_business_type"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- MODEM -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>MODEM:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_modem"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_modem"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_modem"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- ENGINEER -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>ENGINEER:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_engineer"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_engineer"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_engineer"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- SALES -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>SALES:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_sales"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_sales"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_sales"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- REGION -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>REGION:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_region"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_region"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_region"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- AREA -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>AREA:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_area"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_area"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_area"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- BRANCH -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>BRANCH:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_branch"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_branch"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_branch"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>JOB ORDER:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_job_order"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_job_order"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_job_order"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>TICKET:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_ticket"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_ticket"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_ticket"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>TICKET STATUS:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_ticket_status"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_ticket_status"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_ticket_status"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>OLT:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_olt"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_olt"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_olt"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>PON:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_pon"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_pon"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_pon"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- CALENDAR EVENT -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>CALENDAR EVENT:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_event"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_event"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_event"
            ></p-check>
          </div>
        </div>
        <hr />
        <!-- TEAM -->
        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>TEAM:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_team"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_team"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_team"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>Bucket:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_bucket"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_bucket"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_bucket"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="account will show only the monitoring display">VIEWER:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.viewer"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="can change team assign in scheduling">ASSIGN TEAM:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.assign_team"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="can change date assign in scheduling">ASSIGN DATE:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.assign_date"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="can send CVC to the client">SEND CVC:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.sendcvc"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="has control in accounting">ACCOUNTING:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.accounting"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title>SALES:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.sales"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title>ACCOUNT MANAGEMENT:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.account_management"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="has control in helpdesk">HELPDESK:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.helpdesk"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b title="has control as operator">OPERATOR:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.operator"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>NETWORK:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.network"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>RM:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.rm"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>ADMIN:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.admin"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>SOA:</b>
          </div>

          <div class="col-lg-3">
            Create Bill:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_bill"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update SOA:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_soa"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete SOA:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_soa"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>BANK CODE:</b>
          </div>

          <div class="col-lg-3">
            Create:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_bankcode"
            ></p-check>
          </div>

          <div class="col-lg-3">
            Update:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.update_bankcode"
            ></p-check>
          </div>
          <div class="col-lg-3">
            Delete:
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.delete_bankcode"
            ></p-check>
          </div>
        </div>
        <hr />

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>RECEIVE PAYMENT:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.receive_payment"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style>
          <div class="col-lg-3">
            <b>WHT:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.create_wht"
            ></p-check>
          </div>
        </div>

        <div class="rowFields1 row" style v-if="user_login.id == 1">
          <div class="col-lg-3">
            <b title="Can Change roles">ROLE:</b>
          </div>

          <div class="col-lg-3">
            <p-check
              class="checkboxStyle p-switch p-fill"
              color="success"
              v-model="editRoles.roles.role"
            ></p-check>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="roleChecker('hd')">HD Role</b-button>
          <b-button size="sm" variant="success" @click="roleChecker('sales')">Sales Role</b-button>
          <b-button size="sm" variant="success" @click="roleChecker('accounting')">Accounting Role</b-button>
          <b-button size="sm" variant="success" @click="roleChecker('scheduler')">Scheduler Role</b-button>
          <b-button size="sm" variant="success" @click="roleChecker('rm')">RM role</b-button>
          <b-button size="sm" variant="success" @click="updateRoles()">Update</b-button>
        </template>
      </b-modal>
      <!-- End modalRoles -->

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
        title="User Form"
      >
        <!--Form-------->
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
              title="Input the name of the user"
              placeholder="Name of the user"
              v-validate="'required'"
              v-model.trim="user.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="user.branch_id"
              option-value="id"
              option-text="name"
              name="branch"
              ref="branch"
              placeholder="Select/Search a Branch..."
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('branch')">Branch is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Email:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="email"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Email address of the user"
              placeholder="Email Address"
              v-validate="{ required: true, email: true }"
              v-model.trim="user.email"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('email')">Email is required.</small>
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
              ref="password"
              class="form-control"
              v-b-tooltip.hover
              title="Input the password of the user"
              placeholder="Password"
              autocomplete="off"
              v-validate="{ required: true, min: 8 }"
              v-model.trim="user.password"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('password')"
            >{{ errors.first("password") }}</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Re-type Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="retype"
              class="form-control"
              v-b-tooltip.hover
              title="Please re-type the pasword of the user"
              placeholder="Re-type password"
              v-validate="'required|confirmed:password'"
              v-model.trim="user.password2"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('retype')"
            >The password confirmation does not match.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With Helpdesk Role:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              v-if="checkRoleHD"
              class="textLabel p-switch p-fill"
              color="success"
              @change="role_checked"
              v-model="user.withHDRole"
            ></p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With Sales Role:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              v-if="checkRoleSales"
              class="textLabel p-switch p-fill"
              color="success"
              @change="role_checked"
              v-model="user.withSalesRole"
            ></p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With Accounting Role:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              v-if="checkRoleAccounting"
              class="textLabel p-switch p-fill"
              color="success"
              @change="role_checked"
              v-model="user.withAccountingRole"
            ></p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With Scheduler Role:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              v-if="checkRoleScheduler"
              class="textLabel p-switch p-fill"
              color="success"
              @change="role_checked"
              v-model="user.withSchedulerRole"
            ></p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">With RM Role:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              v-if="checkRoleRM"
              class="textLabel p-switch p-fill"
              color="success"
              @change="role_checked"
              v-model="user.withRMRole"
            ></p-check>
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
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "email", sortable: true },
        { key: "branch.name", label: "Branch", sortable: true },
        { key: "status", label: "Status", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 10,
      pageOptions: [10, 20, 50],
      user: {
        id: "",
        branch_id: "",
        name: "",
        email: "",
        password: "",
        password2: "",
        withHDRole: false,
        withSalesRole: false,
        withAccountingRole: false,
        withSchedulerRole: false,
        withRMRole: false
      },
      checkRoleHD: true,
      checkRoleSales: true,
      checkRoleAccounting: true,
      checkRoleScheduler: true,
      checkRoleRM: true,
      branches: [],
      protocolOption: [
        { id: "Internet", name: "Internet" },
        { id: "Intranet", name: "Intranet" }
      ],
      roles: [],
      editRoles: {
        id: "",
        roles: {
          create_account: false,
          update_account: false,
          delete_account: false,
          create_client: false,
          update_client: false,
          delete_client: false,
          create_client_details: false,
          update_client_details: false,
          delete_client_details: false,
          create_package_type: false,
          update_package_type: false,
          delete_package_type: false,
          create_package: false,
          update_package: false,
          delete_package: false,
          create_modem: false,
          update_modem: false,
          delete_modem: false,
          create_engineer: false,
          update_engineer: false,
          delete_engineer: false,
          create_sales: false,
          update_sales: false,
          delete_sales: false,
          create_branch: false,
          update_branch: false,
          delete_branch: false,
          create_job_order: false,
          update_job_order: false,
          delete_job_order: false,
          create_ticket: false,
          update_ticket: false,
          delete_ticket: false,
          create_ticket_status: false,
          update_ticket_status: false,
          delete_ticket_status: false,
          create_olt: false,
          update_olt: false,
          delete_olt: false,
          create_pon: false,
          update_pon: false,
          delete_pon: false,
          create_event: false,
          update_event: false,
          delete_event: false,
          create_team: false,
          update_team: false,
          delete_team: false,
          create_area: false,
          update_area: false,
          delete_area: false,
          viewer: false,
          assign_team: false,
          assign_date: false,
          accounting: false,
          helpdesk: false,
          operator: false,
          role: false,
          rm: false,
          network: false,
          admin: false,
          sendcvc: false,
          sales: false,
          account_management: false,
          create_business_type: false,
          update_business_type: false,
          delete_business_type: false,
          create_bill: false,
          update_soa: false,
          delete_soa: false,
          create_bankcode: false,
          update_bankcode: false,
          delete_bankcode: false,
          receive_payment: false,
          create_wht: false,
          create_bucket: false,
          update_bucket: false,
          delete_bucket: false,
          create_region: false,
          update_region: false,
          delete_region: false
        }
      },
      user_login: {}
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.getUsers();
    this.roles = this.$global.getRoles();
    this.user_login = this.$global.getUser();

    if (this.user_login.id == 1) {
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
    getUsers() {
      this.$http.get("api/user").then(function(response) {
        this.items = response.body;
        this.tblisBusy = false;
        this.totalRows = this.items.length;
      });
    },
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

      this.$http.get("api/Branch").then(function(response) {
        this.branches = response.body;
        var temp = {
          id: 0,
          name: "All(can view all tickets and schedule in all branches)"
        };
        this.branches.unshift(temp);
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_account) {
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
      if (this.roles.update_account) {
        this.$bvModal.show("modalEdit");
        this.user = item;
        this.user.password = "";
        this.user.password2 = "";
        this.editRoles.id = item.id;
        this.editRoles.roles = item.roleList;
        this.editRoles.roless = item.roles;
      }
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnCreate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/user", this.user)
            .then(response => {
              swal("Created", "", "success");
              this.user.name = "";
              this.user.branch_id = "";
              this.user.email = "";
              this.user.password = "";
              this.user.password2 = "";
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              this.$bvModal.hide("modalAdd");
              this.$root.$emit("pageLoaded");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                this.$root.$emit("pageLoaded");
              });
            });
        }
      });
    },
    updateUser() {
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
              this.$root.$emit("pageLoading");
              this.$http
                .put("api/user/" + this.user.id, this.user)
                .then(response => {
                  this.items = response.body;
                  swal("Updated", "", "success");
                  this.$bvModal.hide("modalEdit");
                  this.$bvModal.hide("modalResetPass");
                  this.$root.$emit("pageLoaded");
                })
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  }).then(value => {
                    this.$root.$emit("pageLoaded");
                  });
                });
            }
          });
        }
      });
    },
    deleteUser() {
      if (this.roles.delete_account) {
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
              .delete("api/user/" + this.user.id)
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
          text: "You are not allow to delete a user",
          icon: "warning",
          buttons: true,
          dangerMode: true
        });
      }
    },
    updateRoles() {
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/user/updateRoles", this.editRoles)
            .then(response => {
              this.items = response.body;
              swal("Updated", "", "success");
              this.$bvModal.hide("modalRoles");
              this.$root.$emit("pageLoaded");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                this.$root.$emit("pageLoaded");
              });
            });
        }
      });
    },
    role_checked() {
      if (
        this.user.withHDRole ||
        this.user.withSalesRole ||
        this.user.withAccountingRole ||
        this.user.withSchedulerRole ||
        this.user.withRMRole
      ) {
        this.checkRoleHD = false;
        this.checkRoleSales = false;
        this.checkRoleAccounting = false;
        this.checkRoleScheduler = false;
        this.checkRoleRM = false;
        if (this.user.withHDRole) this.checkRoleHD = true;
        if (this.user.withSalesRole) this.checkRoleSales = true;
        if (this.user.withAccountingRole) this.checkRoleAccounting = true;
        if (this.user.withSchedulerRole) this.checkRoleScheduler = true;
        if (this.user.withRMRole) this.checkRoleRM = true;
      } else {
        this.checkRoleHD = true;
        this.checkRoleSales = true;
        this.checkRoleAccounting = true;
        this.checkRoleScheduler = true;
        this.checkRoleRM = true;
      }
    },
    roleChecker(role) {
      this.editRoles.roles = {
        create_account: false,
        update_account: false,
        delete_account: false,
        create_client: false,
        update_client: false,
        delete_client: false,
        create_client_details: false,
        update_client_details: false,
        delete_client_details: false,
        create_package_type: false,
        update_package_type: false,
        delete_package_type: false,
        create_package: false,
        update_package: false,
        delete_package: false,
        create_modem: false,
        update_modem: false,
        delete_modem: false,
        create_engineer: false,
        update_engineer: false,
        delete_engineer: false,
        create_sales: false,
        update_sales: false,
        delete_sales: false,
        create_branch: false,
        update_branch: false,
        delete_branch: false,
        create_job_order: false,
        update_job_order: false,
        delete_job_order: false,
        create_ticket: false,
        update_ticket: false,
        delete_ticket: false,
        create_ticket_status: false,
        update_ticket_status: false,
        delete_ticket_status: false,
        create_olt: false,
        update_olt: false,
        delete_olt: false,
        create_pon: false,
        update_pon: false,
        delete_pon: false,
        create_event: false,
        update_event: false,
        delete_event: false,
        create_team: false,
        update_team: false,
        delete_team: false,
        create_area: false,
        update_area: false,
        delete_area: false,
        viewer: false,
        assign_team: false,
        assign_date: false,
        accounting: false,
        helpdesk: false,
        operator: false,
        role: false,
        rm: false,
        network: false,
        admin: false,
        sendcvc: false,
        sales: false,
        account_management: false,
        create_business_type: false,
        update_business_type: false,
        delete_business_type: false,
        create_bill: false,
        update_soa: false,
        delete_soa: false,
        create_bankcode: false,
        update_bankcode: false,
        delete_bankcode: false,
        receive_payment: false,
        create_wht: false,
        create_bucket: false,
        update_bucket: false,
        delete_bucket: false,
        create_region: false,
        update_region: false,
        delete_region: false
      };

      if (role == "hd") {
        this.editRoles.roles.update_package_type = true;
        this.editRoles.roles.update_package = true;
        this.editRoles.roles.update_modem = true;
        this.editRoles.roles.create_ticket = true;
        this.editRoles.roles.update_ticket = true;
        this.editRoles.roles.update_olt = true;
        this.editRoles.roles.update_pon = true;
      } else if (role == "sales") {
        this.editRoles.roles.update_client = true;
        // this.editRoles.roles.create_package_type = true;
        // this.editRoles.roles.update_package_type = true;
        // this.editRoles.roles.create_package = true;
        // this.editRoles.roles.update_package = true;
        // this.editRoles.roles.create_engineer = true;
        // this.editRoles.roles.update_engineer = true;
        // this.editRoles.roles.create_sales = true;
        // this.editRoles.roles.update_sales = true;
        // this.editRoles.roles.sales = true;
        // this.editRoles.roles.create_business_type = true;
        // this.editRoles.roles.update_business_type = true;
      } else if (role == "accounting") {
        this.editRoles.roles.update_client = true;
        this.editRoles.roles.accounting = true;
        this.editRoles.roles.create_bill = true;
        this.editRoles.roles.receive_payment = true;
        this.editRoles.roles.create_wht = true;
      } else if (role == "scheduler") {
        this.editRoles.roles.create_client_details = true;
        this.editRoles.roles.update_client_details = true;
        this.editRoles.roles.create_modem = true;
        this.editRoles.roles.update_modem = true;
        this.editRoles.roles.create_engineer = true;
        this.editRoles.roles.update_engineer = true;
        this.editRoles.roles.create_sales = true;
        this.editRoles.roles.update_sales = true;
        this.editRoles.roles.create_job_order = true;
        this.editRoles.roles.update_job_order = true;
        this.editRoles.roles.create_team = true;
        this.editRoles.roles.update_team = true;
        this.editRoles.roles.assign_team = true;
        this.editRoles.roles.assign_date = true;
        this.editRoles.roles.operator = true;
        this.editRoles.roles.sendcvc = true;
      } else if (role == "rm") {
        this.editRoles.roles.create_account = true;
        this.editRoles.roles.update_account = true;
        this.editRoles.roles.create_client_details = true;
        this.editRoles.roles.update_client_details = true;
        this.editRoles.roles.create_package_type = true;
        this.editRoles.roles.update_package_type = true;
        this.editRoles.roles.create_package = true;
        this.editRoles.roles.update_package = true;
        this.editRoles.roles.create_modem = true;
        this.editRoles.roles.update_modem = true;
        this.editRoles.roles.create_engineer = true;
        this.editRoles.roles.update_engineer = true;
        this.editRoles.roles.create_sales = true;
        this.editRoles.roles.update_sales = true;
        this.editRoles.roles.create_region = true;
        this.editRoles.roles.update_region = true;
        this.editRoles.roles.create_job_order = true;
        this.editRoles.roles.update_job_order = true;
        this.editRoles.roles.create_ticket = true;
        this.editRoles.roles.update_ticket = true;
        this.editRoles.roles.create_ticket_status = true;
        this.editRoles.roles.update_ticket_status = true;
        this.editRoles.roles.create_olt = true;
        this.editRoles.roles.update_olt = true;
        this.editRoles.roles.create_pon = true;
        this.editRoles.roles.update_pon = true;
        this.editRoles.roles.create_event = true;
        this.editRoles.roles.update_event = true;
        this.editRoles.roles.create_team = true;
        this.editRoles.roles.update_team = true;
        this.editRoles.roles.assign_team = true;
        this.editRoles.roles.assign_date = true;
        this.editRoles.roles.operator = true;
        this.editRoles.roles.rm = true;
        this.editRoles.roles.sendcvc = true;
        this.editRoles.roles.create_business_type = true;
        this.editRoles.roles.update_business_type = true;
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

.rowFields1 {
  margin: 20px;
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
