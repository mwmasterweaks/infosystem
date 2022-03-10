<template>
  <div class="mx-auto col-md-12 modified-continer" style="height: 100%;">
    <!-- <div v-if="!((user.id == 999) || (user.id == 1000))"> -->
    <div v-if="!roles.viewer">
      <div class="mx-auto row">
        <full-calendar></full-calendar>
      </div>
      <div class="mx-auto row">
        <div class="mx-auto col-md-6">
          <div class="elBG-white panel">
            <div class="panel-heading">
              <p class="elClr-black panel-title">E-ticket Summary</p>
            </div>

            <div class="elClr-black panel-body">
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Chart :</p>
                </div>
                <div class="col-lg-9">
                  <model-list-select
                    :list="chartList"
                    v-model="selectedChart"
                    option-value="id"
                    option-text="name"
                    placeholder="Select a chart"
                  ></model-list-select>
                </div>
              </div>

              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Year :</p>
                </div>
                <div class="col-lg-9">
                  <model-list-select
                    :list="yearsValue"
                    v-model="selectedYear"
                    option-value="id"
                    option-text="name"
                    placeholder="Select a year"
                    @input="onChangeYear"
                  ></model-list-select>
                </div>
              </div>

              <div class="rowFields mx-auto row">
                <div class="col-lg-4" v-if="loaded">
                  <b>
                    <p class="textLabel text-center">Ticket Status</p>
                  </b>
                  <table class="tableDataSummary">
                    <tr>
                      <td class>Jan:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[0] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[0] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[0] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[0] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Feb:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[1] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[1] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[1] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[1] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Mar:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[2] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[2] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[2] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[2] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Apr:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[3] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[3] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[3] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[3] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>May:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[4] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[4] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[4] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[4] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Jun:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[5] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[5] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[5] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[5] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Jul:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[6] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[6] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[6] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[6] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Aug:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[7] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[7] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[7] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[7] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Sep:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[8] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[8] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[8] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[8] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Oct:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[9] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[9] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[9] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[9] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Nov:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[10] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[10] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[10] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[10] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Dec:</td>
                      <td class>
                        <span style="color:rgba(255, 206, 86, 1 );">{{ dataChart[0].data[11] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart[1].data[11] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart[2].data[11] }}</span>
                      </td>
                      <td class>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart[3].data[11] }}</span>
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="col-lg-8">
                  <h5>YEARLY</h5>
                  <line-chart
                    v-if="loaded && selectedChart == 'Line'"
                    :data="eticketCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <bar-chart
                    v-if="loaded && selectedChart == 'Bar'"
                    :data="eticketCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <pie-chart
                    v-if="loaded && selectedChart == 'Pie'"
                    :data="eticketCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <radar-chart
                    v-if="loaded && selectedChart == 'Radar'"
                    :data="eticketCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <polar-chart
                    v-if="loaded && selectedChart == 'Polar'"
                    :data="eticketCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />
                  <hr />
                  <h5>WEEKLY</h5>
                  <line-chart
                    v-if="loaded && selectedChart == 'Line'"
                    :data="eticketCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />
                  <bar-chart
                    v-if="loaded && selectedChart == 'Bar'"
                    :data="eticketCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <pie-chart
                    v-if="loaded && selectedChart == 'Pie'"
                    :data="eticketCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <radar-chart
                    v-if="loaded && selectedChart == 'Radar'"
                    :data="eticketCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <polar-chart
                    v-if="loaded && selectedChart == 'Polar'"
                    :data="eticketCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <div class="elBG divLoaderDiv" v-if="!loaded">
                    <img src="../img/loading.gif" class="imgLoader" />
                  </div>
                </div>

                <!-- <button v-on:click="changeData">Change data</button> -->
              </div>
            </div>
          </div>
        </div>
        <div class="mx-auto col-md-6">
          <div class="elBG-white panel">
            <div class="panel-heading">
              <p class="elClr-black panel-title">Installation Summary</p>
            </div>
            <div class="elClr-black panel-body">
              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Chart :</p>
                </div>
                <div class="col-lg-9">
                  <model-list-select
                    :list="chartList"
                    v-model="selectedChart1"
                    option-value="id"
                    option-text="name"
                    placeholder="Select a chart"
                  ></model-list-select>
                </div>
              </div>

              <div class="rowFields mx-auto row">
                <div class="col-lg-3">
                  <p class="textLabel">Year :</p>
                </div>
                <div class="col-lg-9">
                  <model-list-select
                    :list="yearsValue"
                    v-model="selectedYear1"
                    option-value="id"
                    option-text="name"
                    placeholder="Select a year"
                    @input="onChangeYear1"
                  ></model-list-select>
                </div>
              </div>

              <div class="rowFields mx-auto row">
                <div class="col-lg-4" v-if="loaded1">
                  <b>
                    <p class="textLabel text-center">Activated</p>
                  </b>
                  <table class="tableDataSummary">
                    <tr class>
                      <td class>Jan:</td>

                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[0] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[0] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[0] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[0] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[0] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Feb:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[1] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[1] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[1] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[1] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[1] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Mar:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[2] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[2] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[2] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[2] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[2] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Apr:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[3] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[3] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[3] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[3] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[3] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>May:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[4] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[4] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[4] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[4] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[4] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Jun:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[5] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[5] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[5] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[5] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[5] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Jul:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[6] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[6] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[6] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[6] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[6] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Aug:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[7] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[7] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[7] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[7] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[7] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Sep:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[8] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[8] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[8] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[8] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[8] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Oct:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[9] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[9] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[9] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[9] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[9] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Nov:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[10] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[10] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[10] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[10] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[10] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Dec:</td>
                      <td>
                        <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[11] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[11] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[11] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[11] }}</span>
                      </td>
                      <td>
                        <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[11] }}</span>
                      </td>
                    </tr>

                    <tr class>
                      <td class>Total:</td>
                      <td>
                        <span
                          style="color:rgba(255, 99, 132, 1);"
                        >{{ TotalActivated(dataChart1[0].data) }}</span>
                      </td>
                      <td>
                        <span
                          style="color:rgba(54, 162, 235, 1);"
                        >{{ TotalActivated(dataChart1[1].data) }}</span>
                      </td>
                      <td>
                        <span
                          style="color:rgba(255, 206, 86, 1);"
                        >{{ TotalActivated(dataChart1[2].data) }}</span>
                      </td>
                      <td>
                        <span
                          style="color:rgba(75, 192, 192, 1);"
                        >{{ TotalActivated(dataChart1[3].data) }}</span>
                      </td>
                      <td>
                        <span
                          style="color:rgba(153, 102, 255, 1);"
                        >{{ TotalActivated(dataChart1[4].data) }}</span>
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="col-lg-8">
                  <h5>YEARLY</h5>
                  <line-chart
                    v-if="loaded1 && selectedChart1 == 'Line'"
                    :data="installCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <bar-chart
                    v-if="loaded1 && selectedChart1 == 'Bar'"
                    :data="installCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <pie-chart
                    v-if="loaded1 && selectedChart1 == 'Pie'"
                    :data="installCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <radar-chart
                    v-if="loaded1 && selectedChart1 == 'Radar'"
                    :data="installCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <polar-chart
                    v-if="loaded1 && selectedChart1 == 'Polar'"
                    :data="installCollection"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />
                </div>
                <div class="elBG divLoaderDiv" v-if="!loaded1">
                  <img src="../img/loading.gif" class="imgLoader" />
                </div>
                <!-- <button v-on:click="changeData">Change data</button> -->
              </div>
              <hr />
              <div class="rowFields mx-auto row" v-if="loaded1">
                <div class="col-lg-6">
                  <h5>WEEKLY Installation Efficiency</h5>
                  <line-chart
                    v-if="loaded1 && selectedChart1 == 'Line'"
                    :data="installCollectionWeekPercent"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <bar-chart
                    v-if="loaded1 && selectedChart1 == 'Bar'"
                    :data="installCollectionWeekPercent"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <pie-chart
                    v-if="loaded1 && selectedChart1 == 'Pie'"
                    :data="installCollectionWeekPercent"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <radar-chart
                    v-if="loaded1 && selectedChart1 == 'Radar'"
                    :data="installCollectionWeekPercent"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <polar-chart
                    v-if="loaded1 && selectedChart1 == 'Polar'"
                    :data="installCollectionWeekPercent"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />
                </div>
                <div class="col-lg-6">
                  <h5>WEEKLY Plan and installed</h5>
                  <line-chart
                    v-if="loaded1 && selectedChart1 == 'Line'"
                    :data="installCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <bar-chart
                    v-if="loaded1 && selectedChart1 == 'Bar'"
                    :data="installCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <pie-chart
                    v-if="loaded1 && selectedChart1 == 'Pie'"
                    :data="installCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <radar-chart
                    v-if="loaded1 && selectedChart1 == 'Radar'"
                    :data="installCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />

                  <polar-chart
                    v-if="loaded1 && selectedChart1 == 'Polar'"
                    :data="installCollectionWeek"
                    :options="{responsive: true, maintainAspectRatio: false}"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else style="height: 100%;">
      <div class="row" style="height: 100%;">
        <div class="col-md-6">
          <div class="panel" style="height: 90%;">
            <div class="panel" style="margin-top: 0px; height: 33.3%; margin-bottom: 0px;">
              <div class="panel-heading" style="background-color: #04b52d; height: 5%;">
                <h5 class="elClr" style="margin-left:10px; margin-top: -10px;">TODAY</h5>
                <p
                  class="elClr size12"
                  style="position: relative; left: 90%; margin-top: -25px; width: 130px;"
                >{{ today_totalRows }} items found</p>
              </div>
              <div class="elClr elBG panel-body" style="height: 100%; padding-bottom: 2px;">
                <div
                  class="row tbl_container"
                  style=" margin-top: -25px; margin-bottom: -25px; padding-bottom: 0px;"
                >
                  <b-table
                    class="elClr tbl-display"
                    show-empty
                    striped
                    hover
                    outlined
                    fixed
                    :fields="today_fields"
                    :items="today_items"
                    :busy="today_tblisBusy"
                    :current-page="today_currentPage"
                    :per-page="today_perPage"
                    :tbody-tr-class="tblRowClass"
                    head-variant=" elClr"
                  >
                    <div slot="table-busy" class="text-center text-danger my-2">
                      <b-spinner class="align-middle"></b-spinner>
                      <strong>Loading...</strong>
                    </div>
                    <template v-slot:cell(statname)="data">
                      <span v-html="data.value"></span>
                    </template>
                    <template slot="table-caption"></template>
                  </b-table>
                </div>
              </div>
            </div>

            <div class="panel" style="margin-top: 0px; height: 33.3%; margin-bottom: 0px;">
              <div class="panel-heading" style="background-color:#04b52d; height: 5%;">
                <h5 class="elClr" style="margin-left:10px; margin-top: -10px;">TOMORROW</h5>

                <p
                  class="elClr size12"
                  style="position: relative; left: 90%; margin-top: -25px; width: 130px;"
                >{{ tom_totalRows }} items found</p>
              </div>
              <div class="elClr elBG panel-body" style="height: 100%; padding-bottom: 2px;">
                <div
                  class="row tbl_container"
                  style=" margin-top: -25px; margin-bottom: -25px; padding-bottom: 0px;"
                >
                  <b-table
                    class="elClr tbl-display"
                    show-empty
                    striped
                    hover
                    outlined
                    fixed
                    :fields="today_fields"
                    :items="tom_items"
                    :busy="tom_tblisBusy"
                    :current-page="tom_currentPage"
                    :per-page="tom_perPage"
                    :tbody-tr-class="tblRowClass"
                    head-variant=" elClr"
                  >
                    <div slot="table-busy" class="text-center text-danger my-2">
                      <b-spinner class="align-middle"></b-spinner>
                      <strong>Loading...</strong>
                    </div>
                    <template v-slot:cell(statname)="data">
                      <span v-html="data.value"></span>
                    </template>
                    <template slot="table-caption"></template>
                  </b-table>
                </div>
              </div>
            </div>

            <div class="panel" style="margin-top: 0px; height: 33.3%; margin-bottom: 0px;">
              <div class="panel-heading" style="background-color: #04b52d; height: 5%;">
                <h5 class="elClr" style="margin-left:10px; margin-top: -10px;">ALL PENDINGS</h5>
                <p
                  class="elClr size12"
                  style="position: relative; left: 90%; margin-top: -25px;  width: 130px;"
                >{{ week_totalRows }} items found</p>
              </div>
              <div class="elClr elBG panel-body" style="height: 100%; padding-bottom: 2px;">
                <div
                  class="row tbl_container"
                  style="margin-top: -25px; margin-bottom: -25px; padding-bottom: 0px;"
                >
                  <b-table
                    class="elClr tbl-display"
                    show-empty
                    striped
                    hover
                    outlined
                    :fixed="fixed"
                    :fields="week_fields"
                    :items="week_items"
                    :busy="week_tblisBusy"
                    :current-page="week_currentPage"
                    :per-page="week_perPage"
                    :tbody-tr-class="tblRowClass"
                    head-variant=" elClr"
                  >
                    <div slot="table-busy" class="text-center text-danger my-2">
                      <b-spinner class="align-middle"></b-spinner>
                      <strong>Loading...</strong>
                    </div>
                    <template v-slot:cell(statname)="data">
                      <span v-html="data.value"></span>
                    </template>

                    <template v-slot:cell(target_date)="row">
                      <center>
                        <span v-if="row.item.statname == 'Close'">Fixed</span>
                      </center>
                      <center>
                        <span v-if="row.item.statname == 'For Rebates'">Fixed</span>
                      </center>
                      <span
                        class="btn btn-success disabled"
                        style="width:100px; padding: 1px;"
                        v-if="row.item.target_date != null &&
                          row.item.target_date != datenow &&
                          row.item.target_date != dateTomorrow &&
                          row.item.target_date != dateYesterday &&
                          1 > (dateDiffInDays(datenow, row.item.target_date))"
                      >{{ row.item.target_date }}</span>
                      <span
                        class="btn btn-success disabled"
                        style="width:100px; padding: 1px;"
                        v-if="row.item.target_date == datenow"
                      >Today</span>
                      <span
                        class="btn btn-success disabled"
                        style="width:100px; padding: 1px;"
                        v-if="row.item.target_date == dateTomorrow"
                      >Tomorrow</span>
                      <span
                        class="btn btn-warning disabled"
                        style="width:100px; padding: 1px;"
                        v-if="row.item.target_date == dateYesterday"
                      >Yesterday</span>
                      <span
                        class="btn btn-danger disabled"
                        style="width:100px; padding: 1px;"
                        v-if="1 < (dateDiffInDays(datenow, row.item.target_date)) && row.item.target_date != null"
                      >{{ dateDiffInDays(datenow, row.item.target_date) }} Days delay</span>
                    </template>

                    <template slot="table-caption"></template>
                  </b-table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel" style="height: 90%;">
            <div class="row" style="height: 33.3%;">
              <div class="col-md-6" style="padding-right: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{ teams[0].name }} (Previous) {{teamA_prev_percent}}% Done</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamA_prev_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamA_prev_items"
                      :current-page="teamA_prev_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>
                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>

              <div class="col-md-6" style="padding-left: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{teams[1].name}} (Previous) {{teamB_prev_percent}}% Done</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamB_prev_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamB_prev_items"
                      :current-page="teamB_prev_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>
                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" style="height: 33.3%;">
              <div class="col-md-6" style="padding-right: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{teams[0].name}} (Today) {{teamA_today_percent}}% Done</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamA_today_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamA_today_items"
                      :current-page="teamA_today_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>

                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>

              <div class="col-md-6" style="padding-left: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{teams[1].name}} (Today) {{teamB_today_percent}}% Done</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamB_today_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamB_today_items"
                      :current-page="teamB_today_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>
                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" style="height: 33.3%;">
              <div class="col-md-6" style="padding-right: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{teams[0].name}} (Tomorrow)</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamA_tom_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamA_tom_items"
                      :current-page="teamA_tom_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>

                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>

              <div class="col-md-6" style="padding-left: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >{{teams[1].name}} (Tomorrow)</h5>
                    <p
                      class="elClr size12"
                      style="position: relative; left: 80%; margin-top: -25px;  width: 130px;"
                    >{{ teamB_tom_totalRows }} items found</p>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <b-table
                      class="elClr tbl-display"
                      show-empty
                      striped
                      hover
                      outlined
                      :fields="team_fields"
                      :items="teamB_tom_items"
                      :current-page="teamB_tom_currentPage"
                      :per-page="team_perPage"
                      :tbody-tr-class="tblRowClass"
                      head-variant=" elClr"
                    >
                      <div slot="table-busy" class="text-center text-danger my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                      </div>
                      <template v-slot:cell(statname)="data">
                        <span v-html="data.value"></span>
                      </template>
                      <template slot="table-caption"></template>
                    </b-table>
                  </div>
                </div>
              </div>
            </div>

            <!-- summary -->
            <div class="row" style="height: 33.3%; display:none;">
              <div class="col-md-6" style="padding-right: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5 class="elClr" style="margin-left:10px; margin-top: -10px;">TICKET SUMMARY</h5>
                  </div>
                  <div class="elClr elBG panel-body" style="height: 100%; padding: 10px;">
                    <center>
                      <div class="col-lg-9" v-if="loaded">
                        <line-chart
                          :data="eticketCollection"
                          :options="{responsive: true, maintainAspectRatio: false}"
                        />
                      </div>
                    </center>
                    <div class="elBG divLoaderDiv" v-if="!loaded">
                      <img src="../img/loading.gif" class="imgLoader" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6" style="padding-left: 0px; height: 100%;">
                <div class="panel" style="margin-top: 0px; height: 100%;">
                  <div class="panel-heading phead" style="height: 5%;">
                    <h5
                      class="elClr"
                      style="margin-left:10px; margin-top: -10px;"
                    >INSTALLATION SUMMARY</h5>
                  </div>
                  <div class="elClr-black elBG panel-body" style="height: 100%; padding: 10px;">
                    <center>
                      <div class="col-lg-9" style="height: 100%;" v-if="loaded1">
                        <line-chart
                          :data="installCollection"
                          :options="{responsive: true, maintainAspectRatio: false}"
                        />
                      </div>
                    </center>
                    <div class="elBG divLoaderDiv" v-if="!loaded1">
                      <img src="../img/loading.gif" class="imgLoader" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div
              class="elClr-black panel-body"
              style="background-color: #04b52d; height: 8%; display: none;"
            >
              <center>
                <div id="clock" style="margin-top: -15px;">
                  <table>
                    <tr>
                      <td>
                        <p class="date" style="margin-bottom: 0px;">{{ day }}</p>
                      </td>
                      <td rowspan="2">
                        <p class="time" style="margin-top: -20px; margin-left: 10px;">{{ time }}</p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="date">{{ date }}</p>
                      </td>
                    </tr>
                  </table>
                </div>
              </center>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import LineChart from "../packages/LineChart.js";
