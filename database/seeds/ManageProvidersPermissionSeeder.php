<?php

use Illuminate\Database\Seeder;

use App\Permission;

class ManageProvidersPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() : void
    {
        $permission = Permission::create(
            [
                'permission' => 'manage_providers',
                'label' => 'manage_providers',
                'level' => 1,
                'group' => 1
            ]
        );

        $permission->roles()->sync(1);
    }
}
