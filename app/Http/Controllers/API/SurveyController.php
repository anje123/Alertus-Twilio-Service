<?php

namespace App\Http\Controllers\API;
use Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Survey;
use App\QuestionResponse;
use Twilio\TwiML\VoiceResponse;
use Illuminate\Support\Facades\Validator;


class SurveyController extends Controller
{
    public function getSurveys()
    {
        $surveys = Survey::all();
        return response()->json($surveys, 200);
    }

    public function deactivateSurvey($id)
    {
        $survey = Survey::find($id);
        $survey->delete();
        return $this->sendResponse($survey->toArray(), 'Survey Deactivated successfully.');

        
    }

    public function activateSurvey($id)
    {
        $survey = Survey::onlyTrashed()->find($id);
        $survey->restore();
        return $this->sendResponse($survey->toArray(), 'Survey Activated successfully.');

        
    }

    public function updateSurvey(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $survey = Survey::find($id);
        $survey->title = $request->title;
        $survey->save();
        return $this->sendResponse($survey->toArray(), 'Survey updated successfully.');

    }
}