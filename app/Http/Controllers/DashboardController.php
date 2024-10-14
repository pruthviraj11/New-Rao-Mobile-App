<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatuses;
use App\Models\ClientType;
use App\Models\ChatSession;
use App\Models\ManageRoleSettings;
use App\Models\User;
use App\Models\ChatSessionMessage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Setting;
use DB;
use DataTables;
use Spatie\Permission\Models\Permission;
class DashboardController extends Controller
{
    public function index()
    {
        // $fullAccessRolesData = assignRoleToUsers();
        // dd($fullAccessRolesData);
        $financialYears = $this->getFinancialYears();
        $categorys = ClientType::where('status', '1')->get();
        return view('content/apps/DashboredReport/list', compact('categorys', 'financialYears'));
    }

    public function getSummry(Request $request)
    {
        $defolt_message = [
            DB::table('settings')->where('key', 'admin.admin_send_seven_default_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_transfer_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.admin_sunday_default_message')->value('value')
        ];


        $data_chat = DB::table('chat_session_messages')
            ->select('chat_session_id', DB::raw('count(*) as message_count'))
            ->where('type', '!=', 'outgoing')
            ->groupBy('chat_session_id')
            ->havingRaw('SUM(CASE WHEN type = "outgoing" THEN 1 ELSE 0 END) = 0')
            ->get();

        $fe_users = DB::table('users')->where('role_id', 2)->where('user_category', 1)->pluck('id')->toArray(); //FE Users
        $fe_advisor_not_responded = 0;
        foreach ($fe_users as $usr) {
            $chat_sessions = DB::table('chat_sessions')->where('client_id', $usr)->latest('created_at')->first();
            if ($chat_sessions) {
                $chat_sessions_messages = DB::table('chat_session_messages')
                    ->where('chat_session_id', $chat_sessions->id)
                    ->where('sender_id', $chat_sessions->advisor_id)
                    ->whereNotIn('message', $defolt_message)->count();
                if ($chat_sessions_messages == 0) {
                    $fe_advisor_not_responded++;
                }
            }


        }

        $iv_users = DB::table('users')->where('role_id', 2)->where('user_category', 2)->pluck('id')->toArray(); //IV Users
        $iv_advisor_not_responded = 0;
        foreach ($iv_users as $usr) {
            $chat_sessions = DB::table('chat_sessions')->where('client_id', $usr)->latest('created_at')->first();
            if ($chat_sessions) {
                $chat_sessions_messages = DB::table('chat_session_messages')
                    ->where('chat_session_id', $chat_sessions->id)
                    ->where('sender_id', $chat_sessions->advisor_id)
                    ->whereNotIn('message', $defolt_message)->count();
                if ($chat_sessions_messages == 0) {
                    $iv_advisor_not_responded++;
                }
            }

        }
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (Monday)
        $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (Sunday)
        if ($request->get('year')) {
            $currentYear = $request->get('year');
        } else {

            $currentYear = Carbon::now()->year;
        }
        $startOfFinancialYear = Carbon::createFromDate($currentYear, 4, 1); // Assuming financial year starts in April
        $endOfFinancialYear = Carbon::createFromDate($currentYear + 1, 3, 31); // Ends in March next year

        // Fetch data for "Today"
        $today_fe['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 1)
            ->where('is_download', 'Yes')
            ->whereDate('download_date', $today)
            ->count();
        $thisWeek_fe['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 1)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfWeek, $endOfWeek])
            ->count();
        // Fetch data for "This Month"
        $dataThisMonth_fe['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 1)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Fetch data for "Current Financial Year"
        $dataCurrentFinancialYear_fe['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 1)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        // Fetch "Overall" data
        $dataOverall_fe['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 1)
            ->where('is_download', 'Yes')
            ->count();

        $today_iv['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 2)
            ->where('is_download', 'Yes')
            ->whereDate('download_date', $today)
            ->count();
        $thisWeek_iv['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 2)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfWeek, $endOfWeek])
            ->count();
        // Fetch data for "This Month"
        $dataThisMonth_iv['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 2)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Fetch data for "Current Financial Year"
        $dataCurrentFinancialYear_iv['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 2)
            ->where('is_download', 'Yes')
            ->whereBetween('download_date', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        // Fetch "Overall" data
        $dataOverall_iv['have_reg'] = User::where('role_id', 2)
            ->where('user_category', 2)
            ->where('is_download', 'Yes')
            ->count();
        $firstIncomingMessagesCount = ChatSessionMessage::whereIn('id', function ($query) {
            $query->selectRaw('MIN(id)')
                ->from('chat_session_messages')
                ->whereNotNull('chat_session_id')
                ->whereNotNull('sender_id')
                ->where('type', 'incoming')
                ->groupBy('chat_session_id');
        })->count();


        // Default messages


        function countMessages($defolt_message, $user_category, $start_date = null, $end_date = null)
        {
            return ChatSession::whereHas('chatSessionMessages', function ($query) use ($defolt_message, $start_date, $end_date) {
                $query->where('type', 'outgoing')
                    ->whereNotIn('message', $defolt_message)
                    ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                        $q->whereBetween('created_at', [$start_date, $end_date]);
                    });
            })
                // ->whereHas('advisor', function ($query) use ($user_category) {
                //     $query->where('user_category', $user_category); // Filter by user category (fe or iv)
                // })
                ->get()
                ->filter(function ($chatSession) use ($defolt_message) {
                    // Get the first message of the session
                    $firstMessage = $chatSession->chatSessionMessages()->orderBy('created_at', 'asc')->first();

                    // Check if the first message is outgoing and not a default message
                    return $firstMessage && $firstMessage->type == 'outgoing' && !in_array($firstMessage->message, $defolt_message);
                })->count();
        }

        // Counts for FE (user_category = 1)
        $today_fe['adviser_start'] = countMessages($defolt_message, 1, $today, $today);
        $week_fe['adviser_start'] = countMessages($defolt_message, 1, $startOfWeek, $endOfWeek);
        $month_fe['adviser_start'] = countMessages($defolt_message, 1, $startOfMonth, $endOfMonth);
        $fiscal_fe['adviser_start'] = countMessages($defolt_message, 1, $startOfFinancialYear, $endOfFinancialYear);
        $overall_fe['adviser_start'] = countMessages($defolt_message, 1); // No date filter

        // Counts for IV (user_category = 2)
        $today_iv['adviser_start'] = countMessages($defolt_message, 2, $today, $today);
        $week_iv['adviser_start'] = countMessages($defolt_message, 2, $startOfWeek, $endOfWeek);
        $month_iv['adviser_start'] = countMessages($defolt_message, 2, $startOfMonth, $endOfMonth);
        $fiscal_iv['adviser_start'] = countMessages($defolt_message, 2, $startOfFinancialYear, $endOfFinancialYear);
        $overall_iv['adviser_start'] = countMessages($defolt_message, 2); // No date filter




        // Get user IDs for FE and IV
        $fe_users = DB::table('users')->where('role_id', 2)->where('user_category', 1)->pluck('id')->toArray();
        $iv_users = DB::table('users')->where('role_id', 2)->where('user_category', 2)->pluck('id')->toArray();

        // Initialize counts
        $counts = [
            'today_fe' => 0,
            'today_iv' => 0,
            'week_fe' => 0,
            'week_iv' => 0,
            'month_fe' => 0,
            'month_iv' => 0,
            'fiscal_fe' => 0,
            'fiscal_iv' => 0,
            'overall_fe' => 0,
            'overall_iv' => 0
        ];

        // Helper function to count non-responders
        function countNonResponders($userIds, $startDate, $endDate, &$counts, $category, $defolt_message)
        {
            // Fetch chat sessions in bulk
            $chat_sessions = DB::table('chat_sessions')
                ->whereIn('client_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Fetch chat session messages in bulk
            $chat_session_ids = $chat_sessions->pluck('id')->toArray();
            $chat_sessions_messages = DB::table('chat_session_messages')
                ->whereIn('chat_session_id', $chat_session_ids)
                ->whereNotIn('message', $defolt_message)
                ->get();

            // Group messages by session ID
            $messagesGroupedBySession = $chat_sessions_messages->groupBy('chat_session_id');

            foreach ($chat_sessions as $chat_session) {
                $advisorMessages = $messagesGroupedBySession->get($chat_session->id, collect())
                    ->where('sender_id', $chat_session->advisor_id);

                if ($advisorMessages->count() == 0) {
                    $counts[$category]++;
                }
            }
        }

        // Define date ranges
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (Monday)
        $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (Sunday)
        $currentYear = Carbon::now()->year;
        $startOfFinancialYear = Carbon::createFromDate($currentYear, 4, 1); // Assuming financial year starts in April
        $endOfFinancialYear = Carbon::createFromDate($currentYear + 1, 3, 31); // Ends in March next year

        // Count non-responders for different periods
        $defolt_message = [
            DB::table('settings')->where('key', 'admin.admin_send_seven_default_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_transfer_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.admin_sunday_default_message')->value('value')
        ];

        countNonResponders($fe_users, $today, $today, $counts, 'today_fe', $defolt_message);
        countNonResponders($iv_users, $today, $today, $counts, 'today_iv', $defolt_message);

        countNonResponders($fe_users, $startOfWeek, $endOfWeek, $counts, 'week_fe', $defolt_message);
        countNonResponders($iv_users, $startOfWeek, $endOfWeek, $counts, 'week_iv', $defolt_message);

        countNonResponders($fe_users, $startOfMonth, $endOfMonth, $counts, 'month_fe', $defolt_message);
        countNonResponders($iv_users, $startOfMonth, $endOfMonth, $counts, 'month_iv', $defolt_message);

        countNonResponders($fe_users, $startOfFinancialYear, $endOfFinancialYear, $counts, 'fiscal_fe', $defolt_message);
        countNonResponders($iv_users, $startOfFinancialYear, $endOfFinancialYear, $counts, 'fiscal_iv', $defolt_message);

        countNonResponders($fe_users, Carbon::minValue(), Carbon::maxValue(), $counts, 'overall_fe', $defolt_message);
        countNonResponders($iv_users, Carbon::minValue(), Carbon::maxValue(), $counts, 'overall_iv', $defolt_message);

        // Output counts

        //        answed chat by advisor

        $counts_2 = [
            'today_fe' => 0,
            'today_iv' => 0,
            'week_fe' => 0,
            'week_iv' => 0,
            'month_fe' => 0,
            'month_iv' => 0,
            'fiscal_fe' => 0,
            'fiscal_iv' => 0,
            'overall_fe' => 0,
            'overall_iv' => 0
        ];
        // Helper function to count responders (answered chats)
        function countResponders($userIds, $startDate, $endDate, &$counts_2, $category, $default_message)
        {
            // Fetch chat sessions in bulk
            $chat_sessions = DB::table('chat_sessions')
                ->whereIn('client_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Fetch chat session messages in bulk
            $chat_session_ids = $chat_sessions->pluck('id')->toArray();
            $chat_sessions_messages = DB::table('chat_session_messages')
                ->whereIn('chat_session_id', $chat_session_ids)
                ->whereNotIn('message', $default_message) // Exclude default messages
                ->get();

            // Group messages by session ID
            $messagesGroupedBySession = $chat_sessions_messages->groupBy('chat_session_id');

            foreach ($chat_sessions as $chat_session) {
                // Check if there is at least one message from the advisor in the session
                $advisorMessages = $messagesGroupedBySession->get($chat_session->id, collect())
                    ->where('sender_id', $chat_session->advisor_id);

                if ($advisorMessages->count() > 0) { // Count sessions where advisor has responded
                    $counts_2[$category]++;
                }
            }
        }

        // Define date ranges (unchanged)
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $currentYear = Carbon::now()->year;
        $startOfFinancialYear = Carbon::createFromDate($currentYear, 4, 1);
        $endOfFinancialYear = Carbon::createFromDate($currentYear + 1, 3, 31);

        $default_message = [
            DB::table('settings')->where('key', 'admin.admin_send_seven_default_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_transfer_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.admin_sunday_default_message')->value('value')
        ];

        // Count responders (answered chats) for different periods
        countResponders($fe_users, $today, $today, $counts_2, 'today_fe', $default_message);
        countResponders($iv_users, $today, $today, $counts_2, 'today_iv', $default_message);

        countResponders($fe_users, $startOfWeek, $endOfWeek, $counts_2, 'week_fe', $default_message);
        countResponders($iv_users, $startOfWeek, $endOfWeek, $counts_2, 'week_iv', $default_message);

        countResponders($fe_users, $startOfMonth, $endOfMonth, $counts_2, 'month_fe', $default_message);
        countResponders($iv_users, $startOfMonth, $endOfMonth, $counts_2, 'month_iv', $default_message);

        countResponders($fe_users, $startOfFinancialYear, $endOfFinancialYear, $counts_2, 'fiscal_fe', $default_message);
        countResponders($iv_users, $startOfFinancialYear, $endOfFinancialYear, $counts_2, 'fiscal_iv', $default_message);

        countResponders($fe_users, Carbon::minValue(), Carbon::maxValue(), $counts_2, 'overall_fe', $default_message);
        countResponders($iv_users, Carbon::minValue(), Carbon::maxValue(), $counts_2, 'overall_iv', $default_message);

        //
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisFiscalYear = Carbon::createFromDate(Carbon::now()->year - 1, 4, 1); // Example fiscal year start

        // Create an array to store counts
        $counts_3 = [];

        // Get counts for FE (user_category_id = 1, role_id = 6)
        $counts_3['today_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 6); // Role 6
        })->whereDate('created_at', $today)->count();

        $counts_3['week_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisWeek, Carbon::now()])->count();

        $counts_3['month_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisMonth, Carbon::now()])->count();

        $counts_3['fiscal_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisFiscalYear, Carbon::now()])->count();

        $counts_3['overall_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 6); // Role 6
        })->count();

        // Get counts for IV (user_category = 2, role_id = 6)
        $counts_3['today_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 6); // Role 6
        })->whereDate('created_at', $today)->count();

        $counts_3['week_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisWeek, Carbon::now()])->count();

        $counts_3['month_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisMonth, Carbon::now()])->count();

        $counts_3['fiscal_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 6); // Role 6
        })->whereBetween('created_at', [$thisFiscalYear, Carbon::now()])->count();

        $counts_3['overall_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 6); // Role 6
        })->count();
        //dd($counts_3);

        // Create an array to store counts for role_id = 5 (counts_4)
        $counts_4 = [];

        // Get counts for FE (user_category_id = 1, role_id = 5)
        $counts_4['today_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 5); // Role 5
        })->whereDate('created_at', $today)->count();

        $counts_4['week_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisWeek, Carbon::now()])->count();

        $counts_4['month_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisMonth, Carbon::now()])->count();

        $counts_4['fiscal_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisFiscalYear, Carbon::now()])->count();

        $counts_4['overall_fe'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 1) // FE
                ->where('role_id', 5); // Role 5
        })->count();

        // Get counts for IV (user_category = 2, role_id = 5)
        $counts_4['today_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 5); // Role 5
        })->whereDate('created_at', $today)->count();

        $counts_4['week_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisWeek, Carbon::now()])->count();

        $counts_4['month_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisMonth, Carbon::now()])->count();

        $counts_4['fiscal_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 5); // Role 5
        })->whereBetween('created_at', [$thisFiscalYear, Carbon::now()])->count();

        $counts_4['overall_iv'] = ChatSessionMessage::whereHas('sender', function ($query) {
            $query->where('user_category', 2) // IV
                ->where('role_id', 5); // Role 5
        })->count();
        // How many unanswered chats transferred to DM
        $endOfMonth = Carbon::now()->endOfMonth();
        $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (Sunday)

        // Define base query to get sessions where role_id is 6
        $baseQuery = DB::table('chat_sessions')
            ->join('users', 'chat_sessions.advisor_id', '=', 'users.id')
            ->join('chat_session_messages', 'chat_sessions.id', '=', 'chat_session_messages.chat_session_id')
            ->where('users.role_id', 6);

        // Get count for Category 1 and 2 for different time periods

        // Today
        $todayCategory1Count = $baseQuery->clone()
            ->where('users.user_category', 1)
            ->whereDate('chat_session_messages.created_at', $today)
            ->count();

        $todayCategory2Count = $baseQuery->clone()
            ->where('users.user_category', 2)
            ->whereDate('chat_session_messages.created_at', $today)
            ->count();

        // This Week
        $weekCategory1Count = $baseQuery->clone()
            ->where('users.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfWeek, $endOfWeek])
            ->count();

        $weekCategory2Count = $baseQuery->clone()
            ->where('users.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfWeek, $endOfWeek])
            ->count();

        // This Month
        $monthCategory1Count = $baseQuery->clone()
            ->where('users.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $monthCategory2Count = $baseQuery->clone()
            ->where('users.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // This Financial Year
        $financialYearCategory1Count = $baseQuery->clone()
            ->where('users.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        $financialYearCategory2Count = $baseQuery->clone()
            ->where('users.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        // Get overall counts for Category 1 and Category 2

        $overallCategory1Count = $baseQuery->clone()
            ->where('users.user_category', 1)
            ->count();

        $overallCategory2Count = $baseQuery->clone()
            ->where('users.user_category', 2)
            ->count();

        // How many unanswered chats transferred to DM
//        How many chats unanswered by DM,
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $currentYear = Carbon::now()->year;
        $startOfFinancialYear = Carbon::createFromDate($currentYear, 4, 1); // Assuming financial year starts in April
        $endOfFinancialYear = Carbon::createFromDate($currentYear + 1, 3, 31);

        $defaultMessages = [
            DB::table('settings')->where('key', 'admin.admin_send_seven_default_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_transfer_message')->value('value'),
            DB::table('settings')->where('key', 'admin.chat_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_message')->value('value'),
            DB::table('settings')->where('key', 'admin.help_session_opening_busy_message')->value('value'),
            DB::table('settings')->where('key', 'admin.admin_sunday_default_message')->value('value')
        ];

        // Base query for FE (category_id = 1) and IV (category_id = 2)
        $baseQuery_two = DB::table('chat_sessions')
            ->join('users as advisors', 'chat_sessions.advisor_id', '=', 'advisors.id')
            ->join('chat_session_messages', 'chat_sessions.id', '=', 'chat_session_messages.chat_session_id')
            ->join('users as senders', 'chat_session_messages.sender_id', '=', 'senders.id')
            ->where('advisors.role_id', 6) // Advisor's role is 6 (DM role)
            ->where('senders.role_id', 6)  // Sender's role is 6 (DM role)
            ->whereNotIn('chat_session_messages.message', $defaultMessages) // Exclude default messages
            ->distinct();

        // Count by time periods and categories (FE = category 1, IV = category 2)

        // Today FE
        $today_fe_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 1)
            ->whereDate('chat_session_messages.created_at', $today)
            ->count();

        // Today IV
        $today_iv_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 2)
            ->whereDate('chat_session_messages.created_at', $today)
            ->count();

        // This week FE
        $week_fe_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfWeek, $endOfWeek])
            ->count();

        // This week IV
        $week_iv_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfWeek, $endOfWeek])
            ->count();

        // This month FE
        $month_fe_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // This month IV
        $month_iv_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // This financial year FE
        $fiscal_fe_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 1)
            ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        // This financial year IV
        $fiscal_iv_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 2)
            ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->count();

        // Overall FE
        $overall_fe_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 1)
            ->count();

        // Overall IV
        $overall_iv_uabdm = $baseQuery_two->clone()
            ->where('advisors.user_category', 2)
            ->count();



        //download reports


        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (Monday)
        $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (Sunday)
        $currentYear = Carbon::now()->year;
        $startOfFinancialYear = Carbon::createFromDate($currentYear, 4, 1); // Assuming financial year starts in April
        $endOfFinancialYear = Carbon::createFromDate($currentYear + 1, 3, 31); // Ends in March next year

        $counts_5 = [
            'today_fe' => 0,
            'today_iv' => 0,
            'week_fe' => 0,
            'week_iv' => 0,
            'month_fe' => 0,
            'month_iv' => 0,
            'fiscal_fe' => 0,
            'fiscal_iv' => 0,
            'overall_fe' => 0,
            'overall_iv' => 0
        ];

        // Count for today
        $counts_5['today_fe'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 1)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereDate('uploaded_documents.updated_at', $today)
            ->distinct()
            ->count('users.id');

        $counts_5['today_iv'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 2)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereDate('uploaded_documents.updated_at', $today)
            ->distinct()
            ->count('users.id');

        // Count for this week
        $counts_5['week_fe'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 1)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfWeek, $endOfWeek])
            ->distinct()
            ->count('users.id');

        $counts_5['week_iv'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 2)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfWeek, $endOfWeek])
            ->distinct()
            ->count('users.id');

        // Count for this month
        $counts_5['month_fe'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 1)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfMonth, $endOfMonth])
            ->distinct()
            ->count('users.id');

        $counts_5['month_iv'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 2)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfMonth, $endOfMonth])
            ->distinct()
            ->count('users.id');

