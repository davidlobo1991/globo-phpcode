@extends('Web.payments.layouts.default')

@section('content')
    <div class="l-wrapper">

        <div class="l-grid__item u-2/3@m">
            <h3 class="c-title c-title--grey">YOUR ORDER</h3>
            <div class="c-cart-resume">
                {{ dump($resum) }}

                @foreach($resum->transactions as $key => $transaction)
                    <div class="c-cart-resume__box">
                        @foreach($transaction->item_list  as $item)
                            {{ dump($item) }}
                            {{--<div class="c-cart-resume__item"><span>{{ $item->name }} x {{ $item->quantity }}</span><span>{{ money_format('%i', $item->price) }}</span></div>--}}
                        @endforeach

                        <div class="c-cart-resume__total">
                            <span class="c-cart-resume__total-text u-mrv-m">Total</span>
                            <span class="c-cart-resume__total-price u-mrv-m">
                                {{--{{ money_format('%i', $transaction->related_resources[$key]['sale']['amount']['total']) }}--}}
                                {{--{{ money_format('%i', $transaction->related_resources[$key]['sale']['amount']['currency']) }}--}}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="l-grid__item u-1/1@m u-mrv-s">
            {{--//TODO return y borrar la session--}}
            <a href="//globobalear.web" class="c-cart-resume__btn c-btn c-btn--full" >Return home</a>
        </div>


    </div>

@endsection

