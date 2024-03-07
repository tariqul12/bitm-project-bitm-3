<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use Cart;
use Illuminate\Http\Request;
use Session;

class CheckoutController extends Controller
{
    private $order, $customer, $orderDetail;


    public function index()
    {
        if (Session::get('customer_id'))
        {
            $this->customer = Customer::find(Session::get('customer_id'));
        }
        else
        {
            $this->customer = '';
        }
        return view('front-end.checkout.index', [
            'customer' => $this->customer
        ]);
    }



    public function newOrder(Request $request)
    {
        if (Session::get('customer_id'))
        {
            $this->customer = Customer::find(Session::get('customer_id'));
        }
        else
        {
            $this->customer = Customer::where('email', $request->email)->orWhere('mobile', $request->mobile)->first();
            if ($this->customer )
            {
                Session::put('customer_id', $this->customer->id);
                Session::put('customer_name', $this->customer->name);
            }
            else
            {
                $this->customer = Customer::newCustomer($request);

                Session::put('customer_id', $this->customer->id);
                Session::put('customer_name', $this->customer->name);

            }
        }

        Session::put('customer_id', $this->customer->id);
        Session::put('customer_name', $this->customer->name);

        $this->order = new Order();
        $this->order->customer_id       = $this->customer->id;
        $this->order->order_total       = $request->order_total;
        $this->order->tax_total         = $request->tax_total;
        $this->order->shipping_total    = $request->shipping_total;
        $this->order->order_date        = date('Y-m-d');
        $this->order->order_timestamp   = strtotime(date('Y-m-d'));
        $this->order->delivery_address  = $request->delivery_address;
        $this->order->payment_method    = $request->payment_method;
        $this->order->save();

        foreach (Cart::content() as $item)
        {
            $this->orderDetail = new OrderDetail();
            $this->orderDetail->order_id        = $this->order->id;
            $this->orderDetail->product_id      = $item->id;
            $this->orderDetail->product_name    = $item->name;
            $this->orderDetail->product_code    = $item->options->code;
            $this->orderDetail->product_price   = $item->price;
            $this->orderDetail->product_qty     = $item->qty;
            $this->orderDetail->save();

            Cart::remove($item->rowId);
        }

        return redirect('/complete-order')->with('message', 'Congratulaton.. your order info post successfully. please wait.. we contact with you soon.');
    }



    public function completeOrder()
    {
        return view('front-end.checkout.complete-order');
    }
}
