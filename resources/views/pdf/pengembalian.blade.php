<!DOCTYPE html>
<html>

<head>
    <title>Bukti Pengembalian</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }
    </style>
</head>

<body>
    <h2>Bukti Pengembalian</h2>

    <p><strong>Nama Peminjam:</strong> {{ $record->user->nama }}</p>
    <p><strong>Tanggal Pinjam:</strong> {{ $record->tanggal_pinjam }}</p>
    <p><strong>Tanggal Pengembalian:</strong> {{ $record->tanggal_pengembalian }}</p>
    <p><strong>Status:</strong> {{ $record->status }}</p>

    @if ($record->detailPeminjamanAlat->isNotEmpty())
        <h3>Alat yang Dikembalikan</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Alat</th>
                    <th>Serial Number</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->detailPeminjamanAlat as $detail)
                    <tr>
                        <td>{{ $detail->unitAlat->alat->nama ?? '-' }}</td>
                        <td>{{ $detail->unitAlat->serialNumber->serial_number ?? '-' }}</td>
                        <td>{{ $detail->unitAlat->kondisi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($record->detailPeminjamanPeta->isNotEmpty())
        <h3>Peta yang Dikembalikan</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Peta</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->detailPeminjamanPeta as $detail)
                    <tr>
                        <td>{{ $detail->unitPeta->peta->nama ?? '-' }}</td>
                        <td>{{ $detail->unitPeta->kondisi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($record->bukti_pengembalian)
        <h3>Foto Bukti Pengembalian</h3>
        <img src="{{ public_path('storage/' . $record->bukti_pengembalian) }}" alt="Bukti Pengembalian"
            style="max-width: 100%; height: auto;">
    @endif

</body>

</html>
