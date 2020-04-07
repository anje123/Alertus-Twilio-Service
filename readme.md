# ALERT TWILIO IVR Survey System

## Guidelines

ALERT TWILIO IVR Survey System is used to create surveys Via Voice Calls, this is a sophisticated research method used for collecting data from a pre-defined group of respondents to gain information and insights on various topics of interest

HOW TO SETUP:
* https://www.twilio.com/, Create An Account With Twilio
* Fill the Twilio Credentials on your env
* php artisan migrate , to migrate tables
* Create a Voice Project On Twilio Site
* Fill the webhook with your CallBack URL ..../voice/connect

## RESTful URLs
* Add a new survey:
    * POST /api/survey/create
    field: title
* Update a survey:
    * POST /api/survey/update/{surveyId}
    field: title
    params: Survey Id
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
* Deactivate a question to a particular survey:
    * DELETE /api/question/deactivate/{surveyId}
* Activate a question to a particular survey:
    * PATCH /api/question/activate/{surveyId}
* All questions in (or belonging to) this survey:
    * GET /api/survey/{surveyId}/questions
* To get all Deactivated Questions:
    * GET /api/questions/deactivated
* To get all Activated Questions:
    * GET /api/questions/activated
* To get all Responses For a Particular question:
    * GET /api/questions/responses/{questionId}
* To delete audio from twilio db:
    * POST /api/delete_recording_from_twilio
## HTTP Verbs

| HTTP METHOD | POST            | GET       | PUT         | DELETE |
| ----------- | --------------- | --------- | ----------- | ------ |
| CRUD OP     | CREATE          | READ      | UPDATE      | DELETE |

#### Thank You :heart: :pray:
