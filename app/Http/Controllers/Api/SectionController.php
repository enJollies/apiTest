<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Http\Resources\SectionResource;
use App\Http\Requests\Api\Section\StoreRequest;
use App\Http\Requests\Api\Section\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\SectionService;

class SectionController extends Controller
{
    private $service;

    public function __construct() {
        $this->service = new SectionService();
    }

    public function index(Request $request) {

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => Section::all(),
            'resource' => SectionResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }


    public function store(StoreRequest $request) {
        $data = $request->validated();
        $createdSection = Section::create($data);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $createdSection,
            'resource' => SectionResource::class,
            'isCollection' => false
        ]);

        return $responce;
    }


    public function show(Request $request, Section $section) {

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $section,
            'resource' => SectionResource::class,
            'isCollection' => false
        ]);

        return $responce;
    }


    public function update(UpdateRequest $request, Section $section) {
        $data = $request->validated();
        $section->update($data);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $section->fresh(),
            'resource' => SectionResource::class,
            'isCollection' => false
        ]);

        return $responce;
    }


    public function destroy(Section $section) {
        $section->users()->detach();
        $section->delete();
        return true;
    }

    public function showSubs(Request $request) {

        $data = $request->validate([
            'section_id' => 'required | integer | exists:sections,id',
            'limit' => 'integer'
        ]);
        $limit = $data['limit'] ?? 5;
        $currentSection = Section::findOrFail($data['section_id']);
        $users = $currentSection->users->take($limit);

        $responce = $this->service->generateResponce([
            'request' => $request,
            'result' => $users,
            'resource' => UserResource::class,
            'isCollection' => true
        ]);

        return $responce;
    }
}
