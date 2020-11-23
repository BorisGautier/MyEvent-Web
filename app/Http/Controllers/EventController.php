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

include(app_path() . "\Libraries\phpqrcode\qrlib.php");

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

        $events = Event::where("idUser", $user->id);

        return $this->sendResponse($events, 'Liste des Evenements');
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



            $event = Event::create($input);

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

                return $this->sendResponse($event, "Création de l'évènement reussie");
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
    public function show(Event $event)
    {
        //
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
    public function destroy(Event $event)
    {
        //
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
}
