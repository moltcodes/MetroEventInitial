<?php
    $usersJSON = '../Data/users.json';
    $organizerRequestJSON = '../Data/organizerRequest.json';
    $adminRequestJSON  = '../Data/adminRequest.json';
    $notificationsJSON = '../Data/notifications.json';
    $eventsJSON = '../Data/events.json';
    $userID;

    function getUsersFromFile() {
        global $usersJSON;

        if (!file_exists($usersJSON)) {
            return [];
        }

        $userData = file_get_contents($usersJSON);
        return json_decode($userData, true);
    }

    function getEventsFromFile() {
        global $eventsJSON;

        if (!file_exists($eventsJSON)) {
            return [];
        }

        $eventData = file_get_contents($eventsJSON);
        return json_decode($eventData, true);
    }

    function getNotificationsFromFile(){
        global $notificationsJSON;

        if (!file_exists($notificationsJSON)) {
            return [];
        }

        $notificationData = file_get_contents($notificationsJSON);
        return json_decode($notificationData, true);
    }

    function registerUser($name, $username, $email, $password){
        global $usersJSON;
        $users = getUsersFromFile();
        
        $newUser = [
            'id' => count($users)+1,
            'name' => $name,
            'username' =>  $username,
            'email' => $email,
            'password' => $password
        ];

        $users[] = $newUser;
        file_put_contents($usersJSON, json_encode($users, JSON_PRETTY_PRINT));
    }

    function loginUser($email, $password){
        $users = getUsersFromFile();
        foreach($users as $user){
            if($user['email'] == $email){
                if($password == $user['password']){
                    $_SESSION['userID'] = $user['id'];
                    header("Location: userPage.php");
                    exit();
                }
            }
        }
    return false;
    }

    function requestToJoinEvent($eventID){
        global $eventsJSON;
        $events = getEventsFromFile();
        $ctr = 0;

        foreach($events as $event){
            $ctr++;
            if($event['id'] == $eventID){
                $newParticipant = [
                    'id' => $_SESSION['userID'],
                    'status' => "pending"
                ];
                $events[$ctr-1]['participants'][] = $newParticipant;
            }
        }
        
        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }
?>
