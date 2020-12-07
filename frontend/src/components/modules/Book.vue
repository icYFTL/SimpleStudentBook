<template>
  <div class="book">
    <div class="block-search">
      <input type="text" placeholder="Subject name or teacher name" v-model="search">
    </div>
    <div class="s-blocks">
      <div v-for="(item, idx) in items" :key="idx">
        <SubjectBlock :name=item.name :teacher=item.teacher :marks=item.marks></SubjectBlock>
      </div>
    </div>
  </div>
</template>

<script>
import SubjectBlock from "@/components/modules/book-src/SubjectBlock";
import axios from "axios";

export default {
  name: "Book",
  components: {SubjectBlock},
  data() {
    return {
      "true_items": [
        {'name': 'Math', 'teacher': 'Lol kek lolovich', 'marks': [4, 2, 2, 2, 3, 2 ,1]},
        {'name': 'Rus', 'teacher': 'Haha its teacher', 'marks': [5, 3, 5, 2]},
        {'name': 'OOP', 'teacher': 'Brab brab bras', 'marks': [1, 4, 5, 4]},
        {'name': 'Rus', 'teacher': 'Haha its teacher', 'marks': [5, 3, 5, 2]},
        {'name': 'OOP', 'teacher': 'Brab brab bras', 'marks': [1, 4, 5, 4]},
        {'name': 'Rus', 'teacher': 'Haha its teacher', 'marks': [5, 3, 5, 2]},
        {'name': 'OOP', 'teacher': 'Brab brab bras', 'marks': [1, 4, 5, 4]},

      ],
      "items": [],
      "search": ""
    }
  },
  methods: {
    getMarks: async function (){
      let _ = null;
      await axios.get(this.$store.state.api_host + '?method=getStudentMarks').then((response) => {
        _ = response.data.values;
      })

      return _
    },
    getSubjects: async function(){
      let _ = null;
      await axios.get(this.$store.state.api_host + '?method=getGroupSubjects').then((response) => {
          _ = response.data.values;
      })

      return _;
    },
    getMarksBySubject: function(marks, subject_id){
      let _ = [];
      marks.forEach(function (item) {
        if (item.subject_id === subject_id)
          _.push(item);
      });

      return _;
    }
  },
  async created() {
    let marks = await this.getMarks();
    let subjects = await this.getSubjects();
    console.log(marks)
    console.log(subjects)
    subjects.forEach(function(item) {
      this.true_items.push({
        'name': item.name,
        'teacher': 'non implemented',
        'marks': this.getMarksBySubject(marks, item.id)
      });
    });

    this.items = this.true_items;
  },
  watch: {
    search: function (value) {
      this.items = [];
      if (value === ""){
        this.$nextTick(() => this.items = this.true_items);
        return;
      }
      this.true_items.forEach((item) => {
        if (item.name.toLowerCase().startsWith(value.toLowerCase()) || item.teacher.toLowerCase().includes(value.toLowerCase())){
          this.items.push(item);
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
//Colors
$default: #fff;
$background: #171717;

.book {
  width: 100%;
  height: 100%;
  background-color: $background;
  color: $default;
}

.s-blocks {
  display: flex;
  align-items: flex-start;
  flex-flow: row wrap;
  justify-content: center;
  background-color: inherit;
}

.block-search input {
  background: rgba(23, 23, 23, .6);
  border: none;
  border-bottom: 1px solid #fff;
  color: #fff;
  font-family: 'Caveat', sans-serif;
  font-size: 20px;
  letter-spacing: 1px;
  padding: 10px 0;
  outline: none;
  width: 100%;
}
</style>