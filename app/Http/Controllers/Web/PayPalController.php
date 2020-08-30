<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;

use App\GlobalConf;
use App\Reservation;
use App\ReservationPirates;

use App\Models\Web\Cart;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Traits\HasEmail;

use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;

use PayPal\Rest\ApiContext;

use PayPal\Auth\OAuthTokenCredential;

use PayPal\Exception\PayPalConnectionException;

use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Payments\Models\PaymentMethodPirates;

use Log;
use Exception;

class PayPalController extends Controller
{
    use HasEmail;

    public function createPayment(Cart $cart)
    {
        $payment = $this->getPaypalPayment($cart);
        $apiContext = $this->getApiContext();

        try {
            $payment->create($apiContext);
        } catch (PayPalConnectionException $e) {
            Log::error('PayPal Exception: '.$e->getMessage());
            return redirect()->back()->withErrors(['error' => ['An error occurred during the reservation process. Please try again later.']]);
        }
        return redirect($payment->getApprovalLink());
    }

    /**
     * @param $cart
     * @return array
     */
    private function getPaypalPayment(Cart $cart)
    {
        $items = $cart->getPayPalItems();

        $taxes = 0;
        $subtotal = $cart->getTotal();
        $total = $subtotal;
        $paymentDiscription = "Payment description";
        $transactionDescription = "Payment description Siguiente amount";

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $itemList = new ItemList();
        $itemList->setItems($items->toArray());

        $details = new Details();
        $details->setShipping(0)
            ->setTax($taxes)
            ->setSubtotal($subtotal);

        $amount = new Amount();
        $amount->setCurrency(Cart::CURRENCY)
            ->setTotal($total)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($paymentDiscription)
            ->setInvoiceNumber($cart->getReferenceNumber());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('execute-pay'))
            ->setCancelUrl(route('error-pay'));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        return $payment;
    }

    /**
     * @return ApiContext
     */
    private function getApiContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret')
            )
        );

        $apiContext->setConfig(config('paypal.settings'));

        return $apiContext;
    }

    public function notification(Request $request)
    {
        $paymentId = $request->paymentId;
        $apiContext = $this->getApiContext();

        $payment = Payment::get($paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        $result = null;
        try {
            $result = $payment->execute($execution, $apiContext);
        } catch(PayPalConnectionException $e) {
            Log::error('PayPal Exception: ', $e);
        }

        $price = $result->transactions[0]->amount->total;

        $referenceNumber = $result->transactions[0]->invoice_number;
        $reservationsMallorca = Reservation::where('reference_number', $referenceNumber)->get();

        $reservationsPirates = ReservationPirates::where('reference_number', $referenceNumber)->get();

        // Comprobar que este finalizada
        if (!$this->checkReservations($reservationsMallorca, $reservationsPirates)) {
            Log::info("There's reservations finisheds. Reference Number: " . $referenceNumber);

            return redirect()->route('payment.url-ko');
        }

        try {
            DB::beginTransaction();
            if ($result->state === 'approved') {
                foreach ($reservationsMallorca as $reservation) {
                    $reservation->payment_methods()->attach(
                        PaymentMethod::ONLINE, [
                            'total' => $price
                        ]
                    );

                    $reservation->update(
                        [
                            'finished' => true,
                            'paypal' => (int) GlobalConf::first()->paypal ?? 0
                        ]
                    );
                }

                foreach ($reservationsMallorca as $reservation) {
                    $this->sendMailReservation($reservation);
                    $this->sendMailReservationProviders($reservation);
                }

                foreach ($reservationsPirates as $reservation) {
                    $reservation->paymentsMethods()->attach(
                        PaymentMethodPirates::ONLINE,
                        [
                            'total' => $price
                        ]
                    );

                    $reservation->update(
                        [
                            'status' => 1,
                            'paypal' => (int) GlobalConf::first()->paypal ?? 0
                        ]
                    );
                }

                foreach ($reservationsPirates as $reservation) {
                    $this->sendMailReservationPirates($reservation);
                }
            }

            DB::commit();

            return redirect()->route('payment.url-ok');
        } catch (\Exception $exception) {
            Log::info('PayPal Exception: '.$exception->getMessage());

            DB::rollBack();

            return redirect()->route('payment.url-ko');
        }
    }

    public function checkReservations($reservationsMallorca, $reservationsPirates)
    {
        if ($reservationsMallorca->count() === 0 && $reservationsPirates->count() === 0) {
            return false;
        }
        if ($reservationsMallorca->count() !== 0 && $reservationsMallorca->where('finished', 1)->count() !== 0) {
            return false;
        }
        if ($reservationsPirates->count() !== 0 && $reservationsPirates->where('status', 1)->count() !== 0) {
            return false;
        }

        return true;
    }

    public function error(Request $request)
    {
        Log::info($request->all());

        return redirect()->route('payment.url-ko');
    }
}
