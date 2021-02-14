<?php

namespace Iateadonut\NPAPayProcessing\Http\Controllers;

use Iateadonut\NPAPayProcessing\Payment\NPA;
use Illuminate\Support\Facades\Log;
use stdClass;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Models\Customer;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Paypal\Helpers\Ipn;

class NPAController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
    }

    public function submit_to_npa() {
        //npa documentation
        //https://npa.transactiongateway.com/merchants/resources/integration/integration_portal.php
        //dd( request()->input() );
        $npa = new NPA();
        $array = request()->input();
        //dd(request()->input());
        $npa->doCollectJs($array);
        if ( NPA::APPROVED == $npa->responses['response'] ) {
            return $this->success($npa);
        } else if ( NPA::DECLINED == $npa->responses['response'] ) {
            return $this->declined($npa);
        } else if ( NPA::ERROR == $npa->responses['response'] ) {
            return $this->error( $npa );
        }
        
        //dd('test');
    }


    public function test_cart() {
        $cart = Cart::getCart();
        $shippingAddress = $cart->shippingAddress;
        $billingAddress = $cart->billingAddress;
        //dd(get_class_methods($cart));
        //dd($cart);
        echo '<pre>';
        echo "shipping address\n";
        print_r($shippingAddress);
        echo "billing address\n";
        print_r($billingAddress);
        echo '</pre>';
        exit;
        dd($shippingAddress);
        dd($billingAddress);
    }


    /**
     * Redirects .
     *
     * @return \Illuminate\View\View
     */
    public function redirect()
    {

        $cart = Cart::getCart();
        //dd($cart->billingAddress);
        //dd($cart);
        //dd($cart->shippingAddress);
        $shippingAddress = $cart->shippingAddress;
        $billingAddress = $cart->billingAddress;
        $customer = Customer::find($cart->customer_id);
        //dd(get_class_methods($customer));
        //dd($customer->company);
        //dd($billingAddress);
        $company_name = $customer->company ? $customer->company->name : '';
        return view('npa::standard-redirect')
            ->with(compact('company_name', 'customer', 'shippingAddress', 'billingAddress', 'cart'));
    }

    //A DUMMY PAGE FOR MODIFYING THE DESIGN OF THE PAYMENT PAGE
    public function design()
    {

        $cart = new stdClass();
        $cart->grand_total = '100.00';
        $cart->customer_email = 'test_email@test.com';

        $shippingAddress = new stdClass();
        $shippingAddress->first_name = 'Arthur';
        $shippingAddress->last_name = 'McFly';
        $shippingAddress->address1 = '1901 Phil St';
        $shippingAddress->address2 = 'Suite 9';
        $shippingAddress->city = 'Ft Loddy Doddy';
        $shippingAddress->state = 'FL';
        $shippingAddress->postcode = '11111';
        $shippingAddress->country = 'USA';
        $shippingAddress->phone = '909-555-9919';

        $billingAddress = new stdClass();
        $billingAddress->first_name = 'Arthur';
        $billingAddress->last_name = 'McFly';
        $billingAddress->address1 = '1901 Phil St';
        $billingAddress->address2 = 'Suite 9';
        $billingAddress->city = 'Ft Loddy Doddy';
        $billingAddress->state = 'FL';
        $billingAddress->postcode = '11111';
        $billingAddress->country = 'USA';
        $billingAddress->phone = '909-555-9919';

        $customer = new stdClass();
        $customer->first_name = 'Arthur';
        $customer->last_name = 'McFly';

        $company_name = 'Company Name, Inc';
        return view('npa::standard-redirect')
            ->with(compact('company_name', 'customer', 'shippingAddress', 'billingAddress', 'cart'));
    }



    /**
     * Cancel payment from paypal.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', 'Payment has been canceled.');

        return redirect()->route('shop.checkout.cart.index');
    }

    public function declined($npa)
    {
        Log::error( print_r($npa, 1) );
        session()->flash('error', 'Payment has been declined.');
        return redirect()->route('shop.checkout.cart.index');
    }

    public function error($npa)
    {
        Log::error( print_r($npa, 1) );
        session()->flash('error', 'Payment has been declined.');
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\Response
     */
    public function success($npa)
    {
        //dd(Cart::prepareDataForOrder());

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        $order->transaction_id = $npa->responses['transactionid'];
        $order->save();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }



}