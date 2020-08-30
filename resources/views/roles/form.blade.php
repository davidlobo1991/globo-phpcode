<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Roles Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('role') || $errors->has('role') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('role', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('role')" class="help-inline text-danger">@{{ errors.first('role') }}</div>
        </div>

        <div class="form-group{{ $errors->has('guard_name') || $errors->has('guard_name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('guard_name', NULL, ['class' => 'form-control', 'placeholder' => 'Description', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('guard_name')" class="help-inline text-danger">@{{ errors.first('guard_name') }}</div>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Roles Permission</h4>
    </div>
    <div class="panel-body">

     <div class="form-group{{ $errors->has('role_id') || $errors->has('role_id') ? ' has-error' : '' }}">
           {!! Form::label('role', 'Role') !!}
        
            @if (isset($roleslist))
                {!! Form::select('role_id', $roleslist, null, ['class' => 'form-control select', 'v-validate' => "'required'", 'id' => 'role_id','readonly'=> true]) !!}
            @else
                {!! Form::select('role_id', $roleslist, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'role_id']) !!}
            @endif
            <div v-if="errors.has('role_id')" class="help-inline text-danger">@{{ errors.first('role') }}</div>
        </div>




         <div class="row">
         @foreach($groups as $group)

        
                <div class="col-md-12">
                <hr>
                </div>
                @foreach($permissions->where('group', $group) as $permission)
                 
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::hidden($permission->permission, '0') !!}
                          
                            {!! Form::checkbox($permission->permission, '1', isset($roles) ? $permission->role_permission($roles) : false) !!}
                              
                            {!! Form::label($permission->permission, $permission->label) !!}
                        </div>
                        @if($permission->level)
                        <div class="col-md-6">
                            {!! Form::select('level-'.$permission->id, ['all' => 'All', 'team' => 'Team', 'self' => 'Self'], isset($roles) && $permission->role_permission($roles) ? $permission->role_permission($roles)->level : null, ['class' => 'form-control input-sm']) !!}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                
        @endforeach
        </div>

        
    </div>
</div>
