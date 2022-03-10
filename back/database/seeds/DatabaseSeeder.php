<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        App\User::create([
            'name' => 'Research and Development',
            'region_id' => '0',
            'email' => 'rnd@dctechmicro.com',
            'password' => bcrypt('mweakthegenius'),
            'remember_token' => str_random(10),
        ]);

        App\Role::create(['name' => 'create_account']); //1
        App\Role::create(['name' => 'update_account']); //2
        App\Role::create(['name' => 'delete_account']); //3

        App\Role::create(['name' => 'create_client']); //4
        App\Role::create(['name' => 'update_client']); //5
        App\Role::create(['name' => 'delete_client']); //6

        App\Role::create(['name' => 'create_client_details']); //7
        App\Role::create(['name' => 'update_client_details']); //8
        App\Role::create(['name' => 'delete_client_details']); //9

        App\Role::create(['name' => 'create_package_type']); //10
        App\Role::create(['name' => 'update_package_type']); //11
        App\Role::create(['name' => 'delete_package_type']); //12

        App\Role::create(['name' => 'create_package']); //13
        App\Role::create(['name' => 'update_package']); //14
        App\Role::create(['name' => 'delete_package']); //15

        App\Role::create(['name' => 'create_modem']); //16
        App\Role::create(['name' => 'update_modem']); //17
        App\Role::create(['name' => 'delete_modem']); //18

        App\Role::create(['name' => 'create_engineer']); //19
        App\Role::create(['name' => 'update_engineer']); //20
        App\Role::create(['name' => 'delete_engineer']); //21

        App\Role::create(['name' => 'create_sales']); //22
        App\Role::create(['name' => 'update_sales']); //23
        App\Role::create(['name' => 'delete_sales']); //24

        App\Role::create(['name' => 'create_branch']); //25
        App\Role::create(['name' => 'update_branch']); //26
        App\Role::create(['name' => 'delete_branch']); //27

        App\Role::create(['name' => 'create_job_order']); //28
        App\Role::create(['name' => 'update_job_order']); //29
        App\Role::create(['name' => 'delete_job_order']); //30

        App\Role::create(['name' => 'create_ticket']); //31
        App\Role::create(['name' => 'update_ticket']); //32
        App\Role::create(['name' => 'delete_ticket']); //33

        App\Role::create(['name' => 'create_ticket_status']); //34
        App\Role::create(['name' => 'update_ticket_status']); //35
        App\Role::create(['name' => 'delete_ticket_status']); //36

        App\Role::create(['name' => 'create_olt']); //37
        App\Role::create(['name' => 'update_olt']); //38
        App\Role::create(['name' => 'delete_olt']); //39

        App\Role::create(['name' => 'create_pon']); //40
        App\Role::create(['name' => 'update_pon']); //41
        App\Role::create(['name' => 'delete_pon']); //42

        App\Role::create(['name' => 'create_event']); //43
        App\Role::create(['name' => 'update_event']); //44
        App\Role::create(['name' => 'delete_event']); //45
        App\Role::create(['name' => 'viewer']); //46

        App\Role::create(['name' => 'create_team']); //47
        App\Role::create(['name' => 'update_team']); //48
        App\Role::create(['name' => 'delete_team']); //49

        App\Role::create(['name' => 'assign_team']); //50
        App\Role::create(['name' => 'assign_date']); //51
        App\Role::create(['name' => 'accounting']); //52
        App\Role::create(['name' => 'helpdesk']); //53
        App\Role::create(['name' => 'operator']); //54
        App\Role::create(['name' => 'role']); //55
        App\Role::create(['name' => 'rm']); //56
        App\Role::create(['name' => 'network']); //57
        App\Role::create(['name' => 'admin']); //58
        App\Role::create(['name' => 'sendcvc']); //59
        App\Role::create(['name' => 'sales']); //60
        App\Role::create(['name' => 'account_management']); //61

        App\Role::create(['name' => 'create_business_type']); //62
        App\Role::create(['name' => 'update_business_type']); //63
        App\Role::create(['name' => 'delete_business_type']); //64

        App\Role::create(['name' => 'create_bill']); //65
        App\Role::create(['name' => 'update_soa']); //66
        App\Role::create(['name' => 'delete_soa']); //67
        App\Role::create(['name' => 'create_bankcode']); //68
        App\Role::create(['name' => 'update_bankcode']); //69
        App\Role::create(['name' => 'delete_bankcode']); //70
        App\Role::create(['name' => 'receive_payment']); //71
        App\Role::create(['name' => 'create_wht']); //72

        App\Role::create(['name' => 'create_area']); //73
        App\Role::create(['name' => 'update_area']); //74
        App\Role::create(['name' => 'delete_area']); //75

        App\Role::create(['name' => 'create_bucket']); //76
        App\Role::create(['name' => 'update_bucket']); //77
        App\Role::create(['name' => 'delete_bucket']); //78

        App\Role::create(['name' => 'create_region']); //79
        App\Role::create(['name' => 'update_region']); //80
        App\Role::create(['name' => 'delete_region']); //81


        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 1, 'role_id' => 2],
            ['user_id' => 1, 'role_id' => 3],
            ['user_id' => 1, 'role_id' => 4],
            ['user_id' => 1, 'role_id' => 5],
            ['user_id' => 1, 'role_id' => 6],
            ['user_id' => 1, 'role_id' => 7],
            ['user_id' => 1, 'role_id' => 8],
            ['user_id' => 1, 'role_id' => 9],
            ['user_id' => 1, 'role_id' => 10],
            ['user_id' => 1, 'role_id' => 11],
            ['user_id' => 1, 'role_id' => 12],
            ['user_id' => 1, 'role_id' => 13],
            ['user_id' => 1, 'role_id' => 14],
            ['user_id' => 1, 'role_id' => 15],
            ['user_id' => 1, 'role_id' => 16],
            ['user_id' => 1, 'role_id' => 17],
            ['user_id' => 1, 'role_id' => 18],
            ['user_id' => 1, 'role_id' => 19],
            ['user_id' => 1, 'role_id' => 20],
            ['user_id' => 1, 'role_id' => 21],
            ['user_id' => 1, 'role_id' => 22],
            ['user_id' => 1, 'role_id' => 23],
            ['user_id' => 1, 'role_id' => 24],
            ['user_id' => 1, 'role_id' => 25],
            ['user_id' => 1, 'role_id' => 26],
            ['user_id' => 1, 'role_id' => 27],
            ['user_id' => 1, 'role_id' => 28],
            ['user_id' => 1, 'role_id' => 29],
            ['user_id' => 1, 'role_id' => 30],
            ['user_id' => 1, 'role_id' => 31],
            ['user_id' => 1, 'role_id' => 32],
            ['user_id' => 1, 'role_id' => 33],
            ['user_id' => 1, 'role_id' => 34],
            ['user_id' => 1, 'role_id' => 35],
            ['user_id' => 1, 'role_id' => 36],
            ['user_id' => 1, 'role_id' => 37],
            ['user_id' => 1, 'role_id' => 38],
            ['user_id' => 1, 'role_id' => 39],
            ['user_id' => 1, 'role_id' => 40],
            ['user_id' => 1, 'role_id' => 41],
            ['user_id' => 1, 'role_id' => 42],
            ['user_id' => 1, 'role_id' => 43],
            ['user_id' => 1, 'role_id' => 44],
            ['user_id' => 1, 'role_id' => 45],
            ['user_id' => 1, 'role_id' => 47],
            ['user_id' => 1, 'role_id' => 48],
            ['user_id' => 1, 'role_id' => 49],
            ['user_id' => 1, 'role_id' => 50],
            ['user_id' => 1, 'role_id' => 51],
            ['user_id' => 1, 'role_id' => 52],
            ['user_id' => 1, 'role_id' => 53],
            ['user_id' => 1, 'role_id' => 54],
            ['user_id' => 1, 'role_id' => 55],
            ['user_id' => 1, 'role_id' => 56],
            ['user_id' => 1, 'role_id' => 57],
            ['user_id' => 1, 'role_id' => 58],
        ]);

        DB::table('package_types')->insert([
            ['name' => 'SME', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'CORP', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENT', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RES', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06']
        ]);

        DB::table('packages')->insert([
            ['name' => 'RSU1K18013', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU1K1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU1K3H1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU1K8H1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU2K1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU2K5H1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU2K8H1803', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU31902', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSU999', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUD11901', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUR11901', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUR11902', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUR21902', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUR41901', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RSUR41902', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'RU2000', 'package_type_id' => '4', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],

            ['name' => 'CLUD11901', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'CLUD21902', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'CLUR11901', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COU23K1803', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COU28K1803', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COU30K1803', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COU32K1803', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COU40K1803', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COUR61901', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'COUR81901', 'package_type_id' => '2', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENU100K1803', 'package_type_id' => '3', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENU63K1803', 'package_type_id' => '3', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENUD41901', 'package_type_id' => '3', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENUR11901', 'package_type_id' => '3', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'ENUR31901', 'package_type_id' => '3', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLU15K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLU3K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLU4K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLU5K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLU6K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUD11901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUD11902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUR11901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUR11902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUR21901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUR21902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SLUR31901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMELU1', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMELU2', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMELUS', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMELUR5', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU10K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU11K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU13K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU16K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU6K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU7K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMU9K1803', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUD11901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUD91901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR11901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR11902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR31901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR31902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR41902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR51901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR51902', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'SMUR71901', 'package_type_id' => '1', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],

        ]);

        DB::table('modems')->insert([
            ['name' => 'King type', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Phyhome', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Richer link', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Vsol', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06']
        ]);


        DB::table('engineers')->insert([
            ['name' => 'Engr. Ryan Sumalinog', 'position' => 'VP for Operation', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Engr. Clemente Tresfuentes Jr.', 'position' => 'Department Head', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Engr. Jhun Bryan Cenabre', 'position' => 'Senior Technical Sales', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Butch Edward Rabusa', 'position' => 'Technical Sales', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'John Paul Caliso', 'position' => 'Technical Sales', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06']
        ]);

        // DB::table('sales')->insert([
        //     ['name' => 'Cherry', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Marry', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Ketty', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Jessa', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Reian', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Joseph', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06']
        // ]);

        // DB::table('regions')->insert([
        //     ['name' => 'Dvo', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Digos', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Tagum', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'CDO', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        //     ['name' => 'Gensan', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06']
        // ]);

        DB::table('ticket_statuses')->insert([
            ['name' => 'Pending', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Urgent', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'Close', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
            ['name' => 'For Tech Visit', 'created_at' => '2019-06-12 01:12:06', 'updated_at' => '2019-06-12 01:12:06'],
        ]);

        App\olt::create(['id' => '1', 'name' => 'VSOL', 'ip' => '.27']);
        App\olt::create(['id' => '2', 'name' => 'Kingtype', 'ip' => '.35']);
        App\olt::create(['id' => '3', 'name' => 'Kingtype', 'ip' => '.69']);
        App\olt::create(['id' => '4', 'name' => 'Kingtype', 'ip' => '.68']);

        App\pon::create(['olt_id' => '1', 'pon' => '1', 'area' => 'OFFICE']);
        App\pon::create(['olt_id' => '1', 'pon' => '2', 'area' => 'PANACAN']);
        App\pon::create(['olt_id' => '1', 'pon' => '3', 'area' => 'ULAS CABINET']);
        App\pon::create(['olt_id' => '1', 'pon' => '4', 'area' => 'UYANGGUREN CABINET']);
        App\pon::create(['olt_id' => '1', 'pon' => '5', 'area' => 'SANDAWA-BOULEVARD']);
        App\pon::create(['olt_id' => '1', 'pon' => '6', 'area' => 'V-MAPA']);
        App\pon::create(['olt_id' => '1', 'pon' => '7', 'area' => 'MATINA GSIS']);
        App\pon::create(['olt_id' => '1', 'pon' => '8', 'area' => 'JUNA']);

        App\pon::create(['olt_id' => '2', 'pon' => '1', 'area' => 'Dacudao ver. 2']);
        App\pon::create(['olt_id' => '2', 'pon' => '2', 'area' => 'Matina - Tulip']);
        App\pon::create(['olt_id' => '2', 'pon' => '3', 'area' => 'Cogot']);
        App\pon::create(['olt_id' => '2', 'pon' => '4', 'area' => 'Office']);
        App\pon::create(['olt_id' => '2', 'pon' => '5', 'area' => 'Dacudao']);
        App\pon::create(['olt_id' => '2', 'pon' => '6', 'area' => 'Maa']);
        App\pon::create(['olt_id' => '2', 'pon' => '7', 'area' => 'Magallanes Residences']);
        App\pon::create(['olt_id' => '2', 'pon' => '8', 'area' => 'Uyanguren']);

        App\pon::create(['olt_id' => '3', 'pon' => '1', 'area' => 'TIGATTO']);
        App\pon::create(['olt_id' => '3', 'pon' => '2', 'area' => 'Bacaca ']);
        App\pon::create(['olt_id' => '3', 'pon' => '3', 'area' => 'DIVERSION BUHANGIN']);
        App\pon::create(['olt_id' => '3', 'pon' => '4', 'area' => 'BOULEVARD FATIMA']);
        App\pon::create(['olt_id' => '3', 'pon' => '5', 'area' => 'CABAGUIO']);
        App\pon::create(['olt_id' => '3', 'pon' => '6', 'area' => 'Mamay']);
        App\pon::create(['olt_id' => '3', 'pon' => '8', 'area' => 'LANANG']);

        App\pon::create(['olt_id' => '4', 'pon' => '1', 'area' => 'RIZAL EXTENSION']);
        App\pon::create(['olt_id' => '4', 'pon' => '2', 'area' => 'RCASTILLO']);
        App\pon::create(['olt_id' => '4', 'pon' => '3', 'area' => 'CABANTIAN']);
        App\pon::create(['olt_id' => '4', 'pon' => '4', 'area' => 'SASA']);
        App\pon::create(['olt_id' => '4', 'pon' => '5', 'area' => 'Torres']);
        App\pon::create(['olt_id' => '4', 'pon' => '6', 'area' => 'JACINTO - ATENEO']);
        App\pon::create(['olt_id' => '4', 'pon' => '7', 'area' => 'SPC ULAS']);
        App\pon::create(['olt_id' => '4', 'pon' => '8', 'area' => 'KANTO PUAN']);
        App\pon::create(['olt_id' => '4', 'pon' => '9', 'area' => 'Maa']);


        DB::statement("ALTER TABLE job_orders AUTO_INCREMENT = 10000");
        DB::statement("ALTER TABLE tickets AUTO_INCREMENT = 1000");
    }
}
