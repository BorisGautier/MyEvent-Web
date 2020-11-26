<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function getPackage(Request $request)
    {

        $codeEvent = $request->codeEvent;

        $packages = Package::where("codeEvent", $codeEvent)->get();

        $success["package"] = $packages;

        return $this->sendResponse($success, 'Liste des Packages');
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
        $input = $request->all();

        $package = Package::create($input);


        if ($package) {
            $success["package"] = $package;
            return $this->sendResponse($success, "Création du package reussie");
        } else {
            return $this->sendError("Erreur de Création.", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    }

    public function showPackage(Request $request)
    {

        $idPackage = $request->idPackage;
        $package = Package::where("id", $idPackage)->first();

        $success["package"] = $package;

        return $this->sendResponse($success, "Package");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
    }

    public function deletePackage(Request $request)
    {
        $idPackage = $request->idPackage;
        $package = Package::destroy($idPackage);

        if ($package) {
            return $this->sendResponse("", "Suppression réussie");
        } else {
            return $this->sendError("Erreur de Suppression.", ['error' => 'Unauthorised']);
        }
    }

    public function updatePackage(Request $request)
    {
        $package = new Package();

        $package->nomPackage = $request->nomPackage ?? $package->nomPackage;
        $package->prixPlace = $request->prixPlace ?? $package->prixPlace;

        $save = $package->save();

        if ($save) {
            $success["package"] = $package;
            return $this->sendResponse($success, "Package");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }
}
