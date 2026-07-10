<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * List kategori (public), dipakai frontend untuk render filter chip.
     */
    public function index()
    {
        return response()->json(Category::orderBy('name')->get());
    }
}
