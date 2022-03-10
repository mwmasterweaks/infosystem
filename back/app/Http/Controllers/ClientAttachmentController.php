<?php

namespace App\Http\Controllers;

use App\Http\Requests\clientAttachmentRequest;
use App\client_attachment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientAttachmentController extends Controller
{
    private $cname = "ClientAttachmentController";
    public function index()
    {
        $tbl = client_attachment::all();

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }

    public function store(clientAttachmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $file_name = "noattachment";
            $t = time();

            if ($request->attachment != "") {
                $exploded = explode(',', $request->attachment);
                $decoded = base64_decode($exploded[1]);
                $file_name = str_random() . $t;
                $path = public_path() . "\\attachments\\" . $file_name . "." . $request->file_ext;

                file_put_contents($path, $decoded);
            }
            //return $request;

            $data = client_attachment::create($request->except('file_name') + [
                "file_name" => $file_name
            ]);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new client_attachment: " . $data
            );

            DB::commit();
            return $this->show($request->client_id);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
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

    public function show($client_id)
    {
        try {
            $data = client_attachment::where('client_id', $client_id)->get();
            return response()->json($data);
        } catch (\Illuminate\Database\QueryException $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function edit(client_attachment $client_attachment)
    {
        //
    }

    public function update(clientAttachmentRequest $request, $id)
    {
        try {
            $cmd  = client_attachment::findOrFail($id);
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
                "update client_attachment id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = client_attachment::findOrFail($id);
            client_attachment::destroy($id);
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
}
