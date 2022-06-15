<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $questions = Question::where('status', 'open')->get();

            return response()->json([
                'message' => 'Question list fetched successfully',
                'data' => $questions
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Question list fetch unsuccessfull', 500);
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
                'title' => 'required|string|unique:questions',
                'description' => 'required|string|min:5'
            ]);
            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);

            $question = Question::create([
                'title' => $request->title,
                'slug' => Str::snake($request->title. '_' . Carbon::now()->timestamp),
                'description' => $request->description,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Question created successfully',
                'data' => $question
            ], 201);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Question create unsuccessfull', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        try {
            return response()->json([
                'message' => 'Question fetched successfully',
                'data' => $question
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Question fetch unsuccessfull', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'string|unique:questions',
                'description' => 'string|min:5',
                'status' => 'string|in:open,closed'
            ]);
            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);

            $updatedValues = array_intersect_key($request->all(), $question->toArray());

            Question::where('id', $question->id)->update($updatedValues);

            $question->refresh();

            return response()->json([
                'message' => 'Question update successfully',
                'data' => $question
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Question update unsuccessfull', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();

            return response()->json([
                'message' => 'Question update successfully'
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Question update unsuccessfull', 500);
        }
    }
}
