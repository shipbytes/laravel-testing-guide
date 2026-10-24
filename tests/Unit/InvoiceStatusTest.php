<?php

use App\Enums\InvoiceStatus;

it('allows the transitions of a real invoice lifecycle', function (InvoiceStatus $from, InvoiceStatus $to) {
    expect($from->canTransitionTo($to))->toBeTrue();
})->with([
    'draft to sent' => [InvoiceStatus::Draft, InvoiceStatus::Sent],
    'draft to void' => [InvoiceStatus::Draft, InvoiceStatus::Void],
    'sent to paid' => [InvoiceStatus::Sent, InvoiceStatus::Paid],
    'sent to void' => [InvoiceStatus::Sent, InvoiceStatus::Void],
]);

it('refuses transitions that would rewrite history', function (InvoiceStatus $from, InvoiceStatus $to) {
    expect($from->canTransitionTo($to))->toBeFalse();
})->with([
    'paid to anything' => [InvoiceStatus::Paid, InvoiceStatus::Sent],
    'void to anything' => [InvoiceStatus::Void, InvoiceStatus::Draft],
    'draft straight to paid' => [InvoiceStatus::Draft, InvoiceStatus::Paid],
    'sent back to draft' => [InvoiceStatus::Sent, InvoiceStatus::Draft],
]);

it('is final once paid or void', function () {
    expect(InvoiceStatus::Paid->transitions())->toBe([])
        ->and(InvoiceStatus::Void->transitions())->toBe([]);
});
