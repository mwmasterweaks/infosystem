<template>
  <div>
    <b-modal
      id="modal_add_splitter"
      size="lg"
      title="Splitter Form"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">splitter type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="splitter_types"
            v-model="item.splitter_type_id"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Port type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="port_types"
            v-model="item.port_type"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Status:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="status_list"
            v-model="item.status"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div slot="modal-footer" slot-scope="{  }">
        <button class="btn btn-success btn-labeled" @click="save_data">Insert</button>
      </div>
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
      buffer_color: [
        {
          no: 1,
          color: "blue"
        },
        {
          no: 2,
          color: "Orange"
        },
        {
          no: 3,
          color: "Green"
        },
        {
          no: 4,
          color: "Brown"
        }
      ],
      core_color: [
        {
          no: 1,
          color: "Blue"
        },
        {
          no: 2,
          color: "Orange"
        },
        {
          no: 3,
          color: "Green"
        },
        {
          no: 4,
          color: "Brown"
        },
        {
          no: 5,
          color: "Slate"
        },
        {
          no: 6,
          color: "White"
        },
        {
          no: 7,
          color: "Red"
        },
        {
          no: 8,
          color: "Black"
        },
        {
          no: 9,
          color: "Yellow"
        },
        {
          no: 10,
          color: "Violet"
        },
        {
          no: 11,
          color: "Rose"
        },
        {
          no: 12,
          color: "Aqua"
        }
      ],
      item: {
        splitter_type_id: "",
        port_type: "",
        status: "Not Active"
      },
      splitter_types: [],
      port_types: [
        {
          name: "1x16",
          id: "1x16"
        },
        {
          name: "1x8",
          id: "1x8"
        },
        {
          name: "1x4",
          id: "1x4"
        }
      ],
      status_list: [
        {
          name: "Active",
          id: "Active"
        },
        {
          name: "Not Active",
          id: "Not Active"
        }
      ],
      user: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/SplitterType").then(response => {
        this.splitter_types = response.body;
      });
    },
    save_data() {
      this.item.user_id = this.user.id;
      this.item.user_name = this.user.name;
      this.item.attach_to = this.data.wit;
      this.item.attach_id = this.data.id;
      this.$http
        .post("api/Splitter", this.item)
        .then(response => {
          console.log(response.body);
          console.log(this.data.splitter_closure);
          swal("Created", "", "success");
          this.data.splitter_closure = response.body;
          this.$bvModal.hide("modal_add_splitter");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
        });
    },
    update_closure() {
      console.log(this.data);
      swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: ["No", "Yes"],
        dangerMode: true
      }).then(update => {
        if (update) {
          this.data.user_id = this.user.id;
          this.data.user_name = this.user.name;
          this.data.lat = this.data.position.lat;
          this.data.lng = this.data.position.lng;
          this.$http
            .put("api/Closure/" + this.data.id, this.data)
            .then(response => {
              console.log(response.body);
              console.log(this.data.splitter_closure);
              swal("Updated", "", "success");
              this.data.splitter_closure = response.body;
              this.$bvModal.hide("modal_closure_marker_click");
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
    },
    delete_closure() {
      if (this.roles.rm) {
        swal({
          title: "Are you sure?",
          text: "",
          icon: "warning",
          buttons: ["No", "Yes"],
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.$http
              .delete("api/Closure/" + this.data.id)
              .then(response => {
                this.$bvModal.hide("modal_closure_marker_click");
                this.$root.$emit("delete_closure", this.data.index);
                swal("Deleted", "", "success");
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
          text: "You are not allow to delete a Closure Type",
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
</style>


