<?php

namespace App\Http\Controllers;

use App\Client;
use App\Client_detail;
use App\billing;
use App\customer_payment;
use App\Job_order;
use App\client_history;
use App\clients_cancelled;
use App\ticket_remarks_log;
use App\client_status_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\olt;
use stdClass;
use Carbon\Carbon;
use Exception;

class ClientController extends Controller
{
    private $cname = "ClientController";
    private $mainComand;
    private $mainComand_cancel;
    public function __construct()
    {
        $this->mainComand = Client::with([
            "package", "modem", "package_type", "region.user", "region.area", "area", "branch.area.region", "pon",
            "sales.user", "sales.agent", "sales_agent", "engineer", "bucket", "clientDetail",
            "remarks_log.user", "remarks_log.replies.user", "status_log.user", "billings.client"
        ]);
        $this->mainComand_cancel = clients_cancelled::with([
            "package", "modem", "package_type", "region.user", "region.area", "area", "branch.area.region", "pon",
            "sales.user", "sales.agent", "sales_agent", "engineer", "bucket", "clientDetail",
            "remarks_log.user", "remarks_log.replies.user", "status_log.user", "billings.client"
        ]);
    }

    public function index()
    {
        $tbl = (clone $this->mainComand)->orderBy("name")
            ->get();
        //return $tbl[0]->pon;
        $temp = [];
        $countExpire = 0;
        $countNearExpire = 0;
        foreach ($tbl as $item) {
            $id = $item->pon;
            $olts = olt::where("id", $id["olt_id"])
                ->first();

            $item->olt = (object) $olts;
            $item->pon1 = (object) $item->pon;
            $item->region1 = $item->region["name"];
            $item->regionfilter = "Filter: " . $item->region["name"];
            $item->package_type_name =  $item->package_type["name"];
            $item->termExpire = "unknown";
            if ($item->date_activated != null) {
                $ddddd = new Carbon($item->date_activated);
                $item->date_activate = $ddddd->toFormattedDateString();
                $dt_date_activated = new Carbon($item->date_activated);
                $term = $item->term;
                $dtNow1 = Carbon::now();
                $dateExpire = $dt_date_activated->addMonths($term);

                $monthDiff = $dtNow1->diffInMonths($dateExpire);
                $item->termStatus = $monthDiff . " Months left";

                $dtNow2 = $dtNow1->copy()->addMonths(1);
                $dtNow3 = $dtNow1->copy()->addMonths(3);
                if ($dateExpire < $dtNow3) {
                    $item->termStatus = "Term < 3 months";
                }
                if ($dateExpire < $dtNow2 && $dateExpire > $dtNow1) {
                    $item->termStatus = "Term < 1 months";
                    $item->termfilter = "Filter: NearToExpire";
                    $countNearExpire++;
                }
                if ($dateExpire < $dtNow1) {
                    if ($item->status  == "Disconnected") {
                        $item->termStatus = "Disconnected";
                    } else {
                        $countExpire++;
                        $item->termStatus = "Expired";
                        $item->termfilter = "Filter: Expired";
                    }
                }


                $item->termExpire = $dateExpire->toFormattedDateString();;
            }
            array_push($temp, $item);
        }
        $c1 = collect();
        $c1->put("items", $temp);
        $c1->put("countExpire", $countExpire);
        $c1->put("countNearExpire", $countNearExpire);
        return response()->json($c1);
    }

