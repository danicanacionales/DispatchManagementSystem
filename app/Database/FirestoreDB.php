<?php

namespace App\Database;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Transaction;
use Google\Cloud\Core\GeoPoint;

class FirestoreDB
{
    private $firestore;

    public function __construct () {
        $json_cred = base_path().'/XXXXXDB-XXXXX.json';

        $this-> firestore = new FirestoreClient ( [
            'keyFilePath' => $json_cred
        ]);
    }

    public function get_document(string $collection_name, Array $criteria) {

        $collection = $this -> firestore -> collection($collection_name);
        if (!empty($criteria)) {
            $query = $collection -> where($criteria[0], $criteria[1], $criteria[2]);
            return $query -> documents();
        }
        return $collection -> documents();
    
        // can be an array of documents
    }

    public function createDocument(string $collection, Array $contents) {

        $collection = $this -> firestore -> collection ($collection);
        $newUser = $collection->add([
            'user' => $contents['username'],
            'pass' => $contents['password']
        ]);

        return 'Registered!';
    }

    public function createRespondersDocument(string $collection, Array $contents) {

        $collection = $this -> firestore -> collection ($collection);
        $newUser = $collection->add([
            'location' => new GeoPoint(14.315320, 121.079586),
            'avatarUrl' => "https://firebasestorage.googleapis.com/v0/b/c3chat-782d3.appspot.com/o/Default%2Fdefault-avatar.png?alt=media&token=e3a8ea4f-006f-4759-83b3-1174712fe4c2",
            'status' => $contents['status'],
            'userType' => $contents['userType'],
            'username' => $contents['username']
        ]);

        return 'Registered!';
    }


}