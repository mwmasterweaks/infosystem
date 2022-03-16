<?php

namespace App\Http\Controllers;

use App\splitter;
use App\splitter_port;
use App\closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use stdClass;
use Exception;
use Carbon\Carbon;

class SplitterController extends Controller
{
    private $cname = "SplitterController";

    public function index()
    {
        $tbl = splitter::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = splitter::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new splitter: " . $data
            );
            return $this->show($data->attach_id, $data->attach_to);
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
    public function show($id, $wit)
    {
        $tbl = splitter::with(['splitter_type'])->where('attach_to', $wit)
            ->where('attach_id', $id)->get();
        return response()->json($tbl);
    }
    public function show_lcp($id)
    {
        $tbl = splitter::with(['splitter_nap.splitter_port'])->where('parent_id', $id)
            ->where('parent', 'olt')->get();
        return response()->json($tbl);
    }
    public function edit(splitter $splitter)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = splitter::findOrFail($id);
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
                "update splitter id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->show($cmd->attach_id, $cmd->attach_to);
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
            $tbl = splitter_port::where("splitter_id", $id)->get();

            if (count($tbl) > 0) {
                return response()->json(['error' => 'To delete splitter please unpatch all patched ports'], 500);
            } else {
                $tbl1 = splitter::findOrFail($id);
                splitter::destroy($id);
                splitter_port::where("going", "splitter")
                    ->where("going_id", $id)
                    ->delete();
                $this
                    ->log(
                        Carbon::now(),
                        "",
                        "",
                        $this->cname,
                        "destroy",
                        "message",
                        "delete splitter id " . $id .
                            "\nOld splitter: " . $tbl1
                    );
                return "ok";
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function modiStore(Request $request)
    {
        try {
            DB::beginTransaction();
            $attach_id = $request->attach_id;
            if ($request->attach_to == "closure") {
                $temp = (object) $request->closure;
                if ($temp->isNew == "0") {
                    $closure = new closure;
                    $closure->closure_type_id = $temp->closure_type_id;
                    $closure->name = $temp->name;
                    $closure->lat = $temp->lat;
                    $closure->lng = $temp->lng;
                    $closure->created_at = Carbon::now();
                    $closure->updated_at = Carbon::now();
                    $closure->save();
                    $attach_id = $closure->id;
                }
            }
            $data = new splitter;
            $data->attach_to = $request->attach_to;
            $data->attach_id = $attach_id;
            $data->splitter_type_id = $request->splitter_type_id;
            $data->port_type = $request->port_type;
            $data->parent =  $request->parent;
            $data->parent_id =  $request->parent_id;
            $data->attached_port =  $request->attached_port;
            $data->status =  $request->status;
            $data->created_at = Carbon::now();
            $data->updated_at = Carbon::now();
            $data->save();

            if ($request->splitter_port != null) {
                $sport = (object) $request->splitter_port;
                $splitter_port = new splitter_port;
                $splitter_port->splitter_id = $sport->splitter_id;
                $splitter_port->port_number = $sport->port_number;
                $splitter_port->going = $sport->going;
                $splitter_port->going_id = $data->id;
                $splitter_port->los = $sport->los;
                $splitter_port->save();
            }


            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new splitter: " . $data
            );
            DB::commit();
            return $this->show_lcp($request->parent_id);
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
            DB::rollBack();
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
