@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
    <h1>ユーザー登録</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <p><label>ユーザー名: <input type="text" name="username" value="{{ old('username') }}"></label></p>
        <p><label>パスワード: <input type="password" name="password"></label></p>
        <p><label>メールアドレス: <input type="email" name="email" value="{{ old('email') }}"></label></p>
        <button type="submit">登録</button>
    </form>
@endsection
