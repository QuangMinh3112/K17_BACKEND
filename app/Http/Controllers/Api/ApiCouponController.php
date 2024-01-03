<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiCouponController extends Controller
{
    //
    private $coupon;
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }
    public function getFreeCoupon()
    {
        $coupon = $this->coupon::where('end_date', '>', now())->where('status', 'like', '%public%')->get();
        if (count($coupon) > 0) {
            return response()->json(["message" => "Success", "data" => CouponResource::collection($coupon)], 200);
        } else {
            return response()->json(["message" => "No coupon"], 200);
        }
    }
    public function showCoupon($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            if ($coupon->status === "private") {
                return response()->json(["message" => "Bạn không có quyền xem mã giảm giá"], 403);
            } else {
                return response()->json(["message" => "Success", "data" => new CouponResource($coupon)], 200);
            }
        } else {
            return response()->json(["message" => "Không tìm thấy mã giảm giá"], 404);
        }
    }
    public function getCoupon($id)
    {
        $user = Auth::user();
        $coupon = Coupon::find($id);
        $user_coupon = UserCoupon::where('user_id', $user->id)->where('coupon_id', $coupon->id)->count();
        if ($coupon) {
            if ($coupon->quantity == 0) {
                return response()->json(["message" => "Mã giảm giá hết lượt dùng"], 404);
            }
            if ($coupon->end_date < now()) {
                return response()->json(["message" => "Mã giảm giá hạn"], 404);
            }
            if ($coupon->status === "private") {
                return response()->json(["message" => "Bạn không có quyền lấy mã giảm giá"], 403);
            }
            if ($coupon->point_required > $user->point) {
                return response()->json(["message" => "Bạn không đủ điểm để lấy mã giảm giá"], 404);
            }
            if ($user_coupon > 0) {
                return response()->json(["message" => "Bạn đã sở hữu mã giảm giá, không thể lấy thêm"], 200);
            } else {
                UserCoupon::create([
                    "user_id" => $user->id,
                    "coupon_id" => $coupon->id,
                ]);
                $coupon->quantity--;
                $coupon->used_count++;
                $coupon->save();
                return response()->json(["message" => "Đã lấy mã giảm giá thành công"], 200);
            }
        } else {
            return response()->json(["message" => "Không tìm thấy mã giảm giá"], 404);
        }
    }
}
