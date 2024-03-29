<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        "codeEvent", "dateEvent", "idUser", "nbrePackage", "nbrePlace", "nomEvent", "urlZip", "dateFin", "lon", "lat", "adresse", "siteWeb", "description", "ville", "cover", "vues"
    ];
}
