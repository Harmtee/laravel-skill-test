<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function index()
    {
        $filePath = storage_path('data/data.json');
        $data = [];
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);
        }
        return view('index', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric'
        ]);

        $data = $request->all(['product_name', 'quantity_in_stock', 'price_per_item']);
        $data['datetime_submitted'] = now()->setTimezone('Africa/Lagos')->format('Y-m-d H:i:s');
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filePath = storage_path('data\data.json');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
            $content = [];
            $content[] = $data;
            file_put_contents($filePath, json_encode($content));
            $data['id'] = 0;
            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Data added successfully'], 200);
        }

        $content = json_decode(file_get_contents($filePath), true);
        $content[] = $data;
        $data['id'] = count($content) - 1;
        file_put_contents($filePath, json_encode($content));
        return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Data added successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $filePath = storage_path('data/data.json');
        $content = json_decode(file_get_contents($filePath), true);
        $data = $request->all(['product_name', 'quantity_in_stock', 'price_per_item']);
        $d = $content[$id];
        $content[$id] = array_merge($d, $data);
        file_put_contents($filePath, json_encode($content));
        $data['id'] = $id;

        return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Data updated successfully'], 200);
    }

    public function destroy($id)
    {
        $filePath = storage_path('data/data.json');
        $data = json_decode(file_get_contents($filePath), true);
        array_splice($data, $id, 1);
        file_put_contents($filePath, json_encode($data));
        return response()->json(['status' => 'success', 'message' => 'Data deleted successfully'], 200);
    }
}
