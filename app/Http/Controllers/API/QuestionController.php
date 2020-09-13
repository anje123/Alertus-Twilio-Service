<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Support\Facades\Validator;



class QuestionController extends Controller
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
            return ['response' => 'Validation Error'. $validator->errors()];      
        }

        $question = Question::create($input);

        return response()->json($question, 200);

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
            return ['response' => 'Validation Error.', $validator->errors()];       
        }
        
        $question = Question::find($id);
        $question->body = $request->body;
        $question->kind = $request->kind;
        $question->survey_id = $request->survey_id;
        $question->save();
        return response()->json($question, 200);

    }

    public function deactivateQuestion($id)
    {
        $question = Question::find($id);
        $question->delete();
        return response()->json($question, 200); 

        
    }

    public function activateQuestion($id)
    {
        $question = Question::onlyTrashed()->find($id);
        $question->restore();
        return response()->json($question, 200);        
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