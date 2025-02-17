@extends('expense::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('expense.name') !!}</p>
@endsection
