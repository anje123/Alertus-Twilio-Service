<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Survey;
use App\QuestionResponse;
use Twilio\TwiML\VoiceResponse;


class SurveyController extends Controller
{

    public function connectVoice(Request $request)
    {
        $response = new VoiceResponse();
        $redirectResponse = $this->_redirectWithFirstSurvey('survey.show.voice', $response, $request->input('Digits'));
        return $this->_responseWithXmlType($redirectResponse);
    }
    


    public function showResults($surveyId)
    {
        $survey = Survey::find($surveyId);
        $responsesByCall = QuestionResponse::responsesForSurveyByCall($surveyId)
                         ->get()
                         ->groupBy('session_sid')
                         ->values();

        return response()->view(
            'results',
            ['survey' => $survey, 'responses' => $responsesByCall]
        );
    }

    public function showFirstSurveyResults()
    {
       // $firstSurvey = $this->_getFirstSurvey();
        $firstSurvey = Survey::orderBy('id', 'ASC')->get()->first();

        return redirect(route('survey.results', ['survey' => $firstSurvey->id]))
                ->setStatusCode(303);
    }

    public function showVoice($id)
    {
        $surveyToTake = Survey::find($id);
        $voiceResponse = new VoiceResponse();


        if (is_null($surveyToTake)) {
            return $this->_responseWithXmlType($this->_noSuchVoiceSurvey($voiceResponse));
        }
        $surveyTitle = $surveyToTake->title;
        $voiceResponse->say("Hello and thank you for taking the $surveyTitle survey!");
        $voiceResponse->redirect($this->_urlForFirstQuestion($surveyToTake, 'voice'), ['method' => 'GET']);
       return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');
        
    }

    private function _noSuchVoiceSurvey($voiceResponse)
    {
        $voiceResponse->say('Sorry, we could not find the survey to take');
        $voiceResponse->say('Good-bye');
        $voiceResponse->hangup();
        return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }

    private function _urlForFirstQuestion($survey, $routeType)
    {
        return route(
            'question.show.' . $routeType,
            ['survey' => $survey->id,
             'question' => $survey->questions()->orderBy('id')->first()->id]
        );
    }

    private function _noActiveSurvey($currentQuestion, $surveySession) {
        $noCurrentQuestion = is_null($currentQuestion) || $currentQuestion == 'deleted';
        $noSurveySession = is_null($surveySession) || $surveySession == 'deleted';

        return $noCurrentQuestion || $noSurveySession;
    }
    

    private function _redirectWithFirstSurvey($routeName, $response, $id)
    {
       // $firstSurvey = $this->_getFirstSurvey();
       $firstSurvey = Survey::find($id);

        if (is_null($firstSurvey)) {
            if ($routeName === 'survey.show.voice') {
                return $this->_noSuchVoiceSurvey($response);
            }
        }

        $response->redirect(
            route($routeName, ['id' => $firstSurvey->id]),
            ['method' => 'GET']
        );
        return Response::make($response, '200')->header('Content-Type', 'text/xml');

    }

    private function _responseWithXmlType($response) {
        return $response->header('Content-Type', 'application/xml');
    }


    public function _getFirstSurvey() {
       // return Survey::orderBy('id', 'DESC')->get()->first();
        $voiceResponse = new VoiceResponse();
        $surveys = Survey::all();
        foreach ($surveys as $survey) {
            $voiceResponse->say('Press'. $survey->id .'for' . $survey->title);
        }
       // $voiceResponse->say('Press 2 for crisis.');
        
          $voiceResponse->gather(
            [
                'action' => route('connect.voice'),
                'method' => 'GET'
            ]
        );
        return $voiceResponse;

    }

}
