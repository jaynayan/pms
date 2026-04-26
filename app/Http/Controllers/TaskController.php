<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display a listing of backlog tasks.
     */
    public function backlog()
    {
        //
    }

    /**
     * Claim the specified task.
     */
    public function claim(string $id)
    {
        //
    }

    /**
     * Update the status of the specified task.
     */
    public function updateStatus(\Illuminate\Http\Request $request, string $id)
    {
        //
    }
}
