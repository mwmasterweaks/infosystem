<?php

namespace App\Http\Controllers;

use App\schedule;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use stdClass;

class ScheduleController extends Controller
{
    private $cname = "ScheduleController";

    public function index()
    {
        $tbl = schedule::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = schedule::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new schedule: " . $data
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


    public function show($id)
    {
        $tbl = schedule::where("olt_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(schedule $schedule)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = schedule::findOrFail($id);

            $input = $request->all();

            $cmd->fill($input)->save();

            return "ok";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            schedule::destroy($id);

            return "ok";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function summary_report_daterange(Request $request)
    {

        if ($request->end == null)
            $request->end = $request->start;

        $from = new Carbon($request->start);
        $to = new Carbon($request->end);

        $from = $from->toDateString();
        $to = $to->toDateString();

        $tbl = Region::all();
        $temp = [];
        foreach ($tbl as $item) {
            $inst_plan = DB::table('schedules')
                ->join('client_details', 'client_details.id', 'schedules.client_detail_id')
                ->join('clients', 'clients.id', 'client_details.client_id')
                ->where("clients.region_id", $item->id)
                ->where("schedules.type", "installation")
                ->whereBetween("schedules.target_date", [$from, $to])
                ->count();
            $inst_done = DB::table('schedules')
                ->join('client_details', 'client_details.id', 'schedules.client_detail_id')
                ->join('clients', 'clients.id', 'client_details.client_id')
                ->where("clients.region_id", $item->id)
                ->where("schedules.status", "ok")
                ->where("schedules.type", "installation")
                ->whereBetween("schedules.target_date", [$from, $to])
                ->count();

            $pending = $inst_plan - $inst_done;
            if ($inst_plan != 0) {
                $done_percent = ($inst_done / $inst_plan) * 100;
                $pending_percent = ($pending / $inst_plan) * 100;
            } else {
                $done_percent = 0;
                $pending_percent = 0;
            }
            $item->inst_planned = $inst_plan;
            $item->inst_actual = $inst_done;
            $item->inst_percent = $done_percent;
            $item->inst_pending = $pending;

            $trbl_plan = DB::table('schedules')
                ->join('tickets', 'tickets.id', 'schedules.ticket_id')
                ->join('clients', 'clients.id', 'tickets.client_id')
                ->where("clients.region_id", $item->id)
                ->where("schedules.type", "ticket")
                ->whereBetween("schedules.target_date", [$from, $to])
                ->count();
            $trbl_done = DB::table('schedules')
                ->join('tickets', 'tickets.id', 'schedules.ticket_id')
                ->join('clients', 'clients.id', 'tickets.client_id')
                ->where("clients.region_id", $item->id)
                ->where("schedules.status", "ok")
                ->where("schedules.type", "ticket")
                ->whereBetween("schedules.target_date", [$from, $to])
                ->count();

            $pending1 = $trbl_plan - $trbl_done;
            if ($trbl_plan != 0) {
                $done_percent1 = ($trbl_done / $trbl_plan) * 100;
                $pending_percent1 = ($pending / $trbl_plan) * 100;
            } else {
                $done_percent1 = 0;
                $pending_percent1 = 0;
            }

            $item->trbl_planned = $trbl_plan;
            $item->trbl_actual = $trbl_done;
            $item->trbl_percent = $done_percent1;
            $item->trbl_pending = $pending1;

            array_push($temp, $item);
        }
        $c1 = collect();
        $c1->put("items", $temp);
        return response()->json($c1);
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
