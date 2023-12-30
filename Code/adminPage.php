<?php
    include 'header.php';
    include 'api.php';
    session_start();
    ob_start();

    echo "<div class = 'wrapper'>";
        echo "<section class = 'columns'>";
            echo "<div class = 'column3'>";
                echo "<h5 class ='textCenter'>Control Center</h5>";
                echo "<div class ='notificationWrapper'>";
                echo "<div class ='card-body'>";
                echo "<form method= 'POST'>";
                        echo "<h6>Create Event</h6><br>";
                        echo "<input type= 'text' style = 'width: 90%' class= 'form-control form-control-mg' name= 'eventName' placeholder='Enter event name'><br>";
                        echo "<input type='date' name = 'eventDate' class= 'form-control form-control-mg' value='Create Event'></button><br>";
                        echo "<input type='submit' name = 'createEventButton' value='Create Event'></button>";
                        echo "</form>";

                        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eventDate']) && isset($_POST['eventName'])){
                            $eventName = $_POST['eventName'];
                            $eventDate = $_POST['eventDate'];
                            $userID = $_SESSION['userID'];
                            createEvent($eventName, $eventDate, $userID);
                            header("Location: " . $_SERVER['REQUEST_URI']);
                            exit();
                        }
                echo "</div>";
                echo "</div>";

            echo "</div>";
            echo "<div class = 'column3'>";
                echo "<h5 class ='textCenter'>Events Dashboard</h5>";
                getEventsAdministrator();
            echo "</div>";
            echo "<div class = 'column3'>";
            echo "<h5 class ='textCenter'>Promotion Requests</h5>";
                getRequestList();
                

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reqOrg'])) {
                    requestToBeOrganizer($_SESSION['userID']);
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                }
        echo "</div>";
    echo "</div>";

    function getEvents(){
        $events = getEventsFromFile();
        $organizers = getUsersFromFile();
        $users = getUsersFromFile();
        $participantCounter = 0;
        $userPendingFlag = false;
        $userAcceptedFlag = false;
        $eventID = null;
        if (!empty($events)) {
            foreach ($events as $event) {
                $userPendingFlag = false;
                $userAcceptedFlag = false;
                $participantCounter = 0;
                echo "<div class ='eventWrapper'>";
                echo "<div class ='card-body'>";
                $eventID = $event['id'];
                echo "<h4>{$event['type']}</h4>";
                foreach ($organizers as $organizer){
                    if($event['organizerId'] == $organizer['id']){
                        echo "<p>Organized by {$organizer['name']}</p>";
                    }
                }
                
                if (!empty($event['participants'])) {
                    foreach ($event['participants'] as $participant) {
                        if($_SESSION['userID'] == $participant['userId']){
                            if($participant['status'] == "pending"){
                                $userPendingFlag = true;
                            }else if($participant['status'] == "accepted"){
                                $userAcceptedFlag = true;
                            }
                        }
                        $participantCounter++;
                    }
                    if($participantCounter == 1){
                        echo "<p>$participantCounter user has joined</p>";  
                    }else{
                        echo "<p>$participantCounter users have joined</p>";
                    }
                    
                } else {
                    echo "<p>No participants for this event.</p>";
                }

                echo "<p>Upvotes: {$event['upvotes']}</p>";

                echo "<h6>Reviews:</h6>";
                if (!empty($event['reviews'])) {
                    foreach ($event['reviews'] as $review) {
                        foreach($users as $user){
                            if($review['userId'] == $user['id']){
                                echo $user['name'];
                            }
                        }
                        echo "<p>{$review['text']}</p>";
                    }
                } else {
                    echo "<p class='card-text'>No reviews for this event.</p>";
                }

                if($userPendingFlag){
                    echo "<button>Request to Join Sent</button>";
                }else if($userAcceptedFlag){
                    echo "<button>Joined</button>";
                }else{
                    echo "<form method = 'POST'>";
                    echo "<input type= 'submit' name = 'joinEvent' value = 'Join Event'></button>";
                    echo "<input type='hidden' name='joinEventDetermine' value= $eventID>";
                    echo "</form>";

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['joinEvent'])) {
                        $eventIDPar = $_POST['joinEventDetermine'];
                        requestToJoinEvent($eventIDPar);
                        header("Refresh:0");
                        exit();
                    }
                }
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No events available.</p>";
        }
    }

    function getEventsAdministrator(){
        $events = getEventsFromFile();
        $organizers = getUsersFromFile();
        $users = getUsersFromFile();
        $participantCounter = 0;
        $participantCounter2 = 0;
        $eventID = null;
        if (!empty($events)) {
            foreach ($events as $event) {
                $participantCounter = 0;
                echo "<div class ='eventWrapper'>";
                echo "<div class ='card-body'>";
                $eventID = $event['id'];
                echo "<h4>{$event['type']}</h4>";
                foreach ($organizers as $organizer){
                    if($event['organizerId'] == $organizer['id']){
                        echo "<p>Organized by {$organizer['name']}</p>";
                    }
                }
                
                if (!empty($event['participants'])) {
                    foreach ($event['participants'] as $participant) {
                        if($_SESSION['userID'] == $participant['userId']){
                            if($participant['status'] == "pending"){
                                $userPendingFlag = true;
                            }else if($participant['status'] == "accepted"){
                                $userAcceptedFlag = true;
                            }
                        }
                        $participantCounter++;
                    }
                    if($participantCounter == 1){
                        echo "<p>$participantCounter user has joined</p>";  
                    }else{
                        echo "<p>$participantCounter users have joined</p>";
                    }
                    
                } else {
                    echo "<p>No participants for this event.</p>";
                }

                echo "<h6>List of Accepted Users</h6>";
                    foreach($event['participants'] as $participant){
                        foreach($users as $user){
                            if($user['id'] == $participant['userId'] && $participant['status'] == 'accepted'){
                                echo $user['name'];
                                echo "<br>";
                                $participantCounter2++;
                            }
                        }
                    }
                    if($participantCounter2 == 0){
                        echo "<p>No participants for this event.</p>";
                    }
                

                echo "<br></br>";
                echo "<h6>List of Pending Users</h6>";
                $participantCounter2 = 0;
                if(!empty($event)){
                    foreach($event['participants'] as $participant){
                        foreach($users as $user){
                            if($user['id'] == $participant['userId'] && $participant['status'] == 'pending'){
                                $idToAction = $user['id'];
                                echo $user['name'];
                                $participantCounter2++;

                                echo "<form method='POST' action = ''>";
                                echo "<input type='submit' name = 'approveUser' value = 'Approve'>"; //Line 236
                                echo "<input type='hidden' name='approveUserDetermine' value= $idToAction>"; 
                                echo "<input type='hidden' name='eventDetermine' value= $eventID>";
                                echo "</form>";
    
                                echo "<form method = 'POST'>";
                                echo "<input type= 'submit' name = 'declineUser' value = 'Decline'>";
                                echo "<input type='hidden' name='declineUserDetermine' value= $idToAction>";
                                echo "<input type='hidden' name='eventDetermine' value= $eventID>";
                                echo "</form>";
                                
                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveUser'])) {
                                    $userIDPar = $_POST['approveUserDetermine'];
                                    $eventIDPar = $_POST['eventDetermine'];
                                    respondToEventRequest(true, $eventIDPar, $userIDPar); 
                                    header("Location: ".$_SERVER['PHP_SELF']); //Line 251
                                    exit();
                                }

                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['declineUser'])) {
                                    $userIDPar = $_POST['declineUserDetermine'];
                                    $eventIDPar = $_POST['eventDetermine'];
                                    respondToEventRequest(false,  $eventIDPar, $userIDPar);
                                    header("Location: ".$_SERVER['PHP_SELF']);
                                    exit();
                                }

                            }
                        }
                    }
                }

                if($participantCounter2 == 0){
                    echo "<p>No registrants for this event.</p>";
                }

                echo "<form method = 'POST'>";
                echo "<input type= 'submit' name = 'cancelEvent' value = 'Cancel Event'>";
                echo "<input type='hidden' name='eventDetermine' value= $eventID>";
                echo "</form>";

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelEvent'])) {
                    $eventIDPar = $_POST['eventDetermine'];
                    cancelEvent($eventIDPar);
                    header("Location: ".$_SERVER['PHP_SELF']);
                    exit();
                }

                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No events available.</p>";
        }
    }

    function getRequestList(){
        global $adminRequestJSON;
        global $organizerRequestJSON;

        $adminReq = getAdminReqFromFile();
        $orgReq = getOrgReqFromFile();
        $users = getUsersFromFile();
        $idToAction = null;

        echo "<h6>List of Organizer Applicants</h6>";
        foreach($orgReq as $or){
            $idToAction = $or['userId'];
            foreach($users as $user){
                if($or['userId'] == $user['id'] && $or['status'] == "pending"){
                    echo "<div class ='notificationWrapper'>";
                    echo "<div class ='card-body'>";
                    echo $user['name'];
                    echo "<form method='POST' action = ''>";
                    echo "<input type='submit' name = 'approveUserOrg' value = 'Approve'>"; //Line 236
                    echo "<input type='hidden' name='approveUserOrgDetermine' value= $idToAction>"; 
                    echo "</form>";

                    echo "<form method = 'POST'>";
                    echo "<input type= 'submit' name = 'declineUserOrg' value = 'Decline'>";
                    echo "<input type='hidden' name='declineUserOrgDetermine' value= $idToAction>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveUserOrg'])) {
                        $userIDPar = $_POST['approveUserOrgDetermine'];
                        respondToOrganizerRequest(true, $userIDPar); 
                        header("Location: ".$_SERVER['PHP_SELF']); //Line 251
                        exit();
                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['declineUserOrg'])) {
                        $userIDPar = $_POST['declineUserOrgDetermine'];
                        respondToOrganizerRequest(false, $userIDPar);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit();
                    }
                }
            }
        }

        echo "<h6>List of Administrator Applicants</h6>";
        foreach($adminReq as $ar){
            $idToAction = $ar['userId'];
            foreach($users as $user){
                if($ar['userId'] == $user['id'] && $ar['status'] == "pending"){
                    echo "<div class ='notificationWrapper'>";
                    echo "<div class ='card-body'>";
                    echo $user['name'];
                    echo "<form method='POST' action = ''>";
                    echo "<input type='submit' name = 'approveUserAdmin' value = 'Approve'>"; //Line 236
                    echo "<input type='hidden' name='approveUserAdminDetermine' value= $idToAction>"; 
                    echo "</form>";

                    echo "<form method = 'POST'>";
                    echo "<input type= 'submit' name = 'declineUserAdmin' value = 'Decline'>";
                    echo "<input type='hidden' name='declineUserAdminDetermine' value= $idToAction>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approveUserAdmin'])) {
                        $userIDPar = $_POST['approveUserAdminDetermine'];
                        respondToAdminRequest(true, $userIDPar); 
                        header("Location: ".$_SERVER['PHP_SELF']); //Line 251
                        exit();
                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['declineUserAdmin'])) {
                        $userIDPar = $_POST['declineUserAdminDetermine'];
                        respondToAdminRequest(false, $userIDPar);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit();
                    }
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Your Page Title</title>
        <link rel="stylesheet" href = "stylesheet.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="themes/bootstrap-theme/ej.web.all.min.css" />
		<script src="scripts/external/jquery-3.1.1.min.js"></script> 
		<script src="scripts/web/ej.web.all.min.js"> </script>
    </head>
    <body>
    </body>
</html>

