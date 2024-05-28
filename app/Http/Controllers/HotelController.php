<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class HotelController extends BaseController
{
    public function store(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        $data = $request->all();
        $destinationId = null;

        if (\is_numeric($request->destinationId)) {
            $destinationId = $request->destinationId;
        } else {
            $destinationId = Destination::where('slug', $request->destinationId)->get()->first()?->id ?? null;
        }

        $data['destination_id'] = $destinationId;

        try {
            if ($request->exists('id')) {
                $entity = Hotel::find($request->id);
                $entity->update($data);
            } else {
                Hotel::create($data);
            }
        } catch (\Exception $ex) {
            if (\str_contains($ex->getMessage(), '1062 Duplicate entry')) {
                return response()->json(['status' => 'error', 'message' => 'This slug already exists'], 409);
            }

            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 412);
        }

        return response()->json(['status' => 'success']);
    }

    public function getAll(Request $request) : JsonResponse
    {
        return response()->json(Hotel::all());
    }

    public function getOne(Request $request) : JsonResponse
    {
        if ($request->slug === '' || $request->slug === null) {
            throw new \Exception('Hotel not found');
        }

        return response()->json(
            Hotel::where(\is_numeric($request->slug) ? 'id' : 'slug', $request->slug)->with('destination')->get()->first()
        );
    }

    public function removeOne(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        if (!\is_numeric($request->id) && !\is_int($request->id)) {
            throw new \Exception('Hotel not found');
        }

        Hotel::find($request->id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function copyOne(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        if (!\is_numeric($request->id) && !\is_int($request->id)) {
            throw new \Exception('Hotel not found');
        }

        $entity = Hotel::find($request->id)->replicate();
        $entity->created_at = Carbon::now();
        $entity->slug = $this->generateCopySlug($entity->slug);
        $entity->save();

        return response()->json(['status' => 'success']);
    }

    private function generateCopySlug(string $baseSlug) : string
    {
        $index = 0;

        while (true) {
            if (\str_contains($baseSlug, '-copy')) {
                [$bSlug] = \explode('-copy', $baseSlug);

                $slug = $bSlug . '-copy' . ($index === 0 ? '' : $index);
            } else {
                $slug = $baseSlug . '-copy' . ($index === 0 ? '' : $index);
            }

            $villa = Hotel::withTrashed()->where('slug', $slug)->first();

            if ($villa === null) {
                return $slug;
            }

            $index++;
        }
    }
}
