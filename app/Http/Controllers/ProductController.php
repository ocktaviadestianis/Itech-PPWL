<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
    * Menampilkan daftar produk
    */
    public function index(): View
    {
        return view('products.index');
    }
    /**
    * Menampilkan form tambah produk
    */
    public function create(): View
    {
        return view('products.create');
    }
    /**
    * Menyimpan produk baru
    */
    public function store(Request $request)
    {

    }
    /**
    * Menampilkan form edit produk
    */
    public function edit(Product $product): View
    {
        return view('products.edit');
    }
    /**
    * Mengupdate produk
    */
    public function update(Request $request, Product $product)
    {

    }
    /**
    * Menghapus produk
    */
    public function destroy(Product $product)
    {

    }
}