<?php

namespace App\Http\Controllers;

use App\Engineer;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Contracts\View\Engine;

class EngineerController extends Controller
{
    private $cname = "EngineerController";

    public function index()
    {
        $Engineer = Engineer::with('user')->get();

        foreach ($Engineer as $item) {
            unset($item->name);
            $user = $item->user;
            // $item->asdasda = $user['name'];
            // array_push($ret, $item);
            $item->name = $user['name'];
        }
        return response()->json($Engineer);
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        try {

            $tbl = User::select('name')->where('id', $request->engineer_id)->value('name');

            $data = Engineer::insert([
                'user_id' => $request->engineer_id,
                'name' => $tbl,
                'position' => $request->position,
                'signature' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Engineer: " . $data
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


    public function show(engineer $engineer)
    {
        //
    }

    public function edit(engineer $engineer)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $fileName = null;
            if ($request->signature != "") {

                $exploded = explode(',', $request->signature);

                $decoded = base64_decode($exploded[1]);

                // if (str_contains($exploded[0], "jpeg"))
                //     $extension = "jpeg";
                // elseif (str_contains($exploded[0], "jpg"))
                //     $extension = "jpg";
                // elseif (str_contains($exploded[0], "png"))
                //     $extension = "png";
                // elseif (str_contains($exploded[0], "gif"))
                //     $extension = "gif";
                // elseif (str_contains($exploded[0], "tiff"))
                //     $extension = "tiff";
                // elseif (str_contains($exploded[0], "bmp"))
                //     $extension = "bmp";
                // else
                $extension = "jpg";

                $fileName = "signature" . $id . "." . $extension;

                $path = public_path() . "/imgs/" . $fileName;

                file_put_contents($path, $decoded);
            }

            $logFrom  = Engineer::findOrFail($id);

            $dd = [
                'position' => $request->position,
                'signature' => $fileName,
                'updated_at' => Carbon::now(),

            ];

            $logTo = Engineer::where('id', $id)
                ->update($dd);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Engineer id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = Engineer::findOrFail($id);
            Engineer::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Engineer id " . $id .
                    "\nOld Engineer: " . $tbl1
            );
            return response()->json($this->index());
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
            return response()->json(['error' => 'There was a problem deleting this engineer.'], 500);
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
