<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    private $cname = "TeamController";
    public function index()
    {
        $tbl = Team::with(['user'])->get();

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = Team::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Team: " . $data
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

    public function show(Team $team)
    {
        //
    }

    public function edit(Team $team)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $tbl  = Team::findOrFail($id);
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
                "update Team id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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

            $tbl1 = Team::findOrFail($id);

            Team::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Team id " . $id .
                    "\nOld Team: " . $tbl1
            );
            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function summary(Request $request, $id)
    {
        try {
            if ($request->end == null)
                $request->end = $request->start;

            $from = new Carbon($request->start);
            $to = new Carbon($request->end);
            $ret_val = [];
            $total_plan = 0;
            $total_done = 0;
            while ($from <= $to) {
                $plan = DB::table('schedules')
                    ->where("team_id", $id)
                    ->where("target_date", $from->toDateString())
                    ->count();

                $done  = DB::table('schedules')
                    ->where("team_id", $id)
                    ->where("status", 'ok')
                    ->where("target_date", $from->toDateString())
                    ->count();
                $total_plan += $plan;
                $total_done += $done;
                $pending = $plan - $done;
                if ($plan != 0) {
                    $done_percent = ($done / $plan) * 100;
                    $pending_percent = ($pending / $plan) * 100;
                } else {
                    $done_percent = 0;
                    $pending_percent = 0;
                }
                $c1 = collect();
                $c1->put('date', $from->toDateString());
                $c1->put('plan', $plan);
                $c1->put('done', $done);
                $c1->put('done_percent', number_format((float) $done_percent, 2, '.', '') . "%");
                $c1->put('pending', $pending);
                $c1->put('pending_percent', number_format((float) $pending_percent, 2, '.', '') . "%");
                array_push($ret_val, $c1);
                $from->addDay();
            }

            $total_pending = $total_plan - $total_done;
            if ($total_plan != 0) {
                $total_done_percent = ($total_done / $total_plan) * 100;
                $total_pending_percent = ($total_pending / $total_plan) * 100;
            } else {
                $total_done_percent = 0;
                $total_pending_percent = 0;
            }
            $c1 = collect();
            $c1->put('date', 'Total');
            $c1->put('plan', $total_plan);
            $c1->put('done', $total_done);
            $c1->put('done_percent', number_format((float) $total_done_percent, 2, '.', '') . "%");
            $c1->put('pending', $total_pending);
            $c1->put('pending_percent', number_format((float) $total_pending_percent, 2, '.', '') . "%");
            array_push($ret_val, $c1);

            return $ret_val;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function summary_accounts(Request $request)
    {
        try {
            if ($request->end == null)
                $request->end = $request->start;

            $from = new Carbon($request->start);
            $to = new Carbon($request->end);

            $tbl_inst = DB::table('schedules')
                ->join('client_details', 'client_details.id', 'schedules.client_detail_id')
                ->join('clients', 'clients.id', 'client_details.client_id')
                ->whereBetween("schedules.target_date", [$from->toDateString(), $to->toDateString()])
                ->whereNull('ticket_id')
                ->select("schedules.target_date as TDATE", "schedules.type as TYPE", "clients.*")
                ->get();

            $tbl_ticket = DB::table('schedules')
                ->join('tickets', 'tickets.id', 'schedules.ticket_id')
                ->join('clients', 'clients.id', 'tickets.client_id')
                ->whereBetween("schedules.target_date", [$from->toDateString(), $to->toDateString()])
                ->whereNull('client_detail_id')
                ->select("schedules.target_date as TDATE", "schedules.type as TYPE", "clients.*")
                ->get();

            $c1 = collect();

            $c1->put('inst', $tbl_inst);
            $c1->put('ticket', $tbl_ticket);


            return $c1;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
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
