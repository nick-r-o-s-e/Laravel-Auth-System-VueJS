<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        return response()->json(Country::all(), 200);
    }

    public function languages($countryId)
    {
        $country = Country::with('languages')->find($countryId);

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        return response()->json($country->languages, 200);
    }
}
