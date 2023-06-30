@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <h1>ログイン</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <p><label>ユーザー名: <input type="text" name="username"></label></p>
        <p><label>パスワード: <input type="password" name="password"></label></p>
        <button type="submit">ログイン</button>
    </form>
@endsection
