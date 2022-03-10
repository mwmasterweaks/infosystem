<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="elClr panel-body">
        <span id="span1">{{age}}</span>
        <br />
        <button class="btn btn-success" @click="addAge">+</button>
        <button class="btn btn-success" @click="deductAge">-</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      age: 1
    };
  },
  beforeCreate() {},
  created() {
    document.getElementById("span1");
  },
  mounted() {},
  methods: {
    addAge() {
      this.age++;
    },
    deductAge() {
      this.age--;
    }
  }
};
</script>


<style scoped>
body {
  color: red;
}
</style>
