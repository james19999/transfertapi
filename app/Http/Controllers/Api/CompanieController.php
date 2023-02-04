<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Mail\Information;
use App\Models\Companies;
use Illuminate\Http\Request;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompanieController extends Controller
{
    //

    public function create_company(Request $request){

         try {
             //code...
             $validate=Validator::make($request->all(),[
                'name'        =>'required'      ,
                'phone'       =>'required|unique:companies,phone'      ,
                'adress'      =>'required'      ,
                'email'       =>'required|email|unique:companies,email'      ,
                'raison'      =>'required'      ,
                'domaine'     =>'required'      ,
                'password'    =>'required'      ,
                'quartier'    =>'required'      ,
              ]);
                if ($validate->fails()) {
                    return Helpers::response($validate->getMessageBag(),false,);
                }else {
                $data= Companies::create([

                'name'        =>$request->name   ,
                'phone'       =>$request->phone ,
                'adress'      =>$request->adress ,
                'email'       =>$request->email       ,
                'raison'      =>$request->raison    ,
                'domaine'     =>$request->domaine    ,
                'password'    =>Hash::make($request->password)    ,
                'quartier'    =>$request->quartier    ,
                    ]);
                return Helpers::response("sucess",true,$data);
                }
         } catch (\Throwable $th) {
              return Helpers::response($th->getMessage(),false);
         }



     }


     public function login_company(Request $request) {
           try {
               //code...
                $validate=Validator::make($request->all(),[
                     'email'=>'required|email',
                     'password'=>'required',
                ]);
                 if($validate->fails()){
                     return Helpers::response($validate->getMessageBag(),false);
                 }else{
                       if(Auth::guard('companie')->attempt(['email'=>$request->email,'password'=>$request->password])){
                           $user = Auth::guard('companie')->user();
                          $token= $user->createToken("payement",['companie'])->plainTextToken;
                          return Response::json(['token'=>$token]);
                       }
                    }
           } catch (\Throwable $th) {
               //throw $th;
              return Helpers::response($th->getMessage(),false);

           }
     }


     //create client by companies


        public function createClient (Request $request) {
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
                        $existe=CompanieCostumer::where('company_id' ,'=',$user)
                        ->where('phone',$request->phone)
                        ->where('email',$request->email)
                        ->count();
                         if($existe){
                        return Helpers::response("vous avez un client déjà avec les mêmes informations",false);

                         }else{
                             $company=Companies::findOrfail($user);

                             CompanieCostumer::create(['name'=>$request->name,
                             'ville'=>$request->ville,
                             'phone'=>$request->phone,
                             'adress'=>$request->adress,'email'=>$request->email,
                             'quartier'=>$request->quartier,
                             'company_id'=>$user,
                             'identify'=>$request->identify]);

                             Mail::to($request->email)->send(new Information($request->email,$request->identify,$company->name));
                             return Helpers::response("success",false);

                         }

                    }
                 } catch (\Throwable $th) {
                     //throw $th;
                  return Helpers::response($th->getMessage(),false);

                 }

        }


     //get client order by company auth


     public function get_client(){
           try {
               $client=CompanieCostumer::where('company_id',Auth::user()->id)->get();
               return Helpers::response("success",true,$client);
           } catch (\Throwable $th) {
            return Helpers::response($th->getMessage(),false);
           }
     }
}