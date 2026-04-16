<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function register(Request $request): JsonResponse
    {
        //
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|min:3|max:100|unique:users',
                'password' => 'required|string|min:8',
                'level_of_authorization' => 'required|integer',
            ]);

            
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'level_of_authorization' => $request->level_of_authorization,
            ];

            $new_user = User::create($data);

            if($new_user){
                return $this->sendResponse('User created successfully', ['user' => $new_user]);
            } else {
                return $this->sendError('Failed to create user', [], 400);
            }
        }
        catch (\Exception $e) {
            return $this->sendError('Failed to create user', ['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        //
        try {
            $per_page = $request->get('per_page', 10);
            $keyword = $request->get('keyword', '');

            $users = User::when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%')
                      ->orWhere('username', 'like', '%' . $keyword . '%');
            })
            ->paginate($per_page);

            return $this->sendResponse('Users retrieved successfully', $users);
        }
        catch (\Exception $e) {
            return $this->sendError('Failed to retrieve users', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        //
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->sendError('User not found', [], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'username' => 'sometimes|required|string|min:3|max:100|unique:users,username,' . $id,
                'password' => 'sometimes|required|string|min:8',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return $this->sendResponse('User updated successfully', ['user' => $user]);
        }
        catch (\Exception $e) {
            return $this->sendError('Failed to update user', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id): JsonResponse
    {
        //
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->sendError('User not found', [], 404);
            }

            $user->delete();

            return $this->sendResponse('User deleted successfully');
        }
        catch (\Exception $e) {
            return $this->sendError('Failed to delete user', ['error' => $e->getMessage()], 500);
        }
    }
    

    public function updateUserInfo(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => "sometimes|string|min:3|max:100|unique:users,username,{$user->id}",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validator Error', $validator->errors());
        }

        $validated = $validator->validated();

        if (empty($validated)) {
            return $this->sendError('No fields to update.', [], 422);
        }

        $success = $user->update($validated);


        if (!$success) {
            return $this->sendError('Cannot process updates', ['error' => 'Cannot update user information']);
        }

        return $this->sendResponse ('User Updated Informations successfully');
    }

    public function getCurrentUser(Request $request): JsonResponse
    {
        $loginned_user = Auth::user();

        if (!$loginned_user) {
            return $this->sendError('Unauthenticated', [], 401);
        }

        $user = User::find($loginned_user->id);

        if (!$user) {
            return $this->sendError('User Information Not Found', []);
        }

        return $this->sendResponse('User Information', $user);
    }
}
