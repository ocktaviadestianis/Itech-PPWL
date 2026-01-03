<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class CheckoutController extends Controller
{
// Menampilkan halaman checkout
    public function index()
    {
        $cart = Session::get('cart', []);
        $user = Auth::user();
        return view('user.checkout', compact('cart', 'user'));
    }
// Proses checkout
    public function process(Request $request)
    {
        
    $cart = Session::get('cart', []);
    if (!$cart || count($cart) === 0) {
        return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
    }
    
    // return 1;
    // $validated = $request->validate([
    //     'nama' => 'required|string|max:255',
    //     'alamat' => 'required|string',
    //     'telepon' => 'required|string|max:20',
    //     'metode' => 'required|string',
    // ]);
    
    DB::beginTransaction();
    try {

        $total = collect($cart)->sum(fn ($item) => $item['harga'] * $item['quantity']);
        $orderId = 'ORD-' . time() . '-' . rand(1000, 9999);
        // return $request->all();
       
        
        $order = Order::create([
            'id' => $orderId,
            'user_id' => Auth::id(),
            'tanggal' => date('Y-m-d'),
            'total' => $total,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'metode' => $request->metode,
            'status_pembayaran' => 'pending',
        ]);

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product || $product->stok < $item['quantity']) {
                throw new \Exception("Stok produk {$product->nama} tidak mencukupi.");
            }

            $order->products()->attach($productId, [
                'jumlah' => $item['quantity'],
                'harga_satuan' => $item['harga'],
            ]);

            $product->decrement('stok', $item['quantity']);
        }

        Session::forget('cart');
        DB::commit();

        return redirect()->route('checkout.sukses')
            ->with('success', 'Pesanan berhasil diproses! Terima kasih sudah berbelanja.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    public function sukses()
    {
        $order = Order::where('user_id', Auth::id())->latest()->first();
        return view('user.sukses', compact('order'));
    }
    public function updatePaymentProof(Request $request, Order $order)
    {
    if ($order->user_id !== Auth::id()) {
        abort(403);
    }
    $request->validate([
        'bukti_pembayaran' => 'required|file|mimes:jpg,png,pdf|max:2048',
    ]);
    if ($request->hasFile('bukti_pembayaran')) {
        if ($order->bukti_pembayaran && Storage::disk('public')->exists('payment/' . $order->bukti_pembayaran)) {
        Storage::disk('public')->delete('payment/' . $order->bukti_pembayaran);
        }
        $file = $request->file('bukti_pembayaran');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('payment', $filename, 'public');
        $order->bukti_pembayaran = $filename;
        $order->status_pembayaran = 'lunas';
        $order->save();
    }
    return redirect()->route('orders.history')->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}
