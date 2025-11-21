<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ Edit Produk
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg">Form Edit Data</h3>
                    <a href="{{ route('marketplace') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Kembali</a>
                </div>

                <form action="{{ route('marketplace.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 font-bold">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border-gray-300 rounded mt-1 focus:ring-yellow-500 focus:border-yellow-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 font-bold">Harga (Rp)</label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="w-full border-gray-300 rounded mt-1 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 font-bold">Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required class="w-full border-gray-300 rounded mt-1 bg-yellow-50 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 font-bold">No. WA (628xxx)</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $product->whatsapp_number) }}" required class="w-full border-gray-300 rounded mt-1 focus:ring-yellow-500 focus:border-yellow-500">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 font-bold">Ganti Foto (Opsional)</label>
                            <input type="file" name="image" class="w-full border-gray-300 rounded mt-1 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti foto.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-600 font-bold">Deskripsi</label>
                            <textarea name="description" rows="4" class="w-full border-gray-300 rounded mt-1 focus:ring-yellow-500 focus:border-yellow-500">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600 font-bold shadow">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('marketplace') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 font-bold">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>