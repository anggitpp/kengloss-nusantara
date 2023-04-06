<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function detail(int $id)
    {
        $product = Product::findOrFail($id);

        return view('detail', [
            'product' => $product,
        ]);
    }
}
