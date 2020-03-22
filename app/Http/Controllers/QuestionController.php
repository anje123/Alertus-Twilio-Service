<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Question;
use Twilio\TwiML\VoiceResponse;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;



class QuestionController extends BaseController
{

    public function viewQuestions($surveyId)
    {
        return view('questions.questions')->with('questions', Question::where('survey_id',$surveyId)->get());

    }


    public function createQuestion(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'body' => 'required',
            'kind' => 'required',
            'survey_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $question = Question::create($input);

        return $this->sendResponse($question->toArray(), 'Question created successfully.');

    }

    public function updateSurvey(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'body' => 'required',
            'kind' => 'required',
            'survey_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $question = Question::find($id);
        $question->body = $request->body;
        $question->kind = $request->kind;
        $question->survey_id = $request->survey_id;
        $question->save();
        return $this->sendResponse($question->toArray(), 'Question updated successfully.');

    }
    
    public function showVoice($surveyId, $questionId)
    {
        $questionToAsk = Question::find($questionId);
        return $this->_responseWithXmlType($this->_commandForVoice($questionToAsk));
    }


    private function _commandForVoice($question)
    {
        $voiceResponse = new VoiceResponse();
        $voiceResponse->say($question->body);
        $voiceResponse->say($this->_messageForVoiceQuestion($question));
        $voiceResponse = $this->_registerResponseCommand($voiceResponse, $question);

      return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }


    private function _messageForVoiceQuestion($question)
    {
        $questionPhrases = collect(
            [
                'free-answer' => "Please record your answer after the beep and then hit the pound sign",
                'yes-no'      => "Please press the one key for yes and the zero key for no and then hit the pound sign",
                'numeric'     => "Please press a number between 1 and 10 and then hit the pound sign"
            ]
        );

        return $questionPhrases->get($question->kind, "Please press a number and then the pound sign");
    }


    private function _registerResponseCommand($voiceResponse, $question)
    {
        $storeResponseURL = route(
            'response.store.voice',
            ['question' => $question->id,
             'survey' => $question->survey->id],
            false
        );

        if ($question->kind === 'free-answer') {
            $transcribeUrl = route(
                'response.transcription.store',
                ['question' => $question->id,
                 'survey' => $question->survey->id]
            );
            $voiceResponse->record(
                ['method' => 'GET',
                 'maxLength' => 30,
                 'action' => $storeResponseURL
               //  'transcribe' => true,
               //  'transcribeCallback' => $transcribeUrl
               ]
            );
        } elseif ($question->kind === 'yes-no') {
            $voiceResponse->gather(['method' => 'POST', 'action' => $storeResponseURL]);
        } elseif ($question->kind === 'numeric') {
            $voiceResponse->gather(['method' => 'POST', 'action' => $storeResponseURL]);
        }
        return $voiceResponse;
    }

    private function _responseWithXmlType($response) {
        return $response->header('Content-Type', 'application/xml');
    }

}
