<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'design_id', 'at_home', 'video_thumbnail'];


    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id', 'id');
    }
}
