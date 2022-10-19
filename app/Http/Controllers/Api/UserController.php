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
use App\Services\UserService;

class UserController extends Controller
{
    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    public function index(Request $request) {

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => User::all(),
            'resource' => UserResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }


    public function store(StoreRequest $request) {

        $data = $request->validated();
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        $createdUser = User::create($data);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $createdUser,
            'resource' => UserResource::class,
            'isCollection' => false
        ]);

        return $responce;

    }


    public function show(Request $request, User $user) {

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $user,
            'resource' => UserResource::class,
            'isCollection' => false
        ]);

        return $responce;
    }


    public function update(UpdateRequest $request, User $user) {
        $data = $request->validated();
        $user->update($data);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $user->fresh(),
            'resource' => UserResource::class,
            'isCollection' => false
        ]);

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

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $user->fresh()->sections,
            'resource' => SectionResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }

    public function unsubscribe(SubscribeRequest $request) {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();

        if( !($user->sections->pluck('id')->contains($data['section_id'])) ) {
            return SectionResource::collection($user->sections);
        }

        $user->sections()->detach($data['section_id']);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $user->fresh()->sections,
            'resource' => SectionResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }

    public function unsubscribeAll(Request $request) {

        $data = $request->validate([
            'email' => 'required | email | exists:users'
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->sections()->detach();

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $user->fresh(),
            'resource' => UserResource::class,
            'isCollection' => false
        ]);

        return $responce;
    }

    public function showSubSections(Request $request) {

        $data = $request->validate([
            'limit' => 'integer'
        ]);
        $limit = $data['limit'] ?? 5;
        $currentUser = auth()->user();
        $sections = $currentUser->sections->take($limit);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $sections,
            'resource' => SectionResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }


}

