<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Contracts;

interface ProviderClientInterface
{
    /** @return array<string, mixed> */
    public function myCredit(): array;

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendWebservice(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendPeerToPeer(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendPeerToPeerFile(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendPostalCode(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryProvince(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryCounty(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryCity(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryCount(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryGender(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryV2Province(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryV2County(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryV2City(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendCountryV2Count(array $payload): array;
    /** @return array<string, mixed> */
    public function sendJobCategories(): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendJobSubCategory(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendJobCount(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendKeyword(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendKeywordPhonebook(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendPhonebook(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendPattern(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendFile(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendVotp(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function sendUrl(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function calculatePrice(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function cancelScheduled(array $payload): array;

    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function reportOutbox(array $query = []): array;
    /** @return array<string, mixed> */
    public function reportOutboxById(string $id): array;
    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function reportInbox(array $query = []): array;
    /** @return array<string, mixed> */
    public function reportBulkStats(string $bulkId): array;
    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function reportBulkRecipients(string $bulkId, array $query = []): array;

    /** @return array<string, mixed> */
    public function phonebookList(): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function phonebookStore(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function phonebookUpdate(string $phonebookId, array $payload): array;
    /** @return array<string, mixed> */
    public function phonebookDelete(string $phonebookId): array;

    /** @return array<string, mixed> */
    public function optionList(string $phonebookId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function optionStore(string $phonebookId, array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function optionUpdate(string $optionId, array $payload): array;
    /** @return array<string, mixed> */
    public function optionDelete(string $optionId): array;

    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function numberList(string $phonebookId, array $query = []): array;
    /** @return array<string, mixed> */
    public function numberShow(string $numberId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function numberStore(string $phonebookId, array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function numberUpdate(string $numberId, array $payload): array;
    /** @return array<string, mixed> */
    public function numberDelete(string $numberId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function numberImport(string $phonebookId, array $payload): array;
    /** @return array<string, mixed> */
    public function numberSampleImport(): array;
    /** @return array<string, mixed> */
    public function numberExportContacts(string $phonebookId): array;
    /** @return array<string, mixed> */
    public function numberExportMembers(string $phonebookId): array;

    /** @return array<string, mixed> */
    public function patternList(): array;
    /** @return array<string, mixed> */
    public function patternByCode(string $patternCode): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function patternCreate(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function patternUpdate(string $patternCode, array $payload): array;
    /** @return array<string, mixed> */
    public function patternDelete(string $patternCode): array;

    /** @return array<string, mixed> */
    public function draftGroupList(): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function draftGroupCreate(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function draftGroupUpdate(string $groupId, array $payload): array;
    /** @return array<string, mixed> */
    public function draftGroupDelete(string $groupId): array;

    /** @return array<string, mixed> */
    public function draftList(string $groupId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function draftCreate(string $groupId, array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function draftUpdate(string $draftId, array $payload): array;
    /** @return array<string, mixed> */
    public function draftDelete(string $draftId): array;

    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function userList(array $query = []): array;
    /** @return array<string, mixed> */
    public function userShow(string $userId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function userRegister(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function userUpdate(string $userId, array $payload): array;
    /** @return array<string, mixed> */
    public function userTariff(string $userId): array;
    /** @return array<string, mixed> */
    public function userExists(string $mobile): array;
    /** @return array<string, mixed> */
    public function userParentsTree(string $userId): array;

    /** @return array<string, mixed> */
    public function packageList(): array;

    /** @return array<string, mixed> */
    public function numberPoolList(): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function numberAssign(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function numberUnassign(array $payload): array;

    /** @return array<string, mixed> */
    public function ticketList(): array;
    /** @return array<string, mixed> */
    public function ticketShow(string $ticketId): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function ticketCreate(array $payload): array;
    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function ticketReply(string $ticketId, array $payload): array;

    /** @param array<string, mixed> $query @param array<string, mixed>|null $payload @return array<string, mixed> */
    public function request(string $method, string $path, array $query = [], ?array $payload = null): array;
}
