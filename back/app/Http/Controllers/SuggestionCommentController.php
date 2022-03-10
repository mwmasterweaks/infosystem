<?php

namespace App\Http\Controllers;

use App\suggestion_comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuggestionCommentController extends Controller
{
    public function index()
    {
        $tbl = suggestion_comment::all();

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            suggestion_comment::create($request->all());
            return $this->getComments($request->suggestion_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function show(suggestion_comment $suggestion_comment)
    {
        //
    }

    public function edit(suggestion_comment $suggestion_comment)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = suggestion_comment::findOrFail($id);

            $input = $request->all();

            $cmd->fill($input)->save();

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            suggestion_comment::destroy($id);

            return $this->index();
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was a problem deleting your comment.'], 500);
        }
    }

    public function getComments($s_id)
    {
        $tbl = suggestion_comment::with(["user"])->where('suggestion_id', $s_id)->orderBy('updated_at', 'DESC')->get();

        return response()->json($tbl);
    }
}
