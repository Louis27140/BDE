<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\IpFilter;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
});

// Mail
Route::post('mail', function (Request $request) {
    $request->validate([
        'nom' => 'required',
        'email' => 'required|email',
        'message' => 'required'
    ]);

    $data = array('title' => 'Message de la part de ' . request('nom'), 'subtitle' => 'Message', "description" => request('message'), "url" => "mailto:" . request('email'), 'linkText' => "Contacter");

    Mail::send('layout.mail', $data, function($message) {
        $message->to(env('ADMIN_MAIL', ''), 'Administrator')->subject('Message depuis l\'interface');
        $message->from(env('MAIL_USERNAME', 'bde@bde.fr'), 'BDE');
    });

    return back();
});

Auth::routes();

// API
Route::group(['middleware' => IpFilter::class], function () {
    Route::post('/api/register', 'ApiController@register');
    Route::put('/api/profile', 'ApiController@updateSelf');
    Route::put('/api/users/{id}', 'ApiController@updateUser');
});

// Administration
Route::get('/administration', 'AdministrationController@index')->middleware('auth');

// Notifications
Route::get('/notifications', 'NotificationsController@index')->middleware('auth');
Route::delete('/notifications/{notification}', 'NotificationsController@delete')->middleware('auth');

// Ideas Box
Route::get('ideas', 'IdeasController@index');
Route::get('ideas/search', 'IdeasController@searchIdea');
Route::post('ideas', 'IdeasController@createIdea')->middleware('auth');
Route::get('ideas/create' , 'IdeasController@create')->middleware('auth');
Route::get('ideas/{id}/edit', 'IdeasController@edit')->middleware('auth');
Route::put('ideas/{id}', 'IdeasController@editIdea')->middleware('auth');
Route::delete('ideas/{id}', 'IdeasController@deleteIdea')->middleware('auth');


// Vote des idées
Route::post('votes/{id}', 'IdeasController@addVote')->middleware('auth');
Route::delete('votes/{id}', 'IdeasController@deleteVote')->middleware('auth');