<?php

namespace App\Http\Controllers\api;

use App\Helpers\MyTokenManager;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|unique:users|max:255',
    //         'password' => 'required|string|min:6',
    //         'confirm_password' => 'required|string|same:password', // Ensure confirm_password matches password

    //     ]);

    //     $user = new User([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $user->save();
    //     $lastInsertedUserId = $user->id;
    //     $tokens = MyTokenManager::CreateToken($lastInsertedUserId);
    //     return [
    //         'message' => 'user created successfully',
    //         'token' => $tokens,
    //     ];
    // }
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users|max:255',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:password', // Ensure confirm_password matches password
            ]);
        } catch (ValidationException $e) {
            // Handle validation errors here
            // For example, you could log the error or return a custom response
            // In this example, let's return a custom response with a 422 status code
            return response()->json([
                'message' => 'Validation failed', "statusCode" => 422
            ], 422);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();
        $lastInsertedUserId = $user->id;
        $tokens = MyTokenManager::CreateToken($lastInsertedUserId);

        return [
            'message' => 'User created successfully',
            'token' => $tokens,
        ];
    }
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        // Retrieve the user from the database by email
        $user = DB::table('users')->where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            // Password matches, generate token and return success response
            $token = MyTokenManager::createToken($user->id);
            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $token,
            ]);
        } else {
            // Either user not found or password doesn't match
            return response()->json([
                'error' => 'Wrong email or password',
            ], 401);
        }
    }
    public function profile(Request $request)
    {
        $user = MyTokenManager::currentUser($request);
        return [
            "message"=>'User profile returned successfully',
            "statusCode" => 200,
            'user' => $user
        ];
    }
    public function logout(Request $request)
    {
        MyTokenManager::removeUserTokens($request);
        return [
            'message' => 'logged out successfully',
        ];
    }
    public function uploadUserPhoto(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // Retrieve the uploaded photo from the request
        if ($request->hasFile('photo')) {
            $uploadedPhoto = $request->file('photo');
            // Generate a unique name for the image
            $imageName = time() . '.' . $uploadedPhoto->getClientOriginalExtension();
            // Move the uploaded photo to the desired location
            $uploadedPhoto->move(public_path('application/users/image'), $imageName);
            // Save the photo path to the user's record in the database
            $user = MyTokenManager::currentUser($request);
            $userPath = 'application/users/image/' . $imageName;
            DB::update('update users set photo =? where id=?', [$userPath, $user->id]);
            // $user->photo = 'application/users/image/' . $imageName;
            // $user->save();
            // Return a JSON response indicating success and the path to the uploaded photo
            return response()->json(['success' => 'Image uploaded successfully.', 'path' => $userPath, "statusCode" => 200]);
        } else {
            // Handle case where no photo was uploaded
            return response()->json(['error' =>'No photo uploaded.', "statusCode" => 400], 400);
        }
    }
    public function updatePersonalInformation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:password', // Ensure confirm_password matches password

        ]);
        $user = MyTokenManager::currentUser($request);
        DB::update(
            'update users set name =?,email=?,password=? where id=?',
            [$request->name, $request->email, Hash::make($request->password), $user->id]
        );
        $newUser = DB::select('select * from users where id=?', [$user->id]);
        return response()->json(['success' => 'personal information updated successfully.', 'user' => $newUser]);
    }
}
