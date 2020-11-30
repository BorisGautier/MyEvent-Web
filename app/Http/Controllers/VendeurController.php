<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Event;
use App\Models\Vendeur;
use Illuminate\Http\Request;

class VendeurController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($codeEvent)
    {
    }

    public function getVendeur(Request $request)
    {

        $codeEvent = $request->codeEvent;

        $vendeurs = Vendeur::where("codeEvent", $codeEvent)->get();

        $success["vendeur"] = $vendeurs;

        return $this->sendResponse($success, 'Liste des Vendeurs');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vendeur = Vendeur::create($request->all());

        $event = Event::where("codeEvent", $request->codeEvent)->first();

        if ($vendeur) {
            $oMessage = \Camoo\Sms\Message::create('5cdef6c7d7b7e', 'f2e4dfcdd20689cb762a352b9c1038ca275fce8cee2a638bce5ad567502f3f45');
            $oMessage->from = 'MyEvent';
            $oMessage->to = "+237" . $request->phone;
            $oMessage->message = "Votre Code de connexion vendeur pour l\'évènement " . $event->nomEvent . " est: " . $request->codeVendeur . " \n Gardez le precieusement; Nous vous remercions";

            $j = json_encode($oMessage->send());

            $json = json_decode($j, true);

            $msg = $json['_message'];

            $success["vendeur"] = $vendeur;

            return $this->sendResponse($success, $msg);
        } else {
            return $this->sendError("Erreur de création du vendeur", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendeur  $vendeur
     * @return \Illuminate\Http\Response
     */
    public function show(Vendeur $vendeur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendeur  $vendeur
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendeur $vendeur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendeur  $vendeur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendeur $vendeur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendeur  $vendeur
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
    }

    public function deleteVendeur(Request $request)
    {
        $codeVendeur = $request->codeVendeur;
        $vendeur = Vendeur::where("codeVendeur", $codeVendeur)->first();

        $vendeurDelete = Vendeur::destroy($vendeur->id);

        if ($vendeurDelete) {
            return $this->sendResponse("", "Suppression réussie");
        } else {
            return $this->sendError("Echec de Suppression", ['error' => 'Unauthorised']);
        }
    }

    public function loginVendeur(Request $request)
    {
        $codeVendeur = $request->codeVendeur;

        $vendeur = Vendeur::where("codeVendeur", $codeVendeur)->first();

        if ($vendeur) {
            $event = Event::where("codeEvent", $vendeur->codeEvent)->first();
            $dateEvent = $event->dateEvent;
            $dateNow = date("Y-m-d");

            $d1 = strtotime($dateEvent);
            $d2 = strtotime($dateNow);

            if ($d1 >= $d2) {
                $success["vendeur"] = $vendeur;
                return $this->sendResponse($success, "Vendeur");
            } else {
                return $this->sendError("Cet évènement est terminé", ['error' => 'Unauthorised']);
            }
        } else {
            return $this->sendError("Ce vendeur n'existe pas", ['error' => 'Unauthorised']);
        }
    }
}
