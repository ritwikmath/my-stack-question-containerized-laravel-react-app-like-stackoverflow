<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Exception;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($question_id)
    {
        try {
            $answers = Answer::join('users', 'users.id', 'answers.user_id')->where('question_id', $question_id)->select('answers.*', 'users.name as user_name')->orderByDesc('answers.created_at')->get();

            return response()->json([
                'message' => 'Answers fetched successfully',
                'data' => $answers
            ], 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Answer fetch unsuccessfull', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'body' => 'required|string|min:5',
                'question_id' => 'required|exists:questions,id'
            ]);
            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);

            $answer = Answer::create([
                'body' => $request->body,
                'question_id' => $request->question_id,
                'user_id' => auth()->id()
            ]);

            if ($request->code_snippet) {
                $answer->code()->create(['body' => $request->code_snippet]);
            }

            return response()->json([
                'message' => 'Answer created successfully',
                'data' => $answer
            ], 201);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Answer create unsuccessfull', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Answer $answer)
    {
        try {
            $validator = Validator::make($request->all(), [
                'body' => 'string|unique:questions',
                'depricated' => 'boolean|required_without:body'
            ]);
            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);

            $updatedValues = array_intersect_key($request->all(), $answer->toArray());

            Answer::where('id', $answer->id)->update($updatedValues);

            $answer->refresh();

            return response()->json([
                'message' => 'Answer updated successfully',
                'data' => $answer
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Answer update unsuccessfull', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Answer $answer)
    {
        try {
            $answer->delete();

            return response()->json([
                'message' => 'Answer deleted successfully'
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Answer delete unsuccessfull', 500);
        }
    }
}
