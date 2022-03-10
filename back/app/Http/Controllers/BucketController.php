<?php

namespace App\Http\Controllers;

use App\bucket;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\SSH2;

class BucketController extends Controller
{
    private $cname = "BucketController";
    public function index()
    {
        $tbl = bucket::all();

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
            $data = bucket::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_log,
                $request->user_name_log,
                $this->cname,
                "store",
                "message",
                "Create new bucket: " . $data
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
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

    public function show(bucket $bucket)
    {
        //
    }

    public function edit(bucket $bucket)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = bucket::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();
            $cmd->fill($input)->save();
            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_log,
                $request->user_name_log,
                $this->cname,
                "update",
                "message",
                "update bucket id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_log,
                $request->user_name_log,
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
            $tbl1 = bucket::findOrFail($id);
            bucket::destroy($id);
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete olt id " . $id .
                    "\nOld olt: " . $tbl1
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
            return response()->json(['error' => 'There was a problem deleting this business type.'], 500);
        }
    }

    public function ssh_command(Request $request)
    {
        try {
            $bucket = (object) $request->bucket;
            $ssh = new SSH2($bucket->IP);
            $table = [];
            $msg = "";
            if ($ssh->auth($bucket->username, $bucket->password)) {
                $ssh->exec("export BASS_RDIR='/usr/local/foxbuckv3';/bin/bash /usr/local/foxbuckv3/commands/cbs " .
                    $bucket->user_id . " 10 0 1 " . $request->command);
                $ssh_table =  $ssh->getTable();
                if (count($ssh_table) > 0) {
                    $table = $ssh_table;
                } else {
                    $msg =  $ssh->output();
                }
            } else {
                $msg = "Username password incorrect";
            }
            // $ssh->disconnect();
            $c = collect();
            $c->put('table', $table);
            $c->put('message', $msg);
            return $c;
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

    public function encryptIt($q)
    {
        // $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        // $qEncoded      = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
        // return ($qEncoded);
    }

    public function decryptIt($q)
    {
        // $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        // $qDecoded      = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
        // return ($qDecoded);
    }
}
