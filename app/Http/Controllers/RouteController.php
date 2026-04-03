<?php

namespace App\Http\Controllers;

use App\Models\Routes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Routes::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_code' => 'required|string|unique:routes,route_code|max:255',
            'route_description' => 'required|string|max:255',
        ]);

        $route = Routes::create($validated);

        return response()->json($route, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = Routes::findOrFail($id);

        return response()->json($route);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $route = Routes::findOrFail($id);

        $validated = $request->validate([
            'route_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes')->ignore($route->id),
            ],
            'route_description' => 'required|string|max:255',
        ]);

        $route->update($validated);

        return response()->json($route);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $route = Routes::findOrFail($id);
        $route->delete();

        return response()->json(null, 204);
    }
}
