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
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
   //create carte
     public function create_cart(Request $request){
           try {
               $validate=Validator::make($request->all(),[
                'amount'     =>'required',
                'client_id'  =>'required',
               ]);

                if($validate->fails()){
                    return Helpers::response($validate->getMessageBag(),false);

                }else{
                        $company_auth=Auth::user()->id;
                        $codes=Helpers::cart_number();
                        Cart::create([
                            'code'       =>$request->code=$codes,
                            'created'    =>Carbon::now(),
                            'amount'     =>$request->amount,
                            'qrcode'     =>$request->qrcode,
                            'company_id' =>$request->company_id=$company_auth,
                            'client_id'  =>$request->client_id,
                            'status'  =>$request->status=true,
                        ]);
                        $company=Companies::findOrfail($company_auth);
                        $clieemail=CompanieCostumer::findOrfail($request->client_id);
                        Mail::to($clieemail->email)->send(new InformationCart($company->name, $request->code,$request->amount));

                        return Helpers::response("success",true);

                }
            } catch (\Throwable $th) {
                //throw $th;
                return Helpers::response($th->getMessage(),false);
           }
     }

     //
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
                                     'status'  =>$request->status,
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
                $Carts=Cart::where('company_id',Auth::user()->id)->
                with('client')->orderby('created_at' ,'DESC')
                ->get();

                return Helpers::response("success",true,$Carts);

            } catch (\Throwable $th) {
                //throw $th;
                return Helpers::response($th->getMessage(),false);

            }
        }
        ///get transcation to day de la carte de du company conncté
        public function getcartwithcompanytransaction($cartcode){
            try {
                $Transaction=Transaction::where('company_id',Auth::user()->id)->
                 where('cartcode',$cartcode)->orderby('created_at', 'DESC')->where('status',1)->
                 whereDate('created',Carbon::today())
                ->get();

                return Helpers::response("success",true,$Transaction);

            } catch (\Throwable $th) {
                //throw $th;
                return Helpers::response($th->getMessage(),false);

            }
        }

//recharger une cart a partie du code de la carte
        public function addamountwithcart(Request $request, $code){

                try {
                    $Carts=Cart::where('company_id',Auth::user()->id)
                     ->where('code',$code)->first();
                     $companyid=Companies::findOrfail($Carts->company_id);

                     if($Carts && $Carts->status==true && $companyid->id==$Carts->company_id){
                         $Carts->amount+=$request->amount;
                         $clieemail=CompanieCostumer::findOrfail($Carts->client_id);
                         $cartnumer =$Carts->code;

                        Mail::to($clieemail->email)->send(new Recharge($request->amount,$Carts->amount,$cartnumer));
                         $Carts->save();

                         return Helpers::response("success",true,$Carts);

                     }else{
                return Helpers::response("Carte n'existe pas ou elle est bloquée",false);

                     }
                } catch (\Throwable $th) {
                return Helpers::response("Carte n'existe pas ou elle est bloquée",false);

                }
        }
        // a revoir après
        public function removeamountwithcart(Request $request, $id){
                try {
                    $Carts=Cart::where('company_id',Auth::user()->id)
                     ->where('id',$id)->first();
                     if($Carts && $Carts->status==true){
                           if($Carts->amount>0){
                               $Carts->amount-=$request->amount;
                               $Carts->save();
                               return Helpers::response("success",true,$Carts);
                           }else{
                            return Helpers::response("Montant sur la cart est : $Carts->amount",false);
                           }


                     }else{
                return Helpers::response("carte n' existe ou elle est bloqué",false);

                     }
                } catch (\Throwable $th) {
                return Helpers::response($th->getMessage(),false);

                }
        }

//Achat ou retrait d'argent sur la carte a partie du code de la carte pour sacnner au cas ou le client est présent dans a boutique;
        public function verifyCart (Request $request ,$cart_id){
                    try {

                        $carts =Cart::where('code',$cart_id)->where('company_id',Auth::user()->id)->first();
                         if($carts && $carts->status==true){
                             $companyid=Companies::findOrfail($carts->company_id);

                                if($companyid->id== $carts->company_id && $carts->amount>= $request->amounts){

                                 $carts->amount -=$request->amounts;

                          $clieemail=CompanieCostumer::findOrfail($carts->client_id);
                           Mail::to($clieemail->email)->send(new Retrait($request->amounts,$carts->amount,$carts->code));

                                 $carts->save();

                                 return Helpers::response("success",true,$carts);
                                }else{
                                    return Helpers::response("Le solde de votre carte est insuffisant: $carts->amount XOF ",false);
                                }
                         }else{
                               return Helpers::response("Carte n'existe pas ou elle à été est bloquer ",false);
                         }
                        //code...
                    } catch (\Throwable $th) {
                         return Helpers::response("Carte n'existe pas ou elle est bloquée",false);
                    }
          }

          //activé et désactivé une carte
        public function ActivDesactiveCart($cart_id){
                    try {
                        $carts =Cart::where('code',$cart_id)->where('company_id',Auth::user()->id)->first();
                         if($carts){
                             $companyid=Companies::findOrfail($carts->company_id);

                                if($companyid->id== $carts->company_id){
                                     if($carts->status==1){
                                          $carts->status=0;
                                          $carts->save();
                                 return Helpers::response("Carte désactivé",true,$carts);

                                     }else if($carts->status==0) {
                                        $carts->status=1;
                                        $carts->save();
                                 return Helpers::response("Carte activé",true,$carts);

                                     }

                                    }else{
                                    return Helpers::response("Vous n'est pas l'auteur de cette carte",false,$carts);

                                   }
                         }else{
                            return Helpers::response("La carte n'existe pas",false,$carts);

                         }
                        //code...
                    } catch (\Throwable $th) {
                         return Helpers::response("Carte n'existe pas ou elle est bloquée",false);
                    }
          }

}