<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\QuestionResponse;
use Cookie;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController as BaseController;
use Twilio\Rest\Client;


class QuestionResponseController extends BaseController
{

    public function getResponses($last_object_id)
    {
       $responses = QuestionResponse::where('id','>',$last_object_id)->take(10)->get();
       return response()->json($responses, 200);
    }

    public function deleteRecordFromTwilio(Request $request){
        Log::info($request);

            foreach ($request->all() as $data) {
                Log::info($data['Recording_Sid']);
                $sid = getenv('ACCOUNT_SID');
                $token = getenv('TWILIO_TOKEN');
                $twilio = new Client($sid, $token);
                $twilio->recordings($data['Recording_Sid'])->delete();
            } 
    }
}