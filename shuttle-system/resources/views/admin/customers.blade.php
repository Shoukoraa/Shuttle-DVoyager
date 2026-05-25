@extends('admin.layout')

@section('content')
    <h1>Data Pelanggan (Customer)</h1>

    <p>Admin hanya dapat melihat dan menghapus pelanggan yang melanggar aturan.</p>

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Nama User</th>
            <th>Email</th>
            <th>Nomor HP</th>
            <th>Aksi</th>
        </tr>
        @foreach($customers as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->user->name ?? 'User Terhapus' }}</td>
            <td>{{ $c->user->email ?? '-' }}</td>
            <td>{{ $c->user->phone ?? '-' }}</td>
            <td>
                <a href="/admin/customers/{{ $c->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/customers/{{ $c->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus pelanggan ini secara permanen?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
