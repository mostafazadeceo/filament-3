<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreAccountingCompanyRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateAccountingCompanyRequest;
use Vendor\FilamentAccountingIr\Http\Resources\AccountingCompanyResource;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AccountingCompanyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = AccountingCompany::query()->latest()->paginate();

        return AccountingCompanyResource::collection($companies);
    }

    public function show(AccountingCompany $company): AccountingCompanyResource
    {
        return new AccountingCompanyResource($company);
    }

    public function store(StoreAccountingCompanyRequest $request): AccountingCompanyResource
    {
        $data = $request->validated();
        $company = AccountingCompany::query()->create($data);

        return new AccountingCompanyResource($company);
    }

    public function update(UpdateAccountingCompanyRequest $request, AccountingCompany $company): AccountingCompanyResource
    {
        $company->update($request->validated());

        return new AccountingCompanyResource($company);
    }

    public function destroy(AccountingCompany $company): array
    {
        $company->delete();

        return ['status' => 'ok'];
    }
}
