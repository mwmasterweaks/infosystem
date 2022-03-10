<?php

namespace App\Http\Controllers;

use App\mac_deployment_identifier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MacDeploymentIdentifierController extends Controller
{
    private $cname = "MacDeploymentIdentifierController";
    public function index()
    {
        $tbl = mac_deployment_identifier::all();

        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $model = new mac_deployment_identifier;
            $model->name = $request->name;
            $model->hex_number = $request->hex_number;
            $model->timestamps = false;
            $model->save();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new mac_deployment_identifier: " . $model
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

    public function show(mac_deployment_identifier $mac_deployment_identifier)
    {
        //
    }

    public function edit(mac_deployment_identifier $mac_deployment_identifier)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $tbl  = mac_deployment_identifier::findOrFail($id);
            $logFrom = $tbl->replicate();
            $input = $request->all();
            $tbl->timestamps = false;
            $tbl->fill($input)->save();
            $logTo = $tbl;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update mac_deployment_identifier id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = mac_deployment_identifier::findOrFail($id);
            mac_deployment_identifier::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete mac_deployment_identifier id " . $id .
                    "\nOld mac_deployment_identifier: " . $tbl1
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
