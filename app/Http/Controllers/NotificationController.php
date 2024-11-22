<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\NotificationToBeatMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationOverdueMail;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function emailExpiredSales()
    {
        $todayw = (string) date('w');
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $threeDaysAfter = $now->addDays(3)->format('Y-m-d');
        $now = Carbon::now();
        $twoDaysAfter = $now->addDays(2)->format('Y-m-d');
        $now = Carbon::now();
        $oneDayAfter = $now->addDays(1)->format('Y-m-d');

        $clients = Client::whereNotNull("days_to_send_collections_notifications")
                            ->where('status', 1)
                            // ->where('production', 1)
                            ->get(['id', 'days_to_send_collections_notifications']);

        foreach ($clients as $client) {
            $days = (array) unserialize($client->days_to_send_collections_notifications);

            $containsToday = in_array($todayw, $days);

            if ($containsToday) {
                $toBeat = Sale::with('type_voucher', 'client', 'coin', 'customer', 'client.accountsBank', 'credito')
                        ->where("client_id", $client->id)
                        ->where('status_condition', '0')
                        ->wherenull('credit_note_id')
                        ->where('status',1)
                        ->where(function($query) use ($twoDaysAfter, $threeDaysAfter, $oneDayAfter) {
                            $query->where('expiration', $threeDaysAfter)
                                    ->orWhere('expiration', $twoDaysAfter)
                                    ->orWhere('expiration', $oneDayAfter);
                        })
                        ->get();

                foreach ($toBeat as $tb) {
                    $email = null;

                    if ($tb->customer->email == null) {
                        if ($tb->customer->secondary_email != null) {
                            $email = $tb->customer->secondary_email;
                        }
                    } else {
                        $email = $tb->customer->email;
                    }

                    if ($email != null) {
                        $pdf = $this->showPdfSale($tb->correlative, $tb->serialnumber, $tb->type_voucher->code, $tb->client);
                        $xml = $this->showXmlSale($tb->correlative, $tb->serialnumber, $tb->type_voucher->code, $tb->client);

                        Mail::to($email)->send(new NotificationToBeatMail($tb, $pdf, $xml, $tb->client));
                    }
                }
            }
        }        
    }

    public function OverdueSales()
    {
        $todayw = (string) date('w');


        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        $clients = Client::whereNotNull("days_to_send_collections_notifications")
                            ->where('status', 1)
                            // ->where('production', 1)
                            ->get(['id', 'days_to_send_collections_notifications']);

        foreach ($clients as $client) {
            $days = (array) unserialize($client->days_to_send_collections_notifications);

            $containsToday = in_array($todayw, $days);

            if ($containsToday) {
        
                $overdue = Sale::with('type_voucher', 'client', 'coin', 'customer', 'client.accountsBank', 'credito')
                                ->where('client_id', $client->id)
                                ->where('status',1)
                                ->where('status_condition', 0)
                                ->wherenull('credit_note_id')
                                ->where('expiration', '<',$today)
                                ->get();

                foreach ($overdue as $tb) {
                    $email = null;

                    if ($tb->customer->email == null) {
                        if ($tb->customer->secondary_email != null) {
                            $email = $tb->customer->secondary_email;
                        }
                    } else {
                        $email = $tb->customer->email;
                    }

                    if ($email != null) {
                        $pdf = $this->showPdfSale($tb->correlative, $tb->serialnumber, $tb->type_voucher->code, $tb->client);
                        $xml = $this->showXmlSale($tb->correlative, $tb->serialnumber, $tb->type_voucher->code, $tb->client);

                        Mail::to($email)->send(new NotificationOverdueMail($tb, $pdf, $xml, $tb->client));
                    }
                }
            }
        }
    }

    public function showPdfSale($correlative, $serial_number, $type_voucher, $client)
    {
        try {
            $folder_client = $client->document;
            $file = $serial_number . '-' . $correlative . '.pdf';
            $file_path = 'pdf/' . $folder_client . '/' . $file;
            $pdf = Storage::disk('public')->get($file_path);

            return $pdf;
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            return null;
        }
        
    }

    public function showXmlSale($correlative, $serial_number, $type_voucher, $client)
    {
        try {
            $folder_client = $client->document;
            $file = $folder_client . '-' . $type_voucher . '-' .$serial_number . '-' . $correlative . '.xml';
            $file_path = 'xml/' . $folder_client . '/' . $file;
            $xml = Storage::disk('public')->get($file_path);

            return $xml;
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            return null;
        }   
    }
}
