<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-sitemap"></i> Seat Type Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('title', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('title')" class="help-inline text-danger">@{{ errors.first('title') }}</div>
        </div>

        <div class="form-group{{ $errors->has('acronym') || $errors->has('acronym') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('acronym', NULL, ['class' => 'form-control', 'placeholder' => 'Acronym', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
        </div>
        <div class="form-group{{ $errors->has('default_quantity') || $errors->has('default_quantity') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::number('default_quantity', NULL, ['class' => 'form-control', 'placeholder' => 'Default Quantity', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('default_quantity')" class="help-inline text-danger">@{{ errors.first('default_quantity') }}</div>
        </div>

         <div class="form-group{{ $errors->has('is_enable') || $errors->has('is_enable') ? ' has-error' : '' }}">
            {!! Form::checkbox('is_enable', 1, null, ['class' => 'iCheck']) !!}
            Enable
        </div>

         <div class="form-group{{ $errors->has('sort') || $errors->has('sort') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('sort', NULL, ['class' => 'form-control', 'placeholder' => 'Sort', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('sort')" class="help-inline text-danger">@{{ errors.first('sort') }}</div>
        </div>

        @include('partials.langTab')
        <div class="tab-content">
            @foreach($langs as $i => $lang)
                <div role="tabpanel" id="{{ $lang->iso }}" class="@if(!$i) active @endif tab-pane form-group
                {{ $errors->has('description_' . $lang->iso) || $errors->has('description_' . $lang->iso) ? ' has-error' : '' }}">
                    <float-label>
                        @if(isset($seatType))
                            {!! Form::textarea('description_' . $lang->iso, $seatType->getTranslation('description', strtolower($lang->iso)),
                            ['class' => 'form-control ckeditor', 'placeholder' => 'Description ' . $lang->iso, 'rows' => '5']) !!}
                        @else
                            {!! Form::textarea('description_' . $lang->iso, null,
                            ['class' => 'form-control ckeditor', 'placeholder' => 'Description ' . $lang->iso, 'rows' => '5']) !!}
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