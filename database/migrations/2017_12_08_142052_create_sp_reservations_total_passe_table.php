<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpReservationsTotalPasseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE DEFINER=`globoreserv_globo`@`%` PROCEDURE `sp_reservations_total_passe` (IN `in_passe` INT UNSIGNED)  NO SQL
        SELECT

        
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 1 GROUP BY `rt1`.`ticket_type_id` )
        ,
        (SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id` )          
        )

        as ADU,

            
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 2 GROUP BY `rt1`.`ticket_type_id` )          
        ) as CHD,

            
        coalesce(
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`)
        ,
        (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 3 GROUP BY `rt1`.`ticket_type_id` )          
        ) as INF,

        (
        (IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 1 GROUP BY `rt1`.`ticket_type_id`),0)) + 
        (IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 2 GROUP BY `rt1`.`ticket_type_id`), 0))
        ) 
        +
        (
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0)) +
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0))
        +
        (
        (IFNULL((SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id`),0)))


        ) as TOT

        FROM

        `reservations` `r`
        LEFT JOIN `reservation_tickets` `rt`
        ON
            (`r`.`id` = `rt`.`reservation_id`)

        LEFT JOIN reservation_packs rp ON
        r.id = rp.reservation_id    

        LEFT JOIN `resellers` `rl`
        ON
            (`r`.`reseller_id` = `rl`.`id`)

        LEFT JOIN `channels` `rc`
        ON
            (`r`.`channel_id` = `rc`.`id`)

        LEFT JOIN `customers` `c`
        ON
            (`r`.`customer_id` = `c`.`id`)
            
        LEFT JOIN `reservation_types` `rtp`
        ON
            (`r`.`reservation_type_id` = `rtp`.`id`)

        LEFT JOIN `packs` `pa`
        ON
            (`r`.`pack_id` = `pa`.`id`)

        LEFT JOIN `passes` `p`
        ON
            (`r`.`pass_id` = `p`.`id`)

        where r.pass_id = in_passe or rp.pass_id = in_passe
        GROUP BY
        `r`.`id`
        ORDER BY
        `r`.`id`
        DESC
         
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement( 'DROP PROCEDURE  sp_reservations_total_passe' );
    }
}
