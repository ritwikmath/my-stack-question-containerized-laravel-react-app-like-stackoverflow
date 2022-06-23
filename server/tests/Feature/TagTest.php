<?php

use App\Models\Question;
use App\Models\User;
use App\Models\Tag;
use Database\Factories\TagFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('authenticates user', function () {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $tag_raw = Tag::factory()->raw();
    $tag = Tag::create($tag_raw);
    $this->getJson('api/tags')->assertStatus(401);
    $this->getJson("api/tags/{$tag->id}")->assertStatus(401);
    $this->postJson('api/tags', ['tag' => $tag_raw, 'question_id' => $question->id])->assertStatus(401);
    $this->deleteJson("api/tags/{$tag->id}")->assertStatus(401);
})->group('tags-authentication');

it('validates tag has name', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);

    $response = actingAs($user)->postJson('api/tags', []);
    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());

    $this->assertTrue(in_array('The name field is required.', $responseData));
    $this->assertTrue(in_array('The question id field is required.', $responseData));
});

it('validates question id exists', function() {
    $user = User::factory()->create();

    $response = actingAs($user)->postJson('api/tags', ['name' => ['database'], 'question_id' => 1]);
    $response->assertStatus(400);
    $responseData = json_decode($response->getContent());

    $this->assertTrue(in_array('The selected question id is invalid.', $responseData));
});

it('crates/finds tag and attach it with question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $tag = Tag::factory()->create();

    $response = actingAs($user)->postJson('api/tags', ['name' => ['database', $tag->name], 'question_id' => $question->id]);
    $response->assertStatus(201);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->message == 'Tag attached successfully');
    $this->assertCount(2, $question->tags);
    $this->assertTrue($question->tags[0]->name == 'database');
});

it('fetches all tags', function() {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $response = actingAs($user)->getJson('api/tags');
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());
    $this->assertTrue($responseData->data[0]->name == $tag->name);
});

it('fetches one tag with atatched questions', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $question_two = Question::factory()->create(['user_id' => $user->id]);
    $tag = Tag::factory()->create();
    $tag->questions()->sync([$question->id, $question_two->id]);

    $response = actingAs($user)->getJson("api/tags/{$tag->id}");
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());

    $this->assertTrue($responseData->data[0]->name == $tag->name);
    $this->assertTrue($responseData->data[0]->questions[0]->id == $question->id);
    $this->assertTrue($responseData->data[0]->questions[0]->id == $question->id);
    $this->assertCount(2, $responseData->data[0]->questions);
});

it('remove tag from atatched question', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $question_two = Question::factory()->create(['user_id' => $user->id]);
    $tag = Tag::factory()->create();
    $tag->questions()->sync([$question->id, $question_two->id]);

    $response = actingAs($user)->postJson("api/tags/{$tag->id}/{$question->id}");
    $response->assertStatus(200);
    $responseData = json_decode($response->getContent());

    $this->assertCount(1, $tag->questions);
    $this->assertTrue(!in_array($question->id, $tag->questions()->get()->pluck('id')->toArray()));
});

it('delete tag and link with questions', function() {
    $user = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);
    $tag = Tag::factory()->create();
    $tag->questions()->sync([$question->id]);

    $response = actingAs($user)->deleteJson("api/tags/{$tag->id}");
    $response->assertStatus(200);

    $this->assertEmpty(Tag::find($tag->id));
    $this->assertCount(0, DB::table('taggables')->get());
});


