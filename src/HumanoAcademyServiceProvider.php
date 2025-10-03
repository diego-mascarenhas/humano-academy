<?php

namespace Idoneo\HumanoAcademy;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HumanoAcademyServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package ): void
	{
		
			->name('humano-academy')
			->hasViews()
			->hasRoute('web');
	}

	public function bootingPackage(): void
	{
		parent::bootingPackage();

		try {
			if (Schema::hasTable('modules')) {
				if (class_exists(\App\Models\Module::class)) {
					\App\Models\Module::updateOrCreate([ 'key' => 'academy' ], [
						'name' => 'Academy',
						'icon' => 'ti ti-school',
						'description' => 'Learning content and courses',
						'is_core' => false,
						'status' => 1,
					]);
				}
			}
		} catch (\Throwable ) {
			Log::debug('HumanoAcademy: module registration skipped: ' . ->getMessage());
		}
	}
}
