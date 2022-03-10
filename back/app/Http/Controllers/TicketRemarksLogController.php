<?php

namespace App\Http\Controllers;

use App\ticket_remarks_log;
use App\Ticket;
use App\Client_detail;
use App\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TicketRemarksLogController extends Controller
{
    private $cname = "TicketRemarksLogController";

    public function index()
    {
        $tbl = ticket_remarks_log::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        // return $request;
        try {
            $data = ticket_remarks_log::create($request->all());



            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new ticket_remarks_log: " . $data
            );

            return $this->show($request->ticket_id);
        } catch (\Exception $ex) {
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
    public function show($id)
    {
        $tbl = ticket_remarks_log::with(['user'])
            ->where('ticket_id', $id)
            ->latest()
            ->get();
        foreach ($tbl as $r_log) {
            $r_log->commentVisibility = 'hide';
        }
        return response()->json($tbl);
    }
    public function edit(ticket_remarks_log $ticket_remarks_log)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = ticket_remarks_log::findOrFail($id);
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
                "update ticket_remarks_log id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Exception $ex) {
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
            $tbl1 = ticket_remarks_log::findOrFail($id);
            ticket_remarks_log::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete ticket_remarks_log id " . $id .
                    "\nOld ticket_remarks_log: " . $tbl1
            );
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function moveTicketRemarks()
    {
        $tbl = Client_detail::whereNotNull('contract_status')->get();
        foreach ($tbl as $item) {
            DB::table("clients")
                ->where("id", $item->client_id)
                ->update([
                    "contract" => $item->contract_status
                ]);
        }
        return "ok";
    }
    public function log($date, $userID, $userName, $ControllerName, $functionName, $logType, $message)
    {
        $filenameDate = date("mY");
        $myfile = fopen("logs/log" . $filenameDate . ".txt", "a") or die("Unable to open file!");
        $txt = $date . "\t--\t" . $userID . "\t--\t" . $userName . "\t--\t" . $ControllerName .
            "\t--\t" . $functionName . "\t--\t" . $logType . "\t--\t" . $message . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }
}
