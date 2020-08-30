@extends('Web.payments.layouts.default')

@section('content')
    <div class="l-wrapper">
        <div class="c-cart l-grid l-grid--gutter-m u-mrv-m">
            @if ($errors->any())
            <div class="l-grid__item u-3/3@m">
                <p style="color: #9f191f">Error on validate form!</p>

                @foreach ($errors->all() as $error)
                    <p style="color: #9f191f">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="l-grid__item u-2/3@m">
                <h3 class="c-title c-title--grey u-mrb-m">BILLING DETAILS</h3>

                @include('Web.payments.components.form-joven')

            </div>

            <div class="l-grid__item u-1/3@m">
                <h3 class="c-title c-title--grey">YOUR ORDER</h3>
                <div class="c-cart-resume">

                    @include('Web.payments.components.cart-resum')

                    <div class="c-cart-resume__coupon terms">
                        <div class="l-grid__item u-1/1@m">
                            <div class="checkbox u-mrv-m">
                                <input type="checkbox" name="checkbox" id="checkbox" class="checkbox__element" required="" form="form-cart">
                                <label for="checkbox" class="checkbox__label">I've read and accept the
                                    <a href=""> terms & conditions*</a>
                                </label>
                                <div class="checkbox__icon">
                                    <svg viewBox="0 0 19 13" xmlns="http://www.w3.org/2000/svg">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline class="checkbox__check" stroke="#3EB5AC" stroke-width="2" points="18.0034099 1 6 12 1 7"></polyline>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="l-grid__item u-1/1@m u-mrv-s">
                        <label class="inline-flex">
                            <input type="radio" name="payment_option" value="paypal" checked form="form-cart">
                            <img src="{{ URL::to('/') }}/img/credit/paypal2.png" alt="Pay with Paypal">
                        </label>
                    </div>

                    <div class="l-grid__item u-1/1@m u-mrv-s">
                        <label class="inline-flex">
                            <input type="radio" name="payment_option" value="card" form="form-cart">
                            <img src="{{ URL::to('/') }}/img/credit/cards.png" alt="Accepting Visa, Mastercard, Discover and American Express">
                        </label>
                    </div>

                    <div class="l-grid__item u-1/1@m u-mrv-s">
                        <label class="inline-flex">
                            <input type="radio" name="payment_option" value="tpv" form="form-cart">
                            <img src="{{ URL::to('/') }}/img/credit/caixa.png" alt="TPV">
                        </label>
                    </div>

                    <div class="l-grid__item u-1/1@m u-mrv-s">
                        {{--<div id="paypal-button-container"></div>--}}
                        <button type="submit" class="c-cart-resume__btn c-btn c-btn--full" id="card-button-container" form="form-cart">CONTINUE TO PAYMENT</button>
                    </div>


                </div>
            </div>

        </div>
    </div>

    {{--@include('Web.payments.assets.js-paypal')--}}
    @include('Web.payments.assets.js-cart-custom')

@endsection

