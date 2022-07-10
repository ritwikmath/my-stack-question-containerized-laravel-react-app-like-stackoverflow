<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\Tag;
use Database\Factories\TagFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

$code = '$color = "red";
echo "My car is " . $color . "<br>";
echo "My house is " . $COLOR . "<br>";
echo "My boat is " . $coLOR . "<br>";';

it('authenticates user', function () {
    $user = User::factory()->create();
    $user_answering = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $answer_raw = Answer::factory()->raw(['user_id' => $user_answering->id, 'question_id' => $question->id]);
    $answer = Answer::create($answer_raw);
    $this->postJson('api/answers', [])->assertStatus(401);
    $this->deleteJson("api/answers/{$answer->id}")->assertStatus(401);
})->group('answers-authentication');

it('validates answer has all inputs', function() {
    $user = User::factory()->create();

    $response = actingAs($user)->postJson('api/answers', []);
    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());

    $this->assertTrue(in_array('The body field is required.', $responseData));
    $this->assertTrue(in_array('The question id field is required.', $responseData));
});

it('creates a answer for a question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $answer_raw = Answer::factory()->raw(['question_id' => $question->id]);

    $response = actingAs($user)->postJson('api/answers', $answer_raw);
    $response->assertStatus(201);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->message == 'Answer created successfully');
    $this->assertCount(1, $question->answers);
    $this->assertTrue($responseData->data->body == $answer_raw["body"]);
    $this->assertTrue($question->answers[0]->body == $answer_raw["body"]);
});

it('creates a answer with code snippet', function() use ($code) {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $answer_raw = Answer::factory()->raw(['question_id' => $question->id]);
    $answer_raw['code_snippet'] = $code;

    $response = actingAs($user)->postJson('api/answers', $answer_raw);
    $response->assertStatus(201);
    $responseData = json_decode($response->getContent());
    $stored_answer = Answer::find($responseData->data->id);

    $this->assertTrue($stored_answer->code->body == $code);
});

it('get all answers for a question', function() {
    $user = User::factory()->create();
    $user_answering = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    Answer::factory()->create(['question_id' => $question->id, 'user_id' => $user_answering->id]);
    sleep(1);
    $answer =  Answer::factory()->create(['question_id' => $question->id, 'user_id' => $user_answering->id]);

    $response = actingAs($user)->getJson("api/answers/{$question->id}");
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->message == 'Answers fetched successfully');
    $this->assertTrue($responseData->data[0]->user_name == $user_answering->name);
    $this->assertTrue($responseData->data[0]->body == $answer->body);
    $this->assertCount(2, $responseData->data);
});

it('updates an answer', function() {
    $user = User::factory()->create();
    $user_answering = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $user_answering->id]);

    $response = actingAs($user)->putJson("api/answers/{$answer->id}", ['body' => 'aaa', 'depricated' => true]);
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->message == 'Answer updated successfully');
    $this->assertTrue($responseData->data->body == 'aaa');
    $this->assertTrue($responseData->data->depricated == true);
});

it('deletes an answer', function() {
    $user = User::factory()->create();
    $user_answering = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $user_answering->id]);

    $response = actingAs($user)->deleteJson("api/answers/{$answer->id}");
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->message == 'Answer deleted successfully');
    $this->assertCount(0, $question->answers);
});
