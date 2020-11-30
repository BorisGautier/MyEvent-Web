<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Client;
use App\Models\Event;
use App\Models\Package;
use App\Models\Vendeur;
use Illuminate\Http\Request;
use PDF;

class ClientController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function getClient(Request $request)
    {
        $codeEvent = $request->codeEvent;

        $clients = Client::where("codeEvent", $codeEvent)->get();

        $success["client"] = $clients;

        return $this->sendResponse($success, 'Liste des Clients');
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
        $codePass = $request->codePass;
        $codeEvent = $request->codeEvent;
        $nomClient = $request->nomClient;
        $nomPack = $request->nomPack;
        $nomVendeur = $request->nomVendeur;
        $telClient = $request->telClient;
        $codeVendeur = $request->codeVendeur;

        $client = Client::where(["codePass" => $codePass, "codeEvent" => $codeEvent])->first();

        $event = Event::where("codeEvent", $codeEvent)->first();

        if ($client) {
            if ($client->nomClient == null) {
                $client->nomClient = $nomClient;
                $client->nomPack = $nomPack;
                $client->nomVendeur = $nomVendeur;
                $client->telClient = $telClient;
                $client->valide = "oui";
                $client->save();

                $vendeur = Vendeur::where(["codeVendeur" => $codeVendeur, "codeEvent" => $codeEvent])->first();
                $package = Package::where(["nomPackage" => $nomPack, "codeEvent" => $codeEvent])->first();

                $vendeur->nbreVente = $vendeur->nbreVente + 1;
                $package->nbreVente = $package->nbreVente + 1;
                $vendeur->save();
                $package->save();

                $oMessage = \Camoo\Sms\Message::create('5cdef6c7d7b7e', 'f2e4dfcdd20689cb762a352b9c1038ca275fce8cee2a638bce5ad567502f3f45');
                $oMessage->from = 'MyEvent';
                $oMessage->to = "+237" . $telClient;
                $oMessage->message = "Votre Pass pour l\'évènement " . $event->nomEvent . " a bien été validé et votre code est: " . $client->codePass;

                $j = json_encode($oMessage->send());

                $json = json_decode($j, true);

                $success["client"] = $client;

                return $this->sendResponse($success, $json["_message"]);
            } else {
                return $this->sendError("Ce Pass est deja vendu", ['error' => 'Unauthorised']);
            }
        } else {
            return $this->sendError("Ce Code n'existe pas reverifiez!!!", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    public function showClient(Request $request)
    {
        $codePass = $request->codePass;
        $codeEvent = $request->codeEvent;

        $client = Client::where(["codePass" => $codePass, "codeEvent" => $codeEvent])->first();

        $success["client"] = $client;

        return $this->sendResponse($success, "Client");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }

    public function revoque(Request $request)
    {
        $codePass = $request->codePass;
        $codeEvent = $request->codeEvent;

        $client = Client::where(["codePass" => $codePass, "codeEvent" => $codeEvent])->first();



        if ($client->nomClient != null) {
            $nomPack = $client->nomPack;

            $package = Package::where(["nomPackage" => $nomPack, "codeEvent" => $codeEvent])->first();

            $package->nbreVente = $package->nbreVente - 1;
            $package->save();

            $vendeur = Vendeur::where(["nomVendeur" => $client->nomVendeur, "codeEvent" => $codeEvent])->first();

            $vendeur->nbreVente = $vendeur->nbreVente - 1;
            $vendeur->save();

            $client->nomClient = "";
            $client->nomPack = "";
            $client->nomVendeur = "";
            $client->telClient = 0;
            $client->valide = "non";
            $client->save();
            return $this->sendResponse("", "code bien revoqué");
        } else {
            return $this->sendError("Ce Client n'existe pas reverifiez!!!", ['error' => 'Unauthorised']);
        }
    }

    public function printPdf()
    {
        // retreive all records from db
        $data = Client::where("valide", "oui")->get();

        $event = Event::where("codeEvent", $data[0]->codeEvent)->first();

        foreach ($data as $client) {
            $client["event"] = $event->nomEvent;
        }

        // share data to view
        view()->share('clients', $data);
        $pdf = PDF::loadView('clientpdf', $data);

        // download PDF file with download method
        return $pdf->download('pdf_file.pdf');
    }
}
