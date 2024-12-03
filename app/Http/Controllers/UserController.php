<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
        /**
         * @var User
         */
        $user = $request->user();

        // Determine if the request is for password update
        if ($request->has('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed', // Ensure password confirmation
            ]);

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'Password updated successfully',
            ]);
        }

        // Handle profile update
        $request->validate([
            'name' => 'string|nullable',
            'email' => 'email|nullable',
            'phone' => 'string|nullable',
            'address' => 'string|nullable',
            'image' => 'image|nullable',
        ]);

        // Handle image replacement if a new one is uploaded
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('users', 'public');
            $user->image = $imagePath; // Save relative path
        }

        // Update the other profile fields
        $user->update($request->except(['password', 'image']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }


}
