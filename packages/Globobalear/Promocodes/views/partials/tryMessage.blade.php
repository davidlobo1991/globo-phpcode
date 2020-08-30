@if($promocode)
    <p class="text-success text-bold" style="line-height: 34px;">Promocode applied, {{ $promocode->discount }}% discount.</p>
    {!! Form::hidden('promocode_id', $promocode->id) !!}
    {!! Form::hidden('discount', $promocode->discount) !!}
@else
    <p class="text-danger text-bold" style="line-height: 34px;">Promocode not valid.</p>
@endif