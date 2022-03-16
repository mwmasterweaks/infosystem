<?php

namespace App\Http\Controllers;

use App\complaint_list;
use App\Ticket_status;
use App\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComplaintListController extends Controller
{
    private $cname = "ComplaintListController";

    public function index()
    {
        $tbl = complaint_list::all();
        return response()->json($tbl);
    }

    public function store(Request $request)
    {
        try {
            $data = complaint_list::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new complaint: " . $data
            );
            return $this->index();
        } catch (\Exception $ex) {
            // $this
            //     ->log(
            //         Carbon::now(),
            //         $request->user_id,
            //         $request->user_name,
            //         $this->cname,
            //         "store",
            //         "Error",
            //         $ex->getMessage()
            //     );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $tbl  = complaint_list::findOrFail($id);
            $logFrom = $tbl->replicate();
            $input = $request->all();

            $tbl->fill($input)->save();
            $logTo = $tbl;
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Ticket_status id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
        $tbl = Ticket::where('complaint_new', $id)->first();

        if (empty($tbl)) {
            $tbl1 = complaint_list::findOrFail($id);

            complaint_list::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete complaint_new id " . $id .
                    "\nOld complaint_new: " . $tbl1
            );

            return $this->index();
        } else {

            return response()->json(['error' => 'Cant delete this complaint, It\'s still in use.'], 500);
        }
    }
}
