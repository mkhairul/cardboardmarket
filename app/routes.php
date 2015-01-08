<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Parse\ParseUser;
use Parse\ParseClient;
use Parse\ParseACL;
use Parse\ParseQuery;
use Parse\ParseRole;

Route::get('/', function()
{
    ParseClient::initialize(Config::get('parse.application_id'), Config::get('parse.rest_api'), Config::get('parse.master'));
    $user = new ParseUser();
    if(!$user->isAuthenticated())
    {
    }
    return View::make('mainpage');
});

Route::get('pages/{filename}', function($filename){
    View::addExtension('hbs', 'php');
    $file = explode('.', $filename);
    unset($file[count($file)-1]);
    $filename = implode('.', $file);
    return View::make('pages.' . $filename);
});