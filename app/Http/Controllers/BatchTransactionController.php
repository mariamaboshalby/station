<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BatchTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // TODO: Implement index
        return view('batch-transactions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // TODO: Implement create
        return view('batch-transactions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store
    }
}
