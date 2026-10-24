<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Invoice> */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            // The client belongs to the same company as the invoice.
            'client_id' => fn (array $attributes) => Client::factory()->for(Company::find($attributes['company_id'])),
            'number' => fn (array $attributes) => 'INV-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => InvoiceStatus::Draft,
            'currency' => 'EUR',
            'issued_at' => null,
            'due_at' => null,
            'discount_percent' => 0,
            'notes' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => InvoiceStatus::Draft, 'issued_at' => null, 'due_at' => null]);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::Sent,
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(14)->toDateString(),
        ]);
    }

    public function paid(): static
    {
        return $this->sent()->state(fn () => ['status' => InvoiceStatus::Paid]);
    }

    public function overdue(int $days = 7): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::Sent,
            'issued_at' => now()->subDays(14 + $days)->toDateString(),
            'due_at' => now()->subDays($days)->toDateString(),
        ]);
    }
}
