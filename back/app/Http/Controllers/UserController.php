<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class UserController extends Controller
{
    private $cname = "UserController";
    public function index()
    {
        $tbl = User::with(['roles', 'branch'])->get();

        $users = [];

        foreach ($tbl as $user) {
            $c = collect();
            $c->put("sample", "true");
            foreach ($user->roles as $role) {
                $c->put($role->name, true);
            }
            $user->roleList = $c;
            array_push($users, $user);
        }

        return response()->json($users);
    }
    public function store(Request $request)
    {
        try {
            $user = User::where('email', '=', $request->email)->first();
            if (empty($user)) {
                DB::table('users')->insert([
                    [
                        'name' => $request->name,
                        'branch_id' => $request->branch_id,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'remember_token' => str_random(10),
                        'created_at' =>  \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now()
                    ]
                ]);
                $userID = DB::getPdo()->lastInsertId();;
                if ($request->withHDRole == true) {
                    DB::table('role_user')->insert([
                        ['user_id' => $userID, 'role_id' => 11],
                        ['user_id' => $userID, 'role_id' => 14],
                        ['user_id' => $userID, 'role_id' => 17],
                        ['user_id' => $userID, 'role_id' => 31],
                        ['user_id' => $userID, 'role_id' => 32],
                        ['user_id' => $userID, 'role_id' => 38],
                        ['user_id' => $userID, 'role_id' => 41]
                    ]);
                }
                if ($request->withSalesRole == true) {
                    DB::table('role_user')->insert([
                        ['user_id' => $userID, 'role_id' => 5],
                        ['user_id' => $userID, 'role_id' => 10],
                        ['user_id' => $userID, 'role_id' => 11],
                        ['user_id' => $userID, 'role_id' => 13],
                        ['user_id' => $userID, 'role_id' => 14],
                        ['user_id' => $userID, 'role_id' => 19],
                        ['user_id' => $userID, 'role_id' => 20],
                        ['user_id' => $userID, 'role_id' => 22],
                        ['user_id' => $userID, 'role_id' => 23],
                        ['user_id' => $userID, 'role_id' => 60],
                        ['user_id' => $userID, 'role_id' => 62],
                        ['user_id' => $userID, 'role_id' => 63]
                    ]);
                }
                if ($request->withAccountingRole == true) {
                    DB::table('role_user')->insert([
                        ['user_id' => $userID, 'role_id' => 5],
                        ['user_id' => $userID, 'role_id' => 52],
                    ]);
                }
                if ($request->withSchedulerRole == true) {
                    DB::table('role_user')->insert([
                        ['user_id' => $userID, 'role_id' => 7],
                        ['user_id' => $userID, 'role_id' => 8],
                        ['user_id' => $userID, 'role_id' => 16],
                        ['user_id' => $userID, 'role_id' => 17],
                        ['user_id' => $userID, 'role_id' => 19],
                        ['user_id' => $userID, 'role_id' => 20],
                        ['user_id' => $userID, 'role_id' => 22],
                        ['user_id' => $userID, 'role_id' => 23],
                        ['user_id' => $userID, 'role_id' => 28],
                        ['user_id' => $userID, 'role_id' => 29],
                        ['user_id' => $userID, 'role_id' => 47],
                        ['user_id' => $userID, 'role_id' => 48],
                        ['user_id' => $userID, 'role_id' => 50],
                        ['user_id' => $userID, 'role_id' => 51],
                        ['user_id' => $userID, 'role_id' => 54],
                        ['user_id' => $userID, 'role_id' => 59],
                    ]);
                }
                if ($request->withRMRole == true) {
                    DB::table('role_user')->insert([
                        ['user_id' => $userID, 'role_id' => 1],
                        ['user_id' => $userID, 'role_id' => 2],
                        ['user_id' => $userID, 'role_id' => 7],
                        ['user_id' => $userID, 'role_id' => 8],
                        ['user_id' => $userID, 'role_id' => 10],
                        ['user_id' => $userID, 'role_id' => 11],
                        ['user_id' => $userID, 'role_id' => 13],
                        ['user_id' => $userID, 'role_id' => 14],
                        ['user_id' => $userID, 'role_id' => 16],
                        ['user_id' => $userID, 'role_id' => 17],
                        ['user_id' => $userID, 'role_id' => 19],
                        ['user_id' => $userID, 'role_id' => 20],
                        ['user_id' => $userID, 'role_id' => 22],
                        ['user_id' => $userID, 'role_id' => 23],
                        ['user_id' => $userID, 'role_id' => 25],
                        ['user_id' => $userID, 'role_id' => 26],
                        ['user_id' => $userID, 'role_id' => 28],
                        ['user_id' => $userID, 'role_id' => 29],
                        ['user_id' => $userID, 'role_id' => 31],
                        ['user_id' => $userID, 'role_id' => 32],
                        ['user_id' => $userID, 'role_id' => 34],
                        ['user_id' => $userID, 'role_id' => 35],
                        ['user_id' => $userID, 'role_id' => 37],
                        ['user_id' => $userID, 'role_id' => 38],
                        ['user_id' => $userID, 'role_id' => 40],
                        ['user_id' => $userID, 'role_id' => 41],
                        ['user_id' => $userID, 'role_id' => 43],
                        ['user_id' => $userID, 'role_id' => 44],
                        ['user_id' => $userID, 'role_id' => 47],
                        ['user_id' => $userID, 'role_id' => 48],
                        ['user_id' => $userID, 'role_id' => 50],
                        ['user_id' => $userID, 'role_id' => 51],
                        ['user_id' => $userID, 'role_id' => 53],
                        ['user_id' => $userID, 'role_id' => 54],
                        ['user_id' => $userID, 'role_id' => 56],
                        ['user_id' => $userID, 'role_id' => 59],
                        ['user_id' => $userID, 'role_id' => 62],
                        ['user_id' => $userID, 'role_id' => 63],
                    ]);
                }

                return $this->index();
            } else {
                return response()->json(['error' => 'Email already exists!'], 500);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {


            $fileName = null;
            if ($request->img != "") {

                $exploded = explode(',', $request->img);

                $decoded = base64_decode($exploded[1]);

                // if (str_contains($exploded[0], "jpeg"))
                //     $extension = "jpeg";
                // elseif (str_contains($exploded[0], "jpg"))
                //     $extension = "jpg";
                // elseif (str_contains($exploded[0], "png"))
                //     $extension = "png";
                // elseif (str_contains($exploded[0], "gif"))
                //     $extension = "gif";
                // elseif (str_contains($exploded[0], "tiff"))
                //     $extension = "tiff";
                // elseif (str_contains($exploded[0], "bmp"))
                //     $extension = "bmp";
                // else
                $extension = "jpg";

                $fileName = $id . "." . $extension;

                $path = public_path() . "/imgs/" . $fileName;

                file_put_contents($path, $decoded);
            }

            $dd = [
                'email' => $request->email,
                'branch_id' => $request->branch_id,
                'name' => $request->name,
                'elBG' => $request->elBG,
                'img_bg' => $fileName,
                'elClr' => $request->elClr
            ];
            if ($request->password != '') {
                $dd = [
                    'email' => $request->email,
                    'branch_id' => $request->branch_id,
                    'name' => $request->name,
                    'elBG' => $request->elBG,
                    'elClr' => $request->elClr,
                    'img_bg' => $fileName,
                    'password' => bcrypt($request->password),
                ];
            }
            User::where('id', $id)
                ->update($dd);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            User::destroy($id);

            return $this->index();
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was a problem deleting this user.'], 500);
        }
    }

    public function getUser($email)
    {
        $user = User::with(['branch'])
            ->where('email', '=', $email)->first();
        return response()->json($user);
    }

    public function updateTheme(Request $request, $id)
    {
        try {
            User::where('id', $id)
                ->update([
                    'elBG' => $request->elBG,
                    'elClr' => $request->elClr,
                ]);
            return "ok";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function updateRoles(Request $request)
    {
        try {
            $id = $request->id;
            DB::table('role_user')->where('user_id', $id)->delete();
            $roles = [
                "create_account",
                "update_account",
                "delete_account",
                "create_client",
                "update_client",
                'delete_client',
                'create_client_details',
                'update_client_details',
                'delete_client_details',
                'create_package_type',
                'update_package_type',
                'delete_package_type',
                'create_package',
                'update_package',
                'delete_package',
                'create_modem',
                'update_modem',
                'delete_modem',
                'create_engineer',
                'update_engineer',
                'delete_engineer',
                'create_sales',
                'update_sales',
                'delete_sales',
                'create_region',
                'update_region',
                'delete_region',
                'create_job_order',
                'update_job_order',
                'delete_job_order',
                'create_ticket',
                'update_ticket',
                'delete_ticket',
                'create_ticket_status',
                'update_ticket_status',
                'delete_ticket_status',
                'create_olt',
                'update_olt',
                'delete_olt',
                'create_pon',
                'update_pon',
                'delete_pon',
                'create_event',
                'update_event',
                'delete_event',
                'viewer',
                'create_team',
                'update_team',
                'delete_team',
                'assign_team',
                'assign_date',
                'accounting',
                'helpdesk',
                'operator',
                'role',
                'rm',
                'network',
                'admin',
                'sendcvc',
                'sales',
                'account_management',
                'create_business_type',
                'update_business_type',
                'delete_business_type',
                'create_bill',
                'update_soa',
                'delete_soa',
                'create_bankcode',
                'update_bankcode',
                'delete_bankcode',
                'receive_payment',
                'create_wht',
                'create_area',
                'update_area',
                'delete_area',
                'create_bucket',
                'update_bucket',
                'delete_bucket',
                'create_region',
                'update_region',
                'delete_region',

            ];
            $x = 0;
            $roletemp = [];
            foreach ($roles as $role) {
                $x++;
                if (isset($request->roles[$role])) {
                    if ($request->roles[$role]) {
                        $temp = ['user_id' => $id, 'role_id' => $x];
                        array_push($roletemp, $temp);
                    }
                }
            }
            DB::table('role_user')->insert($roletemp);
            return $this->index();
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

    public function getEmails()
    {

        $tbl = User::select('name', 'email')->get();
        return $tbl;
    }

    public function log_login(Request $request)
    {
        \Logger::instance()->logError(
            Carbon::now(),
            "",
            "",
            $this->cname,
            "Log in Incorrect credentials",
            "Error",
            "Username used: " . $request->username . " password used: " . $request->password
        );
        return "logged";
    }
}
