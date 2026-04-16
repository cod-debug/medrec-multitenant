<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $attempt_email = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $attempt_username = Auth::attempt(['username' => $request->email, 'password' => $request->password]);

        if ($attempt_email || $attempt_username) {

            $user = Auth::user();

            if ($user->status === 'archived') {
                return $this->sendError('Not Authorized', ['error' => 'Your Account is Archived and Cannot be used'], 403);
            }

            $success['token'] = $user->createToken('Authenticated')->plainTextToken;
            $success['name'] = $user->name;
            $success['level_of_authorization'] = $user->level_of_authorization;
            // $success['required_change_password'] = $user->required_change;

            return $this->sendResponse( 'User Login Successfully.', $success);

        } else {
            return $this->sendError('Not Authorized', ['error' => 'Invalid Credentials.'], 403);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'temp_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        if (!Hash::check($request->input('temp_password'), $user->password)) {
            return $this->sendError('Invalid temporary password', ['error' => 'Temporary password is incorrect.']);
        }

        if (Hash::check($request->input('new_password'), $user->password)) {
            return $this->sendError('Detected Reusing of Password', ['error' => 'New password must be different from the old password']);
        }

        $user->password = bcrypt($request->input('new_password'));
        // $user->required_change = false;
        // $user->password_changed_date = now();
        $user->save();

        return $this->sendResponse('Password changed successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return $this->sendResponse("User logged out successfully");
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validator Error', $validator->error());
        }

        $user = User::where('email', $request->email)->first();

        $temporary_password = Str::random(8);

        $user->password = bcrypt($temporary_password);
        // $user->required_change = 1;
        $user->save();

        // Mail::to($user->email)->send(new ForgotPassword($user->name, $temporary_password));

        return $this->sendResponse('Temporary Password sent to your email');

    }

    public function unauthorized(){
        return $this->sendError('Unauthorized user detected.', 401);
    }
}
