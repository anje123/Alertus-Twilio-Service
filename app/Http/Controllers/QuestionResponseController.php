<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Http\Controllers\Controller;
use App\Question;
use App\Survey;
use App\QuestionResponse;
use Twilio\TwiML\VoiceResponse;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;



class QuestionResponseController extends Controller
{

    /**
     *  1. this function is responsible for storing voice responses on the database
    */
    public function storeVoice($surveyId, $questionId, Request $request)
    {
        $question = Question::find($questionId);
        $newResponse = $question->responses()->create(
            ['response' => $this->_responseFromVoiceRequest($question, $request),
             'phone_no' => $request->Caller,
             'recording_sid' => $request->RecordingSid,
             'storage_completed' => false,
             'transcribe_completed' => false,
             'type' => 'voice',
             'country' => $request->FromCountry,
             'session_sid' => $request->input('CallSid')]
       );

       sendResponsesToTranscriptionService($request->all());
        $nextQuestion = $this->_questionAfter($question);

        if (is_null($nextQuestion)) {
            return $this->_responseWithXmlType($this->_voiceMessageAfterLastQuestion());
        } else {
            return $this->_responseWithXmlType(
                $this->_redirectToQuestion($nextQuestion, 'question.show.voice')
            );
        }
    }

     /**
     * 1.this function is responsible for sending responses to the advanced queueing system (RabbitMQ)
    */
    public function sendResponsesToTranscriptionService($data)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        //Create the queue
        $channel->queue_declare('transcribe_queue',   //$queue - Either sets the queue or creates it if not exist
                                false,          //$passive - Do not modify the servers state
                                true,           //$durable - Data will persist if crash or restart occurs
                                false,          //$exclusive - Only one connection will use queue, and deleted when closed
                                false           //$auto_delete - Queue is deleted when consumer is no longer subscribes
                            );


        //Create the message, set the delivery to be persistant for crashes and restarts
        $msg = new AMQPMessage(json_encode($data), array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($msg, '', 'transcribe_queue');

        echo "Sent Audio To Server!'\n";

        $channel->close();
        $connection->close();
    }

    /**
     *  1. this function is responsible for selecting responses whether the user used voice response or digits response
    */
    private function _responseFromVoiceRequest($question, $request)
    {
        if ($question->kind === 'free-answer') {
            return $request->input('RecordingUrl').'.mp3';
        } else {
            return $request->input('Digits');
        }
    }

    /**
     *  1. this function is responsible for selecting the next question after each question
    */
    private function _questionAfter($question)
    {
        $survey = Survey::find($question->survey_id);
        $allQuestions = $survey->questions()->orderBy('id', 'asc')->get();
        $position = $allQuestions->search($question);
        $nextQuestion = $allQuestions->get($position + 1);
        return $nextQuestion;
    }


    /**
     *  1. this function is responsible for saying/speaking when the users are done with the survey, basically after the last question
    */
    private function _voiceMessageAfterLastQuestion()
    {
        $voiceResponse = new VoiceResponse();
        $voiceResponse->say('That was the last question');
        $voiceResponse->say('Thank you for participating in this survey');
        $voiceResponse->say('Good-bye');
        $voiceResponse->hangup();
        return Response::make($voiceResponse, '200')->header('Content-Type', 'text/xml');

    }


    /**
     *  1. when sending responses from one route to another on the IVR, twilio ivr only understand Content-Type: XML response
    */
    private function _responseWithXmlType($response)
    {
        return $response->header('Content-Type', 'application/xml');
    }

    /**
     *  1.this function is responsible for redirecting route the the _questionAfter function
    */
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

}