<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($role) }} - Terra</title>
    <style>
        @page {
            size: A4;
            margin: 25mm 20mm 25mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #6D28D9;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }
        .header h1 {
            color: #6D28D9;
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0;
            color: #555;
            font-size: 11px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
            font-size: 10px;
            page-break-inside: auto;
        }
        
        .table th {
            background-color: #6D28D9;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #6D28D9;
            font-size: 9px;
            white-space: nowrap;
        }
        
        .table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9px;
            word-wrap: break-word;
            max-width: 100px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .status-aman {
            color: #155724;
            font-weight: bold;
        }
        
        .status-warning {
            color: #721c24;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            page-break-inside: avoid;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 30px;
            font-size: 11px;
        }
        
        .table th:nth-child(1), .table td:nth-child(1) { width: 12%; } 
        .table th:nth-child(2), .table td:nth-child(2) { width: 15%; } 
        .table th:nth-child(3), .table td:nth-child(3) { width: 8%; }  
        .table th:nth-child(4), .table td:nth-child(4) { width: 10%; } 
        .table th:nth-child(5), .table td:nth-child(5) { width: 8%; }  
        .table th:nth-child(6), .table td:nth-child(6) { width: 8%; }  
        .table th:nth-child(7), .table td:nth-child(7) { width: 8%; }
        .table th:nth-child(8), .table td:nth-child(8) { width: 15%; } 
        .table th:nth-child(9), .table td:nth-child(9) { width: 16%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan {{ ucfirst($role) }} - Terra</h1>
        <p>Nama: {{ $user->name }} | Email: {{ $user->email }}</p>
        <p>Tanggal: {{ date('d F Y') }} | Total Data: {{ count($data) }}</p>
    </div>

    @if(count($data) > 0)
        @if($role == 'petani' || $role == 'teknisi')
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Hasil Deteksi</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th>Suhu</th>
                        <th>Kelembapan</th>
                        <th>Cahaya</th>
                        <th>Ciri Penyakit</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row['waktu'] }}</td>
                            <td>{{ $row['detection'] }}</td>
                            <td>{{ $row['confidence'] }}</td>
                            <td><span class="{{ $row['status'] == 'Aman' ? 'status-aman' : 'status-warning' }}">{{ $row['status'] }}</span></td>
                            <td>{{ $row['suhu'] }}</td>
                            <td>{{ $row['kelembapan'] }}</td>
                            <td>{{ $row['cahaya'] }}</td>
                            <td>{{ $row['ciri'] }}</td>
                            <td>{{ $row['rekomendasi'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($role == 'penjual')
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Nama Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row['waktu'] }}</td>
                            <td><strong>{{ $row['product'] }}</strong></td>
                            <td>{{ $row['action'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($role == 'penyuluh')
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Isi Konten</th>
                        <th>Jumlah Like</th>
                        <th>Jumlah Komentar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row['waktu'] }}</td>
                            <td>{{ $row['content'] }}</td>
                            <td>{{ $row['likes'] }}</td>
                            <td>{{ $row['comments'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div class="no-data">
            <p>Tidak ada data yang tersedia untuk periode ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis oleh sistem Terra pada {{ date('d F Y H:i:s') }}</p>
        <p>© 2025 Terra - Sistem Deteksi Penyakit Daun Terung Ungu</p>
    </div>
</body>
</html>