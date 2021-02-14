<?php

namespace Iateadonut\NPAPayProcessing\Tests;

use Iateadonut\NPAPayProcessing\Http\Controllers\NPAController;
use Iateadonut\NPAPayProcessing\Payment\NPA;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Checkout\Facades\Cart;
use Webkul\Inventory\Repositories\InventorySourceRepository;

class indexTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testConstruct()
    {
        $gw = new NPA();
        //$npa->doAuth(1, '4111111111111111', '10/25', 999);

        $gw->setLogin("6457Thfj624V5r7WUwc5v6a68Zsd6YEm"); //GOT FROM TEST DOCUMENTATION
        //https://npa.transactiongateway.com/merchants/resources/integration/integration_portal.php#dp_php
        $gw->setBilling("John","Smith","Acme, Inc.","123 Main St","Suite 200", "Beverly Hills",
                "CA","90210","US","555-555-5555","555-555-5556","support@example.com",
                "www.example.com");
        $gw->setShipping("Mary","Smith","na","124 Shipping Main St","Suite Ship", "Beverly Hills",
                "CA","90210","US","support@example.com");
        $gw->setOrder("1234","Big Order",1, 2, "PO1234","65.192.14.10");

        $r = $gw->doSale("50.00","4111111111111111","1010");

        $this->assertEquals("1", $gw->responses["response"]);
        //dd($gw);
        //print $gw->responses['responsetext'];
    }

    public function testCollectJs()
    {

        $npa = new NPA();
        $npa->setLogin("6457Thfj624V5r7WUwc5v6a68Zsd6YEm");
        $array = [
            'type'=>'sale',
            'amount'=>'40.00',
            'payment_token'=> '00000000-000000-000000-000000000000',
        ];
        $npa->doCollectJs($array);
        $this->assertEquals( NPA::APPROVED , $npa->responses['response'] );
        var_export ($npa);
        dd($npa);

    }

    public function testSuccess() {

        //$ar = new AttributeFamilyRepository();
        $ar = app('\Webkul\Attribute\Repositories\AttributeFamilyRepository');
        if ( ! $attribute_family = $ar->find(1) ) {
            $attribute_family = factory( \Webkul\Attribute\Models\AttributeFamily ::class)->states('default')->create();
        }

        //InventorySourceRepository
        $isr = app('\Webkul\Inventory\Repositories\InventorySourceRepository');
        if( ! $inventory_source = $isr->find(1) ) {
            $inventory_source = factory( \Webkul\Inventory\Models\InventorySource::class )->states('default')->create();
        }
        
        DB::insert( 'insert ignore into channel_inventory_sources ( channel_id, inventory_source_id ) values (1,1)' );

        $pr = app( 'Webkul\Product\Repositories\ProductRepository' );
        $product = $pr->create([
            'sku' => md5(md5(rand())),
            'type' => 'simple',
            'attribute_family_id' => $attribute_family->id,
        ]);
        //YOU HAVE TO CREATE THE PRICE ATTRIBUTE IN THE ATTRIBUTE FAMILY
        //dd(get_class_methods($attribute_family));
        $product->setAttribute('price', '1.00');
        //dd(get_class_methods($product));
        DB::insert( 'insert ignore into product_inventories (qty, product_id, inventory_source_id, vendor_id) select 10, id as product_id, 1, 0 from products;' );

        $npa = (object)(array(
            'code' => 'npa',
            'order' => NULL,
            'billing' => NULL,
            'login' => 
           array (
             'security_key' => 'test',
           ),
            'shipping' => NULL,
            'cart' => NULL,
            'responses' => 
           array (
             'response' => '1',
             'responsetext' => 'SUCCESS',
             'authcode' => '123456',
             'transactionid' => '5899779206',
             'avsresponse' => '',
             'cvvresponse' => 'M',
             'orderid' => '',
             'type' => 'sale',
             'response_code' => '100',
             'cc_number' => '4xxxxxxxxxxx1111',
             'customer_vault_id' => '',
             'checkaba' => '',
             'checkaccount' => '',
           ),
        ));

        $npa = new NPA();

        $cart = Cart::create([]);

        Cart::addProduct($product->id, [
            //'product_id' => $product->id,
            //'quantity'   => 1,
            //'price' => '1.00',
        ]);

        $result = Cart::addProduct($product->id, [
            //'_token'     => session('_token'),
            'product_id' => $product->id,
            'quantity'   => 1,
            'price' => '1.00',
        ]);

        dd($cart);

        $controller = app( NPAController::class );
        $controller->success($npa);

    }
    
}
