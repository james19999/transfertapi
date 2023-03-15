<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class HistoryController extends Controller
{


     // get
     public function gethsitoryoffcompanytoday () {
        try {
            $id=Auth::user()->id;

          $history=History::where('company_id',$id)->
           orderby('created_at', 'DESC')->
           whereDate('created_at',Carbon::today())
          ->get();
            if ($history) {
                # code...
                return  Response::json(['history'=>$history]);
            }else{
            return Helpers::response("not data found",false);
            }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);

        }


  }

  //get all history
       public function getallhistory () {
        try {
            $id=Auth::user()->id;
          $history=History::where('company_id',$id)->
           orderby('created_at', 'DESC')
          ->get();
            if ($history) {
                # code...
                return  Response::json(['history'=>$history]);
            }else{
            return Helpers::response("not history",false);
            }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);
        }

  }

  public function gethistoryoffweek () {
        try {
            $id=Auth::user()->id;

          $history=History::where('company_id',$id)->
            whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
          ->get();
            if ($history) {
                # code...
                return  Response::json(['history'=>$history]);
            }else{
            return Helpers::response("error",false);
            }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);
        }

  }

  public function gethistoryoffmonth () {
        try {
            $id=Auth::user()->id;

          $history=History::where('company_id',$id)->
            whereMonth('created_at',date('m'))
          ->get();
            if ($history) {
                # code...
                return  Response::json(['history'=>$history]);
            }else{
            return Helpers::response("history not found",false);
            }
        } catch (\Throwable $th) {
            //throw $th;
           return Helpers::response($th->getMessage(),false);
        }

  }
}
