<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{

    use ApiResponse;

    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_PAGE = 1;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name',
            'min_price',
            'max_price',
            'min_stock',
            'max_stock',
        ]);

        $perPage = max(1, (int) $request->integer('per_page', self::DEFAULT_PER_PAGE));
        $page = max(1, (int) $request->integer('page', self::DEFAULT_PAGE));
        $cacheKey = $this->buildIndexCacheKey($filters, $page, $perPage);

        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $perPage, $page) {
            return Product::query()
                ->filter($filters)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], 'page', $page);
        });

        return $this->success('Products retrieved successfully.', [
            'items' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    private function buildIndexCacheKey(array $filters, int $page, int $perPage): string
    {
        ksort($filters);

        return 'products:index:' . md5(json_encode([
            'filters' => $filters,
            'page' => $page,
            'per_page' => $perPage,
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        Cache::flush();

        return $this->success(
            'Product created successfully.',
            new ProductResource($product),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->success(
            'Product retrieved successfully.',
            new ProductResource($product)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        Cache::flush();

        return $this->success(
            'Product updated successfully.',
            new ProductResource($product->fresh())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        Cache::flush();

        return $this->success('Product deleted successfully.');
    }
}
