{{ Form::open(['route' => 'create-pay', 'class' => 'l-grid l-grid--gutter-m', 'id' => 'form-cart']) }}

    <div class="l-grid__item u-2/2@s">
        <div class="c-input">
            <div class="c-input__wrap">
                <label>First Name *</label>
                <input class="c-input__element" type="text" name="name" placeholder="" value="{{ old('name') }}" required="">
            </div>
        </div>
    </div>
    <div class="l-grid__item u-2/2@s">
        <div class="c-input">
            <div class="c-input__wrap">
                <label>Last Name *</label>
                <input class="c-input__element c-input__borded" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="" required="">
            </div>
        </div>
    </div>

    <div class="l-grid__item u-2/2@s">
        <div class="c-input">
            <div class="c-input__wrap">
                <label>Email address *</label>
                <input class="c-input__element c-input__borded" type="email" name="email" value="{{ old('email') }}" placeholder="" required="">
            </div>
        </div>
    </div>

    <div class="l-grid__item u-2/2@s">
        <div class="c-input">
            <div class="c-input__wrap">
                <label>Phone *</label>
                <input class="c-input__element" type="tel" name="phone" value="{{ old('phone') }}" placeholder="" required="">
            </div>
        </div>
    </div>

    <div class="l-grid__item u-2/2@s">
        <div class="c-input">
            <div class="c-input__wrap">
                <label>Order notes</label>
                <textarea class="c-input__element c-input__borded" type="text" name="comments" placeholder="Notes about your order, e.g special notes for delivery">{{ old('comments') }}</textarea>
            </div>
        </div>
    </div>

{{ Form::close() }}
