<?php

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticates user', function () {
    $user = User::factory()->create();
    $question_raw= Question::factory()->raw();
    $question_raw['user_id'] = $user->id;
    $question = Question::create($question_raw);
    $this->getJson('api/questions')->assertStatus(401);
    $this->getJson("api/questions/{$question->slug}")->assertStatus(401);
    $this->postJson('api/questions', $question_raw)->assertStatus(401);
    $this->putJson("api/questions/{$question->slug}", ['status' => 'closed'])->assertStatus(401);
    $this->deleteJson("api/questions/{$question->slug}")->assertStatus(401);
})->group('questions-authentication');

it('validates question has title', function () {
    $user = User::factory()->create();
    $response = actingAs($user)->postJson('api/questions', ['description' => 'This is a sample description']);
    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The title field is required.');
});

it('validates question has description', function () {
    $user = User::factory()->create();
    $response = actingAs($user)->postJson('api/questions', ['title' => 'This is a sample title']);
    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The description field is required.');
});

it('validates description has atleast 5 characters', function () {
    $user = User::factory()->create();
    $question = Question::factory()->raw(['description' => 'Not']);
    $response = actingAs($user)->postJson('api/questions', $question);

    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The description must be at least 5 characters.');
});

it('can create a question and sets default status value open ', function () {
    $user = User::factory()->create();
    $question = Question::factory()->raw();
    $response = actingAs($user)->postJson('api/questions', $question);

    $response->assertStatus(201);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->data->user_id == $user->id);
    $this->assertTrue($responseData->data->title == $question['title']);
    $this->assertTrue(Question::find($responseData->data->id)->status == 'open');
});

it('can fetch questions', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);
    $response = actingAs($user)->getJson('api/questions');

    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data[0]->status == $question->status);
});

it('can fetch a single question', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = actingAs($user)->getJson("api/questions/{$question->slug}");

    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data instanceof stdClass);
    $this->assertTrue($responseData->data->id == $question->id);
});

it('can update a question', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = actingAs($user)->putJson("api/questions/{$question->slug}", ['status' => 'closed']);
    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data->status == 'closed');
});

it('can delete a question', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = actingAs($user)->deleteJson("api/questions/{$question->slug}");
    $response->assertStatus(200);
    $this->assertCount(0, Question::where('id', $question->id)->get());
});
