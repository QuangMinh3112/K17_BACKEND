<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderSuccess;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class ApiMomo extends Controller
{
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function momo_payment($order_id)
    {
        $order = Order::find($order_id);
        if ($order->payment === "Paid") {
            return response()->json(['message' => 'Đơn hàng đã thanh toán'], 400);
        }
        if ($order) {
            $endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";
            $partnerCode = "MOMOBKUN20180529";
            $accessKey = "klm05TvNBzhg7h7j";
            $secretKey = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";
            $orderInfo = "Thanh toán qua MoMo";
            $amount = strval($order->total);
            // $orderId = strval($order->order_id);
            $returnUrl = "http://127.0.0.1:8000/momo-response";
            $notifyurl = "http://localhost:8000/atm/ipn_momo.php";
            $bankCode = "SML";
            $orderid = strval($order->id);
            $requestId = time() . "";
            $requestType = "payWithMoMoATM";
            $extraData = "";
            $rawHashArr =  array(
                'partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderid,
                'orderInfo' => $orderInfo,
                'bankCode' => $bankCode,
                'returnUrl' => $returnUrl,
                'notifyUrl' => $notifyurl,
                'extraData' => $extraData,
                'requestType' => $requestType
            );
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&bankCode=" . $bankCode . "&amount=" . $amount . "&orderId=" . $orderid . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
            $data =  array(
                'partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderid,
                'orderInfo' => $orderInfo,
                'returnUrl' => $returnUrl,
                'bankCode' => $bankCode,
                'notifyUrl' => $notifyurl,
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            );
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);
            $url = $jsonResult['payUrl'];

            if ($url) {
                $order->payment_url = $jsonResult['payUrl'];
                $order->save();
                return response()->json(['url' => $url], 200);
            } else {
                return response()->json(['message' => 'Error'], 404);
            }
        }
    }
    public function fallBack()
    {
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        if (!empty($_GET)) {
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            $partnerCode = request("partnerCode");
            $accessKey = request("accessKey");
            $orderId = request("orderId");
            $order = Order::find($orderId);
            $localMessage = request("localMessage");
            $message = request("message");
            $transId = request("transId");
            $orderInfo = request("orderInfo");
            $amount = request("amount");
            $errorCode = request("errorCode");
            $responseTime = request("responseTime");
            $requestId = request("requestId");
            $extraData = request("extraData");
            $payType = request("payType");
            $orderType = request("orderType");
            $extraData = request("extraData");
            $m2signature = request("signature");
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                "&payType=" . $payType . "&extraData=" . $extraData;
            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
            echo "<script>console.log('Debug huhu Objects: " . $rawHash . "' );</script>";
            echo "<script>console.log('Debug huhu Objects: " . $secretKey . "' );</script>";
            echo "<script>console.log('Debug huhu Objects: " . $partnerSignature . "' );</script>";
            if ($m2signature == $partnerSignature) {
                if ($errorCode == '0') {
                    $result = 'Success';
                    $order->update([
                        'payment' => 'Paid',
                        'payment_url' => '',
                    ]);
                    $orderDetail = OrderDetail::where('order_id', 'like', '%' . $order->id . '%')->get();
                    $user = User::find($order->user_id);
                    $trangThai = "Đã thanh toán";
                    Mail::to($order->email)->send(new OrderSuccess($order, $orderDetail, $trangThai));
                } else {
                    $result = '<div class="alert alert-danger"><strong>Payment status: </strong>' . $message . '/' . $localMessage . '</div>';
                }
            } else {
                $result = '<div class="alert alert-danger">This transaction could be hacked, please check your signature and returned signature</div>';
            }
        }

        return view('Client.Payment.momo', compact('partnerCode', 'accessKey', 'orderId', 'localMessage', 'message', 'transId', 'orderInfo', 'amount', 'errorCode', 'responseTime', 'requestId', 'extraData', 'payType', 'orderType', 'm2signature', 'result', 'secretKey', 'rawHash', 'partnerSignature', 'orderDetail', 'order'));
    }
}
