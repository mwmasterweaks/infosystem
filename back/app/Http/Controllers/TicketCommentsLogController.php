<?php

namespace App\Http\Controllers;

use App\ticket_comments_log;
use App\Ticket;
use App\Client_detail;
use App\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TicketCommentsLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // private $cname = "TicketCommentsLogController";

    public function index()
    {
        // $tbl = ticket_comments_log::all();
        $tbl = ticket_comments_log::with(['user'])->get();
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
        // return $request;

        try {
            $data = ticket_comments_log::create($request->all());


            return $this->index();
            return "ok";
            // return $this->show($request->remarks_id);
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ticket_comments_log  $ticket_comments_log
     * @return \Illuminate\Http\Response
     */
    public function show(ticket_comments_log $ticket_comments_log)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ticket_comments_log  $ticket_comments_log
     * @return \Illuminate\Http\Response
     */
    public function edit(ticket_comments_log $ticket_comments_log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ticket_comments_log  $ticket_comments_log
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $cmd  = ticket_comments_log::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();
            $logTo = $cmd;
            // $this
            //     ->log(
            //         Carbon::now(),
            //         $request->user_id,
            //         $request->user_name,
            //         $this->cname,
            //         "update",
            //         "message",
            //         "update ticket_commments_log id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            //     );
            return $this->index();
        } catch (\Exception $ex) {
            // $this
            //     ->log(
            //         Carbon::now(),
            //         $request->user_id,
            //         $request->user_name,
            //         $this->cname,
            //         "update",
            //         "Error",
            //         $ex->getMessage()
            //     );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ticket_comments_log  $ticket_comments_log
     * @return \Illuminate\Http\Response
     */
    public function destroy(ticket_comments_log $ticket_comments_log)
    {
        //
    }
}
