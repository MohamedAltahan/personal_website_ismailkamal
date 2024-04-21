<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'slug', 'icon', 'status', 'created_at', 'updated_at'];

    // relations----------------------------------------------------
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
