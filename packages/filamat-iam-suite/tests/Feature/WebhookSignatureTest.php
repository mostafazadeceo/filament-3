<?php

declare(strict_types=1);

use Filamat\IamSuite\Services\WebhookService;

it('verifies webhook signatures', function () {
    $service = app(WebhookService::class);
    $payload = ['event' => 'test'];
    $secret = 'secret-key';
    $timestamp = time();
    $nonce = 'abc123';
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $signature = hash_hmac('sha256', $timestamp.'.'.$nonce.'.'.$body, $secret);

    expect($service->verifySignature($secret, $payload, $signature, $timestamp, $nonce))->toBeTrue();

    $oldTimestamp = $timestamp - 10000;
    $oldSignature = hash_hmac('sha256', $oldTimestamp.'.'.$nonce.'.'.$body, $secret);

    expect($service->verifySignature($secret, $payload, $oldSignature, $oldTimestamp, $nonce))->toBeFalse();
});
