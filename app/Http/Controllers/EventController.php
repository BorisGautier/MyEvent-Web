<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\Models\Client;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use QRcode;
use ZipArchive;

class EventController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $events = Event::where("idUser", $user->id)->get();

        $success["event"] = $events;

        return $this->sendResponse($success, 'Liste des Evenements');
    }

    public function allEvent(Request $request)
    {

        $ville = $request->ville;
        $events = Event::where(["ville" => $ville, "public" => "oui"])->get();

        $success["event"] = $events;

        return $this->sendResponse($success, 'Liste des Evenements');
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

        $user = Auth::user();

        $name = $user->name;

        $input = $request->all();


        $nbreP = $request->nbrePlace;

        $chaine = explode(' ', $request->nomEvent);

        $path = $this->randomString($chaine[0]);



        if ($nbreP <= $user->passRestant && $nbreP != 0) {
            mkdir("qrcode/$name/$path/", 0755, true);

            $tempDir = "qrcode/$name/$path/";

            if ($request->file('cover')->isValid()) {
                $extension = $request->cover->extension();
                $path1      = $request->cover->storeAs($user->name . '/' . $request->codeEvent . '/cover', 'cover.' . $extension, 'public');
                $url       = env("APP_URL") . '/' . $user->name . '/' . $request->codeEvent . '/cover/' . 'cover.' . $extension;
            }




            $event = Event::create($input);
            $event->cover = $url ?? null;
            $event->save();

            if ($event) {
                for ($i = 1; $i < $nbreP + 1; $i++) {
                    $a =  substr($event->nomEvent, 0, 3);
                    $b = date("H");
                    $b1 = date("i");
                    $b2 = date("s");

                    $c = $a . $b . $b1 . $b2 . "n" . $i;

                    $qr = strtoupper($c);
                    $codeContents = $qr;
                    $fileName = $qr . '.png';



                    $pngAbsoluteFilePath = $tempDir . $fileName;

                    if (!file_exists($pngAbsoluteFilePath)) {
                        QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 5);
                    } else {
                        return $this->sendError("Erreur de Création de QRCodes.", ['error' => 'Unauthorised']);
                    }

                    $client = new Client();

                    $client->codePass = $codeContents;
                    $client->codeEvent = $event->codeEvent;
                    $client->save();
                }

                $files = File::glob(public_path() . "/qrcode/$name/$path/*.png");
                $archiveFile = public_path() . "/qrcode/$name/$path/$path.zip";
                $archive = new ZipArchive();


                if ($archive->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                    foreach ($files as $file) {
                        if ($archive->addFile($file, basename($file))) {
                            continue;
                        } else {
                            throw new InvalidArgumentException("file `{$file}` could not be added to the zip file: " . $archive->getStatusString());
                        }
                    }
                    $archive->close();
                    foreach ($files as $file) {
                        File::delete($file);
                    }
                }

                $event->urlZip = env("APP_URL") . "/qrcode/$name/$path/$path.zip";
                $event->save();
                $user = User::find($user->id);
                $user->nbreEvent = $user->nbreEvent + 1;
                $user->passRestant = $user->passRestant - $nbreP;
                $user->save();

                $success["event"] = $event;

                return $this->sendResponse($success, "Création de l'évènement reussie");
            } else {
                return $this->sendError("Erreur de Création.", ['error' => 'Unauthorised']);
            }
        } else {
            return $this->sendError("Nombre de pass insuffisant", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    }

    public function showEvent(Request $request)
    {

        $codeEvent = $request->codeEvent;
        $event = Event::where("codeEvent", $codeEvent)->first();

        $success["event"] = $event;

        return $this->sendResponse($success, "Evenement");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
    }

    public function deleteEvent(Request $request)
    {
        $codeEvent = $request->codeEvent;
        $event = Event::where("codeEvent", $codeEvent)->first();

        $eventDestroy =  Event::destroy($event->id);

        if ($eventDestroy) {
            return $this->sendResponse("", "Suppression réussie");
        } else {
            return $this->sendError("Echec de Suppression", ['error' => 'Unauthorised']);
        }
    }

    public function updateEvent(Request $request)
    {
        $user = Auth::user();
        $event = Event::find($request->idEvent);

        $event->dateEvent = $request->dateEvent ?? $event->dateEvent;

        $event->dateFin = $request->dateFin ?? $event->dateFin;
        $event->public = $request->public ?? $event->public;
        $event->lon = $request->lon ?? $event->lon;
        $event->lat = $request->lat ?? $event->lat;
        $event->adresse = $request->adresse ?? $event->adresse;
        $event->siteWeb = $request->siteWeb ?? $event->siteWeb;
        $event->description = $request->description ?? $event->description;
        $event->ville = $request->ville ?? $event->ville;

        if ($request->file('cover')->isValid()) {
            $extension = $request->cover->extension();
            $path      = $request->cover->storeAs($user->name . '/' . $event->codeEvent . '/cover', 'cover.' . $extension, 'public');
            $url       = $user->name . '/' . $event->codeEvent . '/cover/' . 'cover.' . $extension;
        }

        $event->cover = $url ?? $event->cover;

        $save = $event->save();

        if ($save) {
            $success["event"] = $event;
            return $this->sendResponse($success, "Evenement");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }

    public function randomString($string, $length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $string . $randomString;
    }

    public function updateVues(Request $request)
    {
        $codeEvent = $request->codeEvent;

        $event = Event::where('codeEvent', $codeEvent)->first();

        $event->vues = $event->vues + 1;

        $save =  $event->save();

        if ($save) {
            $success["event"] = $event;
            return $this->sendResponse($success, "Evenement");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }
}
