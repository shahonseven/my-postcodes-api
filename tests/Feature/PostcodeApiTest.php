<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostcodeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create API token
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        // Seed postcode data
        Postcode::insert([
            ['postcode' => '50000', 'city' => 'Kuala Lumpur', 'state' => 'Wp Kuala Lumpur', 'state_code' => 'KUL', 'created_at' => now(), 'updated_at' => now()],
            ['postcode' => '40000', 'city' => 'Shah Alam', 'state' => 'Selangor', 'state_code' => 'SGR', 'created_at' => now(), 'updated_at' => now()],
            ['postcode' => '40100', 'city' => 'Shah Alam', 'state' => 'Selangor', 'state_code' => 'SGR', 'created_at' => now(), 'updated_at' => now()],
            ['postcode' => '80000', 'city' => 'Johor Bahru', 'state' => 'Johor', 'state_code' => 'JHR', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer '.$this->token];
    }

    public function test_requires_authentication_for_postcodes(): void
    {
        $response = $this->getJson('/api/states');

        $response->assertStatus(401);
    }

    public function test_can_get_states_with_auth(): void
    {
        $response = $this->getJson('/api/states', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_cities_with_auth(): void
    {
        $response = $this->getJson('/api/cities', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_can_filter_cities_by_state_with_auth(): void
    {
        $response = $this->getJson('/api/cities?state=SGR', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['city' => 'Shah Alam'])
            ->assertJsonMissing(['city' => 'Kuala Lumpur']);
    }

    public function test_can_search_cities_with_auth(): void
    {
        $response = $this->getJson('/api/cities?search=alam', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['city' => 'Shah Alam']);
    }

    public function test_can_lookup_postcode_with_auth(): void
    {
        $response = $this->getJson('/api/postcode/50000', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    ['postcode' => '50000', 'city' => 'Kuala Lumpur', 'state' => 'Wp Kuala Lumpur', 'state_code' => 'KUL'],
                ],
            ]);
    }

    public function test_returns_404_for_invalid_postcode_with_auth(): void
    {
        $response = $this->getJson('/api/postcode/99999', $this->authHeaders());

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Postcode not found',
            ]);
    }

    public function test_can_search_postcodes_with_auth(): void
    {
        $response = $this->getJson('/api/search?q=Shah', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment(['city' => 'Shah Alam']);
    }

    public function test_search_requires_query_with_auth(): void
    {
        $response = $this->getJson('/api/search', $this->authHeaders());

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Search query is required',
            ]);
    }

    public function test_can_register_user(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully',
            ]);
        $this->assertArrayHasKey('token', $response->json('data'));
    }

    public function test_can_login(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ]);
        $this->assertArrayHasKey('token', $response->json('data'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_can_get_user_info(): void
    {
        $response = $this->getJson('/api/user', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'test@example.com',
                ],
            ]);
    }

    public function test_can_logout(): void
    {
        $response = $this->postJson('/api/logout', [], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);

        // Verify token was revoked by checking it's no longer in the database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_revoked_token_is_invalid(): void
    {
        // Create a token and revoke it
        $token = $this->user->createToken('temp-token')->plainTextToken;
        $this->user->tokens()->delete();

        // Request with revoked token should fail
        $response = $this->getJson('/api/states', ['Authorization' => 'Bearer '.$token]);
        $response->assertStatus(401);
    }
}
