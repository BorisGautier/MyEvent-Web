<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Client;
use App\Models\Event;
use App\Models\Package;
use App\Models\Statistique;
use App\Models\Vendeur;
use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;

class StatistiqueController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function statByEvent(Request $request)
    {

        // representer
        // - le nombre de vente de chaque vendeur sur un graphe
        // - le nombre de vente par package sur un graphe
        // - Total des personnes présentes/ le nombre de pass total
        // - Revenus total obtenu par la vente des pass
        //
        $idEvent = $request->idEvent;

        $event = Event::find($idEvent);

        $vendeurs = Vendeur::where("codeEvent", $event->codeEvent)->get();
        $packages = Package::where("codeEvent", $event->codeEvent)->get();

        $client = Client::where("presence", "oui")->get();

        $nbrePresent = count($client);

        foreach ($packages as $package) {
            $nbreVente = $package->nbreVente;
            $prixPack = $package->prixPlace;

            $total[] = $nbreVente * $prixPack;
        }

        $sum = array_sum($total);

        $passTotal = $event->nbrePlace;

        $success["vendeur"] = $vendeurs;
        $success["package"] = $packages;
        $success["present"] = $nbrePresent;
        $success["totalGain"] = $sum;
        $success["totalPass"] = $passTotal;

        return $this->sendResponse($success, "statistiques");
    }

    public function downloadRapport(Request $request)
    {
        $lava = new Lavacharts;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Statistique  $statistique
     * @return \Illuminate\Http\Response
     */
    public function show(Statistique $statistique)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Statistique  $statistique
     * @return \Illuminate\Http\Response
     */
    public function edit(Statistique $statistique)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Statistique  $statistique
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Statistique $statistique)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Statistique  $statistique
     * @return \Illuminate\Http\Response
     */
    public function destroy(Statistique $statistique)
    {
        //
    }
}
