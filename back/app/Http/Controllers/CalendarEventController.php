<?php

namespace App\Http\Controllers;

use App\calendar_event;
use App\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\billing;
use App\bill_state_list;
use App\bucket;
use App\Region;
use Illuminate\Support\Facades\DB;
use App\Helpers\SSH2;
use App\Package;
use App\Package_type;
use stdClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CalendarEventController extends Controller
{
    private $cname = "CalendarEventController";

    public function index()
    {
    }
    public function subIndex($region_id)
    {
        if ($region_id == 0) {
            $tbl = DB::table('calendar_events')->get();
        } else {
            $tbl = DB::table('calendar_events')->where('region_id', $region_id)->get();
        }
        return response()->json($tbl);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = calendar_event::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "Create new calendar_event: " . $data
            );
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
    public function show($id)
    {
        $tbl = calendar_event::where("id", $id)->get();

        return response()->json($tbl);
    }
    public function edit(calendar_event $calendar_event)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = calendar_event::findOrFail($id);
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
                "update client id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
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
            calendar_event::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy1(Request $request)
    {
        try {
            $tbl1 = calendar_event::findOrFail($request->id);
            calendar_event::destroy($request->id);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroy1",
                "message",
                "delete calendar_event id " . $request->id .
                    "\nOld calendar_event: " . $tbl1
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
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function testSSH(Request $request)
    {
        try {

            $ssh = new SSH2('202.137.115.11');
            //$ssh = new SSH2('202.137.112.10');
            if ($ssh->auth("inf0syst3m", "un1ty1nd1vers1ty")) {
                $ssh->exec("export BASS_RDIR='/usr/local/foxbuckv3';/bin/bash /usr/local/foxbuckv3/commands/cbs 37 10 0 1 subscription --show Valeroso_Rex_bw1");
                //$ssh->exec("export BASS_RDIR='/usr/local/foxbuckv3';/bin/bash /usr/local/foxbuckv3/commands/cbs 73 10 0 1 subscription --show ReyDabalos_bw1");
                return $ssh->output();
                return $ssh->getTable();
            } else {
                //$ssh->disconnect();
                return "error";
            }

            //return \Logger::instance()->test("subscription --show");

            // $connection = /ssh2_connect('hosting-new.dctechmicro.com', 22);
            // if (!$connection) {
            //     return "nect";
            // } else {
            //     return "con";
            // }
            // \ssh2_auth_password($connection, 'root', '8tnhbaa0');
            // $stream = \ssh2_exec($connection, '/usr/local/bin/php -i');
            //$ssh->disconnect();
            return "worked";
        } catch (\Throwable $th) {
            return "error sa try catch";
        }
    }
    public function testEmail(Request $request)
    {
        if ($request->email == "gmail") {
            \Logger::instance()->mailerGmail(
                "Test Email send",
                "body test",
                $request->user_email,
                $request->user_name,
                $request->sendTo,
                $request->CCTO
            );
            return "ok";
        } else {
            return  \Logger::instance()->mailerZimbra(
                "Test Email send",
                "body test",
                $request->user_email,
                $request->user_name,
                $request->sendTo,
                $request->CCTO
            );
            return "ok";
        }
    }
    public function testAutoCommand()
    {
        set_time_limit(0);
        $MailMessage = [];
        try {
            //code...
            $dateNow = Carbon::now();
            $date2daysBefore = $dateNow->copy()->addDays(2);
            $date5daysAfter = $dateNow->copy()->subDays(5);
            $date8daysAfter = $dateNow->copy()->subDays(8);
            $c = collect();

            //2day before due date
            $tbl2daysBefore = billing::where("date", $date2daysBefore->toDateString())
                ->whereHas("client", function ($query) {
                    $query->where("package_type_id", "4");
                })
                ->groupBy('client_id')
                ->get();

            $tblToday = billing::where("date", $dateNow->toDateString())
                ->whereHas("client", function ($query) {
                    $query->where("package_type_id", "4");
                })->groupBy('client_id')
                ->get();


            $tbl5daysAfter = billing::where("date", $date5daysAfter->toDateString())
                ->whereHas("client", function ($query) {
                    $query->where("package_type_id", "4");
                })->groupBy('client_id')
                ->get();
            $tbl8daysAfter = billing::where("date", $date8daysAfter->toDateString())
                ->whereHas("client", function ($query) {
                    $query->where("package_type_id", "4");
                })->groupBy('client_id')
                ->get();



            foreach ($tbl2daysBefore as $item) {
                $tbl = billing::with(['client'])
                    ->where("client_id", $item->client_id)
                    ->where("date", "<=", $date2daysBefore)
                    ->groupBy('client_id')
                    ->selectRaw('*, sum(balance) as sum')
                    ->first();

                $client = $tbl->client;
                $contact = "";
                if ($tbl->sum > 0) {
                    if ($client->contact != null) {
                        $temp = explode(',', $client->contact);
                        $x = 0;
                        foreach ($temp as $cont) {
                            $cont = $this->numberOnly($cont);
                            if (strlen($cont) == 11) {
                                if ($x > 0)
                                    $contact .= ", ";
                                $contact .= $cont;
                                $x++;
                            }
                        }
                    }

                    $textMessage = "To Our Valued Subscriber: %0a %0aWe would like to inform you that your Dctech Internet bill is due on "
                        . $dateNow->copy()->addDays(2)->toFormattedDateString() . ". We encourage you to settle on or before the due date.";
                    if ($contact != "") {
                        $testStatus = "ddddd";
                        $testStatus = \Logger::instance()->send_text(
                            $contact,
                            $textMessage
                        );

                        $msg = "2days before Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: " . $testStatus . " <br>";
                    } else
                        $msg = "2days before Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: Contact number invalid <br>";


                    array_push($MailMessage, $msg);
                    $c->push(['msg' => $msg, 'region_id' => $client->region_id]);
                }
            }
            foreach ($tblToday as $item) {
                $tbl = billing::with(['client'])
                    ->where("client_id", $item->client_id)
                    ->where("date", "<=", $dateNow)
                    ->groupBy('client_id')
                    ->selectRaw('*, sum(balance) as sum')
                    ->first();
                $client = $tbl->client;
                $contact = "";
                if ($tbl->sum > 0) {
                    if ($client->contact != null) {
                        $temp = explode(',', $client->contact);
                        $x = 0;
                        foreach ($temp as $cont) {
                            $cont = $this->numberOnly($cont);
                            if (strlen($cont) == 11) {
                                if (
                                    $x > 0
                                )
                                    $contact .= ", ";
                                $contact .= $cont;
                                $x++;
                            }
                        }
                    }
                    $textMessage = "To Our Valued Subscriber: %0a %0aJust a friendly reminder your Dctech Internet account is already due for settlement."
                        . " Kindly settle your account within 24 hours to avoid service interruption.  %0a %0a" .
                        "Please disregard this message if payment has been made. Thank you.";

                    if ($contact != "") {
                        $testStatus = "ddddd";
                        $testStatus = \Logger::instance()->send_text(
                            $contact,
                            $textMessage
                        );
                        $msg = "Due date Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: " . $testStatus . " <br>";
                    } else
                        $msg = "Due date Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: Contact number invalid <br>";


                    array_push($MailMessage, $msg);

                    $c->push(['msg' => $msg, 'region_id' => $client->region_id]);
                }
            }

            foreach ($tbl5daysAfter as $item) {
                $tbl = billing::with(['client'])
                    ->where("client_id", $item->client_id)
                    ->where("date", "<=", $date5daysAfter)
                    ->groupBy('client_id')
                    ->selectRaw('*, sum(balance) as sum')
                    ->first();
                $client = $tbl->client;
                $contact = "";
                if ($tbl->sum > 0) {
                    if ($client->contact != null) {
                        $temp = explode(',', $client->contact);
                        $x = 0;
                        foreach ($temp as $cont) {
                            $cont = $this->numberOnly($cont);
                            if (strlen($cont) == 11) {
                                if (
                                    $x > 0
                                )
                                    $contact .= ", ";
                                $contact .= $cont;
                                $x++;
                            }
                        }
                    }
                    $textMessage = "To Our Valued Subscriber: %0a %0aPlease note that your Dctech Internet (Account no:" . $client->acc_no
                        . ") will be subject for disconnection due to unpaid balance of PHP" . number_format($tbl->sum, 2)
                        . ". To avoid inconvenience kindly settle your balance immediately.  %0a %0a" .
                        "Please disregard this message if payment has been made. Thank you.";

                    if ($contact != "") {
                        $testStatus = "ddddd";
                        $testStatus = \Logger::instance()->send_text(
                            $contact,
                            $textMessage
                        );
                        $msg = "5 days after Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: " . $testStatus . " <br>";
                    } else
                        $msg = "5 days after Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: Contact number invalid <br>";


                    array_push($MailMessage, $msg);

                    $c->push(['msg' => $msg, 'region_id' => $client->region_id]);
                }
            }

            foreach ($tbl8daysAfter as $item) {
                $tbl = billing::with(["client"])
                    ->where("client_id", $item->client_id)
                    ->where("date", "<=", $date8daysAfter)
                    ->groupBy("client_id")
                    ->selectRaw("*, sum(balance) as sum")
                    ->first();
                $client = $tbl->client;
                $contact = "";
                if ($tbl->sum > 0) {
                    $msg = "something wrong in checking bucket";
                    if ($client->bucket_id != null) {
                        if ($client->subscription_name != null) {
                            $bucket = bucket::findOrFail($client->bucket_id);
                            $ssh = new SSH2($bucket->IP);
                            if ($ssh->auth($bucket->username, $bucket->password)) {
                                $ssh->exec("export BASS_RDIR='/usr/local/foxbuckv3';/bin/bash /usr/local/foxbuckv3/commands/cbs " .
                                    $bucket->user_id . " 10 0 1 subscription --show " .
                                    $client->subscription_name);
                                $ssh_table =  $ssh->getTable();
                                if (count($ssh_table) > 0) {

                                    foreach ($ssh_table as $ssh_item) {
                                        if ($ssh_item['name'] == $client->subscription_name) {
                                            if ($ssh_item['statusName'] == "activated") {
                                                // $ssh->exec("sudo /bin/bash /usr/local/foxbuckv3/commands/cbs " .
                                                //     $bucket->user_id . " 10 0 1 subscription --deact " .
                                                //     $client->subscription_name);
                                                $msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: " . $ssh->output() . " <br>";
                                                //$msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: disconnected <br>";
                                                // \Logger::instance()->logText(
                                                //     Carbon::now(),
                                                //     "",
                                                //     "",
                                                //     $this->cname,
                                                //     "sent",
                                                //     "Client Notice",
                                                //     "Sent Message: " . $msg
                                                // );
                                                //UPDATE STATUS ACCOUNT TO DISCON
                                                //temp comment
                                                // DB::table('clients')
                                                //     ->where('id', $item->client_id)
                                                //     ->update(['status' => "Disconnected"]);

                                            } else
                                                $msg = "Discon Client Name: " . $client->name . " is already disconnected";
                                        }
                                    }
                                } else {
                                    $msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: Subscription name not found. <br>";
                                }
                            } else {
                                $msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: can't connect to bucket <br>";
                            }
                        } else
                            $msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: No subscription <br>";
                    } else
                        $msg = "Discon Client Name: " . $client->name . ", Balance: " . $tbl->sum . ", Status: No Bucket <br>";

                    //$ssh->disconnect();
                    array_push($MailMessage, $msg);

                    $c->push(["msg" => $msg, "region_id" => $client->region_id]);
                }
            }


            $regions = Region::all();
            $sendTo = [];
            $temp = new stdClass;
            $temp->email = "pbismonte@dctechmicro.com";
            $temp->name = "Peter Pogi";
            array_push($sendTo, $temp);

            $CCTO = [];
            $temp1 = new stdClass;
            $temp1->email = "jmnituda@dctechmicro.com";
            $temp1->name = "Jezah";
            array_push($CCTO, $temp1);

            foreach ($regions as $region) {
                //check empty
                if ($region->name != "711 clients") {

                    list($mssgg, $gggmmss) = $c->partition(function ($item) use ($region) {
                        return $item["region_id"] == $region->id;
                    });
                    if (count($mssgg) > 0)
                        \Logger::instance()->mailerZimbra(
                            "SMS Logs for " . $region->name . " Region Clients",
                            $mssgg->implode("msg", " "),
                            "",
                            "Infosystem",
                            $sendTo,
                            $CCTO
                        );
                }
            }



            return "ok";
            $tbl = billing::with(["client"])
                ->whereHas("client", function ($query) {
                    $query->where("package_type_id", "4");
                })
                ->where("date", "<=", $dateNow)
                ->groupBy("client_id")
                ->selectRaw("*, sum(balance) as sum")
                ->get();
            return $tbl;

            // $temp = new stdClass;
            // $temp->email = "pbismonte@dctechmicro.com";
            // $temp->name = "Peter Pogi";
            // $sendTo = [];
            // array_push($sendTo, $temp);inet
            // $CCTO = null;
            // \Logger::instance()->mailerGmail(
            //     "Test Email send",
            //     "body test",
            //     "auto",
            //     "Bot",
            //     $sendTo,
            //     $CCTO
            // );
            // return $sendTo;
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex, "message" => $MailMessage], 500);
        }
    }
    public function automateBill()
    {
        set_time_limit(0);
        $MailMessage = [];

        try {


            $clientsToEmail = Client::where('autoBill', '=', 'Yes')->get();


            foreach ($clientsToEmail as $client) {
                $datebill = billing::where('client_id', $client->id)->latest('date')->value('date');
                $package_mrr = Package::where('id', $client->package_id)->value('mrr');
                $package_type_name = Package_type::where('id', $client->package_type_id)->value('name');



                $datebill = new Carbon($datebill);
                $contain = [];

                $dateCoverFrom = $datebill->addDay();
                $dateCoverTo = $datebill->copy()->addMonthsNoOverflow();
                $dateCoverTo =  $dateCoverTo->subDay();

                $datebill_copy = $datebill->copy()->addMonthsNoOverflow();
                $dateCoverFrom_copy = $dateCoverFrom->copy()->addMonthsNoOverflow();
                $dateCoverTo_copy = $dateCoverTo->copy()->addMonthsNoOverflow();

                $data = [
                    "client_id" => $client->id,
                    "date" => $datebill_copy->subDay()->toDateString(),
                    "item" => "MRR - Internet (" . $package_type_name . ")",
                    "description" => "MRR - " . $package_type_name . " " .
                        $dateCoverFrom_copy->toFormattedDateString() . " - " .
                        $dateCoverTo_copy->toFormattedDateString(),
                    "price" => $package_mrr,
                    "balance" => $package_mrr,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),

                ];
                array_push($contain, $data);
                billing::insert($contain);
            }

            return $contain;
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex, "message" => $MailMessage], 500);
        }
    }

    public function automateEmail()
    {

        $dateNow = Carbon::now();
        $date5daysBefore = $dateNow->copy()->addDays(5);

        $bills = billing::with(['client'])
            ->where('date', $date5daysBefore->toDateString())
            ->whereHas("client", function ($query) {
                $query->where("autoBill", '=', 'Yes');
            })
            ->get();

        foreach ($bills as $bill) {
            $bill = (object) $bill;
            $client = (object) $bill->client;
            $date = Carbon::parse($bill->date)->format('M. d Y');
            $priceFormatted = number_format($bill->price, 2);
            $balanceFormatted = number_format($bill->balance, 2);

            //store bill statement
            $statement_id = DB::table('bill_statements')->insertGetId([
                'client_id' => $bill->client_id,
                'date' => date("Y-m-d"),
                'due_date' => $date,
                'amount_due' => $balanceFormatted
            ]);

            // store bill state list
            $billList = [];

            $temp = [
                "bill_statement_id" => $statement_id,
                'date' => $date,
                'description' => $bill->description,
                'priceFormated' => $priceFormatted,
                'balanceFormated' => $balanceFormatted
            ];

            array_push($billList, $temp);


            bill_state_list::insert($billList);


            //email
            $email =
                "<div>
                <table>
                    <tr>
                        <td>

                        </td>
                    </tr>
                </table>
                </div>
                <style>
                table {
                border-collapse: collapse;
                 background-repeat: no-repeat;
                    background-position: center;
                    background-size: 800px;
                    background:'https://i.ibb.co/7WS6qS7/dctech-logo.png';

                }

                table,
                th,
                td {
                border: 1px solid black;
                padding: 10px;
                }

                table {
                width: 100%;
                }
                th {
                color: black;
                text-align: center;
                }

                td {
                color: black;
                text-align: left;
                }
                .center {
                margin-left: auto;
                margin-right: auto;
                }
                p {
                font-style: arial;
                text-align: left;
                font-size: 13px;
                }
                .normalTbl {
                border-style: none;
                }
                .bottomfet {
                background-color: green;
                }
                .watermarkImg {
                position: absolute;
                width: 800px;
                opacity: 0.2;
                z-index: -1;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                }
                </style>
            ";

            $subject = "Billing Statement No. " . $statement_id;
            $sendTo = "mdmorta@dctechmicro.com";
            $CCTO = "mdmorta@dctechmicro.com";


            return \Logger::instance()->mailer(
                $subject,
                $email,
                "",
                "",
                $sendTo,
                $CCTO
            );
        }

        return 'AYY OKAY!';
        // return $clientBills;
        // return $id;
    }
    function numberOnly($string)
    {
        $string = str_replace("-", "", $string); // Replaces all "-" with "".
        $string = str_replace(" ", "", $string); // Replaces all " " with "".

        return preg_replace("/[^0-9]+/", "", $string); // Numbers only.
    }
    public function download_database()
    {
        try {
            $tables = array(
                "areas",
                "banking_payment_codes",
                "billings",
                "bill_statements",
                "bill_state_lists",
                "branches",
                "regions",
                "buckets",
                "business_types",
                "calendar_events",
                "categories",
                "clients",
                "client_details",
                "client_histories",
                "closures",
                "closure_types",
                "customer_payments",
                "engineers",
                "hardfibers",
                "hardfiber_coordinates",
                "hardfiber_cores",
                "installation_remarks_logs",
                "job_orders",
                "modems",
                "nodes",
                "olts",
                "packages",
                "package_types",
                "payment_methods",
                "pons",
                "roles",
                "role_user",
                "sales",
                "schedules",
                "splitters",
                "splitter_ports",
                "splitter_types",
                "teams",
                "tickets",
                "ticket_remarks_logs",
                "ticket_statuses",
                "users",
                "whts",
            );

            $output = "";
            foreach ($tables as $table) {
                $select_query = DB::table($table)->get();

                foreach ($select_query as $item) {
                    $table_column_array = array_keys((array)$item);
                    $table_value_array = array_values((array)$item);
                    $output .= "\nINSERT INTO $table (";
                    $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                    $output .= "'" . implode("','", $table_value_array) . "');";
                }
            }

            return $output;
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function save_database()
    {
        $filenameDate = date("Y-m-d");
        $myfile = fopen("backupDB/infosystem" . $filenameDate . ".sql", "a") or die("Unable to open file!");
        $txt = $this->download_database();
        fwrite($myfile, $txt);
        fclose($myfile);
        return "ok";
    }
    function cURLcheckBasicFunctions()
    {
        if (
            !function_exists("curl_init") &&
            !function_exists("curl_setopt") &&
            !function_exists("curl_exec") &&
            !function_exists("curl_close")
        ) return false;
        else return true;
    }

    public function testfunction()
    {
            // $ch = curl_init('http://202.137.121.76/cgi-bin/index2.asp')face
        ;
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // $ret = curl_exec($ch); //get error
        // curl_close($ch);
        // return $ret;



        // $sendTo = [];
        // $temp = new stdClass;
        // $temp->email = "pbismonte@dctechmicro.com";
        // $temp->name = "Peter Pogi";
        // array_push($sendTo, $temp);

        // $CCTO = [];
        // \Logger::instance()->mailerZimbra(
        //     "test subject",
        //     "body",
        //     "",
        //     "Infosystem",
        //     $sendTo,
        //     $CCTO
        // );
        //return "0";

        $csv = array_map('str_getcsv', file('qwe.csv'));
        array_walk($csv, function (&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header

        $converted = $this->convert_from_latin1_to_utf8_recursively($csv);

        //check in subscription_name

        $oneIsToOne = [];
        $oneIsToZero = [];
        $oneIsToMany = [];

        foreach ($converted as $i) {
            $i = (object) $i;
            $info_client = DB::table('clients')
                ->join("packages", "packages.id", "clients.package_id")
                ->where("subscription_name", $i->subscription_name)
                ->select("clients.*", "packages.name as pack_name", "packages.mrr as mrr")
                ->get();


            //1 is to 1
            if ($info_client->count()  == 1) {
                $i->info_client = $info_client->first();
                array_push($oneIsToOne, $i);
            }

            //1 is to zero
            if ($info_client->count()  == 0) {
                array_push($oneIsToZero, $i);
            }

            //1 is to many
            if ($info_client->count()  > 1) {
                //$i->info_client = $info_client;
                array_push($oneIsToMany, $i);
            }
        }
        $ret_val = collect();
        $ret_val->put("oneIsToOne", $oneIsToOne);
        $ret_val->put("oneIsToZero", $oneIsToZero);
        $ret_val->put("oneIsToMany", $oneIsToMany);
        return $ret_val;
    }
    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
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
