<?php

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/audio/transcribe', 'QuestionResponseController@convert')->name('upload.audio');
// Route::get('/audio/transcribe', function (Request $request) {  

//     $client = new \GuzzleHttp\Client([
//         'base_uri' => 'http://localhost:8001',
//         'defaults' => [
//             'exceptions' => false
//         ]
//     ]);
    
  
//     $client->request('GET', '/api/transcribe', [
//         'query' => [
//             'questionId' => $request->questionId,
//              'callSid' => $request->callSid,
//              'RecordingSid' => $request->RecordingSid,
//               'url' => $request->url]
//     ]);
    

//     return redirect()->back();
//    // $client->send($request, ['timeout' => 2]);
//    //$response = $client->send($request, ['timeout' => 2]);

// })->name('upload.audio');

Route::post('/survey/create', [
    'uses' => 'SurveyController@createSurvey'
]);

Route::post('/survey/update/{id}', [
    'uses' => 'SurveyController@updateSurvey'
]);


Route::post('/question/create',[ 'uses' => 'QuestionController@createQuestion']);

Route::post('/question/update/{id}',[ 'uses' => 'QuestionController@updateQuestion']);



