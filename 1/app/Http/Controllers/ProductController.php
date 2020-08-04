<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function admin()
    {
        $products = Product::all();
        return view('product.admin')->with(['products' => $products]);
    }
    public function create()
    {
        return view('product.create');
    }
    public function postCreate(ProductRequest $request)
    {
        // nhận tất cả tham số vào mảng product
        $product = $request->all();
        // xử lý upload hình vào thư mục
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            if ($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg') {
                return redirect('product/create')->with('loi', 'Bạn chỉ được chọn file có đuôi jpg,png,jpeg');
            }
            $imageName = $file->getClientOriginalName();
            $file->move("images", $imageName);
        } else {
            $imageName = null;
        }
        $p = new Product($product);
        $p->image = $imageName;
        $p->save();

        return redirect()->action('ProductController@admin');
    }
    public function update($id)
    {
        $p = Product::find($id);
        return view('product.update', ['p' => $p]);
    }
    public function postUpdate(Request $request, $id)
    {
        $product = $request->all();
        // xử lý upload hình vào thư mục
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            if ($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg') {
                return redirect('product/update')->with('loi', 'Bạn chỉ được chọn file có đuôi jpg,png,jpeg');
            }
            $imageName = $file->getClientOriginalName();
            $file->move("public/images", $imageName);
        } else { // không upload hình mới => giữ lại hình cũ
            $p = Product::find($id);
            $imageName = $p->image;
        }
        $p = new Product($product);
        $p->image = $imageName;
        DB::update('UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?', [$p->getAttribute('name'), $p->getAttribute('price'), $p->getAttribute('description'), $imageName, $id]);

        return redirect()->action('ProductController@admin');
    }
    public function delete($id)
    {
        $p = Product::find($id);
        $p->delete();
        return redirect()->action('ProductController@admin');
    }
}
