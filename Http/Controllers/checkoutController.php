<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class checkoutController extends Controller
{
    //
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }
    public function paymentMomo(Request $request)
    {   
        $endpoint ="https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $orderInfo = "Tại đây là Thông tin đơn hàng";
        $amount = intval($request->amount); // Chuyển đổi chuỗi thành số nguyên
        $orderId = time() . mt_rand(1000, 9999); 
        $redirectUrl = "http://127.0.0.1:8000/cart/checkout?ordered";
        $ipnUrl = "http://127.0.0.1:8000/cart/checkout?ordered"; 
        $extraData = "";
        
        $partnerCode = $partnerCode;
        $accessKey = $accessKey;
        $serectkey = $secretKey;
        $orderId = $orderId;
        $orderInfo = $orderInfo;
        $amount = $amount;
        $ipnUrl = $ipnUrl;
        $redirectUrl = $redirectUrl;
        $extraData = $extraData;
        
        $requestId = time() . "";
        $requestType = "payWithATM";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $serectkey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true); 
        if (isset($jsonResult['payUrl'])) {
            return redirect()->away($jsonResult['payUrl']);
        } else {
            return redirect()->back();
        }
    }

}