<!DOCTYPE html>
<html>

<head>
    <title>Detail Peminjaman</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Detail Peminjaman</h2>
    <p><strong>Tanggal Pinjam:</strong> {{ $record->tanggal_pinjam }}</p>
    <p><strong>Tanggal Pengembalian:</strong> {{ $record->tanggal_pengembalian }}</p>
    <p><strong>Status:</strong> {{ $record->status }}</p>

    @if ($record->detailPeminjamanAlat->isNotEmpty())
        <h3>Unit Alat</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Alat</th>
                    <th>Serial Number</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->detailPeminjamanAlat as $detail)
                    <tr>
                        <td>{{ $detail->unitAlat->alat->nama ?? '-' }}</td>
                        <td>{{ $detail->unitAlat->serialNumber->serial_number ?? '-' }}</td>
                        <td>{{ $detail->unitAlat->kondisi ?? '-' }}</td>
                        <td>{{ $detail->unitAlat->lokasi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($record->detailPeminjamanPeta->isNotEmpty())
        <h3>Unit Peta</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Peta</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->detailPeminjamanPeta as $detail)
                    <tr>
                        <td>{{ $detail->unitPeta->peta->nama ?? '-' }}</td>
                        <td>{{ $detail->unitPeta->kondisi ?? '-' }}</td>
                        <td>{{ $detail->unitPeta->lokasi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
