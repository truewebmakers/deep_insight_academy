<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class main_category extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name'
    ];

    public function subCategories()
    {
        return $this->hasMany(sub_category::class, 'main_category_id');
    }
    public function categories()
    {
        return $this->hasMany(practice::class, 'main_category_id');
    }
}
