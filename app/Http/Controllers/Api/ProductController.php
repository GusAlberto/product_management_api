<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

#[Group('Products API')]
class ProductController extends Controller
{

    use ApiResponse;

    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_PAGE = 1;

    /**
     * Display a listing of the resource.
     */
    #[QueryParameter('name', description: 'Filter products by name.', type: 'string', example: 'Wireless Mouse')]
    #[QueryParameter('min_price', description: 'Minimum product price.', type: 'number', example: 25.5)]
    #[QueryParameter('max_price', description: 'Maximum product price.', type: 'number', example: 199.99)]
    #[QueryParameter('min_stock', description: 'Minimum available stock.', type: 'integer', example: 5)]
    #[QueryParameter('max_stock', description: 'Maximum available stock.', type: 'integer', example: 100)]
    #[QueryParameter('page', description: 'Page number to retrieve.', type: 'integer', default: 1, example: 2)]
    #[QueryParameter('per_page', description: 'Number of products per page.', type: 'integer', default: 10, example: 25)]
    #[Response(200, 'Products retrieved successfully.')]
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

        $payload = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $perPage, $page) {
            $products = Product::query()
                ->filter($filters)
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'items' => ProductResource::collection($products->items())->resolve(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ];
        });

        return $this->success('Products retrieved successfully.', $payload);
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
    #[Response(201, 'Product created successfully.')]
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
    #[Response(200, 'Product retrieved successfully.')]
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
    #[Response(200, 'Product updated successfully.')]
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
    #[Response(200, 'Product deleted successfully.')]
    public function destroy(Product $product)
    {
        $product->delete();

        Cache::flush();

        return $this->success('Product deleted successfully.');
    }
}
