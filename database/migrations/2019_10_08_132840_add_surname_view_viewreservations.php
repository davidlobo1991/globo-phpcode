<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurnameViewViewreservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
         CREATE OR REPLACE VIEW `viewreservations` AS
         select `r`.`id` AS `id`,`rtp`.`name` AS `type`,`rtp`.`acronym` AS `acronym`,`r`.`reference_number` AS `reference_number`,`r`.`phone` AS `phone`,`r`.`reservation_number` AS `reservation_number`,`r`.`name` AS `name`,`r`.`surname` AS `surname`,`r`.`email` AS `email`,`r`.`canceled_date` AS `canceled_date`,`r`.`created_by` AS `created_by`,`r`.`canceled_by` AS `canceled_by`,`r`.`discount` AS `discount`,`r`.`identification_number` AS `identification_number`,`r`.`canceled_reason` AS `canceled_reason`,`r`.`finished` AS `finished`,`r`.`channel_id` AS `channel_id`,`rc`.`name` AS `channel`,`r`.`customer_id` AS `customer_id`,`c`.`name` AS `customer`,`rl`.`company` AS `company`,`r`.`pass_id` AS `pass_id`,`r`.`pack_id` AS `pack_id`,`r`.`reservation_type_id` AS `reservation_type_id`,`r`.`promocode_id` AS `promocode_id`,`r`.`reconcile` AS `reconcile`,`pa`.`title` AS `pack`,coalesce((select concat(`s`.`name`,' | ',`p`.`datetime`) AS `passe` from (`passes` `p` left join `products` `s` on((`p`.`product_id` = `s`.`id`))) where (`p`.`id` = `r`.`pass_id`)),`pa`.`title`,(select `wp`.`title` from (`reservation_wristband_pass` `rwp` left join `wristband_passes` `wp` on((`rwp`.`wristband_pass_id` = `wp`.`id`))) where (`rwp`.`reservation_id` = `r`.`id`))) AS `name_reservation`,coalesce((((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`) + (select sum(`rt3`.`quantity`) from `reservation_pack_wristbands` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) + (select sum(`rt4`.`quantity`) from `reservation_packs` `rt4` where ((`rt4`.`reservation_id` = `r`.`id`) and (`rt4`.`ticket_type_id` = 1)) group by `rt4`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`)) AS `ADU`,coalesce(((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `CHD`,coalesce(((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 3)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `INF`,((((((ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt3`.`quantity`) from `reservation_pack_wristbands` `rt3` where (`rt3`.`reservation_id` = `r`.`id`)),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),0)) AS `TOT`,`r`.`booking_fee` AS `booking_fee`,`r`.`paypal` AS `paypal`,`r`.`comments` AS `comments`,`r`.`deleted_at` AS `deleted_at`,`r`.`created_at` AS `created_at`,`r`.`created_at` AS `fecha`,`r`.`updated_at` AS `updated_at` from (((((((`reservations` `r` left join `reservation_tickets` `rt` on((`r`.`id` = `rt`.`reservation_id`))) left join `resellers` `rl` on((`r`.`reseller_id` = `rl`.`id`))) left join `channels` `rc` on((`r`.`channel_id` = `rc`.`id`))) left join `customers` `c` on((`r`.`customer_id` = `c`.`id`))) left join `reservation_types` `rtp` on((`r`.`reservation_type_id` = `rtp`.`id`))) left join `packs` `pa` on((`r`.`pack_id` = `pa`.`id`))) left join `passes` `p` on((`r`.`pass_id` = `p`.`id`))) group by `r`.`id` order by `r`.`id` desc
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
         CREATE OR REPLACE VIEW `viewreservations` AS
         select `r`.`id` AS `id`,`rtp`.`name` AS `type`,`rtp`.`acronym` AS `acronym`,`r`.`reference_number` AS `reference_number`,`r`.`phone` AS `phone`,`r`.`reservation_number` AS `reservation_number`,`r`.`name` AS `name`,`r`.`email` AS `email`,`r`.`canceled_date` AS `canceled_date`,`r`.`created_by` AS `created_by`,`r`.`canceled_by` AS `canceled_by`,`r`.`discount` AS `discount`,`r`.`identification_number` AS `identification_number`,`r`.`canceled_reason` AS `canceled_reason`,`r`.`finished` AS `finished`,`r`.`channel_id` AS `channel_id`,`rc`.`name` AS `channel`,`r`.`customer_id` AS `customer_id`,`c`.`name` AS `customer`,`rl`.`company` AS `company`,`r`.`pass_id` AS `pass_id`,`r`.`pack_id` AS `pack_id`,`r`.`reservation_type_id` AS `reservation_type_id`,`r`.`promocode_id` AS `promocode_id`,`r`.`reconcile` AS `reconcile`,`pa`.`title` AS `pack`,coalesce((select concat(`s`.`name`,' | ',`p`.`datetime`) AS `passe` from (`passes` `p` left join `products` `s` on((`p`.`product_id` = `s`.`id`))) where (`p`.`id` = `r`.`pass_id`)),`pa`.`title`,(select `wp`.`title` from (`reservation_wristband_pass` `rwp` left join `wristband_passes` `wp` on((`rwp`.`wristband_pass_id` = `wp`.`id`))) where (`rwp`.`reservation_id` = `r`.`id`))) AS `name_reservation`,coalesce((((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`) + (select sum(`rt3`.`quantity`) from `reservation_pack_wristbands` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) + (select sum(`rt4`.`quantity`) from `reservation_packs` `rt4` where ((`rt4`.`reservation_id` = `r`.`id`) and (`rt4`.`ticket_type_id` = 1)) group by `rt4`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`)) AS `ADU`,coalesce(((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `CHD`,coalesce(((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`) + (select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 3)) group by `rt2`.`ticket_type_id`)),(select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 3)) group by `rt1`.`ticket_type_id`),(select sum(`rt3`.`quantity`) from `reservation_wristband_pass` `rt3` where (`rt3`.`reservation_id` = `r`.`id`))) AS `INF`,((((((ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 1)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt2`.`quantity`) from `reservation_packs` `rt2` where ((`rt2`.`reservation_id` = `r`.`id`) and (`rt2`.`ticket_type_id` = 2)) group by `rt2`.`ticket_type_id`),0)) + ifnull((select sum(`rt3`.`quantity`) from `reservation_pack_wristbands` `rt3` where (`rt3`.`reservation_id` = `r`.`id`)),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 1)) group by `rt1`.`ticket_type_id`),0)) + ifnull((select sum(`rt1`.`quantity`) from `reservation_tickets` `rt1` where ((`rt1`.`reservation_id` = `r`.`id`) and (`rt1`.`ticket_type_id` = 2)) group by `rt1`.`ticket_type_id`),0)) AS `TOT`,`r`.`booking_fee` AS `booking_fee`,`r`.`paypal` AS `paypal`,`r`.`comments` AS `comments`,`r`.`deleted_at` AS `deleted_at`,`r`.`created_at` AS `created_at`,`r`.`created_at` AS `fecha`,`r`.`updated_at` AS `updated_at` from (((((((`reservations` `r` left join `reservation_tickets` `rt` on((`r`.`id` = `rt`.`reservation_id`))) left join `resellers` `rl` on((`r`.`reseller_id` = `rl`.`id`))) left join `channels` `rc` on((`r`.`channel_id` = `rc`.`id`))) left join `customers` `c` on((`r`.`customer_id` = `c`.`id`))) left join `reservation_types` `rtp` on((`r`.`reservation_type_id` = `rtp`.`id`))) left join `packs` `pa` on((`r`.`pack_id` = `pa`.`id`))) left join `passes` `p` on((`r`.`pass_id` = `p`.`id`))) group by `r`.`id` order by `r`.`id` desc
        ");
    }
}