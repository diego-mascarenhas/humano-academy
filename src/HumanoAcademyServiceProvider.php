<?php

namespace Idoneo\HumanoAcademy;

use Idoneo\HumanoAcademy\Models\SystemModule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HumanoAcademyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('humano-academy')
            ->hasViews()
            ->hasRoute('web')
            ->hasCommand(\Idoneo\HumanoAcademy\Console\InstallAcademyCommand::class);
    }

    public function bootingPackage(): void
    {
        parent::bootingPackage();

        try
        {
            if (Schema::hasTable('modules'))
            {
                if (class_exists(\App\Models\Module::class))
                {
                    \App\Models\Module::updateOrCreate(
                        ['key' => 'academy'],
                        [
                            'name' => 'Academy',
                            'icon' => 'ti ti-school',
                            'description' => 'Academy courses management module',
                            'is_core' => false,
                            'group' => 'content',
                            'order' => 2,
                            'status' => 1,
                        ],
                    );
                } else
                {
                    SystemModule::query()->updateOrCreate(
                        ['key' => 'academy'],
                        [
                            'name' => 'Academy',
                            'icon' => 'ti ti-school',
                            'description' => 'Learning content and courses',
                            'is_core' => false,
                            'status' => 1,
                        ],
                    );
                }
            }
        } catch (\Throwable $e)
        {
            Log::debug('HumanoAcademy: module registration skipped: '.$e->getMessage());
        }

        // Ensure permissions exist for menus and access
        try
        {
            if (Schema::hasTable('permissions') && class_exists(Permission::class))
            {
                // Run the permissions seeder
                if (class_exists(\HumanoAcademy\Database\Seeders\AcademyPermissionsSeeder::class))
                {
                    (new \HumanoAcademy\Database\Seeders\AcademyPermissionsSeeder)->run();
                }

                // Grant all academy permissions to admin role
                $adminRole = class_exists(Role::class) ? Role::where('name', 'admin')->first() : null;
                if ($adminRole)
                {
                    $academyPermissions = Permission::where('name', 'LIKE', 'academy.%')->pluck('name')->toArray();
                    if (! empty($academyPermissions))
                    {
                        $adminRole->givePermissionTo($academyPermissions);
                    }
                }
            }
        } catch (\Throwable $e)
        {
            Log::debug('HumanoAcademy: permissions setup skipped: '.$e->getMessage());
        }
    }
}
