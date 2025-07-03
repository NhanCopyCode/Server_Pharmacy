<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Http\Resources\VoucherResource;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        }

        $vouchers = $query->paginate(10);
        return VoucherResource::collection($vouchers);
    }

    public function getListVouchers()
    {
        $vouchers = Voucher::get()->map(function ($voucher) {
            return [
                'value' => $voucher->id,
                'label' => $voucher->code,
            ];
        });

        return response()->json($vouchers);
    }

    public function selectVouchersNotDeleted(Request $request)
    {
        $vouchers = Voucher::whereNull('deleted_at')->get()->map(function ($voucher) {
            return [
                'value' => $voucher->id,
                'label' => $voucher->code,
            ];
        });

        return response()->json($vouchers);
    }

    public function store(StoreVoucherRequest $request)
    {
        $voucher = Voucher::create($request->validated());
        return new VoucherResource($voucher);
    }

    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        return new VoucherResource($voucher);
    }

    public function update(UpdateVoucherRequest $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update($request->validated());
        return new VoucherResource($voucher);
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return response()->json(['message' => 'Đã xóa thành công'], 204);
    }
}