        // Count for the financial year
        $counts_5['fiscal_fe'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 1)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->distinct()
            ->count('users.id');

        $counts_5['fiscal_iv'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 2)
            ->whereNotNull('uploaded_documents.file_name')
            ->whereBetween('uploaded_documents.updated_at', [$startOfFinancialYear, $endOfFinancialYear])
            ->distinct()
            ->count('users.id');

        // Overall counts
        $counts_5['overall_fe'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 1)
            ->whereNotNull('uploaded_documents.file_name')
            ->distinct()
            ->count('users.id');

        $counts_5['overall_iv'] = User::join('uploaded_documents', 'users.id', '=', 'uploaded_documents.user_id')
            ->where('users.role_id', 2)
            ->where('users.user_category', 2)
            ->whereNotNull('uploaded_documents.file_name')
            ->distinct()
            ->count('users.id');

        $data = [
            [
                'description' => 'How many Clients have registered on Mobile App',
                'today_fe' => $today_fe['have_reg'], // Replace with dynamic data
                'today_iv' => $today_iv['have_reg'],
                'week_fe' => $thisWeek_fe['have_reg'],
                'week_iv' => $thisWeek_iv['have_reg'],
                'month_fe' => $dataThisMonth_fe['have_reg'],
                'month_iv' => $dataThisMonth_iv['have_reg'],
                'fiscal_fe' => $dataCurrentFinancialYear_fe['have_reg'],
                'fiscal_iv' => $dataCurrentFinancialYear_iv['have_reg'],
                'overall_fe' => $dataOverall_fe['have_reg'],
                'overall_iv' => $dataOverall_iv['have_reg'],
            ],

            [
                'description' => 'Chat initiated by Client',

                // Today's counts
                'today_fe' => ChatSessionMessage::whereIn('id', function ($query) use ($today) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 1) // FE (user_category = 1)
                        ->whereDate('chat_session_messages.created_at', $today)
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                'today_iv' => ChatSessionMessage::whereIn('id', function ($query) use ($today) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 2) // IV (user_category = 2)
                        ->whereDate('chat_session_messages.created_at', $today)
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                // This week's counts
                'week_fe' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfWeek) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 1) // FE
                        ->whereBetween('chat_session_messages.created_at', [$startOfWeek, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                'week_iv' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfWeek) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 2) // IV
                        ->whereBetween('chat_session_messages.created_at', [$startOfWeek, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                // This month's counts
                'month_fe' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfMonth) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 1) // FE
                        ->whereBetween('chat_session_messages.created_at', [$startOfMonth, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                'month_iv' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfMonth) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 2) // IV
                        ->whereBetween('chat_session_messages.created_at', [$startOfMonth, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                // Fiscal year counts
                'fiscal_fe' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfFinancialYear) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 1) // FE
                        ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                'fiscal_iv' => ChatSessionMessage::whereIn('id', function ($query) use ($startOfFinancialYear) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 2) // IV
                        ->whereBetween('chat_session_messages.created_at', [$startOfFinancialYear, Carbon::now()])
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                // Overall counts
                'overall_fe' => ChatSessionMessage::whereIn('id', function ($query) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 1) // FE
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

