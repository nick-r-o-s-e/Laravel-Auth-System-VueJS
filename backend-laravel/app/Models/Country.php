<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name'];

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'country_language');
    }
}
