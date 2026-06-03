<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_number_generation_skips_soft_deleted_records()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create a customer
        $customer1 = Customer::create([
            'customer_number' => '00100',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'category' => 'Customer',
        ]);

        // 2. Soft-delete the customer
        $customer1->delete();

        // 3. Attempt to create a new customer via the controller logic
        // We will hit the store route
        $response = $this->post(route('customers.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'gender' => 'Female',
            'phone_number' => '1234567890',
            'email' => 'jane@example.com',
            'category' => 'Customer',
        ]);

        $response->assertStatus(302);
        
        // 4. Verify the new customer got number 00101
        $newCustomer = Customer::where('email', 'jane@example.com')->first();
        $this->assertNotNull($newCustomer);
        $this->assertEquals('00101', $newCustomer->customer_number);

        // 5. Verify the soft-deleted one is still there
        $this->assertTrue(Customer::withTrashed()->where('customer_number', '00100')->exists());
    }
}
