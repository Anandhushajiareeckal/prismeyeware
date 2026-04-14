<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@prismeyewear.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password')
        ]);

        $faker = \Faker\Factory::create();

        // 1. Customers
        $customers = [];
        for ($i = 1; $i <= 15; $i++) {
            $customers[] = \App\Models\Customer::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => substr($faker->phoneNumber, 0, 20),
                'address_line_1' => substr($faker->streetAddress, 0, 50),
                'city' => $faker->city,
                'date_of_birth' => $faker->dateTimeBetween('-70 years', '-10 years')->format('Y-m-d'),
                'gender' => $faker->randomElement(['Male', 'Female', 'Other']),
                'customer_number' => 'CUST-' . strtoupper(\Illuminate\Support\Str::random(6)),
            ]);
        }

        // 2. Prescriptions
        $types = ['Distance', 'Reading', 'Bifocal', 'Progressive', 'Contact Lens'];
        foreach ($customers as $c) {
            if (rand(0, 10) > 2) {
                \App\Models\Prescription::create([
                    'customer_id' => $c->id,
                    'prescription_date' => $faker->dateTimeThisYear()->format('Y-m-d'),
                    'type' => $faker->randomElement($types),
                    'doctor_name' => 'Dr. ' . $faker->lastName,
                    'eye_side' => $faker->randomElement(['Both', 'R', 'L']),
                    'sphere' => number_format($faker->randomFloat(2, -5, 5), 2),
                    'cylinder' => number_format($faker->randomFloat(2, -3, 0), 2),
                    'axis' => rand(1, 180),
                    'add' => number_format($faker->randomFloat(2, 1, 3), 2),
                    'recall_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                    'comments' => 'Regular checkup.'
                ]);
            }
        }

        // 3. Repairs
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Repair::create([
                'customer_id' => $customers[array_rand($customers)]->id,
                'repair_number' => 'REP-' . date('Ym') . rand(100, 999),
                'repair_date' => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'completion_date' => $faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
                'status' => $faker->randomElement(['Pending', 'In Progress', 'Completed']),
                'repair_type' => $faker->randomElement(['Frame Adjust', 'Nosepad Swap', 'Lens Trim']),
                'sku' => 'FRM-' . rand(1000, 9999),
                'assigned_staff' => $faker->firstName,
                'repair_price' => rand(15, 75),
                'repair_notes' => 'Customer dropped glasses.'
            ]);
        }

        // 4. Orders & Invoices
        $categories = ['Frames', 'Lenses', 'Accessories', 'Service'];
        foreach (range(1, 10) as $i) {
            $customer = $customers[array_rand($customers)];
            
            // Order
            $order = \App\Models\Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . date('Ym') . rand(1000, 9999),
                'order_date' => $faker->dateTimeThisMonth()->format('Y-m-d'),
                'sales_staff' => $faker->firstName,
                'order_status' => $faker->randomElement(['Processing', 'Completed']),
                'total_amount' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0
            ]);

            $orderItems = [];
            $subtotal = 0;
            
            for ($k = 0; $k < rand(1, 4); $k++) {
                $qty = rand(1, 2);
                $price = $faker->randomFloat(2, 50, 400);
                $item = \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $faker->word . ' ' . $faker->randomElement($categories),
                    'sku' => 'SKU-' . rand(1000, 9999),
                    'category' => $faker->randomElement($categories),
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => 0,
                    'tax' => 0
                ]);
                $subtotal += ($qty * $price);
            }

            $order->update(['total_amount' => $subtotal]);

            // Create Invoice for Order
            $invoice = \App\Models\Invoice::create([
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . date('Ym') . rand(1000, 9999),
                'invoice_date' => $order->order_date,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'payment_status' => $order->order_status == 'Completed' ? 'Paid' : 'Unpaid',
                'payment_mode' => 'Card'
            ]);

            foreach ($order->items as $oi) {
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $oi->product_name,
                    'sku' => $oi->sku,
                    'quantity' => $oi->quantity,
                    'rate' => $oi->unit_price,
                    'discount' => 0,
                    'tax' => 0
                ]);
            }
        }
    }
}
