<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\User\StoreRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\User\UpdateRequest;

class UserController extends Controller
{

    public function index()
    {
        return UserResource::collection(User::all());
    }


    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        $createdUser = User::create($data);

        return new UserResource($createdUser);

    }


    public function show(User $user)
    {
        return new UserResource($user);
    }


    public function update(UpdateRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        $user->fresh();

        return new UserResource($user);
    }

    
    public function destroy(User $user)
    {
        $user->sections()->detach();
        $user->delete();
        return true;
    }
}
