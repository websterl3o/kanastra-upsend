<?php

namespace App\Http\Controllers;

use App\Models\CollectionList;
use App\Jobs\ProcessCollectionList;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreCollectionListRequest;
use Illuminate\Support\Facades\Log;

class CollectionListController extends Controller
{

    public function create()
    {
        return view('collection-list.create');
    }

    public function store(StoreCollectionListRequest $request)
    {
        $path = $request->file('file')->store('collection-lists-' . now()->format('Y-m-d'));

        $collectionList = CollectionList::create([
            'original_name' => $request->file('file')->getClientOriginalName(),
            'name' => $request->file('file')->hashName(),
            'path' => $path
        ]);

        Log::info("Collection list {$collectionList->id} created with file {$collectionList->name}.");

        ProcessCollectionList::dispatch($collectionList);

        return response()->json(['message' => 'Lista e em processo de disparo de e-mails!'], Response::HTTP_CREATED);
    }
}