    public function getAll()
    {
        $tbl = (clone $this->mainComand)
            ->orderBy("name");

        return $this->ForQuery($tbl->get());
    }
    public function search_data($by, $search, $state)
    {
        if ($state == "true") {
            $tbl = (clone $this->mainComand_cancel)
                ->where($by, 'like', '%' . $search . '%')
                ->orderBy("name");
        } else
            $tbl = (clone $this->mainComand)
                ->where($by, 'like', '%' . $search . '%')
                ->orderBy("name");
        return $this->ForQuery($tbl->get());
    }
    public function cancelled($region_id)
    {
        $tbl = (clone $this->mainComand_cancel)->orderBy("name");
        return $this->ForQuery($tbl->get());
    }
    public function subIndex($region_id)
    {
        $tbl = (clone $this->mainComand)
            ->take(80)
            ->orderBy("name");
        if ($region_id != 0) {
            $tbl->where("region_id", $region_id);
        }
        return $this->ForQuery($tbl->get());
    }
    public function ForQuery($tbl)
    {
        //return $tbl;
        $temp = [];
        $countExpire = 0;
        $countWFS = 0;
        $countNearExpire = 0;
        $billings = [];
        foreach ($tbl as $item) {
            foreach ($item->remarks_log as $r_log) {
                $r_log->commentVisibility = 'hide';
            }
            // $id = $item->pon;
            // $olts = olt::where("id", $id["olt_id"])
            //     ->first();

            // $item->olt = (object) $olts;
            // $item->pon1 = (object) $item->pon;
            if (isset($item->clientDetail["aging"]))
                $item->aging = $item->clientDetail["aging"];
            else
                $item->aging = null;
            $item->contract_status = false;
            if ($item->contract != null)
                $item->contract_status = true;

            $item->region1 = $item->region["name"];
            $item->regionfilter = "Filter: " . $item->region["name"];
            $item->package_type_name =  $item->package_type["name"];
            $item->package_type_id =  $item->package_type["id"];
            $item->termExpire = "unknown";

            if ($item->date_activated != null) {
                $ddddd = new Carbon($item->date_activated);
                $item->date_activate = $ddddd->toFormattedDateString();

                $dt_date_activated = new Carbon($item->date_activated);
                $term = $item->term;
                $dtNow1 = Carbon::now();
                $dateExpire = $dt_date_activated->addMonths($term);

                $monthDiff = $dtNow1->diffInMonths($dateExpire);
                $item->termStatus = $monthDiff . " Months left";

                $dtNow2 = $dtNow1->copy()->addMonths(1);
                $dtNow3 = $dtNow1->copy()->addMonths(3);
                if ($dateExpire < $dtNow3) {
                    $item->termStatus = "Term < 3 months";
                }
                if ($dateExpire < $dtNow2 && $dateExpire > $dtNow1) {
                    $item->termStatus = "Term < 1 months";
                    $item->termfilter = "Filter: NearToExpire";
                    $countNearExpire++;
                }
                if ($dateExpire < $dtNow1) {
                    if ($item->status  == "Disconnected") {
                        $item->termStatus = "Disconnected";
                    } else {
                        $countExpire++;
                        $item->termStatus = "Expired";
                        $item->termfilter = "Filter: Expired";
                    }
                }
                $item->termExpire = $dateExpire->toFormattedDateString();;
            }
            if ($item->clientDetail != null) {
                if ($item->clientDetail["status"] == null) {
                    $item->wfc = true;
                } else
                    $item->wfc = false;
            }
            if (count($item->billings) > 0) {
                $temp1 = $item->billings;
                $billings = array_merge($billings, $temp1->toArray());
            }
            $item->amount_pay = 0;
            array_push($temp, $item);
        }

        $countWFC = Client::join('client_details', "client_details.client_id", "clients.id")
            ->whereNull('client_details.status')
            ->count();
        $countNoContract = Client::join('client_details', "client_details.client_id", "clients.id")
            ->whereNull('contract')
            ->count();
        $countNoDOP = Client::join('client_details', "client_details.client_id", "clients.id")
            ->whereNull('client_details.aging')
            ->count();
        $countWFS = Client::join('client_details', "client_details.client_id", "clients.id")
            ->whereNotNull('client_details.aging')
            ->whereNotNull('contract')
            ->where('wfs', 0)
            ->count();
        $countExpire = Client::whereRaw("DATE_ADD(`date_activated`, interval term MONTH) < curdate() and `status` != 'Disconnected'")
            ->count();

        $countNearExpire = Client::whereRaw("DATE_ADD(`date_activated`, interval term MONTH) > curdate()")
            ->whereRaw("DATE_ADD(`date_activated`, interval term MONTH) < DATE_ADD(curdate(), interval 1 month)")
            ->count();
        $c1 = collect();
        $c1->put("items", $temp);
        $c1->put("billings", $billings);
        $c1->put("countNoContract", $countNoContract);
        $c1->put("countNoDOP", $countNoDOP);
        $c1->put("countWFC", $countWFC);
        $c1->put("countWFS", $countWFS);
        $c1->put("countExpire", $countExpire);
        $c1->put("countNearExpire", $countNearExpire);
        return response()->json($c1);
    }
    public function getClients($region_id)
    {
        $tbl = "";
        if ($region_id == 0) {
            $tbl = Client::join("regions", "regions.id", "clients.region_id")
                ->join("package_types", "package_types.id", "clients.package_type_id")
                ->join("packages", "packages.id", "clients.package_id")
                ->select(
                    "clients.id as id",
                    "clients.location as address",
                    "clients.account_no as account_no",
                    "clients.acc_no as acc_no",
                    "clients.name as name",
                    "clients.email_add as email",
                    "region_id",
                    "regions.name as region_name",
                    "package_types.name as package_type_name",
                    "package_types.id as package_type_id",
                    "packages.mrr as package_mrr"
                )
                ->orderBy("clients.name")
                ->get();
        } else {
            $tbl = Client::join("regions", "regions.id", "clients.region_id")
                ->join("package_types", "package_types.id", "clients.package_type_id")
                ->join("packages", "packages.id", "clients.package_id")
                ->select(
                    "clients.id as id",
                    "clients.location as address",
                    "clients.account_no as account_no",
                    "clients.acc_no as acc_no",
                    "clients.name as name",
                    "clients.email_add as email",
                    "region_id",
                    "regions.name as region_name",
                    "package_types.name as package_type_name",
                    "package_types.id as package_type_id",
                    "packages.mrr as package_mrr"
                )
                ->orderBy("clients.name")
                ->get();
        }
        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        // return $request;
        try {
            DB::beginTransaction();
            $dtnow = Carbon::now();
            $data = Client::create($request->all());

            $account_no = $request->packagetype . "-" . $dtnow->year .
                $dtnow->month . "-" . $data->id;
            DB::table("clients")
                ->where("id", $data->id)
                ->update([
                    "account_no" => $account_no,
                    "acc_no" => $account_no
                ]);
            if ($request->isNew == '0') {

                $cd = new Client_detail;

                $cd->client_id = $data->id;
                $cd->aging = $request->aging;
                $cd->mapping_status = 1;
                $cd->foc_layout = "Pending";
                //$cd->otc = "Paid";
                $cd->applied_date =  $request->aging;
                $cd->cable_category =  $request->cable_category;
                $cd->foc_length =  $request->foc_length;
                $cd->created_at = Carbon::now();
                $cd->updated_at = Carbon::now();
                if (!$request->wfc)
                    $cd->status = "Confirmed";

                $cd->save();
                //message
                if (true) {
                    $message = "
                    <html>
                    <head>
                    </head>
                    <body>
                    <p>Hi Team,</p>
                    <p>Good day!</p>
                    <br />
                    <p>THIS IS AN AUTOMATIC GENERATED E-MAIL FROM INET INFO SYSTEM. </p>
                    <p>From: <b>" . $request->user_name . "</b></p>
                    <b>Sales in-charge: " . $request->salesInCharge_name . "</b>
                    <p>Please be informed of the new " . $request->packagetype . " client.:</p>
                    <p><u><b>Client Information</b></u></p>
                    <table>
                    <tr>
                        <td>Account Name:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->name . "</td>
                    </tr>
                    <tr>
                        <td>Owner's Name:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->owner_name . "</td>
                    </tr>
                    <tr>
                        <td>Contact Person:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->contact_person . "</td>
                    </tr>
                    <tr>
                        <td>Business Type:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->business_type . "</td>
                    </tr>
                    <tr>
                        <td>Contact #:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->contact . "</td>
                    </tr>
                    <tr>
                        <td>Email add:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->email_add . "</td>
                    </tr>
                    <tr>
                        <td>Address: </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->location . "</td>
                    </tr>
                    </table>
                    <br />
                    <p><u><b>Package Details</b></u> </p>
                    <table>
                    <tr>
                        <td>Package Code: </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->packCode . "</td>
                    </tr>
                    <tr>
                        <td>Maximum Speed:</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->packSpeed . "</td>
                    </tr>
                    <tr>
                        <td>CIR: </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->packCIR . "</td>
                    </tr>
                    <tr>
                        <td>Internet MRR: </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->packMRR . "</td>
                    </tr>
                    <tr>
                        <td>OTC: </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->OTC . "</td>
                    </tr>
                    <tr>
                        <td>Term :</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>" . $request->term . " month/s</td>
                    </tr>
                    </table>
                    <br />
                    <p><b>Tech Sales: " . $request->engineer_name . "</b></p>

                    <br />
                    <p>" . nl2br($request->email_note) . "</p>
                    <br />
                    <p>Thank you!</p>
                    <p>PLEASE DO NOT REPLY TO THIS E-MAIL </p>
                    </body>
                    </html>";
                }
                //$emails = explode(", ", $request->contact);
                // \Logger::instance()->mailer(
                //     "ADVISORY: New Client " . $request->name,
                //     $message,
                //     $request->user_email,
                //     $request->user_name,
                //     $request->sendTo,
                //     $request->CCTO
                // );

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    "Create new Client: id: " . $data->id . ",client_details: " . $cd
                );
                // $text = "Hi " . $request->contact_person .
                //     " from Dctech internet, your account " . $request->name .
                //     " is already in our system for processing. Thanks for choosing our service.  " .
                //     "%0aThis is automated text, Sender does not support replies";
                // $contacts = explode(", ", $request->contact);
                // foreach ($contacts as $contact) {
                //     \Logger::instance()->send_text($contact, $text);
                // }
            }
            //\Logger::instance()->log()
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Client: " . $data
            );
            DB::commit();
            return $this->subIndex($request->region_id);
        } catch (Exception $ex) {
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

            if ($ex->errorInfo[1] == 1062)
                return response()->json(["error" => "Account Number Already Exist"], 500);
            else
                return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function show(client $client)
    {
        //
    }
    public function edit(client $client)
    {
        //
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $client  = Client::findOrFail($id);
            $logFrom = $client->replicate();
            $input = $request->all();

            $client->fill($input)->save();
            $logTo = $client;

            //FOR WFC/STATUS
            $tbll =  DB::table("client_details")
                ->where("client_id", $id)
                ->first();
            if (true) {

                if (!empty($tbll)) {
                    if ($tbll->status != 'finished') {
                        if ($request->wfc)
                            $status = null;
                        else
                            $status = "Confirmed";
                        $target_date = $tbll->target_date;
                        if (
                            $status == "done" &&
                            $tbll->mapping_status &&
                            $tbll->modem_status
                        ) {
                            $status = "done";
                        } else {

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
                                // $target_date = null;
                            }
                        }

                        if (
                            $tbll->foc_layout == "Indoor layout done" ||
                            $tbll->foc_layout == null
                        ) {
                            if ($status == "done" || $status == "Confirmed")
                                $status = "Confirmed";
                            else
                                $status = null;
                            // $target_date = null;
                        }
                    } else
                        $status = 'finished';
                }
            }

            if (!empty($tbll)) {
                DB::table("client_details")
                    ->where("client_id", $id)
                    ->update([
                        "status" => $status,
                        "date_activated" => $request->date_activated
                    ]);
            }
            //message
            if (true) {
                $message = "
                <html>
                <head>
                </head>
                <body>
                <p>Hi Team,</p>
                <p>Good day!</p>
                <br />
                <p>THIS IS AN AUTOMATIC GENERATED E-MAIL FROM INET INFO SYSTEM. </p>
                <p>From: <b>" . $request->user_name . "</b></p>
                <b>Sales in-charge: " . $request->salesInCharge_name . "</b>
                <p>Please be informed of the new " . $request->packagetype . " client.:</p>
                <p><u><b>Client Information</b></u></p>
                <table>
                <tr>
                    <td>Account Name:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->name . "</td>
                </tr>
                <tr>
                    <td>Owner's Name:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->owner_name . "</td>
                </tr>
                <tr>
                    <td>Contact Person:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->contact_person . "</td>
                </tr>
                <tr>
                    <td>Business Type:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->business_type . "</td>
                </tr>
                <tr>
                    <td>Contact #:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->contact . "</td>
                </tr>
                <tr>
                    <td>Email add:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->email_add . "</td>
                </tr>
                <tr>
                    <td>Address: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->location . "</td>
                </tr>
                </table>
                <br />
                <p><u><b>Package Details</b></u> </p>
                <table>
                <tr>
                    <td>Package Code: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->packCode . "</td>
                </tr>
                <tr>
                    <td>Maximum Speed:</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->packSpeed . "</td>
                </tr>
                <tr>
                    <td>CIR: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->packCIR . "</td>
                </tr>
                <tr>
                    <td>Internet MRR: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->packMRR . "</td>
                </tr>
                <tr>
                    <td>OTC: </td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->OTC . "</td>
                </tr>
                <tr>
                    <td>Term :</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>" . $request->term . " month/s</td>
                </tr>
                </table>
                <br />
                <p><b>Tech Sales: " . $request->engineer_name . "</b></p>

                <br />
                <p>" . nl2br($request->email_note) . "</p>
                <br />
                <p>Thank you!</p>
                <p>PLEASE DO NOT REPLY TO THIS E-MAIL </p>
                </body>
                </html>";
            }

            //$emails = explode(", ", $request->contact);
            if ($request->re_email)
                \Logger::instance()->mailer(
                    "ADVISORY: New Client " . $request->name,
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
                "update",
                "message",
                "update client id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            DB::commit();
            return $this->subIndex($request->user_region_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );

            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function update_per_row(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;

            if ($request->row == "coordinates") {
                $tbl1 = Client::where("id", $id)
                    ->select("lat", "lng")
                    ->first();
                $oldData = new \stdClass;
                $oldData->data = $tbl1->lat . ", " . $tbl1->lng;
                $data = (object) $request->data;

                DB::table("clients")
                    ->where("id", $id)
                    ->update([
                        "lat" => $data->lat,
                        "lng" => $data->lng
                    ]);

                $newData = new \stdClass;
                $newData->data = $data->lat . ", " . $data->lng;
            } elseif ($request->row == "package_code") {
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
                                "price" => $item->price_update,
                                "balance" => $item->balance_update
                            ]);
                    }
                }

                $newData = new \stdClass;
                $newData->data = $data->package_id . ", " . $data->package_type_id;
            } elseif ($request->row == "branch") {
                $tbl1 = Client::where("id", $id)
                    ->select("branch_id", "area_id", "region_id")
                    ->first();
                $oldData = new \stdClass;
                $oldData->data = "branch_id" . $tbl1->branch_id . ", area_id: " . $tbl1->area_id . ", region_id: " . $tbl1->region_id;
                $data = (object) $request->data;
                DB::table("clients")
                    ->where("id", $id)
                    ->update([
                        "branch_id" => $data->branch_id,
                        "area_id" => $data->area_id,
                        "region_id" => $data->region_id
                    ]);

                $newData = new \stdClass;
                $newData->data = "branch_id" . $data->branch_id . ", area_id: " . $data->area_id . ", region_id: " . $data->region_id;;
            } else {
                $oldData = Client::where("id", $id)
                    ->select($request->row . " as data")
                    ->first();

                DB::table("clients")
                    ->where("id", $id)
                    ->update([
                        $request->row => $request->data,
                    ]);

                $newData = Client::where("id", $id)
                    ->select($request->row . " as data")
                    ->first();
            }


            if ($request->row == "status") {

                client_status_history::insert([
                    'status' => $request->data,
                    'client_id' => $request->id,
                    'user_id' => $request->user_id,
                    'date_change' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $ch = new client_history;

            $ch->client_id = $id;
            $ch->title = "Update " . $request->row;
            $ch->details = "From: " . $oldData->data . " To: " . $newData->data;
            $ch->user_id = $request->user_id;
            $ch->save();

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update_per_row",
                "message",
                "update client id " . $id . "\nRow: " . $request->row .
                    "\nFrom: " . $oldData->data . "\nTo: " . $newData->data
            );
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

    public function destroy($id)
    {
        //
    }

    public function updateDOP(Request $request)
    {
        try {
            DB::beginTransaction();
            $dtnow = Carbon::now();
            //FOR WFC/STATUS
            $tbll =  DB::table("client_details")
                ->where("id", $request->client_detail["id"])
                ->first();

            if (true) {

                if (!empty($tbll)) {
                    if ($tbll->status != 'finished') {
                        if ($request->wfc)
                            $status = null;
                        else
                            $status = "Confirmed";
                        $target_date = $tbll->target_date;
                        if (
                            $status == "done" &&
                            $tbll->mapping_status &&
                            $tbll->modem_status
                        ) {
                            $status = "done";
                        } else {

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
                                // $target_date = null;
                            }
                        }

                        if (
                            $tbll->foc_layout == "Indoor layout done" ||
                            $tbll->foc_layout == null
                        ) {
                            if ($status == "done" || $status == "Confirmed")
                                $status = "Confirmed";
                            else
                                $status = null;
                            // $target_date = null;
                        }
                    } else
                        $status = 'finished';

                    DB::table("client_details")
                        ->where("id", $request->client_detail["id"])
                        ->update([
                            "status" => $status,
                            "otc" => "Paid", //$request->otcc,
                            "aging" => $request->billing_date,
                            "created_at" =>  $request->billing_date,
                            "updated_at" => $request->billing_date,
                        ]);
                }
            }
            // $account_no = $request->package_type_name . "-" . $dtnow->year .
            //     $dtnow->month . "-" . $request->id;

            // DB::table("clients")
            //     ->where("id", $request->id)
            //     ->update([
            //         "account_no" => $account_no,
            //         "acc_no" => $account_no
            //     ]);
            //DARI ANG UPDATE SA BILLINGSSSS
            $dated = new Carbon($request->billing_date);
            $online_id = null;
            if ($request->isPayOnline) {
                $online_id = $request->banking_payment_code_id;
                DB::table("banking_payment_codes")
                    ->where("id", $request->banking_payment_code_id)
                    ->update(["status" => "used"]);
            }

            //DARI dapat ma ilhan kung advance or cashbond
            $addiRemark = "";
            if ($request->package_type_name != 'RES') {
                $addiRemark = "Cash Bond - ";
            } else {
                $addiRemark = "Cash Advance - ";
            }
            $ddaattaa = [
                [
                    "client_id" => $request->id,
                    "user_id" => $request->user_id,
                    "payment_method_id" => $request->payment_method_id,
                    "or_number" => $request->or_number,
                    "remarks" => "OTC - " . $request->remarks,
                    "amount" => $request->OTCPay,
                    "date" => $request->billing_date,
                    "banking_payment_code_id" => $online_id,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ],
                [
                    "client_id" => $request->id,
                    "user_id" => $request->user_id,
                    "payment_method_id" => $request->payment_method_id,
                    "or_number" => $request->or_number,
                    "remarks" => $addiRemark . $request->remarks,
                    "amount" => $request->cashBond,
                    "date" => $request->billing_date,
                    "banking_payment_code_id" => $online_id,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ],
            ];


            customer_payment::insert($ddaattaa);

            //insert bill para sa cashbond
            $bil = new billing;
            $bil->client_id = $request->id;
            $bil->date = $request->billing_date;
            if ($request->staggered == "1") {
                $bil->item = "OTC Staggered"; // if change - ClientDetailCon - refOTCStaggered
                $bil->description = "OTC balance -" . $request->balance
                    . "- payable for -" . $request->numMonths
                    . "- month/s";
            } else {
                $bil->item = "OTC full payment";
                $bil->description = "OTC full payment";
            }
            $bil->price =  $request->OTCPay;
            $bil->created_at = Carbon::now();
            $bil->updated_at = Carbon::now();
            $bil->save();

            if ($request->package_type_name != 'RES') {
                $bil2 = new billing;
                $bil2->client_id = $request->id;
                $bil2->date = $request->billing_date;
                $bil2->item = "CashBond Bill"; // if change - ClientDetailCon - refCashbondPayment
                $bil2->description = "CashBond Bill";
                $bil2->price =  $request->cashBond;
                $bil2->created_at = Carbon::now();
                $bil2->updated_at = Carbon::now();
                $bil2->save();
            }


            //message
            if (true) {
                $message = "
                <html>
                <head>
                </head>
                <body>
                <p>Dear Ma'am/Sir:
                <br />
                Good day!
                <br />
                <br />
                <p>THIS IS AN AUTOMATIC GENERATED E-MAIL FROM INET INFO SYSTEM. </p>
                <p>From: <b>" . $request->user_name . "</b></p>

                <p>Please be informed that <b>" . $request->name . "</b> already paid the OTC </p>

                <table>
                <tr>
                <td>Account No.:</td>
                <td>" . $request->acc_no . "</td>
                </tr>
                 <tr>
                <td>Paid on: </td>
                <td>" . $dated->toFormattedDateString() . "</td>
                </tr>
                </table>
                <p>Account Verified</p>
                <p>With this kindly please proceed to installation</p>
                <br />
                " . $request->email_note . "
                <br />
                <br />
                Thank you and God Bless. <br / > CNC TEAM</p>
                </body>
                </html>";
            }
            //TEMPORARY COMMENT remove this comment when deploy
            \Logger::instance()->mailer(
                "ADVISORY: Client " . $request->name . " OTC payment OK",
                $message,
                $request->user_email,
                $request->user_name,
                $request->sendTo,
                $request->CCTO
            );

            //TEMPORARY COMMENT remove comment when all data accurate.
            // $text = "Good day!" .
            //     "%0aThis is from DCTECH" .
            //     "%0awe have received your payment dated " . $dated->toFormattedDateString() .
            //     " and your account has beed verified on " . $dtnow->toFormattedDateString() .
            //     ".%0aThis is automated text, Sender does not support replies";
            // $contacts = explode(", ", $request->contact);
            // foreach ($contacts as $contact) {
            //     \Logger::instance()->send_text(
            //         $contact,
            //         $text
            //     );
            //     sleep(1);
            // }
            DB::commit();
            return $this->filterNoDOP($request->user_region_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function destroy1(Request $request)
    {

        try {
            $id = $request->id;

            $tblchk = Job_order::where('client_id', $id)->first();
            $tbl = Client_detail::where("client_id", $id)->first();
            $tbl1 = Client::findOrFail($id);

            if (!empty($tblchk)) {

                DB::table("job_orders")
                    ->where("id", $tblchk->id)
                    ->update([
                        "details" => $tbl1->name,
                        "location" => $tbl1->location,
                        "client_id" => 0,
                        "client_detail_id" => null
                    ]);
            }
            Client_detail::where("client_id", $id)->delete();
            Client::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroy1",
                "message",
                "delete client id " . $request->id .
                    "\nOld Client_details: " . $tbl . "\nOld Client: " . $tbl1
            );

            if (
                $request->filterIn == "multi"
            ) {
                return $this->multipleFilter($request);
            } elseif ($request->filterIn == "search") {
                return $this->search_data($request->searchby, $request->tblFilter, $request->state);
            } else
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
            return response()->json(["error" => "There was a problem deleting this client."], 500);
        }
    }

    public function filterByDate(Request $request)
    {
        try {
            if ($request->end == null)
                $request->end = $request->start;

            $from = new Carbon($request->start);
            $to = new Carbon($request->end);
            //$to = $to->addDay();

            if ($request->region_id == 0) {
                $tbl = (clone $this->mainComand)
                    ->whereBetween("date_activated", [$from, $to])
                    ->get();
            } else {
                $tbl = (clone $this->mainComand)
                    ->where("region_id", $request->region_id)
                    ->whereBetween("date_activated", [$from, $to])
                    ->get();
            }
            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "filterByDate",
                "Error",
                $ex->getMessage()
            );
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function filterNoDOP($region_id)
    {
        $tbl = (clone $this->mainComand)
            ->whereHas("clientDetail", function (Builder $query) {
                $query->whereNull("aging");
            })
            ->orderBy("name");
        if ($region_id != 0) {
            $tbl->where("region_id", $region_id);
        }
        return $this->ForQuery($tbl->get());
    }

    public function filterNoContract($region_id)
    {
        $tbl =  (clone $this->mainComand)
            ->whereNull("contract")
            ->take(500)
            ->orderBy("name");
        if ($region_id != 0) {
            $tbl->where("region_id", $region_id);
        }
        return $this->ForQuery($tbl->get());
    }

    public function filterWFC($region_id)
    {
        $tbl =  (clone $this->mainComand)
            ->whereHas("clientDetail", function (Builder $query) {
                $query->whereNull("status");
            })
            ->take(500)
            ->orderBy("name");

        return $this->ForQuery($tbl->get());
    }

    public function filterWFS($region_id)
    {
        $tbl =  (clone $this->mainComand)
            ->whereHas("clientDetail", function (Builder $query) {
                $query->whereNotNull("aging");
            })
            ->whereNotNull("contract")
            ->where("wfs", 0)
            ->take(500)
            ->orderBy("name");

        return $this->ForQuery($tbl->get());
    }

    public function filterExpired($region_id)
    {
        $tbl =  (clone $this->mainComand)
            ->whereRaw("DATE_ADD(`date_activated`, interval term MONTH) < curdate() and `status` != 'Disconnected'")
            ->orderBy("name");

        return $this->ForQuery($tbl->get());
    }

    public function filterCease($region_id)
    {
        $tbl =  (clone $this->mainComand)
            ->whereRaw("DATE_ADD(`date_activated`, interval term MONTH) > curdate()")
            ->whereRaw("DATE_ADD(`date_activated`, interval term MONTH) < DATE_ADD(curdate(), interval 1 month)")
            ->orderBy("name");
        return $this->ForQuery($tbl->get());
    }

    public function multipleFilter(Request $request)
    {

        try {
            // if ($request->filterIn == "multi") {
            if ($request->cbFilter != null) {
                $temp = (object) $request->cbFilter;
                unset($request);
                $request = $temp;
            }
            $data = (object) $request->data;

            $tbl =  (clone $this->mainComand)
                ->orderBy("name");


            if ($request->has_sched)
                $tbl->whereHas("clientDetail", function ($query) {
                    $query->whereNotNull("client_id");
                });

            if ($request->area) {
                $area_temp = [];
                foreach ($data->area_id as $ii) {
                    $dd = (object)$ii;
                    $dd = $dd->id;
                    array_push($area_temp, $dd);
                }
                $tbl->whereIn("area_id", $area_temp);
            }

            if ($request->region)
                $tbl->where("region_id", $data->region_id);
            if ($request->address)
                $tbl->where("location", "like", "%" . $data->location . "%");
            if ($request->referral)
                $tbl->where("referral", "like", "%" . $data->referral . "%");
            if ($request->business_type)
                $tbl->where("business_type", "like", "%" . $data->business_type . "%");
            if ($request->term)
                $tbl->where("term", $data->term);
            if ($request->contract) {
                if ($data->contract == "Done")
                    $tbl->whereNotNull("contract");
                if ($data->contract == "Undone")
                    $tbl->whereNull("contract");
            }
            if ($request->sales)
                $tbl->where("sales_id", $data->sales_id);
            if ($request->engineer)
                $tbl->where("engineers_id", $data->engineers_id);
            if ($request->package)
                $tbl->where("package_id", $data->package_id);
            if ($request->package_type)
                $tbl->where("package_type_id", $data->package_type_id);
            if ($request->bucket)
                $tbl->where("bucket_id", $data->bucket_id);
            if ($request->communication_protocol)
                $tbl->where("communication_protocol", $data->communication_protocol);
            if ($request->status)
                $tbl->where("status", $data->status);

            if ($request->date_activated) {
                if ($request->date_activated_type == "range") {
                    $date_activated = (object) $data->date_activated;
                    $from = new Carbon($date_activated->from);
                    $to = new Carbon($date_activated->to);
                    $tbl->whereBetween("date_activated", [$from->toDateString(), $to->toDateString()]);
                }
                if ($request->date_activated_type == "active") {
                    $tbl->whereNotNull("date_activated");
                }
                if ($request->date_activated_type == "not_active") {
                    $tbl->whereNull("date_activated");
                }
            }
            if ($request->created_at) {
                $created_at = (object) $data->created_at;
                $from = new Carbon($created_at->from);
                $to = new Carbon($created_at->to);
                $tbl->whereBetween("created_at", [$from->toDateString(), $to->toDateString()]);
            }
            if ($request->id) {
                $id_from = $data->id_from;
                $id_to = $data->id_to;
                $tbl->whereBetween("id", [$id_from, $id_to]);
            }


            return $this->ForQuery($tbl->get());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function updatePackage(Request $request)
    {
        try {
            DB::table("clients")
                ->where("id", $request->id)
                ->update(["package_type_id" => $request->package_type_id]);
            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "updatePackage",
                "Error",
                $ex->getMessage()
            );
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function getSched(Request $request)
    {
        try {
            $c1 = collect();
            $tbl1 = DB::table("client_details")
                ->join("schedules", "client_details.id", "schedules.client_detail_id")
                ->where("client_details.client_id", $request->id)
                ->get();
            $c1->put("installation", $tbl1);

            $tbl2 = DB::table("tickets")
                ->join("schedules", "tickets.id", "schedules.ticket_id")
                ->where("tickets.client_id", $request->id)
                ->get();

            $c1->put("ticket", $tbl2);
            return response()->json($c1);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "getSched",
                "Error",
                $ex->getMessage()
            );
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function updateDateActivated(Request $request)
    {
        if ($request->date_activated != null) {

            DB::table("client_details")
                ->where("client_id", $request->id)
                ->update(["date_activated" => $request->date_activated]);

            DB::table("clients")
                ->where("id", $request->id)
                ->update(["date_activated" => $request->date_activated]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateDateActivated",
                "message",
                "update date_activated of client id " . $request->id .
                    "\nFrom: " . $request->date_activate . "\nTo: " . $request->date_activated .
                    "\nRemarks: " . $request->rm_remarks
            );
        }
    }
    public function updateContract(Request $request)
    {
        try {
            DB::beginTransaction();

            $conStatus = Carbon::now();

            DB::table("clients")
                ->where("id", $request->id)
                ->update([
                    "contract" => $conStatus
                ]);

            //email
            if ($request->sendEmail) {
                $message = "<html>
                <head>
                </head>
                <body>
                <p>Dear Ma'am/Sir,</p>
                <br>
                <p>The contract of <b>" . $request->name . "</b> has been signed. </p>
                <br>
                <p>Thank you and God Bless.</p>
                </body
                </head>
                </html>";

                \Logger::instance()->mailer(
                    "ADVISORY: Contract Singed - " . $request->name,
                    $message,
                    $request->user_email,
                    $request->user_name,
                    $request->sendTo,
                    $request->CCTO
                );
            }
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateContract",
                "message",
                "update Contract client ID" . $request->id
            );
            DB::commit();
            return $this->filterNoContract($request->user_region_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function updateSubscription(Request $request)
    {
        try {

            DB::table("clients")
                ->where("id", $request->id)
                ->update([
                    "wfs" => true
                ]);
            return $this->filterWFS($request->user_region_id);
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function testlog()
    {
        \Logger::instance()->log(
            Carbon::now(),
            "user_id",
            "user_name",
            $this->cname,
            "store",
            "message",
            "TEST MESSAGE"
        );
        return "tested";
    }
    public function getContacts()
    {

        // $contacts = DB::table('clients')
        //     ->where('contact', '!=', 'null')
        //     ->get('contact');
        // $temp = [];

        // foreach ($contacts as $item) {

        //     array_push($temp, $item->contact);
        // }

        // return response()->json($temp);

        $tbl =
            DB::table('clients')
            ->select('name', 'contact', 'email_add as email')
            ->where('contact', '!=', 'null')
            ->get();

        return response()->json($tbl);
    }

    public function getClientEmail()
    {
        $tbl = DB::table('clients')
            ->select('name', 'email_add as email')
            ->where('email_add', '!=', 'null')
            ->where('email_add', '!=', '')
            ->get();

        return response()->json($tbl);
    }
    public function cancelClient(Request $request)
    {
        // return "ok";
        try {
            DB::beginTransaction();
            $data = clients_cancelled::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Cancelled Client: " . $data
            );
            Client::destroy($request->id);
            DB::commit();
            return $this->subIndex($request->region_id);
        } catch (Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "cancelClient",
                "Error",
                $ex->getMessage()
            );

            if ($ex->errorInfo[1] == 1062)
                return response()->json(["error" => "Account Number Already Exist"], 500);
            else
                return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function retrieveClient(Request $request)
    {
        // return $request;
        try {
            DB::beginTransaction();
            $data = Client::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "retrieveClient",
                "message",
                "Cancelled Client: " . $data
            );
            clients_cancelled::destroy($request->id);
            DB::commit();
            return $this->cancelled($request->region_id);
        } catch (Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "retrieveClient",
                "Error",
                $ex->getMessage()
            );

            if ($ex->errorInfo[1] == 1062)
                return response()->json(["error" => "Account Number Already Exist"], 500);
            else
                return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
    public function updateRows(Request $request)
    {
        try {
            DB::beginTransaction();

            $values = array();
            if ($request->data != null)
                foreach ($request->data as $item) {
                    $item = (object) $item;
                    $temp = array($item->row => $item->val);
                    $values = array_merge($values, $temp);
                }

            $oldData = Client::where("id", $request->client_id)->first();

            $newData = Client::where("id", $request->client_id)->update($values);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateRows",
                "message",
                "Old data: " . json_encode($oldData) . "/r/n" .
                    "New data: " . json_encode($newData)
            );

            DB::commit();
            return "ok";
        } catch (Exception $ex) {
            DB::rollBack();
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateRows",
                "Error",
                $ex->getMessage()
            );
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function updateAutoBill(Request $request)
    {

        $state = DB::table("clients")->select('autoBill')->where("id", $request->client_id)->value('autoBill');

        if ($state == 'No') {
            DB::table("clients")
                ->where("id", $request->client_id)
                ->update(["autoBill" => 'Yes']);
        } else {
            DB::table("clients")
                ->where("id", $request->client_id)
                ->update(["autoBill" => 'No']);
        }

        if (
            $request->filterIn == "multi"
        ) {
            return $this->multipleFilter($request);
        } elseif ($request->filterIn == "search") {
            return $this->search_data($request->searchby, $request->tblFilter, $request->state);
        } else
            return $this->subIndex($request->region_id);

        \Logger::instance()->log(
            Carbon::now(),
            $request->user_id,
            $request->user_name,
            $this->cname,
            "updateAutoBill",
            "message",
            "update auto-bill of client id " . $request->client_id
        );
    }
}
