<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="reservationNumber">Reservation Number</label>
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" value="{{ request()->reservationNumber }}" name="reservationNumber" placeholder="Number">
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="email">Email</label>
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" value="{{ request()->email }}" name="email" placeholder="Email">
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="promocode">Promocode</label>
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" value="{{ request()->promocode }}" name="promocode" placeholder="Promocode">
    </div>
</div>

<div class="c-booking-search__field c-booking-search__field--dates">
    <div class="c-booking-search__field__title">
        <label for="createdAtFrom">Creation Date</label>
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" class="js-datepicker" autocomplete="off" placeholder="Created From" value="{{ request()->createdAtFrom }}" name="createdAtFrom">
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" class="js-datepicker" autocomplete="off" placeholder="Created To" value="{{ request()->createdAtTo }}" name="createdAtTo">
    </div>
</div>

<div class="c-booking-search__field c-booking-search__field--dates">
    <div class="c-booking-search__field__title">
        <label for="passFrom">Pass Date</label>
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" class="js-datepicker" placeholder="Pass Date From" value="{{ request()->passFrom }}" name="passFrom">
    </div>
    <div class="c-booking-search__field__input">
        <input type="text" class="js-datepicker" placeholder="Pass Date To" value="{{ request()->passTo }}" name="passTo">
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="productId">Product</label>
    </div>
    <div class="c-booking-search__field__input">
        <select class="js-select2" name="productId" id="productId">
            <option value="">-</option>
            @foreach ($products as $product)
                <option {{ request()->productId && request()->productId == $product->id ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="passId">Pass</label>
    </div>
    <div class="c-booking-search__field__input">
        <select class="js-select2" name="passId" id="passId">
            <option value="">-</option>
            @foreach ($passes as $pass)
                <option {{ request()->passId && request()->passId == $pass->id ? 'selected' : '' }} value="{{ $pass->id }}">{{ $pass->title }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="providerId">Provider</label>
    </div>
    <div class="c-booking-search__field__input">
        <select class="js-select2" name="providerId" id="providerId">
            <option value="">-</option>
            @foreach ($providers as $provider)
                <option {{ request()->providerId && request()->providerId == $provider->id ? 'selected' : '' }} value="{{ $provider->id }}">{{ $provider->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="c-booking-search__field">
    <div class="c-booking-search__field__title">
        <label for="channelId">Channel</label>
    </div>
    <div class="c-booking-search__field__input">
        <select class="js-select2" name="channelId" id="channelId">
            <option value="">-</option>
            @foreach ($channels as $channel)
                <option {{ request()->channelId && request()->channelId == $channel->id ? 'selected' : '' }} value="{{ $channel->id }}">{{ $channel->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('.js-datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        });

        $('.js-select2').select2();
    })
</script>
@endpush
