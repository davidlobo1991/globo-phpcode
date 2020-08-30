@if ($errors->any())
    @foreach ($errors->all() as $error)
        {{ Toastr::error($error) }}
    @endforeach
@endif