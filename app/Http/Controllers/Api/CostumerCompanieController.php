<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CompanieCostumer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CostumerCompanieController extends Controller
{

    public function login_costumer(Request $request) {
        try {
            //code...
             $validate=Validator::make($request->all(),[
                  'email'=>'required|email',
                  'identify'=>'required',
             ]);
              if($validate->fails()){
                  return Helpers::response($validate->getMessageBag(),false);
              }else{
                  $costumercompany =CompanieCostumer::
                  where('email',$request->email)->
                  where('identify',$request->identify)->first();

                    if ($costumercompany){
                        $token= $costumercompany->createToken("costumer",['companiecostumer'])->plainTextToken;
                        return Response::json(['token'=>$token]);
                    }else{
                        return Helpers::response("error ",false);;
                    }
                 }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);

        }
  }
 //get cart off  auth costumer
        public function getauthcart () {
              try {
                  $id=Auth::user()->id;
                $carts=  Cart::where('client_id',$id)->first();
                  if ($carts) {
                      # code...
                      return Helpers::response("success",true,$carts);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);

              }


        }
}