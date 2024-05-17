<?php

namespace App\Models\api;

use App\Models\Contraindiacations;
use App\Models\Dosage;
use App\Models\Interaction;
use App\Models\SideEffects;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pill extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'name',
    //     'photo',
    //     'description',
    // ];
    protected $guarded = [];
    public function dosages()
    {
        return $this->hasMany(Dosage::class);
    }

    public function sideEffects()
    {
        return $this->hasMany(SideEffects::class);
    }


    public function contraindiacations()
    {
        return $this->hasMany(Contraindiacations::class);
    }
}
