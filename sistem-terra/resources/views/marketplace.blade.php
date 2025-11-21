<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Marketplace Pertanian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <input type="text" placeholder="Cari pupuk, bibit, atau obat..." class="w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <img src="https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?w=500" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg">Pupuk Organik Super</h3>
                        <p class="text-green-600 font-bold text-xl mt-2">Rp 45.000</p>
                        <p class="text-gray-500 text-sm mt-2">Menyuburkan tanah dan mempercepat pertumbuhan daun terung.</p>
                        <button onclick="alert('Fitur checkout akan segera hadir!')" class="mt-4 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Beli Sekarang</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <img src="https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=500" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg">Bibit Terung Unggul F1</h3>
                        <p class="text-green-600 font-bold text-xl mt-2">Rp 25.000</p>
                        <p class="text-gray-500 text-sm mt-2">Tahan hama dan penyakit, potensi panen 10 ton/ha.</p>
                        <button onclick="alert('Fitur checkout akan segera hadir!')" class="mt-4 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Beli Sekarang</button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <img src="https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=500" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-lg">Pestisida Nabati</h3>
                        <p class="text-green-600 font-bold text-xl mt-2">Rp 60.000</p>
                        <p class="text-gray-500 text-sm mt-2">Aman untuk lingkungan, efektif membasmi kutu daun.</p>
                        <button onclick="alert('Fitur checkout akan segera hadir!')" class="mt-4 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Beli Sekarang</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>