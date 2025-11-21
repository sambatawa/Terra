<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Marketplace Pertanian') }}
            @if(Auth::user()->role == 'penjual')
                <span class="bg-blue-100 text-blue-800 text-xs font-medium ml-2 px-2.5 py-0.5 rounded">Mode Penjual</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 animate-pulse">
                    {{ session('success') }}
                </div>
            @endif

            @if(Auth::user()->role == 'penjual')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 border-l-4 border-blue-500">
                <h3 class="font-bold text-lg mb-4">➕ Jual Produk Baru</h3>
                <form action="{{ route('marketplace.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600">Nama Produk</label>
                            <input type="text" name="name" required class="w-full border-gray-300 rounded mt-1">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600">Harga (Rp)</label>
                                <input type="number" name="price" required class="w-full border-gray-300 rounded mt-1">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600">Stok Awal</label>
                                <input type="number" name="stock" value="10" required class="w-full border-gray-300 rounded mt-1 bg-yellow-50">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">No. WA (628xxx)</label>
                            <input type="text" name="whatsapp_number" placeholder="62812345678" required class="w-full border-gray-300 rounded mt-1">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Foto Produk</label>
                            <input type="file" name="image" required class="w-full border-gray-300 rounded mt-1">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-600">Deskripsi</label>
                            <textarea name="description" class="w-full border-gray-300 rounded mt-1"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Jual Sekarang</button>
                </form>
            </div>
            @endif

            <h3 class="font-bold text-xl mb-4 text-gray-700">Etalase Produk</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg relative transition hover:shadow-xl {{ $product->stock == 0 ? 'opacity-75 grayscale' : '' }}">
                    
                    <div class="absolute top-2 right-2 z-10">
                        @if($product->stock == 0)
                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">❌ SOLD OUT</span>
                        @elseif($product->stock < 5)
                            <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded animate-bounce flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 shrink-0">
                                    <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177 7.547 7.547 0 01-1.705-1.715.75.75 0 00-1.152-.082A9 9 0 004 15.662C4 20.16 7.582 23.75 12 23.75s8-3.59 8-8.088a9 9 0 00-3.356-7.152.75.75 0 00-.956 1.112c.257.345.477.716.654 1.11A7.5 7.5 0 0115 15.662a3 3 0 01-3-3 3 3 0 013-3c.62 0 1.208.13 1.745.366.36.16.781-.007.968-.354.707-1.314 1.118-2.733 1.218-4.161a.75.75 0 00-1.071-.797 9.787 9.787 0 01-4.897 1.188 9.756 9.756 0 01-3.128-.523c-.37-.124-.751-.242-1.132-.343z" clip-rule="evenodd" />
                                </svg>
                                Sisa {{ $product->stock }}!
                            </span>
                        @else
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">Stok: {{ $product->stock }}</span>
                        @endif
                    </div>

                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-48 object-cover">
                    
                    <div class="p-6">
                        <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                        <p class="text-green-600 font-bold text-xl mt-2">Rp {{ number_format($product->price) }}</p>
                        
                        @if(Auth::user()->role == 'penjual' && Auth::id() == $product->user_id)
                            <div class="mt-4 bg-gray-100 p-3 rounded border border-gray-200">
                                
                                <div class="flex items-center justify-between mb-3 border-b border-gray-300 pb-2">
                                    <span class="text-xs font-bold text-gray-500">Atur Stok:</span>
                                    <div class="flex gap-2">
                                        <form action="{{ route('marketplace.stock', $product->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="minus">
                                            <button class="bg-red-200 hover:bg-red-300 w-6 h-6 rounded text-red-700 font-bold text-xs">-</button>
                                        </form>
                                        <span class="font-bold text-sm pt-0.5">{{ $product->stock }}</span>
                                        <form action="{{ route('marketplace.stock', $product->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="plus">
                                            <button class="bg-green-200 hover:bg-green-300 w-6 h-6 rounded text-green-700 font-bold text-xs">+</button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('marketplace.edit', $product->id) }}" class="flex-1 bg-yellow-500 text-white text-xs font-bold py-2 rounded hover:bg-yellow-600 flex items-center justify-center gap-2 shadow-sm transition duration-200 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 group-hover:scale-110 transition-transform">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Edit
                                    </a>

                                    <form action="{{ route('marketplace.destroy', $product->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button class="w-full bg-red-600 text-white text-xs font-bold py-2 rounded hover:bg-red-700 flex items-center justify-center gap-2 shadow-sm transition duration-200 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 group-hover:scale-110 transition-transform">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>

                        @else
                            @if($product->stock > 0)
                                @php
                                    $pesan = "Halo, saya mau beli {$product->name}. Ready?";
                                    $linkWA = "https://wa.me/{$product->whatsapp_number}?text=" . urlencode($pesan);
                                @endphp
                                <a href="{{ $linkWA }}" target="_blank" 
                                        onclick="catatKlik('{{ $product->user_id }}', '{{ $product->name }}')"
                                        class="mt-4 block text-center w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 shadow cursor-pointer flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    Beli via WhatsApp
                                </a>
                            @else
                                <button disabled class="mt-4 w-full bg-gray-300 text-gray-500 py-2 rounded cursor-not-allowed font-bold">
                                    Stok Habis
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function catatKlik(sellerId, productName) {
        // Kirim data ke server diam-diam (AJAX)
        fetch("{{ route('history.track_click') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                seller_id: sellerId,
                product_name: productName
            })
        });
    }
</script>