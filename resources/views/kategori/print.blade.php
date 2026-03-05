<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kategori Buku</title>
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
    <h2>Daftar Kategori Buku</h2>
    <table>
        <thead>
            <tr>
                <th width="15%">No</th>
                <th width="35%">ID Kategori</th>
                <th width="50%">Nama Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategori as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->idkategori }}</td>
                <td>{{ $item->nama_kategori }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
