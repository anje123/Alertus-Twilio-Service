<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Question;
use Twilio\TwiML\VoiceResponse;



class QuestionController extends Controller
{

     /**
      * 1. this function is responsible for saying/speaking questions of the selected survey
      */
    private function _commandForVoice($question)
    {
        $voiceResponse = new VoiceResponse();
        $voiceResponse->say($question->body);
        $voiceResponse->say($this->_messageForVoiceQuestion($question));
        $voiceResponse = $this->_registerResponseCommand($voiceResponse, $question);

      return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }

      /**
      *  1. this function is responsible for for recording responses gotten from the user.
      *  2. it is responsible for redirecting the responses to the route with which it will be stored on the database
      */
    
      private function _registerResponseCommand($voiceResponse, $question)
      {
          $storeResponseURL = route(
              'response.store.voice',
              ['question' => $question->id,
               'survey' => $question->survey->id],
              false
          );
  
          if ($question->kind === 'free-answer') {
              $voiceResponse->record(
                  ['method' => 'GET',
                   'maxLength' => 30,
                   'action' => $storeResponseURL
                 ]
              );
          } elseif ($question->kind === 'yes-no') {
              $voiceResponse->gather(['method' => 'POST', 'action' => $storeResponseURL]);
          } elseif ($question->kind === 'numeric') {
              $voiceResponse->gather(['method' => 'POST', 'action' => $storeResponseURL]);
          }
          return $voiceResponse;
      }

     /**
      *  1. this function is responsible for selecting and speaking questions phrases 
      *   e.g Please record your answer after the beep and then hit the pound sign
      */
    private function _messageForVoiceQuestion($question)
    {
        $questionPhrases = collect(
            [
                'free-answer' => "Please record your answer after the beep and then hit the pound sign",
                'yes-no'      => "Please press the one key for yes and the zero key for no and then hit the pound sign",
                'numeric'     => "Please press a number between 1 and 10 and then hit the pound sign"
            ]
        );

        return $questionPhrases->get($question->kind);
    }


    /**
     *  1. when sending responses from one route to another on the IVR, twilio ivr only understand Content-Type: XML response
    */
    private function _responseWithXmlType($response) {
        return $response->header('Content-Type', 'application/xml');
    }


    /**
     *  1.this function is responsible for redirecting route the the _commandForVoice function
    */
    public function showVoice($surveyId, $questionId)
    {
        $questionToAsk = Question::find($questionId);
        return $this->_responseWithXmlType($this->_commandForVoice($questionToAsk));
    }

}
