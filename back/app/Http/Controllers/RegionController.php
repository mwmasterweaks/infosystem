<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{

    private $cname = "RegionController";
    public function index()
    {
        $tbl = Region::with(['user', 'visor', 'area.branches'])->get();
        $retval = [];
        foreach ($tbl as $item) {
            $user = (object) $item->user;
            if ($user != null) {
                $item->rm_name = $user->name;
                $item->rm_email = $user->email;
            }

            $visor = (object) $item->visor;
            if ($visor != null) {
                $item->visor_name = $visor->name;
                $item->visor_email = $visor->email;
            }
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
            $data = Region::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Region: " . $data
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

    public function show(Region $Region)
    {
        //
    }

    public function edit(Region $Region)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = Region::findOrFail($id);
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
                "update Region id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = Region::findOrFail($id);
            Region::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Region id " . $id .
                    "\nOld Region: " . $tbl1
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
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
