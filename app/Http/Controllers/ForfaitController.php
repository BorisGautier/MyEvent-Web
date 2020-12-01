<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Forfait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForfaitController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forfaits = Forfait::all();

        $success["forfait"] = $forfaits;

        return $this->sendResponse($success, 'Liste des Forfaits');
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

        $forfait = Forfait::create($input);

        if ($forfait) {
            $success["forfait"] = $forfait;

            return $this->sendResponse($success, "Creation réussie");
        } else {
            return $this->sendError("Erreur de création du forfait", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Forfait  $forfait
     * @return \Illuminate\Http\Response
     */
    public function show(Forfait $forfait)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Forfait  $forfait
     * @return \Illuminate\Http\Response
     */
    public function edit(Forfait $forfait)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Forfait  $forfait
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Forfait $forfait)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Forfait  $forfait
     * @return \Illuminate\Http\Response
     */
    public function destroy(Forfait $forfait)
    {
        //
    }

    public function updateForfait(Request $request)
    {
        $idForfait = $request->idForfait;

        $forfait = Forfait::find($idForfait);

        $forfait->nomForfait = $request->nomForfait ?? $forfait->nomForfait;
        $forfait->prix = $request->prix ?? $forfait->prix;
        $forfait->nbrePass = $request->nbrePass ?? $forfait->nbrePass;

        $save = $forfait->save();

        if ($save) {
            $success["forfait"] = $forfait;
            return $this->sendResponse($success, "forfait");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }

    public function deleteForfait(Request $request)
    {
        $delete = Forfait::destroy($request->idForfait);

        if ($delete) {
            return $this->sendResponse("", "Suppression réussie");
        } else {
            return $this->sendError("Echec de Suppression", ['error' => 'Unauthorised']);
        }
    }

    public function buyForfait(Request $request)
    {
        $user = Auth::user();
        $idForfait = $request->idForfait;

        $forfait = Forfait::find($idForfait);

        $user = User::find($user->id);

        $user->passRestant = $user->passRestant + $forfait->nbrePass;

        $save = $user->save();

        if ($save) {
            $success["user"] = $user;
            return $this->sendResponse($success, "Utilisateur");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }
}
