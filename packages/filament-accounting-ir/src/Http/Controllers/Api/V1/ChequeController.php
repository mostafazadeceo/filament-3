<?php

namespace Vendor\FilamentAccountingIr\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentAccountingIr\Http\Controllers\Controller;
use Vendor\FilamentAccountingIr\Http\Requests\StoreChequeRequest;
use Vendor\FilamentAccountingIr\Http\Requests\UpdateChequeRequest;
use Vendor\FilamentAccountingIr\Http\Resources\ChequeResource;
use Vendor\FilamentAccountingIr\Models\Cheque;

class ChequeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = Cheque::query()->latest()->paginate();

        return ChequeResource::collection($items);
    }

    public function show(Cheque $cheque): ChequeResource
    {
        return new ChequeResource($cheque);
    }

    public function store(StoreChequeRequest $request): ChequeResource
    {
        $item = Cheque::query()->create($request->validated());

        return new ChequeResource($item);
    }

    public function update(UpdateChequeRequest $request, Cheque $cheque): ChequeResource
    {
        $cheque->update($request->validated());

        return new ChequeResource($cheque);
    }

    public function destroy(Cheque $cheque): array
    {
        $cheque->delete();

        return ['status' => 'ok'];
    }
}
