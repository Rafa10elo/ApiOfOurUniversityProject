<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{

    public function store(StoreApartmentRequest $request)
    {
        $data = $request->validated();
        $data['owner_id'] = auth()->id();

        $apartment = Apartment::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                ApiHelper::saveMedia($apartment, $img, 'apartment_images');
            }
        }
        $apartment->load(['owner', 'reviews']);
        return ApiHelper::success('Apartment created', new ApartmentResource($apartment), 201);
    }


    public function update(UpdateApartmentRequest $request, $id)
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->owner_id !== auth()->id())
            return ApiHelper::error("Not authorized", 403);


        $apartment->update($request->validated());

        if ($request->hasFile('images')) {
            $apartment->clearMediaCollection('apartment_images');
            foreach ($request->file('images') as $img)
                ApiHelper::saveMedia($apartment, $img, 'apartment_images');

        }

        $apartment->load(['owner', 'reviews']);


        return ApiHelper::success('Apartment updated', new ApartmentResource($apartment));
    }

    public function destroy($id)
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->owner_id !== auth()->id())
          return ApiHelper::error("Not authorized", 403);

        $apartment->delete();
        return ApiHelper::success("Apartment deleted");
    }

    #[QueryParameter(
        name: 'city',
        description: 'keyword search on city name',
        example: 'damascus'
    )]
    #[QueryParameter(
        name: 'governorate',
        description: 'filter by governorate',
        example: 'damascus'
    )]
    #[QueryParameter(
        name: 'min_price',
        description: 'minimum price',
        type: 'int',
        example: 300
    )]
    #[QueryParameter(
        name: 'max_price',
        description: 'maximum price',
        type: 'int',
        example: 800
    )]
    /**
     * @unauthenticated
     */
    public function index(Request $request)
    {
        $query = Apartment::query();

        if ($request->filled('city')) {
            $keyword = $request->city;
            $query->where('city', 'LIKE', "%{$keyword}%");
        }
       if ($request->filled('governorate'))
            $query->where('governorate', $request->governorate);


        if ($request->filled('min_price'))
           $query->where('price', '>=', $request->min_price);


         if ($request->filled('max_price'))
          $query->where('price', '<=', $request->max_price);


        $apartments = $query->with(['owner', 'reviews'])->paginate(10);

        return ApiHelper::success("apartments list", ApartmentResource::collection($apartments)
        );
    }


    /**
     * @unauthenticated
     */
    public function show($id)
    {
        $apartment = Apartment::with(['owner', 'bookings', 'reviews.user'])->findOrFail($id);

      return ApiHelper::success("Apartment details", new ApartmentResource($apartment));
    }
    /**
     * @unauthenticated
     */
    public function topRated(Request $request)
    {
        $apartments = Apartment::withAvg('reviews', 'rating')
        ->with(['owner', 'reviews'])
        ->orderByDesc('reviews_avg_rating')
        ->paginate($request->get('per_page', 10));

        return ApiHelper::success(
            "Top rated apartments",
            ApartmentResource::collection($apartments)
        );
    }


}
