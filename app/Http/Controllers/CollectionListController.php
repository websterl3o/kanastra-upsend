<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CollectionListController extends Controller
{

    public function index()
    {
        // Code to list all resources
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('collection-list.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        // Code to store a new resource
    }

    // Display the specified resource.
    public function show($id)
    {
        // Code to display a specific resource
    }

    // Show the form for editing the specified resource.
    public function edit($id)
    {
        // Code to show form for editing a specific resource
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        // Code to update a specific resource
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        // Code to delete a specific resource
    }
}
