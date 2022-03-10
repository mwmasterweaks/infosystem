<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <span class="elClr panel-title">
          <div class="rowFields mx-auto row">
            <div class="col-lg-1">Map</div>
            <div class="col-lg-3">
              <input
                type="text"
                class="form-control"
                v-b-tooltip.hover
                title="Search Coordinates"
                placeholder="Coordinates (lat,lng)"
                v-model.trim="search_coor"
                v-on:keyup.enter="search_coordinates"
              />
            </div>
            <div class="col-lg-5"></div>
            <div class="col-lg-3">
              <button
                @click="addClosure"
                v-if="roles.operator"
                type="button"
                class="btn btn-success btn-labeled pull-right margin-right-10"
              >Add Closure</button>
              <button
                @click="addNode"
                v-if="roles.operator"
                type="button"
                class="btn btn-success btn-labeled pull-right margin-right-10"
              >Add Node</button>
            </div>
          </div>
        </span>
      </div>

      <div class="elClr panel-body">
        <div class="rowFields mx-auto row">
          <div
            class="scrollmenu col-lg-2"
            style="border-style: outset; min-height: 800px; max-height: 800px;"
          >
            <table class="sumReportTable">
              <tr class="sumReportTR">
                <td>
                  <b>DP :</b>
                </td>
                <td>{{sum_report.dp}}</td>
              </tr>
              <tr class="sumReportTR">
                <td>
                  <b>PORTS :</b>
                </td>
                <td>{{sum_report.ports}}</td>
              </tr>
              <tr class="sumReportTR">
                <td>
                  <b>OCCUPANCY :</b>
                </td>
                <td>{{sum_report.occupancy}}</td>
              </tr>
              <tr class="sumReportTR">
                <td>OCCUPIED :</td>
                <td>{{sum_report.occupied}}</td>
              </tr>
              <tr class="sumReportTR">
                <td>VACANT :</td>
                <td>{{sum_report.vacant}}</td>
              </tr>
            </table>
          </div>
          <div class="col-lg-8" style="border-style: outset; min-height: 800px; max-height: 800px;">
            <div id="floating-panel">
              <span
                style="color:red"
                id="map_hint1"
              >* Click the map to draw a line where the fiber going. *</span>
              <br />
              <span
                style="color:red"
                id="map_hint2"
              >* To End drawing a line click a marker and attach the fiber *</span>
              <br />
              <button id="btnUndo" @click="undo_path" disabled>Undo</button>
              <button id="btnRedo" @click="redo_path" disabled>Redo</button>
              <button @click="cancel_path">Cancel</button>
            </div>

            <GmapMap
              ref="gmap1"
              :center="coordinates"
              :zoom="map_zoom"
              map-type-id="satellite"
              style="width: 100%; height: 100%"
              :clickable="true"
              @click="map_clicked"
            >
              <gmap-polyline
                v-bind:path.sync="tempPath"
                v-bind:options="{ strokeColor: '#ff0000ff' }"
                v-bind:clickable="true"
              ></gmap-polyline>

              <gmap-polyline
                :key="m.hardfiber_id"
                v-for="m in hardfiber_coordinates"
                v-bind:path.sync="m.coor"
                v-bind:options="{ strokeColor: '#ff0000ff' }"
                v-bind:clickable="true"
              ></gmap-polyline>

              <gmap-info-window
                @closeclick="window_open = false"
                :opened="window_open"
                :position="window_position"
                :options="{
                  pixelOffset: {
                    width: 0,
                    height: -40
                  }
                }"
              >
                <p style="color:red">Name: {{ window_details.name }}</p>
                <!-- <p style="color:red">Type: {{ window_details.type }}</p> -->
                <button
                  class="btn btn-success btn-labeled"
                  v-if="booleanAddPath"
                  @click="attach_fiber"
                >Attach</button>
                <button @click="marker_view_clicked" class="btn btn-success btn-labeled" v-else>View</button>
              </gmap-info-window>

              <GmapMarker
                :key="index"
                v-for="(m, index) in markers"
                :position.sync="m.position"
                :clickable="true"
                :draggable="true"
                :animation="2"
                :label="m.name"
                @dragend="updateCoordinates($event.latLng, index, 'markers')"
                @click="openWindow($event.latLng, m, index, 'marker')"
              />
              <!-- v-for="(m, index) in closures.filter(m => m.dp_group_id != 1)" -->
              <GmapMarker
                :key="m.name"
                v-for="(m, index) in closure_markers"
                :position.sync="m.position"
                :clickable="true"
                :draggable="m.draggable"
                :animation="2"
                :icon="{
                   url: require(`../../../img/${m.icon}.png`),
                  scaledSize: { width: 26, height: 40 },
                  labelOrigin: { x: 10, y: -10 }
                }"
                :label="{ text: m.name, color: 'white', fontSize: '12px' }"
                @dragend="updateCoordinates($event.latLng, index, 'closures')"
                @click="openWindow($event.latLng, m, index, 'closure')"
              />

              <GmapMarker
                :key="m.name"
                v-for="m in client_marker"
                :position.sync="m.position"
                :animation="2"
                :icon="{
                  url: require('../../../img/marker_user.png'),
                  scaledSize: { width: 26, height: 40 },
                  labelOrigin: { x: 10, y: -10 }
                }"
                :label="{ text: m.name, color: 'white', fontSize: '12px' }"
              />

              <GmapMarker
                :key="m.name"
                v-for="m in marker_search_coor"
                :position.sync="m.position"
                :animation="2"
                :label="{ text: m.name, color: 'white', fontSize: '15px' }"
              />

              <GmapMarker
                :key="m.name"
                v-for="(m, index) in nodes"
                :position.sync="m.position"
                :clickable="true"
                :draggable="m.draggable"
                :animation="2"
                :icon="{
                  url: require('../../../img/marker_node.png'),
                  scaledSize: { width: 26, height: 40 },
                  labelOrigin: { x: 10, y: -10 }
                }"
                :label="{ text: m.name, color: 'white', fontSize: '15px' }"
                @dragend="updateCoordinates($event.latLng, index, 'node')"
                @click="openWindow($event.latLng, m, index, 'node')"
              />
            </GmapMap>
            <!-- <img src="../../../img/marker_closure.png" alt="" /> -->
          </div>

          <div
            class="scrollmenu col-lg-2"
            style="border-style: outset; min-height: 800px; max-height: 800px;"
          >
            <v-treeview
              v-model="selection"
              selectable
              return-object
              :items="treeview_item"
              :open="open"
              activatable
              hoverable
              rounded
              selection-type="leaf"
              expand-icon="mdi-chevron-down"
              open-on-click
              @update:open="test"
            >
              <template v-slot:prepend="{ item, open }">
                <span v-if="!item.file">
                  <v-icon>{{ open ? "mdi-folder-open" : "mdi-folder" }}</v-icon>
                </span>
                <span v-else>
                  <img
                    class="treeview_icon"
                    v-if="item.file == 'user'"
                    src="../../../img/marker_user.png"
                    alt
                  />
                  <img
                    class="treeview_icon"
                    v-if="item.file == 'closure'"
                    :src="require(`../../../img/${item.icon}.png`)"
                    alt
                  />
                  <img
                    class="treeview_icon"
                    v-if="item.file == 'node'"
                    src="../../../img/marker_node.png"
                    alt
                  />
                </span>

                <!-- <v-icon >
                </v-icon>
                <v-icon v-else>
                {{ files[item.file] }}
                </v-icon>-->
              </template>

              <template slot="label" slot-scope="{ item }">
                <a @dblclick="treeview_dblclick(item)">{{ item.name }}</a>
              </template>
            </v-treeview>
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
    <!-- dari ang mga modal -->
    <modal_closure_marker_click v-bind:data="window_details"></modal_closure_marker_click>
    <modal_node_marker_click v-bind:data="window_details"></modal_node_marker_click>
    <modal_add_hardfiber v-bind:data="add_hard_details"></modal_add_hardfiber>
  </div>
