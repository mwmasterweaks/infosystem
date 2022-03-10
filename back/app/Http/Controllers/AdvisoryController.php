<?php

namespace App\Http\Controllers;

use App\advisory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvisoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $cname = "AdvisoryController";

    public function index()
    {
        $tbl = advisory::all();
        return response()->json($tbl);
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
            $data = advisory::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new advisory template: " . $data
            );
            return $this->index();
        } catch (\Exception $ex) {


            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\advisory  $advisory
     * @return \Illuminate\Http\Response
     */
    public function show(advisory $advisory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\advisory  $advisory
     * @return \Illuminate\Http\Response
     */
    public function edit(advisory $advisory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\advisory  $advisory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {

            $tbl  = advisory::findOrFail($id);
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
                "update template " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\advisory  $advisory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $tbl1 = advisory::findOrFail($id);

        advisory::destroy($id);

        \Logger::instance()->log(
            Carbon::now(),
            "",
            "",
            $this->cname,
            "destroy",
            "message",
            "delete template " . $id .
                "\nOld template: " . $tbl1
        );

        return $this->index();
    }


    public function getTemplate(Request $request)
    {
        $temp = $request->template;
        $ret = DB::table('advisories')
            ->select('content')->where('id', $temp)->value('content');


        // $return = collect();
        // $return->put('data', $ret);
        return response()->json($ret);
    }
}
