<?php

namespace Haida\FilamentThreeCx\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Haida\FilamentThreeCx\Clients\CallControlClient;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Exceptions\ThreeCxApiException;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;

class ThreeCxApiExplorerPage extends Page implements HasForms
{
    use AuthorizesIam;
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'کاوشگر API';

    protected static ?string $title = 'کاوشگر API 3CX';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-command-line';

    protected static string|\UnitEnum|null $navigationGroup = '3CX';

    protected static ?int $navigationSort = 30;

    protected static ?string $permission = 'threecx.api_explorer';

    protected string $view = 'filament-threecx::pages.api-explorer';

    public ?array $data = [];

    public ?array $response = null;

    public ?int $statusCode = null;

    public ?int $durationMs = null;

    public ?string $error = null;

    public function mount(): void
    {
        $this->form->fill([
            'api_area' => 'xapi',
            'method' => 'GET',
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (! (bool) config('filament-threecx.api_explorer.enabled', false)) {
            return false;
        }

        return parent::shouldRegisterNavigation();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('درخواست')
                    ->schema([
                        Select::make('instance_id')
                            ->label('اتصال')
                            ->options(fn () => ThreeCxInstance::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        Select::make('api_area')
                            ->label('بخش API')
                            ->options([
                                'xapi' => 'XAPI',
                                'call_control' => 'Call Control',
                            ])
                            ->required(),
                        Select::make('method')
                            ->label('روش')
                            ->options($this->allowedMethods())
                            ->required(),
                        TextInput::make('path')
                            ->label('مسیر')
                            ->helperText('مسیر نسبی بدون دامنه، مثل: /contacts')
                            ->required(),
                        Textarea::make('query_json')
                            ->label('Query (JSON)')
                            ->rows(4)
                            ->placeholder('{"top": 10, "filter": "..."}'),
                        Textarea::make('body_json')
                            ->label('Body (JSON)')
                            ->rows(6)
                            ->placeholder('{"key": "value"}'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function sendRequest(): void
    {
        if (! (bool) config('filament-threecx.api_explorer.enabled', false)) {
            Notification::make()
                ->title('کاوشگر API غیرفعال است.')
                ->danger()
                ->send();

            return;
        }

        $state = $this->form->getState();
        $instanceId = $state['instance_id'] ?? null;
        if (! $instanceId) {
            $this->notifyError('اتصال انتخاب نشده است.');

            return;
        }

        $instance = ThreeCxInstance::query()->find($instanceId);
        if (! $instance) {
            $this->notifyError('اتصال یافت نشد.');

            return;
        }

        $apiArea = (string) ($state['api_area'] ?? 'xapi');
        $method = strtoupper((string) ($state['method'] ?? 'GET'));
        $path = trim((string) ($state['path'] ?? ''));

        if ($apiArea === 'xapi' && ! $instance->xapi_enabled) {
            $this->notifyError('XAPI برای این اتصال غیرفعال است.');

            return;
        }

        if ($apiArea === 'call_control' && ! $instance->call_control_enabled) {
            $this->notifyError('Call Control برای این اتصال غیرفعال است.');

            return;
        }

        if ($path === '') {
            $this->notifyError('مسیر الزامی است.');

            return;
        }

        if (! in_array($method, array_keys($this->allowedMethods()), true)) {
            $this->notifyError('روش نامعتبر است.');

            return;
        }

        if ($this->isDeniedPath($path)) {
            $this->notifyError('این مسیر به دلایل امنیتی مسدود است.');

            return;
        }

        $maxBodyBytes = (int) config('filament-threecx.api_explorer.max_body_bytes', 65536);
        $bodyJson = $state['body_json'] ?? null;
        if ($bodyJson && strlen((string) $bodyJson) > $maxBodyBytes) {
            $this->notifyError('اندازه بدنه بیش از حد مجاز است.');

            return;
        }

        $query = $this->decodeJson($state['query_json'] ?? null, true);
        if ($query === null) {
            $this->notifyError('ساختار Query معتبر نیست.');

            return;
        }

        $body = $this->decodeJson($bodyJson, false);
        if ($body === null && ! empty($bodyJson)) {
            $this->notifyError('ساختار Body معتبر نیست.');

            return;
        }

        $client = $apiArea === 'call_control'
            ? app(CallControlClient::class, ['instance' => $instance])
            : app(XapiClient::class, ['instance' => $instance]);

        $this->response = null;
        $this->error = null;
        $this->statusCode = null;
        $this->durationMs = null;

        try {
            $response = $client->request($method, $path, $query ?? [], $body);
            $this->response = $response;
            $this->statusCode = $client->lastStatusCode() ?? 200;
            $this->durationMs = $client->lastDurationMs();

            Notification::make()
                ->title('درخواست با موفقیت ارسال شد.')
                ->success()
                ->send();
        } catch (ThreeCxApiException $exception) {
            $this->response = $exception->payload();
            $this->statusCode = $exception->statusCode();
            $this->durationMs = $client->lastDurationMs();
            $this->error = $exception->getMessage();

            $this->notifyError($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
            $this->notifyError('درخواست ناموفق بود.');
        }
    }

    /**
     * @return array<string, string>
     */
    protected function allowedMethods(): array
    {
        $methods = (array) config('filament-threecx.api_explorer.allowed_methods', ['GET']);

        return collect($methods)
            ->mapWithKeys(fn ($method) => [strtoupper((string) $method) => strtoupper((string) $method)])
            ->toArray();
    }

    protected function isDeniedPath(string $path): bool
    {
        $denylist = array_map('strtolower', (array) config('filament-threecx.api_explorer.denylist', []));
        $haystack = strtolower($path);

        foreach ($denylist as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeJson(?string $json, bool $allowEmpty): ?array
    {
        if ($json === null || trim($json) === '') {
            return $allowEmpty ? [] : null;
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    protected function notifyError(string $message): void
    {
        Notification::make()
            ->title($message)
            ->danger()
            ->send();
    }
}
