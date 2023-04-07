<?php

namespace App\Http\Controllers\Api;

use App\Mail\Compte;
use App\Helper\Helpers;
use App\Mail\Information;
use App\Models\Companies;
use Illuminate\Http\Request;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompanieController extends Controller
{
    // get company
    public function index () {
     $company =Companies:: orderby('created_at', 'DESC')->get();

     try {
         return $company;
     } catch (\Throwable $th) {
         //throw $th;
     }
    }

    //create company
    public function createcompany(Request $request){

         try {
             //code...
             $validate=Validator::make($request->all(),[
                'name'        =>'required'      ,
                'phone'       =>'required|unique:companies,phone'      ,
                'adress'      =>'required',
                'description'      =>'required',
                'email'       =>'required|email|unique:companies,email'      ,
                // 'raison'      =>'required'      ,
                // 'domaine'     =>'required'      ,
               'img' =>'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',

                'password'    =>'required'      ,
                'quartier'    =>'required'      ,
              ]);
                if ($validate->fails()) {

                    return Helpers::response($validate->getMessageBag(),false,);
                }else {
                    $file = $request->file('img');
                    $filename = $file->getClientOriginalName();
                                $dest_path = public_path('images/'.$filename);
                                Image::make($file)->save($dest_path);
                $data= Companies::create([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'adress'=>$request->adress,
                'email'=>$request->email,
                'raison'=>$request->raison,
                'description'=>$request->description,
                'quartier'=>$request->quartier,
                'img'=>$filename,
                'password'=>Hash::make($request->password),
                    ]);

                    foreach (['ahjames721@gmail.com', $request->email] as $recipient) {
                        Mail::to($recipient)->send(new Compte($request->email,$request->name));
                    }

                return Helpers::response("sucess",true,$data);
                }
         } catch (\Throwable $th) {
              return Helpers::response($th->getMessage(),false);
         }



     }
    public function upadatecompany(Request $request,$id){

         try {
             //code...
             $validate=Validator::make($request->all(),[
                'name'        =>'required',
                'phone'       =>'required',
                'adress'      =>'required',
                'description'      =>'required',
                'email'       =>'required'      ,
                // 'raison'      =>'required'      ,
                // 'domaine'     =>'required'      ,

                'password'    =>'required'      ,
                'quartier'    =>'required'      ,
              ]);
                if ($validate->fails()) {

                    return Helpers::response($validate->getMessageBag(),false,);
                }else {

                $company=Companies::where('id',Auth::user()->id)->
                                    where('id',$id)
                                    ->first();

                  if($company){
                    $company->update([
                      'name'=>$request->name,
                      'status'=>$request->status=1,
                      'phone'=>$request->phone,
                      'adress'=>$request->adress,
                      'email'=>$request->email,
                      'raison'=>$request->raison,
                      'description'=>$request->description,
                      'quartier'=>$request->quartier,
                      'password'=>$request->password,
                          ]);
                        //   if(file_exists(public_path('images/'.$filename))){
                        //     unlink(public_path('images/'.$filename));
                        //   }else{
                        //     dd('File not found');
                        //   }
                      return Helpers::response("sucess",true,$company);
                      }

                  }
         } catch (\Throwable $th) {
              return Helpers::response($th->getMessage(),false);
         }



     }
    public function upadatecompanyimage(Request $request,$id){

         try {
             //code...
             $validate=Validator::make($request->all(),[
               'img' =>'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
              ]);
                if ($validate->fails()) {

                    return Helpers::response($validate->getMessageBag(),false,);
                }else {
                    $file = $request->file('img');
                    $filename = $file->getClientOriginalName();
                                $dest_path = public_path('images/'.$filename);
                                Image::make($file)->save($dest_path);
                $company=Companies::where('id',Auth::user()->id)->
                                    where('id',$id)
                                    ->first();

                  if($company){
                    $company->update([
                      'img'=>$filename,
                          ]);
                      return Helpers::response("sucess",true,$company);
                      }

                  }
         } catch (\Throwable $th) {
              return Helpers::response($th->getMessage(),false);
         }



     }

     //login companie

     public function logincompany(Request $request) {
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
                          return Response::json(['token'=>$token,'status'=>true,'name'=>$user->name,'id'=>$user->id,"company"=>$user]);
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
                        return Helpers::response("vous avez un client déjà avec les même informations",false);

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
                             return Helpers::response("success",true);

                         }

                    }
                 } catch (\Throwable $th) {
                     //throw $th;
                  return Helpers::response($th->getMessage(),false);

                 }

        }

        //update client par les entreprises.

        public function UpdateClient (Request $request,$id) {
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
                                'identify'=>$request->identify]);

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


     //get client order by company auth


     public function getclient(){
           try {
               $client=CompanieCostumer::where('company_id',Auth::user()->id)->
                orderby('created_at','DESC')
               ->get();
               return Helpers::response("success",true,$client);
           } catch (\Throwable $th) {
            return Helpers::response($th->getMessage(),false);
           }
     }




     // logout
     public  function logoutcompany(Request $request){
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

  //changer le mot de passe d'une entreprise
     public function changePasswordCompany(Request $request) {

          try {
              //code...
              $user=Auth::user()->id;
              $existe=Companies::where('id','=',$user)->first();
               if($existe){
                $existe->password =Hash::make($request->newpassword);
                $existe->save();
               return Helpers::response("Votre mot de passe à été bien modifier",true);

               }else{
                   return Helpers::response("L'entreprise n'existe pas",false);
               }
          } catch (\Throwable $th) {
            return Helpers::response($th,false);

          }
     }


     public function ActiveCompany($id) {
        try {

            $company =Companies::findOrfail($id);

         if ($company) {

            if ($company->status==0) {
                # code...
                 $company->status=1;
                 $company->save();
            } elseif ($company->status==1){
                $company->status=0;
                $company->save();

            }
            return Helpers::response("Company active",true);

         }else{
            return Helpers::response("Company not found",false);

        }
    } catch (\Throwable $th) {
           return Helpers::response($th,false);
        //throw $th;
       }
    }
}
