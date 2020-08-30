<template>
    <div class="col-md-12" id="generatePasses">
        <div class="row">
            <div class="col-md-2">
                <button class="btn btn-block btn-primary" v-on:click.prevent="newLine()">Add Dates</button></div>
        </div>

        <hr>

        <GenerateLine v-for="q in qty" :key="q" v-bind="q" @delete="deleteLine(q)"></GenerateLine>
    </div>
</template>

<script>

    import GenerateLine from './GenerateLine.vue';

    export default {
      components: { GenerateLine },
      data: function () {
         return {
           actual: 1,
           qty: [1]
         }
      },
      methods: {
        newLine() {
            this.actual++;
            this.qty.push(this.actual);
        },
        deleteLine(q) {
          let index = this.qty.indexOf(q);
          this.qty.splice(index, 1);
        }
      },
    }

</script>