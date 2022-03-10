<?php

namespace App\Http\Controllers;

use App\Package;
use App\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PackageController extends Controller
{
    private $cname = "PackageController";
    public function index()
    {
        $Package = Package::with(['package_type'])->orderBy('name', 'DESC')->get();

        return response()->json($Package);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = Package::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Package: " . $data
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

    public function show(package $package)
    {
        //
    }

    public function edit(package $package)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = Package::findOrFail($id);
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
                "update Package id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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

            $tbl = Client::where('package_id', $id)->first();


            if (empty($tbl)) {

                $tbl1 = Package::findOrFail($id);
                Package::destroy($id);

                \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Package id " . $id .
                        "\nOld Package: " . $tbl1
                );

                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this package, It\'s still in use.'], 500);
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
