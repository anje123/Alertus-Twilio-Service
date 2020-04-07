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


Route::post('/survey/create', ['uses' => 'API\SurveyController@createSurvey']);
Route::get('/surveys', ['uses' => 'API\SurveyController@getSurveys']);

Route::post('/survey/update/{id}', [
    'uses' => 'API\SurveyController@updateSurvey'
]);
Route::delete('/survey/deactivate/{id}',[ 'uses' => 'API\SurveyController@deactivateSurvey']);
Route::patch('/survey/activate/{id}',[ 'uses' => 'API\SurveyController@activateSurvey']);




Route::post('/question/create',[ 'uses' => 'API\QuestionController@createQuestion']);
Route::post('/question/update/{id}',[ 'uses' => 'API\QuestionController@updateQuestion']);
Route::delete('/question/deactivate/{id}',[ 'uses' => 'API\QuestionController@deactivateQuestion']);
Route::patch('/question/activate/{id}',[ 'uses' => 'API\QuestionController@activateQuestion']);
Route::get('/survey/{surveyId}/questions', ['uses' => 'API\QuestionController@getQuestionsBySurveyId']);
Route::get('/questions/deactivated', ['uses' => 'API\QuestionController@getDeactivatedQuestions']);
Route::get('/questions/activated', ['uses' => 'API\QuestionController@getActivatedQuestions']);


Route::get('/responses/{last_object_id}', ['uses' => 'API\QuestionResponseController@getResponses']);
Route::get('/questions/responses/{questionId}', ['uses' => 'API\QuestionResponseController@getResponsesByQuestionId']);

Route::post('/delete_recording_from_twilio', ['uses' => 'API\QuestionResponseController@deleteRecordFromTwilio']);



