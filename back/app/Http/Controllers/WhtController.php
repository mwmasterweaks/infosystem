<?php

namespace App\Http\Controllers;

use App\wht;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class WhtController extends Controller
{
    private $cname = "WhtController";

    public function index()
    {
        $tbl = wht::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $data = wht::create($request->all());

            $bills = (array) $request->selectedToPay;

            foreach ($bills as $item) {
                $item = (object) $item;
                DB::table("billings")
                    ->where("id", $item->id)
                    ->decrement('balance', $item->payment);
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new wht: " . $data
            );
            DB::commit();
            return $this->show($request->client_id);
        } catch (\Exception $ex) {
            DB::rollBack();
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

    public function report(Request $request)
    {
        // return $request;

        $yearMonth = $request->date;
        $area_id = $request->area_id;

        if ($area_id != 0) {
            $tbl = wht::with(["client"])
                ->groupBy('client_id')
                ->selectRaw('*, sum(amount) as amount')
                ->whereHas("client", function ($query) use ($area_id) {
                    $query->where("area_id", $area_id);
                })
                ->where('date', 'like', '%' . $yearMonth . '%')
                ->get();
        } else
            $tbl = wht::with(["client"])
                ->groupBy('client_id')
                ->selectRaw('*, sum(amount) as amount')
                ->where('date', 'like', '%' . $yearMonth . '%')
                ->get();



        return $tbl;
    }


    public function show($id)
    {
        $tbl = wht::where("client_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(wht $wht)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = wht::findOrFail($id);
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
                "update wht id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->show($request->olt_id);
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
            wht::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy1($id, $olt_id)
    {
        try {
            $tbl1 = wht::findOrFail($id);
            wht::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete wht id " . $id .
                    "\nOld wht: " . $tbl1
            );
            return $this->show($olt_id);
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
