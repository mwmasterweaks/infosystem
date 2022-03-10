<template>
  <div>
    <b-modal
      id="modal_add_hardfiber"
      size="sm"
      title="Hardfiber Form"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      :hide-header-close="true"
      :no-close-on-backdrop="true"
      :no-close-on-esc="true"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Core type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="hardfiber_core_type_list"
            v-model="data.type"
            option-value="id"
            option-text="name"
          ></model-list-select>
        </div>
      </div>

      <div slot="modal-footer" slot-scope="{  }">
        <button class="btn btn-success btn-labeled" @click="save_data">Attach</button>
        <button class="btn btn-success btn-labeled" @click="close_modal">Cancel</button>
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
      hardfiber_core_type_list: [
        {
          name: "12 core",
          id: "12 core"
        },
        {
          name: "24 core",
          id: "24 core"
        },
        {
          name: "48 core",
          id: "48 core"
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
    load() {},
    save_data() {
      this.$http.post("api/Hardfiber", this.data).then(response => {
        swal("Attached", "", "success");

        this.$bvModal.hide("modal_add_hardfiber");
        var temp = {
          coor: this.data.coordinates,
          hardfiber_id: response.body
        };
        this.$root.$emit("update_hardfiber_coordinates", temp);
      });
      // .catch(response => {
      //   swal({
      //     title: "Error",
      //     text: response.body.error,
      //     icon: "error",
      //     dangerMode: true
      //   });
      // });
    },
    close_modal() {
      this.$bvModal.hide("modal_add_hardfiber");
      this.$root.$emit("call_undo");
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
</style>


