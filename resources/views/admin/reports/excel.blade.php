<table border="1">
    <tr>
        <th colspan="5" style="font-size: 14pt; font-weight: bold; text-align: center;">LAPORAN HASIL UJIAN - MTS AL HUDA PAMEGATAN</th>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td><b>Mata Pelajaran</b></td>
        <td colspan="4">: {{ $exam->subject->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><b>Guru Pengampu</b></td>
        <td colspan="4">: {{ $exam->guru->user->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><b>Kelas</b></td>
        <td colspan="4">: {{ $exam->kelas->nama_kelas ?? '-' }}</td>
    </tr>
    <tr>
        <td><b>Judul Ujian</b></td>
        <td colspan="4">: {{ $exam->title }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr style="background-color: #f8f9fa;">
        <th><b>NO</b></th>
        <th><b>NAMA SISWA</b></th>
        <th><b>WAKTU MULAI</b></th>
        <th><b>WAKTU SELESAI</b></th>
        <th><b>NILAI AKHIR</b></th>
    </tr>
    @foreach($sessions as $index => $session)
        <tr>
            <td align="center">{{ $index + 1 }}</td>
            <td>{{ $session->user->name ?? 'Nama Siswa' }}</td>
            <td align="center">{{ \Carbon\Carbon::parse($session->started_at)->format('d/m/Y H:i') }}</td>
            <td align="center">{{ \Carbon\Carbon::parse($session->completed_at)->format('d/m/Y H:i') }}</td>
            <td align="center"><b>{{ $session->score }}</b></td>
        </tr>
    @endforeach
</table>