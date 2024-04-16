<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Cart2;
use App\Models\ProductType;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::all();
        $newProduct = Product::where('new', 1)->get();
        return view('homepage',["products"=>$product,"newP"=>$newProduct]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
        return view('product', ["product"=>$product]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('editCart', ['Id' => $id]);
    }

    public function getProductType($id){
        $product = Product::all()->where('id_type',$id);
        return view('product_type',['data'=>$product]);
    }

    public function updateQuantity($id,Request $newQuantity){
        $cart = session('cart'); // Lấy dữ liệu giỏ hàng từ session
        $cart->updateQuantity($id, $newQuantity->qty); // Gọi phương thức removeItem trên đối tượng giỏ hàng
        session(['cart' => $cart]); // Gán lại giỏ hàng vào session
        return redirect('/cart/checkout');
    }
    public function showCheckout(Request $request)
    {
        return view('checkout');
    }
    public function shoppingCard()
    {
        return view('shopping_cart');
    }
}