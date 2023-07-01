<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Helper\Helpers;
use App\Models\History;
use App\Mail\Information;
use App\Models\Companies;
use Illuminate\Http\Request;
use App\Mail\InformationCart;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //login colleteur company

    public  function login_user_collect(Request $request){

        try {
            //code...
             $validate=Validator::make($request->all(),[
                  'username'=>'required',
                  'password'=>'required',
             ]);
              if($validate->fails()){
                  return Helpers::response($validate->getMessageBag(),false);
              }else{
       
                $user = User::where('username', $request->username)->first();
                 if($user->status==1){
                     if (! $user || ! Hash::check($request->password, $user->password) || !$user->company_id ) {
                         throw ValidationException::withMessages([
                             'username' => ['The provided credentials are incorrect.'],
                         ]);
                     }
                  $token= $user->createToken($request->password)->plainTextToken;
                     return Response::json(['status'=>true,'token'=>$token ,'user'=>$user]);
                 }else{
                    return Response::json(['status'=>false,'message'=>"compte bloqué"]);

                 }

                 }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);

        }
     }



    public  function logout_user_person(Request $request){
        $logout=  $request->user()->currentAccessToken()->delete();

        try {
                if($logout){
                    return Response::json([
                        'status'=>true,
                        "message"=>"success"
                    ]);

                }else{
                    return Response::json([
                        'status'=>false,
                        "message"=>"errors"
                ]);
                }
        } catch (\Throwable $th) {
            return Response::json([
                'status'=>false,
                "message"=>"$th"
        ]);
        }

     }


     public function create_costumer_by_person_company(Request $request){


        try {
            //code...

            $validate=Validator::make($request->all(),[
               'name'    =>'required'  ,
               'ville'   =>'required'  ,
               'phone'   =>'required'  ,
               'adress'  =>'required'  ,
               'email'   =>'required'  ,
               'quartier'=>'required'  ,
               'identify'  =>'required|unique:companie_costumers,identify' ,
           ]);

           if($validate->fails()){
            return Helpers::response($validate->getMessageBag(),false);

           }else{

               $user=Auth::user()->id;
               $existe=CompanieCostumer::where('person_company' ,'=',$user)
               ->where('phone',$request->phone)
               ->where('email',$request->email)
               ->count();
                if($existe){
               return Helpers::response("vous avez un client déjà avec les même informations",false);

                }else{
                    $company=Companies::findOrfail($user);

                    CompanieCostumer::create(['name'=>$request->name,
                    'ville'=>$request->ville,
                    'phone'=>$request->phone,
                    'adress'=>$request->adress,'email'=>$request->email,
                    'quartier'=>$request->quartier,
                    'company_id'=>$user,
                    'identify'=>$request->identify,
                    'person_company'=>$user

                   ]);

                    Mail::to($request->email)->send(new Information($request->email,$request->identify,$company->name));
                    return Helpers::response("success",true);

                }

           }
        } catch (\Throwable $th) {
            //throw $th;
         return Helpers::response($th->getMessage(),false);

        }

     }





     public function update_costumer_by_person_company (Request $request,$id) {

        try {
            //code...

            $validate=Validator::make($request->all(),[
               'name'    =>'required'  ,
               'ville'   =>'required'  ,
               'phone'   =>'required'  ,
               'adress'  =>'required'  ,
               'email'   =>'required'  ,
               'quartier'=>'required'  ,
           ]);

           if($validate->fails()){
            return Helpers::response($validate->getMessageBag(),false);

           }else{
               $CompanieCostumer=CompanieCostumer::findOrfail($id);
               if($CompanieCostumer){
                   //  $company=Companies::findOrfail($user);
                     $user=Auth::user()->id;

                       $CompanieCostumer->update(['name'=>$request->name,
                       'ville'=>$request->ville,
                       'phone'=>$request->phone,
                       'adress'=>$request->adress,'email'=>$request->email,
                       'quartier'=>$request->quartier,
                       'company_id'=>$user,
                       'identify'=>$request->identify,
                       'person_company'=>$user
                     ]);

                       //  Mail::to($request->email)->send(new Information($request->email,$request->identify,$company->name));
                       return Helpers::response("success",true);
                }else{
                   return Helpers::response("Le client n'existe pas",false);

                }


           }
        } catch (\Throwable $th) {
            //throw $th;
         return Helpers::response($th->getMessage(),false);

        }

      }


      public function get_costumer_by_person_compay_auth(){

           try {
              $CompanieCostumer =CompanieCostumer::where('person_company',Auth::user()->id)->get();

            return Response::json(['status'=>true,'CompanieCostumer'=>$CompanieCostumer]);

           } catch (\Throwable $th) {
            //throw $th;
           }
      }




      public function createcart_by_user_person_company(Request $request){
        try {
            $validate=Validator::make($request->all(),[
             'amount'     =>'required',
             'client_id'  =>'required',
            ]);

             if($validate->fails()){
                 return Helpers::response($validate->getMessageBag(),false);

             }else{
                     $company_auth_user_person=Auth::user()->company_id;
                     $existe=Cart::where('company_id' ,'=',$company_auth_user_person)
                     ->where('client_id',$request->client_id)
                     ->count();
                      if($existe){
                      return Helpers::response("Card existe",false);
                      }else{
                      $codes=Helpers::cart_number();
                      $cart=    Cart::create([
                              'code'     =>$request->code=$codes,
                              'created'    =>Carbon::now(),
                              'amount'     =>$request->amount,
                              'qrcode'     =>$request->qrcode,
                              'company_id' =>$request->company_id=$company_auth_user_person,
                              'client_id'  =>$request->client_id,
                              'status'  =>$request->status=true,
                          ]);
                          History::create([
                               'amount'=>$cart->amount,
                               'company_id'=>$company_auth_user_person,
                               'cart_number'=>$cart->code,
                               'person_company'=>Auth::user()->id,
                          ]);
                          $company=Companies::findOrfail($company_auth_user_person);
                          $clieemail=CompanieCostumer::findOrfail($request->client_id);
                        //   Mail::to($clieemail->email)->send(new InformationCart($company->name, $request->code,$request->amount));

                          return Helpers::response("success",true);
                      }

             }
         } catch (\Throwable $th) {
             //throw $th;
             return Helpers::response($th->getMessage(),false);
        }
  }

    public function user_person_company_history (){

        $hisory =History::where('person_company',Auth::user()->id)
         ->where('company_id',Auth::user()->company_id)

        ->sum('amount');

        return $hisory;
    }
}
