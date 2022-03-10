<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="panel-heading">
      <p class="elClr panel-title">
        Joined Bucket Infosystem Clients
        <button
          type="button"
          @click="getAll"
          class="btn btn-success btn-labeled pull-right margin-right-10"
        >
          Load All
        </button>
      </p>
    </div>

    <div class="elClr panel-body">
      <div>
        <b-row style="margin:10px;">
          <b-col md="5" class="my-1">
            <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
              <b-input-group>
                <b-form-input
                  v-model="tblFilter"
                  placeholder="Filter"
                ></b-form-input>
                <b-input-group-append>
                  <b-button :disabled="!tblFilter" @click="tblFilter = ''"
                    >Clear</b-button
                  >
                </b-input-group-append>
              </b-input-group>
            </b-form-group>
          </b-col>
          <b-col md="5 " class="my-1"></b-col>

          <b-col md="2 " class="my-1">
            <b-form-group label-cols-sm="4" label="Show" class="mb-0">
              <b-form-select
                v-model="perPage"
                :options="pageOptions"
              ></b-form-select>
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
          head-variant=" elClr"
          @filtered="onFiltered"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template v-slot:cell(action)="row">
            <b-button
              variant="success"
              size="sm"
              title="Matched"
              @click="btnMatch(row.item.id)"
            >
              <i class="fa fa-fw fa-thumbs-up"></i
            ></b-button>
            <b-button
              variant="danger"
              size="sm"
              title="Unmatched"
              @click="btnUnmatch(row.item.id)"
            >
              <i class="fa fa-fw fa-thumbs-down"></i
            ></b-button>
          </template>
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
</template>
<script>
export default {
  components: {},
  data() {
    return {
      tblisBusy: true,
      items: [],
      fields: [
        { key: "bucketName", label: "Bucket Name", sortable: true },
        { key: "status", label: "Status", sortable: true },
        {
          key: "subscription_name",
          label: "Subscription Name",
          sortable: true
        },
        { key: "infoName", label: "Infosystem Name", sortable: true },
        { key: "action", label: "Action", sortable: true }
      ],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100]
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.loadTable();
    this.tblisBusy = false;
    this.totalRows = this.items.length;
  },
  mounted() {},
  updated() {},
  methods: {
    loadTable() {
      this.$http.get("api/getBucketInfoClient").then(function(response) {
        console.log(response.body);
        this.items = response.body;
        this.tblisBusy = false;
      });
    },
    getAll() {
      this.$http.get("api/getBucketInfoClient/all").then(function(response) {
        console.log(response.body);
        this.items = response.body;
        this.tblisBusy = false;
      });
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },

    btnMatch(id) {
      this.tblisBusy = true;
      this.$http.post("api/match/" + id).then(response => {
        console.log(response.body);
        // swal("Member Application #" + this.member.id + "has been approved!", {
        //   icon: "success"
        // });

        this.loadTable();
      });
    },
    btnUnmatch(id) {
      this.tblisBusy = true;
      this.$http.post("api/unmatch/" + id).then(response => {
        // swal("Member Application #" + this.member.id + "has been rejected!", {
        //   icon: "warning"
        // });
        this.loadTable();
      });
    }
  }
};
</script>
