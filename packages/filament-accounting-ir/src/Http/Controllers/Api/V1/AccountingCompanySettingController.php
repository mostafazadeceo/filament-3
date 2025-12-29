<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreAccountingCompanySettingRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateAccountingCompanySettingRequest;
use Vendor\FilamentAccountingIr\Http\Resources\AccountingCompanySettingResource;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;

class AccountingCompanySettingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = AccountingCompanySetting::query()->latest();

        if ($companyId = $request->integer('company_id')) {
            $query->where('company_id', $companyId);
        }

        return AccountingCompanySettingResource::collection($query->paginate());
    }

    public function show(AccountingCompanySetting $company_setting): AccountingCompanySettingResource
    {
        return new AccountingCompanySettingResource($company_setting);
    }

    public function store(StoreAccountingCompanySettingRequest $request): AccountingCompanySettingResource
    {
        $setting = AccountingCompanySetting::query()->create($request->validated());

        return new AccountingCompanySettingResource($setting);
    }

    public function update(
        UpdateAccountingCompanySettingRequest $request,
        AccountingCompanySetting $company_setting
    ): AccountingCompanySettingResource {
        $data = $request->validated();

        if (array_key_exists('posting_accounts', $data)) {
            $data['posting_accounts'] = array_merge($company_setting->posting_accounts ?? [], $data['posting_accounts']);
        }

        $company_setting->update($data);

        return new AccountingCompanySettingResource($company_setting);
    }

    public function destroy(AccountingCompanySetting $company_setting): array
    {
        $company_setting->delete();

        return ['status' => 'ok'];
    }
}
