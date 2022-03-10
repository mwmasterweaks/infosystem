<?php

namespace App\Http\Controllers;

use App\activity_ticket_type;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityTicketTypeController extends Controller
{

    private $cname = "ActivityTicketTypeController";
    public function index()
    {
        $activity_ticket_types = activity_ticket_type::all();

        return response()->json($activity_ticket_types);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = activity_ticket_type::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new activity_ticket_type: " . $data
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function show(activity_ticket_type $activity_ticket_type)
    {
        //
    }

    public function edit(activity_ticket_type $activity_ticket_type)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = activity_ticket_type::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();
            $cmd->fill($input)->save();
            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update activity_ticket_type id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            activity_ticket_type::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {

            return response()->json(['error' => 'There was a problem deleting this type.'], 500);
        }
    }
}
