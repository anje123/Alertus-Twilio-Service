<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\Question;
use App\Survey;
use App\QuestionResponse;
use App\ResponseTranscription;
use Twilio\TwiML\VoiceResponse;
use Cookie;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;





class QuestionResponseController extends Controller
{


    public function __construct()
    { 
        $this->path = public_path('audio-contents/');
        $this->apikey = config('cloudconvert.api_key');
        $this->bucket_name = env('GOOGLE_CLOUD_STORAGE_BUCKET', 'femmy2');
    }

    /**
     * Initializes the SpeechClient
     * @return object \SpeechClient
     */
  


    public function storeVoice($surveyId, $questionId, Request $request)
    {
        Log::info($request);
        $question = Question::find($questionId);
        $newResponse = $question->responses()->create(
            ['response' => $this->_responseFromVoiceRequest($question, $request),
             'phone_no' => $request->Caller,
             'recording_sid' => $request->RecordingSid,
             'storage_completed' => false,
             'transcribe_completed' => false,
             'type' => 'voice',
             'session_sid' => $request->input('CallSid')]
       );

        $nextQuestion = $this->_questionAfter($question);

        if (is_null($nextQuestion)) {
            return $this->_responseWithXmlType($this->_voiceMessageAfterLastQuestion());
        } else {
            return $this->_responseWithXmlType(
                $this->_redirectToQuestion($nextQuestion, 'question.show.voice')
            );
        }
    }

    private function _responseFromVoiceRequest($question, $request)
    {
        if ($question->kind === 'free-answer') {
            return $request->input('RecordingUrl');
        } else {
            return $request->input('Digits');
        }
    }

    private function _questionAfter($question)
    {
        $survey = Survey::find($question->survey_id);
        $allQuestions = $survey->questions()->orderBy('id', 'asc')->get();
        $position = $allQuestions->search($question);
        $nextQuestion = $allQuestions->get($position + 1);
        return $nextQuestion;
    }


    private function _voiceMessageAfterLastQuestion()
    {
        $voiceResponse = new VoiceResponse();
        $voiceResponse->say('That was the last question');
        $voiceResponse->say('Thank you for participating in this survey');
        $voiceResponse->say('Good-bye');
        $voiceResponse->hangup();
        return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }



    private function _redirectToQuestion($question, $route)
    {
        $questionUrl = route(
            $route,
            ['question' => $question->id, 'survey' => $question->survey->id]
        );
        $redirectResponse = new VoiceResponse();

        $redirectResponse->redirect($questionUrl, ['method' => 'GET']);
        return Response::make($redirectResponse, '200')->header('Content-Type', 'text/xml');

    }

    private function _responseWithXmlType($response)
    {
        return $response->header('Content-Type', 'application/xml');
    }



    /**
     * Rename the file to FLAC format
     * @param  string  $name unique filename
     * @return striing $name
     */
    public static function getFilename($name)
    {
        return $name.".flac";
    } 

}