<?php

namespace App\Http\Controllers;

use App\Models\StudentProgress;
use App\Http\Requests\StoreStudentProgressRequest;
use App\Http\Requests\UpdateStudentProgressRequest;

class StudentProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentProgressRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentProgress $studentProgress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentProgress $studentProgress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentProgressRequest $request, StudentProgress $studentProgress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentProgress $studentProgress)
    {
        //
    }
}
