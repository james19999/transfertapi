<?php
namespace App\Helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class Helpers {
 // response message
     public static function response(String $message, bool $status,$datas=null ,$token=null) {
         return Response::json([
                'datas'=>$datas,
                'message'=>$message,
                'token'=>$token,
                'status'=>$status,
         ]);
      }

     // url genarator
     public static function url_generator (String $url) {

        return url($url);
     }

    //code generator
     public static function str_rancode($length=4){

        $c='0123456789';
        return substr(str_shuffle($c),0,$length);
      }
      //cart code
     public static function cart_number($length=12){


        $c='0123456789';
        return substr(str_shuffle($c),0,$length);;
      }

      public static function getauth () {

        return Auth::user()->id;
      }
      public static function getauthcompanieid () {

        return Auth::user()->company_id;
      }

}
