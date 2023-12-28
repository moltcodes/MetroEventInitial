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
                echo "Third column";
            echo "</div>";
        echo "</div>";
        echo "</div>";

    function getEvents(){
        $events = getEventsFromFile();
        $organizers = getUsersFromFile();
        $participantCounter = 0;
        if (!empty($events)) {
            foreach ($events as $event) {
                foreach ($event['participants'] as $participant) {
                    if($participant['userId'] == $_SESSION['userID']){
                        echo "<div class ='eventWrapper'>";
                        echo "<div class ='card-body'>";
                        echo "<h4>{$event['type']}</h4>";

                        foreach ($organizers as $organizer){
                            if($event['organizerId'] == $organizer['id']){
                                echo "<p>Organized by {$organizer['name']}</p>";
                        }
                }
                
                if (!empty($event['participants'])) {
                    foreach ($event['participants'] as $participant) {
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

                echo "<p class='card-text mt-4'>Upvotes: {$event['upvotes']}</p>";
                echo "<h3 class='mt-4'>Reviews:</h3>";
                if (!empty($event['reviews'])) {
                    foreach ($event['reviews'] as $review) {
                        echo "<p class='card-text'>User ID: {$review['userId']}, Review: {$review['text']}</p>";
                    }
                } else {
                    echo "<p class='card-text'>No reviews for this event.</p>";
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

