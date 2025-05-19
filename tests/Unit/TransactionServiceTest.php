<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();
    }

    /** @test */
    public function it_can_create_a_deposit_transaction()
    {
        // Arrange
        $user = User::factory()->create(['balance' => 0]);
        $amount = 100.00;

        // Act
        $transaction = $this->transactionService->deposit($user, $amount, 'Test deposit');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('Test deposit', $transaction->description);

        // Check user balance was updated
        $user->refresh();
        $this->assertEquals($amount, $user->balance);
    }

    /** @test */
    public function it_cannot_deposit_zero_or_negative_amount()
    {
        // Arrange
        $user = User::factory()->create(['balance' => 0]);

        // Act & Assert
        $this->expectException(Exception::class);
        $this->transactionService->deposit($user, 0, 'Invalid deposit');

        $this->expectException(Exception::class);
        $this->transactionService->deposit($user, -50, 'Invalid deposit');
    }

    /** @test */
    public function it_can_create_a_transfer_transaction()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 200]);
        $recipient = User::factory()->create(['balance' => 0]);
        $amount = 100.00;

        // Act
        $transaction = $this->transactionService->transfer($sender, $recipient, $amount, 'Test transfer');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals('transfer', $transaction->type);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals('Test transfer', $transaction->description);
        $this->assertEquals($sender->id, $transaction->user_id);
        $this->assertEquals($recipient->id, $transaction->recipient_id);

        // Check balances were updated
        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(100, $sender->balance);
        $this->assertEquals(100, $recipient->balance);
    }

    /** @test */
    public function it_prevents_transfer_if_insufficient_balance()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 50]);
        $recipient = User::factory()->create(['balance' => 0]);
        $amount = 100.00;

        // Act & Assert
        $this->expectException(Exception::class);
        $this->transactionService->transfer($sender, $recipient, $amount, 'Test transfer');

        // Verify balances remain unchanged
        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(50, $sender->balance);
        $this->assertEquals(0, $recipient->balance);
    }

    /** @test */
    public function it_can_reverse_a_deposit_transaction()
    {
        // Arrange
        $user = User::factory()->create(['balance' => 0]);
        $amount = 100.00;
        $deposit = $this->transactionService->deposit($user, $amount, 'Test deposit');

        $user->refresh();
        $this->assertEquals(100, $user->balance);

        // Act
        $reversal = $this->transactionService->reverseTransaction($deposit, 'Reversing deposit');

        // Assert
        $this->assertInstanceOf(Transaction::class, $reversal);
        $this->assertEquals('reversal', $reversal->type);
        $this->assertEquals($amount, $reversal->amount);

        // Check the original transaction is marked as reversed
        $deposit->refresh();
        $this->assertTrue($deposit->is_reversed);
        $this->assertEquals($reversal->id, $deposit->reversed_by);

        // Check user balance was updated
        $user->refresh();
        $this->assertEquals(0, $user->balance);
    }

    /** @test */
    public function it_can_reverse_a_transfer_transaction()
    {
        // Arrange
        $sender = User::factory()->create(['balance' => 200]);
        $recipient = User::factory()->create(['balance' => 0]);
        $amount = 100.00;

        $transfer = $this->transactionService->transfer($sender, $recipient, $amount, 'Test transfer');

        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(100, $sender->balance);
        $this->assertEquals(100, $recipient->balance);

        // Act
        $reversal = $this->transactionService->reverseTransaction($transfer, 'Reversing transfer');

        // Assert
        $this->assertInstanceOf(Transaction::class, $reversal);
        $this->assertEquals('reversal', $reversal->type);
        $this->assertEquals($amount, $reversal->amount);

        // Check the original transaction is marked as reversed
        $transfer->refresh();
        $this->assertTrue($transfer->is_reversed);
        $this->assertEquals($reversal->id, $transfer->reversed_by);

        // Check balances were restored
        $sender->refresh();
        $recipient->refresh();
        $this->assertEquals(200, $sender->balance);
        $this->assertEquals(0, $recipient->balance);
    }

    /** @test */
    public function it_cannot_reverse_an_already_reversed_transaction()
    {
        // Arrange
        $user = User::factory()->create(['balance' => 0]);
        $amount = 100.00;
        $deposit = $this->transactionService->deposit($user, $amount, 'Test deposit');

        // First reversal
        $reversal = $this->transactionService->reverseTransaction($deposit, 'Reversing deposit');

        // Act & Assert
        $this->expectException(Exception::class);
        // Attempt second reversal
        $this->transactionService->reverseTransaction($deposit, 'Attempting second reversal');
    }
}