import BarChart from "../packages/Bar.js";
import PieChart from "../packages/Pie.js";
import RadarChart from "../packages/Radar.js";
import PolarChart from "../packages/PolarArea.js";
import { ModelListSelect } from "vue-search-select";
import FullCalendar from "./Calendar.vue";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
//import LineChart from "./LineChart.vue";

export default {
  name: "LineChartContainer",
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "full-calendar": FullCalendar,
    LineChart,
    BarChart,
    PieChart,
    RadarChart,
    PolarChart
  },
  data() {
    return {
      fixed: true,
      date: "",
      time: "",
      day: "",
      user: [],
      loaded: false,
      loaded1: false,
      eticketCollection: {},
      eticketCollectionWeek: {
        labels: "",
        datasets: {
          data: ""
        }
      },
      dataChart: [],
      dataChart1: [],
      installCollection: {},
      installCollectionWeek: {},
      installCollectionWeekPercent: {},
      chartOptions: {},
      yearsValue: [],
      teams: [
        {
          name: ""
        },
        {
          name: ""
        }
      ],
      selectedYear: "",
      selectedYear1: "",
      selectedChart: "Line",
      selectedChart1: "Line",
      roles: [],
      chartList: [
        {
          id: "Line",
          name: "Line"
        },
        {
          id: "Bar",
          name: "Bar"
        },
        {
          id: "Pie",
          name: "Pie"
        },
        {
          id: "Radar",
          name: "Radar"
        },
        {
          id: "Polar",
          name: "Polar"
        }
      ],

      today_tblisBusy: false,
      today_fields: [
        { key: "subid", label: "ticket/Account #" },
        {
          key: "statname",
          label: "Status",
          formatter: value => {
            if (value == "Close")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> Fixed </span>';
            else if (value == "For Rebates")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> For Rebates </span>';
            else if (value == "Pending")
              return '<span class="btn btn-warning disabled" style="width:100px; padding: 1px;"> Pending </span>';
            else if (value == "Urgent")
              return '<span class="btn btn-danger disabled" style="width:100px; padding: 1px;"> Urgent </span>';
            else if (value == "For Tech Visit")
              return '<span class="btn btn-primary disabled" style="width:100px; padding: 1px;"> For tech visit </span>';
            else if (value == "Installation")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> Installation </span>';
            else return value;
          }
        },
        {
          key: "cname",
          label: "Account Name",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        },

        {
          key: "created",
          label: "Created By",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        },
        {
          key: "updated_by",
          label: "Updated By",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        }
      ],

      week_fields: [
        { key: "subid", label: "ticket/Account #" },
        {
          key: "statname",
          label: "Status",
          formatter: value => {
            if (value == "Close")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> Fixed </span>';
            else if (value == "For Rebates")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> For Rebates </span>';
            else if (value == "Pending")
              return '<span class="btn btn-warning disabled" style="width:100px; padding: 1px;"> Pending </span>';
            else if (value == "Urgent")
              return '<span class="btn btn-danger disabled" style="width:100px; padding: 1px;"> Urgent </span>';
            else if (value == "For Tech Visit")
              return '<span class="btn btn-primary disabled" style="width:100px; padding: 1px;"> For tech visit </span>';
            else if (value == "Installation")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> Installation </span>';
            else return value;
          }
        },
        {
          key: "target_date",
          label: "Target Date",
          sortable: true
        },
        {
          key: "cname",
          label: "Account Name",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        },

        {
          key: "created",
          label: "Created By",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        }
      ],
      today_items: [],
      today_totalRows: 1,
      today_currentPage: 1,
      today_perPage: 8,
      today_totalPage: 1,

      tom_tblisBusy: false,

      tom_items: [],
      tom_totalRows: 1,
      tom_currentPage: 1,
      tom_perPage: 8,
      tom_totalPage: 1,

      week_tblisBusy: false,
      week_items: [],
      week_totalRows: 1,
      week_currentPage: 1,
      week_perPage: 8,
      week_totalPage: 1,

      team_fields: [
        {
          key: "statname",
          label: "Status",
          sortable: true,
          formatter: value => {
            if (value == "Installation")
              return '<span class="btn btn-success disabled" style="width:100px; padding: 1px;"> Installation </span>';
            else
              return '<span class="btn btn-danger disabled" style="width:100px; padding: 1px;"> Urgent </span>';
          }
        },
        {
          key: "cname",
          label: "Account Name",
          formatter: value => {
            var temp = "";
            if (value != null) {
              if (value.length > 20) temp = "...";
              return value.slice(0, 20) + temp;
            } else return "";
          }
        },
        {
          key: "stat",
          label: "Status",
          formatter: (value, key, item) => {
            if (item.statname == "Installation") {
              if (item.sched_stat != "ok") return "Pending";
              else return "Activated";
            } else {
              if (item.sched_stat != "ok") return "Pending";
              else return "Fixed";
            }
          }
        }
      ],
      teamA_prev_percent: 0,
      teamB_prev_percent: 0,
      teamA_today_percent: 0,
      teamB_today_percent: 0,
      team_perPage: 7,

      teamA_prev_items: [],
      teamA_prev_totalRows: 1,
      teamA_prev_currentPage: 1,
      teamA_prev_totalPage: 1,

      teamA_today_items: [],
      teamA_today_totalRows: 1,
      teamA_today_currentPage: 1,
      teamA_today_totalPage: 1,

      teamA_tom_items: [],
      teamA_tom_totalRows: 1,
      teamA_tom_currentPage: 1,
      teamA_tom_totalPage: 1,

      teamB_prev_items: [],
      teamB_prev_totalRows: 1,
      teamB_prev_currentPage: 1,
      teamB_prev_totalPage: 1,

      teamB_today_items: [],
      teamB_today_totalRows: 1,
      teamB_today_currentPage: 1,
      teamB_today_totalPage: 1,

      teamB_tom_items: [],
      teamB_tom_totalRows: 1,
      teamB_tom_currentPage: 1,
      teamB_tom_totalPage: 1,

      client_details: {},
      eticket: {},
      datenow: new Date(),
      dateTomorrow: new Date(),
      dateYesterday: new Date()
    };
  },
  created() {
    this.dateTomorrow.setDate(this.dateTomorrow.getDate() + 1);
    this.dateTomorrow = this.formatDate(this.dateTomorrow);
    this.dateYesterday.setDate(this.dateYesterday.getDate() - 1);
    this.dateYesterday = this.formatDate(this.dateYesterday);
    this.datenow = this.formatDate(this.datenow);

    this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.getTeam();

    this.$global.elBG(this.user.elBG);
    this.$global.elClr(this.user.elClr);
  },
  mounted() {
    this.loaded = false;
    this.loaded1 = false;
    if (!this.roles.viewer) {
      this.load();
      this.fillData();
    } else {
      //var timerID = setInterval(this.updateTime, 1000);
      var timerID1 = setInterval(this.loadActivity, 100000);
      var timerID2 = setInterval(this.animateChange, 10000);
      this.loadActivity();
      //this.updateTime();
      this.animateChange();
    }
  },
  updated() {},
  beforeDestroy() {
    this.destroy();
  },
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("navbarCalendar").style.backgroundColor =
            "#2196f3";
          document.getElementById("navbarTicket").style.backgroundColor = "";
          document.getElementById("navbarInstallation").style.backgroundColor =
            "";
          document.getElementById("navbarMap").style.backgroundColor = "";
          document.getElementById("navbarComponents").style.backgroundColor =
            "";
          document.getElementById("navbarAccounts").style.backgroundColor = "";
        }, 100);
      });
    },
    getTeam() {
      this.$http.get("api/team").then(function(response) {
        this.teams = response.body;
      });
    },
    loadActivity() {
      this.$http
        .get("api/getMonitoring/" + this.user.region_id)
        .then(function(response) {
          console.log(response.body);

          this.today_items = response.body.today;
          this.tom_items = response.body.tomorrow;
          this.week_items = response.body.week;
          this.today_totalRows = this.today_items.length;
          this.tom_totalRows = this.tom_items.length;
          this.week_totalRows = this.week_items.length;
          this.today_totalPage = this.countTotalPage(
            this.today_totalRows,
            this.today_perPage
          );
          this.tom_totalPage = this.countTotalPage(
            this.tom_totalRows,
            this.tom_perPage
          );
          this.week_totalPage = this.countTotalPage(
            this.week_totalRows,
            this.week_perPage
          );

          this.teamA_prev_items = response.body.teama_prev;
          this.teamA_today_items = response.body.teama_today;
          this.teamA_tom_items = response.body.teama_tom;

          this.teamB_prev_items = response.body.teamb_prev;
          this.teamB_today_items = response.body.teamb_today;
          this.teamB_tom_items = response.body.teamb_tom;
        });

      this.$http
        .get("api/getMonitoringSched/" + this.user.region_id)
        .then(function(response) {
          //team A
          this.teamA_prev_items = this.teamA_prev_items.concat(
            response.body.teama_prev
          );
          this.teamA_today_items = this.teamA_today_items.concat(
            response.body.teama_today
          );
          this.teamA_tom_items = this.teamA_tom_items.concat(
            response.body.teama_tom
          );
          this.teamA_prev_totalRows = this.teamA_prev_items.length;
          this.teamA_today_totalRows = this.teamA_today_items.length;
          this.teamA_tom_totalRows = this.teamA_tom_items.length;
          this.teamA_prev_totalPage = this.countTotalPage(
            this.teamA_prev_totalRows,
            this.team_perPage
          );
          this.teamA_today_totalPage = this.countTotalPage(
            this.teamA_today_totalRows,
            this.team_perPage
          );
          this.teamA_tom_totalPage = this.countTotalPage(
            this.teamA_tom_totalRows,
            this.team_perPage
          );

          var teamA_total_fix = 0;
          for (var i = 0; i < this.teamA_prev_items.length; i++) {
            if (this.teamA_prev_items[i].sched_stat == "ok") teamA_total_fix++;
          }
          this.teamA_prev_percent = Math.floor(
            (teamA_total_fix / this.teamA_prev_items.length) * 100
          );

          var teamA_total_fix_today = 0;
          for (var i = 0; i < this.teamA_today_items.length; i++) {
            if (this.teamA_today_items[i].sched_stat == "ok")
              teamA_total_fix_today++;
          }
          this.teamA_today_percent = Math.floor(
            (teamA_total_fix_today / this.teamA_today_items.length) * 100
          );

          //team B
          this.teamB_prev_items = this.teamB_prev_items.concat(
            response.body.teamb_prev
          );
          this.teamB_today_items = this.teamB_today_items.concat(
            response.body.teamb_today
          );
          this.teamB_tom_items = this.teamB_tom_items.concat(
            response.body.teamb_tom
          );
          this.teamB_prev_totalRows = this.teamB_prev_items.length;
          this.teamB_today_totalRows = this.teamB_today_items.length;
          this.teamB_tom_totalRows = this.teamB_tom_items.length;
          this.teamB_prev_totalPage = this.countTotalPage(
            this.teamB_prev_totalRows,
            this.team_perPage
          );
          this.teamB_today_totalPage = this.countTotalPage(
            this.teamB_today_totalRows,
            this.team_perPage
          );
          this.teamB_tom_totalPage = this.countTotalPage(
            this.teamB_tom_totalRows,
            this.team_perPage
          );

          var teamB_total_fix = 0;
          for (var i = 0; i < this.teamB_prev_items.length; i++) {
            if (this.teamB_prev_items[i].sched_stat == "ok") teamB_total_fix++;
          }
          this.teamB_prev_percent = Math.floor(
            (teamB_total_fix / this.teamB_prev_items.length) * 100
          );

          var teamB_total_fix_today = 0;
          for (var i = 0; i < this.teamB_today_items.length; i++) {
            if (this.teamB_today_items[i].sched_stat == "ok")
              teamB_total_fix_today++;
          }
          this.teamB_today_percent = Math.floor(
            (teamB_total_fix_today / this.teamB_today_items.length) * 100
          );

          //others
          // this.today_items = this.today_items.concat(response.body.today);
          // this.tom_items = this.tom_items.concat(response.body.tomorrow);
          // this.week_items = this.week_items.concat(response.body.week);
          // this.today_totalRows = this.today_items.length;
          // this.tom_totalRows = this.tom_items.length;
          // this.week_totalRows = this.week_items.length;
          // this.today_totalPage = this.countTotalPage(
          //   this.today_totalRows,
          //   this.today_perPage
          // );
          // this.tom_totalPage = this.countTotalPage(
          //   this.tom_totalRows,
          //   this.tom_perPage
          // );
          // this.week_totalPage = this.countTotalPage(
          //   this.week_totalRows,
          //   this.week_perPage
          // );

          this.animateChange();
          this.fillData();
        });
    },
    countTotalPage(total, perpage) {
      var temp = 1;
      while (total > perpage) {
        total -= perpage;
        temp++;
      }
      return temp;
    },
    animateChange() {
      if (this.today_currentPage < this.today_totalPage) {
        this.today_currentPage += 1;
      } else {
        this.today_currentPage = 1;
      }

      if (this.tom_currentPage < this.tom_totalPage) {
        this.tom_currentPage += 1;
      } else {
        this.tom_currentPage = 1;
      }

      if (this.week_currentPage < this.week_totalPage) {
        this.week_currentPage += 1;
      } else {
        this.week_currentPage = 1;
      }

      if (this.teamA_currentPage < this.teamB_totalPage) {
        this.teamA_currentPage += 1;
      } else {
        this.teamA_currentPage = 1;
      }

      if (this.teamB_currentPage < this.teamB_totalPage) {
        this.teamB_currentPage += 1;
      } else {
        this.teamB_currentPage = 1;
      }

      if (this.ticket_currentPage < this.ticket_totalPage) {
        this.ticket_currentPage += 1;
      } else {
        this.ticket_currentPage = 1;
      }

      if (this.inst_currentPage < this.inst_totalPage) {
        this.inst_currentPage += 1;
      } else {
        this.inst_currentPage = 1;
      }
    },
    destroy() {
      //document.getElementById("navbarDashboard").className = "";
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr";
    },
    fillData() {
      this.loaded = false;
      this.loaded1 = false;
      this.eticketCollection = {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ],
        datasets: [
          {
            label: "Fixed ticket",
            data: [1, 2, 3, 4, 3, 4, 5, 4, 3, 4, 2],

            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",

              "rgba(200, 90, 1, 1)",
              "rgba(54, 162, 1, 1)",
              "rgba(255, 206, 1, 1)",
              "rgba(65, 52, 1, 100)",
              "rgba(153, 102, 1, 1)",
              "rgba(255, 159, 1, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.eticketCollectionWeek = {
        labels: ["Jan"],
        datasets: [
          {
            label: "Fixed ticket",
            data: [0, 0, 0, 0, 0, 0, 0],

            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
              "rgba(200, 90, 1, 1)"
            ],
            borderWidth: 1
          },
          {
            label: "Reported ticket",
            data: [0, 0, 0, 0, 0, 0, 0],

            borderColor: [
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
              "rgba(200, 90, 1, 1)",
              "rgba(54, 162, 1, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.installCollection = {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ],
        datasets: [
          {
            label: "Activated",
            data: [12, 19, 3, 5, 2, 3],

            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",

              "rgba(200, 90, 1, 1)",
              "rgba(54, 162, 1, 1)",
              "rgba(255, 206, 1, 1)",
              "rgba(65, 52, 1, 100)",
              "rgba(153, 102, 1, 1)",
              "rgba(255, 159, 1, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.installCollectionWeek = {
        labels: ["Jan"],
        datasets: [
          {
            label: "Planned",
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
              "rgba(200, 90, 1, 1)"
            ],
            borderWidth: 1
          },
          {
            label: "Installed",
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: [
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
              "rgba(200, 90, 1, 1)",
              "rgba(255, 99, 132, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.installCollectionWeekPercent = {
        labels: ["Jan"],
        datasets: [
          {
            label: "Installation Efficiency",
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: [
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
              "rgba(200, 90, 1, 1)",
              "rgba(255, 99, 132, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.chartOptions = {
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              }
            }
          ]
        }
      };
      var yearNow = new Date();
      yearNow = yearNow.getFullYear();
      this.selectedYear = yearNow;
      this.selectedYear1 = yearNow;
      var temp = yearNow - 20;
      for (var x = 0; x < 41; x++) {
        var val = {
          id: temp,
          name: temp
        };
        this.yearsValue.push(val);
        temp++;
      }

      this.getEticketCollection();
      //this.getInstallCollection();
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5;
    },
    getInstallCollection() {
      this.$http
        .get(
          "api/clientDetail/installationSummary/" +
            this.selectedYear1 +
            "/" +
            this.user.region_id
        )
        .then(response => {
          console.log(response.body);

          this.dataChart1 = response.body.yearDataset;
          this.installCollection.datasets = response.body.yearDataset;
          this.installCollectionWeek.labels = response.body.weekLabel;
          this.installCollectionWeek.datasets[0].data =
            response.body.weekDataPlan;
          this.installCollectionWeek.datasets[1].data =
            response.body.weekDataDone;

          this.installCollectionWeekPercent.labels = response.body.weekLabel;
          this.installCollectionWeekPercent.datasets[0].data =
            response.body.weekPercentDone;
          //this.installCollectionWeekPercent.datasets[0].data =
          //  response.body.weekPercentPending;

          this.loaded1 = true;
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
    },
    getEticketCollection() {
      this.$http
        .get(
          "api/Ticket/eticketSummary/" +
            this.selectedYear +
            "/" +
            this.user.region_id
        )
        .then(response => {
          this.dataChart = response.body.yearDataset;
          this.eticketCollection.datasets = response.body.yearDataset;
          this.eticketCollectionWeek.labels = response.body.weekLabel;
          this.eticketCollectionWeek.datasets[0].data = response.body.weekData;
          this.eticketCollectionWeek.datasets[1].data =
            response.body.weekly_data_all;
          this.loaded = true;
          this.getInstallCollection();
        });
      // .catch(response => {
      //   swal({
      //     title: "Error",
      //     text: response.body.error,
      //     icon: "error",
      //     dangerMode: true
      //   }).then(value => {
      //     if (value) {
      //     }
      //   });
      // });
    },
    changeData() {
      this.dataChart = [1, 2, 3, 4, 3, 4, 5, 4, 3, 4, 2, 1];
    },
    onChangeYear() {
      this.loaded = false;
      this.getEticketCollection();
    },
    onChangeYear1() {
      this.loaded1 = false;
      this.getInstallCollection();
    },
    TotalActivated(arr) {
      var c = arr.length;
      var total = 0;
      for (var x = 0; x < c; x++) {
        total += arr[x];
      }
      return total;
    },

    updateTime() {
      var cd = new Date();
      var week = [
        "SUNDAY",
        "MONDAY",
        "TUESDAY",
        "WEDNESDAY",
        "THURSDAY",
        "FRIDAY",
        "SATURDAY"
      ];
      var month = [
        "JANUARY",
        "FEBRUARY",
        "MARCH",
        "APRIL",
        "MAY",
        "JUNE",
        "JULY",
        "AUGUST",
        "SEPTEMBER",
        "OCTOBER",
        "NOVEMBER",
        "DECEMBER"
      ];
      var hh = cd.getHours();
      var ii = "AM";
      if (hh > 12) {
        hh = hh - 12;
        ii = "PM";
      }
      if (hh == 12) ii = "PM";
      this.time =
        this.zeroPadding(hh, 2) +
        ":" +
        this.zeroPadding(cd.getMinutes(), 2) +
        ":" +
        this.zeroPadding(cd.getSeconds(), 2) +
        ii;

      this.day = week[cd.getDay()];
      this.date =
        month[cd.getMonth()] +
        " " +
        this.zeroPadding(cd.getDate(), 2) +
        "," +
        this.zeroPadding(cd.getFullYear(), 4);
    },

    zeroPadding(num, digit) {
      var zero = "";
      for (var i = 0; i < digit; i++) {
        zero += "0";
      }
      return (zero + num).slice(-digit);
    },
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    },
    formatDateDefualt(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("/");
    },
    dateDiffInDays(a, b) {
      const dt1 = new Date(b);
      const dt2 = new Date(a);
      return Math.floor(
        (Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) -
          Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) /
          (1000 * 60 * 60 * 24)
      );
    }
  }
};
</script>
<style scope>
.smalll {
  margin: 250px auto;
}

.textLabel {
  margin-top: 9px;
  font-size: 15px;
}
.rowFields {
  margin-top: 15px;
}

.tableDataSummary {
  width: 100%;
}

.tableDataSummary > tr > td {
  padding: 4px;
}
.elBG-white {
  background-color: white;
}
.elClr-black {
  color: black;
}
.elClr-white {
  color: white;
}

.tbl_container {
  padding: 20px;
}

#clock {
  font-family: "Share Tech Mono", monospace;
  color: #000;

  /* transform: translate(-50%, -50%); */
  color: #000;
  /* text-shadow: 0 0 20px #009, 0 0 20px rgba(10, 175, 230, 0); */
}
.time {
  letter-spacing: 0.05em;
  font-size: 60px;
  padding: 5px 0;
  margin-bottom: 0px;
  color: white;
}
.date {
  letter-spacing: 0.1em;
  font-size: 30px;
  color: white;
}
.tbl-display > tbody > tr > td {
  font-size: 14px;
}

.tbl-display {
  margin-bottom: 0px;
}

.size12 {
  font-size: 14px;
}

.tbl-display > tbody > tr > td > span > span {
  font-size: 10px;
}
.phead {
  background-color: #04b52d;
  padding: 0px;
  padding-bottom: 0px;
}
</style>
