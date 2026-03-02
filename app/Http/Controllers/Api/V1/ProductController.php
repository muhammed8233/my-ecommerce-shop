<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\InventoryMovementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected InventoryMovementService $inventoryMovementService
    ) {}

    /**
     * GET /api/v1/products
     * Equivalent to listProducts()
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $perPage = $request->query('size', 10);
        $sortBy = $request->query('sort', 'name'); // Default to productName equivalent
        $direction = $request->query('direction', 'asc');

        $products = $this->productService->getProducts($search, $perPage, $sortBy, $direction);

        return response()->json($products);
    }

    /**
     * POST /api/v1/products/add
     * Equivalent to createProduct()
     */
    public function store(Request $request): JsonResponse
    {
        // Equivalent to @Valid @RequestBody
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
        ]);

        $product = $this->productService->createProduct($validated);
        
        return response()->json($product, 201);
    }

    /**
     * PUT /api/v1/products/{productId}
     * Equivalent to updateProduct()
     */
    public function update(string $productId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
        ]);

        $updatedProduct = $this->productService->updateProduct($productId, $validated);
        
        return response()->json($updatedProduct);
    }

    /**
     * PUT /api/v1/products/{productId}/restock
     * Equivalent to restockProduct()
     */
    public function restock(string $productId, Request $request): JsonResponse
    {
        $quantity = (int) $request->query('quantity', 0);
        
        $this->inventoryMovementService->restockProduct($productId, $quantity);
        
        return response()->json(['message' => 'product restock successfully']);
    }
}
