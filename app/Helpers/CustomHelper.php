<?php

use App\Mail\DynamicEmail;
use App\Models\Activity;
use App\Models\ActivityLogger;
use App\Models\Branch;
use App\Models\Checklist;
use App\Models\Document;
use App\Models\DocumentActionStatus;
use App\Models\EmailTemplate;
use App\Models\FollowUp;
use App\Models\Inquiry;
use App\Models\InquiryDocument;
use App\Models\InquiryPayment;
use App\Models\InquiryStatus;
use App\Models\InternalNotifications;
use App\Models\Package;
use App\Models\ProcessComment;
use App\Models\Product;
use App\Models\QcComment;
use App\Models\Role;
use App\Models\Setting;
use App\Models\TravelHistory;
use App\Models\User;
use App\Models\VisaType;
use App\Models\Community;
use App\Models\Workshop;
use App\Models\InquiryTransferRequest;
use App\Models\ApplicationData;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Jobs\SendEmail;

// use Exception;


function sendSMS($template = '', $mobile_no = [])
{
    try {
        $response = Http::withHeaders(['Cache-Control' => 'no-cache'])
            ->get('http://msg.icloudsms.com/rest/services/sendSMS/sendGroupSms', [
                'AUTH_KEY' => env('SMS_GATEWAY_AUTH_KEY'),
                'message' => $template,
                'senderId' => 'RAOCON',
                'routeId' => '3',
                'mobileNos' => $mobile_no,
                'smsContentType' => 'english',
            ]);

        if ($response->successful()) {
            dd("success", $response->body());
            echo $response->body();
        } else {
            dd("error");
            // Handle the error response here
            echo 'HTTP Error: ' . $response->status() . ' ' . $response->body();
        }
    } catch (\Exception $ex) {
        // Handle exceptions, if any
        dd("exception");
        echo 'Exception: ' . $ex->getMessage();
        die;
    }
}

function getSettings()
{
    return Setting::first();
}

function registrationStatusId()
{
    return Setting::pluck('registration_status')->first();
}

function documentStatusId()
{
    return Setting::pluck('documentation_status')->first();
}

function filePreparationStatusId()
{
    return Setting::pluck('file_preparation')->first();
}

function qcStatusId()
{
    return Setting::pluck('qc_status_id')->first();
}

function getAllConsultant()
{
    return User::role(array('Consultant'))->leftJoin('branches', 'users.branch', '=', 'branches.id')->select('users.id', 'users.first_name', 'users.last_name', 'branches.branch_code')->get();
}

function submissionStatusId()
{
    return Setting::pluck('submission_status_id')->first();
}

function refusalStatusId()
{
    return Setting::pluck('refusal_status_id')->first();
}

function cancelStatusId()
{
    return Setting::pluck('cancel_status_id')->first();
}

function proDiscussionStatusID()
{
    return Setting::pluck('pro_discussion')->first();
}

function cancelWithRefundStatusId()
{
    return Setting::pluck('cancel_refund_status_id')->first();
}

function fullListAccessRoles()
{
    $roles_id = json_decode(Setting::pluck('full_inquiry_access_roles')->first());
    $roleArray = array();

    foreach ($roles_id as $id) {
        $roleName = Role::where('id', $id)->pluck('name')->first();
        array_push($roleArray, $roleName);
    }
    return $roleArray;
}

function accountRole()
{
    $roleName = Role::where('id', getSettings()->account_role)->pluck('name')->first();
    return $roleName;
}

function userRole()
{
    return auth()->user()->getRoleNames()[0];
}

function isUserConsultant()
{
    return auth()->user()->getRoleNames()[0] == 'Consultant' ? true : false;
}

function reportToListAccessRoles()
{
    $roles_id = json_decode(Setting::pluck('report_to_inquiry_access_roles')->first());
    $roleArray = array();

    foreach ($roles_id as $id) {
        $roleName = Role::where('id', $id)->pluck('name')->first();
        array_push($roleArray, $roleName);
    }
    return $roleArray;
}


