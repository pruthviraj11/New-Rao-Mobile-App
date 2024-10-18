<?php

namespace App\Http\Controllers;

use App\Mail\DynamicEmail;
use App\Models\EmailTemplate;
use App\Models\Role;
use App\Models\ManageRoleSettings;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Models\SMSTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    private $importedCount = 0;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function gst(){
        return ManageRoleSettings::pluck('gst')->first();
    }
    public function tds(){
        return ManageRoleSettings::pluck('tds')->first();
    }
    public function getSettings()
    {
        return ManageRoleSettings::first();
    }
    public function advisorRole()
    {
        $roles_id = json_decode(ManageRoleSettings::pluck('adviser')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            array_push($roleArray, $roleName);
        }
        return $roleArray;
    }
    public function sendDynamicEmail($type = '', $inquiry_ids = [],  $other = [] ){
        $documentList = '';
        if ($type == 'document_reminder' || $type == 'document_list'){
            $documentList = '<ul>';
            foreach ($other['document'] as $doc){
                $documentList .= '<li>'.$doc->name.'</li>';
            }
            $documentList = '</ul>';
        }
        $file = '';
        $amount_paid = '';
        if ($type == 'send_receipt'){
            $receiptData = $other['paymentReceipt'];
            $amount_paid = $receiptData->amount_paid;
            $file = storage_path('app/public/'.$receiptData->receipt_name);
        }
        $setting = ManageRoleSettings::pluck($type)->first();
        if ($setting){
            foreach ($inquiry_ids as $inquiry_id){
                $inquiry = Inquiry::where('id', $inquiry_id)->first();
                $emailTemplate = EmailTemplate::where('id', $setting)->first();
                $template = $emailTemplate->html;
                $template = str_replace('{first_name}', $inquiry->first_name, $template);
                $template = str_replace('{last_name}', $inquiry->last_name, $template);
                $template = str_replace('{full_name}', $inquiry->first_name.' '.$inquiry->last_name, $template);
                $template = str_replace('{email}', $inquiry->email, $template);
                $template = str_replace('{mobile_no}', $inquiry->mobile_one, $template);
                $template = str_replace('{inquiry_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{registration_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{document_list}', $documentList, $template);
                $template = str_replace('{amount_paid}', $amount_paid, $template);

                Mail::to($inquiry->email)->send(new DynamicEmail($emailTemplate->subject, $template, $file));
            }
        }
    }

    public function sendDynamicSMS($type = '', $inquiry_id = '',  $other = [] ){

        try {
            $amount_paid = '';
            if ($type == 'send_receipt'){
                $receiptData = $other['paymentReceipt'];
                $amount_paid = $receiptData->amount_paid;
            }
            $setting = ManageRoleSettings::pluck($type)->first();

            if ($setting){

                $inquiry = Inquiry::where('id', $inquiry_id)->first();
                $emailTemplate = SMSTemplate::where('id', $setting)->first();
                $template = $emailTemplate->message;
                $template = str_replace('{first_name}', $inquiry->first_name, $template);
                $template = str_replace('{last_name}', $inquiry->last_name, $template);
                $template = str_replace('{full_name}', $inquiry->first_name.' '.$inquiry->last_name, $template);
                $template = str_replace('{email}', $inquiry->email, $template);
                $template = str_replace('{mobile_no}', $inquiry->mobile_one, $template);
                $template = str_replace('{inquiry_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{registration_no}', $inquiry->inquiry_no, $template);
                $template = str_replace('{amount_paid}', $amount_paid, $template);

                sendSMS($template, $inquiry->mobile_one);
            }
        }catch (\Exception $error){
            dd($error->getMessage());
        }
    }
    public function sendPushNotification($fcm_token, $title, $message, $data)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';


        $serverKey = env("SERVER_API_KEY");


        $data = [
            "message" => [
                "token" => $fcm_token,
                "notification" => [
                    "title" => $title,
                    "body" => $message,
                ],
                "data" => $data,
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "default"
                        ]
                    ]
                ],
                "android" => [
                    "priority" => "high",
                ]
            ]
        ];

        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        \Log::info(json_encode($ch));
        // Execute post
        $result = curl_exec($ch);
        \Log::info("In Pusher");

        \Log::info($result);

        if ($result === FALSE) {
            \Log::info(curl_error($ch));
            //        die('Curl failed: ' . curl_error($ch));
            \Log::info('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
