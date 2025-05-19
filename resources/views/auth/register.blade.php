@extends('layouts.app')

@section('content')
<div class="flex justify-center">
    <div class="w-full sm:max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-center text-2xl font-bold mb-6 text-gray-800">Cadastro</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
                <input id="name" type="text" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input id="email" type="email" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <input id="password" type="password" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Senha</label>
                <input id="password-confirm" type="password" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Cadastrar
                </button>
                <a href="{{ route('login') }}" class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                    Já tenho conta
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
