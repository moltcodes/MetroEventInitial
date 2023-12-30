<?php
    include 'header.php';
    include 'api.php';
    session_start();

    echo "<div class = 'wrapper'>";
        echo "<section class = 'columns'>";
            echo "<div class = 'column'>";
                echo "<h5 class ='textCenter'>Notifications</h5>";
                getNotifications();
            echo "</div>";
            echo "<div class = 'column'>";
                echo "<h5 class ='textCenter'>Events</h5>";
                getEvents();
            echo "</div>";
            echo "<div class = 'column'>";
            echo "<h5 class ='textCenter'>Opportunity Log</h5>";
                echo "<div class ='notificationWrapper'>";
                    echo "<div class ='card-body'>";
                        echo "<h6>Be an Organizer!</h6>";
                        echo "<p>Send your resume to metroevents@admin.com and click the button</p>";
                        echo "<form method = 'POST'>";
                        $orgReq = getOrgReqFromFile();
                        $orgReqFlag = false;
                        foreach($orgReq as $or){
                            if($_SESSION['userID'] == $or['userId']){
                                $orgReqFlag = true;
                            }
                        }

                        if($orgReqFlag){
                            echo "<input type= 'submit' name = 'reqOrg' value = 'Request Pending'></button>";
                        }else{
                            echo "<input type= 'submit' name = 'reqOrg' value = 'Join the Team'></button>";
                        }
                       
                        echo "</form>";
                    echo "</div>";
                echo "</div>";

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reqOrg'])) {
                    requestToBeOrganizer($_SESSION['userID']);
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
                        exit(0);
                    }
                }
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No events available.</p>";
        }
    }

    function getNotifications(){
        $notifications = getNotificationsFromFile();
        $notificationID = null;
        foreach($notifications as $notification){
            $notificationID = $notification['id'];
            echo "<div class = 'notificationWrapper'>";
            echo "<div class ='card-body'>";
            echo "<h5> From {$notification['name']}</h5>";
            echo "<p>{$notification['message']}</p>";
            echo "<form method = 'POST'>";
            echo "<input type= 'submit' name = 'deleteNotif' value = 'Delete'></button>";
            echo "<input type='hidden' name='deleteNotifDetermine' value= $notificationID>";
            echo "</form>";
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteNotif'])) {
            $notificationIDPar = $_POST['deleteNotifDetermine'];
            deleteNotification($notificationIDPar);
            header("Refresh:0");
            exit(0);
            }
            echo "</div>";
            echo "</div>";
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
    </head>
    <body>

    </body>
</html>

