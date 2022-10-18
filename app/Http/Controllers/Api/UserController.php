<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\User\StoreRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\Api\User\SubscribeRequest;
use App\Http\Resources\SectionResource;

class UserController extends Controller
{

    public function index() {
        return UserResource::collection(User::all());
    }


    public function store(StoreRequest $request) {
        $data = $request->validated();
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        $createdUser = User::create($data);

        return new UserResource($createdUser);

    }


    public function show(User $user) {
        return new UserResource($user);
    }


    public function update(UpdateRequest $request, User $user) {
        $data = $request->validated();
        $user->update($data);
        $user->fresh();

        return new UserResource($user);
    }


    public function destroy(User $user) {
        $user->sections()->detach();
        $user->delete();
        return true;
    }

    public function subscribe(SubscribeRequest $request) {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();

        if($user->sections->pluck('id')->contains($data['section_id'])) {
            return SectionResource::collection($user->sections);
        }

        $user->sections()->attach($data['section_id']);
        return SectionResource::collection($user->fresh()->sections);
    }

    public function unsubscribe(SubscribeRequest $request) {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();

        if( !($user->sections->pluck('id')->contains($data['section_id'])) ) {
            return SectionResource::collection($user->sections);
        }

        $user->sections()->detach($data['section_id']);
        return SectionResource::collection($user->fresh()->sections);
    }

    public function unsubscribeAll(Request $request) {
        $data = $request->validate([
            'email' => 'required | email | exists:users'
        ]);
        $user = User::where('email', $data['email'])->firstOrFail();
        $user->sections()->detach();

        return new UserResource($user->fresh());
    }

    public function showSubSections(User $user) {
        // $currentUser = auth()->user();
        
        return SectionResource::collection($user->sections);
    }
}
