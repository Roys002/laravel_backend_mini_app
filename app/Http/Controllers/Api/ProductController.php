<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Helpers\ApiResponse;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        // 💲 Filter by min price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        // 💲 Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        // ⏱️ Pagination (10 per page)
        $products = $query->paginate(10);

        // return ProductResource::collection($products);
        return ApiResponse::success(
            ProductResource::collection($products)->response()->getData(true),
            'Product list retrieved'
        );
    }

    public function store(StoreProductRequest  $request)
    {
        $product = Product::create($request->validated());
        // return new ProductResource($product);
        return ApiResponse::success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    public function show(Product $product)
    {
        // return new ProductResource($product);
        return ApiResponse::success(
            new ProductResource($product),
            'Product detail'
        );
    }

    public function update(UpdateProductRequest  $request, Product $product)
    {
        $product->update($request->validated());
        // return new ProductResource($product);
        return ApiResponse::success(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();
        // return response()->json(['message' => 'Product deleted']);
        return ApiResponse::success(
            null,
            'Product deleted successfully'
        );
    }
}
