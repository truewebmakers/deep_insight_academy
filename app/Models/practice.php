<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class practice extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'title',
        'q_num',
        'prepare_time',
        'test_time',
        'paragraph',
        'audio',
        'image',
        'is_short',
        'difficulty',
        'image_type',
        'essay_type',
        'main_category_id',
        'sub_category_id',
        'disable'
    ];

    public function mainCategory()
    {
        return $this->belongsTo(main_category::class, 'main_category_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(sub_category::class, 'sub_category_id');
    }
}
