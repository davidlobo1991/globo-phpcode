<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Cart Resum</title>
        <meta name="viewport" content="width=device-width">
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
        @include('Web.payments.assets.style-joven')
    </head>

    <body id="top">
        <section class="c-banner">
            <picture class="c-banner__media">
                <source srcset="" media="(min-width: 768px)">
                <img class="c-banner__media-content" src="https://source.unsplash.com/random/1920x1080" alt="">
            </picture>
            <div class="c-banner__wrap l-wrapper">
                <div class="c-banner__box c-banner__box--small">
                    <h1 class="c-banner__title">CART</h1>
                    <h2 class="c-banner__subtitle">BUY FROM FEELSUMMER MAGALUF WITH CONFIDENCE, ALL OUR SERVERS ARE 100% SECURE</h2>
                </div>
            </div>
        </section>
        @yield('content')
        <footer class="c-footer">
            <div class="c-copy">
                <div class="c-copy__wrap l-wrapper">
                    <div class="c-copy__text">
                        {{--<img class="u-mrb-s" src="assets/img/cards.png" alt="">--}}
                        <p class="u-mrb-none">Our servers are 100% secure so buy tickets with confidence.</p>
                    </div>
                    <a href="#top" class="c-copy__top js-animate-scroll"><span class="c-copy__top-icon fa fa-angle-up"></span><span class="c-copy__top-text">Top</span></a>
                    <div class="c-copy__text">
                        <p class="u-mrb-none">Copyright Globo Reservation 2017 | <a href="#">Privacy</a> | <a href="#">Terms & Conditions</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>