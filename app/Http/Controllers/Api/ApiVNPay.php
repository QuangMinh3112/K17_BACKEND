<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ApiVNPay extends Controller
{
    //
    public function vnpay_payment(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if ($order->payment == 'Paid') {
            return response()->json(['message' => 'Đơn hàng đã thanh toán'], 404);
        } else {
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = redirect()->route('response.vnpay');
            $vnp_TmnCode = "23R8KWXT"; //Mã website tại VNPAY
            $vnp_HashSecret = "IXUUHLENJNDRPWPAODGJXJHBMXCWOMHL"; //Chuỗi bí mật

            $vnp_TxnRef = $order->order_code;
            $vnp_OrderInfo = "Thanh toán đơn hàng";
            $vnp_OrderType = "billpayment";
            $vnp_Amount = $order->total * 100;
            $vnp_Locale = "vn";
            $vnp_BankCode = "NCB";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            if (isset($fullName) && trim($fullName) != '') {
                $name = explode(' ', $fullName);
                $vnp_Bill_FirstName = array_shift($name);
                $vnp_Bill_LastName = array_pop($name);
            }
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef
            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }
            if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                $inputData['vnp_Bill_State'] = $vnp_Bill_State;
            }
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            $returnData = array(
                'code' => '00', 'message' => 'success', 'data' => $vnp_Url,
            );
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                return response()->json(["data" => $returnData, "order" => $order]);
            }
        }
    }
    public function returnCallBack()
    {
        $vnp_HashSecret = "IXUUHLENJNDRPWPAODGJXJHBMXCWOMHL";
        $inputData = request()->except('vnp_SecureHash');
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            $hashData .= ($i == 0 ? '' : '&') . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        return view('vnpay_response', [
            'vnp_TxnRef' => request('vnp_TxnRef'),
            'vnp_Amount' => request('vnp_Amount'),
            'vnp_OrderInfo' => request('vnp_OrderInfo'),
            'vnp_ResponseCode' => request('vnp_ResponseCode'),
            'vnp_TransactionNo' => request('vnp_TransactionNo'),
            'vnp_BankCode' => request('vnp_BankCode'),
            'vnp_PayDate' => request('vnp_PayDate'),
            'secureHash' => $secureHash,
            'vnp_SecureHash' => request('vnp_SecureHash'),
        ]);
    }
}
