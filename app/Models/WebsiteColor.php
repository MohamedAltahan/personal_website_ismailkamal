<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'main_background',
        'secondary_background',
        'main_banner',
        'btn',
        'navbar',
        'text',
    ];
}
