<template>
  <div class="s-block">
      <a class="s-name" href="#">{{name}}</a>
      <hr>
      <div class="s-teacher">
        <p>Teacher: <a href="#">{{teacher}}</a></p>
      </div>
      <div class="last-marks">
        <div v-for="(item, idx) in marks" :key="idx">
        <Mark :mark=item></Mark>
        </div>
      </div>

  </div>
</template>

<script>
import Mark from "@/components/common/Mark";
export default {
  name: "SubjectBlock",
  components: {Mark},
  props: {
    name: String,
    teacher: String,
    marks: Array
  }
}
</script>

<style lang="scss" scoped>
  .s-block{
    width: 300px;
    height: 130px;
    background: aquamarine;
    margin: 30px;
    box-shadow: inset 0 0 6px black;
  }
  .s-block hr{
    display: block;
    border-style: inset;
    border-width: 1px;
    border-color: black;
    margin-top: -5px;
  }
  .s-name {
    font-size: 2.3em;
    text-decoration: none;
    color: cornflowerblue;
  }

  .s-teacher {
    font-size: 1.2em;
    float: left;
    color: black;
    margin-top: -8px;
  }
  .s-teacher a{
    font-size: 1em;
    text-decoration: none;
    color: black;
  }
  .last-marks{
    display: flex;
    flex-flow: row nowrap;
    float: left;
    height: 30px;
    width: inherit;
    margin-top: -15px;
  }
</style>