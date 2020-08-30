@extends('Web.payments.layouts.default')
@section('content')
<div class="l-wrapper">

    <div class="l-grid__item ">
                        <h4><i class="fa fa-user"></i> Payment Confirmed </h4>
                   
                            <h4>  <i class="fa fa-ticket"></i>
                            We can confirm we have received your payment.</br></br>Thanks</br>
                            </h4>
    </div>

                            
                            

                            
                 
                </div>
            </div>
           
        </section>

        <div class="l-grid__item u-1/1@m u-mrv-s">
            {{--//TODO return y borrar la session--}}
              <a href="//magalufessential.com" class="c-cart-resume__btn c-btn c-btn--full" >Return home</a>
          </div>

@endsection
