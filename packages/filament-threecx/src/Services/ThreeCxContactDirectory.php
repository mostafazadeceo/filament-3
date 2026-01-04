<?php

namespace Haida\FilamentThreeCx\Services;

use Haida\FilamentThreeCx\Contracts\ContactDirectoryInterface;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Support\Arr;

class ThreeCxContactDirectory implements ContactDirectoryInterface
{
    public function __construct(
        protected ThreeCxEventDispatcher $events,
    ) {}

    public function lookup(ThreeCxInstance $instance, ?string $phone, ?string $email): array
    {
        if (! $phone && ! $email) {
            return [];
        }

        $query = ThreeCxContact::query()->where('instance_id', $instance->getKey());

        $query->where(function ($builder) use ($phone, $email) {
            if ($phone) {
                $builder->orWhereJsonContains('phones', $phone);
            }

            if ($email) {
                $builder->orWhereJsonContains('emails', $email);
            }
        });

        return $query->limit($this->limit())->get()->map(fn (ThreeCxContact $contact) => $this->transform($contact))->all();
    }

    public function search(ThreeCxInstance $instance, string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $builder = ThreeCxContact::query()->where('instance_id', $instance->getKey());

        $builder->where(function ($sub) use ($query) {
            $sub->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('external_id', 'like', '%'.$query.'%')
                ->orWhereJsonContains('phones', $query)
                ->orWhereJsonContains('emails', $query);
        });

        return $builder->limit($this->limit())->get()->map(fn (ThreeCxContact $contact) => $this->transform($contact))->all();
    }

    public function create(ThreeCxInstance $instance, array $payload): array
    {
        $phones = Arr::wrap($payload['phones'] ?? $payload['phone'] ?? null);
        $emails = Arr::wrap($payload['emails'] ?? $payload['email'] ?? null);

        $storeRaw = (bool) config('filament-threecx.crm_connector.store_raw_payload', false);

        $contact = ThreeCxContact::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'name' => (string) ($payload['name'] ?? ''),
            'phones' => array_values(array_filter($phones, fn ($value) => $value !== null && $value !== '')),
            'emails' => array_values(array_filter($emails, fn ($value) => $value !== null && $value !== '')),
            'external_id' => $payload['external_id'] ?? null,
            'crm_url' => $payload['crm_url'] ?? null,
            'raw_payload' => $storeRaw ? $payload : null,
        ]);

        $this->events->dispatchContactCreated($contact);

        return $this->transform($contact);
    }

    protected function limit(): int
    {
        return (int) config('filament-threecx.crm_connector.max_results', 10);
    }

    /**
     * @return array<string, mixed>
     */
    protected function transform(ThreeCxContact $contact): array
    {
        return [
            'id' => $contact->external_id ?: (string) $contact->getKey(),
            'name' => $contact->name,
            'phones' => $contact->phones ?? [],
            'emails' => $contact->emails ?? [],
            'crm_url' => $contact->crm_url,
        ];
    }
}
