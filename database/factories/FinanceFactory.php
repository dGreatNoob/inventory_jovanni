<?php

namespace Database\Factories;

use App\Models\Finance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finance>
 */
class FinanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Finance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['receivable', 'payable', 'expense']);
        $date = $this->faker->dateTimeBetween('-6 months', 'now');
        
        $baseData = [
            'date' => $date,
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'payment_method' => $this->faker->randomElement(['Cash', 'Bank Transfer', 'Credit Card', 'Check', 'GCash', 'Maya']),
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'remarks' => $this->faker->optional()->sentence(),
        ];

        switch ($type) {
            case 'receivable':
                return array_merge($baseData, [
                    'type' => 'receivable',
                    'reference_id' => 'REC' . $date->format('ymd') . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
                    'customer' => $this->faker->randomElement(['Customer 1', 'Customer 2', 'Customer 3', 'Customer 4', 'Customer 5']),
                    'sales_order' => 'SO-' . str_pad($this->faker->numberBetween(1, 100), 3, '0', STR_PAD_LEFT),
                    'party' => $this->faker->sentence(3),
                    'due_date' => $this->faker->dateTimeBetween($date, '+30 days'),
                ]);
                
            case 'payable':
                $amount = $baseData['amount'];
                $balance = $this->faker->randomFloat(2, 0, $amount);
                $status = $balance == 0 ? 'paid' : ($balance < $amount ? 'partial' : 'pending');
                
                return array_merge($baseData, [
                    'type' => 'payable',
                    'reference_id' => 'PAY' . $date->format('ymd') . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
                    'supplier' => $this->faker->randomElement(['Supplier 1', 'Supplier 2', 'Supplier 3', 'Supplier 4', 'Supplier 5']),
                    'purchase_order' => 'PO-' . str_pad($this->faker->numberBetween(1, 100), 3, '0', STR_PAD_LEFT),
                    'party' => $this->faker->sentence(3),
                    'due_date' => $this->faker->dateTimeBetween($date, '+30 days'),
                    'balance' => $balance,
                    'status' => $status,
                ]);
                
            case 'expense':
                return array_merge($baseData, [
                    'type' => 'expense',
                    'reference_id' => 'EXP' . $date->format('ymd') . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
                    'party' => $this->faker->sentence(3),
                    'category' => $this->faker->randomElement([
                        'Office Supplies', 'Travel', 'Utilities', 'Meals & Entertainment', 
                        'Marketing', 'Rent', 'Maintenance', 'Professional Fees', 'Miscellaneous'
                    ]),
                ]);
                
            default:
                return $baseData;
        }
    }

    /**
     * Indicate that the finance record is a receivable.
     */
    public function receivable(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'receivable',
        ]);
    }

    /**
     * Indicate that the finance record is a payable.
     */
    public function payable(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'payable',
        ]);
    }

    /**
     * Indicate that the finance record is an expense.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
        ]);
    }
}
