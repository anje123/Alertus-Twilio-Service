<?php

use Illuminate\Http\RedirectResponse;
use App\Question;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get(
    '/survey/{survey}/results',
    ['as' => 'survey.results', 'uses' => 'SurveyController@showResults']
);
Route::get(
    '/',
    ['as' => 'root', 'uses' => 'SurveyController@showFirstSurveyResults']
);
Route::get(
    '/voice/connect',
    ['as' => 'voice.connect', 'uses' => 'SurveyController@_getFirstSurvey']
);

Route::get(
    '/connect/voice',
    ['as' => 'connect.voice', 'uses' => 'SurveyController@connectVoice']
);

Route::get(
    '/survey/{id}/voice',
    ['as' => 'survey.show.voice', 'uses' => 'SurveyController@showVoice']
);

Route::get(
    '/survey/{survey}/question/{question}/voice',
    ['as' => 'question.show.voice', 'uses' => 'QuestionController@showVoice']
);

Route::get(
    '/survey/{survey}/question/{question}/response/voice',
    ['as' => 'response.store.voice', 'uses' => 'QuestionResponseController@storeVoice']
);

Route::post(
    '/survey/{survey}/question/{question}/response/transcription',
    ['as' => 'response.transcription.store', 'uses' => 'QuestionResponseController@storeTranscription']
);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/questions/{surveyId}', 'QuestionController@viewQuestions')->name('questions');
Route::get('/create_question',[ 'uses' => 'QuestionController@createQuestion']);
Route::get('/create_question',[ 'uses' => 'QuestionController@createQuestion']);
Route::get('/edit_question/{id}',[ 'uses' => 'QuestionController@editQuestion'])->name('question.edit');
Route::post('/store_question',[ 'uses' => 'QuestionController@storeQuestion'])->name('question.store');

Route::post('/question_update/{id}', [
    'uses' => 'QuestionController@updateQuestion',
    'as' => 'question.update'
   ]);