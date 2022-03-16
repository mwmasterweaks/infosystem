<?php

namespace App\Http\Controllers;

use App\Job_order;
use Illuminate\Http\Request;
use App\Client_detail;
use App\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JobOrderController extends Controller
{
    private $cname = "JobOrderController";
    public function index()
    {
        try {
            $tbl = Job_order::with(['region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])->orderBy('id', 'DESC')
                ->get();
            $temp = [];
            foreach ($tbl as $item) {
                if ($item->client_id == "0") {
                    $c = collect();
                    $c->put('name', $item->details);
                    $c->put('location',  $item->location);
                    $item->client = $c;
                } else {
                    $client = Client::where('id', $item->client_id)->first();
                    $item->client = $client;
                }

                if ($item->client_detail_id != null) {
                    $client_details = Client_detail::where('id', $item->client_detail_id)->first();
                    $item->client_details = $client_details;
                } else
                    $item->client_details = null;

                if ($item->finished != null) {
                    $date_finished_formated = new Carbon($item->finished);
                    $item->date_finished_formated = $date_finished_formated->toFormattedDateString();
                }
                if ($item->started != null) {
                    $date_started_formated = new Carbon($item->started);
                    $item->date_started_formated = $date_started_formated->toFormattedDateString();
                }
                $item->jo_num1 = $item->region_id . $item->jo_num;
                array_push($temp, $item);
            }
            return response()->json($temp);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function subIndex($region_id)
    {
        try {
            $tbl = "";
            if ($region_id == 0) {
                $tbl = Job_order::with(['client', 'region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])

                    ->orderBy('id', 'DESC')
                    ->take(250)
                    ->get();
            } else {
                $tbl = Job_order::with(['client', 'region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])

                    ->orderBy('id', 'DESC')
                    ->take(250)
                    ->get();
                // ->where('region_id', $region_id)
            }

            return $this->ForQuery($tbl);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function search_data($by, $search)
    {
        try {
            $tbl = Job_order::with(['region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])
                ->leftJoin("clients", "job_orders.client_id", "clients.id")
                ->select("clients.*", "job_orders.*", "job_orders.location as jo_location")
                ->orderBy('job_orders.id', 'DESC')
                ->where($by, 'like', '%' . $search . '%')
                ->get();

            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function ForQuery($tbl)
    {
        $temp = [];

        foreach ($tbl as $item) {
            if ($item->client == null) {
                unset($item->client);
                if ($item->details == null)
                    $item->details = " ";
                if ($item->location == null)
                    $item->location = " ";
                $c = collect();
                $c->put('name', $item->details);
                $c->put('location',  $item->jo_location);
                $item->client = $c;
            }
            // else {
            //     $client = Client::where('id', $item->client_id)->first();
            //     $item->client = $client;
            // }

            if ($item->client_detail_id != null) {
                $client_details = Client_detail::where('id', $item->client_detail_id)->first();
                $item->client_details = $client_details;
            } else
                $item->client_details = null;

            if ($item->finished != null) {
                $date_finished_formated = new Carbon($item->finished);
                $item->date_finished_formated = $date_finished_formated->toFormattedDateString();
            } else
                $item->date_finished_formated = "";

            if ($item->started != null) {
                $date_started_formated = new Carbon($item->started);
                $item->date_started_formated = $date_started_formated->toFormattedDateString();
            } else
                $item->date_started_formated = "";

            $item->jo_num = $item->region_id . $item->jo_num;
            array_push($temp, $item);
        }
        return response()->json($temp);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //return response()->json($request);
        try {
            $jo = new Job_order;

            if ($request->id != null)
                $jo->id = $request->id;
            $jo->jo_num = $this->getMaxID($request->region_id) + 1;
            $jo->client_id = $request->client_id;
            $jo->region_id = $request->region_id;
            $jo->details = $request->details;
            $jo->location = $request->location;
            $jo->project_description = $request->project_description;
            $jo->cable_category = $request->cable_category;
            $jo->foc_length = $request->foc_length;
            $jo->started = $request->started;
            $jo->finished = $request->finished;
            $jo->engineer_in_charge = $request->engineer_in_charge;
            $jo->prepare = $request->prepare;
            $jo->approve = $request->approve;
            $jo->note = $request->note;

            $jo->save();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new job order: " . $jo
            );

            return $this->subIndex($request->region_id1);
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

    public function show(Request $request, $client_id)
    {
        try {
            //return $client_id;
            $tbl = Job_order::where('client_id', $client_id)->get();
            return response()->json($tbl);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function edit(job_order $job_order)
    {
        //
    }

    public function update(Request $request, $id)
    {

        try {
            $request->region = (object) $request->region;

            $prepare = (object) $request->prepare_engineer;
            $approve = (object) $request->approve_engineer;
            $note = (object) $request->note_engineer;
            $engineer = (object) $request->engineer;
            $logFrom  = Job_order::findOrFail($id);

            Job_order::where('id', $id)
                ->update([
                    'client_id' => $request->client_id,
                    'client_detail_id' =>  $request->client_detail_id,
                    'details' => $request->details,
                    'location' => $request->location,
                    'cable_category' => $request->cable_category,
                    'foc_length' => $request->foc_length,
                    'region_id' => $request->region_id,
                    'project_description' => $request->project_description,
                    'started' => $request->started,
                    'finished' => $request->finished,
                    'engineer_in_charge' => $engineer->id,
                    'prepare' => $prepare->id,
                    'approve' => $approve->id,
                    'note' => $note->id
                ]);
            $logTo  = Job_order::findOrFail($id);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update region id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            if ($request->update_in == "schedule")
                return $this->getJobOrder($id);
            else
                return $this->subIndex($request->region_id);
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
            Job_order::destroy($id);
            return $this->index();
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was a problem deleting this thing.'], 500);
        }
    }

    public function destroy1(Request $request)
    {
        try {
            $tbl1  = Job_order::findOrFail($request->id);
            Job_order::destroy($request->id);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroy1",
                "message",
                "delete Job_order id " . $request->id .
                    "\nOld Job_order: " . $tbl1
            );
            return $this->subIndex($request->region_id);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroy1",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => 'There was a problem deleting this thing.'], 500);
        }
    }

    public function getJobOrder($id)
    {
        try {
            //return $client_id;
            $tbl = Job_order::with(['client', 'region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])->where('id', $id)->get();
            $temp = [];
            foreach ($tbl as $item) {
                $client_detail = Client_detail::where('client_id', $item->client_id)->get();
                $item->client_detail = $client_detail[0];
                array_push($temp, $item);
            }
            return response()->json($temp);
        } catch (\Illuminate\Database\QueryException $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "getJobOrder",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function filterByDate(Request $request)
    {
        try {
            if ($request->end == null)
                $request->end = $request->start;

            $from = new Carbon($request->start);
            $to = new Carbon($request->end);
            // $to = $to->addDay();

            $tbl = Job_order::with(['region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])->orderBy('id', 'DESC')
                ->whereBetween("finished", [$from, $to])
                ->get();
            $temp = [];
            foreach ($tbl as $item) {
                if ($item->client_id == "0") {
                    $c = collect();
                    $c->put('name', $item->details);
                    $c->put('location',  $item->location);
                    $item->client = $c;
                } else {
                    $client = Client::where('id', $item->client_id)->first();
                    $item->client = $client;
                }
                if ($item->finished != null) {
                    $date_finished_formated = new Carbon($item->finished);
                    $item->date_finished_formated = $date_finished_formated->toFormattedDateString();
                }
                if ($item->started != null) {
                    $date_started_formated = new Carbon($item->started);
                    $item->date_started_formated = $date_started_formated->toFormattedDateString();
                }
                array_push($temp, $item);
            }
            return response()->json($temp);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "filterByDate",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function getMaxID($region_id)
    {
        if ($region_id == 0)
            $region_id = 10;
        $tbl = DB::table('job_orders')
            ->where('region_id', $region_id)
            ->max('jo_num');
        if ($tbl == null)
            $tbl = 1000;
        return $tbl;
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
