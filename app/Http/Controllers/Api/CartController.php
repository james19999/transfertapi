<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cart;
use App\Mail\Retrait;
use App\Mail\Recharge;
use App\Helper\Helpers;
use App\Models\Companies;
use Illuminate\Http\Request;
use App\Mail\InformationCart;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

     public function create_cart(Request $request){
           try {
               $validate=Validator::make($request->all(),[
                'created'    =>'required',
                'amount'     =>'required',
                'client_id'  =>'required',
                // 'code'  =>'required|unique:carts,code',
               ]);

                if($validate->fails()){
                    return Helpers::response($validate->getMessageBag(),false);

                }else{
                        $company_auth=Auth::user()->id;
                        $codes=Helpers::cart_number();
                        Cart::create([
                            'code'       =>$request->code=$codes,
                            'created'    =>$request->created,
                            'amount'     =>$request->amount,
                            'qrcode'     =>$request->qrcode,
                            'company_id' =>$request->company_id=$company_auth,
                            'client_id'  =>$request->client_id,
                        ]);
                        $company=Companies::findOrfail($company_auth);
                        $clieemail=CompanieCostumer::findOrfail($request->client_id);
                        Mail::to($clieemail->email)->send(new InformationCart($company->name, $request->code,$request->amount));

                        return Helpers::response("success",false);

                }
            } catch (\Throwable $th) {
                //throw $th;
                return Helpers::response($th->getMessage(),false);
           }
     }

        public function edit_cart(Request $request ,$id){
            try {
                $validate=Validator::make($request->all(),[
                    'created'    =>'required',
                    'amount'     =>'required',
                    'client_id'  =>'required',
                    // 'code'  =>'required|unique:carts,code',
                ]);

                    if($validate->fails()){
                        return Helpers::response($validate->getMessageBag(),false);

                    }else{
                            $company_auth=Auth::user()->id;
                            $codes=Helpers::cart_number();
                            $Carts=Cart::findOrfail($id);
                             if($Carts){
                                 $Carts->update([
                                     'code'       =>$request->code=$codes,
                                     'created'    =>$request->created,
                                     'amount'     =>$request->amount,
                                     'qrcode'     =>$request->qrcode,
                                     'company_id' =>$request->company_id=$company_auth,
                                     'client_id'  =>$request->client_id,
                                 ]);
                                 return Helpers::response("success",false);

                             }else{
                               return Helpers::response("cart not found",false);

                             }

                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    return Helpers::response($th->getMessage(),false);
            }
        }


        public function getcartwithcompany(){
            try {
                $Carts=Cart::where('company_id',Auth::user()->id)->get();
                return Helpers::response("success",true,$Carts);

            } catch (\Throwable $th) {
                //throw $th;
                return Helpers::response($th->getMessage(),false);

            }
        }

//recharger une cart a partie de identifiant de la carte
        public function addamountwithcart(Request $request, $id){

                try {
                    $Carts=Cart::where('company_id',Auth::user()->id)
                     ->where('id',$id)->first();
                     if($Carts){
                         $Carts->amount+=$request->amount;
                         $clieemail=CompanieCostumer::findOrfail($Carts->client_id);
                         $cartnumer =$Carts->code;

                        Mail::to($clieemail->email)->send(new Recharge($request->amount,$Carts->amount,$cartnumer));
                         $Carts->save();

                         return Helpers::response("success",true,$Carts);

                     }else{
                return Helpers::response("cart no existe",false);

                     }
                } catch (\Throwable $th) {
                return Helpers::response($th->getMessage(),false);

                }
        }
        // a revoir après
        public function removeamountwithcart(Request $request, $id){
                try {
                    $Carts=Cart::where('company_id',Auth::user()->id)
                     ->where('id',$id)->first();
                     if($Carts){
                           if($Carts->amount>0){
                               $Carts->amount-=$request->amount;
                               $Carts->save();
                               return Helpers::response("success",true,$Carts);
                           }else{
                            return Helpers::response("Montant sur la cart est : $Carts->amount",false);
                           }


                     }else{
                return Helpers::response("cart no existe",false);

                     }
                } catch (\Throwable $th) {
                return Helpers::response($th->getMessage(),false);

                }
        }

//Achat ou retrait d'argent sur la carte a partie du code de la carte pour sacnner au cas ou le client est présent dans a boutique;
        public function verifyCart (Request $request ,$cart_id){
                    try {

                        $carts =Cart::where('code',$cart_id)->where('company_id',Auth::user()->id)->first();
                         if($carts){
                             $companyid=Companies::findOrfail($carts->company_id);

                                if($companyid->id== $carts->company_id && $carts->amount>= $request->amounts){

                                 $carts->amount -=$request->amounts;

                          $clieemail=CompanieCostumer::findOrfail($carts->client_id);
                           Mail::to($clieemail->email)->send(new Retrait($request->amounts,$carts->amount,$carts->code));

                                 $carts->save();

                                 return Helpers::response("success",true,$carts);
                                }else{
                                    return Helpers::response("Le solde de votre carte est insuffisant",false);
                                }
                         }else{
                               return Helpers::response("Carte Crédit not found",false);
                         }
                        //code...
                    } catch (\Throwable $th) {
                         return Helpers::response($th->getMessage(),false);
                    }
          }

}