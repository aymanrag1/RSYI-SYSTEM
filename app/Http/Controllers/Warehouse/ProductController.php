<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Product;
use App\Models\Warehouse\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out' => $query->outOfStock(),
                'low' => $query->lowStock()->where('current_qty', '>', 0),
                'ok'  => $query->where('current_qty', '>', 0)->whereColumn('current_qty', '>', 'min_qty'),
            };
        }
        if ($request->filled('s')) {
            $s = $request->s;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"));
        }

        $products   = $query->orderBy('name')->paginate(25);
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total'     => Product::count(),
            'active'    => Product::where('status', 'active')->count(),
            'low_stock' => Product::lowStock()->count(),
            'out'       => Product::outOfStock()->count(),
        ];

        return view('warehouse.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('warehouse.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:wp_rsyi_wh_products,code'],
            'name'        => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer'],
            'unit'        => ['required', 'string', 'max:20'],
            'min_qty'     => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        Product::create($validated + ['current_qty' => 0, 'status' => 'active', 'created_at' => now()]);

        return redirect()->route('warehouse.products.index')
            ->with('success', 'تم إضافة الصنف بنجاح.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('warehouse.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer'],
            'unit'        => ['required', 'string', 'max:20'],
            'min_qty'     => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        $product->update($validated + ['updated_at' => now()]);

        return redirect()->route('warehouse.products.index')
            ->with('success', 'تم تحديث الصنف.');
    }
}
