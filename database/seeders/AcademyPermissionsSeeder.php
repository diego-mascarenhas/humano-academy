<?php

namespace HumanoAcademy\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AcademyPermissionsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Academy permissions
		Permission::firstOrCreate(['name' => 'academy.index']);
		Permission::firstOrCreate(['name' => 'academy.list']);
		Permission::firstOrCreate(['name' => 'academy.create']);
		Permission::firstOrCreate(['name' => 'academy.show']);
		Permission::firstOrCreate(['name' => 'academy.edit']);
		Permission::firstOrCreate(['name' => 'academy.store']);
		Permission::firstOrCreate(['name' => 'academy.update']);
		Permission::firstOrCreate(['name' => 'academy.destroy']);
	}
}

