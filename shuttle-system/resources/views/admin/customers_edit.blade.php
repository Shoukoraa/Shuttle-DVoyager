@extends('admin.layout')

@section('content')
    <h1>Edit Pelanggan</h1>

    <form action="/admin/customers/{{ $customer->id }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" placeholder="Nama Lengkap" value="{{ $customer->user->name ?? '' }}" required>
        <input type="email" name="email" placeholder="Email" value="{{ $customer->user->email ?? '' }}" required>
        <input type="text" name="phone" placeholder="Nomor HP" value="{{ $customer->user->phone ?? '' }}">
        <input type="password" name="password" placeholder="Password (Kosongkan jika tidak ingin mengubah)" minlength="6">
        <button type="submit">Update</button>
        <a href="/admin/customers"><button type="button">Batal</button></a>
    </form>
@endsection
