<?php

namespace App\Http\Controllers;

use App\activity_ticket;
use App\billing;
use App\Package;
use App\Client;
use App\logs;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityTicketController extends Controller
{

    private $cname = "ActivityTicketController";
    private $mainComand;
    public function __construct()
    {
        $this->mainComand = activity_ticket::with(['ticket_type', 'client.branch', 'created_by', 'updated_by', "remarks_log.user", "remarks_log.replies.user"]);
    }

    public function index()
    {
        $tbl = (clone $this->mainComand)
            ->where("status", "!=", "Completed")
            ->orderBy('created_at')
            ->get();

        return $this->ForQuery($tbl);
    }

    public function subIndex($role)
    {
        if ($role == "admin") {
            $tbl = (clone $this->mainComand)
                ->where("status", "!=", "Completed")
                ->orderBy('created_at')
                ->get();
        } else {
            $tbl = (clone $this->mainComand)
                ->where("state", $role)
                ->where("status", "!=", "Completed")
                ->orderBy('created_at')
                ->get();
        }

        return $this->ForQuery($tbl);
    }

    public function search_data($by, $search)
    {
        $tbl = (clone $this->mainComand)
            ->join("clients", "activity_tickets.client_id", "clients.id")
            ->join("activity_ticket_types", "activity_tickets.ticket_type_id", "activity_ticket_types.id")
            ->where($by, 'like', '%' . $search . '%')
            ->select("activity_tickets.*")
            ->orderBy('activity_tickets.created_at')
            ->get();

        return $this->ForQuery($tbl);
    }

    public function ForQuery($tbl)
    {
        $temp = [];
        foreach ($tbl as $item) {
            //count aging
            foreach ($item->remarks_log as $r_log) {
                $r_log->commentVisibility = 'hide';
            }
            $dt = new Carbon($item->created_at);
            $dtnow = Carbon::now();
            $aging = $dt->diffInDays($dtnow, false);
            $item->aging = $aging;
            $item->check = false;

            //add updated package to item
            if ($item->ticket_type_id == 4 || $item->ticket_type_id == 5) {
                $remarks = explode(":", $item->remarks);
                $pack = Package::with(['package_type'])->where("name", $remarks[1])->first();
                $item->packageToUpdate = $pack;
            }
            array_push($temp, $item);
        }
        $c1 = collect();
        $c1->put('items', $temp);
        // $c1->put('pendingCount', $pendingCount);
        // $c1->put('urgentCount', $urgentCount);
        // $c1->put('techVisitCount', $techVisitCount);
        // $c1->put('itndCount', $itndCount);
        // $c1->put('transferCount', $transferCount);
        // $c1->put('data', $data);

        return response()->json($c1);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $tbl = activity_ticket::where('client_id', $request->client_id)
                ->where('status', 'Pending')
                ->first();
            if ($tbl != null)
                return response()->json(['error' => "Client has already job order request"], 500);

            $data = activity_ticket::create($request->all());

            logs::create([
                'user_id' => $request->user_id,
                'controller' => $this->cname,
                'function_name' => 'store',
                'action' => 'Create new activity_ticket',
                'source_table' => 'activity_tickets',
                'source_id' => $data->id,
                'data' => $data,
            ]);
            // \Logger::instance()->log(
            //     Carbon::now(),
            //     $request->user_id,
            //     $request->user_name,
            //     $this->cname,
            //     "store",
            //     "message",
            //     "Create new activity_ticket: " . $data
            // );
            DB::commit();
            return $this->index();
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
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

    public function show(activity_ticket $activity_ticket)
    {
        //
    }

    public function edit(activity_ticket $activity_ticket)
    {
        //
    }

    public function update(Request $request, $id)
    {

        try {
            $cmd  = activity_ticket::findOrFail($id);
            $logFrom = $cmd->replicate();

            $logTo = DB::table("activity_tickets")
                ->where("id", $id)
                ->update([
                    'ticket_type_id' => $request->ticket_type_id,
                    'updated_by' => $request->updated_by,
                    'status' => $request->status,
                    'state' => $request->state,
                    'remarks' => $request->remarks,
                    'updated_at' => Carbon::now(),
                ]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update activity_ticket id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->subIndex($request->role);
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
            activity_ticket::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {

            return response()->json(['error' => 'There was a problem deleting this type.'], 500);
        }
    }

    public function updateClientStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $activity_ticket = (object)$request->activity_ticket;
            DB::table("clients")
                ->where("id", $id)
                ->update([
                    $request->row => $request->data,
                ]);

            DB::table("activity_tickets")
                ->where("id", $activity_ticket->id)
                ->update([
                    'status' => 'Completed',
                    'updated_by' => $request->user_id,
                    'date_applied' => Carbon::now(),
                ]);


            logs::create([
                'user_id' => $request->user_id,
                'controller' => $this->cname,
                'function_name' => 'updateClientStatus',
                'action' => 'Update status to ' . $request->data,
                'source_table' => 'clients',
                'source_id' => $id,
                'data' => '',
            ]);
            // \Logger::instance()->log(
            //     Carbon::now(),
            //     $request->user_id,
            //     $request->user_name,
            //     $this->cname,
            //     "updateClientStatus",
            //     "message",
            //     "activity_ticket_id: " . $activity_ticket->id
            // );

            DB::commit();
            return $this->subIndex($request->role);
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateClientStatus",
                "Error",
                $ex->getMessage()
            );

            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function getTempDiscon()
    {
        set_time_limit(0);
        try {
            $dateNow = Carbon::now();
            $date8daysAfter = $dateNow->copy()->subDays(8);
            $c = collect();

            $tbl8daysAfter = billing::where("date", $date8daysAfter->toDateString())
                // ->whereHas("client", function ($query) {
                //     $query->where("package_type_id", "4");
                // })
                ->groupBy('client_id')
                ->get();
            $data = [];
            foreach ($tbl8daysAfter as $item) {
                $tbl = billing::with(["client"])
                    ->where("client_id", $item->client_id)
                    ->where("date", "<=", $date8daysAfter)
                    ->groupBy("client_id")
                    ->selectRaw("*, sum(balance) as sum")
                    ->first();
                if ($tbl->sum > 0) {

                    $tbl = activity_ticket::where('client_id', $item->client_id)
                        ->where('status', 'Verification')
                        ->where('ticket_type_id', 1)
                        ->first();

                    $temp = [
                        "ticket_type_id" => 1,
                        "client_id" => $item->client_id,
                        "created_by" => 1,
                        "status" => "Verification",
                        "state" => "accounting",
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ];
                    if ($tbl == null)
                        array_push($data, $temp);
                }
            }
            if (count($data) > 0)
                activity_ticket::insert($data);

            return $data;
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex], 500);
        }
    }

    public function verify(Request $request)
    {
        DB::beginTransaction();
        try {

            $verifySelected = $request->verifySelected;
            $log_msg = "";
            foreach ($verifySelected as $item) {
                $item = (object)$item;
                DB::table("activity_tickets")
                    ->where("id", $item->id)
                    ->update([
                        'status' => 'Pending',
                        'state' => 'helpdesk',
                        'updated_by' => $request->user_id,
                        'updated_at' => Carbon::now(),
                    ]);
                $log_msg .= $item->id . ', ';
            }

            logs::create([
                'user_id' => $request->user_id,
                'controller' => $this->cname,
                'function_name' => 'verify',
                'action' => 'verify selected JO ',
                'source_table' => 'activity_tickets',
                'source_id' => $log_msg,
                'data' => '',
            ]);
            // \Logger::instance()->log(
            //     Carbon::now(),
            //     $request->user_id,
            //     $request->user_name,
            //     $this->cname,
            //     "verify",
            //     "message",
            //     "verify selected: " . $log_msg
            // );
            DB::commit();
            return $this->subIndex($request->role);
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "verify",
                "Error",
                $ex->getMessage()
            );

            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function updateSOA(Request $request)
    {
        try {
            $id = $request->id;
            $activity_ticket_id = $request->activity_ticket_id;
            DB::beginTransaction();
            $tbl1 = Client::where("id", $id)
                ->select("package_id", "package_type_id")
                ->first();
            $oldData = new \stdClass;
            $oldData->data = "package_id: " . $tbl1->package_id . ", package_type_id:" . $tbl1->package_type_id;
            $data = (object) $request->data;
            DB::table("clients")
                ->where("id", $id)
                ->update([
                    "package_id" => $data->package_id,
                    "package_type_id" => $data->package_type_id
                ]);

            foreach ($data->soa_items as $item) {
                $item = (object) $item;
                if ($item->isSelected) {
                    DB::table("billings")
                        ->where("id", $item->id)
                        ->update([
                            "description" => $item->description,
                            "price" => $item->price_update,
                            "balance" => $item->balance_update
                        ]);
                }
            }

            //UPDATE ACTIVITYTICKET
            DB::table("activity_tickets")
                ->where("id", $activity_ticket_id)
                ->update([
                    'status' => 'Completed',
                    'updated_by' => $request->user_id,
                    'date_applied' => Carbon::now(),
                ]);


            $newData = new \stdClass;
            $newData->data = $data->package_id . ", " . $data->package_type_id;

            // \Logger::instance()->log(
            //     Carbon::now(),
            //     $request->user_id,
            //     $request->user_name,
            //     $this->cname,
            //     "updateSOA",
            //     "message",
            //     "update client id " . $id .
            //         "\nFrom: " . $oldData->data . "\nTo: " . $newData->data
            // );

            logs::create([
                'user_id' => $request->user_id,
                'controller' => $this->cname,
                'function_name' => 'updateSOA',
                'action' => 'Update Client Package',
                'source_table' => 'clients',
                'source_id' => $id,
                'data' => "From: " . $oldData->data . "\nTo: " . $newData->data,
            ]);

            DB::commit();
            return "ok";
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateSOA",
                "Error",
                $ex->getMessage()
            );

            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
}
