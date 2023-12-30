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
            echo "</div>";
        echo "</div>";
        echo "</div>";

    function getEvents(){
        $events = getEventsFromFile();
        $organizers = getUsersFromFile();
        $users = getUsersFromFile();
        $participantCounter = 0;
        $eventID = null;
        if (!empty($events)) {
            foreach ($events as $event) {

                $participantCounter = 0;
                foreach ($event['participants'] as $participant) {
                    if($participant['userId'] == $_SESSION['userID'] && $participant['status'] == "accepted"){
                        echo "<div class ='eventWrapper'>";
                        echo "<div class ='card-body'>";
                        echo "<h4>{$event['type']}</h4>";
                        $eventID = $event['id'];
                        foreach ($organizers as $organizer){
                            if($event['organizerId'] == $organizer['id']){
                                echo "<p>Organized by {$organizer['name']}</p>";
                            }
                        }
            
                        if(!empty($event['participants'])) {
                            foreach ($event['participants'] as $participant) {
                                $participantCounter++;
                            }
                            if($participantCounter == 1){
                                echo "<p>$participantCounter user has joined</p>";  
                            }else{
                                echo "<p>$participantCounter users have joined</p>";
                            }
                            
                        }else{
                            echo "<p>No participants for this event.</p>";
                        }

                        echo "<p>Upvotes: {$event['upvotes']}</p>";
                        echo "<form method = 'POST'>";
                        echo "<input type= 'submit' name = 'upvoteEvent' value = 'Upvote'></button>";
                        echo "<input type='hidden' name='upvoteEventDetermine' value= $eventID>";
                        echo "</form>";

                        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upvoteEvent'])) {
                            $eventIDPar = $_POST['upvoteEventDetermine'];
                            upvoteEvent($eventIDPar, $_SESSION['userID']);
                            header("Refresh:0");
                        }

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
                        }else{
                            echo "<p class='card-text'>No reviews for this event.</p>";
                        }

                        echo "<form method= 'POST'>";
                        echo "<input type= 'text' style = 'width: 90%' name= 'review' placeholder='Enter review'>";
                        echo "<input type='submit' name = 'reviewEventButton' value='Post'></button>";
                        echo "<input type='hidden' name='reviewDetermine' value= $eventID>";
                        echo "</form>";

                        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['review']) && isset($_POST['reviewEventButton'])){
                            $reviewMessage = $_POST['review'];
                            $eventID = $_POST['reviewDetermine'];
                            $userID = $_SESSION['userID'];
                            reviewEvent($eventID, $reviewMessage, $userID);
                            header("Location: " . $_SERVER['REQUEST_URI']);
                            exit();
                        }
                        
                        echo "</div>";
                        echo "</div>";
                    }
                }
                
            }
        } else {
            echo "<p>No events available.</p>";
        }
    }

    function getNotifications(){
        $notifications = getNotificationsFromFile();

        foreach($notifications as $notification){
            echo "<div class = 'notificationWrapper'>";
            echo "<div class ='card-body'>";
            echo "<h5> From {$notification['name']}</h5>";
            echo "<p>{$notification['message']}</p>";
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

