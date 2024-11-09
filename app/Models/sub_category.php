<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_category extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'main_category_id',
        'description',
        'ai_score',
        'content_type',
        'input_type',
    ];

    public function mainCategory()
    {
        return $this->belongsTo(main_category::class, 'main_category_id');
    }
    public function categories()
    {
        return $this->hasMany(practice::class, 'sub_category_id');
    }
}
