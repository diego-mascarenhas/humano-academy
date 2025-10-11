<?php

namespace Idoneo\HumanoAcademy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallAcademyCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'academy:install {--fresh : Drop existing tables before migrating}';

    /**
     * The console command description.
     */
    protected $description = 'Install Humano Academy module (migrations, permissions, etc.)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🎓 Installing Humano Academy...');
        $this->newLine();

        // Step 1: Migrations
        if ($this->option('fresh'))
        {
            $this->warn('⚠️  Dropping existing Academy tables...');

            if ($this->confirm('This will delete all Academy data. Continue?'))
            {
                Artisan::call('migrate:fresh', ['--path' => 'database/migrations']);
            } else
            {
                $this->error('Installation cancelled.');

                return 1;
            }
        } else
        {
            $this->info('📦 Running migrations...');
            Artisan::call('migrate', ['--path' => 'database/migrations']);
        }

        $this->info('✅ Migrations completed');
        $this->newLine();

        // Step 2: Storage Link
        $this->info('🔗 Creating storage link...');
        Artisan::call('storage:link');
        $this->info('✅ Storage link created');
        $this->newLine();

        // Step 3: Permissions
        $this->info('🔐 Installing permissions...');
        Artisan::call('db:seed', [
            '--class' => 'Idoneo\\HumanoAcademy\\Database\\Seeders\\AcademyPermissionsSeeder',
        ]);
        $this->info('✅ Permissions installed');
        $this->newLine();

        // Summary
        $this->info('✅ Humano Academy installed successfully!');
        $this->newLine();
        $this->info('📋 Next steps:');
        $this->info('1. Configure the seeder: database/seeders/TeamLizamaSeeder.php');
        $this->info('2. Run the seeder: php artisan db:seed --class=TeamLizamaSeeder');
        $this->info('3. Visit: /academy to see your courses');
        $this->newLine();

        return 0;
    }
}
