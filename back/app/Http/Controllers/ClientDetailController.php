<?php

namespace App\Http\Controllers;

use App\Client_detail;
use App\Client;
use App\Ticket;
use App\Job_order;
use App\schedule;
use App\cvc;
use App\billing;
use App\Sales;
use App\Region;
use App\Engineer;
use App\customer_payment;
use App\Helpers\SSH2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use stdClass;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ClientDetailController extends Controller
{
    private $cname = "ClientDetailController";
    public function index()
    {
    }
    public function subIndex($region_id)
    {
        try {
            //return $region_id;
            $tbl = "";
            if ($region_id == 0) {
                $tbl = Client_detail::with(['remarks_log.user'])
                    ->join("clients", "client_details.client_id", "clients.id")
                    ->leftJoin("schedules", function ($join) {

                        $join->on('client_details.id', '=', 'schedules.client_detail_id');

                        $join->on('client_details.target_date', '=', 'schedules.target_date');
                    })
                    ->leftJoin('teams', "schedules.team_id", "teams.id")
                    ->select("clients.*", "client_details.*", "teams.user_id as team_user_id")
                    ->whereNull('client_details.date_activated')
                    ->orderByRaw("CASE WHEN client_details.target_date IS NULL THEN 1 ELSE 0 END, client_details.target_date")
                    ->take(100)
                    ->get();
            } else {

                $tbl = Client_detail::with(['remarks_log.user'])
                    ->join("clients", "client_details.client_id", "clients.id")
                    ->leftJoin("schedules", function ($join) {

                        $join->on('client_details.id', '=', 'schedules.client_detail_id');

                        $join->on('client_details.target_date', '=', 'schedules.target_date');
                    })
                    ->leftJoin('teams', "schedules.team_id", "teams.id")
                    ->select("clients.*", "client_details.*", "teams.user_id as team_user_id")
                    ->whereNull('client_details.date_activated')
                    ->where("region_id", $region_id)
                    ->orderByRaw("CASE WHEN client_details.target_date IS NULL THEN 1 ELSE 0 END, client_details.target_date")
                    ->take(100)
                    ->get();
            }
            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function getAll()
    {
        try {
            $tbl = "";
            $tbl = Client_detail::with(['remarks_log.user'])
                ->join("clients", "client_details.client_id", "clients.id")
                ->leftJoin("schedules", function ($join) {
                    $join->on('client_details.id', '=', 'schedules.client_detail_id');
                    $join->on('client_details.target_date', '=', 'schedules.target_date');
                })
                ->leftJoin('teams', "schedules.team_id", "teams.id")
                ->orderByRaw("CASE WHEN client_details.target_date IS NULL THEN 1 ELSE 0 END, client_details.target_date")
                ->get();
            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function search_data($by, $search)
    {
        try {
            //return $region_id;
            $tbl = "";
            $tbl = Client_detail::with(['remarks_log.user'])
                ->join("clients", "client_details.client_id", "clients.id")
                ->leftJoin("schedules", function ($join) {

                    $join->on('client_details.id', '=', 'schedules.client_detail_id');

                    $join->on('client_details.target_date', '=', 'schedules.target_date');
                })
                ->leftJoin('teams', "schedules.team_id", "teams.id")
                ->select("clients.*", "client_details.*", "teams.user_id as team_user_id")
                ->orderByRaw("CASE WHEN client_details.target_date IS NULL THEN 1 ELSE 0 END, client_details.target_date")
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
        $targetDateSort = 1;
        foreach ($tbl as $item) {
            $id = $item->client_id;
            $Client = Client::with([

                'package', 'modem', 'package_type', 'region', 'sales.user', 'engineer.user',

                'splitter_port.belongs_to_splitter_nap.belongs_to_splitter_lcp.belongs_to_olt.belongs_to_node'

            ])

                ->where('id', $id)

                ->get();





            $foc_check_red = 0;
            $item->foc_check_red = null;
            $foc_dt = new Carbon();
            $item->no_foc_layout_sched = "Filter: no_foc_layout_sched";
            if ($item->foc_plan_duration != null) {

                $foc_dt = new Carbon($item->foc_plan_duration);

                $item->no_foc_layout_sched = "";
            }
            $created_dt = new Carbon($item->created_at);
            $foc_duration = $foc_dt->diffForHumans(

                $created_dt,

                [

                    'parts' => 3,

                    //'short' => true,

                    'syntax' => CarbonInterface::DIFF_ABSOLUTE

                ]

            );
            $item->foc_duration = $foc_duration;
            $foc_check_red = $foc_dt->diffInHours($created_dt);
            $item->foc_check_red = $foc_check_red;
            //$item->foc_duration = null;
            $item->targetDateSort = $targetDateSort;
            $targetDateSort++;
            $item->contract = $Client[0]->contract;
            $item->client1 = $Client[0];
            $c = collect();
            if ($foc_check_red >= 24)
                $c->put('foc_duration', 'danger');
            if (!$item->mapping_status)
                $c->put('mapping_status', 'danger');
            // if (!$item->modem_status)
            //     $c->put('modem_status', 'danger');
            if (
                $item->foc_layout == 'Indoor layout done' ||
                $item->foc_layout == null
            )
                $c->put('foc_layout', 'danger');
            if ($item->foc_layout == 'Outdoor layout done')
                $c->put('foc_layout', 'info');
            if ($item->otc == null || $item->otc == "Waiting for C&C advisory")
                $c->put('otc', 'danger');
            if ($item->cable_category == null)
                $c->put('cable_category', 'danger');
            if ($Client[0]->contract == null)
                $c->put('client1.contract', 'danger');
            $item->_cellVariants = $c;
            $tdate = $item->target_date;
            if ($item->status == null)
                $item->status_filter = "Filter: wfc";
            if ($item->status != null && $item->target_date == null)
                $item->status_filter = "Filter: assign";
            if ($item->status == 'finished')
                $tdate = "9999-11-11";
            $tdateFormated = new Carbon($tdate);
            $c1 = collect();
            $c1->put('status', $item->status);
            //$c1->put('target_date target_date_formated', $tdate);
            $c1->put('target_date', $tdateFormated->toFormattedDateString());
            $c1->put('client_id', $item->client_id);
            $item->status1 = $c1;
            $applied_date_formated = new Carbon($item->applied_date);
            $item->applied_date_formated = $applied_date_formated->toFormattedDateString();
            $item->applied_date_sort = $applied_date_formated->valueOf();
            $agingCount = null;
            if ($item->aging != null) {
                $dt = new Carbon($item->aging);
                $dtnow = Carbon::now();
                if ($item->date_activated != null)
                    $dtnow = new Carbon($item->date_activated);
                $agingCount = -1;
                while ($dt < $dtnow) {
                    //if (!$dt->isSaturday())
                    //if (!$dt->isSunday())
                    $agingCount++;
                    $dt = $dt->addDay();
                }
            }
            $item->agingCount = $agingCount;
            //joborder
            $tbl1 = null;
            $tbl1 = Job_order::with(['client', 'region', 'engineer.user', 'note_engineer.user', 'prepare_engineer.user', 'approve_engineer.user'])
                ->where('client_detail_id', $item->id)
                ->where('project_description', 'Installation')
                ->first();
            if ($tbl1 != null) {
                $JobOrder_finished = new Carbon($tbl1->finished);
                if ($tbl1->finished != null)
                    $item->JobOrder_finished = $JobOrder_finished->toFormattedDateString();
                else
                    $item->JobOrder_finished = "";
                $JobOrder_started = new Carbon($tbl1->started);
                if ($tbl1->started != null)
                    $item->JobOrder_started = $JobOrder_started->toFormattedDateString();
                else
                    $item->JobOrder_started = "";
                $tbl1->jo_num =
                    $tbl1->region_id . $tbl1->jo_num;
                $prepare = $tbl1->prepare_engineer;
                if ($prepare != null) {
                    unset($prepare->name);
                    $prepare->name = $prepare->user->name;
                }
                $approve = $tbl1->approve_engineer;
                if ($approve != null) {
                    unset($approve->name);
                    $approve->name = $approve->user->name;
                }
                $note = $tbl1->note_engineer;
                if ($note != null) {
                    unset($note->name);
                    $note->name = $note->user->name;
                }
            }
            $item->JobOrder = $tbl1;
            //if ($item->date_activated == null)
            array_push($temp, $item);
        }
        $item = $temp;
        return response()->json($item);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //return response()->json($request);
        try {
            $data =  Client_detail::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new schedule: " . $data
            );
            return $this->subIndex($request->region_id);
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
            if (strpos($ex->getMessage(), 'Duplicate entry') !== false) {
                return response()->json(['error' => 'Client has already scheduled'], 500);
            } else {
                return response()->json(['error' => $ex->getMessage()], 500);
            }
        }
    }
    public function storeJobOrder(Request $request)
    {
        try {
            $tbl = DB::table('job_orders')
                ->where('region_id', $request->region_id)
                ->max('jo_num');
            $jo = new Job_order;
            //JO# UPDATE
            if ($request->id != null)

                $jo->id = $request->id;

            $jo->jo_num = $tbl + 1;
            $jo->client_id = $request->client_id;
            $jo->client_detail_id = $request->client_detail_id;
            $jo->region_id = $request->region_id;
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
                "storeJobOrder",
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
                "storeJobOrder",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function show(client_detail $client_detail)
    {
        //
    }
    public function edit(client_detail $client_detail)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {
            $status = $request->status;
            $target_date = $request->target_date;
            if (
                $status == "done" &&
                $request->mapping_status &&
                $request->modem_status
            ) {
                $status = "done";
            } else {
                if (
                    $status == "Confirmed" &&
                    $request->mapping_status &&
                    $request->modem_status
                )
                    $status = "done";
                else {
                    if ($status == "done" || $status == "Confirmed")

                        $status = "Confirmed";

                    else
                        $status = null;
                    // $target_date = null;
                }
            }
            if (
                $request->foc_layout == "Indoor layout done" ||
                $request->foc_layout == null
            ) {
                if ($status == "done" || $status == "Confirmed")
                    $status = "Confirmed";
                else
                    $status = null;
                // $target_date = null;
            }
            // else {
            //     $target_date = null;
            // }
            $aging = $request->aging;
            $otc = $request->otc;
            if (
                $otc == "Waiting for C&C advisory" ||
                $otc == "" ||
                $otc == null
            ) {
                if ($status == "done" || $status == "Confirmed")
                    $status = "Confirmed";
                else
                    $status = null;
                //$target_date = null;
                $aging = null;
            } elseif (
                $otc == "Paid" ||
                $otc == "Promo" ||
                $otc == "Billing" ||
                $otc == "Waived" ||
                $otc == "NTP"
            ) {
                if ($aging == null)
                    $aging = $request->aging;
            }
            $team_assigned = $request->team_assigned;
            if ($status == 'done' && $target_date == null) {
                $team_assigned = 1;
            }
            if ($status != 'done') {
                $team_assigned = 0;
            }
            $logFrom  = Client_detail::findOrFail($id);
            $id_update = $id;
            if ($request->id_update != null)
                $id_update = $request->id_update;
            Client_detail::where('id', $id)
                ->update([
                    'id' => $id_update,
                    'otc' => $request->otc,
                    'contract_status' => $request->contract_status,
                    'mapping_status' => $request->mapping_status,
                    'cable_category' => $request->cable_category,
                    'foc_length' => $request->foc_length,
                    'foc_layout' => $request->foc_layout,
                    'foc_schedule' => $request->foc_schedule,
                    'layout_remarks' => $request->layout_remarks,
                    'modem_status' => $request->modem_status,
                    'applied_date' => $request->applied_date,
                    'inst_remarks' => $request->inst_remarks,
                    'layout_status' => $request->layout_status,
                    //team_assigned are true or false
                    'team_assigned' => $team_assigned,
                    'target_date' => $target_date,
                    'status' => $status,
                    'aging' => $aging
                ]);
            if ($logFrom->foc_plan_duration == null && $request->foc_schedule != null) {
                Client_detail::where('id', $id)

                    ->update([

                        'foc_plan_duration' => Carbon::now()

                    ]);
            }
            $logTo  = Client_detail::findOrFail($id_update);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update region id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            //return $cmd; return $str;
            if ($request->filterIn == "multi") {
                //$request1 = new Illuminate\Http\Request($request->cbFilter);
                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {
                return $this->search_data($request->searchby, $request->tblFilter);
            } else
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
    public function update_per_row(Request $request)
    {

        DB::beginTransaction();

        try {

            $id = $request->id;

            if ($request->row == "wfc") {

                //FOR WFC/STATUS

                $tbll =  DB::table("client_details")

                    ->where("id", $id)

                    ->first();

                if (true) {



                    if (!empty($tbll)) {

                        if ($tbll->status != 'finished') {

                            if ($request->data)

                                $status = null;

                            else

                                $status = "Confirmed";





                            if (

                                $status == "Confirmed" &&

                                $tbll->mapping_status &&

                                $tbll->modem_status

                            )

                                $status = "done";

                            else {

                                if ($status == "done" || $status == "Confirmed")

                                    $status = "Confirmed";

                                else

                                    $status = null;
                            }





                            if (

                                $tbll->foc_layout == "Indoor layout done" ||

                                $tbll->foc_layout == null

                            ) {

                                if ($status == "done" || $status == "Confirmed")

                                    $status = "Confirmed";

                                else

                                    $status = null;
                            }
                        } else

                            $status = 'finished';



                        DB::table("client_details")

                            ->where("id", $id)

                            ->update([

                                "status" => $status

                            ]);



                        \Logger::instance()->log(

                            Carbon::now(),

                            $request->user_id,

                            $request->user_name,

                            $this->cname,

                            "update_per_row",

                            "message",

                            "update client_detail id " . $id . " status to " . $status

                        );
                    }
                }
            } else {

                $oldData = Client_detail::where("id", $id)

                    ->select($request->row . " as data")

                    ->first();



                DB::table("client_details")

                    ->where("id", $id)

                    ->update([

                        $request->row => $request->data,

                    ]);



                $newData = Client_detail::where("id", $id)

                    ->select($request->row . " as data")

                    ->first();



                \Logger::instance()->log(

                    Carbon::now(),

                    $request->user_id,

                    $request->user_name,

                    $this->cname,

                    "update_per_row",

                    "message",

                    "update client_detail id " . $id . "\nRow: " . $request->row .

                        "\nFrom: " . $oldData->data . "\nTo: " . $newData->data

                );
            }



            DB::commit();

            return "ok";
        } catch (\Exception $ex) {

            DB::rollBack();

            \Logger::instance()->logError(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "update_per_row",

                "Error",

                $ex->getMessage()

            );



            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function destroy($id, $region_id)
    {

        try {

            Client_detail::destroy($id);

            return $this->subIndex($region_id);
        } catch (\Exception $e) {

            return response()->json(['error' => 'There was a problem deleting this Item.'], 500);
        }
    }
    public function destroy1(Request $request)
    {



        try {



            $tbl = Job_order::where('client_detail_id', $request->id)->first();



            if (empty($tbl)) {

                $tbl1 = Client_detail::findOrFail($request->id);

                DB::table('client_details')->where('id', $request->id)->delete();



                $this

                    ->log(

                        Carbon::now(),

                        $request->user_id,

                        $request->user_name,

                        $this->cname,

                        "destroy1",

                        "message",

                        "delete client_details id " . $request->id .

                            "\nOld client_details: " . $tbl1

                    );



                if ($request->filterIn == "multi") {

                    return $this->multipleFilter($request);
                } elseif ($request->filterIn == "search") {

                    return $this->search_data($request->searchby, $request->tblFilter);
                } else

                    return $this->subIndex($request->region_id);
            } else {

                return response()->json(['error' => 'Cant delete this schedule, It has Job order'], 500);
            }
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

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function clientConfirm(Request $request, $id)
    {

        //return response()->json($request);

        try {

            $status = "Confirmed";

            $target_date = $request->target_date;

            if (

                $status == "done" &&

                $request->mapping_status &&

                $request->modem_status

            ) {

                $status = "done";
            } else {



                if (

                    $status == "Confirmed" &&

                    $request->mapping_status &&

                    $request->modem_status

                )

                    $status = "done";

                else {

                    if ($status == "done" || $status == "Confirmed")

                        $status = "Confirmed";

                    else

                        $status = null;

                    // $target_date = null;

                }
            }



            if (

                $request->foc_layout == "Indoor layout done" ||

                $request->foc_layout == null

            ) {

                if ($status == "done" || $status == "Confirmed")

                    $status = "Confirmed";

                else

                    $status = null;

                // $target_date = null;

            }

            // else {

            //     $target_date = null;

            // }



            $team_assigned = $request->team_assigned;

            if ($status == 'done' && $target_date == null) {

                $team_assigned = 1;
            }

            if ($status != 'done') {

                $team_assigned = 0;
            }



            $logFrom = Client_detail::where('id', $id)

                ->select("team_assigned", "target_date", "status")->get();

            Client_detail::where('id', $id)

                ->update([

                    'team_assigned' => $team_assigned,

                    'target_date' => $target_date,

                    'status' => $status

                ]);

            $logTo = Client_detail::where('id', $id)

                ->select("team_assigned", "target_date", "status")->get();



            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "clientConfirm",

                "message",

                "update Client_detail id:" . $id . " \nFrom: " . $logFrom . "\nTo:" . $logTo

            );

            //return $cmd;

            return $this->subIndex($request->user_region_id);
        } catch (\Exception $ex) {

            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "clientConfirm",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function updateTargetDate(Request $request)
    {

        try {

            DB::beginTransaction();

            $extraMsg = "";



            $logFrom = Client_detail::where('id', $request->client_detail_id)

                ->select("target_date")->first();

            if ($request->team_only == true) {

                DB::table('schedules')

                    ->where('client_detail_id', $request->client_detail_id)

                    ->where('target_date', $request->current_target_date)

                    ->update(['team_id' => $request->team_id]);



                Client_detail::where('id', $request->client_detail_id)

                    ->update([

                        'inst_remarks' => $request->inst_remarks,

                    ]);

                $extraMsg = "(team_only)";
            } else if ($request->date_only == true) {

                DB::table('schedules')

                    ->where('client_detail_id', $request->client_detail_id)

                    ->where('target_date', $request->current_target_date)

                    ->update(['target_date' => $request->target_date]);



                Client_detail::where('id', $request->client_detail_id)

                    ->update([

                        'target_date' => $request->target_date,

                        'inst_remarks' => $request->inst_remarks,

                    ]);

                $extraMsg = "(date_only)";
            } else {

                if ($logFrom->target_date == null) {

                    schedule::create($request->all());
                }



                Client_detail::where('id', $request->client_detail_id)

                    ->update([

                        'target_date' => $request->target_date,

                        'inst_remarks' => $request->inst_remarks,

                    ]);





                // store job order



                $chk_JO = Job_order::where('client_detail_id', $request->client_detail_id)->first();

                if ($chk_JO == null) {

                    $jo_details = (object)$request->item;

                    $client = (object)$jo_details->client1;

                    $region = (object)$client->region;

                    $project_desc = "Installation";

                    $jo_num = Job_order::where('region_id', $client->region_id)

                        ->max('jo_num');



                    $prepare_id = Engineer::where("user_id", $region->user_id_visor)->first();

                    $approve_id = Engineer::where("user_id", $region->user_id_rm)->first();





                    $jo = DB::table('job_orders')->insert([

                        'jo_num' => $jo_num + 1,

                        'client_id' => $jo_details->client_id,

                        'client_detail_id' => $request->client_detail_id,

                        'region_id' => $client->region_id,

                        'project_description' => $project_desc,

                        'cable_category' => $jo_details->cable_category,

                        'foc_length' => $jo_details->foc_length,

                        'started' => $request->target_date,

                        'finished' => null,

                        'engineer_in_charge' => $jo_details->engineers_id,

                        'prepare' => $prepare_id->id,

                        'approve' => $approve_id->id,

                        'note' => '1',

                        'created_at' => Carbon::now(),

                        'updated_at' => Carbon::now()

                    ]);

                    $this

                        ->log(

                            Carbon::now(),

                            $request->user_id,

                            $request->user_name,

                            $this->cname,

                            "storeJobOrder",

                            "message",

                            "Create new job order: " . $jo

                        );
                }
            }







            // logger

            $logTo = Client_detail::where('id', $request->client_detail_id)

                ->select("target_date")->first();

            $msg = "Update target date from: " . $logFrom . " to: " . $logTo . " " .

                $extraMsg . " of client_detail ID: " . $request->client_detail_id;

            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "updateTargetDate",

                "message",

                $msg

            );

            DB::commit();

            if ($request->filterIn == "multi") {

                //$request1 = new Illuminate\Http\Request($request->cbFilter);

                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {

                return $this->search_data($request->searchby, $request->tblFilter);
            } else

                return $this->subIndex($request->region_id);
        } catch (\Exception $ex) {

            DB::rollBack();

            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "updateTargetDate",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroyJobOrder(Request $request)
    {

        try {

            $tbl1 = Job_order::findOrFail($request->id);

            Job_order::destroy($request->id);

            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "destroyJobOrder",

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

                "destroyJobOrder",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => 'There was a problem deleting this thing.'], 500);
        }
    }
    public function multipleFilter(Request $request)
    {





        try {

            if ($request->cbFilter != null) {

                $temp = (object) $request->cbFilter;

                unset($request);

                $request = $temp;
            }

            $data = (object) $request->data;



            $tbl = Client_detail::with(['remarks_log.user'])

                ->join("clients", "client_details.client_id", "clients.id")

                ->leftJoin("schedules", function ($join) {

                    $join->on('client_details.id', '=', 'schedules.client_detail_id');

                    $join->on('client_details.target_date', '=', 'schedules.target_date');
                })

                ->leftJoin('teams', "schedules.team_id", "teams.id")

                ->select("clients.*", "client_details.*", "teams.user_id as team_user_id")

                ->orderByRaw("CASE WHEN client_details.target_date IS NULL THEN 1 ELSE 0 END, client_details.target_date");



            if ($request->region)

                $tbl->where("clients.region_id", $data->region_id);



            if ($request->area) {

                // $tbl->where("clients.area_id", $data->area_id);

                $area_temp = [];

                foreach ($data->area_id as $ii) {

                    $dd = (object)$ii;

                    $dd = $dd->id;

                    array_push($area_temp, $dd);
                }

                $tbl->whereIn("clients.area_id", $area_temp);
            }

            if ($request->address)

                $tbl->where("clients.location", 'like', '%' . $data->address . '%');

            if ($request->package)

                $tbl->where("clients.package_id", $data->package_id);

            if ($request->sales)

                $tbl->where("clients.sales_id", $data->sales_id);

            if ($request->installation_date) {

                $installation_date = (object) $data->installation_date;

                $from = new Carbon($installation_date->from);

                $to = new Carbon($installation_date->to);

                // return $from->toDateString() . " " . $to->toDateString();

                $tbl->whereBetween("client_details.target_date", [$from->toDateString(), $to->toDateString()]);
            }

            if ($request->foc_schedule) {

                $date = new Carbon($data->foc_schedule_date);

                $tbl->where("foc_schedule", $date->toDateString());
            }

            if ($request->aging) {

                $aging = (object) $data->aging;

                $from = new Carbon($aging->from);

                $to = new Carbon($aging->to);

                $tbl->whereBetween("client_details.aging", [$from->toDateString(), $to->toDateString()]);
            }

            if ($request->contract) {

                if ($data->contract == "Done")

                    $tbl->whereNotNull("contract_status");

                if ($data->contract == "Undone")

                    $tbl->whereNull("contract_status");
            }

            if ($request->otc) {

                if ($data->otc == "Paid")

                    $tbl->whereNotNull("aging");

                if ($data->otc == "Unpaid")

                    $tbl->whereNull("aging");
            }

            if ($request->layout_status)

                $tbl->where("layout_status", $data->layout_status);



            if ($request->date_activated) {

                if ($request->date_activated_type == "range") {

                    $date_activated = (object) $data->date_activated;

                    $from = new Carbon($date_activated->from);

                    $to = new Carbon($date_activated->to);

                    $tbl->whereBetween("client_details.date_activated", [$from->toDateString(), $to->toDateString()]);
                }

                if ($request->date_activated_type == "active") {

                    $tbl->whereNotNull("client_details.date_activated");
                }

                if ($request->date_activated_type == "not_active") {

                    $tbl->whereNull("client_details.date_activated");
                }
            }



            if ($request->created_at) {

                $date = new Carbon($data->created_at);

                $tbl->where("client_details.created_at", "like", "%" . $date->toDateString() . "%");
            }

            // return response()->json($request);

            return $this->ForQuery($tbl->get());
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function activateClient(Request $request)
    {

        // return $request;
        try {



            $stat = "";

            $bucket = (object) $request->bucket;

            $exist = Client::where('bucket_id', $bucket->id)->where('subscription_name', $request->subscription_name)->first();

            if (empty($exist)) {

                if ($bucket->name != "Dole") {

                    $ssh = new SSH2($bucket->IP);

                    if ($ssh->auth($bucket->username, $bucket->password)) {

                        $ssh->exec("export BASS_RDIR='/usr/local/foxbuckv3';/bin/bash /usr/local/foxbuckv3/commands/cbs " .

                            $bucket->user_id . " 10 0 1 subscription --show " .

                            $request->subscription_name);

                        //Valeroso_Rex_bw1

                        $ssh_table =  $ssh->getTable();

                        if (count($ssh_table) > 0) {



                            foreach ($ssh_table as $item) {

                                if ($item['name'] == $request->subscription_name) {

                                    if ($item['statusName'] == "activated")

                                        $stat = "ok";

                                    else {

                                        $ssh->exec("sudo /bin/bash /usr/local/foxbuckv3/commands/cbs " .

                                            $bucket->user_id . " 10 0 1 subscription --act " .

                                            $request->subscription_name);

                                        //return response()->json(['error' => 'This client is still deactivated in the bucket server'], 500);



                                        $stat = "ok";
                                    }
                                }
                            }
                        }
                    } else {

                        return response()->json(['error' => 'There are some problem connecting in the bucket server please try again'], 500);
                    }

                    if ($stat != "ok")

                        return response()->json(['error' => 'There are some problem connecting in the bucket server please try again'], 500);
                }

                //$ssh->disconnect();

                DB::beginTransaction();

                //$dtnow = new Carbon($request->dt);

                // $chk = cvc::where('client_detail_id', $request->id)

                //     ->where('cvc', $request->otp_input)->first();

                // $dtnow = new Carbon();

                // if ($chk != null || $request->user_id == 1) {

                // if ($chk != null)

                //     $cvcCreated = new Carbon($chk->created_at);

                // if ($request->user_id == 1)

                //     $cvcCreated = new Carbon();

                // $chk_hours = $cvcCreated->diffInHours($dtnow, false);

                // if ($chk_hours <= 24 && $chk_hours >= 0) {

                $client = (object) $request->client1;

                $dtnow = new Carbon($request->activated_date);

                $dtdtdt = new Carbon($request->activated_date);

                Client_detail::where('id', $request->id)

                    ->update([

                        'status' => 'finished',

                        'inst_remarks' => $request->inst_remarks,

                        'date_activated' => $dtnow

                    ]);



                // CHANGE THE TARGET DATE NOT NULL WHEN ACTIVATE

                //IF BUG AQUIRE SET THE TARGET DATE TO NULL AND ADD COLUMN TARGETDATE2

                // Client_detail::where('id', $id)

                //     ->update([

                //         'status' => 'finished',

                //         'date_activated' => $dtnow,

                //         'target_date' => null, //why set null?

                //     ]);



                schedule::where('client_detail_id', $request->id)

                    ->where('target_date', $dtnow->toDateString())

                    ->update([

                        'status' => 'ok'



                    ]);



                if ($request->line_transfer != 1) {

                    Client::where('id', $request->client_id)

                        ->whereNull('date_activated')

                        ->update([

                            'date_activated' => $dtnow,

                            'status' => 'Active',

                            'bucket_id' => $bucket->id,

                            'subscription_name' => $request->subscription_name

                        ]);



                    //BILLINGS--------------

                    if (true) {



                        $package_type = (object) $client->package_type;

                        $package = (object) $client->package;

                        //$mrr =  $package->mrr;

                        $datebill = null;

                        $dateCoverFrom = null;

                        $dateCoverTo = null;

                        $bill = 0;

                        $balance = 0;

                        $tempbal = 0;

                        //DARI KAY FIRST BILL INSERT

                        if (true) {





                            if ($package_type->name != "RES") {

                                $dateCoverFrom = $dtdtdt->copy()->addDay();

                                $dateCoverTo = $dtdtdt->copy()->addMonthsNoOverflow();

                                $datebill = $dtdtdt->copy()->addMonthsNoOverflow();

                                if ($dtdtdt->day >= 23  && $dtdtdt->day <= 31) {

                                    $datebill->day = 1;

                                    $datebill = $datebill->addMonthsNoOverflow();
                                } elseif ($dtdtdt->day >= 1  && $dtdtdt->day <= 7) {

                                    $datebill->day = 8;
                                } elseif ($dtdtdt->day >= 8  && $dtdtdt->day <= 15) {

                                    $datebill->day = 16;
                                } elseif ($dtdtdt->day >= 16  && $dtdtdt->day <= 22) {

                                    $datebill->day = 23;
                                } else {

                                    $datebill->day = 1;

                                    $datebill->day = $datebill->daysInMonth;
                                }

                                $bill = $package->mrr;

                                $balance = $bill;
                            } else {

                                $datebill = $dtdtdt->copy();

                                if ($dtdtdt->day >= 1  && $dtdtdt->day <= 15)

                                    $datebill->day = 15;

                                else

                                    $datebill = $datebill->endOfMonth();



                                $datebill = $datebill->addMonthsNoOverflow();

                                $dateCoverFrom = $dtdtdt->copy()->addDay();

                                // $difday = $dtdtdt->diffInDays($datebill);

                                // $bill = ($package->mrr / 30) * $difday;

                                $dateCoverTo = $dtdtdt->copy()->addMonthsNoOverflow();

                                $bill = $package->mrr;

                                // $tempbal =  $bill - $package->mrr;

                                // if ($tempbal < 0)

                                $balance = 0;

                                // else

                                //     $balance = $tempbal;

                            }



                            $bil1 = new billing;

                            $bil1->client_id = $request->client_id;

                            $bil1->date = $datebill;

                            $bil1->item = "MRR - Internet (" . $package_type->name . ")";

                            $bil1->description =

                                "MRR - " . $package_type->name . " " .

                                $dateCoverFrom->toFormattedDateString() . " - " .

                                $dateCoverTo->toFormattedDateString();

                            $bil1->price =  round($bill);

                            $bil1->balance =  round($balance);

                            $bil1->created_at = Carbon::now();

                            $bil1->updated_at = Carbon::now();

                            $bil1->save();

                            $dddddttttt = $datebill->copy();





                            //set date to res

                            //client name test LYDA BORRAL-LUMINGKIT

                            if ($package_type->name == "RES") {

                                $datebill = $datebill->copy()->subMonthNoOverflow();

                                // $dateCoverFrom->day = 15;

                                $balance = $package->mrr;
                            }



                            //generate whole bill

                            $contain = [];

                            for ($x = 1; $x < $client->term - 1; $x++) { //if condition change - change here too - refCashbondPayment



                                $datebill_copy = $datebill->copy()->addMonthsNoOverflow($x);

                                $dateCoverFrom_copy = $dateCoverFrom->copy()->addMonthsNoOverflow($x);

                                $dateCoverTo_copy = $dateCoverTo->copy()->addMonthsNoOverflow($x);



                                if ($tempbal < 0) {

                                    $tempbal = $package->mrr + $tempbal;

                                    if ($tempbal < 0)

                                        $balance = 0;

                                    else

                                        $balance = $tempbal;
                                } else

                                    $balance = $package->mrr;



                                $data = [

                                    "client_id" => $request->client_id,

                                    "date" => $datebill_copy,

                                    "item" => "MRR - Internet (" . $package_type->name . ")",

                                    "description" => "MRR - " . $package_type->name . " " .

                                        $dateCoverFrom_copy->toFormattedDateString() . " - " .

                                        $dateCoverTo_copy->toFormattedDateString(),

                                    "price" => $package->mrr,

                                    "balance" => round($balance),

                                    "created_at" => Carbon::now(),

                                    "updated_at" => Carbon::now(),

                                ];

                                array_push($contain, $data);



                                //insert cashbond to payment

                                if ($x == ($client->term - 2)) { // refCashbondPayment

                                    if ($package_type->name != "RES") {

                                        $tbl = billing::where('client_id', $request->client_id)

                                            ->where('item', 'CashBond Bill')->first();

                                        if (!empty($tbl)) {

                                            $ddaattaa = [

                                                [

                                                    "client_id" => $request->client_id,

                                                    "user_id" => $request->user_id,

                                                    "remarks" => "Cash Bond payment",

                                                    "amount" => $tbl->price,

                                                    "date" => $datebill_copy,

                                                    "created_at" => Carbon::now(),

                                                    "updated_at" => Carbon::now(),

                                                ]

                                            ];

                                            customer_payment::insert($ddaattaa);
                                        }
                                    } else {

                                        // fix ang last paydate sa RES----------FIX-------------------------------------FIX

                                    }
                                }
                            }

                            billing::insert($contain);
                        }





                        //DARI KAY OTC KUNG STAGGERED

                        $tbl = billing::where('client_id', $request->client_id)

                            ->where('item', 'OTC Staggered')->first(); // refOTCStaggered

                        if (!empty($tbl)) {

                            $temp = explode("-", $tbl->description);

                            $amount = $temp[1];

                            $months = $temp[3];

                            $permonth = $amount / $months;



                            //DARI KAY OTC BILL INSERT

                            for ($x = 0; $x < $months; $x++) {



                                $bil2 = new billing;

                                $bil2->client_id = $request->client_id;

                                $bil2->date = $dddddttttt;

                                $bil2->item = "OTC - Internet";

                                $bil2->description =

                                    "OTC - Installation Fee: Staggered " . $months . " months -" . ($x + 1) . "/" . $months;



                                $bil2->price =  round($permonth);

                                $bil2->balance =  round($permonth);

                                $bil2->created_at = Carbon::now();

                                $bil2->updated_at = Carbon::now();

                                $bil2->save();

                                $dddddttttt = $dddddttttt->addMonthsNoOverflow();
                            }
                        }
                    }
                }
                //temp comment
                // $text = "Greetings,%0a %0aThis is Dctech. We are glad to inform you that your account has been activated on " . $dtnow->toDateString()
                //     . "%0a %0aThis is a system-generated message. Please do not reply";
                // $contacts = explode(", ", $request->contact);
                // foreach ($contacts as $contact) {
                //     \Logger::instance()->send_text(
                //         $contact,
                //         $text
                //     );
                // }



                //email dari

                if (true) {

                    $message = "

                <html>

                <head>

                </head>

                <body>

                <p>Hi Team,</p>

                <p>Good day!</p>

                <p>

                <br />

                This is only to inform you all that this client had been activated.

                <br />

                <table>

                <tr>

                <td>Name:</td>

                <td>" . $request->name . "</td>

                </tr>

                 <tr>

                <td>Date Activated: </td>

                <td>" . $dtnow->toFormattedDateString() . "</td>

                </tr>

                </table>

                <br />

                Thank you and God Bless.</p>

                </body>

                </html>";
                }



                //remove comment in production

                \Logger::instance()->mailer(

                    "ACTIVATED CLIENT",

                    $message,

                    $request->user_email,

                    $request->user_name,

                    $request->sendTo,

                    $request->CCTO

                );



                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "activateClient",
                    "message",
                    "Activate client ID: " . $request->client_id . " date activated " . $dtnow

                );



                DB::commit();

                if ($request->filterIn == "multi") {

                    return $this->multipleFilter($request);
                } elseif ($request->filterIn == "search") {

                    return $this->search_data($request->searchby, $request->tblFilter);
                } else

                    return $this->subIndex($request->region_id);

                // } else

                //     return response()->json(['error' => 'CVC Expired!' .  $chk_hours], 500);

                // } else

                //     return response()->json(['error' => 'Wrong! CVC'], 500);


            } else {
                return response()->json(['error' => 'Subscription name already exists!'], 500);
            }
        } catch (\Exception $ex) {

            DB::rollBack();

            \Logger::instance()->log(

                Carbon::now(),

                $request->user_id,

                $request->user_name,

                $this->cname,

                "activateClient",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function installationSummary($year, $region_id)
    {

        try {

            $month = [

                'January', 'February', 'March', 'April', 'May', 'June',

                'July', 'August', 'September', 'October', 'November', 'December'

            ];

            $countAll = [];

            $countSME = [];

            $countCORP = [];

            $countENT = [];

            $countRES = [];

            $datasets = [];

            for ($x = 0; $x < 12; $x++) {

                $from = new Carbon('first day of ' . $month[$x] . ' ' . $year);

                $to = new Carbon('last day of ' . $month[$x] . ' ' . $year);



                if ($region_id == 0) {

                    $tbl = DB::table('clients')

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp1 = DB::table('clients')

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp2 = DB::table('clients')

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp3 = DB::table('clients')

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);
                } else {

                    $tbl = DB::table('clients')

                        ->where("region_id", $region_id)

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp1 = DB::table('clients')

                        ->where("region_id", $region_id)

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp2 = DB::table('clients')

                        ->where("region_id", $region_id)

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);



                    $temp3 = DB::table('clients')

                        ->where("region_id", $region_id)

                        ->where("date_activated", "!=", "NULL")

                        ->whereBetween("date_activated", [$from, $to]);
                }



                array_push($countAll, $tbl->count());

                $temp = $tbl;



                array_push($countSME, $temp->where('package_type_id', "1")->count());



                array_push($countCORP, $temp1->where('package_type_id', "2")->count());



                array_push($countENT, $temp2->where('package_type_id', "3")->count());



                array_push($countRES, $temp3->where('package_type_id', "4")->count());

                //array_push($retVal, $countAll);

            }



            $dataSME = new stdClass;

            $dataSME->label = "SME";

            $dataSME->data = $countSME;

            $dataSME->borderColor = [

                "rgba(255, 99, 132, 1)",

                "rgba(54, 162, 235, 1)",

                "rgba(255, 206, 86, 1)",

                "rgba(75, 192, 192, 1)",

                "rgba(153, 102, 255, 1)",

                "rgba(255, 159, 64, 1)",



                "rgba(200, 90, 1, 1)",

                "rgba(54, 162, 1, 1)",

                "rgba(255, 206, 1, 1)",

                "rgba(65, 52, 1, 100)",

                "rgba(153, 102, 1, 1)",

                "rgba(255, 159, 1, 1)"



            ];

            $dataSME->borderWidth = 1;

            $datasets[0] = $dataSME;



            $dataCORP = new stdClass;

            $dataCORP->label = "CORP";

            $dataCORP->data = $countCORP;

            $dataCORP->borderColor = [



                "rgba(54, 162, 235, 1)",

                "rgba(255, 206, 86, 1)",

                "rgba(75, 192, 192, 1)",

                "rgba(153, 102, 255, 1)",

                "rgba(255, 159, 64, 1)",



                "rgba(200, 90, 1, 1)",

                "rgba(54, 162, 1, 1)",

                "rgba(255, 206, 1, 1)",

                "rgba(65, 52, 1, 100)",

                "rgba(153, 102, 1, 1)",

                "rgba(255, 159, 1, 1)",

                "rgba(255, 99, 132, 1)",



            ];

            $dataCORP->borderWidth = 1;

            $datasets[1] = $dataCORP;



            $dataENT = new stdClass;

            $dataENT->label = "ENT";

            $dataENT->data = $countENT;

            $dataENT->borderColor = [



                "rgba(255, 206, 86, 1)",

                "rgba(75, 192, 192, 1)",

                "rgba(153, 102, 255, 1)",

                "rgba(255, 159, 64, 1)",



                "rgba(200, 90, 1, 1)",

                "rgba(54, 162, 1, 1)",

                "rgba(255, 206, 1, 1)",

                "rgba(65, 52, 1, 100)",

                "rgba(153, 102, 1, 1)",

                "rgba(255, 159, 1, 1)",

                "rgba(255, 99, 132, 1)",

                "rgba(54, 162, 235, 1)",



            ];

            $dataENT->borderWidth = 1;

            $datasets[2] = $dataENT;



            $dataRES = new stdClass;

            $dataRES->label = "RES";

            $dataRES->data = $countRES;

            $dataRES->borderColor = [



                "rgba(75, 192, 192, 1)",

                "rgba(153, 102, 255, 1)",

                "rgba(255, 159, 64, 1)",



                "rgba(200, 90, 1, 1)",

                "rgba(54, 162, 1, 1)",

                "rgba(255, 206, 1, 1)",

                "rgba(65, 52, 1, 100)",

                "rgba(153, 102, 1, 1)",

                "rgba(255, 159, 1, 1)",

                "rgba(255, 99, 132, 1)",

                "rgba(54, 162, 235, 1)",

                "rgba(255, 206, 86, 1)",



            ];

            $dataRES->borderWidth = 1;

            $datasets[3] = $dataRES;



            $dataAll = new stdClass;

            $dataAll->label = "All";

            $dataAll->data = $countAll;

            $dataAll->borderColor = [

                "rgba(153, 102, 255, 1)",

                "rgba(255, 159, 64, 1)",



                "rgba(200, 90, 1, 1)",

                "rgba(54, 162, 1, 1)",

                "rgba(255, 206, 1, 1)",

                "rgba(65, 52, 1, 100)",

                "rgba(153, 102, 1, 1)",

                "rgba(255, 159, 1, 1)",

                "rgba(255, 99, 132, 1)",

                "rgba(54, 162, 235, 1)",

                "rgba(255, 206, 86, 1)",

                "rgba(75, 192, 192, 1)",

            ];

            $dataAll->borderWidth = 1;

            $datasets[4] = $dataAll;



            //second graph

            $datenow = Carbon::now();

            $baseDate = Carbon::now();

            $baseDate = $baseDate->subDays(6);

            $datenow = $datenow->addDay();

            $weekly_label = [];

            $weekly_data_plan = [];

            $weekly_data_done = [];

            $weekly_percent_done = [];

            $weekly_percent_pending = [];

            while ($baseDate <= $datenow) {

                array_push($weekly_label, $baseDate->toFormattedDateString());

                if ($region_id == 0) {

                    $temp1 = DB::table('schedules')

                        ->where("type", "installation")

                        ->where("target_date", $baseDate->toDateString())

                        ->count();



                    $temp2 = DB::table('schedules')

                        ->where("type", "installation")

                        ->where("status", "ok")

                        ->where("target_date", $baseDate->toDateString())

                        ->count();
                } else {

                    $temp1 = DB::table('schedules')

                        ->join("client_details", "schedules.client_detail_id", "client_details.id")

                        ->join("clients", "client_details.client_id", "clients.id")

                        ->where("region_id", $region_id)

                        ->where("type", "installation")

                        ->where("schedules.target_date", $baseDate->toDateString())

                        ->count();



                    $temp2 = DB::table('schedules')

                        ->join("client_details", "schedules.client_detail_id", "client_details.id")

                        ->join("clients", "client_details.client_id", "clients.id")

                        ->where("region_id", $region_id)

                        ->where("schedules.type", "installation")

                        ->where("schedules.status", "ok")

                        ->where("schedules.target_date", $baseDate->toDateString())

                        ->count();
                }

                $pending = $temp1 - $temp2;

                if ($temp1 != 0) {

                    $done_percent = ($temp2 / $temp1) * 100;

                    $pending_percent = ($pending / $temp1) * 100;
                } else {

                    $done_percent = 0;

                    $pending_percent = 0;
                }

                array_push($weekly_percent_done, $done_percent);

                array_push($weekly_percent_pending, $pending_percent);



                array_push($weekly_data_plan, $temp1);

                array_push($weekly_data_done, $temp2);

                $baseDate->addDay();
            }



            $c1 = collect();

            $c1->put("yearDataset", $datasets);

            $c1->put("weekLabel", $weekly_label);

            $c1->put("weekDataPlan", $weekly_data_plan);

            $c1->put("weekDataDone", $weekly_data_done);

            $c1->put("weekPercentPending", $weekly_percent_pending);

            $c1->put("weekPercentDone", $weekly_percent_done);

            return response()->json($c1);
        } catch (\Exception $ex) {



            \Logger::instance()->log(

                Carbon::now(),

                "",

                "",

                $this->cname,

                "installationSummary",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function client_has_sched($id)
    {

        try {

            $tbl = Client_detail::where('client_id', $id)->first();

            if (empty($tbl)) {

                return response()->json(0);
            } else {

                return response()->json($tbl);
            }
        } catch (\Exception $ex) {

            \Logger::instance()->log(

                Carbon::now(),

                "",

                "",

                $this->cname,

                "client_has_sched",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function getMonitoring($region_id)
    {

        try {

            $dtnow = Carbon::now();

            $c1 = collect();



            if ($region_id == 0) {

                $tbl = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('target_date', $dtnow->toDateString())

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('today', $tbl);



                $dtnow = $dtnow->addDay();



                $tbl1 = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('target_date', $dtnow->toDateString())

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('tomorrow', $tbl1);



                $tbl2 = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where(DB::raw('yearweek(DATE(target_date), 1)'), DB::raw('yearweek(curdate(), 1)'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('week', $tbl2);



                $tbl3 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_prev', $tbl3);



                $tbl4 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE()'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_today', $tbl4);



                $tbl5 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_tom', $tbl5);



                $tbl6 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_prev', $tbl6);



                $tbl7 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE()'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_today', $tbl7);



                $tbl8 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_tom', $tbl8);

                //ELSE ----------------------------------------by Regionssssssssssssssssssssssssssssssssssssssssssssssssssss

            } else {



                $tbl = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('target_date', $dtnow->toDateString())

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('today', $tbl);



                $dtnow = $dtnow->addDay();



                $tbl1 = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('target_date', $dtnow->toDateString())

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('tomorrow', $tbl1);



                $tbl2 = DB::table('client_details')

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where(DB::raw('yearweek(DATE(target_date), 1)'), DB::raw('yearweek(curdate(), 1)'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*"

                    )

                    ->get();

                $c1->put('week', $tbl2);



                $tbl3 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_prev', $tbl3);



                $tbl4 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE()'))

                    ->select(
                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_today', $tbl4);



                $tbl5 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '1')

                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teama_tom', $tbl5);



                $tbl6 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_prev', $tbl6);



                $tbl7 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE()'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_today', $tbl7);



                $tbl8 = DB::table('schedules')

                    ->join("client_details", "schedules.client_detail_id", "client_details.id")

                    ->join("clients", "client_details.client_id", "clients.id")

                    ->where("region_id", $region_id)

                    ->where('schedules.team_id', '2')

                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))

                    ->select(

                        "clients.name as cname",

                        "clients.account_no as subid",

                        DB::raw("'Installation' as statname"),

                        "clients.*",

                        "client_details.*",

                        "schedules.status as sched_stat"

                    )

                    ->get();

                $c1->put('teamb_tom', $tbl8);
            }











            return $c1;
        } catch (\Exception $ex) {



            \Logger::instance()->log(

                Carbon::now(),

                "",

                "",

                $this->cname,

                "getMonitoring",

                "Error",

                $ex->getMessage()

            );

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function performance_sheet(Request $request)

    {

        if ($request->end == null)

            $request->end = $request->start;



        $from = new Carbon($request->start);

        $to = new Carbon($request->end);





        $region_id = $request->region_id;

        $from_copy = $from->copy();

        $to_copy = $from->copy();



        $date_from = [];

        $date_to = [];

        $everyseven = [];

        $data_chart_label = [];

        $data_result_Ave_day_client = [];

        $data_result_client_per_day = [];

        $data_target_client_per_day = [];

        $data_target_value = [];

        $count_perweek = [];

        $diffnito = $to_copy->diffInDays($to);

        $counterPerTurn = 1;

        $firstloop = true;

        $totalClients = [];

        $totalClientsAve = [];

        $ClientUnique = [];



        if ($diffnito >= 7) {

            $to_copy->addDays(6);

            array_push($data_chart_label, "1st 7Days");
        } else {

            $to_copy = $to;

            array_push($data_chart_label, $diffnito . "Days");
        }

        while ($from_copy <= $to) {

            //client_details

            if (true) {





                array_push($data_target_value, 9);

                array_push($date_from, $from_copy->toDateString());

                array_push($date_to, $to_copy->toDateString());

                $tbl = DB::table('client_details')

                    ->join('clients', 'clients.id', 'client_details.client_id')

                    ->leftJoin('job_orders', 'job_orders.client_detail_id', 'client_details.id')



                    ->where("clients.region_id", $region_id)

                    ->whereBetween("client_details.aging", [$from_copy->toDateString(), $to_copy->toDateString()])



                    ->whereNotNull('client_details.date_activated')

                    ->orWhereNotNull('client_details.target_date')



                    ->where("clients.region_id", $region_id)

                    ->whereBetween("client_details.aging", [$from_copy->toDateString(), $to_copy->toDateString()])



                    ->select("clients.*", "client_details.*", "job_orders.finished as foc_actual")

                    ->orderBy("aging")

                    ->get();

                array_push($count_perweek, count($tbl));

                //SELECT * FROM `client_details` WHERE aging BETWEEN CAST('2020-05-29' AS DATE) AND CAST('2020-05-31' AS DATE);

                $totalnumDays = 0;

                $result_dates = [];

                $cnc_date = [];

                foreach ($tbl as $item) {



                    $dt1 = new Carbon($item->aging);

                    $dt2 = "";



                    if ($item->date_activated != null)

                        $dt2 = new Carbon($item->date_activated);

                    else

                        $dt2 = new Carbon($item->target_date);



                    $num_days = $dt1->diffInDays($dt2);

                    $totalnumDays += $num_days;



                    if ($item->date_activated != null)

                        array_push($result_dates, $item->date_activated);

                    else

                        array_push($result_dates, $item->target_date);



                    array_push($cnc_date, $item->aging);
                }



                $cnc_datesunique = array_unique($cnc_date);

                $result_datesunique = array_unique($result_dates);



                if (count($tbl) > 0) {

                    $ave = $totalnumDays / count($tbl);

                    $ave1 = count($tbl) / count($result_datesunique);

                    $ave2 = count($tbl) / count($cnc_datesunique);



                    array_push($data_result_Ave_day_client, round($ave, 1));

                    array_push($data_result_client_per_day, round($ave1, 1));

                    array_push($data_target_client_per_day, round($ave2, 1));
                } else {

                    array_push($data_result_Ave_day_client, 0);

                    array_push($data_result_client_per_day, 0);

                    array_push($data_target_client_per_day, 0);
                }

                array_push($everyseven, $tbl);
            }

            //clients

            if (true) {

                $tblclients = Client::where("region_id", $region_id)

                    ->whereBetween("created_at", [$from_copy->toDateString(), $to_copy->toDateString()])

                    ->select("*", DB::raw('DATE(created_at) as created'))

                    ->get();

                $created_client = [];

                foreach ($tblclients as $item) {

                    array_push($created_client, $item->created);
                }

                array_push($totalClients, $tblclients);

                $client_unique = array_unique($created_client);

                array_push($ClientUnique, count($client_unique));

                if (count($tblclients) > 0)

                    array_push($totalClientsAve, (count($tblclients) / count($client_unique)));

                else

                    array_push($totalClientsAve, 0);
            }





            $diffnito = $to_copy->diffInDays($to);

            if ($firstloop) {

                $from_copy->addDay();

                $to_copy->addDay();

                //$firstloop = false;

            }

            if ($diffnito >= 7) {

                $to_copy->addDays(6);

                $counterPerTurn++;

                if ($counterPerTurn == 2)

                    array_push($data_chart_label, "2nd 7Days");

                elseif ($counterPerTurn == 3)

                    array_push($data_chart_label, "3rd 7Days");

                else

                    array_push($data_chart_label, $counterPerTurn . "th 7Days");
            } else {

                $to_copy = $to;

                if ($diffnito != 0)

                    array_push($data_chart_label, "Last " . $diffnito . "Day/s");
            }

            $from_copy->addDays(6);
        }



        $c2 = collect();



        $c2->put("ClientUnique", $ClientUnique);

        $c2->put("ClientsAve", $totalClientsAve);

        $c2->put("Clients", $totalClients);

        $c2->put("date_from", $date_from);

        $c2->put("date_to", $date_to);

        $c2->put("count_perweek", $count_perweek);

        $c2->put("data_target_value", $data_target_value);

        $c2->put("data_chart_label", $data_chart_label);

        $c2->put("data_result_Ave_day_client", $data_result_Ave_day_client);

        $c2->put("data_result_client_per_day", $data_result_client_per_day);

        $c2->put("data_target_client_per_day", $data_target_client_per_day);

        //---------------------------------------------------------------------------------------------------

        $from = new Carbon($request->start);

        $to = new Carbon($request->end);

        $clients = DB::table('client_details')

            ->join('clients', 'clients.id', 'client_details.client_id')

            ->leftJoin('job_orders', 'job_orders.client_detail_id', 'client_details.id')

            ->where("clients.region_id", $region_id)

            ->whereBetween("client_details.aging", [$from->toDateString(), $to->toDateString()])

            ->select("clients.*", "client_details.*", "job_orders.finished as foc_actual")

            ->orderBy("client_details.aging")

            ->get();





        $temp = [];

        $itemnodate = [];

        $total_num_days = 0;

        $count_num = 1;

        $count_num_nodate = 1;

        $result_dates = [];

        $CnC_target_dates = [];





        foreach ($clients as $item) {

            $item->offset = null;

            $item->foc_offset = null;



            if ($item->date_activated != null and $item->target_date != null) {

                $dt3 = new Carbon($item->date_activated);

                $dt4 = new Carbon($item->target_date);



                $item->offset = $dt4->diffInDays($dt3, false);;
            }



            if ($item->foc_schedule != null and $item->foc_actual != null) {

                $dt3 = new Carbon($item->foc_schedule);

                $dt4 = new Carbon($item->foc_actual);



                $item->foc_offset = $dt3->diffInDays($dt4, false);;
            }



            if ($item->date_activated != null || $item->target_date != null) {

                $dt1 = new Carbon($item->aging);

                $dt2 = "";

                if ($item->date_activated != null)

                    $dt2 = new Carbon($item->date_activated);

                else

                    $dt2 = new Carbon($item->target_date);





                $num_days = $dt1->diffInDays($dt2);

                $item->num_days = $num_days;

                $total_num_days += $item->num_days;





                if ($item->date_activated != null)

                    array_push($result_dates, $item->date_activated);

                else

                    array_push($result_dates, $item->target_date);



                $item->count_num = $count_num;

                array_push($temp, $item);

                $count_num++;
            } else {

                array_push($CnC_target_dates, $item->aging);

                $item->count_num_nodate = $count_num_nodate;

                array_push($itemnodate, $item);

                $count_num_nodate++;
            }
        }



        $target_item = DB::table('client_details')

            ->join('clients', 'clients.id', 'client_details.client_id')

            ->where("clients.region_id", $region_id)

            ->whereRaw("client_details.aging between '" . $from . "' and '" . $to . "'")

            ->whereNotNull('client_details.date_activated')

            ->orWhereNotNull('client_details.target_date')

            ->where("clients.region_id", $region_id)

            ->whereRaw("client_details.aging between '" . $from . "' and '" . $to . "'")

            ->select('aging', DB::raw('count(*) as total'))

            ->groupBy("aging")

            ->get();



        $result_dates_unique = array_unique($result_dates);

        $CnC_Confirm_Date_unique = array_unique($CnC_target_dates);

        $c1 = collect();

        $c1->put("items", $temp);

        if (count($temp) > 0) {



            $AverageDaysPerClient_Result = $total_num_days / count($temp);

            $clientPerDay_TargetValue = count($temp) / count($target_item);

            $clientPerDay_Result = count($temp) / count($result_dates_unique);

            $AverageDaysPerClient_Eval = "";



            $clientPerDay_Eval = "";

            $cnc_rate = 0;

            if (count($itemnodate) > 0)

                $cnc_rate =  count($itemnodate) / count($CnC_Confirm_Date_unique);



            if (round($AverageDaysPerClient_Result, 1) <= 9)

                $AverageDaysPerClient_Eval = "PASS";

            else

                $AverageDaysPerClient_Eval = "FAIL";



            if (round($clientPerDay_Result, 1) < round($clientPerDay_TargetValue, 1))

                $clientPerDay_Eval = "FAIL";

            else

                $clientPerDay_Eval = "PASS";





            $c1->put("clientPerDay_TargetValue", round($clientPerDay_TargetValue, 1));

            $c1->put("clientPerDay_Result", round($clientPerDay_Result, 1));

            $c1->put("AverageDaysPerClient_Result", round($AverageDaysPerClient_Result, 1));

            $c1->put("AverageDaysPerClient_Eval", $AverageDaysPerClient_Eval);

            $c1->put("clientPerDay_Eval", $clientPerDay_Eval);

            $c1->put("result_dates_unique", count($result_dates_unique));

            $c1->put("target_item", $target_item);

            $c1->put("total_num_days", $total_num_days);



            $c1->put("itemnodate", $itemnodate);

            $c1->put("cnc_unique", count($CnC_Confirm_Date_unique));

            $c1->put("cnc_rate", round($cnc_rate, 1));

            $c1->put("everyseven", $everyseven);

            $c1->put("c2", $c2);
        }

        return response()->json($c1);
    }
    public function misc_summary(Request $request)
    {

        if ($request->end == null)

            $request->end = $request->start;



        $from = new Carbon($request->start);

        $to = new Carbon($request->end);

        $region_id = $request->region_id;



        $from_copy = $from->copy();

        $to_copy = $from->copy();



        $daily_sales_incoded = [];

        $daily_sales_incoded_count = [];

        $date_label = [];



        while ($from_copy <= $to) {

            $tbl = Client::with(['clientDetail'])

                ->where(DB::raw('DATE(created_at)'), $from_copy->toDateString())

                ->where("region_id", $region_id)

                ->get();

            array_push($date_label, $from_copy->toFormattedDateString());

            array_push($daily_sales_incoded, $tbl);

            array_push($daily_sales_incoded_count, count($tbl));

            $from_copy->addDay();
        }

        $c1 = collect();



        $c1->put("date_label", $date_label);

        $c1->put("daily_sales_incoded", $daily_sales_incoded);

        $c1->put("daily_sales_incoded_count", $daily_sales_incoded_count);

        //$from_copy->toDateString()



        return response()->json($c1);
    }
    public function temp_graph(Request $request) //installaion graph
    {

        if ($request->end == null)

            $request->end = $request->start;



        $from = new Carbon($request->start);

        $to = new Carbon($request->end);

        $region_id = $request->region_id;



        $from_copy = $from->copy();

        $to_copy = $from->copy();



        $date_label = [];

        $sales = [];

        $installed = [];

        $contractCount = [];

        $paidCounts = [];

        $installed_minus_sales = [];

        $installed_minus_cnc = [];

        $x = 0;



        while ($from_copy <= $to) {

            $salesCount = Client::with(['clientDetail'])

                ->where(DB::raw('DATE(created_at)'), $from_copy->toDateString())

                ->where("region_id", $region_id)

                ->whereHas("clientDetail", function ($query) {

                    $query->whereNotNull("client_id");
                })

                ->count();



            $installedCount = Client::with(['clientDetail'])

                ->where("date_activated", $from_copy->toDateString())

                ->where("region_id", $region_id)

                ->count();



            $paidCount = Client::join('client_details', 'client_details.client_id', 'clients.id')

                ->where("aging", $from_copy->toDateString())

                ->where("region_id", $region_id)

                ->count();



            $contract = Client::join('client_details', 'client_details.client_id', 'clients.id')

                ->where("contract", $from_copy->toDateString())

                ->where("region_id", $region_id)

                ->count();

            array_push($date_label, $from_copy->toFormattedDateString());

            if ($x > 0) {

                array_push($sales, $sales[$x - 1] + $salesCount);

                array_push($installed, $installed[$x - 1] + $installedCount);

                array_push($paidCounts, $paidCounts[$x - 1] + $paidCount);

                array_push($contractCount, $contractCount[$x - 1] + $contract);
            } else {

                array_push($sales, $salesCount);

                array_push($installed, $installedCount);

                array_push($paidCounts, $paidCount);

                array_push($contractCount, $contract);
            }



            array_push($installed_minus_sales, $installed[$x] - $sales[$x]);

            array_push($installed_minus_cnc, $installed[$x] - $paidCounts[$x]);

            $from_copy->addDay();

            $x++;
        }

        $c1 = collect();



        $c1->put("date_label", $date_label);

        $c1->put("sales", $sales);

        $c1->put("installed", $installed);

        $c1->put("installed_minus_sales", $installed_minus_sales);

        $c1->put("installed_minus_cnc", $installed_minus_cnc);

        $c1->put("paidCounts", $paidCounts);

        $c1->put("contractCount", $contractCount);

        //$from_copy->toDateString()



        return response()->json($c1);
    }
    public function trouble_graph(Request $request)
    {

        if ($request->end == null)

            $request->end = $request->start;



        $from = new Carbon($request->start);

        $to = new Carbon($request->end);

        $region_id = $request->region_id;



        $from_copy = $from->copy();

        $to_copy = $from->copy();



        $date_label = [];

        $tickets = [];

        $ticketFixed = [];

        $rate = [];

        $x = 0;



        while ($from_copy <= $to) {



            $ticketCount = Ticket::whereHas("client", function ($query) use ($region_id) {

                $query->where("region_id", $region_id);
            })

                ->where(DB::raw('DATE(created_at)'), $from_copy->toDateString())

                ->count();



            $ticketFixedCount =

                Ticket::whereHas("client", function ($query) use ($region_id) {

                    $query->where("region_id", $region_id);
                })

                ->where(DB::raw('DATE(updated_at)'), $from_copy->toDateString())

                ->where('Status_Ticket_id', '3')

                ->count();

            array_push($date_label, $from_copy->toFormattedDateString());

            if ($x > 0) {

                array_push($tickets, $tickets[$x - 1] + $ticketCount);

                array_push($ticketFixed, $ticketFixed[$x - 1] + $ticketFixedCount);
            } else {

                array_push($tickets, $ticketCount);

                array_push($ticketFixed, $ticketFixedCount);
            }



            array_push($rate, $ticketFixed[$x] - $tickets[$x]);

            $from_copy->addDay();

            $x++;
        }

        $c1 = collect();



        $c1->put("date_label", $date_label);

        $c1->put("tickets", $tickets);

        $c1->put("ticketFixed", $ticketFixed);

        $c1->put("rate", $rate);

        //$from_copy->toDateString()



        return response()->json($c1);
    }

    public function sales_graph(Request $request)
    {

        $region_id = $request->region_id;
        $from = new Carbon($request->start);
        $to = new Carbon($request->end);
        $overall_clients = 0;
        $overall_no_contract = 0;
        $overall_no_otc = 0;
        $overall_paid_mrr = 0;
        $overall_for_activation = 0;
        $overall_for_activation_mrr = 0;
        $overall_activated = 0;
        $overall_activated_mrr = 0;
        $overall_quota = 0;
        $overall_hit_rate = 0;
        $retval = [];

        if ($region_id == 0) {

            $tbl = Region::all();
        } else

            $tbl = Sales::with(['user'])

                ->where("sales.status", "active")

                ->whereHas("user", function ($query) use ($region_id) {

                    $query->where("region_id", $region_id);
                })

                ->get();

        $total_clients1 = Client::with(['clientDetail'])

            ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("client_id");
            });

        $total_no_contract1 = Client::with(['clientDetail'])

            ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNull("contract")

            ->whereNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("client_id");
            });

        $total_no_otc1 = Client::with(['clientDetail'])

            ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNotNull("contract")

            ->whereNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNull("otc");
            });

        $paid_mrr1 = Client::join("packages", "packages.id", "clients.package_id")

            ->whereBetween("clients.created_at", [$from->toDateString(), $to->toDateString()])

            //->whereNotNull("date_activated")

            ->whereNotNull("contract")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");
            })

            ->select(DB::raw('SUM(packages.mrr) as total'));

        $for_activation1 = Client::with(['clientDetail'])

            ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNotNull("contract")

            ->whereNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");
            });

        $for_activation_mrr1 = Client::join("packages", "packages.id", "clients.package_id")

            ->whereBetween("clients.created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNotNull("contract")

            ->whereNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");
            })

            ->select(DB::raw('SUM(packages.mrr) as total'));

        $activated1 = Client::with(['clientDetail'])

            ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNotNull("contract")

            ->whereNotNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");
            });

        $activated_mrr1 = Client::join("packages", "packages.id", "clients.package_id")

            ->whereBetween("clients.created_at", [$from->toDateString(), $to->toDateString()])

            ->whereNotNull("contract")

            ->whereNotNull("date_activated")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");
            })

            ->select(DB::raw('SUM(packages.mrr) as total'));

        $quota1 = Sales::where("sales.status", "active")

            ->join("users", "users.id", "sales.user_id");



        if ($region_id == 0) {

            $tbl = Region::all();



            foreach ($tbl as $item) {

                $total_clients = (clone $total_clients1)->where("region_id", $item->id)->get();

                $total_no_contract = (clone $total_no_contract1)->where("region_id", $item->id)->get();

                $total_no_otc = (clone $total_no_otc1)->where("region_id", $item->id)->get();

                $paid_mrr = (clone $paid_mrr1)->where("region_id", $item->id)->first();

                $for_activation = (clone $for_activation1)->where("region_id", $item->id)->get();

                $for_activation_mrr = (clone $for_activation_mrr1)->where("region_id", $item->id)->first();

                $activated = (clone $activated1)->where("region_id", $item->id)->get();

                $activated_mrr = (clone $activated_mrr1)->where("region_id", $item->id)->first();

                $quota = (clone $quota1)->where("users.region_id", $item->id)->sum('quota');





                $hit_rate = 0;

                if ($quota > 0)

                    $hit_rate = ($paid_mrr->total / $quota) * 100;



                $overall_clients += count($total_clients);

                $overall_no_contract += count($total_no_contract);

                $overall_no_otc += count($total_no_otc);

                $overall_paid_mrr += $paid_mrr->total;

                $overall_for_activation += count($for_activation);

                $overall_for_activation_mrr += $for_activation_mrr->total;

                $overall_activated += count($activated);

                $overall_activated_mrr += $activated_mrr->total;

                $overall_quota += $quota;



                $item->total_clients = $total_clients;

                $item->total_no_contract = $total_no_contract;

                $item->total_no_otc = $total_no_otc;

                $item->paid_mrr = number_format($paid_mrr->total);

                $item->for_activation = $for_activation;

                $item->for_activation_mrr = number_format($for_activation_mrr->total);

                $item->activated = $activated;

                $item->activated_mrr = number_format($activated_mrr->total);

                $item->hit_rate = round($hit_rate, 1) . "%";

                $item->name_name = $item->name;

                $item->quota = number_format($quota);

                array_push($retval, $item);
            }
        } else {
            foreach ($tbl as $item) {
                $user = $item->user;

                $total_clients = (clone $total_clients1)->where("sales_id", $item->id)->get();
                $total_no_contract = (clone $total_no_contract1)->where("sales_id", $item->id)->get();
                $total_no_otc = (clone $total_no_otc1)->where("sales_id", $item->id)->get();
                $paid_mrr = (clone $paid_mrr1)->where("sales_id", $item->id)->first();
                $for_activation = (clone $for_activation1)->where("sales_id", $item->id)->get();
                $for_activation_mrr = (clone $for_activation_mrr1)->where("sales_id", $item->id)->first();
                $activated = (clone $activated1)->where("sales_id", $item->id)->get();
                $activated_mrr = (clone $activated_mrr1)->where("sales_id", $item->id)->first();
                $hit_rate = 0;

                if ($item->quota > 0)
                    $hit_rate = ($paid_mrr->total / $item->quota) * 100;


                // REAM ME BASAHA KO... FOR THE MEANTIME SA CONTIDION SA NATO DULAUN TONG CONTRACT -> OTC -> ACTIVATED. hehe

                //---------------------------------------------------------------------------------------------------------

                $overall_clients += count($total_clients);
                $overall_no_contract += count($total_no_contract);
                $overall_no_otc += count($total_no_otc);
                $overall_paid_mrr += $paid_mrr->total;
                $overall_for_activation += count($for_activation);
                $overall_for_activation_mrr += $for_activation_mrr->total;
                $overall_activated += count($activated);
                $overall_activated_mrr += $activated_mrr->total;
                $overall_quota += $item->quota;

                $item->total_clients = $total_clients;
                $item->total_no_contract = $total_no_contract;
                $item->total_no_otc = $total_no_otc;
                $item->paid_mrr = number_format($paid_mrr->total);
                $item->for_activation = $for_activation;
                $item->for_activation_mrr = number_format($for_activation_mrr->total);
                $item->activated = $activated;
                $item->activated_mrr = number_format($activated_mrr->total);
                $item->hit_rate = round($hit_rate, 1) . "%";
                $item->name_name = $user->name;
                $item->quota = number_format($item->quota);
                array_push($retval, $item);
            }
        }

        if ($overall_quota > 0)

            $overall_hit_rate = ($overall_paid_mrr / $overall_quota) * 100;



        $c = collect();
        $c->put("data", $retval);
        $c->put("overall_clients", $overall_clients);
        $c->put("overall_no_contract", $overall_no_contract);
        $c->put("overall_no_otc", $overall_no_otc);
        $c->put("overall_paid_mrr", number_format($overall_paid_mrr));
        $c->put("overall_for_activation", $overall_for_activation);
        $c->put("overall_for_activation_mrr", number_format($overall_for_activation_mrr));
        $c->put("overall_activated", $overall_activated);
        $c->put("overall_activated_mrr", number_format($overall_activated_mrr));
        $c->put("overall_quota", number_format($overall_quota));
        $c->put("overall_hit_rate", round($overall_hit_rate, 1) . "%");
        return response()->json($c);
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
    public function trend($region_id)
    {
        $overall_pending_clients =

            Client::with(['clientDetail'])

            ->whereNotNull("contract")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");

                $query->whereNull("date_activated");
            })

            ->count();



        $activated_clients_no =

            Client::with(['clientDetail'])

            ->whereNotNull("contract")

            ->whereHas("clientDetail", function ($query) {

                $query->whereNotNull("otc");

                $query->whereNotNull("date_activated");
            })

            ->whereYear("date_activated",  Carbon::now()->year)

            ->whereMonth("date_activated", Carbon::now()->month)

            ->count();





        if ($region_id > 0) {
            $overall_pending_clients =

                Client::with(['clientDetail'])

                ->where("region_id", $region_id)

                ->whereNotNull("contract")

                ->whereHas("clientDetail", function ($query) {

                    $query->whereNotNull("otc");

                    $query->whereNull("date_activated");
                })

                ->count();



            $activated_clients_no =
                Client::with(['clientDetail'])
                ->where("region_id", $region_id)
                ->whereNotNull("contract")
                ->whereHas("clientDetail", function ($query) {

                    $query->whereNotNull("otc");

                    $query->whereNotNull("date_activated");
                })
                ->whereYear("date_activated",  Carbon::now()->year)
                ->whereMonth("date_activated", Carbon::now()->month)
                ->count();
        }
        $c = collect();
        $c->put("pendingTrend", $overall_pending_clients);
        $c->put("activatedTrend", $activated_clients_no);
        return response()->json($c);
    }
}
