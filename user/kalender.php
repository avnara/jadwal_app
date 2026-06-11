<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['id_user'])) {

    header("Location: ../auth/login_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kalender Jadwal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css" rel="stylesheet">

<style>

#calendar{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <h2>Jadwal App</h2>

    <ul>

        <li>
            <a href="dashboard.php">
                <i class="fa fa-home"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="jadwal.php">
                <i class="fa fa-calendar"></i>
                Jadwal Saya
            </a>
        </li>

        <li>
            <a href="kalender.php">
                <i class="fa fa-calendar-days"></i>
                Kalender
            </a>
        </li>

        <li>
            <a href="../auth/logout.php">
                <i class="fa fa-sign-out-alt"></i>
                Logout
            </a>
        </li>

    </ul>

</div>

<!-- MAIN -->

<div class="main-content">

    <div class="topbar">

        <h4>
            Kalender Jadwal Saya
        </h4>

    </div>

    <!-- KALENDER -->

    <div id="calendar"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>

<script>
/* global FullCalendar */
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        height: 'auto',

        selectable: true,

        editable: false,

        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: 'dayGridMonth,timeGridWeek,timeGridDay'

        },

        events: 'get_jadwal.php',

        eventTimeFormat: {

            hour: '2-digit',
            minute: '2-digit',
            meridiem: false

        }

    });

    calendar.render();

});

</script>

</body>
</html>