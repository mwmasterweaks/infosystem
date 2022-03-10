<template>
  <div>{{ datatable }}</div>
</template>




<script>
export default {
  created() {
    this.changeData();
  },
  data() {
    return {
      datatable: ""
    };
  },
  methods: {
    changeData() {
      this.datatable = "123";
    }
  }
};
</script>





<style scoped>
</style>
