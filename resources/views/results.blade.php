@extends('layouts.frontend')

@section('content')
<div class="main-content">
<div class="page-content">
    <div class="container-fluid">
    <ul class="list-unstyled">
        @foreach ($responses as $response)
        <li>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Response from: {{ $response->first()->session_sid }}</h5>
                    Survey type:
                    @if($response->first()->type == 'voice')
                    <span class="badge badge-pill badge-soft-success font-size-11">{{ $response->first()->type }}</span>
                    @else
                    <span class="label label-success">
                    @endif
                    </span>
                </div>
                <div class="card-body">
                    @foreach ($response as $questionResponse)
                    <ol class="list-group">
                        <li class="list-group-item">Question: {{ $questionResponse->question->body }}</li>
                        <li class="list-group-item">Answer type: {{ $questionResponse->question->kind }}</li>
                        <li class="list-group-item">
                            @if($questionResponse->question->kind === 'free-answer' && $questionResponse->type === 'voice')
                            <div class="voice-response">
                                <audio controls>
                                            <source src="{{ $questionResponse->response }}" type="audio/mpeg">

                                  </audio>
                            </div>
                            @elseif($questionResponse->question->kind === 'yes-no')
                                @if($questionResponse->response == 1)
                                YES
                                @else
                                NO
                                @endif
                            @else
                            {{ $questionResponse->response }}
                            @endif
                        </li>
    
                        @if(!is_null($questionResponse->transcription))
                        <li class="list-group-item">Transcribed Answer: {{ $questionResponse->transcription }}</li>
                       @else
                        <li class="list-group-item">
                            <a class="btn btn-xs"  href="{{ route('upload.audio',[
                            'questionId' => $questionResponse->question->id,
                            'callSid' => $response->first()->session_sid,
                            'RecordingSid' => $response->first()->recording_sid,
                            'url' => $questionResponse->response]) }}"> 
                         <span class="badge badge-pill badge-soft-primary font-size-11">Transcribe</span>
                            </a>
                        </li>
                        @endif

                    </ol>
                    <br>
                    @endforeach
                    
                </div>
            </div>
        </li>
        @endforeach
    </ul>
</div>
    </div>
</div>

@stop
