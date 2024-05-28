<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DestinationController extends BaseController
{
    public function store(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        $data = $request->all();

        try {
            if ($request->exists('id')) {
                $entity = Destination::find($request->id);
                $entity->update($data);
            } else {
                Destination::create($data);
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
        return response()->json(Destination::all());
    }

    public function getOne(Request $request) : JsonResponse
    {
        if ($request->slug === '' || $request->slug === null) {
            throw new \Exception('Destination not found');
        }

        return response()->json(
            Destination::where(\is_numeric($request->slug) ? 'id' : 'slug', $request->slug)->get()->first()
        );
    }

    public function removeOne(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        if (!\is_numeric($request->id) && !\is_int($request->id)) {
            throw new \Exception('Destination not found');
        }

        Destination::find($request->id)->delete();

        return response()->json(['status' => 'success']);
    }

    public function copyOne(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        if (!\is_numeric($request->id) && !\is_int($request->id)) {
            throw new \Exception('Destination not found');
        }

        $entity = Destination::find($request->id)->replicate();
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

            $villa = Destination::withTrashed()->where('slug', $slug)->first();

            if ($villa === null) {
                return $slug;
            }

            $index++;
        }
    }
}
