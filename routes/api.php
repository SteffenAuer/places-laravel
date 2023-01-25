<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('places/{id}', function (Request $request, string $placeId) {
    $place = Cache::remember("place_{$placeId}", 3600  * 24, function () use ($placeId) {
        info('Fetching item with id ' . $placeId . ' from upstream');

        return json_decode(Http::get(config('places.base_url') . "/{$placeId}"), true);
    });

    return json_encode(array_intersect_key(
        $place,
        array_flip([
            'displayed_what',
            'displayed_where',
            'opening_hours',
            'local_entry_id',
            'addresses',
        ])
    ));
});
