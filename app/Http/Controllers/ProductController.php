<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ProductAccessMiddleware;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct() {
        $this->middleware(ProductAccessMiddleware::class)->only([
            "store",
            "update",
            "destroy",
            "uploadImageLocal",
            "uploadImagePublic",
        ]);
    }
    public function index()
    {
        return response()->json([
            "message" => "Display all products.",
            Product::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|string",
            "price" => "required|decimal:2"
        ]);
        return response()->json([
            "message" => "Product created successfully.",
            Product::create($validatedData)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // return Product::find($id);
        return response()->json([
            "message" => "Display product with ID: {$id}",
            Product::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            "message" => "Product with ID: {$id} updated successfully.",
            Product::find($id)->update($request->all())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        return response()->json([
            "message" => "Product with ID: {$id} deleted successfully.",
            Product::find($id)->delete()
        ]);
    }

    public function uploadImageLocal(Request $request)
    {
        if ($request->hasFile("image")) {
            Storage::disk('local')->put("/", $request->file("image"));
            return response()->json([
                "message" => "Image successfully stored in local disk driver"
            ]);
        } else {
            return response()->json([
                "error" => "Failed to upload or missing file!"
            ]);
        }
    }

    public function uploadImagePublic(Request $request)
    {
        if ($request->hasFile("image")) {
            Storage::disk('public')->put("/", $request->file("image"));
            return response()->json([
                "message" => "Image successfully stored in public disk driver"
            ]);
        } else {
            return response()->json([
                "error" => "Failed to upload or missing file!"
            ]);
        }
    }
}
