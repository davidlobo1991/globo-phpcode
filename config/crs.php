<?php

return [
    /** RESERVATIONS INFO */
    'reservations-table' => 'reservations',
    'reservations-key' => 'reservation_id',
    'reservations-model' => 'App\Reservation',
    'reservations-relation' => 'reservationTickets',

    /** RESERVATIONS TICKETS INFO */
    'reservations-tickets-table' => 'reservation_tickets',
    'reservations-tickets-key' => 'reservation_ticket_id',
    'reservations-tickets-model' => 'App\ReservationTicket',
    'reservations-tickets-relation' => 'reservation',

    'reservation-number-prfix' => 'ME'
];
