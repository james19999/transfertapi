<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cart;
use App\Helper\Helpers;
use App\Models\Companies;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use App\Mail\NewOrder;
use App\Mail\TransactionPay;
use App\Mail\Transactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    //demande d'une transaction par le client connecter pour une autre persconne
    // on dois géner un code transfert qui sera scanner liée à une cart
    public function new_transaction(Request $request){
        try {
            $valide =Validator::make($request->all(),[
                'title'          =>'required',
                'amount'         =>'required|numeric',
                'code_tansaction'         =>'required',
                // 'cart_id'        =>'required',
                // 'costumer_id'    =>'required',
                // 'created'        =>'required',
            ]);

             if($valide->fails()){
               return Helpers::response($valide->getMessageBag(),false);
             }else{
                 $user=Auth::user()->id;
                 $usecartid=Cart::where('client_id',$user)->first();
                Transaction::create([
                    'title' =>$request->title,
                    // 'status'=>$request->status='peding',
                    'amount' =>$request->amount,
                    'code_tansaction' =>$request->code_tansaction,
                    'cart_id'        =>$usecartid->id,
                    'costumer_id'    =>$user,
                    'cartcode'    =>$usecartid->code,
                    'company_id'=>$usecartid->company_id,
                    'created'        =>Carbon::now(),
                ]);

               return Helpers::response("success",true);
             }
        } catch (\Throwable $th) {
            return Helpers::response($th->getMessage(),false);

        }
    }
//validation de la transaction par l'entreprise  cette fonction est à revoir un peut

    public function validate_transaction($code_tansaction){
          try {
             //entreprise connecte
            $authcompany=Auth::user()->id;
            //récuperation du code liée à la transaction

            $transaction=Transaction::where('code_tansaction',$code_tansaction)->first();
            //verifier si la transaction existe
            if($transaction){

                //carte code find
                $cart=Cart::where('code',$transaction->cartcode)->first();

                //si la transaction existe verifions son état
                if($transaction->status =="pending" && $cart->status ==true){

                    if($transaction->cartcode==$cart->code &&
                        $cart->amount>=$transaction->amount &&
                        $authcompany==$transaction->company_id){
                           $cart->amount-=$transaction->amount;
                           $transaction->status ="success";
                           $transaction->save();
                           $cart->save();

                     $costumercompany=CompanieCostumer::where('id',$transaction->costumer_id)->first();
                     $companyname=Companies::where('id',$costumercompany->company_id)->first();

                      $company_name=$companyname->name;
                       $amounts=$transaction->amount;
                       $titles=$transaction->title;
                       $restant=$cart->amount;
                       $mail=$costumercompany->email;
                       Mail::to($mail)->send(new TransactionPay($amounts,$titles,$company_name,$restant));
                   return Helpers::response("Opération  effectuée",true);

                    }else{
                         return Helpers::response("Le solde de votre carte est insuffisant pour effectuer cette opération",false);
                    }
                }else{
                   return Helpers::response("Opération déjà effectué ou la  Carte à été  bloquer",false);
                }

            }else{
                return Helpers::response("Code transaction invalide",false);

            }




          } catch (\Throwable $th) {
            return Helpers::response($th->getMessage(),false);

          }
      }

   //annuler une transaction par le costumer
    public function cancel_transaction($code_tansaction){
          try {
             //entreprise connecte
            $authcostumer=Auth::user()->id;
            //récuperation du code liée à la transaction

            $transaction=Transaction::where('code_tansaction',$code_tansaction)->first();
            //verifier si la transaction existe
            if($transaction){

                if($transaction->status =="pending"){

                    if($transaction->costumer_id==$authcostumer){
                    $transaction->status ="cancelled";
                    $transaction->save();
                   return Helpers::response("Transaction annuler",true);

                    }else{
                         return Helpers::response("Error d 'annulation",false);
                    }
                }else{
                   return Helpers::response("Opération déjà effectué",false);
                }

            }else{
                return Helpers::response("Code transaction invalide",false);

            }




          } catch (\Throwable $th) {
            return Helpers::response($th->getMessage(),false);

          }
      }



 //paiement par free pay carte
      public function payement(Request $request){
        try {
            $Cartes =Cart::where('code',$request->code)->first();

            if ($Cartes && $Cartes->status==1 && $Cartes->company_id==$request->companyid) {

                // $companyid=$Cartes->company_id;
                $Company=Companies::findOrfail($companyid);
                $companymail=$Company->email;
                  if($Cartes->amount>=$request->amount){

                      $Cartes->amount-=$request->amount;
                      $Cartes->save();
                    //   Mail::to($companymail)->send(new NewOrder($request->amount));
                      return Helpers::response("Achat bien effectué",true);
                  }else{
                      return Helpers::response("Le solde de votre carte est insuffisant",false);

                  }

            }else{
                return Helpers::response("Votre carte n'existe pas ou elle à été bloquée contactez votre entreprise",false);
            }

        } catch (\Throwable $th) {
          return Helpers::response($th->getMessage(),false);

        }
    }

}