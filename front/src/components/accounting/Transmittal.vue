<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Transmittal</p>
      </div>

      <div class="elClr panel-body">
        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Day:</p>
            </div>
            <div class="col-lg-9">
              <input
                type="number"
                class="form-control"
                placeholder="Day"
                max="31"
                min="1"
                v-model="data.day"
              />
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Branch:</p>
            </div>
            <div class="col-lg-9">
              <!-- <model-list-select
                @input="generate"
                :list="branches"
                v-model="data.region_id"
                option-value="id"
                option-text="name"
                placeholder="Select/Search a Region..."
              ></model-list-select>-->

              <multiselect
                v-model="data.branches"
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
            </div>
          </div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
              <button @click="generate" type="button" class="btn btn-success btn-labeled">Generate</button>
            </div>
          </div>
        </div>
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input v-model="tblFilter" placeholder="Filter"></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
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
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import Multiselect from "vue-multiselect";

export default {
  components: {
    multiselect: Multiselect,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "client.name", label: "Name", sortable: true },
        { key: "sum", label: "Total Balance", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 20,
      pageOptions: [10, 20, 50, 100],
      data: {
        day: 0,
        branches: []
      },
      user: [],
      branches: [],
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
    this.totalRows = this.items.length;
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
      this.$http.get("api/Branch").then(response => {
        this.branches = response.body;
      });
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
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    generate() {
      console.log(this.data);
      if (this.data.day != 0 && this.data.branches.length > 0) {
        this.$root.$emit("pageLoading");
        this.$http
          .post("api/Billing/getTransmittal", this.data)
          .then(response => {
            this.$root.$emit("pageLoaded");
            this.items = response.body;
            this.totalRows = this.items.length;
          })
          .catch(response => {
            swal({
              title: "Error",
              text: response.body.error,
              icon: "error",
              dangerMode: true
            });
            this.tblisBusy = false;
            this.$root.$emit("pageLoaded");
          });
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
</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css" />;
