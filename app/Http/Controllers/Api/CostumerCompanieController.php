<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Helper\Helpers;
use App\Models\Companies;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompanieCostumer;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CostumerCompanieController extends Controller
{

    public function logincostumer(Request $request) {
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
                        return Response::json(['token'=>$token,'name'=>$costumercompany->name,'status'=>true]);
                    }else{
                        return Helpers::response("error ",false);
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
                $carts=  Cart::where('client_id',$id)
                ->first();
                $companyname=Companies::findOrfail($carts->company_id);
                $costumercompany=CompanieCostumer::findOrfail($carts->client_id);
                $namecostumer=$costumercompany->name;
                $name=$companyname->name;
                $phone=$companyname->phone;
                  if ($carts) {
                      # code...
                      return  Response::json(['Cartes'=>$carts ,'NameCompanye'=>$name , 'PhoneCompanye'=>$phone ,'NameCostumer'=> $namecostumer]);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);

              }


        }

 // get transaction off the auth user today transaction
        public function getauthcarttransaction ($code) {
              try {
                  $id=Auth::user()->id;
                // $carts=  Cart::where('client_id',$id)
                // ->first();
                $Transactions=Transaction::where('costumer_id',$id)->
                 where("cartcode",$code)->orderby('created_at', 'DESC')->
                 whereDate('created',Carbon::today())
                ->get();
                  if ($Transactions) {
                      # code...
                      return  Response::json(['transaction'=>$Transactions]);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);

              }


        }

        //get all transaction by created_at
        public function getalltransaction ($code) {
              try {
                  $id=Auth::user()->id;
                // $carts=  Cart::where('client_id',$id)
                // ->first();
                $Transactions=Transaction::where('costumer_id',$id)->
                 where("cartcode",$code)->
                 orderby('created_at', 'DESC')
                ->get();
                  if ($Transactions) {
                      # code...
                      return  Response::json(['transaction'=>$Transactions]);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);
              }

        }
        //get all transaction gettransactionoffweek
        public function gettransactionoffweek ($code) {
              try {
                  $id=Auth::user()->id;
                // $carts=  Cart::where('client_id',$id)
                // ->first();
                $Transactions=Transaction::where('costumer_id',$id)->
                 where("cartcode",$code)->
                  whereBetween('created',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
                ->get();
                  if ($Transactions) {
                      # code...
                      return  Response::json(['transaction'=>$Transactions]);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);
              }

        }
        //get all transaction gettransactionoffmonth
        public function gettransactionoffmonth ($code) {
              try {
                  $id=Auth::user()->id;
                // $carts=  Cart::where('client_id',$id)
                // ->first();
                $Transactions=Transaction::where('costumer_id',$id)->
                 where("cartcode",$code)->
                  whereMonth('created',date('m'))
                ->get();
                  if ($Transactions) {
                      # code...
                      return  Response::json(['transaction'=>$Transactions]);
                  }else{
                  return Helpers::response("Cart not found",false);
                  }
              } catch (\Throwable $th) {
                  //throw $th;
                 return Helpers::response($th->getMessage(),false);
              }

        }

// logout
        public  function logoutuser(Request $request){
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

// A revoir
           public function deletecostumer ($id) {
                try {
                  $ids=Auth::user()->id;

                    $costumercompany =CompanieCostumer::where('company_id',$ids)->where('id',$id)->first();
                       if ($costumercompany) {
                        # code...
                        $costumercompany->delete();

                        return Response::json([
                            "status"=>true,
                            "message"=>"client supprimé",
                       ]);
                       } else {
                        return Response::json([
                            "status"=>false,
                            "message"=>"costumer not found",
                       ]);
                       }

                } catch (\Throwable $th) {
                    return Response::json([
                        "status"=>false,
                        "message"=>"$th",
                   ]);
                }
           }



        public function UpdateCostumerIdenfify(Request $request) {

              try {
                //code...
                $authuser=Auth::user();
                $user_id=$authuser->id;
                $user_company_id=$authuser->company_id;

               $costumercompany =CompanieCostumer::where('company_id',$user_company_id)->where('id',$user_id)->first();
                 if($costumercompany){

                  $costumercompany->update(['identify'=>$request->identify]);
                  return Response::json(['status'=>true ,'message'=>'success']);
                 }else{
                  return Response::json(['status'=>false ,'message'=>'error']);

                 }

              } catch (\Throwable $th) {
                return Response::json(['status'=>false ,'message'=>"$th"]);

              }

        }
}
