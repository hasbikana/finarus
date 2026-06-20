<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_dashboard_returns_data(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'balance', 'total_income', 'total_expense', 'active_saving_goals', 'recent_transactions', 'budget_progress',
            ]);
    }

    public function test_dashboard_calculates_totals(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'income']);
        $account = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 100000]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'account_id' => $account->id,
            'type' => 'income',
            'amount' => 500000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(500000, $data['total_income']);
    }

    public function test_unauthenticated_cannot_access_dashboard(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }
}