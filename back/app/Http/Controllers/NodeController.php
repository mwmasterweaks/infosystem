<?php

namespace App\Http\Controllers;

use App\node;
use App\olt;
use App\closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use stdClass;

class NodeController extends Controller
{
    private $cname = "NodeController";

    public function index()
    {
        $tbl = node::with(['olts.splitter_lcp.splitter_nap.splitter_port.client'])->get();
        $retval = [];
        $sub_id = 0;
        foreach ($tbl as $item) {

            $item->lat = (float) $item->lat;
            $item->lng = (float) $item->lng;
            $item->draggable = false;
            $item->save = true;
            $item->marker_type = "node";
            $item->children = $item->olts;
            $item->sub_id = $sub_id;
            $sub_id++;
            unset($item->olts);
            $ttt = $item->children;


            $copy = clone $item;
            unset($copy->children);
            $copy->file = "node";
            $copy->name = $copy->name . " (Marker)";
            $temp = array();
            $copy->sub_id = $sub_id;
            $sub_id++;
            array_push($temp, $copy);

            foreach ($ttt as $olt) {

                array_push($temp, $olt);
                $olt->children = $olt->splitter_lcp;
                unset($olt->splitter_lcp);
                $olt->sub_id = $sub_id;
                $sub_id++;
                $ttt = $olt->children;



                foreach ($ttt as $splitter_lcp) {

                    $closure = closure::with(["closure_type"])
                        ->where("id", $splitter_lcp->attach_id)->first();

                    $splitter_lcp->lat = (float) $closure->lat;
                    $splitter_lcp->lng = (float) $closure->lng;
                    $splitter_lcp->name = $closure->name;
                    $closure_type_temp = (object) $closure->closure_type;
                    $splitter_lcp->type = $closure_type_temp->name;
                    $splitter_lcp->closure_id = $closure->id;
                    $splitter_lcp->icon = "lcp";

                    $splitter_lcp->children = $splitter_lcp->splitter_nap;
                    $splitter_lcp->sub_id = $sub_id;
                    $splitter_lcp->nap_count = count($splitter_lcp->splitter_nap);
                    $sub_id++;
                    unset($splitter_lcp->splitter_nap);
                    $ttt = $splitter_lcp->children;


                    $copy = clone $splitter_lcp;
                    unset($copy->children);
                    $copy->file = "closure";
                    $copy->name = $closure->name;
                    $copy->sub_id = $sub_id;
                    $sub_id++;
                    $temp1 = array();
                    array_push($temp1, $copy);

                    foreach ($ttt as $splitter_nap) {
                        array_push($temp1, $splitter_nap);
                        $closure = closure::with(["closure_type"])
                            ->where("id", $splitter_nap->attach_id)->first();

                        $splitter_nap->lat = (float) $closure->lat;
                        $splitter_nap->lng = (float) $closure->lng;
                        $splitter_nap->name = $closure->name;
                        $closure_type_temp = (object) $closure->closure_type;
                        $splitter_nap->type = $closure_type_temp->name;
                        $splitter_nap->closure_id = $closure->id;
                        $splitter_nap->occupied_port = count($splitter_nap->splitter_port);
                        if ($splitter_nap->port_type == "1x16") {
                            $splitter_nap->total_port = 16;
                            $splitter_nap->vacant_port = 16 -  $splitter_nap->occupied_port;
                        } elseif ($splitter_nap->port_type == "1x8") {
                            $splitter_nap->total_port = 8;
                            $splitter_nap->vacant_port = 8 -  $splitter_nap->occupied_port;
                        } elseif ($splitter_nap->port_type == "1x4") {
                            $splitter_nap->total_port = 4;
                            $splitter_nap->vacant_port = 4 -  $splitter_nap->occupied_port;
                        }

                        // $splitter_nap->closure_id = $closure->id;
                        $splitter_nap->icon = "nap" . count($splitter_nap->splitter_port);

                        $splitter_nap->children = $splitter_nap->splitter_port;
                        $splitter_nap->sub_id = $sub_id;
                        $sub_id++;
                        unset($splitter_nap->splitter_port);
                        $ttt = $splitter_nap->children;

                        $copy = clone $splitter_nap;
                        unset($copy->children);
                        $copy->file = "closure";

                        $copy->name = $closure->name;
                        $copy->sub_id = $sub_id;
                        $sub_id++;
                        $temp2 = array();
                        array_push($temp2, $copy);

                        //  foreach ($ttt as $splitter_port) {
                        //      array_push($temp2, $splitter_port);
                        //      $client = (object) $splitter_port->client;
                        //      $splitter_port->name = $client->name . " (port " . $splitter_port->port_number . ")";
                        //      $splitter_port->lat = (float) $client->lat;
                        //      $splitter_port->lng = (float) $client->lng;
                        //     $splitter_port->sub_id = $sub_id;
                        //     $sub_id++;
                        //     $splitter_port->file = "user";
                        //  }
                        $splitter_nap->children = $temp2;
                    }
                    $splitter_lcp->children = $temp1;
                }
            }
            $item->children = $temp;
            array_push($retval, $item);
        }
        $closures = closure::whereNotIn('id', function ($query) {
            $query->select('attach_id')
                ->from('splitters')
                ->where('attach_to', 'closure');
        })->get();
        //add sub_id
        $c = collect();
        $c->put('name', 'DP w/o Splitter');
        $c->put('sub_id', $sub_id);
        $sub_id++;
        foreach ($closures as $item) {
            $item->file = "closure";
            $item->closure_id = $item->id;
            $item->sub_id =  $sub_id;
            $item->lat = (float) $item->lat;
            $item->lng = (float) $item->lng;
            $item->icon = "lcp";
            //$splitter_lcp->nap_count = 0;
            $sub_id++;
        }
        $c->put('children', $closures);

        array_push($retval, $c);
        return response()->json($retval);
    }

    public function getNode($id)
    {
        $tbl = node::where("id", $id)
            ->first();

        $position = new stdClass;
        $position->lat = (float) $tbl->lat;
        $position->lng = (float) $tbl->lng;
        $tbl->position = $position;
        $tbl->draggable = false;
        $tbl->save = true;
        $tbl->marker_type = "node";

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $data = node::create($request->all());
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new node: " . $data
            );
            return $this->getNode($data->id);
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
        $tbl = node::with(['olts.splitter_lcp'])
            ->where('id', $id)
            ->first();

        foreach ($tbl->olts as $olt) {
            foreach ($olt->splitter_lcp as $splitter_lcp) {
                $closure = closure::with(["closure_type"])
                    ->where("id", $splitter_lcp->attach_id)->first();

                $splitter_lcp->lat = (float) $closure->lat;
                $splitter_lcp->lng = (float) $closure->lng;
                $splitter_lcp->name = $closure->name;
                $closure_type_temp = (object) $closure->closure_type;
                $splitter_lcp->type = $closure_type_temp->name;
            }
        }
        $tbl->save = true;
        return $tbl;
    }
    public function edit(node $node)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {

            $cmd  = node::findOrFail($id);
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
                "update node id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
        try {
            $tbl = olt::where("node_id", $id)->count();
            if ($tbl > 0) {
                return response()->json(['error' => 'You cannot delete this node it has olt inside'], 500);
            } else {
                $tbl1 = node::findOrFail($id);
                node::destroy($id);

                \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete node id " . $id .
                        "\nOld node: " . $tbl1
                );
                return $this->index();
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
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
