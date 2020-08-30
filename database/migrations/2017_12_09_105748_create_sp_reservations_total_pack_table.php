<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpReservationsTotalPackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE DEFINER=`globoreserv_globo`@`%` PROCEDURE ` sp_reservations_total_pack ` (IN `in_passe`,IN `in_reservation` INT UNSIGNED)  NO SQL
        SELECT
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`) as ADU ,
            
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2  and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`) as CHD,
                        
        (SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id` )        as INF,   
            
        (
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 1 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0)) +
        (IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 and rt2.pass_id = in_passe GROUP BY `rt2`.`ticket_type_id`),0))
              
        ) as TOT
        FROM
        `reservations` `r`
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

     where rp.pass_id = in_passe
     and r.id = in_reservation
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
        DB::statement( 'DROP PROCEDURE  sp_reservations_total_pack' );
    }
}

