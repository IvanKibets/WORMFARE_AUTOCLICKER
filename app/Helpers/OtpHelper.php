<?php
namespace App\Helpers;
use Request;
use App\Models\UserOtpHistory;
use App\Models\UserOtp;

class OtpHelper
{
    public static $result=['status'=>true,'message'=>'Success'];

    public static function check($otp)
    {
        $cek_otp = UserOtp::where(['otp'=>$otp,'status'=>0])->first();
        if($cek_otp){
            if(strtotime(date('Y-m-d H:i:s')) >= strtotime($cek_otp->expired)){
                $cek_otp->status = 1;
                $cek_otp->save();
                self::$result['status'] = false;
                self::$result['message'] = __('OTP anda sudah expired');
            }
        }else{
            self::$result['status'] = false;
            self::$result['message'] = __('OTP anda salah, silahkan di coba kembali');
        }
        
        return self::$result;
    }
}