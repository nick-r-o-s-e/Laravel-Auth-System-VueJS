<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name'];

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_language');
    }
}