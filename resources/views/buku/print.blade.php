<!DOCTYPE html>
<html>
<head>
    <title>Daftar Koleksi Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Daftar Koleksi Buku Perpustakaan</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Buku</th>
                <th width="35%">Judul Buku</th>
                <th width="25%">Pengarang</th>
                <th width="20%">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buku as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->pengarang }}</td>
                <td>{{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
