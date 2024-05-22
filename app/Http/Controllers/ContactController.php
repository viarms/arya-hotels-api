<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'time' => 'required',
            'email' => 'required|email',
        ]);

        $data = $request->all();

        $contact = Contact::create($data);

        // $this->submitLead($contact->id);
        // $this->submitZappier($contact->id, $data);

        return response()->json(['status' => 'success']);
    }

    public function getAll(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            return response()->json([]);
        }

        return response()->json(Contact::orderBy('id', 'desc')->get());
    }

    public function markAsDone(Request $request) : JsonResponse
    {
        if (!$request->user('sanctum')) {
            throw new \Exception('Not allowed');
        }

        $contact = Contact::find((int) $request->id);
        $contact->update([
            'is_done' => 1,
        ]);

        return response()->json(['status' => 'success']);
    }

    private function submitZappier(int $id, array $data) : void
    {
        $data['id'] = $id;
        $data = \http_build_query($data);

        $ch = curl_init('https://hooks.zapier.com/hooks/catch/18259982/30xrgoy/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
    }

    private function submitLead(int $id) : void
    {
        $data = [
            'data' => [
                [
                    'event_name' => 'LeadSubmitted',
                    'event_time' => time(),
                    'action_source' => 'website',
                    'user_data' => [
                        'subscription_id' => $id,
                        'external_id' => [
                            $id
                        ],
                    ],
                ],
            ],
            'access_token' => 'EAAKFVLadIncBOwBPlLlqkd1hiPKT46wpKCT2YRZBoV5xElZA6ZC7kvM1hB9woZC92W885C3zdKWZBdUOva1XdWgJAqLGuZCIqPAGxeke4TjeO4f4mTneDWjlMbW2hn888ANTMZCfn8dF26mvCOMPHrZAN27De4YMuGjqfcbXgZA7PZBKFYaqDJ6JSzZBj24m8h1sPjoNgZDZD'
        ];

        $dataString = json_encode($data);
        $ch = curl_init('https://graph.facebook.com/v19.0/1096139928254538/events');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataString))
        );
        curl_exec($ch);
    }
}
