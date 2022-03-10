<?php

namespace App\Http\Controllers;

use App\splitter_port;
use App\splitter;
use App\Client;
use App\closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use stdClass;

class SplitterPortController extends Controller
{
    private $cname = "SplitterPortController";

    public function index()
    {
        $tbl = splitter_port::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            // return response()->json($request);
            // $data = splitter_port::create($request->all());
            $data = splitter_port::where('going_id', '=', $request->going_id)->first();

            if (empty($data)) {
                $data = splitter_port::create($request->all());
            } else {
                return response()->json("Error");
            }
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new splitter_port: " . $data
            );
            return $this->show($data->splitter_id);
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
        $splitter = splitter::findOrFail($id);
        $x = 0;

        $retval = [];
        if ($splitter->port_type == "1x4")
            $x = 4;
        if ($splitter->port_type == "1x8")
            $x = 8;
        if ($splitter->port_type == "1x16")
            $x = 16;

        for ($i = 1; $i <= $x; $i++) {

            $tbl = splitter_port::where("splitter_id", $id)
                ->where("port_number", $i)
                ->first();;
            if ($tbl == null) {
                $container = new stdClass;
                $container->splitter_id = $id;
                $container->port_number = $i;
                $container->going = "VACANT";
                $container->name = "VACANT";
                $container->status = "VACANT";
                $container->going_id = "";
                $container->los = 0;
                array_push($retval, $container);
            } else {

                if ($tbl->going == "Client") {
                    $client = Client::where("id", $tbl->going_id)->first();
                    $name = $client->name;
                    $status = $client->status;
                    $tbl->name = $client->name;
                    $tbl->client = $client;
                }
                if ($tbl->going == "splitter") {
                    $split = splitter::with(['closure'])->where("id", $tbl->going_id)
                        ->where("attach_to", "closure")
                        ->first();
                    $closure = (object) $split->closure;
                    $name = $closure->name;
                    $status = $split->status;
                }
                $tbl->attach_to = $name;
                $tbl->status = $status;
                array_push($retval, $tbl);
            }
        }

        return response()->json($retval);
    }
    public function edit(splitter_port $splitter_port)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = splitter_port::findOrFail($id);
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
                "update splitter_port id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = splitter_port::findOrFail($id);
            splitter_port::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete splitter_port id " . $id .
                    "\nOld splitter_port: " . $tbl1
            );
            return $this->index();
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
