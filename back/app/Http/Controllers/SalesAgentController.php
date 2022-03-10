<?php

namespace App\Http\Controllers;

use App\sales_agent;
use App\Client;
use App\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesAgentController extends Controller
{
    private $cname = "SalesAgentController";
    public function index()
    {
        $tbl = sales_agent::with(['sales.user'])->get();

        $retval = [];
        foreach ($tbl as $item) {
            $sales = (object) $item->sales;
            $user = (object) $sales->user;
            $item->sales_name = $user->name;
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

            $data = sales_agent::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_log,
                $request->user_name_log,
                $this->cname,
                "store",
                "message",
                "Create new sales_agent: " . $data
            );
            return $this->index();
        } catch (\Exception $ex) {
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

    public function show(sales_agent $sales_agent)
    {
        //
    }

    public function edit(sales_agent $sales_agent)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = sales_agent::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();
            $logTo = $cmd;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_update,
                $request->user_name_update,
                $this->cname,
                "update",
                "message",
                "update sales_agent id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id_update,
                $request->user_name_update,
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
            $tbl1 = sales_agent::findOrFail($id);
            sales_agent::destroy($id);
            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete sales_agent id " . $id .
                    "\nOld sales_agent: " . $tbl1
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
            return response()->json(['error' => 'There was a problem deleting the sales.'], 500);
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


    public function agent_report(Request $request)
    {
        $agent_id = $request->agent;
        $date = $request->date;
        $hit_rate = 0;
        $total_pay = 0;
        $firstPay = 0;
        $secondPay = 0;
        $thirdPay = 0;
        $secondDec = 0;
        $thirdDec = 0;
        $firstNov = 0;
        $secondNov = 0;
        $formatted = new Carbon($date);
        $month = $formatted->format('m');
        // $string = $formatted->format('F');

        $clientsFirst = Client::with(['clientDetail', 'sales_agent', 'package'])
            ->where("sales_agent_id", $agent_id)
            ->whereHas("clientDetail", function ($query) {
                $query->where("otc", "=", "Paid");
            })
            ->whereHas('clientDetail', function ($query) use ($date) {
                $query->where('aging', 'like', '%' . $date . '%');
            })
            ->get();

        $total_clients = $clientsFirst->count();

        $quota = sales_agent::where("status", 'active')
            ->where('id', $agent_id)
            ->sum('quota');
        $activated_mrr = $clientsFirst->sum("package.mrr");

        if ($activated_mrr >= $quota) {
            $bonus = $activated_mrr * 0.05;
        } else {
            $bonus = "0";
        }
        if ($quota > 0) {
            $hit_rate = ($activated_mrr / $quota) * 100;
        }

        $firstPay = $activated_mrr * 0.20;

        // 2nd payment

        $second =  $formatted->subMonth()->format('Y-m');
        $clientsSecond = Client::with(['clientDetail', 'sales_agent', 'package'])
            ->where("sales_agent_id", $agent_id)
            ->whereHas("clientDetail", function ($query) {
                $query->where("otc", "=", "Paid");
            })->whereHas('clientDetail', function ($query) use ($second) {
                $query->where('aging', 'like', '%' . $second . '%');
            })
            ->get();

        $second_activated_mrr = $clientsSecond->sum("package.mrr");
        $secondPay = $second_activated_mrr * 0.15;


        // 3rd payment

        $third =  $formatted->subMonth(1)->format('Y-m');
        $clientsThird = Client::with(['clientDetail', 'sales_agent', 'package'])
            ->where("sales_agent_id", $agent_id)
            ->whereHas("clientDetail", function ($query) {
                $query->where("otc", "=", "Paid");
            })->whereHas('clientDetail', function ($query) use ($third) {
                $query->where('aging', 'like', '%' . $third . '%');
            })
            ->get();

        $third_activated_mrr = $clientsThird->sum("package.mrr");
        $thirdPay = $third_activated_mrr * 0.10;


        // december additional calculation
        $secondDec = $activated_mrr * 0.15;
        $thirdDec = $activated_mrr * 0.10;
        $firstNov = $second_activated_mrr * 0.15;
        $secondNov = $second_activated_mrr * 0.10;

        if ($month >= '03' && $month != '12') {
            $total_pay = $firstPay + $secondPay + $thirdPay +  $bonus;
            $c = collect();
            $c->put("first", $firstPay);
            $c->put("second", $secondPay);
            $c->put("third", $thirdPay);
            $c->put("total", $total_pay);
        } else if ($month == '01') {
            $total_pay = $firstPay + $bonus;
            $c = collect();
            $c->put("first", $firstPay);
            $c->put("total", $total_pay);
        } else if ($month == '02') {
            $total_pay = $firstPay + $secondPay +  $bonus;
            $c = collect();
            $c->put("first", $firstPay);
            $c->put("second", $secondPay);
            $c->put("total", $total_pay);
        } else if ($month == '12') {
            $total_pay = $firstPay + $secondDec + $thirdDec + $firstNov + $secondNov + $thirdPay +  $bonus;
            $c = collect();
            $c->put("first", $firstPay);
            $c->put("secondDec", $secondDec);
            $c->put("thirdDec", $thirdDec);
            $c->put("firstNov", $firstNov);
            $c->put("secondNov", $secondNov);
            $c->put("third", $thirdPay);
            $c->put("total", $total_pay);
        }


        $c = collect();
        $c->put("data", $clientsFirst);
        $c->put("total_clients", $total_clients);
        $c->put("quota", $quota);
        $c->put("total_mrr", $activated_mrr);
        $c->put("hit_rate", $hit_rate);
        $c->put("bonus", $bonus);
        $c->put("first", $firstPay);
        $c->put("second", $secondPay);
        $c->put("third", $thirdPay);
        $c->put("secondDec", $secondDec);
        $c->put("thirdDec", $thirdDec);
        $c->put("firstNov", $firstNov);
        $c->put("secondNov", $secondNov);
        $c->put("total", $total_pay);

        return response()->json($c);
    }
}
