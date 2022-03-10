<?php

namespace App\Http\Controllers;

use App\ticket_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\olt;
use stdClass;
use Carbon\Carbon;
use Exception;

class TicketGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\ticket_group  $ticket_group
     * @return \Illuminate\Http\Response
     */
    public function show(ticket_group $ticket_group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ticket_group  $ticket_group
     * @return \Illuminate\Http\Response
     */
    public function edit(ticket_group $ticket_group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ticket_group  $ticket_group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $request;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ticket_group  $ticket_group
     * @return \Illuminate\Http\Response
     */
    public function destroy(ticket_group $ticket_group)
    {
        //
    }

    public function getBucketInfoClient()
    {
        // return "created connection!";
        $tbl = DB::table('bucket_info_clients')->where('status', 'pending')->get();

        return response()->json($tbl);
    }
    public function getBucketInfoClientAll()
    {
        // return "created connection!";
        $tbl = DB::table('bucket_info_clients')->get();

        return response()->json($tbl);
    }

    public function match($id)
    {

        DB::table('bucket_info_clients')
            ->where('id', $id)
            ->update([
                'status' => "Matched"
            ]);

        return "matched!";
    }
    public function unmatch($id)
    {
        DB::table('bucket_info_clients')
            ->where('id', $id)
            ->update([
                'status' => "Unmatched",
            ]);

        return "unmatched!";
    }
}
