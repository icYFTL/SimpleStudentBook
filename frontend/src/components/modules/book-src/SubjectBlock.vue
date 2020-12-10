<template>
  <div class="s-block">
      <p class="s-name">{{name}}</p>
      <hr>
      <div class="s-info">
        <i href="#">{{teacher}}</i>
        <br>
        <strong>{{type}}</strong>
      </div>
    <b class="mark">{{mark}}</b>
  </div>
</template>

<script>
import SubjectPage from "@/components/modules/book-src/SubjectPage";

export default {
  name: "SubjectBlock",
  props: {
    id: Number,
    name: String,
    teacher: String,
    mark: Array,
    type: String
  },
  methods:{
    click: function (){
      this.$store.state.current_module_params = {'id': this.id, 'name': this.name};
      this.$store.commit('change_module', SubjectPage);
    }
  }

}
</script>

<style lang="scss" scoped>
  .s-block{
    width: 300px;
    height: 130px;
    background: #006b3e;
    margin: 30px;
    box-shadow: inset 0 0 6px black;
    border-radius: 4px;
  }
  .s-block hr{
    display: block;
    border-style: inset;
    border-width: 1px;
    border-color: black;
    margin-top: -3px;
  }
  .s-name {
    font-size: 1.3em;
    text-decoration: none;
    color: #84B8E3;
  }

  .s-info {
    width: 100%;
    font-size: 1.2em;
    float: left;
    color: black;
    margin-top: 5px;
  }
  .s-info i strong{
    align-self: center;
    font-size: 1em;
    color: black;
  }
  .mark{
    color: red;
  }
</style>