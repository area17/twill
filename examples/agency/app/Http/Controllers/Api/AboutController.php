<?php


namespace App\Http\Controllers\Api;


use App\Http\Resources\PersonResource;
use App\Models\About;
use App\Models\Person;
use App\Repositories\PersonRepository;
use Illuminate\Http\JsonResponse;

class AboutController
{
    /**
     * @var PersonRepository
     */
    private PersonRepository $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $about = About::first();
        $people = Person::published()->with(['slugs', 'medias', 'office'])->get()->map(function ($person) {
            return [
                'full_name' => $person->full_name,
                'office' => $person->office_name,
                'slug' => $person->slug,
                'image' => $person->imagesAsArrays('main')
            ];
        });

        return response()->json([
            'title' => $about->title ?? '',
            'tagline' => $about->tagline ?? '',
            'text' => $about->text ?? '',
            'people' => $people
        ]);
    }

    /**
     * @param $slug
     * @return PersonResource
     */
    public function person($slug)
    {
        $person = $this->personRepository->forSlug($slug, ['slugs', 'medias', 'office', 'videos.medias', 'works.medias']);

        return new PersonResource($person);
    }
}
