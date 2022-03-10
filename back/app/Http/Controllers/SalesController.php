<?php

namespace App\Http\Controllers;

use App\Sales;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesController extends Controller
{
    private $cname = "SalesController";
    public function index()
    {
        $tbl = Sales::with(['user', 'agent'])->get();

        $retval = [];
        foreach ($tbl as $item) {
            $user = (object) $item->user;
            $item->name = $user->name;
            $item->email = $user->email;
            array_push($retval, $item);
        }
        return response()->json($retval);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $chk = Sales::where("user_id", $request->user_id)->first();

            if ($chk == null) {
                $data = Sales::create($request->all());
                $this
                    ->log(
                        Carbon::now(),
                        $request->user_id_log,
                        $request->user_name_log,
                        $this->cname,
                        "store",
                        "message",
                        "Create new Sales: " . $data
                    );
                return $this->index();
            } else {
                return response()->json(['error' => "The Sales you select are already in the list."], 500);
            }
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_log,
                $request->user_name_log,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function show(sales $sales)
    {
        //
    }

    public function edit(sales $sales)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = Sales::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();
            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_update,
                $request->user_name_update,
                $this->cname,
                "update",
                "message",
                "update Sales id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_update,
                $request->user_name_update,
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
            $tbl1 = Sales::findOrFail($id);
            Sales::destroy($id);
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Sales id " . $id .
                    "\nOld Sales: " . $tbl1
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
            return response()->json(['error' => 'There was a problem deleting the sales.'], 500);
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
