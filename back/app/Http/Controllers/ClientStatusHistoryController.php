<?php

namespace App\Http\Controllers;

use App\client_status_history;
use Illuminate\Http\Request;

class ClientStatusHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tbl = client_status_history::with(['user', 'client'])->get();


        return response()->json($tbl);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\client_status_history  $client_status_history
     * @return \Illuminate\Http\Response
     */
    public function show(client_status_history $client_status_history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\client_status_history  $client_status_history
     * @return \Illuminate\Http\Response
     */
    public function edit(client_status_history $client_status_history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\client_status_history  $client_status_history
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, client_status_history $client_status_history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\client_status_history  $client_status_history
     * @return \Illuminate\Http\Response
     */
    public function destroy(client_status_history $client_status_history)
    {
        //
    }
}
