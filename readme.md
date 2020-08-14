# ALERTUS IVR INTEGRATION SERVICE

## Introduction

AlertUS is a robust and highly scalable Interactive Voice Response (IVR) System for crowdsourcing real-time reports during natural disasters and crises in Nigeria.

## Other Microservices
 
1. Transcription Service : https://github.com/anje123/alertus-transcription-service

## Project Prototype:

#####  Phone Call Demo:​ ​https://www.youtube.com/watch?v=_FCB_C4cOPk
#####  Web View Demo: https://www.youtube.com/watch?v=onUL2L-JZ74

## Guidelines

This plateform can also be used to create surveys Via Voice Calls, this is a sophisticated research method used for collecting real time report from a pre-defined group of respondents to gain information and insights on various topics of interest


### HOW TO SETUP:

```
 git clone https://github.com/anje123/Alertus-Twilio-Service.git
```
```
 cp .env.example .env
```
```
 php artisan key:generate
```
```
 php artisan migrate
```
```
1. Create An Account With Twilio https://www.twilio.com
2. Fill the Twilio Credentials on your env file
3. Create a Voice Project On Twilio console https://www.twilio.com/console
4. Purchase a number, twilio gives you a trial number
5. Fill the webhook with your CallBack URL your-domain-name/voice/connect
   use ngrok for local development testing https://ngrok.com/
```

## RESTful URLs
```
* Add a new survey:
    * POST /api/survey/create
     field: title
  
* Update a survey:
    * POST /api/survey/update/{surveyId}
     field: title
    
* Deactivate a survey:
    * DELETE /api/survey/deactivate/{surveyId}
    
* Activate a survey:
    * PATCH /api/survey/activate/{surveyId}
    
* Add a new question to a particular survey:
    * POST /api/survey/create
     field: body, kind, survey_id
     
* Update a question to a particular survey:
    * POST /api/question/update/{surveyId}
     field: body, kind, survey_id
     
* Deactivate a question:
    * DELETE /api/question/deactivate/{questionId}
    
* Activate a question:
    * PATCH /api/question/activate/{questionId}
    
* All questions in (or belonging to) this survey:
    * GET /api/survey/{surveyId}/questions
    
* To get all Deactivated Questions:
    * GET /api/questions/deactivated
    
* To get all Activated Questions:
    * GET /api/questions/activated
    
* To get all Responses For a Particular question:
    * GET /api/questions/responses/{questionId}
    
* To get all Responses:   
    * GET /api/responses
    
* To delete audio from twilio db:
    * POST /api/delete_recording_from_twilio
        field: Recording_Sid
```

## HTTP Verbs

| HTTP METHOD | POST            | GET       | PUT         | DELETE |
| ----------- | --------------- | --------- | ----------- | ------ |
| CRUD OP     | CREATE          | READ      | UPDATE      | DELETE |

### Building IVR System With Twilio IVR is very easy to bulid, deploy and Iterate :+1::sparkling_heart:	

