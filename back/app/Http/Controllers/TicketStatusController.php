<?php

namespace App\Http\Controllers;

use App\Ticket_status;
use App\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketStatusController extends Controller
{
    private $cname = "TicketStatusController";
    public function index()
    {
        $tbl = Ticket_status::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = Ticket_status::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new ticket status: " . $data
            );
            return $this->index();
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

    public function show(ticket_status $ticket_status)
    {
        //
    }

    public function edit(ticket_status $ticket_status)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $tbl  = Ticket_status::findOrFail($id);
            $logFrom = $tbl->replicate();
            $input = $request->all();

            $tbl->fill($input)->save();
            $logTo = $tbl;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Ticket_status id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
        $tbl = Ticket::where('Status_Ticket_id', $id)->first();

        if (empty($tbl)) {
            $tbl1 = Ticket_status::findOrFail($id);

            Ticket_status::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Ticket_status id " . $id .
                    "\nOld Ticket_status: " . $tbl1
            );

            return $this->index();
        } else {

            return response()->json(['error' => 'Cant delete this status, It\'s still in use.'], 500);
        }
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
