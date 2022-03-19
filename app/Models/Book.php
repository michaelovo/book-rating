<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['user_id', 'title', 'description'];

    /* A book can be own by a user */
    public function user()
    {
      return $this->belongsTo(User::class);
    }

    /* A book can be rated by various users */
    public function ratings()
    {
      return $this->hasMany(Rating::class);
    }
}
