<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;
    protected $fillable = ['image', 'main_title', 'description', 'banner_at_home'];
}
