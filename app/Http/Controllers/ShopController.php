<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Shop::with('route')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_code' => 'required|string|unique:shops,shop_code|max:255',
            'shop_name' => 'required|string|max:255',
            'Address' => 'nullable|string|max:255',
            'phoneno' => 'nullable|string|max:255',
            'route_code' => 'required|exists:routes,route_code',
        ]);

        $shop = Shop::create($validated);

        return response()->json($shop, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::with('route')->findOrFail($id);
        return response()->json($shop);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'shop_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops')->ignore($shop->id),
            ],
            'shop_name' => 'required|string|max:255',
            'Address' => 'nullable|string|max:255',
            'phoneno' => 'nullable|string|max:255',
            'route_code' => 'required|exists:routes,route_code',
        ]);

        $shop->update($validated);

        return response()->json($shop);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();

        return response()->json(null, 204);
    }
}
