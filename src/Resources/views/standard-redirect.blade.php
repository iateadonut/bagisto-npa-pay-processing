
<script src="https://npa.transactiongateway.com/token/Collect.js" data-tokenization-key="{{ config('npa.public_security_key') }}" ></script>
    <h3 id='loading'>Loading Secure Payment System...</h3>
    {{-- <h4><a href="{{ route('shop.checkout.onepage.index') }}"><< back to checkout</a></h4>--}}
    <form action="{{ route('npa.submit_to_npa') }}" method="post">

                    <input type="hidden" name="firstname" value="{{ $billingAddress->first_name }}" />

                    <input type="hidden" name="lastname" value="{{ $billingAddress->last_name }}" />

                    <input type="hidden" name="company" value="{{ $company_name }}" />

                    <input type="hidden" name="address1" value="{{ $billingAddress->address1 }}">

                    <input type="hidden" name="address2" value="{{ $billingAddress->address2 }}">

                    <input type="hidden" name="city" value="{{ $billingAddress->city }}">

                    <input type="hidden" name="state" value="{{ $billingAddress->state }}">

                    <input type="hidden" name="zip" value="{{ $billingAddress->postcode }}">

                    <input type="hidden" name="country" value="{{ $billingAddress->country }}">

                    <input type="hidden" name="phone" value="{{ $billingAddress->phone }}">

                    <input type="hidden" name="shipping_firstname" value="{{ $shippingAddress->first_name }}" />

                    <input type="hidden" name="shipping_lastname" value="{{ $shippingAddress->last_name }}" />

                    <input type="hidden" name="shipping_address1" value="{{ $shippingAddress->address1 }}">

                    <input type="hidden" name="shipping_address2" value="{{ $shippingAddress->address2 }}">

                    <input type="hidden" name="shipping_city" value="{{ $shippingAddress->city }}">

                    <input type="hidden" name="shipping_state" value="{{ $shippingAddress->state }}">

                    <input type="hidden" name="shipping_zip" value="{{ $shippingAddress->postcode }}">

                    <input type="hidden" name="shipping_country" value="{{ $shippingAddress->country }}">

                    <input type="hidden" name="amount" value="{{ round($cart->grand_total,2) }}">

            <input type="hidden" name="type" value="sale">
            <input type="hidden" name="email" value="{{ $cart->customer_email }}">

        <button style='display:none' id="payButton" type="button">Submit Payment</button>
        @csrf
    </form>



<script type="text/javascript">
    window.onload = function() {
        
        console.log('running');
        document.getElementById("payButton").click();
    }
</script>

    
