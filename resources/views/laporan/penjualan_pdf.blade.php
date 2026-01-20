<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">{{ $judul }}</h2>
    <p>Dicetak pada: {{ $tanggal_cetak }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th class="text-right">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $p)
            @php
                $totalPerNota = $p->detilPesan->sum(function($i){ return $i->jumlah * $i->harga; });
            @endphp
            <tr>
                <td>{{ $p->id_pesan }}</td>
                <td>{{ $p->tgl_pesan }}</td>
                <td>{{ $p->pelanggan->nm_pelanggan }}</td>
                <td class="text-right">Rp {{ number_format($totalPerNota, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">GRAND TOTAL</th>
                <th class="text-right">Rp {{ number_format($grand_total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
