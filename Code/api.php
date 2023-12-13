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
                    header("Location: userPage.php");
                    exit();
                }
            }
        }
    return false;
    }
?>
