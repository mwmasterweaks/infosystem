<?php

namespace App\Http\Controllers;

use App\hardfiber;
use App\hardfiber_coordinate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HardfiberController extends Controller
{
    private $cname = "HardfiberController";

    public function index()
    {
        $tbl = hardfiber::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //return $request;
        try {
            $data = hardfiber::create($request->all());

            $coor = [];
            foreach ($request->coordinates as $item) {
                $item = (object) $item;
                $temp = ["hardfiber_id" => $data->id, "lat" => $item->lat, "lng" => $item->lng];
                array_push($coor, $temp);
            }

            hardfiber_coordinate::insert($coor);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new hardfiber: " . $data
            );

            return response()->json($data->id);
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
    }
    public function edit(hardfiber $hardfiber)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = hardfiber::findOrFail($id);
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
                "update hardfiber id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = hardfiber::findOrFail($id);
            hardfiber::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete hardfiber id " . $id .
                    "\nOld hardfiber: " . $tbl1
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
