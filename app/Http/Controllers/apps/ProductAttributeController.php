<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();
        return view('content.apps.ecommerce.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,color,boolean',
        ]);

        $code = Str::slug($request->input('code') ?: $request->name, '_');

        ProductAttribute::create([
            'name'          => $request->name,
            'code'          => $code,
            'type'          => $request->type,
            'is_filterable' => $request->boolean('is_filterable', true),
            'is_required'   => $request->boolean('is_required', false),
            'sort_order'    => (int)$request->input('sort_order', 0),
        ]);

        return back()->with('success', "Attribute '{$request->name}' created successfully.");
    }

    public function storeValue(Request $request, $attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);

        $request->validate([
            'value'      => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
        ]);

        AttributeValue::create([
            'product_attribute_id' => $attribute->id,
            'value'                => $request->value,
            'color_code'           => $request->color_code,
            'sort_order'           => (int)$request->input('sort_order', 0),
        ]);

        return back()->with('success', "Value added to '{$attribute->name}'.");
    }

    public function destroy($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();

        return back()->with('success', 'Attribute deleted.');
    }
}
