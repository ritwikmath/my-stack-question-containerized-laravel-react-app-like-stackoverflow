<?php

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validates question has title', function() {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('api/questions', ['description' => 'This is a sample description']);

    $response->assertStatus(403);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The title field is required.');
});

it('validates question has description', function() {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('api/questions', ['title' => 'This is a sample title']);

    $response->assertStatus(403);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The description field is required.');
});

it('validates description has atleast 5 characters', function() {
    $user = User::factory()->create();
    $question = Question::factory()->raw(['description' => 'Not']);
    $response = $this->actingAs($user)->postJson('api/questions', $question);

    $response->assertStatus(403);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData[0] == 'The description must be at least 5 characters.');
});

it('can create a question and sets default status value open ', function() {
    $user = User::factory()->create();
    $question = Question::factory()->raw();
    $response = $this->actingAs($user)->postJson('api/questions', $question);

    $response->assertStatus(201);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->data->user_id == $user->id);
    $this->assertTrue($responseData->data->title == $question['title']);
    $this->assertTrue(Question::find($responseData->data->id)->status == 'open');
});

it('can fetch questions', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);
    $response = $this->actingAs($user)->getJson('api/questions');

    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data[0]->status == $question->status);
});

it('can fetch a single question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = $this->actingAs($user)->getJson("api/questions/{$question->slug}");

    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data instanceof stdClass);
    $this->assertTrue($responseData->data->id == $question->id);
});

it('can update a question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = $this->actingAs($user)->putJson("api/questions/{$question->slug}", ['status' => 'closed']);
    $responseData = json_decode($response->getContent());
    $response->assertStatus(200);
    $this->assertTrue($responseData->data->status == 'closed');
});

it('can delete a question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $response = $this->actingAs($user)->deleteJson("api/questions/{$question->slug}");
    $response->assertStatus(200);
    $this->assertCount(0, Question::where('id', $question->id)->get());
});
