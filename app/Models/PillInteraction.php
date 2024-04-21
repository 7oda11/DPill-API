<?php

namespace App\Models;

use App\Models\api\Pill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PillInteraction extends Model
{
    use HasFactory;
    public function pill1()
    {
        return $this->belongsTo(Pill::class, 'pill_1_id');
    }

    public function pill2()
    {
        return $this->belongsTo(Pill::class, 'pill_2_id');
    }
}
