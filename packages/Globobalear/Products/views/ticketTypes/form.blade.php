<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Ticket Type Info</h4>
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

         <div class="form-group{{ $errors->has('sort') || $errors->has('sort') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('sort', NULL, ['class' => 'form-control', 'placeholder' => 'Sort', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('sort')" class="help-inline text-danger">@{{ errors.first('sort') }}</div>
        </div>

        <div class="form-group{{ $errors->has('take_place') || $errors->has('take_place') ? ' has-error' : '' }}">
            {!! Form::checkbox('take_place', 1, null, ['class' => 'iCheck']) !!}
            Take Place
        </div>

        <div class="form-group{{ $errors->has('pirates_ticket_type_id') || $errors->has('pirates_ticket_type_id') ? ' has-error' : '' }}">
            {!! Form::label('pirates_ticket_type_id', 'Pirates Related Ticket Type') !!}

            {!! Form::select('pirates_ticket_type_id', $piratesTicketTypes, null, ['class' => 'form-control']) !!}
            <div v-if="errors.has('pirates_ticket_type_id')" class="help-inline text-danger">@{{ errors.first('pirates_ticket_type_id') }}</div>
        </div>
    </div>
</div>