                'overall_iv' => ChatSessionMessage::whereIn('id', function ($query) {
                    $query->selectRaw('MIN(chat_session_messages.id)')
                        ->from('chat_session_messages')
                        ->join('users', 'chat_session_messages.sender_id', '=', 'users.id')
                        ->whereNotNull('chat_session_messages.chat_session_id')
                        ->where('chat_session_messages.type', 'incoming')
                        ->where('users.role_id', 2)
                        ->where('users.user_category', 2) // IV
                        ->groupBy('chat_session_messages.chat_session_id');
                })->count(),

            ],
            [
                'description' => 'Chat Initiated by Advisor',
                'today_fe' => $today_fe['adviser_start'],
                'today_iv' => $today_iv['adviser_start'],
                'week_fe' => $week_fe['adviser_start'],
                'week_iv' => $week_iv['adviser_start'],
                'month_fe' => $month_fe['adviser_start'],
                'month_iv' => $month_iv['adviser_start'],
                'fiscal_fe' => $fiscal_fe['adviser_start'],
                'fiscal_iv' => $fiscal_iv['adviser_start'],
                'overall_fe' => $overall_fe['adviser_start'],
                'overall_iv' => $overall_iv['adviser_start'],
            ],
            [
                'description' => 'Unanswered chats by advisor',
                'today_fe' => $counts['today_fe'],
                'today_iv' => $counts['today_iv'],
                'week_fe' => $counts['week_fe'],
                'week_iv' => $counts['week_iv'],
                'month_fe' => $counts['month_fe'],
                'month_iv' => $counts['month_iv'],
                'fiscal_fe' => $counts['fiscal_fe'],
                'fiscal_iv' => $counts['fiscal_iv'],
                'overall_fe' => $counts['overall_fe'],
                'overall_iv' => $counts['overall_iv'],
            ],
            [
                'description' => 'Answered chats by advisor',
                'today_fe' => $counts_2['today_fe'],
                'today_iv' => $counts_2['today_iv'],
                'week_fe' => $counts_2['week_fe'],
                'week_iv' => $counts_2['week_iv'],
                'month_fe' => $counts_2['month_fe'],
                'month_iv' => $counts_2['month_iv'],
                'fiscal_fe' => $counts_2['fiscal_fe'],
                'fiscal_iv' => $counts_2['fiscal_iv'],
                'overall_fe' => $counts_2['overall_fe'],
                'overall_iv' => $counts_2['overall_iv'],
            ],
            [
                'description' => 'How many chats answered by DM',
                'today_fe' => $counts_3['today_fe'],
                'today_iv' => $counts_3['today_iv'],
                'week_fe' => $counts_3['week_fe'],
                'week_iv' => $counts_3['week_iv'],
                'month_fe' => $counts_3['month_fe'],
                'month_iv' => $counts_3['month_iv'],
                'fiscal_fe' => $counts_3['fiscal_fe'],
                'fiscal_iv' => $counts_3['fiscal_iv'],
                'overall_fe' => $counts_3['overall_fe'],
                'overall_iv' => $counts_3['overall_iv'],
            ],
            [
                'description' => 'Answered chats by PRO',
                'today_fe' => $counts_4['today_fe'],
                'today_iv' => $counts_4['today_iv'],
                'week_fe' => $counts_4['week_fe'],
                'week_iv' => $counts_4['week_iv'],
                'month_fe' => $counts_4['month_fe'],
                'month_iv' => $counts_4['month_iv'],
                'fiscal_fe' => $counts_4['fiscal_fe'],
                'fiscal_iv' => $counts_4['fiscal_iv'],
                'overall_fe' => $counts_4['overall_fe'],
                'overall_iv' => $counts_4['overall_iv'],
            ],
            [
                'description' => 'How many unanswered chats transferred to DM',
                'today_fe' => $todayCategory1Count,
                'today_iv' => $todayCategory2Count,
                'week_fe' => $weekCategory1Count,
                'week_iv' => $weekCategory2Count,
                'month_fe' => $monthCategory1Count,
                'month_iv' => $monthCategory2Count,
                'fiscal_fe' => $financialYearCategory1Count,
                'fiscal_iv' => $financialYearCategory2Count,
                'overall_fe' => $overallCategory1Count,
                'overall_iv' => $overallCategory2Count,
            ],
            [
                'description' => 'How many chats unanswered by DM,',
                'today_fe' => $today_fe_uabdm,
                'today_iv' => $today_iv_uabdm,
                'week_fe' => $week_fe_uabdm,
                'week_iv' => $week_iv_uabdm,
                'month_fe' => $month_fe_uabdm,
                'month_iv' => $month_iv_uabdm,
                'fiscal_fe' => $fiscal_fe_uabdm,
                'fiscal_iv' => $fiscal_iv_uabdm,
                'overall_fe' => $overall_fe_uabdm,
                'overall_iv' => $overall_iv_uabdm,
            ],
            [
                'description' => 'How many Clients has uploaded Documents on App',
                'today_fe' => $counts_5['today_fe'],
                'today_iv' => $counts_5['today_iv'],
                'week_fe' => $counts_5['week_fe'],
                'week_iv' => $counts_5['week_iv'],
                'month_fe' => $counts_5['month_fe'],
                'month_iv' => $counts_5['month_iv'],
                'fiscal_fe' => $counts_5['fiscal_fe'],
                'fiscal_iv' => $counts_5['fiscal_iv'],
                'overall_fe' => $counts_5['overall_fe'],
                'overall_iv' => $counts_5['overall_iv'],
            ]
        ];

        return response()->json(['data' => $data]);
    }

    public function getUsersData(Request $request)
    {
        // dd($request->all());
        $query = User::query();
        $query->where('role_id', 4); // Assuming role_id 4 is for advisors

        // Apply filters
//        if ($request->start_date) {
//            $query->whereDate('created_at', '>=', $request->start_date);
//        }
//
//        if ($request->end_date) {
//            $query->whereDate('created_at', '<=', $request->end_date);
//        }

        if ($request->category_id) {
            $query->where('user_category', $request->category_id);
        }

        if ($request->user_id) {
            $query->where('reporting_to', $request->user_id);
        }


        // Get the list of advisors
//        $advisors = $query->get();


        return DataTables::of($query)->addColumn('advisor_name', function ($row) {
            return $row->name;
        })
            ->addColumn('done_count', function ($row) use ($request) {


                $adviser_data = User::where('id', $row->id)->first();

                $query = DB::table('users')
                    ->where('advisor_id', $adviser_data->advisor_user_id)
                    ->leftJoin('user_application_status', 'users.id', '=', 'user_application_status.user_id')
                    ->where('user_application_status.status_value', 'Done');
                // Add the application status filter if provided
                if ($request->get('application_status')) {
                    $query->where('user_application_status.application_status', $request->get('application_status'));
                }

                // Add the date filter if both start date and end date are provided
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('user_application_status.updated_at', [$request->start_date, $request->end_date]);
                }

                $count = $query->count();
                return $count;


            })
            ->addColumn('pending_count', function ($row) use ($request) {
                $query = DB::table('users')
                    ->where('advisor_id', $row->advisor_user_id)
                    ->leftJoin('user_application_status', 'users.id', '=', 'user_application_status.user_id')
                    ->where('user_application_status.status_value', 'Pending'); // Static filter for status value
    
                // Add the application status filter if provided
                if ($request->get('application_status')) {
                    $query->where('user_application_status.application_status', $request->get('application_status'));
                }

                // Add the date filter if both start date and end date are provided
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('user_application_status.updated_at', [$request->start_date, $request->end_date]);
                }

                $count = $query->count();

                return $count;
            })
            ->addColumn('na_count', function ($row) use ($request) {
                $query = DB::table('users')
                    ->where('advisor_id', $row->advisor_user_id)
                    ->leftJoin('user_application_status', 'users.id', '=', 'user_application_status.user_id')
                    ->where('user_application_status.status_value', 'N/A'); // Static filter for status value
    
                // Add the application status filter if provided
                if ($request->get('application_status')) {
                    $query->where('user_application_status.application_status', $request->get('application_status'));
                }

                // Add the date filter if both start date and end date are provided
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('user_application_status.updated_at', [$request->start_date, $request->end_date]);
                }

                // Count the records based on the dynamic filters
                $count = $query->count();

                return $count;
            })->addColumn('total_count', function ($row) use ($request) {
                $query = DB::table('users')
                    ->join('user_application_status', 'users.id', '=', 'user_application_status.user_id')
                    ->where('users.advisor_id', $row->advisor_user_id)
                    ->whereNotNull('user_application_status.status_value'); // Ensure status_value is not null
    
                // Add the application status filter if provided
                if ($request->get('application_status')) {
                    $query->where('user_application_status.application_status', $request->get('application_status'));
                }

                // Add the date filter if both start date and end date are provided
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('user_application_status.created_at', [$request->start_date, $request->end_date]);
                }

                // Execute the query and count the records
                $count = $query->count();

                return $count;

                // Add the application status filter if provided
                if ($request->get('application_status')) {
                    $query->where('user_application_status.application_status', $request->get('application_status'));
                }

                // Add the date filter if both start date and end date are provided
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('user_application_status.updated_at', [$request->start_date, $request->end_date]);
                }

                // Count the records based on the dynamic filters
                $count = $query->count();

                return $count;


            })
            ->make(true);
    }
    // In a suitable controller
    public function getFinancialYears()
    {
        $firstCreatedDate = DB::table('chat_sessions')->orderBy('created_at', 'asc')->value('created_at');
        $lastCreatedDate = DB::table('chat_sessions')->orderBy('created_at', 'desc')->value('created_at');

        $startYear = \Carbon\Carbon::parse($firstCreatedDate)->format('Y');
        $endYear = \Carbon\Carbon::parse($lastCreatedDate)->format('Y');

        $yearRanges = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearRanges[$year] = $year . '-' . ($year + 1);
        }

        return $yearRanges;
    }

    public function getUsers_data(Request $request)
    {
        $categoryId = $request->input('categoryId');

        $roles = $this->dyManager();
        $roleIds = array_column($roles, 'id');

        $roleNames = Role::whereIn('id', $roleIds)->pluck('name', 'id')->toArray();


        $roleArray = [
            1 => "admin",
            2 => "user",
            3 => "Super Admin",
            4 => "advisor",
            5 => "pro",
            6 => "Dy Manager",
            7 => "Backend",
            8 => "Head_operation_IV",
            9 => "Head_Operation_FE",
            10 => "Quality Check",
            11 => "audit",
            12 => "Application Advisor (FE)",
            13 => "Visa Advisor (FE)",
            14 => "manager",
        ];


        $matchedRoleIds = [];


        foreach ($roleNames as $id => $name) {
            $roleKey = array_search($name, $roleArray);
            if ($roleKey !== false) {
                $matchedRoleIds[] = $roleKey;
            }
        }
        if (empty($matchedRoleIds)) {
            return response()->json(['message' => 'No valid roles found'], 404);
        }

        $users = User::where('user_category', $categoryId)
            ->whereIn('role_id', $matchedRoleIds)
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json($users);
    }


    public function getApplicationStatuses(Request $request)
    {
        $categoryId = $request->input('categoryId'); // Get category ID from request

        // Your logic to fetch application statuses goes here
        $statuses = ApplicationStatuses::where('category_id', $categoryId)->get();

        if ($statuses->isEmpty()) {
            return response()->json(['message' => 'No statuses found'], 404);
        }

        return response()->json($statuses);
    }

    public function fullAccessRoles(): array
    {
        $roles_id = json_decode(ManageRoleSettings::pluck('full_access')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            $roleArray[] = ['id' => $id, 'name' => $roleName]; // Store both ID and name
        }

        return $roleArray; // Returns an array of arrays with ID and name
    }

    public function dyManager()
    {
        $roles_id = json_decode(ManageRoleSettings::pluck('dymanager_manager')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            $roleArray[] = ['id' => $id, 'name' => $roleName]; // Store both ID and name
        }

        return $roleArray; // Returns an array of arrays with ID and name
    }
    public function getSettings()
    {
        return ManageRoleSettings::first();
    }

    public function pearoRole()
    {
        $roleName = Role::where('id', $this->getSettings()->pearo)->pluck('name')->first();
        return $roleName;
    }




}
