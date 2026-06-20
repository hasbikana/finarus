<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Category $category;
    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense']);
        $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_create_transaction(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/transactions', [
                'category_id' => $this->category->id,
                'account_id' => $this->account->id,
                'type' => 'expense',
                'amount' => 50000,
                'description' => 'Kopi Starbucks',
                'transaction_date' => '2024-01-15',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['description' => 'Kopi Starbucks']);
    }

    public function test_user_can_list_transactions(): void
    {
        Transaction::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'account_id' => $this->account->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/transactions');

        $response->assertStatus(200);
    }

    public function test_user_can_filter_transactions_by_type(): void
    {
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'account_id' => $this->account->id,
            'type' => 'expense',
        ]);

        $incomeCat = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'income']);
        Transaction::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $incomeCat->id,
            'account_id' => $this->account->id,
            'type' => 'income',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/transactions?type=expense');

        $response->assertStatus(200);
    }

    public function test_user_can_delete_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'account_id' => $this->account->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_transaction_amount_must_be_positive(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/transactions', [
                'category_id' => $this->category->id,
                'type' => 'expense',
                'amount' => -100,
                'transaction_date' => '2024-01-15',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }
}