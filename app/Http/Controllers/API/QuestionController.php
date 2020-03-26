<?php

namespace App\Http\Controllers\API;

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

    public function getActivatedQuestions()
    {
        $questions = Question::all();
        return response()->json($questions, 200);
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
            return $this->sendError('Validations Error.', $validator->errors());       
        }

        $question = Question::create($input);

        return $this->sendResponse($question->toArray(), 'Question created successfully.');

    }

    public function updateQuestion(Request $request, $id)
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

    public function deactivateQuestion($id)
    {
        $question = Question::find($id);
        $question->delete();
        return $this->sendResponse($question->toArray(), 'Question Deactivated successfully.');

        
    }

    public function activateQuestion($id)
    {
        $question = Question::onlyTrashed()->find($id);
        $question->restore();
        return $this->sendResponse($question->toArray(), 'Question Activated successfully.');

        
    }
    
    public function viewQuestions($surveyId)
    {
        return view('questions.questions')->with('questions', Question::where('survey_id',$surveyId)->get());

    }


    public function getQuestionsBySurveyId($surveyId)
    {
        $questions = Question::where('survey_id',$surveyId)->get();
        return response()->json($questions, 200);
    }

    public function getDeactivatedQuestions()
    {
        $questions = Question::onlyTrashed()->get();
        return response()->json($questions, 200);
    }
}