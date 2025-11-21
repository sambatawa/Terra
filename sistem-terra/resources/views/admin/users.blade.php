<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">👥 Manajemen User</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <table class="w-full text-left">
                <thead><tr class="bg-gray-100"><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Role</th><th class="p-3">Aksi</th></tr></thead>
                <tbody>
                    @foreach($users as $u)
                    <tr class="border-b">
                        <td class="p-3">{{ $u->name }}</td>
                        <td class="p-3">{{ $u->email }}</td>
                        <td class="p-3 uppercase font-bold text-xs">{{ $u->role }}</td>
                        <td class="p-3">
                            <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div></div>
</x-app-layout>