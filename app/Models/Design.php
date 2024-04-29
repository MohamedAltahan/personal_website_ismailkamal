<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'status', 'thumbnail'];


    // relations____________________________________________________
    public function images()
    {
        return $this->hasMany(Image::class, 'design_id', 'id');
    }
    public function videos()
    {
        return $this->hasMany(Video::class, 'design_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
