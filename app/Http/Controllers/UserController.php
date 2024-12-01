<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'string',
            'email' => 'email',
            'password' => 'string',
            'phone' => 'string',
            'address' => 'string',
            'image' => 'string|image',
        ]);

        /**
         * @var User
         */
        $user = $request->user();

        // Handle image replacement if a new one is uploaded
        if ($request->hasFile('image')) {
            if ($user->image && file_exists(storage_path($user->image))) {
                Storage::disk('public')->delete(storage_path($user->image));
            }

            $image = $request->file('image')->store('products');
            $user->image = asset($image);
        }


        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }
}
