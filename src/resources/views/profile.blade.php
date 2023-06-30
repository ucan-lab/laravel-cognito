@extends('layouts.app')

@section('title', 'プロフィール')

@section('content')
    <h1>プロフィール</h1>

    <ul>
        <li>ユーザー名: {{ $username }}</li>
        <li>メールアドレス: {{ $email }}</li>
    </ul>

    <p><a href="{{ route('dashboard') }}">ダッシュボードへ戻る</a></p>
@endsection
