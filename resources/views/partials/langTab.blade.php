<ul class="nav nav-tabs" role="tablist">
    @foreach($langs as $i => $lang)
        <li role="presentation" @if(!$i) class="active" @endif>
            <a href="#{{ $lang->iso }}" aria-controls="{{ $lang->iso }}" role="tab" data-toggle="tab">
                {{ strtoupper($lang->iso) }}
            </a>
        </li>
    @endforeach
</ul>