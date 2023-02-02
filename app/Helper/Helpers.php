<?php
namespace App\Helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class Helpers {
 // response message
     public static function response(String $message, bool $status,$data=null ,$token=null) {
         return Response::json([
                'data'=>$data,
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

        $c='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNSTUVWXYZ';
        return substr(str_shuffle($c),0,$length);
      }
      //cart code
     public static function cart_number($length=12){


        return str_pad(mt_rand(1,99999999),$length,'0',STR_PAD_LEFT);
      }

      public static function getauth () {

        return Auth::user()->id;
      }
      public static function getauthcompanieid () {

        return Auth::user()->company_id;
      }

}