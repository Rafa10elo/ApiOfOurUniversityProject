<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
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

        $apartment->load('owner');

        return ApiHelper::success('Apartment created', new ApartmentResource($apartment), 201);
    }

    public function update(UpdateApartmentRequest $request,  $id)
    {
        $apartment = Apartment::findOrFail($id);
        if ($apartment->owner_id !== auth()->id())
            return ApiHelper::error("Not authorized", 403);


        $apartment->update($request->validated());

        if ($request->hasFile('images')) {
            $apartment->clearMediaCollection('apartment_images');
            foreach ($request->file('images') as $img) {
                ApiHelper::saveMedia($apartment, $img, 'apartment_images');
            }
        }

        $apartment->load('owner');

        return ApiHelper::success('Apartment updated', new ApartmentResource($apartment));
    }

    public function destroy($id)
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->owner_id !== auth()->id()) {
            return ApiHelper::error("Not authorized", 403);
        }

        $apartment->delete();

        return ApiHelper::success("Apartment deleted");
    }
    /**
     * @unauthenticated
     */
    public function index(Request $request)
    {
        $query = Apartment::query();

        if ($request->city) $query->where('city', $request->city);
        if ($request->governorate) $query->where('governorate', $request->governorate);
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);
        if ($request->rooms) $query->where('rooms', $request->rooms);

        $apartments = $query->with('owner')->paginate(10);

        return ApiHelper::success("Apartments list", ApartmentResource::collection($apartments));
    }
    /**
     * @unauthenticated
     */
    public function show($id)
    {
        $apartment = Apartment::with(['owner', 'bookings'])->findOrFail($id);

        return ApiHelper::success("Apartment details", new ApartmentResource($apartment));
    }
}
