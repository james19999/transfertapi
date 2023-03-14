<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Models\Companies;
use App\Mail\ResertPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Code;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class ChangePasswordController extends Controller
{
    public function sendcode(Request $request){
          try {
            //code...
            $company =Companies::where ('email',$request->email)->first();
            if ($company) {
                $codes=Helpers::str_rancode();
                Code::create(['code'=>$codes]);
                Mail::to($request->email)->send(new ResertPassword($codes));
                 return Response::json([
                     'status'=>true,
                 ]);
            }else {
             return Response::json([
                'status'=>false,
                 'message'=>"Adresse mail n'existe pas ",
             ]);
            }
          } catch (\Throwable $th) {
            //throw $th;
          }
   }

   public function verifycode (Request $request ){

       try {
        //code...cod
        $code=Code::where('code',$request->code)->first();
           if($code){
               return Response::json(['status'=>true,'message'=>'valide code']);
                
           }else{
            return Response::json(['status'=>false ,'message'=>'code invalide']);
           }
    } catch (\Throwable $th) {
        //throw $th;
       }
   }

   public function upadatepassword(Request $request) {
     
       $company=Companies::where('email',$request->email)->first();

           if($company){
              $company->update(['password'=>Hash::make($request->password)]);
              return Response::json(['status'=>true ,'message'=>'password update']);
           }else{
            return Response::json(['status'=>false ,'message'=>'Error to update password']);

           }
   }
}
