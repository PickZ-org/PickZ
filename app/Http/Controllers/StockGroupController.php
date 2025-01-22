<?php

namespace App\Http\Controllers;

use App\Models\StockGroup;
use App\Models\StockGroupType;
use App\Services\StockService;
use DB;
use Illuminate\Http\Request;

class StockGroupController extends Controller
{
    /**
     * Function for finding stockgroups through ajax post
     * @param Request $request
     * @return array
     */
    public function find(Request $request, StockGroupType $stockgrouptype)
    {
        if ($request->input('search')) {
            $whereArray = [
                ['archive', '=', false],
                ['group_no', 'LIKE', '%' . $request->input('search') . '%'],
            ];
            if ($request->input(['data'])) {
                $whereArray[] = ['stock_group_type_id', '=', (int)$request->input('data')];
            }
            if ($stockgrouptype->exists) {
                $whereArray[] = ['stock_group_type_id', '=', $stockgrouptype->id];
            }
            if ($request->input('extra') && $request->input('extra') !== '') {
                // Extra variable is set, look for groups which only have stock with this product ID
                $productId = (int)$request->input('extra');
                return [
                    'results' => StockGroup::whereHas('stocks.product', static function ($query) use ($productId) {
                        $query->where('id', $productId);
                    })->where($whereArray)
                        ->select(DB::raw("id, group_no, barcode, CONCAT_WS(' - ', group_no, expiry_date) as text"))
                        ->get()
                ];
            }
            return [
                'results' => StockGroup::where($whereArray)
                    ->select(DB::raw("id, group_no, barcode, CONCAT_WS(' - ', group_no, expiry_date) as text"))
                    ->get()
            ];
        }
    }

    /**
     * Function for generating a new stock group through ajax request
     * @param Request $request
     * @param StockGroupType $stockgrouptype
     * @param StockService $stockService
     * @return boolean|\Illuminate\Http\JsonResponse
     */
    public function generate(Request $request, StockGroupType $stockgrouptype, StockService $stockService)
    {
        if ($request->ajax()) {
            $stockGroupNo = $stockService->generateStockGroupNumber($stockgrouptype);
            $stockGroup = StockGroup::create([
                'stock_group_type_id' => $stockgrouptype->id,
                'group_no' => $stockGroupNo,
                'barcode' => $stockGroupNo,
            ]);
            if (null !== $stockGroup) {
                return response()->json([
                    'success' => true,
                    'message' => $stockGroupNo . ' created',
                    'stockgroup' => $stockGroup
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error while creating stock group'
            ]);
        }
        return false;
    }
}
