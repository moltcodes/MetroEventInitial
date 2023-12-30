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

    function getOrgReqFromFile(){
        global $organizerRequestJSON;

        if (!file_exists($organizerRequestJSON)) {
            return [];
        }

        $orgReqData = file_get_contents($organizerRequestJSON);
        return json_decode($orgReqData, true);
    }

    function getAdminReqFromFile(){
        global $adminRequestJSON;

        if (!file_exists($adminRequestJSON)) {
            return [];
        }

        $adminReqData = file_get_contents($adminRequestJSON);
        return json_decode($adminReqData, true);
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
                    if($user['role'] == "user"){
                        header("Location: userPage.php");
                        exit();
                    }else if($user['role'] == "organizer"){
                        header("Location: organizerPage.php");
                        exit();
                    }else{
                        header("Location: adminPage.php");
                        exit();
                    }
                   
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
                foreach($event['participants'] as $participant){
                    if($participant['userId'] == $_SESSION['userID']){
                        return;
                    }
                }
                $newParticipant = [
                    'userId' => $_SESSION['userID'],
                    'status' => "pending"
                ];
                $events[$ctr-1]['participants'][] = $newParticipant;
            }
        }
        
        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }

    
    function upvoteEvent($eventID, $userID) {
        global $eventsJSON;
        $events = getEventsFromFile();
        $increment = 1;

        foreach ($events as $key => $event) {
            if ($event['id'] == $eventID) {
                $userAlreadyUpvoted = false;
                foreach ($event['participants'] as $participant) {
                    if ($participant['userId'] == $userID && $participant['status'] == 'upvoted') {
                        $userAlreadyUpvoted = true;
                        break;
                    }
                }

                if (!$userAlreadyUpvoted) {
                    $events[$key]['upvotes'] += $increment;
                    $events[$key]['participants'][] = array('userId' => $userID, 'status' => 'upvoted');
                }

                break;
            }
        }

        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }

    function reviewEvent($eventID, $reviewMessage, $userID){
        global $eventsJSON;
        $events = getOrgReqFromFile();
    
        foreach($events as $key => $event){
            if($event['id'] == $eventID){
                $newReview = [
                    'userId' => $userID,
                    'text' => $reviewMessage
                ];
                $events[$key]['reviews'][] = $newReview;
                break;
            }
        }

        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }

    function requestToBeOrganizer($userID){
        global $organizerRequestJSON;
        $orgReq = getOrgReqFromFile();

        foreach($orgReq as $or){
            if($or['userId'] == $userID){
                return;
            }
        }

        $newOrgReq = [
            'userId' => $userID,
            'status' => "pending"
        ];

        $orgReq[] = $newOrgReq;
        file_put_contents($organizerRequestJSON, json_encode($orgReq, JSON_PRETTY_PRINT));
    }

    function createEvent($eventName, $eventDate, $userID){
        global $eventsJSON;

        $events = getEventsFromFile();

        $lastContent = end($events);
        $newID = $lastContent['id'] + 1;

        $newEvent = [
            'id' => $newID,
            'type' => $eventName,
            'organizerId' => $userID,
            'date' => $eventDate,
            'participants' => [],
            'upvotes' => 0,
            'reviews' => []
        ];

        $events[] = $newEvent;
        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }

    function respondToEventRequest($acceptOrDecline, $eventID, $userID){
        global $eventsJSON;
        $events = getEventsFromFile();
        $accepted = "accepted";
    
        if($acceptOrDecline){
            foreach($events as &$event){  
                foreach($event['participants'] as &$participant){  
                    if($participant['userId'] == $userID && $participant['status'] == "pending"){
                        $participant['status'] =  $accepted;
                        sendNotification($userID, $_SESSION['userID'], "Your request to join the ".$event['type']." event is accepted!");
                        break;
                    }
                }
            }
        } else {
            foreach($events as &$event){  
                if($event['id'] == $eventID){
                    foreach($event['participants'] as &$participant){  
                        if($participant['userId'] == $userID && $participant['status'] == "pending"){
                            $participant['status'] = "declined";
                            $organizerID = $_SESSION['userID'];
                            echo $organizerID;
                            sendNotification($userID, $organizerID, "Your request to join the ".$event['name']." event is declined!");
                            break;
                        }
                    }
                }
            } 
        }
    
        file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
    }

    function sendNotification($userID, $senderID, $message){
        global $notificationsJSON;
        global $usersJSON;

        $notifications = getNotificationsFromFile();
        $users = getUsersFromFile();
        $determineSender = null;

        $lastContent = end($notifications);
        $newID = $lastContent['id']+1;

        foreach($users as $user){
            if($user['id'] == $senderID){
                $determineSender = $user['name'];
            }
        }

        $newNotification = [
            'id' => $newID,
            'userId' => $userID,
            'name' => $determineSender,
            'type' => "Event Request Update",
            'message' => $message
        ];

        $notifications[] =$newNotification;
        file_put_contents($notificationsJSON, json_encode($notifications, JSON_PRETTY_PRINT));
    }

    function cancelEvent($eventID){
        global $eventsJSON;
        $events = getEventsFromFile();
        $found = false;

        foreach($events as $key => $event){
            if($event['id'] == $eventID){
                unset($events[$key]);
                $found = true;
            }
        }

        if($found){
            file_put_contents($eventsJSON, json_encode($events, JSON_PRETTY_PRINT));
        }
    }

    function deleteNotification($notificationIDPar){
        global $notificationsJSON;
        $notifs = getNotificationsFromFile();

        foreach($notifs as $key => $notif){
            if($notif['id'] == $notificationIDPar){
                unset($notifs[$key]);
                file_put_contents($notificationsJSON, json_encode($notifs, JSON_PRETTY_PRINT));
            }
        }
    }

    function respondToOrganizerRequest($acceptOrDecline, $userID){
        global $usersJSON;
        global $organizerRequestJSON;
        $orgReq = getOrgReqFromFile();
        $users = getUsersFromFile();

        if($acceptOrDecline){
            foreach($orgReq as &$or){
                if($or['userId'] == $userID){
                    $or['status'] = "accepted";
                    sendNotification($userID, $_SESSION['userID'], "Your request to be an Organizer was accepted!");
                    foreach($users as &$user){
                        if($user['id'] == $userID){
                            $user['role'] = "organizer";
                        }
                    }
                }
            }
        }else{
            foreach($orgReq as &$or){
                if($or['userId'] == $userID){
                    $or['status'] = "declined";
                    sendNotification($userID, $_SESSION['userID'], "Your request to be an Organizer was declined!");
                }
            }
        }

        file_put_contents($organizerRequestJSON, json_encode($orgReq, JSON_PRETTY_PRINT));
        file_put_contents($usersJSON, json_encode($users, JSON_PRETTY_PRINT));
        
    }

    function respondToAdminRequest($acceptOrDecline, $userID){
        global $usersJSON;
        global $adminRequestJSON;
        $adminReq = getAdminReqFromFile();
        $users = getUsersFromFile();

        if($acceptOrDecline){
            foreach($adminReq as &$ar){
                if($ar['userId'] == $userID){
                    $ar['status'] = "accepted";
                    sendNotification($userID, $_SESSION['userID'], "Your request to be an Administrator was accepted!");
                    foreach($users as &$user){
                        if($user['id'] == $userID){
                            $user['role'] = "admin";
                        }
                    }
                }
            }
        }else{
            foreach($adminReq as &$ar){
                if($ar['userId'] == $userID){
                    $ar['status'] = "declined";
                    sendNotification($userID, $_SESSION['userID'], "Your request to be an Administrator was declined!");
                }
            }
        }

        file_put_contents($adminRequestJSON, json_encode($adminReq, JSON_PRETTY_PRINT));
        file_put_contents($usersJSON, json_encode($users, JSON_PRETTY_PRINT));
        
    }

?>