function getDescendants($userId)
{
    $descendants = User::where('reporting_to', $userId)->where('role_id', "!=", 2)->pluck('id');

    $allDescendants = collect($descendants);

    foreach ($descendants as $descendant) {
        $allDescendants = $allDescendants->merge(getDescendants($descendant));
    }

    return $allDescendants;
}




function getTimeAgo($created_at)
{
    // Parse the created_at timestamp using Carbon
    $createdTime = Carbon::parse($created_at);

    // Calculate the difference in minutes
    $minutesDifference = $createdTime->diffInMinutes(now());

    // Determine the appropriate time unit (minutes, hours, days, etc.)
    if ($minutesDifference < 60) {
        // Less than 60 minutes ago
        $timeAgo = $minutesDifference . ' minutes ago';
    } elseif ($minutesDifference < 1440) {
        // Less than 24 hours ago
        $hoursDifference = $createdTime->diffInHours(now());
        $timeAgo = $hoursDifference . ' hours ago';
    } else {
        // More than 30 days ago, return the actual date

        $timeAgo = $createdTime->format('d-m-Y h:i A');
    }

    return $timeAgo;
}



function sendDynamicEmail(string $title = '', string $type = '', array $inquiry_ids = [], array $other = [])
{

    $file = '';
    $trainer_name = '';
    $workshop_date_time = '';
    $mode_of_workshop = '';
    $callToAction = '';

    if ($type == 'workshop_feedback_mail') {
        $workshopId = $other['workshop']->id;
        $feedBackURL = route('workshop_feedback', encrypt($workshopId));
        Log::info('URL: ' . $feedBackURL);
        $callToAction = "<a target='_blank' href='{$feedBackURL}' style=' display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;'>Submit Feedback</a>";
        $trainer_name = $other['workshop']->trainer_name;
    } elseif ($type == 'scheduled_workshop_email') {
        $trainer_name = $other['workshop']->trainer_name;
        $workshop_date_time = $other['workshop']->scheduled_time;
        $mode_of_workshop = ucfirst($other['workshop']->mode_of_workshop);
    } elseif ($type == 'cv_feedback_mail') {
        $cvAssistanceId = $other['cv_assistance']->id;
        $feedBackURL = route('cvFeedback', encrypt($cvAssistanceId));
        $callToAction = "<a target='_blank' href='{$feedBackURL}' style=' display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;'>Submit Feedback</a>";
    } elseif ($type == 'application_feedback_mail') {
        $applicationId = $other['application']->id;
        $feedBackURL = route('app.feedback', encrypt($applicationId));
        $callToAction = "<a target='_blank' href='{$feedBackURL}' style=' display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;'>Submit Feedback</a>";
    }


}

function workshopCaseManagerRole()
{
    $roleName = Role::where('id', getSettings()->role_for_workshop)->pluck('name')->first();
    return $roleName;
}

function jsCaseManagerRole()
{
    $roleName = Role::where('id', getSettings()->role_for_cv_assistance)->pluck('name')->first();
    return $roleName;
}

function applicationManagerRole()
{
    $roleName = Role::where('id', getSettings()->role_for_application)->pluck('name')->first();
    return $roleName;
}



/**
 * Write code on Method
 *
 * @return response()
 */
function getDisableNeedWorkshop($inq)
{
    $disabledWorkshops = array();

    if (!empty($inq->workshops)) {

        foreach ($inq->workshops as $key => $value) {
            if ($value->need_workshop == 0) {
                $disabledWorkshops[] = $value->workshop_no;
            }
        }

        if (empty($disabledWorkshops)) {
            return array(1);
        }

    }

    return $disabledWorkshops;

}

/**
 * Write code on Method
 *
 * @return response()
 */
