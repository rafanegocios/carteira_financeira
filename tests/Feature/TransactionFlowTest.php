<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_deposit_money()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->post(route('deposit.store'), [
            'amount' => 100,
            'description' => 'Test deposit',
        ]);

        // Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 100,
            'description' => 'Test deposit',
        ]);

        $user->refresh();
        $this->assertEquals(100, $user->balance);
    }

    /** @test */
    public function user_can_transfer_money_to_another_user()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 200]);
        $recipient = User::factory()->create(['balance' => 0]);

        $this->actingAs($sender);

        // Act
        $response = $this->post(route('transfer.store'), [
            'email' => $recipient->email,
            'amount' => 100,
            'description' => 'Test transfer',
        ]);

        // Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'type' => 'transfer',
            'amount' => 100,
            'description' => 'Test transfer',
        ]);

        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(100, $sender->balance);
        $this->assertEquals(100, $recipient->balance);
    }

    /** @test */
    public function user_cannot_transfer_more_than_their_balance()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 50]);
        $recipient = User::factory()->create(['balance' => 0]);

        $this->actingAs($sender);

        // Act
        $response = $this->post(route('transfer.store'), [
            'email' => $recipient->email,
            'amount' => 100,
            'description' => 'Invalid transfer',
        ]);

        // Assert
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseMissing('transactions', [
            'user_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'type' => 'transfer',
            'amount' => 100,
        ]);

        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(50, $sender->balance);
        $this->assertEquals(0, $recipient->balance);
    }

    /** @test */
    public function user_can_reverse_a_transaction()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 200]);
        $recipient = User::factory()->create(['balance' => 0]);

        $this->actingAs($sender);

        // Create transfer
        $response = $this->post(route('transfer.store'), [
            'email' => $recipient->email,
            'amount' => 100,
            'description' => 'Test transfer',
        ]);

        $transaction = Transaction::latest()->first();

        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(100, $sender->balance);
        $this->assertEquals(100, $recipient->balance);

        // Act - Reverse the transaction
        $response = $this->post(route('transactions.reverse', $transaction->id), [
            'reason' => 'Test reversal',
        ]);

        // Assert
        $response->assertRedirect(route('transactions.show', $transaction->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'original_transaction' => $transaction->id,
            'type' => 'reversal',
        ]);

        $transaction->refresh();
        $this->assertTrue($transaction->is_reversed);

        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(200, $sender->balance);
        $this->assertEquals(0, $recipient->balance);
    }

    /** @test */
    public function user_cannot_reverse_another_users_transaction()
    {
        // Arrange
        $user1 = User::factory()->create(['balance' => 200]);
        $user2 = User::factory()->create(['balance' => 0]);
        $user3 = User::factory()->create(); // Third user who will try to reverse

        // User1 makes a transfer to User2
        $this->actingAs($user1);
        $this->post(route('transfer.store'), [
            'email' => $user2->email,
            'amount' => 100,
            'description' => 'Test transfer',
        ]);

        $transaction = Transaction::latest()->first();

        // User3 tries to reverse the transaction
        $this->actingAs($user3);
        $response = $this->post(route('transactions.reverse', $transaction->id), [
            'reason' => 'Unauthorized reversal attempt',
        ]);

        // Assert
        $response->assertSessionHasErrors('error');

        $transaction->refresh();
        $this->assertFalse($transaction->is_reversed);

        $user1->refresh();
        $user2->refresh();
        // Balances remain unchanged
        $this->assertEquals(100, $user1->balance);
        $this->assertEquals(100, $user2->balance);
    }

    /** @test */
    public function user_can_view_transaction_history()
    {
        // Arrange
        $user = User::factory()->create(['balance' => 0]);
        $this->actingAs($user);

        // Create some transactions
        $this->post(route('deposit.store'), ['amount' => 100, 'description' => 'First deposit']);
        $this->post(route('deposit.store'), ['amount' => 200, 'description' => 'Second deposit']);

        // Act
        $response = $this->get(route('transactions.history'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('First deposit');
        $response->assertSee('Second deposit');
    }

    /** @test */
    public function deposit_functionality_handles_negative_balance_correctly()
    {
        // Arrange
        $user = User::factory()->create(['balance' => -50]); // User with negative balance
        $this->actingAs($user);

        // Act
        $response = $this->post(route('deposit.store'), [
            'amount' => 100,
            'description' => 'Deposit to cover negative balance',
        ]);

        // Assert
        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertEquals(50, $user->balance); // -50 + 100 = 50
    }
}
