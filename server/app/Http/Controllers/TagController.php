<?php

namespace App\Http\Controllers;

use App\Models\Tag;
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

class TagController extends Controller
{
    public function index() {
        try {
            $tags = Tag::all();

            return response()->json([
                'message' => 'Tags fetched successfully',
                'data' => $tags
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Tags fetch unsuccessfull', 500);
        }
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|array',
                'name.*' => 'required|string|min:2',
                'question_id' => 'required|int|exists:questions,id'
            ]);
            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);

            $tag_ids = array();
            foreach($request->name as $tag_name ) {
                array_push($tag_ids, Tag::firstOrCreate(['name' => $tag_name])->id);
            }

            $question = Question::find($request->question_id);
            $question->tags()->syncWithoutDetaching($tag_ids);

            return response()->json([
                'message' => 'Tag attached successfully'
            ], 201);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Tag attach unsuccessfull', 500);
        }
    }

    public function show(Tag $tag) {
        try {
            return response()->json([
                'message' => 'Tags fetched successfully',
                'data' => $tag->with('questions')->get()
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Tags fetch unsuccessfull', 500);
        }
    }

    public function remove(Tag $tag, $question_id) {
        try {
            $tag->questions()->detach($question_id);

            return response()->json([
                'message' => 'Tag removed successfully'
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Tag remove unsuccessfull', 500);
        }
    }

    public function destroy(Tag $tag) {
        try {
            $tag->delete();

            return response()->json([
                'message' => 'Tag deleted successfully'
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json('Tag delete unsuccessfull', 500);
        }
    }
}
