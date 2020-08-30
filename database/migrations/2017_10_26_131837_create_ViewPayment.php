<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW  viewpayment AS 
        SELECT payment_method_reservations.id,
        users.id as user_id,
        users.name as user_name,
        users.email as user_email,
        customers.name as customers_name,
        customers.email as customers_email,
        customers.phone as customers_phone,
        payment_method_reservations.total,

        /*..muestras..*/ 
        (SELECT SUM(shows.commission)  FROM `reservation_wristband_pass` 
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id
        inner join wristband_passes on wristband_passes.wristband_id = show_wristband_pass.wristband_pass_id
        inner join shows on shows.id = show_wristband_pass.show_id
        where reservation_wristband_pass.reservation_id = reservations.id) as commision_wristaband,
            
        (SELECT sum(reservation_packs.unit_price)* reservation_packs.quantity as total FROM `reservation_packs` inner join shows on reservation_packs.show_id = shows.id where reservation_packs.reservation_id= reservations.id) as totalmenoscomision, 
        
        (abs(payment_method_reservations.total - (SELECT round ((payment_method_reservations.total * SUM(shows.commission)) /100,2) FROM `reservation_wristband_pass`
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id 
        
        inner join shows on shows.id = show_wristband_pass.show_id 
        inner join payment_method_reservations on payment_method_reservations.reservation_id = reservation_wristband_pass.reservation_id 
        where reservation_wristband_pass.reservation_id = reservations.id GROUP by payment_method_reservations.id)
        )) as comisionpulseraspirates,    
        /*..muestras..*/ 


        /*..commission..*/  
        coalesce(
        (SELECT shows.commission AS passes
        FROM (passes LEFT JOIN shows ON (passes.show_id = shows.id)) WHERE passes.id =reservations.pass_id) 
        ,    
        (SELECT SUM(shows.commission)  FROM `packs`
        inner join pack_shows on packs.id = pack_shows.pack_id
        inner join shows on pack_shows.show_id = shows.id
        where packs.id= reservations.pack_id
        GROUP by packs.id) 
        ,
        (SELECT SUM(shows.commission)  FROM `reservation_wristband_pass` 
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id
        inner join wristband_passes on wristband_passes.wristband_id = show_wristband_pass.wristband_pass_id
        inner join shows on shows.id = show_wristband_pass.show_id
        where reservation_wristband_pass.reservation_id = reservations.id)
        )as commission,
        /*..fin commission..*/  

        
        /*..totalcomision..*/           
        coalesce(
        (round((payment_method_reservations.total * (SELECT shows.commission AS passes
        FROM (passes LEFT JOIN shows ON (passes.show_id = shows.id)) WHERE passes.id =reservations.pass_id))/100,2))
        ,
        (round(((SELECT sum(reservation_packs.unit_price)* reservation_packs.quantity as total FROM `reservation_packs` inner join shows on reservation_packs.show_id = shows.id where reservation_packs.reservation_id= reservations.id) * (SELECT SUM(shows.commission)  FROM `packs`
        inner join pack_shows on packs.id = pack_shows.pack_id
        inner join shows on pack_shows.show_id = shows.id
        where packs.id= reservations.pack_id
        GROUP by packs.id) 
        )/100,2))
        ,
        (SELECT round ((payment_method_reservations.total * SUM(shows.commission)) /100,2) FROM `reservation_wristband_pass`
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id 
        
        inner join shows on shows.id = show_wristband_pass.show_id 
        inner join payment_method_reservations on payment_method_reservations.reservation_id = reservation_wristband_pass.reservation_id 
        where reservation_wristband_pass.reservation_id = reservations.id GROUP by payment_method_reservations.id)
        ) as totalcomision,
        /*..fin totalcomision..*/ 

        (SELECT round ((payment_method_reservations.total * SUM(shows.commission)) /100,2) FROM `reservation_wristband_pass`
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id 
        inner join shows on shows.id = show_wristband_pass.show_id 
        inner join payment_method_reservations on payment_method_reservations.reservation_id = reservation_wristband_pass.reservation_id 
        where reservation_wristband_pass.reservation_id = reservations.id GROUP by payment_method_reservations.id) as totalcomisionpulsera,

        /*..comisionpirates..*/     
        coalesce(
        (abs((round((payment_method_reservations.total * (SELECT shows.commission AS passes FROM (passes LEFT JOIN shows ON (passes.show_id = shows.id)) WHERE passes.id =reservations.pass_id))/100,2)) 
        - (round(payment_method_reservations.total))))
        ,
        (abs( (round(((SELECT sum(reservation_packs.unit_price)* reservation_packs.quantity as total FROM `reservation_packs` inner join shows on reservation_packs.show_id = shows.id where reservation_packs.reservation_id= reservations.id) * (SELECT SUM(shows.commission)  FROM `packs`
        inner join pack_shows on packs.id = pack_shows.pack_id
        inner join shows on pack_shows.show_id = shows.id
        where packs.id= reservations.pack_id
        GROUP by packs.id) 
        )/100,2)) - (round((SELECT sum(reservation_packs.unit_price)* reservation_packs.quantity as total FROM `reservation_packs` inner join shows on reservation_packs.show_id = shows.id where reservation_packs.reservation_id= reservations.id) ))))     
        ,
        (abs(payment_method_reservations.total - (SELECT round ((payment_method_reservations.total * SUM(shows.commission)) /100,2) FROM `reservation_wristband_pass`
        inner join show_wristband_pass on reservation_wristband_pass.wristband_pass_id = show_wristband_pass.wristband_pass_id 
        
        inner join shows on shows.id = show_wristband_pass.show_id 
        inner join payment_method_reservations on payment_method_reservations.reservation_id = reservation_wristband_pass.reservation_id 
        where reservation_wristband_pass.reservation_id = reservations.id GROUP by payment_method_reservations.id)
        ))
        ) as comisionpirates,
        /*..fin comisionpirates..*/ 



        reservations.booking_fee, 
        reservations.paypal,    
        reservations.reservation_number,
        reservations.reference_number,
        resellers.company,
        channels.name as channels,
        payment_method_reservations.reservation_id,
        payment_method_reservations.payment_method_id,
        payment_methods.name as method,
        reservations.pass_id,
        reservations.pack_id,
        passes.datetime as pass_datetime,
        reservations.reservation_type_id AS reservation_type_id,
        `reservation_types`.`name` AS `type`,
        (SELECT shows.id AS passes
        FROM (passes LEFT JOIN shows ON (passes.show_id = shows.id)) WHERE passes.id =reservations.pass_id) as show_id,
        
        (SELECT wp.id FROM reservation_wristband_pass as rwp LEFT JOIN wristband_passes as wp ON (rwp.wristband_pass_id = wp.id)where  rwp.reservation_id = reservations.id) as wristaband_id,
        coalesce(
        (SELECT CONCAT(shows.name, ' | ', DATE_FORMAT(passes.datetime,'%d/%m/%Y %H:%i')  ) AS passe
        FROM (passes LEFT JOIN shows ON (passes.show_id = shows.id)) WHERE passes.id =reservations.pass_id)
        ,(SELECT packs.title
        FROM packs WHERE packs.id =reservations.pack_id)
        ,(SELECT wp.title FROM reservation_wristband_pass as rwp LEFT JOIN wristband_passes as wp ON (rwp.wristband_pass_id = wp.id)where  rwp.reservation_id = reservations.id)
        ) AS `name_reservation`,
        payment_method_reservations.deleted_at,
        payment_method_reservations.created_at,
        payment_method_reservations.updated_at
        FROM users 
        left JOIN payment_method_reservations ON users.id = payment_method_reservations.user_id 
        left JOIN reservations ON payment_method_reservations.reservation_id = reservations.id 
        left JOIN reservation_wristband_pass ON reservation_wristband_pass.reservation_id = reservations.id 
        left JOIN reservation_types ON reservations.reservation_type_id = reservation_types.id 
        left JOIN channels on channels.id = reservations.channel_id
        LEFT join payment_methods on payment_methods.id = payment_method_reservations.payment_method_id
        left JOIN customers on customers.id = reservations.customer_id
        left JOIN passes on passes.id = reservations.pass_id 
        
        left JOIN resellers on resellers.id = reservations.reseller_id
        
        where payment_method_reservations.deleted_at IS  NULL and reservations.finished = 1 

");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
