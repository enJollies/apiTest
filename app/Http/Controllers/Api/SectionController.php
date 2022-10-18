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

    public function index() {
        return SectionResource::collection(Section::all());
    }


    public function store(StoreRequest $request) {
        $data = $request->validated();
        $createdSection = Section::create($data);

        return new SectionResource($createdSection);
    }


    public function show(Section $section) {
        return new SectionResource($section);
    }


    public function update(UpdateRequest $request, Section $section) {
        $data = $request->validated();
        $section->update($data);
        $section->fresh();

        return new SectionResource($section);
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

        switch($request->header('Accept')) {
            case 'application/xml':
                $array = $this->service->generateXmlArray($users, 'name');
                return response()->xml($array);
            default:
                return UserResource::collection($users);
        }
    }
}
