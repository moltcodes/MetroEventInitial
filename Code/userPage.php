<?php
include 'header.php';
include 'api.php';
getEvents();

function getEvents(){
    $events = getEventsFromFile();
    if (!empty($events)) {
        foreach ($events as $event) {
            echo "<div class='eventWrapper'>";
            echo "<div class='card-body'>";
            echo "<h2 class='card-title text-primary'>Event Type: {$event['type']}</h2>";
            echo "<p class='card-text'>Event ID: {$event['id']}</p>";
            echo "<p class='card-text'>Organizer ID: {$event['organizerId']}</p>";

            // Display participants
            echo "<h3 class='mt-4'>Participants:</h3>";
            if (!empty($event['participants'])) {
                foreach ($event['participants'] as $participant) {
                    echo "<p class='card-text'>User ID: {$participant['userId']}, Status: {$participant['status']}</p>";
                }
            } else {
                echo "<p class='card-text'>No participants for this event.</p>";
            }

            // Display upvotes
            echo "<p class='card-text mt-4'>Upvotes: {$event['upvotes']}</p>";

            // Display reviews
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
    } else {
        echo "<p>No events available.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Your Page Title</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel='stylesheet' href='stylesheet.css'>
    </head>
    <body>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <!-- Your HTML content goes here -->

    </body>
</html>