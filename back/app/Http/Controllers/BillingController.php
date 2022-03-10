<?php

namespace App\Http\Controllers;

use App\billing;
use App\customer_payment;
use App\wht;
use App\Ticket;
use App\Client;
use App\bill_state_list;
use App\bill_statement;
use App\rebate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BillingController extends Controller
{
    private $cname = "BillingController";

    public function index()
    {
        $tbl = billing::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $data = billing::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new billing: " . $data
            );
            return $this->show($request->client_id);
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
        $tbl = billing::where("client_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(billing $billing)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = billing::findOrFail($id);
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
                "update billing id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->show($request->olt_id);
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
            billing::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy1($id, $olt_id)
    {
        try {
            $tbl1 = billing::findOrFail($id);
            billing::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete billing id " . $id .
                    "\nOld billing: " . $tbl1
            );
            return $this->show($olt_id);
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

    public function soa($client_id, $billrange)
    {
        $retval = [];
        $temp = [];
        $rebates = [];
        $bill = billing::where("client_id", $client_id)->get()->toArray();
        $payment = customer_payment::where("client_id", $client_id)->get()->toArray();
        $wht = wht::where("client_id", $client_id)->get()->toArray();
        $rebates_new = rebate::where("client_id", $client_id)->get()->toArray();

        //ticket calculate rebates //test account ALISA ERE
        if (true) {
            $ticket = Ticket::with(["client.package"])->where("client_id", $client_id)
                ->whereNotNull("downtime_hours")
                ->where("to_soa", "1")
                ->get();


            //ticket rebates
            foreach ($ticket as $item) {

                $client = (object) $item->client;
                $package = (object) $client->package;
                $rate = 0;
                $totalHr = $item->downtime_hours;
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
                if ($totalHr >= 0.5) {
                    $rate += 0.1;
                    $totalHr = 0;
                }
                $item->amount = round(($package->mrr / 30) * $rate);
                $ticketDate = new Carbon($item->date_time_fixed);
                $item->date = $ticketDate->toDateString();
                $item->description = "Rebates for ticket ID: " . $item->id . ". No. of Hour/s: " . $item->downtime_hours;
                $item->tbl_name = "ticket";
                array_push($rebates, $item);
            }
        }
        $temp = array_merge($bill, $payment);
        $temp = array_merge($temp, $rebates);
        $temp = array_merge($temp, $wht);
        $temp = array_merge($temp, $rebates_new);
        usort(
            $temp,
            function ($object1, $object2) {
                $object1 = (object) $object1;
                $object2 = (object) $object2;
                return $object1->date > $object2->date;
            }
        );

        $bal = 0;
        foreach ($temp as $item) {
            $item = (object) $item;
            if (!isset($item->description)) {
                //customer_payment
                $item->description = $item->remarks;
            }
            if ($item->tbl_name == "bill") {
                $item->check = false;
                $item->balanceFormated = number_format($item->balance, 2);
            }

            if (isset($item->price)) {
                $bal = $bal + $item->price;
                $item->priceFormated = number_format($item->price, 2);
            } else {
                $bal = $bal - $item->amount;
                $item->amountFormated = number_format($item->amount, 2);
            }

            $item->balanceSum = number_format($bal, 2);

            $now = Carbon::now();
            $itemDate = new Carbon($item->date);
            $endOfMonth = $now->copy();
            $endOfMonth->day = $endOfMonth->daysInMonth;
            $nextMonth = $now->copy()->addMonth();
            $endOfNextMonth = $nextMonth->copy();
            $endOfNextMonth->day = $nextMonth->daysInMonth;


            if ($billrange == "month")
                if ($itemDate->greaterThanOrEqualTo($endOfMonth))
                    break;
            if ($billrange == "nextmonth")
                if ($itemDate->greaterThanOrEqualTo($endOfNextMonth))
                    break;
            array_push($retval, $item);
        }

        return response()->json($retval);
    }

    public function agingReport(Request $request)
    {
        $pack_id = $request->package_type_id;
        $area_id = $request->area_id;
        $date_from = new Carbon($request->date_from);
        $date_to = new Carbon($request->date_to);
        $date_from->day = 1;
        $date_to->day =  $date_to->daysInMonth;
        $fields = [];


        $tbl = billing::with(["client"])
            ->groupBy('client_id')
            ->selectRaw('*, sum(balance) as sum, MONTHNAME(date) as month_name ')
            ->whereHas("client", function ($query) use ($pack_id, $area_id) {
                $query->where("package_type_id", $pack_id);
                $query->where("area_id", $area_id);
            })
            ->whereBetween("date", [$date_from, $date_to])
            ->groupBy('month_name')
            ->orderBy('date', 'asc')
            ->get();

        $collection = collect($tbl);
        $fields = $collection->groupBy('client_id');

        $months = array();
        foreach (CarbonPeriod::create($date_from, '1 month', $date_to) as $month) {
            //$months[$month->format('F')] = ;
            array_push($months, $month->format('F'));
        }
        $c = collect();
        $c->put('months', $months);
        $c->put('items',  $fields);

        return $c;
    }

    public function to_pay($client_id, $billrange)
    {
        $retval = [];
        $temp = [];

        $bill = billing::where("client_id", $client_id)
            ->where("balance", "!=", "0")
            ->get()->toArray();

        $temp = array_merge($temp, $bill);
        usort(
            $temp,
            function ($object1, $object2) {
                $object1 = (object) $object1;
                $object2 = (object) $object2;
                return $object1->date > $object2->date;
            }
        );

        $bal = 0;
        foreach ($temp as $item) {
            $item = (object) $item;
            $bal = $bal + $item->balance;

            $now = Carbon::now();
            $itemDate = new Carbon($item->date);
            $endOfMonth = $now->copy();
            $endOfMonth->day = $endOfMonth->daysInMonth;
            $nextMonth = $now->copy()->addMonth();
            $endOfNextMonth = $nextMonth->copy();
            $endOfNextMonth->day = $nextMonth->daysInMonth;

            $item->isSelected = false;

            if ($billrange == "month")
                if ($itemDate->greaterThanOrEqualTo($endOfMonth))
                    break;
            if ($billrange == "nextmonth")
                if ($itemDate->greaterThanOrEqualTo($endOfNextMonth))
                    break;
            array_push($retval, $item);
        }

        return response()->json($retval);
    }

    public function updateSOA(Request $request)
    {
        try {
            //return $request;
            $id = $request->id;
            $cmd = "";
            if ($request->tbl_name == "bill") {
                $cmd  = billing::findOrFail($id);
                $logFrom = $cmd->replicate();
                $input = $request->all();

                $cmd->fill($input)->save();
            }

            if ($request->tbl_name == "customer_payment") {
                $cmd  = customer_payment::findOrFail($id);
                $logFrom = $cmd->replicate();
                $input = $request->all();

                $cmd->fill($input)->save();
            }

            if ($request->tbl_name == "wht") {
                $cmd  = wht::findOrFail($id);
                $logFrom = $cmd->replicate();
                $input = $request->all();

                $cmd->fill($input)->save();
            }

            if ($request->tbl_name == "ticket") {

                $logFrom = "down time";

                $cmd = DB::table('tickets')
                    ->where("id", $id)
                    ->update(["downtime_hours" => $request->downtime_hours]);
            }

            if ($request->tbl_name == "tbl_rebates") {
                $cmd  = rebate::findOrFail($id);
                $logFrom = $cmd->replicate();
                $input = $request->all();

                $cmd->fill($input)->save();
            }

            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateSOA",
                "message",
                "update soa id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return "OK";
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateSOA",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function deleteSOA(Request $request)
    {
        try {
            // return $request;
            $id = $request->id;
            if ($request->tbl_name == "bill") {
                $tbl1 = billing::findOrFail($id);
                billing::destroy($id);
            }

            if ($request->tbl_name == "customer_payment") {
                $tbl1 = customer_payment::findOrFail($id);
                customer_payment::destroy($id);
            }

            if ($request->tbl_name == "wht") {
                $tbl1 = wht::findOrFail($id);
                wht::destroy($id);
            }
            if ($request->tbl_name == "tbl_rebates") {
                $tbl1 = rebate::findOrFail($id);
                rebate::destroy($id);
            }

            if ($request->tbl_name == "ticket") {

                $tbl1 = DB::table('tickets')
                    ->where("id", $id)
                    ->update(["to_soa" => false]);
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "deleteSOA",
                "message",
                "delete deleteSOA id " . $id .
                    "\nOld deleteSOA: " . $tbl1
            );
            return $this->show($request->olt_id);
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "deleteSOA",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function deleteMultiSOA(Request $request)
    {
        try {
            $input = $request->all();

            foreach ($input as $item) {

                $item = (object)$item;
                $id = $item->id;
                $tbl_name = $item->tbl_name;

                if ($tbl_name == "bill") {
                    $tbl1 = billing::findOrFail($id);
                    billing::destroy($id);
                }

                if ($tbl_name == "customer_payment") {
                    $tbl1 = customer_payment::findOrFail($id);
                    customer_payment::destroy($id);
                }

                if ($tbl_name == "wht") {
                    $tbl1 = wht::findOrFail($id);
                    wht::destroy($id);
                }

                if ($tbl_name == "ticket") {

                    $tbl1 = DB::table('tickets')
                        ->where("id", $id)
                        ->update(["to_soa" => false]);
                }

                if ($tbl_name == "tbl_rebates") {
                    $tbl1 = rebate::findOrFail($id);
                    rebate::destroy($id);
                }

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "deleteMultiSOA",
                    "message",
                    "delete deleteSOA id " . $id .
                        "\nOld deleteSOA: " . $tbl1
                );
            }


            return $this->show($request->olt_id);
            // return "ok";
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "deleteMultiSOA",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function deleteBillState(Request $request)
    {


        try {

            // return $request;

            $state = (object)$request->data;
            foreach ($state as $item) {
                $item = (object)$item;



                bill_state_list::where('bill_statement_id', $request->id)->delete();

                $tbl1 = bill_statement::findOrFail($request->id);
                bill_statement::destroy($request->id);

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "deleteBillState",
                    "message",
                    "delete deleteSOA id " . $request->id .
                        "\nOld deleteSOA: " . $tbl1
                );
            }

            return "ok";
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "deleteBillState",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function emailSOA(Request $request)
    {
        try {
            $msg =
                "<div>" .
                $request->msg . "

                </div>
                <style>
                table {
                border-collapse: collapse;
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

            if ($request->emailSender == "default") {
                return \Logger::instance()->mailer(
                    $request->subject,
                    $msg,
                    $request->user_email,
                    $request->user_name,
                    $request->sendTo,
                    $request->CCTO
                );
            } else {
                return \Logger::instance()->mailerUser(
                    $request->subject,
                    $msg,
                    $request->emailSenderDataE,
                    $request->emailSenderDataP,
                    $request->sendTo,
                    $request->CCTO
                );
            }

            return "ok";
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "emailSoa",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function storeBillStatement(Request $request)
    {
        try {
            DB::beginTransaction();
            $client = (object) $request->client;


            $id = DB::table('bill_statements')->insertGetId([
                'client_id' => $client->id,
                'date' => date("Y-m-d"),
                'due_date' => $request->due_date,
                'amount_due' => $request->amount_due
            ]);

            $retval = collect();
            $billList = [];
            $retval->put("saved", true);
            $retval->put("id", $id);


            foreach ($request->data as $item) {
                $item = (object) $item;
                if (isset($item->priceFormated))
                    $amount = $item->priceFormated;
                else
                    $amount = $item->sub_amount;
                if (isset($item->balanceFormated))
                    $balance = $item->balanceFormated;
                else
                    $balance = " ";

                $temp = [
                    "bill_statement_id" => $id,
                    'date' => $item->date,
                    'description' => $item->description,
                    'priceFormated' => $amount,
                    'balanceFormated' => $balance
                ];

                array_push($billList, $temp);
            }

            bill_state_list::insert($billList);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "storeBillStatement",
                "message",
                "Create new bill statement id: " . $id
            );
            DB::commit();
            return $retval;
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "storeBillStatement",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function generateBill(Request $request)
    {
        $client = (object) $request->client;

        $datebill = null;
        $dateCoverFrom = null;
        $dateCoverTo = null;
        $contain = [];

        if (true) {

            //generate whole bill
            //$datebill = Carbon::create($request->year, $request->month, $request->day);
            $datebill = new Carbon($request->date);
            //$dateCoverFrom = $datebill->copy()->addDay();
            //$dateCoverTo = $datebill->copy()->addMonthsNoOverflow();
            // if ($request->pack_type == "SME") {
            $dateCoverFrom = new Carbon($request->cover_date);
            $dateCoverTo = $dateCoverFrom->copy()->addMonthsNoOverflow();
            $dateCoverTo =  $dateCoverTo->subDay();
            // }
            for ($x = 1; $x <= $request->count; $x++) { //if condition change - change here too - refCashbondPayment

                $datebill_copy = $datebill->copy()->addMonthsNoOverflow($x - 1);
                $dateCoverFrom_copy = $dateCoverFrom->copy()->addMonthsNoOverflow($x - 1);
                $dateCoverTo_copy = $dateCoverTo->copy()->addMonthsNoOverflow($x - 1);

                $data = [
                    "client_id" => $client->id,
                    "date" => $datebill_copy->toDateString(),
                    "item" => "MRR - Internet (" . $client->package_type_name . ")",
                    "description" => "MRR - " . $client->package_type_name . " " .
                        $dateCoverFrom_copy->toFormattedDateString() . " - " .
                        $dateCoverTo_copy->toFormattedDateString(),
                    "price" => $client->package_mrr,
                    "balance" => $client->package_mrr
                ];
                array_push($contain, $data);
            }
        }
        return response()->json($contain);
    }

    public function insertGeneratedBill(Request $request)
    {
        $contain = [];
        foreach ($request->bill as $item) {
            $item = (object) $item;
            $data = [
                "client_id" => $item->client_id,
                "date" => $item->date,
                "item" => $item->item,
                "description" => $item->description,
                "price" => $item->price,
                "balance" => $item->balance,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
            array_push($contain, $data);
        }

        billing::insert($contain);
        return 0;
    }
    public function getTransmittal(Request $request)
    {
        //$region_id = $request->region_id;


        $branch_ids = [];
        foreach ($request->branches as $item) {
            $temp = (object)$item;
            array_push($branch_ids, $temp->id);
        }

        $day = $request->day;
        if (strlen($day) == 1)
            $day = "0" . $day;
        $tbl = billing::with(['client'])
            ->where("date", "like", "%-" . $day)
            ->whereHas("client", function ($query) use ($branch_ids) {
                $query->whereIn("branch_id", $branch_ids);
            })
            ->groupBy('client_id')
            ->get();
        foreach ($tbl as $item) {
            $tbl1 = billing::where("client_id", $item->client_id)
                ->groupBy('client_id')
                ->selectRaw('*, sum(balance) as sum')
                ->first();
            $item->sum = $tbl1->sum;
        }
        return response()->json($tbl);
    }

    public function getBillStateList(Request $request)
    {
        $tbl = bill_statement::with(["data", "client"])
            ->where("client_id", $request->id)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($tbl);
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

    public function getBillToExport(Request $request)
    {
        $temp = $request;
        $dtnow = Carbon::now();
        $endOfMonth = $dtnow->endOfMonth();
        $tbl = billing::with(['client'])
            //->whereBetween("client_id", [0, 1000])
            ->groupBy('client_id')
            ->selectRaw('*, sum(balance) as sum_bal')
            ->whereHas("client", function ($query) {
                $query->whereNotNull("id");
                $query->where("status");
            })
            ->where("date", "<=", $endOfMonth)
            ->havingRaw("sum(balance) > 0")
            ->get();
        //$collection = collect($tbl);
        //$fields = $collection->groupBy('client_id');
        return response()->json($tbl);
    }
}
