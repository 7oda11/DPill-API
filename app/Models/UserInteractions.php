<?php

namespace App\Models;

use App\Models\api\Pill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInteractions extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function PillInteraction()
    {
        return $this->belongsTo(PillInteraction::class, 'interaction_id');
    }

    public function pill1()
    {
        return $this->hasOneThrough(Pill::class, PillInteraction::class, 'id', 'id', 'interaction_id', 'pill_1_id');
    }

    public function pill2()
    {
        return $this->hasOneThrough(Pill::class, PillInteraction::class, 'id', 'id', 'interaction_id', 'pill_2_id');
    }
}
