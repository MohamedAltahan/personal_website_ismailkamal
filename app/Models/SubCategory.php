<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'slug', 'category_id', 'status'];

    // relations ----------------------------------------------------
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    // relations ----------------------------------------------------
    public function childCategories()
    {
        return $this->hasMany(ChildCategory::class);
    }
}
