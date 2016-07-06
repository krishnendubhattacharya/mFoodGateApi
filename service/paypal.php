<?php
require   './paypal/vendor/autoload.php';

use PayPal\Api\Amount; 
use PayPal\Api\Details; 
use PayPal\Api\Item; 
use PayPal\Api\ItemList; 
use PayPal\Api\Payer; 
use PayPal\Api\Payment; 
use PayPal\Api\RedirectUrls; 
use PayPal\Api\Transaction;


function cart_checkout(){ 
    $rarray = array();   
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
        
    if(!empty($body->cart))
    {
       
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                PAYPAL_CLIENT_ID,     // ClientID
                PAYPAL_SECRET      // ClientSecret
            )
        );





        $payer = new Payer();
        $payer->setPaymentMethod("paypal");


        // ### Itemized information
        // (Optional) Lets you specify item wise
        // information
        $items = array();
        foreach($body->cart as $temp)
        {
            $temp = (array)$temp;
            
            $item1 = new Item();
            $item1->setName($temp['offer_title'].' : '.$temp['restaurant_title'])
                ->setCurrency('USD')
                ->setQuantity($temp['quantity'])
                ->setSku($temp['offer_id']) // Similar to `item_number` in Classic API
                ->setPrice($temp['offer_price']);
            $items[] = $item1;
        }
        /*$item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setSku("321321") // Similar to `item_number` in Classic API
            ->setPrice(2);*/

        $itemList = new ItemList();
        $itemList->setItems($items);

        // ### Additional payment details
        // Use this optional field to set additional
        // payment information such as tax, shipping
        // charges etc.
        $details = new Details();
        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($body->total);

        // ### Amount
        // Lets you specify a payment amount.
        // You can also specify additional details
        // such as shipping, tax.
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($body->total)
            ->setDetails($details);

        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it. 
        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // ### Redirect urls
        // Set the urls that the buyer must be redirected to after 
        // payment approval/ cancellation.
        $baseUrl = WEBSITEURL;
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($baseUrl."payment_return?success=true")
            ->setCancelUrl($baseUrl."payment_return?success=false");


        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent set to 'sale'
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


        // For Sample Purposes Only.
        $request = clone $payment;
        
        // ### Create Payment
        // Create a payment by calling the 'create' method
        // passing it a valid apiContext.
        // (See bootstrap.php for more on `ApiContext`)
        // The return object contains the state and the
        // url to which the buyer must be redirected to
        // for payment approval
        try {
            $payment->create($apiContext);
        }catch (PayPal\Exception\PayPalConnectionException $ex) {
    /*echo $ex->getCode(); // Prints the Error Code
    echo $ex->getData(); // Prints the detailed error message 
    die($ex);*/
            $rarray = array('type' => 'error');
            echo $rarray;
            exit;
} catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                //ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
            //echo $ex;
            $rarray = array('type' => 'error');
            echo $rarray;
            exit;
            exit(1);
        }


        // ### Get redirect url
        // The API response provides the url that you must redirect
        // the buyer to. Retrieve the url from the $payment->getApprovalLink()
        // method
        $approvalUrl = $payment->getApprovalLink();

        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
         //ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);
        //echo $payment->id;
        //print_r($payment);
        //exit;
        $order_data = array();
        $order_data['user_id'] = $body->user_id;
        $order_data['amount'] = $body->total;
        $order_data['payment_id'] = $payment->id;
        $order_data['is_paid'] = 'U';
        $order_save = add(json_encode(array('save_data' => $order_data)),'orders');
        
        if($order_save)
        {
            $order_save = (array)json_decode($order_save);
            
            foreach($body->cart as $temp)
            {
                $temp = (array)$temp;
                $temp_details = array();
                $temp_details['user_id'] = $body->user_id;
                $temp_details['offer_id'] = $temp['offer_id'];
                $temp_details['order_id'] = $order_save['id'];
                $temp_details['price'] = $temp['price'];
                $temp_details['offer_price'] = $temp['offer_price'];
                $temp_details['quantity'] = $temp['quantity'];
               
                add(json_encode(array('save_data' => $temp_details)),'order_details');
            }
        }
       
        $rarray = array('type' => 'success', 'url' => $approvalUrl);
    }
    else
    {
        $rarray = array('type' => 'error');
    }
    echo json_encode($rarray);
    exit;
}

