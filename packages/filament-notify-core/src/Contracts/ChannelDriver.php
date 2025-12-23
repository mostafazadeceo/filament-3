<?php

namespace Haida\FilamentNotify\Core\Contracts;

use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\RenderedMessage;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;

interface ChannelDriver
{
    public function key(): string;

    public function label(): string;

    public function isInstalled(): bool;

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public function configSchema(): array;

    public function supportsTemplates(): bool;

    public function send(DeliveryContext $context, RenderedMessage $message): DeliveryResult;

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public function connectionTestForm(): array;

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public function sendTestForm(): array;

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     */
    public function testConnection(array $settings, array $data = []): DeliveryResult;

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $context
     */
    public function testSend(array $settings, array $data, array $context = []): DeliveryResult;
}