function getCommunities($ids)
{
    return Community::whereIn('id', $ids)->get();
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getProgramSates($ids)
{
    return State::whereIn('id', $ids)->get();
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getFlags()
{
    $data = [
        '1' => 'province',
        '2' => 'community',
        '3' => 'gtp',
    ];

    return $data;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function countDates($companyDetail)
{
    $dateCounts = [];

    if (is_array($companyDetail)) {
        foreach ($companyDetail as $communityId => $entries) {
            $dateCounts[$communityId] = [];

            foreach ($entries as $entry) {
                if (is_array($entry) && isset($entry['date'])) {
                    $date = $entry['date'];
                    if (isset($dateCounts[$communityId][$date])) {
                        $dateCounts[$communityId][$date]++;
                    } else {
                        $dateCounts[$communityId][$date] = 1;
                    }
                }
            }
        }
    }

    return $dateCounts;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function countDatesOther($companyDetail)
{
    $dateCounts = [];

    if (is_array($companyDetail)) {
        foreach ($companyDetail as $communityId => $entries) {
            if (is_array($entries) && isset($entries['date'])) {
                $date = $entries['date'];
                if (isset($dateCounts[$date])) {
                    $dateCounts[$date]++;
                } else {
                    $dateCounts[$date] = 1;
                }
            }
        }
    }

    return $dateCounts;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function renderSelectDropdown($program, $val, $rnipProgram)
{
    $html = '';

    if (!is_null($rnipProgram)) {
        $applicationsRnipApply = json_decode($rnipProgram->apply, true);

        if ($program->id == $rnipProgram->program_id) {
            if (!empty($applicationsRnipApply)) {
                $found = false;
                foreach ($applicationsRnipApply as $applyRnkey => $apply) {
                    if ($applyRnkey == $val->id) {
                        $found = true;
                        $html .= '<select name="program[' . $program->id . '][apply][' . $val->id . '][is_apply]" id="is_apply" class="is_apply form-control mt-1">';
                        // $html .= '<option value="">Select</option>';
                        $html .= '<option value="0"' . (!is_null($apply['is_apply']) && $apply['is_apply'] == 0 ? ' selected' : '') . '>Not Apply</option>';
                        $html .= '<option value="1"' . (!is_null($apply['is_apply']) && $apply['is_apply'] == 1 ? ' selected' : '') . '>Apply</option>';
                        $html .= '</select>';
                    }
                }
                if (!$found) {
                    $html .= '<select name="program[' . $program->id . '][apply][' . $val->id . '][is_apply]" id="is_apply" class="is_apply form-control mt-1">';
                    // $html .= '<option value="">Select</option>';
                    $html .= '<option value="0" selected>Not Apply</option>';
                    $html .= '<option value="1">Apply</option>';
                    $html .= '</select>';
                }
            } else {
                $html .= '<select name="program[' . $program->id . '][apply][' . $val->id . '][is_apply]" id="is_apply" class="is_apply form-control mt-1">';
                // $html .= '<option value="">Select</option>';
                $html .= '<option value="0" selected>Not Apply</option>';
                $html .= '<option value="1">Apply</option>';
                $html .= '</select>';
            }
        }
    } else {
        $html .= '<select name="program[' . $program->id . '][apply][' . $val->id . '][is_apply]" id="is_apply" class="is_apply form-control mt-1">';
        // $html .= '<option value="">Select</option>';
        $html .= '<option value="0" selected>Not Apply</option>';
        $html .= '<option value="1">Apply</option>';
        $html .= '</select>';
    }

    return $html;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getInquiryStatus()
{
    return InquiryStatus::orderByRaw('`order` IS NULL, `order`')->get();
}

/**
 * Write code on Method
 *
 * @return response()
 */
function logWorkshopAction($workshop, $inquiry, $actionType)
{
    switch ($actionType) {
        case 'skip':
            activity()->causedBy(auth()->user())->inquiryId($workshop->inquiry_id)->actionName('inquiry_transfer')->log("Workshop {$workshop->workshop_no} Skipped, Inquiry {$inquiry->inquiry_no}.");
            break;
        case 'complete':
            activity()->causedBy(auth()->user())->inquiryId($workshop->inquiry_id)->actionName('inquiry_transfer')->log("Workshop {$workshop->workshop_no} Completed, Inquiry {$inquiry->inquiry_no}.");
            break;
        case 'enable':
            activity()->causedBy(auth()->user())->inquiryId($workshop->inquiry_id)->actionName('inquiry_transfer')->log("Workshop {$workshop->workshop_no} Enabled, Inquiry {$inquiry->inquiry_no}.");
            break;
        default:
            // Handle default case if necessary
            break;
    }
}

/**
 * Write code on Method
 *
 * @return response()
 */
function makeArray($value1, $value2)
{

    $yourArray = [
        'noc_1_confirmed' => $value1,
        'noc_2_confirmed' => $value2,
    ];

    return $yourArray;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function countNameOccurrences($mergedArray)
{
    $count = 0;

    foreach ($mergedArray as $key => $value) {
        if (is_array($value)) {
            $count += countNameOccurrences($value);
        } elseif ($key == 'name') {
            $count++;
        }
    }

    return $count;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getCurrentStatus()
{
    $inquiryStage = [
        [
            'id' => 1,
            'stage' => 'Active Client'
        ],
        [
            'id' => 2,
            'stage' => 'Service Completed'
        ],
        [
            'id' => 3,
            'stage' => 'Cancelled with Refund'
        ],
        [
            'id' => 4,
            'stage' => 'Cancelled Without Refund'
        ],
        [
            'id' => 5,
            'stage' => 'Hold Client Due to pending Payment'
        ],
        [
            'id' => 6,
            'stage' => 'Hold Client Due to Other Reason'
        ],
        [
            'id' => 7,
            'stage' => 'Total Hold Client'
        ],
        [
            'id' => 8,
            'stage' => 'Registration'
        ],
        [
            'id' => 9,
            'stage' => 'Canada'
        ],
        [
            'id' => 10,
            'stage' => 'Combo'
        ],
    ];

    return $inquiryStage;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getAppDoneStatus()
{
    $inquiryStage = [
        [
            'id' => 1,
            'stage' => 'Application'
        ]
    ];

    return $inquiryStage;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function mergeAndCount($array)
{
    $result = [];

    foreach ($array as $subArray) {
        foreach ($subArray as $date => $count) {
            if (!isset($result[$date])) {
                $result[$date] = 0;
            }
            $result[$date] += $count;
        }
    }

    return $result;
}

/**
 * Write code on Method
 *
 * @return response()
 */
function getAppDoneCount($user, $type)
{
    if ($type == 1) {
        $inquiries = $user->inquiries()->select('inquiries.*', 'users.*')
            ->where('inquiries.inquiry_stage', 11)
            ->count();
        return $inquiries;
    } elseif ($type == 2) {
        $applicationData = $user->inquiries()->leftJoin('application_data', 'inquiries.id', '=', 'application_data.inquiry_id')
            ->select('application_data.*')
            ->where('inquiries.agent_id', $user->id)
            ->get();

        $totalCount = 0;
        $mergedArray = [];

        if (!empty($applicationData)) {
            foreach ($applicationData as $value) {
                $companyDetail = json_decode($value->company_detail, true);

                if (!is_null($companyDetail)) {
                    $mergedArray = array_merge_recursive($mergedArray, $companyDetail);
                }
            }
        }

        $totalCount = countNameOccurrences($mergedArray);
        return $totalCount;
    } elseif ($type == 3) {
        $applicationData = $user->inquiries()->leftJoin('application_data', 'inquiries.id', '=', 'application_data.inquiry_id')
            ->select('application_data.*', 'inquiries.id')
            ->where('inquiries.agent_id', $user->id)
            ->get();

        $clientsCompleted25 = [];

        if (!empty($applicationData)) {
            foreach ($applicationData as $value) {
                $companyDetail = json_decode($value->company_detail, true);

                if (!is_null($companyDetail)) {
                    $completedApplications = countNameOccurrences($companyDetail);

                    if ($completedApplications >= 25) {
                        $clientsCompleted25[] = $value->inquiry_id;
                    }
                }
            }
        }

        $totalCount = count(array_unique($clientsCompleted25));
        return $totalCount;

    } elseif ($type == 4) {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        $inquiries = $user->inquiries()
            ->leftJoin('application_data', 'inquiries.id', '=', 'application_data.inquiry_id')
            ->select('application_data.*')
            ->where('inquiries.agent_id', $user->id)
            ->get();

        $monthCount = 0;

        foreach ($inquiries as $key => $value) {
            $companyDetail = json_decode($value->company_detail, true);

            if ($companyDetail != null) {
                foreach ($companyDetail as $outerKey => $outerValue) {
                    foreach ($outerValue as $innerKey => $innerValue) {
                        if (isset($innerValue['date'])) {
                            if ($innerValue['date'] >= $startDate && $innerValue['date'] <= $endDate) {
                                $monthCount++;
                            }
                        }
                    }
                }
            }
        }

        return $monthCount;
    } elseif ($type == 5) {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $inquiries = $user->inquiries()->leftJoin('application_data', 'inquiries.id', '=', 'application_data.inquiry_id')
            ->select('application_data.*', 'inquiries.*', 'application_data.id as app_id')
            ->where('inquiries.agent_id', $user->id)
            ->get();

        $clientsCount = 0;

        $tmpCount = [];
        foreach ($inquiries as $key => $value) {
            $companyDetail = json_decode($value->company_detail, true);
            $applicationCount = 0;
            $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] ?? 0;

            if ($companyDetail != null) {
                foreach ($companyDetail as $outerKey => $outerValue) {
                    if (isset($outerValue['date'])) {
                        $applicationDate = $outerValue['date'];
                        if (Carbon::parse($applicationDate)->between($startDate, $endDate)) {
                            $applicationDate++;
                            $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] + 1;
                        }
                    } else {
                        foreach ($outerValue as $innerKey => $innerValue) {
                            if (isset($innerValue['date'])) {
                                $applicationDate = $innerValue['date'];
                                if (Carbon::parse($applicationDate)->between($startDate, $endDate)) {
                                    $applicationDate++;
                                    $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] + 1;
                                }
                            }
                        }
                    }
                }

                if ($applicationDate >= 25) {
                    $clientsCount++;
                }
            }
        }

        $count = collect($tmpCount)->filter(function ($value, $key) {
            return $value >= 25;
        })->count();

        return $count;
    } elseif ($type == 6) {
        $startDate = now()->startOfYear();
        $endDate = now()->endOfYear();

        $inquiries = $user->inquiries()->leftJoin('application_data', 'inquiries.id', '=', 'application_data.inquiry_id')
            ->select('application_data.*', 'inquiries.*', 'application_data.id as app_id')
            ->where('inquiries.agent_id', $user->id)
            ->get();

        $clientsCount = 0;

        $tmpCount = [];
        foreach ($inquiries as $key => $value) {
            $companyDetail = json_decode($value->company_detail, true);
            $applicationCount = 0;
            $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] ?? 0;

            if ($companyDetail != null) {
                foreach ($companyDetail as $outerKey => $outerValue) {
                    if (isset($outerValue['date'])) {
                        $applicationDate = $outerValue['date'];
                        if (Carbon::parse($applicationDate)->between($startDate, $endDate)) {
                            $applicationDate++;
                            $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] + 1;
                        }
                    } else {
                        foreach ($outerValue as $innerKey => $innerValue) {
                            if (isset($innerValue['date'])) {
                                $applicationDate = $innerValue['date'];
                                if (Carbon::parse($applicationDate)->between($startDate, $endDate)) {
                                    $applicationDate++;
                                    $tmpCount[$value->inquiry_id] = $tmpCount[$value->inquiry_id] + 1;
                                }
                            }
                        }
                    }
                }

                if ($applicationDate >= 25) {
                    $clientsCount++;
                }
            }
        }

        $count = collect($tmpCount)->filter(function ($value, $key) {
            return $value >= 25;
        })->count();

        return $count;
    }

    return true;
}

function roleName($value)
{
    $role_id = Setting::where('role_config->role_name', $value)->select('role_config->role_id')->first();
    $role_name = role::where('id', $role_id)->pluck('name')->first();
    // dd($);
    return $role_name;
}
