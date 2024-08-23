<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Calendar</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.0.0/dist/index.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.0.0/dist/index.global.min.js'></script>
</head>
<body>
    <header>
        <h1>Student Calendar</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="view_notifications.php">Notifications</a>
        <a href="studentssettings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div id="calendar"></div>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('fetch_events.php')
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        failureCallback(error);
                    });
            },
            eventClick: function(info) {
                var eventObj = info.event;

                alert('Event: ' + eventObj.title + '\nDescription: ' + eventObj.extendedProps.description);
            }
        });

        calendar.render();
    });
    </script>
</body>
</html>
