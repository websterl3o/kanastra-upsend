@extends('layouts.app')
@section('title', 'Lista de cobrança')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <Upload-collection-list upload-url="{{ route('collection-lists.store') }}" />
@endsection
