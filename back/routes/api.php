<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json([
        'user' => [
            'first_name' => 'Peter',
            'last_name' => 'Pogi'
        ]
    ]);
});
Route::get('calendar_events/testAutoCommand', 'CalendarEventController@testAutoCommand');

Route::post('log_login', 'UserController@log_login');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('Branch', 'BranchController');
    Route::resource('Region', 'RegionController');
    Route::resource('Client', 'ClientController');
    Route::resource('ClientDetail', 'ClientDetailController');
    Route::resource('Engineer', 'EngineerController');
    Route::resource('JobOrder', 'JobOrderController');
    Route::resource('Modem', 'ModemController');
    Route::resource('Package', 'PackageController');
    Route::resource('PackageType', 'PackageTypeController');
    Route::resource('Role', 'RoleController');
    Route::resource('Sales', 'SalesController');
    Route::resource('user', 'UserController');
    Route::resource('Ticket', 'TicketController');
    Route::resource('TicketGroup', 'TicketGroupController');
    Route::resource('TicketStatus', 'TicketStatusController');
    Route::resource('ComplaintList', 'ComplaintListController');
    Route::resource('Advisory', 'AdvisoryController');
    Route::resource('ActivityTicketType', 'ActivityTicketTypeController');
    Route::resource('ActivityTicket', 'ActivityTicketController');


    Route::resource('Suggestion', 'SuggestionController');
    Route::resource('olt', 'OltController');
    Route::resource('pon', 'PonController');
    Route::resource('calendar_events', 'CalendarEventController');
    Route::resource('SuggestionComment', 'SuggestionCommentController');
    Route::resource('team', 'TeamController');
    Route::resource('Schedule', 'ScheduleController');
    Route::resource('MacDeploymentIdentifier', 'MacDeploymentIdentifierController');
    Route::resource('MacLocation', 'MacLocationController');
    Route::resource('MacTable', 'MacTableController');
    Route::resource('DeviceType', 'DeviceTypeController');
    Route::resource('BusinessType', 'BusinessTypeController');

    Route::resource('Closure', 'ClosureController');
    Route::resource('ClosureType', 'ClosureTypeController');
    Route::resource('Hardfiber', 'HardfiberController');
    Route::resource('HardfiberCoordinate', 'HardfiberCoordinateController');
    Route::resource('HardfiberCore', 'HardfiberCoreController');
    Route::resource('Node', 'NodeController');
    Route::resource('Splitter', 'SplitterController');
    Route::resource('SplitterPort', 'SplitterPortController');
    Route::resource('SplitterType', 'SplitterTypeController');
    Route::resource('TicketRemarksLog', 'TicketRemarksLogController');
    Route::resource('TicketCommentsLog', 'TicketCommentsLogController');
    Route::resource('InstallationRemarksLog', 'InstallationRemarksLogController');

    Route::resource('Paymethod', 'PaymentMethodController');
    Route::resource('BankCode', 'BankingPaymentCodeController');
    Route::resource('Billing', 'BillingController');
    Route::resource('CustomerPayment', 'CustomerPaymentController');
    Route::resource('WHT', 'WhtController');
    Route::resource('area', 'AreaController');
    Route::resource('Bucket', 'BucketController');
    Route::resource('sales_agent', 'SalesAgentController');
    Route::resource('Rebates', 'RebateController');
    Route::resource('getStatusHistory', 'ClientStatusHistoryController');
    Route::resource('ClientAttachment', 'ClientAttachmentController');

    Route::post('Rebates/report', 'RebateController@report');
    Route::post('WHT/report', 'WhtController@report');

    Route::post('agent_report', 'SalesAgentController@agent_report');
    Route::post('BankCode/getall', 'BankingPaymentCodeController@getall');
    Route::post('BankCode/store_multi', 'BankingPaymentCodeController@store_multi');

    Route::post('Billing/agingReport', 'BillingController@agingReport');
    Route::post('Billing/updateSOA', 'BillingController@updateSOA');
    Route::post('Billing/deleteSOA', 'BillingController@deleteSOA');
    Route::post('Billing/deleteMultiSOA', 'BillingController@deleteMultiSOA');
    Route::post('Billing/deleteBillState', 'BillingController@deleteBillState');
    Route::post('Billing/soa/{id}/{billrange}', 'BillingController@soa');
    Route::post('Billing/to_pay/{id}/{billrange}', 'BillingController@to_pay');
    Route::post('Billing/emailSOA', 'BillingController@emailSOA');
    Route::post('Billing/storeBillStatement', 'BillingController@storeBillStatement');
    Route::post('Billing/getBillStateList', 'BillingController@getBillStateList');
    Route::post('Billing/generateBill', 'BillingController@generateBill');
    Route::post('Billing/insertGeneratedBill', 'BillingController@insertGeneratedBill');
    Route::post('Billing/getTransmittal', 'BillingController@getTransmittal');
    Route::post('Billing/getBillToExport', 'BillingController@getBillToExport');


    Route::post('CustomerPayment/dailyReport', 'CustomerPaymentController@dailyReport');
    Route::post('CustomerPayment/storePayment', 'CustomerPaymentController@storePayment');

    Route::post('Splitter/modiStore', 'SplitterController@modiStore');

    Route::post('user/updateRoles', 'UserController@updateRoles');
    Route::get('user/getUser/{email}', 'UserController@getUser');
    Route::post('user/updateTheme/{id}', 'UserController@updateTheme');
    Route::get('getEmails', 'UserController@getEmails');



    Route::put('clientDetail/clientConfirm/{id}', 'ClientDetailController@clientConfirm');
    Route::post('clientDetail/updateTargetDate', 'ClientDetailController@updateTargetDate');
    Route::post('clientDetail/storeJobOrder', 'ClientDetailController@storeJobOrder');
    Route::post('clientDetail/destroyJobOrder', 'ClientDetailController@destroyJobOrder');
    Route::post('clientDetail/activateClient', 'ClientDetailController@activateClient');
    Route::get('clientDetail/installationSummary/{year}/{region_id}', 'ClientDetailController@installationSummary');
    Route::get('clientDetail/subIndex/{region_id}', 'ClientDetailController@subIndex');
    Route::post('clientDetail/destroy1', 'ClientDetailController@destroy1');
    Route::get('client_has_sched/{id}', 'ClientDetailController@client_has_sched');
    Route::post('performance_sheet', 'ClientDetailController@performance_sheet');
    Route::post('misc_summary', 'ClientDetailController@misc_summary');
    Route::post('temp_graph', 'ClientDetailController@temp_graph');
    Route::post('trouble_graph', 'ClientDetailController@trouble_graph');
    Route::post('sales_graph', 'ClientDetailController@sales_graph');
    Route::get('clientDetail/search_data/{by}/{search}', 'ClientDetailController@search_data');
    Route::get('clientDetail/getAll', 'ClientDetailController@getAll');
    Route::post('clientDetail/multipleFilter', 'ClientDetailController@multipleFilter');
    Route::post('clientDetail/update_per_row', 'ClientDetailController@update_per_row');
    Route::post('clientDetail/getTrend/{region_id}', 'ClientDetailController@trend');

    Route::post('send_mail_otp', 'ClientDetailController@send_mail_otp');



    Route::get('Ticket/eticketSummary/{year}/{region_id}', 'TicketController@eticketSummary');
    Route::get('Ticket/subIndex/{region_id}', 'TicketController@subIndex');
    // Route::get('Ticket/{id}/{region_id}', 'TicketController@destroy1');
    Route::post('Ticket/deleteTicket', 'TicketController@destroy1');
    Route::get('checkTicket/{id}', 'TicketController@checkTicket');
    Route::post('Ticket/updateTargetDate', 'TicketController@updateTargetDate');
    Route::post('Ticket/sendText', 'TicketController@sendText');
    Route::post('Ticket/troubleTypes', 'TicketController@troubleTypes');
    Route::post('Ticket/complaintList', 'TicketController@complaintList');

    Route::get('getTrend', 'TicketController@getTrend');
    Route::post('getTrend', 'TicketController@getTrend');
    Route::get('checkAreas/{id}', 'TicketController@checkAreas');
    Route::post('getTemplate', 'AdvisoryController@getTemplate');


    Route::post('Ticket/filterByDate', 'TicketController@filterByDate');
    Route::post('Ticket/multipleFilter', 'TicketController@multipleFilter');
    Route::post('Ticket/emailTicket', 'TicketController@emailTicket');


    Route::post('getComplaint', 'TicketController@getComplaint');
    Route::get('getMonitoring/{region_id}', 'TicketController@getMonitoring');
    Route::get('getMonitoringSched/{region_id}', 'ClientDetailController@getMonitoring');
    Route::post('CalculateRebates', 'TicketController@CalculateRebates');
    Route::post('updateRebates', 'TicketController@updateRebates');
    Route::get('Ticket/search_data/{by}/{search}', 'TicketController@search_data');
    Route::get('Ticket/getAll', 'TicketController@getAll');

    Route::post('TicketRemarksLog/moveTicketRemarks', 'TicketRemarksLogController@moveTicketRemarks');



    Route::get('pon/{id}/{olt_id}', 'PonController@destroy1');

    Route::get('calendar_events/subIndex/{region_id}', 'CalendarEventController@subIndex');
    Route::post('calendar_events/destroy1', 'CalendarEventController@destroy1');
    Route::post('calendar_events/testSSH', 'CalendarEventController@testSSH');
    Route::post('calendar_events/testEmail', 'CalendarEventController@testEmail');
    Route::post('calendar_events/download_database', 'CalendarEventController@download_database');
    Route::post('calendar_events/testfunction', 'CalendarEventController@testfunction');

    Route::post('Client/filterByDate', 'ClientController@filterByDate');
    Route::get('Client/subIndex/{region_id}', 'ClientController@subIndex');
    Route::get('Client/cancelled/{region_id}', 'ClientController@cancelled');
    Route::post('Client/destroy1', 'ClientController@destroy1');
    Route::post('updatePackage', 'ClientController@updatePackage');
    Route::post('getSched', 'ClientController@getSched');
    Route::get('getClients/{region_id}', 'ClientController@getClients');
    Route::post('updateDateActivated', 'ClientController@updateDateActivated');
    Route::get('Client/filterNoDOP/{region_id}', 'ClientController@filterNoDOP');
    Route::get('Client/filterNoContract/{region_id}', 'ClientController@filterNoContract');
    Route::get('Client/filterWFC/{region_id}', 'ClientController@filterWFC');
    Route::get('Client/filterWFS/{region_id}', 'ClientController@filterWFS');
    Route::get('Client/filterExpired/{region_id}', 'ClientController@filterExpired');
    Route::get('Client/filterCease/{region_id}', 'ClientController@filterCease');
    Route::get('Client/search_data/{by}/{search}/{state}', 'ClientController@search_data');
    Route::get('Client/getAll', 'ClientController@getAll');
    Route::post('Client/updateDOP', 'ClientController@updateDOP');
    Route::post('Client/updateContract', 'ClientController@updateContract');
    Route::post('Client/updateSubscription', 'ClientController@updateSubscription');
    Route::post('Client/multipleFilter', 'ClientController@multipleFilter');
    Route::get('getContacts', 'ClientController@getContacts');
    Route::get('getClientEmail', 'ClientController@getClientEmail');
    Route::post('Client/update_per_row', 'ClientController@update_per_row');
    Route::post('Client/cancelClient', 'ClientController@cancelClient');
    Route::post('Client/retrieveClient', 'ClientController@retrieveClient');
    Route::post('Client/updateRows', 'ClientController@updateRows');



    Route::post('testlog', 'ClientController@testlog');

    // bucket info clients
    Route::get('getBucketInfoClient', 'TicketGroupController@getBucketInfoClient');
    Route::get('getBucketInfoClient/all', 'TicketGroupController@getBucketInfoClientAll');
    Route::post('match/{client}', 'TicketGroupController@match');
    Route::post('unmatch/{client}', 'TicketGroupController@unmatch');

    Route::get('JobOrder/subIndex/{region_id}', 'JobOrderController@subIndex');
    Route::post('JobOrder/filterByDate', 'JobOrderController@filterByDate');
    Route::get('JobOrder/getMaxID/{region_id}', 'JobOrderController@getMaxID');
    Route::get('jobOrder/getJobOrder/{jobID}', 'JobOrderController@getJobOrder');
    Route::get('jobOrder/search_data/{by}/{search}', 'JobOrderController@search_data');
    Route::post('jobOrder/destroy1', 'JobOrderController@destroy1');


    Route::put('getComments/{s_id}', 'SuggestionCommentController@getComments');

    Route::put('team/summary/{id}', 'TeamController@summary');
    Route::post('team/summary_accounts', 'TeamController@summary_accounts');


    Route::post('summary_report_daterange', 'ScheduleController@summary_report_daterange');

    Route::put('Bucket/encryptIt/{q}', 'BucketController@encryptIt');
    Route::put('Bucket/decryptIt/{q}', 'BucketController@decryptIt');
    Route::post('Bucket/ssh_command', 'BucketController@ssh_command');



    Route::post('ActivityTicket/updateClientStatus', 'ActivityTicketController@updateClientStatus');
    Route::post('ActivityTicket/getTempDiscon', 'ActivityTicketController@getTempDiscon');
    Route::post('ActivityTicket/verify', 'ActivityTicketController@verify');
    Route::get('ActivityTicket/search_data/{by}/{search}', 'ActivityTicketController@search_data');
    Route::get('ActivityTicket/subIndex/{role}', 'ActivityTicketController@subIndex');
    Route::post('ActivityTicket/updateSOA/', 'ActivityTicketController@updateSOA');
});
