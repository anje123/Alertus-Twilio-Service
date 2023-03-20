<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Survey;
use Twilio\TwiML\VoiceResponse;


class SurveyController extends Controller
{

    /**
     *  1.this function is responsible for saying/speaking all the avaliable surveys on the database to the users
    */
    private function _getFirstSurvey() {
        $voiceResponse = new VoiceResponse();
        $surveys = Survey::all();
        foreach ($surveys as $survey) {
            $voiceResponse->say('Press'. $survey->id .'for' . $survey->title);
        }         
          $voiceResponse->gather(
            [
                'action' => route('connect.voice'),
                'method' => 'GET'
            ]
        );
        return $voiceResponse;

    }

     /**
      *  1. this function is responsible for checking if the selected survey exist
      *  2. this function is responsible for redirecting a selected survey to a its Questions
      */

    public function showVoice($id)
    {
        $surveyToTake = Survey::find($id);
        $voiceResponse = new VoiceResponse();


        if (is_null($surveyToTake)) {
            return $this->_responseWithXmlType($this->_noSuchVoiceSurvey($voiceResponse));
        }

        $surveyTitle = $surveyToTake->title;
        $voiceResponse->say("Hello and thank you for taking the $surveyTitle survey!");
        $voiceResponse->redirect($this->_urlForFirstQuestion($surveyToTake), ['method' => 'GET']);
        return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');
        
    }

     /**
      *  1.this function is responsible for saying/speaking if selected the survey does not exist to the users
      */
    private function _noSuchVoiceSurvey($voiceResponse)
    {
        $voiceResponse->say('Sorry, we could not find the survey to take');
        $voiceResponse->say('Good-bye');
        $voiceResponse->hangup();
        return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }


    /**
      *  1.this function provides the url or routes to the questions of the selected survey
      */
    private function _urlForFirstQuestion($survey)
    {
        return route(
            'question.show.voice',
            ['survey' => $survey->id,
             'question' => $survey->questions()->orderBy('id')->first()->id]
        );
    }

     /**
      *  this function is responsible for calling the showVoice function
      */
     
      private function connectVoice(Request $request)
      {
            $response = new VoiceResponse();
            $redirectResponse = $this->showVoice($request->input('Digits'));
            return $this->_responseWithXmlType($redirectResponse);
      }
      
    /**
     *  1. when sending responses from one route to another on the IVR, twilio ivr only understand Content-Type: XML response
    */
    private function _responseWithXmlType($response)
    {
        return $response->header('Content-Type', 'application/xml');
    }
}