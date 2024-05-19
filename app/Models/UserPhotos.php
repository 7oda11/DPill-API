<?php

namespace App\Models;

use App\Models\api\Pill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhotos extends Model
{
    use HasFactory;
    protected $table = 'user_photo';
    protected $guarded = [];


    public function pill()
    {
        return $this->belongsTo(Pill::class);
    }
}
