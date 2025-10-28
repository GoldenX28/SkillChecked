<?php

namespace Tests\Feature;

use App\Models\TypeSpeedResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeSpeedResultApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_store_result()
    {
        $payload = [
            'wpm' => 60,
            'correct_chars' => 300,
            'errors' => 5,
            'typed_chars' => 305,
            'accuracy' => 98.0,
        ];

        $response = $this->postJson('/api/typespeed/result', $payload);
        $response->assertStatus(401);
    }

    public function test_validation_errors_returned_for_missing_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/typespeed/result', []); // missing fields
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['wpm', 'correct_chars', 'errors', 'typed_chars', 'accuracy']);
    }

    public function test_authenticated_user_can_store_result()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $payload = [
            'wpm' => 72,
            'correct_chars' => 360,
            'errors' => 3,
            'typed_chars' => 363,
            'accuracy' => 99.17,
        ];

        $response = $this->postJson('/api/typespeed/result', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['wpm' => 72]);

        $this->assertDatabaseHas('type_speed_results', [
            'user_id' => $user->id,
            'wpm' => 72,
        ]);
    }

    public function test_leaderboard_returns_top_results_ordered()
    {
        $users = User::factory()->count(3)->create();

        // create results with different wpms
        TypeSpeedResult::factory()->create(['user_id' => $users[0]->id, 'wpm' => 50]);
        TypeSpeedResult::factory()->create(['user_id' => $users[1]->id, 'wpm' => 90]);
        TypeSpeedResult::factory()->create(['user_id' => $users[2]->id, 'wpm' => 70]);

        $response = $this->getJson('/api/typespeed/leaderboard');
        $response->assertStatus(200)
                 ->assertJsonStructure([['id','user_id','wpm','created_at']]);

        $data = $response->json();
        // ensure highest wpm comes first
        $this->assertGreaterThanOrEqual($data[1]['wpm'] ?? 0, $data[0]['wpm']);
    }
}