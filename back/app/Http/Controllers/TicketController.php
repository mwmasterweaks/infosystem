<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\schedule;
use App\ticket_remarks_log;
use App\tickets_trouble_type;
use App\ticket_attachment;
use App\ticket_group;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Exists;

use stdClass;

class TicketController extends Controller
{

    private $cname = "TicketController";
    public function index()
    {
        $tbl = Ticket::with(
            ['Ticket_status', 'Client', 'User', 'remarks.user', 'Client.package']
        )->get();


        $temp = [];

        foreach ($tbl as $item) {
            $mTicket_id = $item->ticket_id . $item->id;
            $item->mTicket_id = $mTicket_id;
            array_push($temp, $item);
        }



        return response()->json($temp);
    }
    public function subIndex($region_id)
    {
        $tbl1 = Ticket::with(['Client.pon.olt', 'Client.region', 'Client.package', 'User', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
            ->leftJoin("areas", "tickets.area_id", "areas.id")
            ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
            ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
            ->where("ticket_statuses.id", "!=", "3")
            ->select(
                "areas.id as area_id",
                "areas.name as area_name",
                "ticket_statuses.name as statname",
                "tickets.Status_Ticket_id as current_status",
                "complaint_lists.name as compname",
                "tickets.complaint_new as current_complaint",
                "complaint_lists.*",
                "ticket_statuses.*",
                "tickets.*"
            )
            ->orderBy('target_date')
            ->orderBy('ticket_statuses.name')
            ->get();

        $tbl2 = ticket_group::with(['ticket_group_client.client', 'user', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
            ->leftJoin("complaint_lists", "ticket_groups.complaint_new", "complaint_lists.id")
            ->join("ticket_statuses", "ticket_groups.Status_Ticket_id", "ticket_statuses.id")
            ->where("ticket_statuses.id", "!=", "3")
            ->select(
                "ticket_statuses.name as statname",
                "ticket_groups.Status_Ticket_id as current_status",
                "complaint_lists.name as compname",
                "ticket_groups.complaint_new as current_complaint",
                "ticket_groups.downtime as group_name",
                "complaint_lists.*",
                "ticket_statuses.*",
                "ticket_groups.*"
            )
            ->orderBy('target_date')
            ->orderBy('ticket_statuses.name')
            ->get();


        $result = $tbl1->merge($tbl2);
        return $this->ForQuery($result);
    }
    public function getAll()
    {
        try {

            $tbl = Ticket::with(['Client.pon.olt', 'Client.region', 'User', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
                ->leftJoin("areas", "tickets.area_id", "areas.id")
                ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                ->select(
                    "areas.id as area_id",
                    "areas.name as area_name",
                    "ticket_statuses.name as statname",
                    "tickets.Status_Ticket_id as current_status",
                    "complaint_lists.name as compname",
                    "tickets.complaint_new as current_complaint",
                    "complaint_lists.*",
                    "ticket_statuses.*",
                    "tickets.*"
                )
                ->orderBy('target_date')
                ->orderBy('ticket_statuses.name')
                ->get();

            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function search_data($by, $search)
    {
        try {
            $tbl = Ticket::with(['remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
                ->join("clients", "tickets.client_id", "clients.id")
                ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                ->join("users", "tickets.user_id", "users.id")
                ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                ->leftJoin("areas", "tickets.area_id", "areas.id")
                ->leftJoin("pons", "clients.pon_id", "pons.id")
                ->leftJoin("olts", "pons.olt_id", "olts.id")
                ->select(
                    "pons.*",
                    "olts.*",
                    "areas.id as area_id",
                    "areas.name as area_name",
                    "clients.name as cname",
                    "clients.location as location",
                    "clients.contact as contact",
                    "clients.ip_assigned as ip_assigned",
                    "clients.vlan as vlan",
                    "clients.onu as onu",
                    "ticket_statuses.name as statname",
                    "tickets.Status_Ticket_id as current_status",
                    "complaint_lists.name as compname",
                    "tickets.complain as oldcomp",
                    "tickets.complaint_new as current_complaint",
                    "users.name as uname",
                    "complaint_lists.*",
                    "users.*",
                    "ticket_statuses.*",
                    "tickets.*"
                )
                ->where($by, 'like', '%' . $search . '%')
                ->orderBy('tickets.updated_at', 'desc')
                ->get();

            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function emailTicket()
    {
        $dtnow = Carbon::now();
        $dttom = $dtnow->copy()->subDay();
        $tbl = Ticket::with(['Client.pon.olt', 'Client.region', 'User', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
            ->leftJoin("areas", "tickets.area_id", "areas.id")
            ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
            ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
            ->where("ticket_statuses.id", "2")
            ->where("tickets.created_at", "<", $dttom)
            ->select(
                "areas.id as area_id",
                "areas.name as area_name",
                "ticket_statuses.name as statname",
                "tickets.Status_Ticket_id as current_status",
                "complaint_lists.name as compname",
                "tickets.complaint_new as current_complaint",
                "complaint_lists.*",
                "ticket_statuses.*",
                "tickets.*"
            )
            ->orderBy('tickets.created_at')
            ->get();

        return $this->ForQuery($tbl);
    }
    public function ForQuery($tbl)
    {
        $temp = [];

        foreach ($tbl as $item) {
            foreach ($item->remarks_log as $r_log) {
                $r_log->commentVisibility = 'hide';
            }

            $c = collect();
            $c->put('name', $item->statname);

            $c->put('name', $item->compname);
            $c->put('name', $item->oldcomp);


            $item->ticket_status = $c;

            $obj1 = collect();
            $obj1->put('name', $item->region_name);


            $c = collect();
            $c->put('name', $item->cname);
            $c->put('location', $item->location);
            $c->put('contact', $item->contact);
            $c->put('region', $obj1);
            $c->put('created_at', $item->created_at);
            $item->client = $c;
            // $item->client->region->name = $item->region_name;
            $c = collect();
            $c->put('name', $item->uname);
            $item->user = $c;

            $mTicket_id = $item->ticket_id . $item->id;
            $item->mTicket_id = $mTicket_id;

            $created_at_formated = new Carbon($item->created_at);
            $item->created_at_formated = $created_at_formated->toDayDateTimeString();

            $updated_at_formated = new Carbon($item->updated_at);
            $item->updated_at_formated = $updated_at_formated->toDayDateTimeString();

            $tdateFormated = new Carbon($item->target_date);
            $item->tdateFormated = $tdateFormated->toFormattedDateString();

            $dt = new Carbon($item->created_at);
            $dtnow = Carbon::now();
            $aging = $dt->diffInDays($dtnow, false);
            $item->aging = $aging;

            $dtdtdtdt = Carbon::now();

            if ($item->Status_Ticket_id == '3')
                $dtdtdtdt = new Carbon($item->updated_at);
            $diffHuman = $dt->diffForHumans(
                $dtdtdtdt,
                [
                    'parts' => 3,
                    //'short' => true,
                    'syntax' => CarbonInterface::DIFF_ABSOLUTE
                ]
            );
            $item->diffHuman = $diffHuman;
            $diffHour = $dt->diffInHours($dtdtdtdt);
            $item->diffHour = $diffHour;
            $c = collect();
            if ($diffHour >= 24)
                $c->put('diffHuman', 'danger');

            $item->_cellVariants = $c;

            // mao ning sa pagcheck sa checkbox based on db
            $selected_trouble = [];
            if ($item->trouble_type != null)
                foreach ($item->trouble_type as $trouble) {
                    array_push($selected_trouble, $trouble->trouble_type_id);
                }
            $item->selected_trouble = $selected_trouble;

            $attachments = [];
            if ($item->ticket_attachment != null)
                foreach ($item->ticket_attachment as $attachment) {
                    array_push($attachments, $attachment->filename);
                }
            $item->attachments = $attachments;
            array_push($temp, $item);
        }

        $data = DB::table('tickets')
            ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
            ->select('complaint_lists.name', DB::raw('count(*) as total'))
            ->where('tickets.Status_Ticket_id', '!=', '3')
            ->where("tickets.created_at", ">", Carbon::now()->subDay())
            ->groupBy('complaint_lists.name')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get();


        $pendingCount = Ticket::where("Status_Ticket_id", "1")->count();
        $urgentCount = Ticket::where("Status_Ticket_id", "2")->count();
        $techVisitCount = Ticket::where("Status_Ticket_id", "4")->count();
        $transferCount = Ticket::where("Status_Ticket_id", "9")->count();
        $itndCount = Ticket::where("Status_Ticket_id", "7")->count();

        $pendingCount += ticket_group::where("Status_Ticket_id", "1")->count();
        $urgentCount += ticket_group::where("Status_Ticket_id", "2")->count();
        $techVisitCount += ticket_group::where("Status_Ticket_id", "4")->count();
        $transferCount += ticket_group::where("Status_Ticket_id", "9")->count();
        $itndCount += ticket_group::where("Status_Ticket_id", "7")->count();


        $c1 = collect();
        $c1->put('items', $temp);
        $c1->put('pendingCount', $pendingCount);
        $c1->put('urgentCount', $urgentCount);
        $c1->put('techVisitCount', $techVisitCount);
        $c1->put('itndCount', $itndCount);
        $c1->put('transferCount', $transferCount);
        $c1->put('data', $data);

        return response()->json($c1);
    }
    public function create()
    {
    }
    public function store(Request $request)
    {

        try {
            // for individual tickets
            if ($request->ticketType == 1) {

                $tbl = Ticket::create($request->all());

                $fileName = "";


                if ($request->remarks != "") {
                    $tremarks = new ticket_remarks_log;
                    $tremarks->ticket_id = $tbl->id;
                    $tremarks->user_id = $request->user_id;
                    $tremarks->remarks = $request->remarks;
                    $tremarks->save();
                }

                if ($request->selected_trouble != "") {
                    $items = (object)$request->selected_trouble;

                    foreach ($items as $item) {
                        $item =
                            json_decode(json_encode($item), true);

                        DB::table('tickets_trouble_types')->insert([
                            'ticket_id' => $tbl->id,
                            'trouble_type_id' => $item
                        ]);
                    }
                }

                if ($request->attachments != "") {
                    $attachments = (object) $request->attachments;
                    foreach ($attachments as $attachment) {
                        $attachment =
                            json_decode(json_encode($attachment), true);

                        $exploded = explode(',', $attachment);

                        $decoded = base64_decode($exploded[1]);

                        if (str_contains($exploded[0], "jpeg"))
                            $extension = "jpeg";
                        elseif (str_contains($exploded[0], "jpg"))
                            $extension = "jpg";
                        elseif (str_contains($exploded[0], "png"))
                            $extension = "png";
                        elseif (str_contains($exploded[0], "gif"))
                            $extension = "gif";
                        elseif (str_contains($exploded[0], "tiff"))
                            $extension = "tiff";
                        elseif (str_contains($exploded[0], "bmp"))
                            $extension = "bmp";
                        else
                            $extension = "txt";

                        $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                        $path = public_path() . "/attachments/" . $fileName;

                        file_put_contents($path, $decoded);

                        DB::table('ticket_attachments')
                            ->insert([
                                'ticket_id' => $tbl->id,
                                'filename' => $fileName

                            ]);
                    }
                }

                if ($request->area_id != "") {
                    $area = $request->area_id;
                    $client = $request->client_id;

                    $tbl =
                        DB::table('clients')
                        ->where('id', $client)
                        ->update(['area_id' => $area]);
                }
            }
            // for multiple tickets
            else if ($request->ticketType == 2) {


                foreach ($request->multipleClients as $client) {
                    $client = (object)$client;
                    $date = Carbon::now()->format('Ymd');

                    $package = $client->package_type_name;
                    $package .= $date;

                    $temp = DB::table('tickets')->insertGetId(
                        [
                            "ticket_id" => $package,
                            "client_id" => $client->id,
                            "user_id"  => $request->user_id,
                            "updated_by" => null,
                            "complain" => $client->name,
                            "findings" => null,
                            "action" => null,
                            "complaint_new" => $request->complaint_new,
                            "bwmon" => $request->bwmon,
                            "device" => $request->device,
                            "loss" => $request->loss,
                            "downtime" => $request->downtime,
                            "cacti" => $request->cacti,
                            "rep_findings" => null,
                            "rep_action" => null,
                            "rebatable" => null,
                            "remarks" => $request->remarks,
                            "report" => $request->report,
                            "prev_status" => null,
                            "Status_Ticket_id" => $request->Status_Ticket_id,
                            "area_id" => $request->area_id,
                            "connection_status" => $request->connection_status,
                            "technical_assigned" => null,
                            "target_date" => null,
                            "team_assigned" => null,
                            "downtime_hours" => null,
                            "date_time_fixed" => null,
                            "to_soa" => 0,
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                        ]
                    );

                    $fileName = "";


                    if ($request->remarks != "") {
                        $tremarks = new ticket_remarks_log;
                        $tremarks->ticket_id =
                            $temp;
                        $tremarks->user_id = $request->user_id;
                        $tremarks->remarks = $request->remarks;
                        $tremarks->save();
                    }

                    if ($request->selected_trouble != "") {
                        $items = (object)$request->selected_trouble;

                        foreach ($items as $item) {
                            $item =
                                json_decode(json_encode($item), true);

                            DB::table('tickets_trouble_types')->insert([
                                'ticket_id' => $temp,
                                'trouble_type_id' => $item
                            ]);
                        }
                    }

                    if ($request->attachments != "") {
                        $attachments = (object) $request->attachments;
                        foreach ($attachments as $attachment) {
                            $attachment =
                                json_decode(json_encode($attachment), true);

                            $exploded = explode(',', $attachment);

                            $decoded = base64_decode($exploded[1]);

                            if (str_contains($exploded[0], "jpeg"))
                                $extension = "jpeg";
                            elseif (str_contains($exploded[0], "jpg"))
                                $extension = "jpg";
                            elseif (str_contains($exploded[0], "png"))
                                $extension = "png";
                            elseif (str_contains($exploded[0], "gif"))
                                $extension = "gif";
                            elseif (str_contains($exploded[0], "tiff"))
                                $extension = "tiff";
                            elseif (str_contains($exploded[0], "bmp"))
                                $extension = "bmp";
                            else
                                $extension = "txt";

                            $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                            $path = public_path() . "/attachments/" . $fileName;

                            file_put_contents($path, $decoded);

                            DB::table('ticket_attachments')
                                ->insert([
                                    'ticket_id' => $temp,
                                    'filename' => $fileName

                                ]);
                        }
                    }
                }
                //  Ticket::insertGetId($data);


            } else if ($request->ticketType == 3) {
                // return $request;
                // $tbl = ticket_group::create($request->all());

                $date = Carbon::now()->format('Ymd');

                $ticket = 'GRP';
                $ticket .= $date;


                $tbl = ticket_group::create($request->except('ticket_id') + ["ticket_id" => $ticket]);

                $fileName = "";


                if ($request->remarks != "") {
                    $tremarks = new ticket_remarks_log;
                    $tremarks->ticket_id = $tbl->id;
                    $tremarks->user_id = $request->user_id;
                    $tremarks->remarks = $request->remarks;
                    $tremarks->save();
                }

                if ($request->selected_trouble != "") {
                    $items = (object)$request->selected_trouble;

                    foreach ($items as $item) {
                        $item =
                            json_decode(json_encode($item), true);

                        DB::table('tickets_trouble_types')->insert([
                            'ticket_id' => $tbl->id,
                            'trouble_type_id' => $item
                        ]);
                    }
                }

                if ($request->attachments != "") {
                    $attachments = (object) $request->attachments;
                    foreach ($attachments as $attachment) {
                        $attachment =
                            json_decode(json_encode($attachment), true);

                        $exploded = explode(',', $attachment);

                        $decoded = base64_decode($exploded[1]);

                        if (str_contains($exploded[0], "jpeg"))
                            $extension = "jpeg";
                        elseif (str_contains($exploded[0], "jpg"))
                            $extension = "jpg";
                        elseif (str_contains($exploded[0], "png"))
                            $extension = "png";
                        elseif (str_contains($exploded[0], "gif"))
                            $extension = "gif";
                        elseif (str_contains($exploded[0], "tiff"))
                            $extension = "tiff";
                        elseif (str_contains($exploded[0], "bmp"))
                            $extension = "bmp";
                        else
                            $extension = "txt";

                        $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                        $path = public_path() . "/attachments/" . $fileName;

                        file_put_contents($path, $decoded);

                        DB::table('ticket_attachments')
                            ->insert([
                                'ticket_id' => $tbl->id,
                                'filename' => $fileName

                            ]);
                    }
                }

                if ($request->area_id != "") {
                    $area = $request->area_id;
                    $client = $request->client_id;

                    $tbl =
                        DB::table('clients')
                        ->where('id', $client)
                        ->update(['area_id' => $area]);
                }

                foreach ($request->multipleClients as $client) {
                    $client = (object)$client;


                    $temp = DB::table('ticket_group_clients')->insert(
                        [
                            "ticket_group_id" => $tbl->id,
                            "client_id" => $client->id
                        ]
                    );
                }
            }


            return $this->subIndex($request->region_id);
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
    public function show(ticket $ticket)
    {
    }
    public function edit(ticket $ticket)
    {
    }
    public function update(Request $request, $id)
    {
        try {


            $tdate = $request->target_date;
            if ($request->Status_Ticket_id == 3 || $request->Status_Ticket_id == 5)
                $tdate = null;
            if ($request->Status_Ticket_id > 5)
                $tdate = null;


            $prev_status = $request->prev_status;
            $current_status = $request->current_status;
            if ($current_status != $request->Status_Ticket_id) {
                $prev_status = $current_status;
            }


            if ($current_status != 2)
                if ($request->Status_Ticket_id == 2)
                    $tdate = null;

            if ($request->ticketType == 1) {
                $logFrom = Ticket::findOrFail($id);

                $data = Ticket::where('id', $id)
                    ->update([
                        'ticket_id' => $request->ticket_id,
                        'client_id' => $request->client_id,
                        'complaint_new' => $request->complaint_new,
                        'bwmon' => $request->bwmon,
                        'cacti' => $request->cacti,
                        'device' => $request->device,
                        'loss' => $request->loss,
                        'downtime' => $request->downtime,
                        'rep_findings' => $request->rep_findings,
                        'rep_action' => $request->rep_action,
                        'rebatable' => $request->rebatable,
                        'complain' => $request->complain,
                        'findings' => $request->findings,
                        'action' => $request->action,
                        'remarks' => $request->remarks,
                        'report' => $request->report,
                        'connection_status' => $request->connection_status,
                        'prev_status' => $prev_status,
                        'Status_Ticket_id' => $request->Status_Ticket_id,
                        'area_id' => $request->area_id,
                        'technical_assigned' => $request->technical_assigned,
                        'downtime_hours' => $request->downtime_hours,
                        'to_soa' => $request->to_soa,
                        'date_time_fixed' => $request->date_time_fixed,
                        'target_date' => $tdate,
                        'team_assigned' => $request->team_assigned,
                        'updated_by' => $request->updated_by,
                        'updated_at' => Carbon::now()

                    ]);
            } elseif ($request->ticketType == 3) {
                // return $request;

                $logFrom = ticket_group::findOrFail($id);

                $data = ticket_group::where('id', $id)
                    ->update([
                        'ticket_id' => $request->ticket_id,
                        'complaint_new' => $request->complaint_new,
                        'bwmon' => $request->bwmon,
                        'cacti' => $request->cacti,
                        'device' => $request->device,
                        'loss' => $request->loss,
                        'downtime' => $request->downtime,
                        'rep_findings' => $request->rep_findings,
                        'rep_action' => $request->rep_action,
                        'rebatable' => $request->rebatable,
                        'complain' => $request->complain,
                        'findings' => $request->findings,
                        'action' => $request->action,
                        'remarks' => $request->remarks,
                        'report' => $request->report,
                        'connection_status' => $request->connection_status,
                        'prev_status' => $prev_status,
                        'Status_Ticket_id' => $request->Status_Ticket_id,
                        'technical_assigned' => $request->technical_assigned,
                        'downtime_hours' => $request->downtime_hours,
                        'to_soa' => $request->to_soa,
                        'date_time_fixed' => $request->date_time_fixed,
                        'target_date' => $tdate,
                        'team_assigned' => $request->team_assigned,
                        'updated_by' => $request->updated_by,
                        'updated_at' => Carbon::now()

                    ]);

                DB::table('ticket_group_clients')
                    ->where('ticket_group_id', '=', $id)
                    ->delete();

                foreach ($request->ticket_group_client as $client) {
                    $client = (object)$client;

                    // //insert
                    DB::table('ticket_group_clients')->insert(
                        [
                            "ticket_group_id" => $id,
                            "client_id" => $client->client_id
                        ]
                    );
                }
            }

            if ($request->selected_trouble != "") {
                $items = (object)$request->selected_trouble;
                DB::table('tickets_trouble_types')
                    ->where('ticket_id', '=', $id)
                    ->delete();

                foreach ($items as $item) {
                    $item =
                        json_decode(json_encode($item), true);



                    DB::table('tickets_trouble_types')
                        ->insert([
                            'ticket_id' => $id,
                            'trouble_type_id' => $item

                        ]);
                }
            }



            $fileName = "";
            if ($request->attachments != "") {


                $attachments = (object) $request->attachments;

                foreach ($attachments as $attachment) {
                    $attachment =
                        json_decode(json_encode($attachment), true);

                    $exploded = explode(',', $attachment);

                    $decoded = base64_decode($exploded[1]);

                    if (str_contains($exploded[0], "jpeg"))
                        $extension = "jpeg";
                    elseif (str_contains($exploded[0], "jpg"))
                        $extension = "jpg";
                    elseif (str_contains($exploded[0], "png"))
                        $extension = "png";
                    elseif (str_contains($exploded[0], "gif"))
                        $extension = "gif";
                    elseif (str_contains($exploded[0], "tiff"))
                        $extension = "tiff";
                    elseif (str_contains($exploded[0], "bmp"))
                        $extension = "bmp";
                    else
                        $extension = "txt";

                    $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                    $path = public_path() . "/attachments/" . $fileName;

                    file_put_contents($path, $decoded);

                    // return "w/attachments";

                    DB::table('ticket_attachments')
                        ->insert([
                            'ticket_id' => $id,
                            'filename' => $fileName

                        ]);
                }
            }
            // else {
            //     return "no attachment";
            // }



            if ($current_status == 2) {
                if ($request->Status_Ticket_id == 3) {
                    schedule::where('ticket_id', $request->id)
                        ->where('target_date', $request->target_date)
                        ->update([
                            'status' => 'ok'
                        ]);
                }
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Ticket id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $data
            );

            if ($request->filterIn == "multi") {
                //$request1 = new Illuminate\Http\Request($request->cbFilter);
                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {
                return $this->search_data($request->searchby, $request->tblFilter);
            } elseif ($request->filterIn == "date") {
                return $this->dateFilter($request->start, $request->end);
            } elseif ($request->filterIn == "badge") {
                return $this->search_data("tickets.Status_Ticket_id", $request->tblFilter);
            } else
                return $this->subIndex($request->region_id);
            // return $this->subIndex($request->region_id);
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

    public function updateAttachment(Request $request)
    {
        try {
            $fileName = $request->id;

            return response()->json($fileName);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            Ticket::destroy($id);
            return "deleted";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy1(Request $request)
    {
        try {
            // return $request;
            $id = $request->id;
            $region = $request->region;

            if ($request->ticketType == 1) {
                $tbl1 = Ticket::findOrFail($id);
                Ticket::destroy($id);

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Ticket id " . $id .
                        "\nOld Ticket: " . $tbl1
                );
            } elseif ($request->ticketType == 3) {
                DB::table('ticket_group_clients')
                    ->where('ticket_group_id', '=', $id)
                    ->delete();

                DB::table('ticket_groups')
                    ->where('id', $id)
                    ->delete();
            }


            if ($request->filterIn == "multi") {
                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {
                return $this->search_data($request->searchby, $request->tblFilter);
            } elseif ($request->filterIn == "date") {
                return $this->dateFilter($request->start, $request->end);
            } elseif ($request->filterIn == "badge") {
                return $this->search_data("tickets.Status_Ticket_id", $request->tblFilter);
            } else
                return $this->subIndex($region);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function eticketSummary($year, $region_id)
    {
        try {
            //
            //return "asd";
            $month = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            $retVal = [];
            $countClose = [];
            $countUrgent = [];
            $countPending = [];
            $countTechVisit = [];
            $datasets = [];
            for ($x = 0; $x < 12; $x++) {
                $from = new Carbon('first day of ' . $month[$x] . ' ' . $year);
                $to = new Carbon('last day of ' . $month[$x] . ' ' . $year);
                $to = $to->addDay();

                if ($region_id == 0) {
                    $temp1 = DB::table('tickets')
                        ->where("Status_Ticket_id", "1")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp1 += DB::table('tickets')
                        ->where("prev_status", "1")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp2 = DB::table('tickets')
                        ->where("Status_Ticket_id", "2")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp2 += DB::table('tickets')
                        ->where("prev_status", "2")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp3 = DB::table('tickets')
                        ->where("Status_Ticket_id", "3")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp3 += DB::table('tickets')
                        ->where("prev_status", "3")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp4 = DB::table('tickets')
                        ->where("Status_Ticket_id", "4")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();

                    $temp4 += DB::table('tickets')
                        ->where("prev_status", "4")
                        ->whereBetween("created_at", [$from, $to])
                        ->count();
                } else {

                    $temp1 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("Status_Ticket_id", "1")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp1 += DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("prev_status", "1")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp2 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("Status_Ticket_id", "2")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp2 += DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("prev_status", "2")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp3 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("Status_Ticket_id", "3")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp3 += DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("prev_status", "3")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp4 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("Status_Ticket_id", "4")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();

                    $temp4 += DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("prev_status", "3")
                        ->where("clients.region_id", $region_id)
                        ->whereBetween("tickets.created_at", [$from, $to])
                        ->count();
                }

                array_push($countPending, $temp1);
                array_push($countUrgent, $temp2);
                array_push($countTechVisit, $temp4);
                array_push($countClose, $temp3);
            }

            $data = new stdClass;
            $data->label = "Pending";
            $data->data = $countPending;
            $data->borderColor = [

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
            $data->borderWidth = 1;
            $datasets[0] = $data;

            $data = new stdClass;
            $data->label = "Urgent";
            $data->data = $countUrgent;
            $data->borderColor = [
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
            $data->borderWidth = 1;
            $datasets[1] = $data;

            $data = new stdClass;
            $data->label = "Tech Visit";
            $data->data = $countTechVisit;
            $data->borderColor = [
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
                "rgba(255, 99, 132, 1)"

            ];
            $data->borderWidth = 1;
            $datasets[2] = $data;

            $data = new stdClass;
            $data->label = "Fixed";
            $data->data = $countClose;
            $data->borderColor = [

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
            $data->borderWidth = 1;
            $datasets[3] = $data;

            $datenow = Carbon::now();
            $baseDate = Carbon::now();
            $baseDate = $baseDate->subDays(6);
            $datenow = $datenow->addDay();
            $weekly_label = [];
            $weekly_data = [];
            $weekly_data_all = [];
            while ($baseDate <= $datenow) {
                array_push($weekly_label, $baseDate->toFormattedDateString());
                if ($region_id == 0) {
                    $temp1 = DB::table('tickets')
                        ->where("Status_Ticket_id", "3")
                        ->where("created_at", "LIKE", "%" . $baseDate->toDateString() . "%")
                        ->count();

                    $temp2 = DB::table('tickets')
                        ->where("created_at", "LIKE", "%" . $baseDate->toDateString() . "%")
                        ->count();
                } else {

                    $temp1 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("Status_Ticket_id", "3")
                        ->where("clients.region_id", $region_id)
                        ->where("tickets.created_at", "LIKE", "%" . $baseDate->toDateString() . "%")
                        ->count();

                    $temp2 = DB::table('tickets')
                        ->join("clients", "tickets.client_id", "clients.id")
                        ->where("clients.region_id", $region_id)
                        ->where("tickets.created_at", "LIKE", "%" . $baseDate->toDateString() . "%")
                        ->count();
                }
                array_push($weekly_data, $temp1);
                array_push($weekly_data_all, $temp2);
                $baseDate->addDay();
            }

            $c1 = collect();
            $c1->put("yearDataset", $datasets);
            $c1->put("weekLabel", $weekly_label);
            $c1->put("weekly_data_all", $weekly_data_all);
            $c1->put("weekData", $weekly_data);
            return response()->json($c1);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateTargetDate(Request $request)
    {
        try {


            $extraMsg = "";

            $logFrom = Ticket::where('id', $request->ticket_id)
                ->select("target_date")->first();

            $team = $request->team_id;
            $tdate = $request->target_date;
            if ($request->team_only == true) {
                DB::table('schedules')
                    ->where('ticket_id', $request->ticket_id)
                    ->where('target_date', $request->current_target_date)
                    ->update(['team_id' => $request->team_id]);

                Ticket::where('id', $request->ticket_id)
                    ->update([
                        'team_assigned' => $team,
                    ]);
                $extraMsg = "(team_only)";
            } else if ($request->date_only == true) {
                DB::table('schedules')
                    ->where('ticket_id', $request->ticket_id)
                    ->where('target_date', $request->current_target_date)
                    ->update(['target_date' => $request->target_date]);

                Ticket::where('id', $request->ticket_id)
                    ->update([
                        'target_date' => $tdate,
                    ]);
                $extraMsg = "(date_only)";
            } else {
                schedule::create($request->all());
                Ticket::where('id', $request->ticket_id)
                    ->update([
                        'team_assigned' => $team,
                        'target_date' => $tdate,
                    ]);
            }
            $logTo = Ticket::where('id', $request->ticket_id)
                ->select("target_date")->first();
            $msg = "Update target date from: " . $logFrom . " to: " . $logTo . " " .
                $extraMsg . " of Ticket ID: " . $request->ticket_id;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateTargetDate",
                "message",
                $msg
            );

            if ($request->filterIn == "multi") {
                //$request1 = new Illuminate\Http\Request($request->cbFilter);
                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {
                return $this->search_data($request->searchby, $request->tblFilter);
            } elseif ($request->filterIn == "date") {
                return $this->dateFilter($request->start, $request->end);
            } elseif ($request->filterIn == "badge") {
                return $this->search_data("tickets.Status_Ticket_id", $request->tblFilter);
            } else
                return $this->subIndex($request->region_id);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "updateTargetDate",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function dateFilter($start, $end)
    {
        try {



            if ($end == null)
                $end = $start;

            $from = new Carbon($start);
            $to = new Carbon($end);
            $to = $to->addDay();

            $tbl = Ticket::with(['Client.pon.olt', 'User', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
                ->leftJoin("areas", "tickets.area_id", "areas.id")
                ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                ->whereBetween("tickets.created_at", [$from, $to])
                ->select(
                    "areas.id as area_id",
                    "areas.name as area_name",
                    "ticket_statuses.name as statname",
                    "tickets.Status_Ticket_id as current_status",
                    "complaint_lists.name as compname",
                    "tickets.complaint_new as current_complaint",
                    "complaint_lists.*",
                    "ticket_statuses.*",
                    "tickets.*"
                )
                ->orderBy('target_date')
                ->orderBy('ticket_statuses.name')
                ->get();

            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function filterByDate(Request $request)
    {
        try {
            if ($request->cbFilter != null) {
                $temp = (object) $request->cbFilter;
                unset($request);
                $request = $temp;
            }

            if ($request->end == null)
                $request->end = $request->start;

            $from = new Carbon($request->start);
            $to = new Carbon($request->end);
            $to = $to->addDay();

            $tbl = Ticket::with(['Client.pon.olt', 'User', 'remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
                ->leftJoin("areas", "tickets.area_id", "areas.id")
                ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                ->whereBetween("tickets.created_at", [$from, $to])
                ->select(
                    "areas.id as area_id",
                    "areas.name as area_name",
                    "ticket_statuses.name as statname",
                    "tickets.Status_Ticket_id as current_status",
                    "complaint_lists.name as compname",
                    "tickets.complaint_new as current_complaint",
                    "complaint_lists.*",
                    "ticket_statuses.*",
                    "tickets.*"
                )
                ->orderBy('target_date')
                ->orderBy('ticket_statuses.name')
                ->get();

            return $this->ForQuery($tbl);
            // return $this->ForQuery($tbl->get());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function multipleFilter(Request $request)
    {
        // return response()->json($request);
        try {
            if ($request->cbFilter != null) {
                $temp = (object) $request->cbFilter;
                unset($request);
                $request = $temp;
            }
            $data = (object) $request->data;

            $tbl = Ticket::with(['remarks_log.user', 'remarks_log.replies.user', 'trouble_type.type', 'ticket_attachment'])
                ->leftJoin("areas", "tickets.area_id", "areas.id")
                ->join("clients", "tickets.client_id", "clients.id")
                ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                ->join("users", "tickets.user_id", "users.id")
                ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                ->leftJoin("pons", "clients.pon_id", "pons.id")
                ->leftJoin("olts", "pons.olt_id", "olts.id")
                ->leftJoin("regions", "clients.region_id", "regions.id")
                ->select(
                    "pons.*",
                    "olts.*",
                    "areas.id as area_id",
                    "areas.name as area_name",
                    "clients.name as cname",
                    "clients.location as location",
                    "clients.contact as contact",
                    "clients.ip_assigned as ip_assigned",
                    "clients.vlan as vlan",
                    "clients.onu as onu",
                    "regions.name as region_name",
                    "ticket_statuses.name as statname",
                    "tickets.Status_Ticket_id as current_status",
                    "complaint_lists.name as compname",
                    "tickets.complaint_new as current_complaint",
                    "complaint_lists.*",
                    "users.name as uname",
                    "users.*",
                    "ticket_statuses.*",
                    "tickets.*"
                )

                ->orderBy('tickets.updated_at', 'desc');


            if ($request->region)
                $tbl->where("clients.region_id", $data->region_id);
            if ($request->address)
                $tbl->where("clients.location", 'like', '%' . $data->address . '%');
            // if ($request->cname)
            //     $tbl->where("complain", 'like', '%' . $data->address . '%');

            if ($request->con_status)
                $tbl->where("connection_status", $data->con_status);

            if ($request->status)
                $tbl->where("ticket_statuses.id", $data->status_id);

            if ($request->area) {

                $area = [];
                foreach ($data->area_id as $item) {
                    $data = (object)$item;
                    $data = $data->id;
                    array_push($area, $data);
                }
                $tbl->whereIn("tickets.area_id", $area);
            }

            if ($request->target_date) {
                $date = new Carbon($data->target_date);
                $tbl->where("target_date", $date->toDateString());
            }

            if ($request->aging) {
                $data = (object)$request->data;
                $date = (object)$data->date_created;
                $from = new Carbon($date->start);
                $to = new Carbon($date->end);

                if ($to == null)
                    $to = $from;
                $tbl->whereDate("tickets.created_at", ">=", $from->toDateString())->whereDate("tickets.created_at", "<=", $to->toDateString());
            }
            if ($request->fixed) {
                $data = (object)$request->data;
                $date = (object)$data->date_fixed;
                $from = new Carbon($date->start);
                $to = new Carbon($date->end);

                if ($to == null)
                    $to = $from;
                $tbl->whereDate("tickets.date_time_fixed", ">=", $from->toDateString())->whereDate("tickets.date_time_fixed", "<=", $to->toDateString());
            }
            if ($request->findings)
                $tbl->where("complain", 'like', '%' . $data->findings . '%');
            if ($request->consolidated_tech)
                $tbl->where("technical_assigned", 'like', '%' . $data->consolidated_tech . '%');
            if ($request->username)
                $tbl->where("tickets.user_id", $data->user_id);


            return $this->ForQuery($tbl->get());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function getComplaint()
    {
        $tbl = DB::table('tickets')->groupBy('complain')->get('complain');

        $temp = [];

        foreach ($tbl as $item) {
            array_push($temp, $item->complain);
        }
        return response()->json($temp);
    }
    public function getMonitoring($region_id)
    {
        try {
            $dtnow = Carbon::now();
            $c1 = collect();
            if ($region_id == 0) {
                $tbl = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new0", "complaint_lists.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('target_date', $dtnow->toDateString())
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "users.name as created",
                        "complaint_lists.*",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('today', $tbl);

                $dtnow = $dtnow->addDay();
                $tbl1 = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('target_date', $dtnow->toDateString())
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",

                        "users.name as created",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('tomorrow', $tbl1);

                $tbl2 = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('tickets.Status_Ticket_id', '!=', 3)
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "users.name as created",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('week', $tbl2);
                //->where(DB::raw('yearweek(DATE(target_date), 1)'), DB::raw('yearweek(curdate(), 1)'))

                $tbl3 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_prev', $tbl3);

                $tbl4 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE()'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_today', $tbl4);

                $tbl5 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_tom', $tbl5);

                $tbl6 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_prev', $tbl6);

                $tbl7 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE()'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_today', $tbl7);

                $tbl8 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_tom', $tbl8);
                //ELSE ----------------------------------------by regionsssssssssssssssssssssssssssssssssssssssssssssssssss
            } else {

                $tbl = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('target_date', $dtnow->toDateString())
                    ->where("clients.region_id", $region_id)
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "users.name as created",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('today', $tbl);

                $dtnow = $dtnow->addDay();
                $tbl1 = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('target_date', $dtnow->toDateString())
                    ->where("clients.region_id", $region_id)
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "users.name as created",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('tomorrow', $tbl1);

                $tbl2 = DB::table('tickets')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->join("users", "tickets.user_id", "users.id")
                    ->where('tickets.Status_Ticket_id', '!=', 3)
                    ->where('tickets.Status_Ticket_id', '!=', 6)
                    ->where('tickets.Status_Ticket_id', '!=', 5)
                    //->where(DB::raw('yearweek(DATE(target_date), 1)'), DB::raw('yearweek(curdate(), 1)'))
                    ->where("clients.region_id", $region_id)
                    ->select(
                        "clients.name as cname",
                        "tickets.ticket_id as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "users.name as created",
                        "ticket_statuses.*",
                        "clients.*",
                        "tickets.*"
                    )
                    ->get();
                $c1->put('week', $tbl2);

                $tbl3 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_prev', $tbl3);

                $tbl4 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE()'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_today', $tbl4);

                $tbl5 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '1')
                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teama_tom', $tbl5);

                $tbl6 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE() - INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_prev', $tbl6);

                $tbl7 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE()'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_today', $tbl7);

                $tbl8 = DB::table('schedules')
                    ->join("tickets", "schedules.ticket_id", "tickets.id")
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->join("ticket_statuses", "tickets.Status_Ticket_id", "ticket_statuses.id")
                    ->leftJoin("complaint_lists", "tickets.complaint_new", "complaint_lists.id")
                    ->where("clients.region_id", $region_id)
                    ->where('schedules.team_id', '2')
                    ->where('schedules.target_date', DB::raw('CURDATE() + INTERVAL 1 DAY'))
                    ->select(
                        "clients.name as cname",
                        "clients.acc_no as subid",
                        "ticket_statuses.name as statname",
                        "complaint_lists.name as compname",
                        "tickets.complaint_new as current_complaint",
                        "complaint_lists.*",
                        "clients.*",
                        "tickets.*",
                        "schedules.status as sched_stat"
                    )
                    ->get();
                $c1->put('teamb_tom', $tbl8);
            }


            return $c1;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function CalculateRebates(Request $request)
    {
        $from = new Carbon($request->from);
        $to = new Carbon($request->to);
        $to = $to->addMinute();
        $totalHr = $from->diffInHours($to);
        $hrsCopy = $totalHr;
        $rate = 0;
        if ($totalHr > 24) {
            while ($totalHr > 24) {
                $rate++;
                $totalHr -= 24;
            }
        }
        if ($totalHr >= 15) {
            $rate++;
            $totalHr = 0;
        }
        if ($totalHr >= 12) {
            $rate += 0.8;
            $totalHr = 0;
        }
        if ($totalHr >= 9) {
            $rate += 0.6;
            $totalHr = 0;
        }
        if ($totalHr >= 6) {
            $rate += 0.4;
            $totalHr = 0;
        }
        if ($totalHr >= 3) {
            $rate += 0.2;
            $totalHr = 0;
        }
        if ($totalHr >= 1) {
            $rate += 0.1;
            $totalHr = 0;
        }

        $downtimeRate = ($request->mrr / $request->days) * $rate;
        $payable = $request->mrr - $downtimeRate;
        $c1 = collect();
        $c1->put("totalHour", $hrsCopy);
        $c1->put("rate", $rate);
        $c1->put("downtimeRate", $downtimeRate);
        $c1->put("payable", $payable);
        return response()->json($c1);
    }
    public function updateRebates(Request $request)
    {
        try {
            Ticket::where('id', $request->id)
                ->update([
                    'downtime_hours' => $request->totalHour,
                ]);

            return $this->subIndex($request->region_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function checkTicket($id)
    {

        $ticket = DB::table('tickets')
            ->where('client_id', $id)
            ->where('Status_Ticket_id', '!=', '3')
            ->first();

        $temp = DB::table('clients')
            ->select('area_id')
            ->where('id', $id)
            ->value('area_id');

        $collection = collect();
        $collection->put("ticket", $ticket);
        $collection->put("area", $temp);

        return response()->json($collection);
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

    public function sendText(Request $request)
    {

        try {
            $numArray = $request->number;
            $string = $request->msg;
            $msg = str_replace("\n", "%0a", $string);
            $number = implode(', ', array_column($numArray, 'contact'));

            $ret =  \Logger::instance()->send_text($number, $msg);

            $sendTo = [];
            $temp = new stdClass;
            // $temp->email = "pbismonte@dctechmicro.com";
            // $temp->name = "Peter Pogi";
            $temp->email = "mdmorta@dctechmicro.com";
            $temp->name = "Mice Gwapa";
            array_push($sendTo, $temp);

            $CCTO = [];
            $temp1 = new stdClass;
            $temp1->email = "helpdesk@dctechmicro.com";
            $temp1->name = "Helpdesk";
            array_push($CCTO, $temp1);

            \Logger::instance()->mailerZimbra(
                "Auto Text Logger",
                $ret,
                "",
                "Infosystem",
                $sendTo,
                $CCTO
            );


            \Logger::instance()->logText(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "sent",
                "message",
                "Sent Message: " . $ret
            );
            return response()->json(['message' => 'sent successfully', 'response' => $ret]);
        } catch (\Exception $ex) {
            \Logger::instance()->logText(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "sent",
                "message",
                "Sent Message: " . $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function troubleTypes()
    {
        $tbl =
            DB::table('trouble_types')
            ->get();

        return response()->json($tbl);
    }
    public function complaintList()
    {
        $tbl =
            DB::table('complaint_lists')
            ->get();

        return response()->json($tbl);
    }
    public function getTrend(Request $request)
    {

        $trendRegion = $request->trendRegion;
        $trendTime = $request->trendTime;
        $trendDate = "";



        if ($trendTime == 1) {
            if ($trendRegion != null) {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where("tickets.created_at", ">=", Carbon::now()->subDay())
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->where("clients.region_id", '=', $trendRegion)
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            } else {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where("tickets.created_at", ">=", Carbon::now()->subDay())
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            }
        } else if ($trendTime == 2) {
            if ($trendRegion != null) {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where("tickets.created_at", ">=", Carbon::now()->subDays(2))
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->where("clients.region_id", '=', $trendRegion)
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            } else {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where("tickets.created_at", ">=", Carbon::now()->subDays(2))
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            }
        } else if ($trendTime == 3) {
            if ($trendRegion != null) {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->where("clients.region_id", '=', $trendRegion)
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            } else {
                $data = DB::table('tickets')
                    ->join('complaint_lists', 'complaint_lists.id', '=', 'tickets.complaint_new')
                    ->join("clients", "tickets.client_id", "clients.id")
                    ->select('complaint_lists.name', DB::raw('count(*) as total'))
                    ->where('tickets.Status_Ticket_id', '!=', '3')
                    ->groupBy('complaint_lists.name')
                    ->orderBy('total', 'desc')
                    ->take(3)
                    ->get();
            }
        }



        $c1 = collect();
        $c1->put('data', $data);
        return response()->json($c1);
    }

    public function checkAreas($id)
    {

        // return $id;
        $tbl = DB::table('areas')
            // ->select('name as name', 'id as id')
            ->select('id', 'name')
            ->where('region_id', $id)
            ->get();
        // ->pluck('name', 'id');

        return response()->json($tbl);
    }
}
