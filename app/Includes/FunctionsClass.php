<?php
    
namespace App\Includes;
use App\Database\FirestoreDB;

/*
*   This is a custom class that holds functions that are 
*   commonly used by multiple controllers
*
*/

class FunctionsClass
{
    public function getUserDetails() {

        $firestore = new FirestoreDB();

        $user = $firestore -> get_document('Responders', ['username', '=', session() -> get('username')]);
        $user_details = [];

        foreach ($user as $details) {
            $user_details = [
                    'avatarUrl' => $details['avatarUrl'],
                    'usertype' => $details['userType'],
                    'location' => $details['location'],
                    'status' => $details['status'],
                    'name' => $details['username']
                ];
        }

        return $user_details;
    }
    public function getUsers() {
        $responders = new FirestoreDB();

        $responder_arr = $responders -> get_document('responders_location', []);
        $resp = [];
        foreach ($responder_arr as $responder) {
            $resp = [
                'event_id' => $responder['event_id'],
                'lat' => $responder['lat'],
                'lng' => $responder['lng'],
                'name' => $responder['name'],
                'status' => $responder['status'],
            ];
        }
        return $resp;
    }
}