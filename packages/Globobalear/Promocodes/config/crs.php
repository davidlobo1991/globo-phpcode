<?php

return [
    /** RESERVATIONS INFO */
    'reservations-table' => 'reservations',
    'reservations-model' => 'App\Reservation',
    'reservations-relation' => 'reservationTickets',

    /** RESERVATIONS TICKETS INFO */
    'reservations-tickets-table' => 'reservation_tickets',
    'reservations-tickets-model' => 'App\ReservationTicket',
    'reservations-tickets-relation' => 'reservation',
];