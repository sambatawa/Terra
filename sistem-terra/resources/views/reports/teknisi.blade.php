<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">🛠️ Dashboard Tiket Masalah</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="grid gap-8">
            @foreach($reports as $rpt)
            <div class="bg-white rounded shadow overflow-hidden border-l-4 {{ $rpt->status == 'resolved' ? 'border-green-500' : 'border-red-500' }}">
                
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">#{{ $rpt->id }} - {{ $rpt->title }}</h3>
                        <p class="text-xs text-gray-500">Pelapor: {{ $rpt->user->name }} • {{ $rpt->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        @if($rpt->status == 'resolved')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">✅ SELESAI</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">⏳ PROSES</span>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-gray-100 max-h-60 overflow-y-auto space-y-3">
                    @foreach($rpt->messages as $msg)
                        @php
                            $isMe = $msg->user_id == Auth::id(); // Cek apakah ini chat saya (Teknisi)
                            $isPetani = $msg->user->role == 'petani';
                        @endphp
                        
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $isMe ? 'bg-blue-100 text-blue-900' : 'bg-white text-gray-800' }} p-3 rounded-lg shadow-sm text-sm">
                                <p class="font-bold text-xs mb-1 {{ $isMe ? 'text-blue-600' : 'text-green-600' }}">
                                    {{ $msg->user->name }} ({{ ucfirst($msg->user->role) }})
                                </p>
                                <p>{{ $msg->message }}</p>
                                <p class="text-[10px] text-right mt-1 opacity-50">{{ $msg->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($rpt->status != 'resolved')
                <div class="p-4 bg-white border-t">
                    <div class="flex gap-2 items-start">
                        
                        <form action="{{ route('chat.kirim', $rpt->id) }}" method="POST" class="flex-1 flex gap-2">
                            @csrf
                            <input type="text" name="message" class="w-full border rounded p-2" required>
                            <button class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 text-sm font-bold"><i class="fas fa-paper-plane mr-2"></i>Kirim</button>
                        </form>

                        <form action="{{ route('chat.selesai', $rpt->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin masalah ini sudah selesai? Tiket akan ditutup.');">
                            @csrf 
                            @method('PUT') 
                            <button class="bg-green-600 text-white px-6 py-2.5  rounded hover:bg-green-700 text-sm font-bold">Selesai</button>
                        </form>

                    </div>
                </div>
                @else
                    <div class="p-2 text-center bg-gray-50 text-xs text-gray-400">Tiket ditutup.</div>
                @endif

            </div>
            @endforeach
        </div>

    </div></div>
</x-app-layout>