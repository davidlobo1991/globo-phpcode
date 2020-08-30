<?php namespace App\Http\Notification\Facade;

use Illuminate\Support\Facades\Facade;

class Toastr extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'toastr';
    }

}
