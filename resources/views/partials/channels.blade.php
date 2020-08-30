<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Channels</h4>
    </div>
    <div class="panel-body">
        <div class="form-group col-md-12">

        <div class="col-md-12" >
            <div class="form-group">
                {!! Form::select('channel_id', $channels, null, ['class' => 'form-control select','v-validate' => "'required'", 'id' => 'channel_id']) !!}
            </div>
        </div>

    </div>
</div>
</div>

@push('scripts')
    <script type="text/javascript">
      $('select.select').select2({
        placeholder: 'Select a:'
      })
    </script>
@endpush