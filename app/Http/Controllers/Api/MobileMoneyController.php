<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MobileMoneyController extends Controller
{
    public function TmoneyCredite(Request $request){
          try {
            //code...
            $Carts=Cart::where('code',$request->code)->first();
                 if ($Carts) {
                    # code...
                  //téléphone entrer par utilisateur
                  $phonecompanyrequest=$request->phone;

                  //montant entrer par l'utilisateur
                  $montant=$request->montant;

                  //récupération de l'entreprise
                  $company=Companies::findOrfail($Carts->company_id);
                  //téléphone de l'entreprise auteur de la carte
                   $phonecompany=$company->phone;
                  //vérifion si le numéro entrer par user correspond a celui de l entreprise
                     if($phonecompany==$phonecompanyrequest) {

                       //ici on demande le montant qu'il aimerais recharge cela correspond également celui demandé par l'api TG;
                       //Traitement Api TG pour savoir s'il veut ajouter des frais de recharge

                       //maintenant prenon le montant $montant envoyer sur le numéro de l'entreprise et le même montant pour recharge sa carte
                       // 1 api TG traitement
                         //numérodestinataieTG =  $phonecompany;
                         //montantRechargeTG=$montant;
                       // 2 Recharge carte virtuelle
                       //on dois vérifier si le traitement MobileMoney de passe bien avant de créditer la carte user;
                       return  Response::json(['status'=>true,"phone"=>"$phonecompany","montant recharge"=>"$montant"]); //il est à supprimé plus tard
                     }else {
                      return  Response::json(['status'=>false,"message"=>"Numéro de l'entreprise incorrect"]); //il est à supprimé plus tard

                     }



                  $Carts->amount=+$montant;

                 $Carts->save();

                 //Après ce traitement on envoi soit un sms ou mail a E/S et le client
                 return Response::json(['status'=>true,"message"=>"success"]);
                 }else{
                 return Response::json(['status'=>false,"message"=> 'Carte not found']);

                 }

          } catch (\Throwable $th) {
            //throw $th;
            return Response::json(['status'=>false,"message"=> "$th"]);

          }
    }
    public function FloozCredite(Request $request){
          try {
            //code...
            $Carts=Cart::where('code',$request->code)->first();
                 if ($Carts) {
                    # code...
                  //téléphone entrer par utilisateur
                  $phonecompanyrequest=$request->phone;

                  //montant entrer par l'utilisateur
                  $montant=$request->montant;

                  //récupération de l'entreprise
                  $company=Companies::findOrfail($Carts->company_id);
                  //téléphone de l'entreprise auteur de la carte
                   $phonecompany=$company->phone;
                  //vérifion si le numéro entrer par user correspond a celui de l entreprise
                     if($phonecompany==$phonecompanyrequest) {

                       //ici on demande le montant qu'il aimerais recharge cela correspond également celui demandé par l'api TG;
                       //Traitement Api TG pour savoir s'il veut ajouter des frais de recharge

                       //maintenant prenon le montant $montant envoyer sur le numéro de l'entreprise et le même montant pour recharge sa carte
                       // 1 api TG traitement
                         //numérodestinataieTG =  $phonecompany;
                         //montantRechargeTG=$montant;
                       // 2 Recharge carte virtuelle
                       //on dois vérifier si le traitement MobileMoney de passe bien avant de créditer la carte user;
                       return  Response::json(['status'=>true,"phone"=>"$phonecompany","montant recharge"=>"$montant"]); //il est à supprimé plus tard
                     }else {
                      return  Response::json(['status'=>false,"message"=>"Numéro de l'entreprise incorrect"]); //il est à supprimé plus tard

                     }



                  $Carts->amount=+$montant;

                 $Carts->save();

                 //Après ce traitement on envoi soit un sms ou mail a E/S et le client
                 return Response::json(['status'=>true,"message"=>"success"]);
                 }else{
                 return Response::json(['status'=>false,"message"=> 'Carte not found']);

                 }

          } catch (\Throwable $th) {
            //throw $th;
            return Response::json(['status'=>false,"message"=> "$th"]);

          }
    }

}
