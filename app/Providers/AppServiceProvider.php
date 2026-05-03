<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // @active('route.name') → 'active' class if current route matches
        Blade::directive('active', function ($expression) {
            return "<?php echo (request()->routeIs({$expression})) ? 'active' : ''; ?>";
        });

        // @role('role_name') → check if user has given role
        // Supports comma-separated: @role('admin,guru')
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole(array_map('trim', explode(',', {$expression})))): ?>";
        });
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // Carbon locale
        \Carbon\Carbon::setLocale('id');

        // Load settings from DB if table exists (avoids edit .env and restart)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
                
                config([
                    'school.name'      => $settings['school_name'] ?? config('school.name'),
                    'school.address'   => $settings['school_address'] ?? config('school.address'),
                    'school.phone'     => $settings['school_phone'] ?? config('school.phone'),
                    'school.email'     => $settings['school_email'] ?? config('school.email'),
                    'school.principal' => $settings['school_principal'] ?? config('school.principal'),
                    
                    'attendance.start_time'     => $settings['att_start_time'] ?? config('attendance.start_time'),
                    'attendance.late_limit'     => $settings['att_late_limit'] ?? config('attendance.late_limit'),
                    'attendance.end_time'       => $settings['att_end_time'] ?? config('attendance.end_time'),
                    'attendance.min_percentage' => $settings['att_min_pct'] ?? config('attendance.min_percentage'),
                    'attendance.working_days'   => $settings['att_working_days'] ?? config('attendance.working_days'),
                ]);
            }
        } catch (\Exception $e) {
            // Table doesn't exist or DB is offline (e.g. during initial setup/migrations)
        }

        // Share school config with all views
        view()->share('schoolConfig', config('school'));
    }
}
