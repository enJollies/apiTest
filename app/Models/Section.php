<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sections';
    protected $fillable = [
        'title'
    ];

    public function users() {

        return $this->belongsToMany(User::class, 'section_user', 'section_id', 'user_id');
    }

}
