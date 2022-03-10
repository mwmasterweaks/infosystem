<?php

namespace App\Http\Controllers;

use App\suggestion;
use App\suggestion_comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tbl = suggestion::with(['user'])->get();

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
            suggestion::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function show(suggestion $suggestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function edit(suggestion $suggestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $cmd  = suggestion::findOrFail($id);

            $input = $request->all();

            $cmd->fill($input)->save();

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\suggestion  $suggestion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            suggestion::destroy($id);

            DB::table('suggestion_comments')->where('suggestion_id', $id)->delete();
            return $this->index();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->message], 500);
        }
    }
}
