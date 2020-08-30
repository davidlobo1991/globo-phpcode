<?php

return [
    /** RESERVATIONS INFO */
    'reservations-table' => 'reservations',
    'reservations-model' => 'App\Reservation',
    'reservations-relation' => 'reservationTickets',
    'reservations-foreign' => 'reservation_id',

    /** RESERVATIONS TICKETS INFO */
    'reservations-tickets-table' => 'reservation_tickets',
    'reservations-tickets-model' => 'App\ReservationTicket',
    'reservations-tickets-relation' => 'reservation',
    'reservations-tickets-foreign' => 'reservation_ticket_id',
];