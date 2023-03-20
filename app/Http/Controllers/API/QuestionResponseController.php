<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\QuestionResponse;
use Twilio\Rest\Client;


class QuestionResponseController extends Controller
{

    public function getResponses()
    {
       $responses = QuestionResponse::all();
       return response()->json($responses, 200);
    }

    public function deleteRecordFromTwilio(Request $request){
                $sid = getenv('ACCOUNT_SID');
                $token = getenv('TWILIO_TOKEN');
                $twilio = new Client($sid, $token);
                $twilio->recordings($request->Recording_Sid)->delete();
    }


    public function getResponsesByQuestionId($questionId)
    {
       $responses = QuestionResponse::where('question_id',$questionId)->get();
       return response()->json($responses, 200);
    }

    public function getResponsesBySurveyId($surveyId)
    {
       $responses = QuestionResponse::where('survey_id',$surveyId)->get();
       return response()->json($responses, 200);
    }
}