function success_payment()
{
   
    $rarray = array();
    if(!empty($_POST))
    {
        
        $data = array();
        $data['is_paid'] = 'P';
        $payment_id = $_POST['payment_id'];
        $details = editByField(json_encode(array('save_data' => $data)),'orders',array('payment_id' => $payment_id));
        if($details)
        {
            $sql = "SELECT * FROM orders WHERE payment_id=:payment_id";
            $db = getConnection();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam("payment_id", $payment_id);
            $stmt->execute();			
            $user = $stmt->fetchObject();  
            $db = null;
            if($user)
            {
                //echo $user->id;
                $sql = "SELECT * FROM order_details WHERE order_id=:order_id";
                $db = getConnection();
                $stmt = $db->prepare($sql);  
                $stmt->bindParam("order_id", $user->id);
                $stmt->execute();			
                $order_details = $stmt->fetchAll(PDO::FETCH_OBJ); 
                $db = null;
                
                foreach($order_details as $order_detail)
                {
                        $qty = $order_detail->quantity;
                    $offer_details = json_decode(findById($order_detail->offer_id,'offers'));
                    for($i=0;$i<$qty;$i++){
                            $voucher_data = array();
                            $voucher_data['offer_id'] =$order_detail->offer_id;
                            $voucher_data['created_on'] = date('Y-m-d h:i:s');
                            $voucher_data['price'] = $offer_details->price;
                            $voucher_data['offer_price'] = $offer_details->offer_price;
                            $voucher_data['offer_percent'] = $offer_details->offer_percent;
                            $voucher_data['from_date'] = $offer_details->offer_from_date;
                            $voucher_data['to_date'] = $offer_details->offer_to_date;
                            
                            if($offer_details->start_date_type=='P')
                            {
                                $voucher_data['item_start_date'] = date('Y-m-d');
                                $voucher_data['item_expire_date'] = date('Y-m-d', strtotime("+".$offer_details->valid_days." days"));
                            }
                            else
                            {
                                $voucher_data['item_start_date'] = $offer_details->item_start_date;
                                $voucher_data['item_expire_date'] = $offer_details->item_expire_date;
                            }
                            $voucher_data['item_start_hour'] = $offer_details->item_start_hour;
                            $voucher_data['item_end_hour'] = $offer_details->item_end_hour;
                            $voucher_data['is_active'] = 1;
                            $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
					        $s = json_decode($s);
                   //print_r($s);  exit;       
                            $owner_data = array();
                            $owner_data['offer_id'] = $order_detail->offer_id;
                            $owner_data['voucher_id'] = $s->id;
                            $owner_data['from_user_id'] = 0;
                            $owner_data['to_user_id'] = $order_detail->user_id;
                            $owner_data['purchased_date'] = date('Y-m-d h:i:s');
                            $owner_data['is_active'] = 1;
                            $owner_data['price'] = $offer_details->price;
                            $owner_data['offer_price'] = $offer_details->offer_price;
                            $owner_data['offer_percent'] = $offer_details->offer_percent;
                            $owner_data['buy_price'] = $offer_details->offer_price;
                            add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
                            
                            
                    }
                    /********* Update Offer *************/
                    $up_query = "UPDATE offers SET buy_count=buy_count+".$order_detail->quantity." where id=".$order_detail->offer_id;
                    updateByQuery($up_query);
                    
                }
                
               // $orders = getlist('order_details',array('order_id' => $user->id));
              //print_r($orders);
            }
            
            //print_r($user);
            //exit;
        }
        $rarray = array('type' => 'success');
    }
    else {
        $rarray = array('type' => 'error');
    }
     echo json_encode($rarray);
    exit;
}

 
?>
