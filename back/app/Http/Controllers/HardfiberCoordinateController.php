<?php

namespace App\Http\Controllers;

use App\hardfiber_coordinate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use stdClass;

class HardfiberCoordinateController extends Controller
{
    private $cname = "HardfiberCoordinateController";

    public function index()
    {
        $tbl = hardfiber_coordinate::orderBy('id')->get();

        $retVal = [];
        $storeCoor = [];
        $temp_id = "";
        $chk = 0;
        $id = 0;
        $counttbl = count($tbl);
        foreach ($tbl as $item) {


            if ($item->hardfiber_id != $temp_id) {

                if ($chk != 0) {
                    $path = new stdClass;
                    $path->hardfiber_id = $id;
                    $path->coor = $storeCoor;
                    array_push($retVal, $path);
                }
                $id = $item->hardfiber_id;
                $temp_id = $item->hardfiber_id;
                $storeCoor = [];
                $coor = new stdClass;
                $coor->lat = (float) $item->lat;
                $coor->lng = (float) $item->lng;
                array_push($storeCoor, $coor);
            } else {
                $coor = new stdClass;
                $coor->lat = (float) $item->lat;
                $coor->lng = (float) $item->lng;
                array_push($storeCoor, $coor);
            }
            if (($counttbl - 1) == $chk) {
                $path = new stdClass;
                $path->hardfiber_id = $id;
                $path->coor = $storeCoor;
                array_push($retVal, $path);
            }
            $chk++;
        }
        return response()->json($retVal);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = hardfiber_coordinate::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new hardfiber_coordinate: " . $data
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
    }
    public function edit(hardfiber_coordinate $hardfiber_coordinate)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = hardfiber_coordinate::findOrFail($id);
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
                "update hardfiber_coordinate id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = hardfiber_coordinate::findOrFail($id);
            hardfiber_coordinate::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete hardfiber_coordinate id " . $id .
                    "\nOld hardfiber_coordinate: " . $tbl1
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
