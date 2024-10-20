<?php

namespace Tests\Unit\Jobs;

use Tests\DataBaseTestCase;
use App\Models\CollectionList;
use App\Models\RegisterOfDebt;
use Illuminate\Support\Facades\Log;
use App\Jobs\RegisterAndSendBilling;
use PHPUnit\Framework\Attributes\Test;

class RegisterAndSendBillingTest extends DataBaseTestCase
{
    /**
     * @description Testa o método handle.
     */
    #[Test]
    public function it_handles_register_and_send_billing()
    {
        $this->refreshDatabase();
        Log::shouldReceive('info')->times(6);

        $collectionList = CollectionList::factory()->create();
        $data = [
            ['John Doe', '123456789', 'john@example.com', 100.00, '2023-12-31', 'debt-uuid-1'],
            ['Jane Doe', '987654321', 'jane@example.com', 200.00, '2023-12-31', 'debt-uuid-2'],
        ];

        $job = new RegisterAndSendBilling($collectionList, $data);
        $job->handle();

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-1',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-2',
            'email' => 'jane@example.com',
        ]);
    }

    /**
     * @description Testa o método handle com um registro já notificado.
     */
    #[Test]
    public function it_handles_register_and_send_billing_with_notified_register()
    {
        $this->refreshDatabase();
        Log::shouldReceive('info')->times(4);

        $collectionList = CollectionList::factory()->create();
        $data = [
            ['John Doe', '123456789', 'john@example.com', 100.00, '2023-12-31', 'debt-uuid-1'],
            ['Jane Doe', '987654321', 'jane@example.com', 200.00, '2023-12-31', 'debt-uuid-2'],
        ];

        $notified_at = now();

        RegisterOfDebt::factory()->create([
            'uuid' => 'debt-uuid-1',
            'email' => 'john@example.com',
            'government_id' => '123456789',
            'amount' => 100.00,
            'dueDate' => '2023-12-31',
            'name' => 'John Doe',
            'collectionlist_id' => $collectionList->id,
            'notified_at' => $notified_at,
        ]);

        RegisterOfDebt::factory()->create([
            'uuid' => 'debt-uuid-2',
            'amount' => 200.00,
            'dueDate' => '2023-12-31',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'government_id' => '987654321',
            'collectionlist_id' => $collectionList->id,
            'notified_at' => $notified_at,
        ]);

        $job = new RegisterAndSendBilling($collectionList, $data);

        $job->handle();

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-1',
            'email' => 'john@example.com',
            'notified_at' => $notified_at,
        ]);

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-2',
            'email' => 'jane@example.com',
            'notified_at' => $notified_at,
        ]);

        $this->assertDatabaseCount('register_of_debts', 2);
    }

    /**
     * @description Testa o método handle onde ele recebe um registro repetido.
     */
    #[Test]
    public function it_handles_register_and_send_billing_with_duplicated_register()
    {
        $this->refreshDatabase();
        Log::shouldReceive('info')->times(10);

        $collectionList = CollectionList::factory()->create();
        $data = [
            ['John Doe', '123456789', 'john@example.com', 100.00, '2023-12-31', 'debt-uuid-1'],
            ['John Doe', '123456789', 'john@example.com', 100.00, '2023-12-31', 'debt-uuid-1'],
            ['John Doe', '123456789', 'john@example.com', 100.00, '2023-12-31', 'debt-uuid-1'],
            ['Jane Doe', '987654321', 'jane@example.com', 200.00, '2023-12-31', 'debt-uuid-2'],
        ];

        $job = new RegisterAndSendBilling($collectionList, $data);
        $job->handle();

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-1',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('register_of_debts', [
            'uuid' => 'debt-uuid-2',
            'email' => 'jane@example.com',
        ]);

        $this->assertDatabaseCount('register_of_debts', 2);
    }

}
