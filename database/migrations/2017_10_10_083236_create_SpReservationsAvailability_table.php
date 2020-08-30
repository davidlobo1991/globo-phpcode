<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpReservationsAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE DEFINER=`globoreserv_globo`@`%` PROCEDURE `sp_reservations_availability` (IN `in_passe` INT UNSIGNED, IN `in_seller` INT UNSIGNED)  NO SQL
        SELECT
        st.title,
        st.acronym, 
        st.default_quantity,
        IFNULL(IFNULL(seats_sold,0) + IFNULL(seats_sold_pack,0),0) as seats_sold,
        IFNULL(seats_sold_pack, 0) as seats_sold_packs,
        ABS(
            IFNULL(IFNULL(IFNULL(seats_sold,0) + IFNULL(seats_sold_pack,0),0), 0) - IFNULL(total_seats, 0)
        ) AS total_solded

        FROM
        seat_types AS st
        LEFT JOIN(
        SELECT st.id,
            (SUM(rt.quantity)) AS seats_sold,
            (SUM(rp.quantity)) AS seats_sold_pack
        FROM
        seat_types st
        LEFT JOIN reservation_tickets rt ON
        st.id = rt.seat_type_id
        LEFT JOIN reservation_packs rp ON
        st.id = rp.seat_type_id
        LEFT JOIN reservations r ON
        r.id = rt.reservation_id or r.id = rp.reservation_id
        LEFT JOIN channels rc ON
        r.channel_id = rc.id
        LEFT JOIN ticket_types t ON
        t.id = rt.ticket_type_id or t.id = rp.ticket_type_id 
        WHERE
        r.finished = 1 AND r.canceled_date IS NULL AND t.take_place != 0 AND rc.passes_seller_id = in_seller AND (r.pass_id = in_passe or rp.pass_id = in_passe)
        GROUP BY
        st.id,r.reconcile
        ) seat_types_quantities
        ON
        st.id = seat_types_quantities.id
        LEFT JOIN(
        SELECT
            st.id,
            pst.seats_available AS total_seats
        FROM
            seat_types st
        INNER JOIN pass_seat_type pst ON
            st.id = pst.seat_type_id AND pst.pass_id = in_passe
        ) seat_types_total
        ON
        st.id = seat_types_total.id
        WHERE st.is_enable = 1
        ORDER BY
        st.sort
         
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP PROCEDURE sp_reservations_availability' );
    }
}
