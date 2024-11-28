@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold text-center mb-4">Register</h1>
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium">Name</label>
                <input type="text" name="name" id="name"
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300" value="{{ old('name') }}" required>
                @error('name')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" id="email"
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300" value="{{ old('email') }}" required>
                @error('email')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" id="password"
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                Register
            </button>
        </form>
    </div>
@endsection
