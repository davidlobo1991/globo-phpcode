<?php

use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Menu;

Menu::macro('sidebar', function () {
    return Menu::adminlteMenu()
        ->route('home', '<i class="fa fa-home"></i><span>' . trans('menu.home') . '</span>')
        ->addIf(Auth::user()->role->role_permission('create_reservations'),
        Menu::adminlteSubmenu('<span>' . trans('menu.reservationscrete') . '</span>', '<i class="fa fa-ticket"></i>')
        ->route('reservations.create', '<i class="fa fa-ticket"></i><span>' . trans('menu.reservationsproduct' ), 'id=1')
        ->route('reservationspacks.create', '<i class="fa fa-bars"></i><span>' . trans('menu.reservationspack' ), '1')
/*        ->route('reservationsWristbands.create', '<i class="fa fa-circle-o"></i><span>' . trans('menu.reservationswristbans' ))*/
        )
        
        ->addIf(Auth::user()->role->role_permission('view_reservations'),
            Menu::adminlteSubmenu('<span>' . trans('menu.reservationsmanager') . '</span>', '<i class="fa fa-ticket"></i>')
                ->route('reservations.index',  '<i class="fa fa-money"></i><span>' . trans('menu.reservationsFinished'))
                ->route('reservations.unfinished', '<i class="fa fa-th-list"></i><span>' . trans('menu.reservationsUnfinished'))
                ->route('reservations.canceled', '<i class="fa fa-ban"></i><span>' . trans('menu.reservationsCanceled'))
                ->route('reservations.deleted', '<i class="fa fa-trash"></i><span>' .  trans('menu.reservationsDeleted'))
        )

        ->routeIf(Auth::user()->role->role_permission('view_payment'),'payments.index',  '<i class="fa fa-money"></i><span>' . trans('menu.payments'))
        ->routeIf(Auth::user()->role->role_permission('view_global'),'global.index',  '<i class="fa fa-wrench"></i><span>' . trans('menu.global'))

        ->addIf(Auth::user()->role->role_permission('view_shows') || Auth::user()->role->role_permission('view_passes'),Menu::adminlteSubmenu('<span>' . trans('menu.products') . '</span>', '<i class="fa fa-calendar-o"></i>')
        ->routeIf(Auth::user()->role->role_permission('view_pack'),'products.index', trans('menu.products'))
        ->routeIf(Auth::user()->role->role_permission('view_passes'),'passes.index', trans('menu.passes'))
        ->routeIf(Auth::user()->role->role_permission('view_seatypes'),'seatTypes.index', trans('menu.seatTypes'))
        ->routeIf(Auth::user()->role->role_permission('view_tickettype'),'ticketTypes.index', trans('menu.ticketTypes'))
        ->routeIf(Auth::user()->role->role_permission('manage_providers'),'providers.index', trans('menu.providers'))
        )

        ->routeIf(Auth::user()->role->role_permission('view_promocodes'),'promocodes.index', '<i class="fa fa-tag"></i><span>' . trans('menu.promocodes') .'</span>')

        ->routeIf(Auth::user()->role->role_permission('view_promocodes'),'packs.index', '<i class="fa fa-bars"></i><span>' . trans('menu.packs') .'</span>')

      /*  ->addIf(Auth::user()->role->role_permission('view_wristbands'),
            Menu::adminlteSubmenu('<span>' . trans('menu.wristband') . '</span>', '<i class="fa fa-circle-o"></i>')
            ->routeIf(Auth::user()->role->role_permission('view_wristbands'),'wristband.index',  '<i class="fa fa-circle-o-notch"></i><span>' . trans('menu.wristband'))
            ->routeIf(Auth::user()->role->role_permission('view_wristband_passes'),'wristband-pass.index', '<i class="fa fa-ticket"></i><span>' . trans('menu.wristband-passes'))
        )*/

        ->routeIf(Auth::user()->role->role_permission('view_customers'),'customers.index', '<i class="fa fa-user"></i><span>' . trans('menu.customers') . '</span>')
        // ->route('resellers.index', '<i class="fa fa-building-o"></i><span>' . trans('menu.resellers') . '</span>')
       
        
        
        ->addIf(Auth::user()->role->role_permission('manage_users') ||
        Auth::user()->role->role_permission('manage_roles') ||
        Auth::user()->role->role_permission('view_system_logs'),
        Menu::adminlteSubmenu('<span>' . trans('menu.usersAndRoles') . '</span>', '<i class="fa fa-lock"></i>')
            ->action('UsersController@index', trans('menu.users'))
            ->action('RolesController@index', trans('menu.roles'))
            ->action('\Rap2hpoutre\LaravelLogViewer\LogViewerController@index', trans('menu.log'))
            
        )->setActiveFromRequest();
});

Menu::macro('adminlteMenu', function () {
    return Menu::new()
        ->addClass('sidebar-menu');
});

Menu::macro('adminlteSubmenu', function ($submenuName, $icon) {
    return Menu::new()->prepend('<a href="#">' . $icon . '<span> ' . $submenuName . '</span> <i class="fa fa-angle-left pull-right"></i></a>')
        ->addParentClass('treeview')->addClass('treeview-menu');
});

Menu::macro('adminlteSeparator', function ($title) {
    return Html::raw($title)->addParentClass('header');
});

/* https://github.com/spatie/laravel-menu */
