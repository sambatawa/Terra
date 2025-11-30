<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <h2 class="font-semibold text-xl text-gray-800"> Laporan Masalah Saya</h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <button @click="openModal = true" class="mb-6 bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-700 shadow">
                + Buat Laporan Baru
            </button>

            <div class="grid gap-8">
                @foreach($reports as $rpt)
                <div class="bg-white rounded shadow overflow-hidden border-l-4 {{ $rpt->status == 'resolved' ? 'border-green-500' : 'border-yellow-500' }}">
                    
                    <div class="p-4 bg-gray-50 border-b flex justify-between">
                        <h3 class="font-bold text-gray-800">{{ $rpt->title }}</h3>
                        <span class="text-xs px-2 py-1 rounded font-bold {{ $rpt->status == 'resolved' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
                            {{ strtoupper($rpt->status) }}
                        </span>
                    </div>

                    <div class="p-4 bg-gray-100 max-h-60 overflow-y-auto space-y-3">
                        @foreach($rpt->messages as $msg)
                            @php $isMe = $msg->user_id == Auth::id(); @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[85%] {{ $isMe ? 'bg-green-100 text-green-900' : 'bg-white text-gray-800' }} p-3 rounded-lg shadow-sm text-sm">
                                    <p class="font-bold text-xs mb-1 opacity-70">{{ $msg->user->name }}</p>
                                    <p>{{ $msg->message }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                   @if($rpt->status != 'resolved')
                    <div class="p-4 bg-white border-t">
                        <form action="{{ route('chat.kirim', $rpt->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="message" class="w-full border rounded p-2" required>
                            <button class="bg-green-600 text-white px-4 py-2 rounded">Kirim</button>
                        </form>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg w-1/2 p-6 relative animate-bounce-in">
                <h3 class="text-lg font-bold mb-4">Lapor Masalah Baru</h3>
                <form action="{{ route('reports.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Judul</label>
                        <input type="text" name="title" class="w-full border rounded p-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">Keterangan Awal</label>
                        <textarea name="description" rows="3" class="w-full border rounded p-2" required></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openModal = false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Kirim</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>