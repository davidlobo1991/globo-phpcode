<div class="c-cart-resume__box">
    <div class="c-cart-resume__text"><span><b>PRODUCT</b></span><span><b>TOTAL</b></span></div>
    @foreach($cart->getPayPalItems() as $i => $item)
        @if($item->getSku() == 'TAX-PAYPAL' or $item->getSku() == 'TAX-BOOKINGFEE')
            <div class="c-cart-resume__item {{ $item->getSku() }}" data-price="{{ number_format($item->price,2) }}"><span> <i>{{ $item->name }}</i> </span><span>{{ number_format($item->price,2) }} {{ \App\Models\Web\Cart::CURRENCY_SIM }}</span></div>
        @else
            <div class="c-cart-resume__item"><span>{{ $item->name }} x {{ $item->quantity }}</span><span>{{ number_format($item->price,2) }} {{ \App\Models\Web\Cart::CURRENCY_SIM }}</span></div>
        @endif
    @endforeach


    <div class="c-cart-resume__text"><span></span><span></span></div>

    <div class="c-cart-resume__item"><span><b>Subtotal</b></span><span><b>{{ number_format(($cart->getSubtotal() + $cart->getDiscount() ),2) }} {{ \App\Models\Web\Cart::CURRENCY_SIM }}</b></span></div>

    {{--<div class="c-cart-resume__item"><span><i>Taxes</i></span><span>{{ money_format('%i', $cart->getPaypalTax()) }}</span></div>--}}
    {{--<div class="c-cart-resume__item"><span><i>Booking fee</i></span><span>{{ money_format('%i', $cart->getBookinFee()) }}</span></div>--}}

    <div class="c-cart-resume__text"><span></span><span></span></div>
    <div class="c-cart-resume__item"><span><b>Promocodes</b></span><span></span></div>
    @if($cart->getPromocode())
        <div class="c-cart-resume__item">
            <span>{{ $cart->getPromocode() }}</span>
            <span>@if($cart->getPromocode()) {{ number_format($cart->getDiscount(),2) }} {{ \App\Models\Web\Cart::CURRENCY_SIM }} @endif</span>
        </div>
    @endif

    <div class="c-cart-resume__total">
        <span class="c-cart-resume__total-text u-mrv-m">Total</span>
        <span class="c-cart-resume__total-price u-mrv-m"><span id="cart-total">{{ number_format($cart->getTotal(),2) }}</span> {{ \App\Models\Web\Cart::CURRENCY_SIM }}</span>
    </div>
</div>
