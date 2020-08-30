<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewreservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW  viewreservations AS 
            SELECT
            `r`.`id` AS `id`,
            `rtp`.`name` AS `type`,
            `rtp`.`acronym` AS `acronym`,
            `r`.`reference_number` AS `reference_number`,
            `r`.`phone` AS `phone`,
            `r`.`reservation_number` AS `reservation_number`,
            `r`.`name` AS `name`,
            `r`.`email` AS `email`,
            `r`.`canceled_date` AS `canceled_date`,
            `r`.`created_by` AS `created_by`,
            `r`.`canceled_by` AS `canceled_by`,
            `r`.`discount` AS `discount`,
            `r`.`identification_number` AS `identification_number`,
            `r`.`canceled_reason` AS `canceled_reason`,
            `r`.`finished` AS `finished`,
            `r`.`channel_id` AS `channel_id`,
            `rc`.`name` AS `channel`,
            `r`.`customer_id` AS `customer_id`,
            `c`.`name` AS `customer`,
            `rl`.`company` AS `company`,
            `r`.`pass_id` AS `pass_id`,
            `r`.`pack_id` AS `pack_id`,
            `r`.`reservation_type_id` AS `reservation_type_id`,
            `r`.`promocode_id` AS `promocode_id`,
            `r`.`reconcile` AS `reconcile`,
            `pa`.`title` AS `pack`,

            coalesce( 
            (SELECT CONCAT(`s`.`name`, ' | ', `p`.`datetime`) AS `passe`
            FROM `passes` `p`LEFT JOIN `shows` `s`ON `p`.`show_id` = `s`.`id` WHERE `p`.`id` = `r`.`pass_id`),
            (`pa`.`title`),   
            (SELECT wp.title FROM reservation_wristband_pass as rwp LEFT JOIN wristband_passes as wp ON (rwp.wristband_pass_id = wp.id) 
            where  rwp.reservation_id = r.id)
            )AS `name_reservation`,




            COALESCE(((
            (SELECT SUM(`rt2`.`quantity`)  FROM `pre_globoreserv_crs3`.`reservation_packs` `rt2` WHERE ((`rt2`.`reservation_id` = `r`.`id`) AND(`rt2`.`ticket_type_id` = 1)) GROUP BY `rt2`.`ticket_type_id`)
            +(SELECT SUM(`rt3`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_pack_wristbands` `rt3` WHERE (`rt3`.`reservation_id` = `r`.`id`)))
            +(SELECT SUM(`rt4`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_packs` `rt4` WHERE((`rt4`.`reservation_id` = `r`.`id`) AND(`rt4`.`ticket_type_id` = 1)) GROUP BY `rt4`.`ticket_type_id`))
            ,
            (SELECT SUM(`rt1`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_tickets` `rt1`WHERE((`rt1`.`reservation_id` = `r`.`id`) AND(`rt1`.`ticket_type_id` = 1)) GROUP BY `rt1`.`ticket_type_id`)) 
            AS `ADU`,

                
            coalesce(
            (SELECT SUM(`rt2`.`quantity`) FROM pre_globoreserv_crs3.`reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 GROUP BY `rt2`.`ticket_type_id`)
            +
            (SELECT SUM(`rt2`.`quantity`) FROM globoreserv_globo_pre.`reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 2 GROUP BY `rt2`.`ticket_type_id`)
            ,
            (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 2 GROUP BY `rt1`.`ticket_type_id` )  
            ,
            (SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id` )        
            ) as CHD,

                
            coalesce(
            (SELECT SUM(`rt2`.`quantity`) FROM pre_globoreserv_crs3.`reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3 GROUP BY `rt2`.`ticket_type_id`) 
                +            
            (SELECT SUM(`rt2`.`quantity`) FROM globoreserv_globo_pre.`reservation_packs` `rt2` WHERE `rt2`.`reservation_id` = `r`.`id` AND `rt2`.`ticket_type_id` = 3 GROUP BY `rt2`.`ticket_type_id`) 
            ,
            (SELECT SUM(`rt1`.`quantity`) FROM `reservation_tickets` `rt1` WHERE `rt1`.`reservation_id` = `r`.`id` AND `rt1`.`ticket_type_id` = 3 GROUP BY `rt1`.`ticket_type_id` )    
            ,
            (SELECT SUM(`rt3`.`quantity`) FROM `reservation_wristband_pass` `rt3` WHERE `rt3`.`reservation_id` = `r`.`id` )      
            ) as INF,

            ((((((
            IFNULL((
                SELECT SUM(`rt2`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_packs` `rt2` WHERE((`rt2`.`reservation_id` = `r`.`id`) AND(`rt2`.`ticket_type_id` = 1)) GROUP BY `rt2`.`ticket_type_id`),0) 
                + IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_packs` `rt2` WHERE((`rt2`.`reservation_id` = `r`.`id`) AND(`rt2`.`ticket_type_id` = 2)) GROUP BY `rt2`.`ticket_type_id`),0)) 
                + IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `pre_globoreserv_crs3`.`reservation_packs` `rt2` WHERE((`rt2`.`reservation_id` = `r`.`id`) AND(`rt2`.`ticket_type_id` = 1)) GROUP BY `rt2`.`ticket_type_id`),0)) 
                + IFNULL((SELECT SUM(`rt2`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_packs` `rt2` WHERE((`rt2`.`reservation_id` = `r`.`id`) AND(`rt2`.`ticket_type_id` = 2)) GROUP BY `rt2`.`ticket_type_id`),0)) 
                + IFNULL((SELECT SUM(`rt3`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_pack_wristbands` `rt3` WHERE (`rt3`.`reservation_id` = `r`.`id`)),0))
                + IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_tickets` `rt1` WHERE((`rt1`.`reservation_id` = `r`.`id`) AND(`rt1`.`ticket_type_id` = 1)) GROUP BY `rt1`.`ticket_type_id`),0)) 
                + IFNULL((SELECT SUM(`rt1`.`quantity`) FROM `globoreserv_globo_pre`.`reservation_tickets` `rt1` WHERE((`rt1`.`reservation_id` = `r`.`id`) AND(`rt1`.`ticket_type_id` = 2)) GROUP BY `rt1`.`ticket_type_id`),0)) 
                AS `TOT`,


            r.booking_fee,
            r.paypal,
            r.comments,

            `r`.`deleted_at` AS `deleted_at`,
            `r`.`created_at` AS `created_at`,
            `r`.`created_at` AS `fecha`,
            `r`.`updated_at` AS `updated_at`
            FROM

            `reservations` `r`
            LEFT JOIN `reservation_tickets` `rt`
            ON
                (`r`.`id` = `rt`.`reservation_id`)

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
        DB::statement( 'DROP VIEW viewreservations' );
    }
}



