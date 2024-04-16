<?php

namespace App\Http\Controllers;

use App\Mail\UserActivationEmail;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Cart2;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function contacts()
    {
        return view('contacts');
    }
    public function sendMessages(Request $request)
    {
        $data = $request->validate([
            'your-name' => 'required',
            'your-email' => 'required|email',
            'your-subject' => 'required',
            'your-message' => 'required'
        ]);

        if($data){
            Mail::to('xuom.ho25@student.passerellesnumeriques.org')->send(new UserActivationEmail($data));

            return redirect('/')->with('success', 'Gửi tin nhắn thành công');
        }
        
    }

    
    public function getProductType($id)
    {
        $producttype = ProductType::find($id);
        return view('product_type', compact('producttype'));
    }
    public function getSignin(){
        return view('register');
    }
    public function postSignup(Request $req){
        $this->validate($req,
        ['email'=>'required|email|unique:users,email',
           'password'=>'required|min:6|max:20',
           'fullname'=>'required',
           'repassword'=>'required|same:password'
        ],
        ['email.required'=>'Vui lòng nhập email',
        'email.email'=>'Không đúng định dạng email',
        'email.unique'=>'Email đã có người sử  dụng',
        'password.required'=>'Vui lòng nhập mật khẩu',
        'repassword.same'=>'Mật khẩu không giống nhau',
        'password.min'=>'Mật khẩu ít nhất 6 ký tự'
       ]);
  
       $user=new User();
       $user->full_name=$req->fullname;
       $user->email=$req->email;
       $user->password=Hash::make($req->password);
       $user->phone=$req->phone;
       $user->address=$req->address;
         $user->level=2;
       $user->save();
       return redirect()->back()->with('success','Tạo tài khoản thành công');
     }
     public function signin(){
        return view('login');
    }

    public function postsignin(Request $req){
        dd($req);
        $this->validate($req,
        [
            'email'=>'required|email',
            'password'=>'required|min:6|max:20'
        ],
        [
            'email.required'=>'Vui lòng nhập email',
            'email.email'=>'Không đúng định dạng email',
            'email.unique'=>'Email đã có người sử  dụng',
            'password.required'=>'Vui lòng nhập mật khẩu',
            'password.min'=>'Mật khẩu ít nhất 6 ký tự'
        ]
        );
        $credentials=['email'=>$req->email,'password'=>$req->password];
        if(Auth::attempt($credentials)){//The attempt method will return true if authentication was successful. Otherwise, false will be returned.
            return redirect('/')->with(['flag'=>'alert','message'=>'Đăng nhập thành công']);
        }
        else{
            return redirect()->back()->with(['flag'=>'danger','message'=>'Đăng nhập không thành công']);
        }
    }
    public function getLogout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('banhang.index');
    }
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $oldCart = Session('cart') ? Session::get('cart') : null;
        if (!is_null($oldCart)) {
            $cart = $oldCart;
        } else {
            $cart = new Cart2();
        }
        $cart->add($product, $id);
        $request->session()->put('cart', $cart);
        return redirect()->back();
    }

    public function deleteCart(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            if (Session::has('cart')) {
                $oldCart = Session('cart') ? Session::get('cart') : null;
                $cart = $oldCart;
                $cart->removeItem($id);
                return redirect()->back();
                // Kiểm tra xem mục đã được xóa thành công hay không
                if ($cart->items && count($cart->items) > 0) {
                    Session::put('cart', $cart);
                    return redirect()->back();
                } else {
                    Session::forget('cart');
                    return redirect()->back()->with('error', 'Cannot delete item from your cart successfully');
                }
            } else {
                return redirect()->back()->with('error', 'No items in your cart');
            }
        } else {
            return redirect()->back()->with('error', 'No items in your cart');
        }
    }
    
}