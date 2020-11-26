<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendeur extends Model
{
    use HasFactory;

    protected $fillable = [
        "codeEvent", "nbreVente", "nomVendeur", "codeVendeur", "phone", "fcmToken", "platform"
    ];
}
