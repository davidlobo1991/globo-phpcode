<template>
    <div>
        <div class="col-md-12" style="margin-bottom: 10px;">
            <button class="btn btn-danger" v-on:click.prevent="$emit('delete')"><i class="fa fa-times"></i></button>
        </div>
        <div class="col-md-2">
            <input type="text" name="from[]" class="date form-control" placeholder="From">
        </div>
        <div class="col-md-2">
            <input type="text" name="to[]" class="date form-control" placeholder="To">
        </div>
        <div class="col-md-2">
            <input type="text" name="hour[]" class="date form-control" placeholder="Hour">
        </div>
        <div class="col-md-2">
            <input type="text" name="hour[]" class="date form-control" placeholder="Hour">
        </div>

        <div class="col-md-12">
            <hr>
        </div>
    </div>
</template>

<script>
  export default {
    props: ['element'],
    data: function () {
      return {}
    },
    methods: {},
  }

</script>