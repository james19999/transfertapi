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
use App\Mail\TransactionPay;
use App\Mail\Transactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    //demande d'une transaction par le client connecter pour une autre persconne
    public function new_transaction(Request $request){
        try {
            $valide =Validator::make($request->all(),[
                'title'          =>'required',
                'amount'         =>'required',
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
                    'title'          =>$request->title,
                    'amount'         =>$request->amount,
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
//validation de la transaction par l'entreprise

    public function validate_transaction($cartnumber){
          try {
            $authcompany=Auth::user()->id;
            $transaction=Transaction::where('cartcode',$cartnumber)->first();

            $cart=Cart::where('code',$transaction->cartcode)->first();
             if($transaction->status ==false){

                 if($transaction->cartcode==$cart->code && $cart->amount>=$transaction->amount){
                        $cart->amount-=$transaction->amount;
                        $transaction->status =true;
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
                return Helpers::response("Opération  effectué",true);

                 }else{
                      return Helpers::response("Le solde de votre carte est insuffisant pour effectuer cette opération",false);
                 }
             }else{
                return Helpers::response("Opération déjà effectué",false);
             }
          } catch (\Throwable $th) {
              //throw $th;
          }
      }


 // get the transaction auth user

}