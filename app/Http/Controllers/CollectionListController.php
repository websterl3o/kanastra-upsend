<?php

namespace App\Http\Controllers;

use App\Models\CollectionList;
use App\Jobs\ProcessCollectionList;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreCollectionListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CollectionListController extends Controller
{

    public function create()
    {
        return view('collection-list.create');
    }

    public function store(StoreCollectionListRequest $request): JsonResponse
    {
        try {
            $path = $request->file('file')->store('collection-lists-' . now()->format('Y-m-d'));

            $collectionList = CollectionList::create([
                'original_name' => $request->file('file')->getClientOriginalName(),
                'name' => $request->file('file')->hashName(),
                'path' => $path
            ]);

            Log::info("Collection list {$collectionList->id} created with file {$collectionList->original_name}.");

            ProcessCollectionList::dispatch($collectionList);

            return response()->json(['message' => 'Lista em processo de disparo de e-mails!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("Failed to process collection list: {$e->getMessage()}");
            return response()->json(['error' => 'Falha ao processar o arquivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