</template>
<script>
import { gmapApi } from "vue2-google-maps";
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import modal_closure_marker_click from "../../modal_vue/closure_marker_click.vue";
import modal_node_marker_click from "../../modal_vue/node_marker_click.vue";

import modal_add_hardfiber from "../../modal_vue/modal_add_hardfiber.vue";

export default {
  components: {
    modal_closure_marker_click: modal_closure_marker_click,
    modal_node_marker_click: modal_node_marker_click,
    modal_add_hardfiber: modal_add_hardfiber,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      map: null,
      map_zoom: 13,
      search_coor: "",
      coordinates: {
        lat: 0,
        lng: 0
      },
      selection: [],
      markers: [],
      tempPath: [],
      closures: [],
      marker: {
        position: {
          lat: 7.0688182,
          lng: 125.59859859999999
        }
      },
      marker_search_coor: [],
      shape: {
        coords: [5, 5, 10, 15, 15, 15, 15, 10],
        type: "poly"
      },
      pathMarkers: [],
      window_open: false,
      window_position: {
        lat: 0,
        lng: 0
      },
      window_details: {
        name: "",
        type: "",
        closure_type_id: 0
      },
      open: ["public"],
      booleanAddPath: false,
      add_hard_details: {},
      hardfiber_coordinates: [],
      store_redo: [],
      user: [],
      roles: [],
      items: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.$getLocation({
      enableHighAccuracy: true
    })
      .then(coordinates => {
        console.log("got coor");
        console.log(coordinates);
        this.coordinates = coordinates;
        // this.markers[0].position = this.coordinates;
      })
      .catch(error => {
        console.log("no coor");
        var coor = {
          lat: 7.040641,
          lng: 125.577053
        };
        this.coordinates = coor;
      });

    //console.log(this.markers[0].position);
  },
  mounted() {
    this.load();
    this.$refs.gmap1.$mapPromise.then(map => {
      this.map = map;
    });

    this.$root.$on("delete_closure", item => {
      this.items = item;
    });

    this.$root.$on("update_closures", item => {
      this.closures = item;
    });

    this.$root.$on("update_closure", (item, index) => {
      this.closures.push(item);
      this.markers.splice(index, 1);
    });

    this.$root.$on("delete_node", index => {
      this.nodes.splice(index, 1);
    });

    this.$root.$on("update_node", (item, index) => {
      this.nodes.push(item);
      this.markers.splice(index, 1);
    });
    this.$root.$on("draw_line", item => {
      this.add_hard_details.end1 = item.wit;
      this.add_hard_details.end1_id = item.id;
      this.tempPath = [];
      this.store_redo = [];
      var coor = {
        lat: item.position.lat,
        lng: item.position.lng
      };
      this.tempPath.push(coor);
      this.booleanAddPath = true;
      this.window_open = false;
      document.getElementById("floating-panel").style.display = "inline";
      document.getElementById("btnUndo").disabled = true;
      document.getElementById("btnRedo").disabled = true;
    });

    this.$root.$on("call_undo", () => {
      this.undo_path();
    });

    this.$root.$on("update_hardfiber_coordinates", item => {
      console.log(item);
      console.log(this.hardfiber_coordinates);
      this.hardfiber_coordinates.push(item);
      document.getElementById("floating-panel").style.display = "none";
      this.tempPath = [];
      this.booleanAddPath = false;
      this.window_open = false;
    });
  },
  computed: {
    mapCoordinates() {
      if (!this.map) {
        return {
          lat: 0,
          lng: 0
        };
      }
      return {
        lat: this.map.getCenter().lat(),
        lng: this.map.getCenter().lng()
        // lat: this.map
        //   .getCenter()
        //   .lat()
        //   .toFixed(4),
        // lng: this.map
        //   .getCenter()
        //   .lng()
        //   .toFixed(4)
      };
    },
    treeview_item() {
      return this.getNodes(this.items);
    },
    client_marker() {
      if (this.selection.length > 0) {
        var retVal = [];
        this.selection.forEach(item => {
          if (item.file == "user") {
            item.position = {
              lat: item.lat,
              lng: item.lng
            };
            item.marker_type = "client";
            retVal.push(item);
          }
        });
        return retVal;
      } else return [];
    },
    closure_markers() {
      if (this.selection.length > 0) {
        var retVal = [];
        this.selection.forEach(item => {
          if (item.file == "closure") {
            if (retVal.some(ret => ret.name === item.name)) {
              //
            } else {
              item.position = {
                lat: item.lat,
                lng: item.lng
              };
              //item.draggable = true;
              item.marker_type = "closure";
              retVal.push(item);
            }
          }
        });
        return retVal;
      } else return [];
    },
    nodes() {
      if (this.selection.length > 0) {
        var retVal = [];
        this.selection.forEach(item => {
          if (item.file == "node") {
            item.position = {
              lat: item.lat,
              lng: item.lng
            };
            item.marker_type = "node";
            retVal.push(item);
          }
        });
        return retVal;
      } else return [];
    },
    sum_report() {
      if (this.selection.length > 0) {
        var retVal = {
          dp: 0,
          ports: 0,
          occupancy: "0%",
          occupied: 0,
          vacant: 0
        };
        this.selection.forEach(item => {
          if (item.file == "closure" && item.icon != "lcp") retVal.dp += 1;
          if (item.occupied_port != null) retVal.occupied += item.occupied_port;
          if (item.total_port != null) retVal.ports += item.total_port;
          if (item.vacant_port != null) retVal.vacant += item.vacant_port;
        });
        if (retVal.ports > 0) {
          retVal.occupancy = (retVal.occupied / retVal.ports) * 100;

          //round off dari
          retVal.occupancy = retVal.occupancy.toFixed(2) + "%";
        }

        return retVal;
      } else
        return {
          dp: 0,
          ports: 0,
          occupancy: "0%",
          occupied: 0,
          vacant: 0
        };
    }
  },
  methods: {
    test() {
      console.log("dari");
      console.log(this.closure_markers);
    },
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";

          document.getElementById("navbarCalendar").style.backgroundColor = "";
          document.getElementById("navbarTicket").style.backgroundColor = "";
          document.getElementById("navbarInstallation").style.backgroundColor =
            "";
          document.getElementById("navbarMap").style.backgroundColor =
            "#2196f3";
          document.getElementById("navbarComponents").style.backgroundColor =
            "";
          document.getElementById("navbarAccounts").style.backgroundColor = "";
        }, 100);
      });
      this.$http.get("api/Closure").then(response => {
        this.closures = response.body;
      });

      this.$http.get("api/Node").then(response => {
        this.items = response.body;
        console.log(response.body);
      });

      this.$http.get("api/HardfiberCoordinate").then(response => {
        this.hardfiber_coordinates = response.body;
      });
    },
    getNodes(items) {
      return items.map(item => ({
        id: item.sub_id,
        name: item.name,
        file: item.file,
        lat: item.lat,
        lng: item.lng,
        real_id: item.id,
        draggable: false,
        save: true,
        type: item.type,
        closure_id: item.closure_id,
        icon: item.icon,
        nap_count: item.nap_count ? item.nap_count : null,
        occupied_port: item.occupied_port ? item.occupied_port : null,
        total_port: item.total_port ? item.total_port : null,
        vacant_port: item.vacant_port ? item.vacant_port : null,
        children: item.children ? this.getNodes(item.children) : []
      }));
    },
    updateCoordinates(position, index, name) {
      console.log(position);
      if (name == "markers") {
        this.markers[index].position.lat = position.lat();
        this.markers[index].position.lng = position.lng();
      }
      if (name == "closures") {
        this.closures[index].position.lat = position.lat();
        this.closures[index].position.lng = position.lng();
      }
      this.window_open = false;
    },
    openWindow(event, marker, index, wit) {
      this.window_position = event;
      marker.index = index;
      marker.wit = marker.marker_type;
      marker.isMap = true;

      this.window_details = marker;
      //console.log(marker);
      if (this.booleanAddPath) {
        if (marker.save) this.window_open = true;
      } else {
        if (this.window_details.wit == "closure") {
          if (marker.save) {
            this.$root.$emit("pageLoading");
            this.$http
              .get("api/Closure/" + marker.closure_id)
              .then(response => {
                this.window_details = response.body;
                console.log(this.window_details);
                console.log("openWindow function in closure");
                this.$bvModal.show("modal_closure_marker_click");
                this.$root.$emit("pageLoaded");
              });
          } else {
            this.$bvModal.show("modal_closure_marker_click");
          }
        }
        if (this.window_details.wit == "node") {
          if (marker.save) {
            this.$root.$emit("pageLoading");
            this.$http.get("api/Node/" + marker.real_id).then(response => {
              this.window_details = response.body;
              console.log(this.window_details);
              this.$bvModal.show("modal_node_marker_click");
              this.$root.$emit("pageLoaded");
            });
          } else {
            this.$bvModal.show("modal_node_marker_click");
          }
        }
      }
    },
    addClosure() {
      var new_marker = {
        position: {
          lat: this.mapCoordinates.lat,
          lng: this.mapCoordinates.lng
        },
        name: "untitled",
        type: "",
        save: false,
        marker_type: "closure"
      };
      this.markers.push(new_marker);
    },

    map_clicked(event) {
      //
      var coor = {
        lat: event.latLng.lat(),
        lng: event.latLng.lng()
      };
      if (this.booleanAddPath) this.add_point_in_temp_path(coor);
    },
    add_point_in_temp_path(coor) {
      this.tempPath.push(coor);
      document.getElementById("map_hint1").style.display = "none";
      document.getElementById("map_hint2").style.display = "inline";
      this.store_redo = [];
      document.getElementById("btnRedo").disabled = true;
      if (this.tempPath.length > 1) {
        document.getElementById("btnUndo").disabled = false;
      } else document.getElementById("btnUndo").disabled = true;
    },
    undo_path() {
      if (this.tempPath.length > 1) {
        var temp = [...this.tempPath];
        this.store_redo.push(temp);
        this.tempPath.splice(this.tempPath.length - 1, 1);
        document.getElementById("btnRedo").disabled = false;

        if (this.tempPath.length == 1)
          document.getElementById("btnUndo").disabled = true;
      }
    },
    redo_path() {
      //console.log(this.store_redo[this.store_redo.length - 1]);
      //console.log(this.tempPath);
      if (this.store_redo.length > 0) {
        this.tempPath = this.store_redo[this.store_redo.length - 1];
        this.store_redo.splice(this.store_redo.length - 1, 1);
        document.getElementById("btnUndo").disabled = false;
        if (this.store_redo.length == 0)
          document.getElementById("btnRedo").disabled = true;
      }
    },
    cancel_path() {
      this.tempPath = [];
      this.store_redo = [];
      this.booleanAddPath = false;
      this.window_open = false;
      document.getElementById("floating-panel").style.display = "none";
    },
    attach_fiber() {
      console.log(this.window_details);
      var coor = {
        lat: this.window_position.lat(),
        lng: this.window_position.lng()
      };
      this.add_point_in_temp_path(coor);
      this.add_hard_details.end2 = this.window_details.wit;
      this.add_hard_details.end2_id = this.window_details.id;
      this.add_hard_details.coordinates = this.tempPath;
      this.add_hard_details.type = "";
      this.$bvModal.show("modal_add_hardfiber");
    },
    addNode() {
      var new_marker = {
        position: {
          lat: this.mapCoordinates.lat,
          lng: this.mapCoordinates.lng
        },
        name: "untitled",
        save: false,
        marker_type: "node"
      };
      this.markers.push(new_marker);
    },
    marker_view_clicked() {
      if (this.window_details.wit == "closure")
        this.$bvModal.show("modal_closure_marker_click");
    },
    treeview_dblclick(item) {
      console.log(item);
      if (item.lat != null) {
        if (item.lat != 0) {
          this.map.setZoom(19);
          this.map.setCenter({
            lat: parseFloat(item.lat),
            lng: parseFloat(item.lng)
          });
        } else swal("no coordinates");
      } else swal("no coordinates");
    },
    search_coordinates() {
      if (this.search_coor != null) {
        this.marker_search_coor = [];
        var temp = this.search_coor.split(",");
        this.map.setZoom(19);
        this.map.setCenter({
          lat: parseFloat(temp[0]),
          lng: parseFloat(temp[1])
        });

        var new_marker = {
          position: {
            lat: this.mapCoordinates.lat,
            lng: this.mapCoordinates.lng
          },
          name: this.search_coor,
          type: "",
          save: false,
          marker_type: "search_coor"
        };
        this.marker_search_coor.push(new_marker);
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

#floating-panel {
  position: absolute;
  top: 20px;
  left: 40%;
  z-index: 5;
  background-color: #fff;
  padding: 5px;
  border: 1px solid #999;
  text-align: center;
  line-height: 30px;
  padding-left: 10px;
  font-family: Arial;
  box-shadow: 0 2px 2px rgba(33, 33, 33, 0.4);
  display: none;
}
#map_hint2 {
  display: none;
}
.sumReportTR {
  font-size: 15px;
}
.sumReportTR > td {
  padding: 4px;
  font-size: 14px;
}
.sumReportTable {
  margin-top: 20px;
}
</style>
