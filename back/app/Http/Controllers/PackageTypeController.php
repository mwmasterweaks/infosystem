<?php

namespace App\Http\Controllers;

use App\Package_type;
use App\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PackageTypeController extends Controller
{
    private $cname = "PackageTypeController";
    public function index()
    {
        $Package_type = Package_type::all();

        return response()->json($Package_type);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = Package_type::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Package_type: " . $data
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
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

    public function show(package_type $package_type)
    {
        //
    }

    public function edit(package_type $package_type)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = Package_type::findOrFail($id);
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
                "update Package_type id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
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

            $tbl = Package::where('package_type_id', $id)->first();

            if (empty($tbl)) {
                $tbl1 = Package_type::findOrFail($id);
                Package_type::destroy($id);
                \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Package_type id " . $id .
                        "\nOld Package_type: " . $tbl1
                );

                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this package type, It\'s still in use.'], 500);
            }
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
            return response()->json(['error' => 'There was a problem deleting this Package type.'], 500);
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
