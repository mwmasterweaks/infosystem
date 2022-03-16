<?php

namespace App\Http\Controllers;

use App\Client;
use App\Ticket;
use App\rebate;
use App\billing;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RebateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();


            foreach ($request->clients as $client) {


                $data = (object)$client;
                $client_id = $data->id;


                // Ticket::where("client_id", $client_id)->update(["to_soa" => "1"]);

                // if no mrr
                if (empty($data->package_mrr)) {

                    $tbl = Client::with('package')->where("id", $client_id)->get();
                    foreach ($tbl as $mrr) {
                        $mrr = (object)$mrr->package;
                        $package_mrr = $mrr->mrr;
                    }
                } else
                    $package_mrr = $data->package_mrr;

                // calculation of rebates
                $rate = 0;
                $totalHr = $request->downtime;
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
                $amount = round(($package_mrr / 30) * $rate);


                $remarks =
                    "Rebates for ticket ID: " . $request->ticket_no . ". No. of Hour/s: " . $request->downtime;
                $temp = [];

                $billings = billing::where("client_id", $client_id)
                    ->where("balance", ">", "0")
                    ->orderBy("date")
                    ->get();

                $dup_amount = $amount;
                foreach ($billings as $billing) {

                    $temp = $dup_amount - $billing->balance;
                    if ($temp > 0) {
                        DB::table("billings")
                            ->where("id", $billing->id)
                            ->update(["balance" => "0"]);
                        $remarks .= $billing->date . ", ";
                        $dup_amount = $temp;
                    } else {
                        $temp = $billing->balance - $dup_amount;
                        DB::table("billings")
                            ->where("id", $billing->id)
                            ->update(["balance" => $temp]);
                        $remarks .= $billing->date;
                        $dup_amount = 0;
                        break;
                    }
                }
                $rebates = new rebate;
                $rebates->client_id = $client_id;
                $rebates->user_id = $request->user_id;
                $rebates->amount = $amount;
                $rebates->date = $request->date_effective;
                $rebates->description = $remarks;
                $rebates->tbl_name = "tbl_rebates";
                $rebates->save();
            }
            DB::commit();
            return "saved";
        } catch (\Exception $ex) {
            DB::rollBack();
            $this
                ->log(
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

    public function report(Request $request)
    {
        // return $request;

        $yearMonth = $request->date;
        $area_id = $request->area_id;

        if ($area_id != 0) {
            $tbl = rebate::with(["client"])
                ->groupBy('client_id')
                ->selectRaw('*, sum(amount) as amount')
                ->whereHas("client", function ($query) use ($area_id) {
                    $query->where("area_id", $area_id);
                })
                ->where('date', 'like', '%' . $yearMonth . '%')
                ->get();
        } else
            $tbl = rebate::with(["client"])
                ->groupBy('client_id')
                ->selectRaw('*, sum(amount) as amount')
                ->where('date', 'like', '%' . $yearMonth . '%')
                ->get();



        return $tbl;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\rebate  $rebate
     * @return \Illuminate\Http\Response
     */
    public function show(rebate $rebate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\rebate  $rebate
     * @return \Illuminate\Http\Response
     */
    public function edit(rebate $rebate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\rebate  $rebate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, rebate $rebate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rebate  $rebate
     * @return \Illuminate\Http\Response
     */
    public function destroy(rebate $rebate)
    {
        //
    }
}
