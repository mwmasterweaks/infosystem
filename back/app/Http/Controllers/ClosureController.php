<?php

namespace App\Http\Controllers;

use App\closure;
use App\splitter;
use App\hardfiber;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class ClosureController extends Controller
{
    private $cname = "ClosureController";

    public function index()
    {
        $tbl = closure::with(["closure_type", "splitter_closure.splitter_type"])
            // ->whereHas("splitter", function ($query) {
            //     $query->where("attach_to", "closure");
            // })
            ->get();

        $retval = [];
        foreach ($tbl as $item) {

            $position = new stdClass;
            $position->lat = (float) $item->lat;
            $position->lng = (float) $item->lng;
            $item->position = $position;
            $item->draggable = false;
            $item->save = true;
            $item->marker_type = "closure";
            $closure_type_temp = (object) $item->closure_type;
            $item->type = $closure_type_temp->name;
            array_push($retval, $item);
        }

        return response()->json($retval);
    }
    public function getClosure($id)
    {
        $tbl = closure::with(["closure_type", "splitter_closure.splitter_type"])
            ->where("id", $id)
            ->first();

        $position = new stdClass;
        $position->lat = (float) $tbl->lat;
        $position->lng = (float) $tbl->lng;
        $tbl->position = $position;
        $tbl->draggable = false;
        $tbl->save = true;
        $tbl->marker_type = "closure";
        $closure_type_temp = (object) $tbl->closure_type;
        $tbl->type = $closure_type_temp->name;

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = closure::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new closure: " . $data
            );

            return $this->getClosure($data->id);
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
            if ($ex->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Name already exist.'], 500);
            } else {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        }
    }
    public function show($id)
    {
        $tbl = closure::with(["closure_type", "splitter_closure.splitter_type"])
            ->where("id", $id)
            ->first();

        $tbl->save = true;
        $tbl->marker_type = "closure";
        $closure_type_temp = (object) $tbl->closure_type;
        $tbl->type = $closure_type_temp->name;

        return $tbl;
    }
    public function edit(closure $closure)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = closure::findOrFail($id);
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
                "update closure id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl = splitter::where("attach_to", "closure")->where("attach_id", $id)->get();
            $tbl1 = hardfiber::where("end1", "closure")->where("end1_id", $id)->get();
            $tbl2 = hardfiber::where("end2", "closure")->where("end2_id", $id)->get();

            if (count($tbl) > 0) {
                return response()->json(['error' => 'You cannot delete this closure it has splitter inside'], 500);
            } else {
                if (count($tbl1) > 0 || count($tbl2) > 0) {
                    return response()->json(['error' => 'You cannot delete this closure it has hardfiber attached'], 500);
                } else {
                    $tbl1111 = closure::findOrFail($id);
                    closure::destroy($id);

                    $this
                        ->log(
                            Carbon::now(),
                            "",
                            "",
                            $this->cname,
                            "destroy",
                            "message",
                            "delete closure id " . $id .
                                "\nOld closure: " . $tbl1111
                        );
                    return app('App\Http\Controllers\NodeController')->index();
                }
            }
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
