<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = "settings";
    protected $fillable = [
        "name",
        "logo",
        "email",
        "address",
        "phone_number",
        "name",
    ];
    public function getLogoAttribute($logo)
    {
        return asset('storage/' . $logo);
    }
}
