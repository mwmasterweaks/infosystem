<?php

namespace App\Http\Controllers;

use App\mac_table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MacTableController extends Controller
{
    private $cname = "MacTableController";
    public function index()
    {
        $tbl = mac_table::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {



            $location = (object) $request->location;
            $deploy = (object) $request->deploy;
            $device = (object) $request->device;
            $mac_address_main = "D6:7E:C" . $location->hex_number . ":" . $deploy->hex_number . $device->hex_number;
            $mac_address_first = $mac_address_main;
            $max_second = mac_table::where('mac_address_first', $mac_address_first)
                ->max('mac_address_second');
            if ($max_second <= 65535) {


                if ($max_second == null)
                    $max_second = 1;
                else
                    $max_second += 1;

                $hex_second = dechex($max_second);
                $char_count = strlen($hex_second);
                if ($char_count == 1)
                    $mac_address_main .= ":00:0" . $hex_second;
                if ($char_count == 2)
                    $mac_address_main .= ":00:" . $hex_second;
                if ($char_count == 3)
                    $mac_address_main .= ":0" . substr($hex_second, 0, 1) . ":" .  substr($hex_second, 1, 3);
                if ($char_count == 4)
                    $mac_address_main .= ":" . substr($hex_second, 0, 2) . ":" .  substr($hex_second, 2, 4);

                $check_exist = mac_table::where('mac_address_main', $mac_address_main)
                    ->first();
                //return $request;
                if (empty($check_exist)) {
                    $tbl = new mac_table;
                    $tbl->pmx_ip = $request->pmx_ip;
                    $tbl->ip = $request->ip;
                    $tbl->mac_desc = $request->mac_desc;
                    $tbl->port_desc = $request->port_desc;
                    $tbl->mac_address_main = $mac_address_main;
                    $tbl->mac_address_first = $mac_address_first;
                    $tbl->mac_address_second = $max_second;
                    $tbl->save();
                    \Logger::instance()->log(
                        Carbon::now(),
                        $request->user_id,
                        $request->user_name,
                        $this->cname,
                        "store",
                        "message",
                        "Create new mac_table: " . $tbl
                    );
                    return $this->index();
                } else {
                    return response()->json(['error' => 'Can\'t Generate Already has max Mac or has duplicate Mac'], 500);
                }
            } else {
                return response()->json(['error' => 'Can\'t Generate Already has max Mac or has duplicate Mac'], 500);
            }
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

    public function show(mac_table $mac_table)
    {
        //
    }

    public function edit(mac_table $mac_table)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $tbl  = mac_table::findOrFail($id);
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
                "update mac_table id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = mac_table::findOrFail($id);
            mac_table::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete mac_table id " . $id .
                    "\nOld mac_table: " . $tbl1
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
            return response()->json(['error' => 'There was a problem deleting this Package.'], 500);
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
