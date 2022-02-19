<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationControlller extends Controller
{
    //

    public function postcode(Request $request)
    {
       $token = config('mapbox.token');
       $search = $request->input('search');
       $url = config('mapbox.url') .'{'. $search .'}.json';
       $filterType = '%2Cpostcode';
       $query = 'type=place' . $filterType;
       $location =  Http::get($url, [
           $query,
           'access_token' => $token
       ]);

       return $location;
    }
}
