<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollectionList;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreCollectionListRequest;

class CollectionListController extends Controller
{

    public function index()
    {
        // Code to list all resources
    }

    public function create()
    {
        return view('collection-list.create');
    }

    public function store(StoreCollectionListRequest $request)
    {
        $path = $request->file('file')->store('collection_lists');

        CollectionList::create([
            'name_file' => $request->file('file')->getClientOriginalName(),
            'path' => $path
        ]);

        return response()->json(['message' => 'Lista e em processo de disparo de e-mails!'], Response::HTTP_CREATED);
    }
    public function show($id)
    {
        // Code to display a specific resource
    }
    public function edit($id)
    {
        // Code to show form for editing a specific resource
    }
    public function update(Request $request, $id)
    {
        // Code to update a specific resource
    }
    public function destroy($id)
    {
        // Code to delete a specific resource
    }
}
