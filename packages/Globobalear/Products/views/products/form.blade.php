<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Product Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('acronym') || $errors->has('acronym') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('acronym', NULL, ['class' => 'form-control', 'placeholder' => 'Acronym', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
        </div>

        <div class="form-group{{ $errors->has('category_id') || $errors->has('category_id') ? ' has-error' : '' }}">
            <label>Category*</label>
            <float-label>
                {!! Form::select('category_id', $categories, NULL, ['class' => 'form-control', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('category_id')" class="help-inline text-danger">@{{ errors.first('category_id') }}</div>
        </div>

        <div class="form-group{{ $errors->has('sort') || $errors->has('sort') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('sort', NULL, ['class' => 'form-control', 'placeholder' => 'Sort', 'v-validate' => "'required'", ]) !!}
            </float-label>
            <div v-if="errors.has('sort')" class="help-inline text-danger">@{{ errors.first('sort') }}</div>
        </div>

        <div class="form-group{{ $errors->has('commission') || $errors->has('commission') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::number('commission', NULL, ['class' => 'form-control', 'placeholder' => 'Commission*', 'v-validate' => "'required'", 'step' => '0.001']) !!}
            </float-label>
            <div v-if="errors.has('commission')" class="help-inline text-danger">@{{ errors.first('commission') }}</div>
        </div>

        <div class="form-group">
            <label>
                {!! Form::checkbox('has_passes', 1, null, ['class' => 'iCheck']) !!}
                Has passes
            </label>
            &nbsp;-&nbsp;
            <label>
                {!! Form::checkbox('has_quota',  1, null, ['class' => 'iCheck']) !!}
                Has quota
            </label>
        </div>
        <div class="form-group">
            <float-label>
                {!! Form::number('limit_days', NULL, ['class' => 'form-control', 'placeholder' => 'Limit Days', 'step' => '1']) !!}
            </float-label>
            <div v-if="errors.has('limit_days')" class="help-inline text-danger">@{{ errors.first('limit_days') }}</div>
        </div>
        <div class="form-group">
            <float-label>
                {!! Form::number('limit_hours', NULL, ['class' => 'form-control', 'placeholder' => 'Limit Hours', 'step' => '1']) !!}
            </float-label>
            <div v-if="errors.has('limit_hours')" class="help-inline text-danger">@{{ errors.first('limit_hours') }}</div>
        </div>

        <div class="form-group{{ $errors->has('image') || $errors->has('image') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::file('image', ['placeholder' => 'Image', 'id' => 'image']) !!}
            </float-label>
            <div v-if="errors.has('image')" class="help-inline text-danger">@{{ errors.first('image') }}</div>
            @if(isset($product))
                <img style='max-width: 25%; margin-top:15px' id="preview" src='{{ asset('files/' . $product->image)}}'
                     alt=''/>
            @else
                <img style='max-width: 25%; margin-top:15px' id="preview" src='' alt=''/>
            @endif
        </div>

        @include('partials.langTab')
        <div class="tab-content">
            @foreach($langs as $i => $lang)
                <div role="tabpanel" id="{{ $lang->iso }}" class="@if(!$i) active @endif tab-pane form-group
                {{ $errors->has('description_' . $lang->iso) || $errors->has('description_' . $lang->iso) ? ' has-error' : '' }}">
                    <float-label>
                        @if(isset($product))
                            {!! Form::textarea('description_' . $lang->iso, $product->getTranslation('description', strtolower($lang->iso)),
                            ['class' => 'form-control ckeditor', 'placeholder' => 'Description ' . $lang->iso, 'rows' => '3']) !!}
                        @else
                            {!! Form::textarea('description_' . $lang->iso, null,
                            ['class' => 'form-control ckeditor', 'placeholder' => 'Description ' . $lang->iso, 'rows' => '3']) !!}
                        @endif
                    </float-label>
                    <div v-if="errors.has('description_{{ $lang->iso }}')"
                         class="help-inline text-danger">@{{ errors.first('acronym') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-sitemap"></i> Seats
            <small style="color: white;">(Select available seats for this show)</small>
        </h4>
    </div>


    <div class="panel-body">

    <div class="form-group col-md-12">
    <a href="{{ route('seatTypes.create') }}" class="btn btn-warning"><i
            class="glyphicon glyphicon-create"></i> {{ trans('common.create') }}</a>
    </div>

        @foreach($seatTypes as $seatType)
            <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-6">
                @if(isset($product))
                    {!! Form::checkbox('seats[]', $seatType->id, !$product->seatTypes->where('id', $seatType->id)->isEmpty(), ['class' => 'iCheck', 'placeholder' => 'Seat Type']) !!}
                @else
                    {!! Form::checkbox('seats[]', $seatType->id, true, ['class' => 'iCheck', 'placeholder' => 'Seat Type']) !!}
                @endif
                {{ $seatType->title }}
            </div>
        @endforeach
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Tickets
            <small style="color: white;">(Select available tickets for this show)</small>
        </h4>
    </div>
    <div class="panel-body">

        @foreach($ticketTypes as $ticketType)
            <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-4">
                @if(isset($product))
                    {!! Form::checkbox('tickets[]', $ticketType->id, !$product->ticketTypes->where('id', $ticketType->id)->isEmpty(), ['class' => 'iCheck', 'placeholder' => 'Ticket Type']) !!}
                @else
                    {!! Form::checkbox('tickets[]', $ticketType->id, true, ['class' => 'iCheck', 'placeholder' => 'Ticket Type']) !!}
                @endif
                {{ $ticketType->title }}
            </div>
        @endforeach
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Provider
            <small style="color: white;">(Select one of the available providers for this show)</small>
        </h4>
    </div>
    <div class="panel-body">
        <div class="form-group{{ $errors->has('provider_id') || $errors->has('provider_id') ? ' has-error' : '' }} col-md-4">
            {!! Form::select('provider_id', $providers, null, ['class' => 'form-control', 'placeholder' => 'Providers']) !!}
        </div>
    </div>
</div>

@push('scripts')
    <script>
      function readURL (input) {
        if (input.files && input.files[0]) {
          let reader = new FileReader()

          reader.onload = function (e) {
            $('#preview').attr('src', e.target.result)
          }

          reader.readAsDataURL(input.files[0])
        }
      }

      $('#image').change(function () {
        readURL(this)
      })
    </script>
@endpush
