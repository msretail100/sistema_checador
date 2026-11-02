<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_users_to_their_role_dashboard(): void
    {
        $worker = User::factory()->create([
            'username' => 'ana',
            'password' => 'password-very-long',
        ]);

        $this->post(route('login.store'), [
            'username' => 'ana',
            'password' => 'password-very-long',
        ])->assertRedirect(route('worker.dashboard'));

        $this->assertAuthenticatedAs($worker);
    }

    public function test_disabled_accounts_cannot_sign_in(): void
    {
        User::factory()->create([
            'username' => 'disabled',
            'password' => 'password-very-long',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'username' => 'disabled',
            'password' => 'password-very-long',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_attendance_uses_the_server_time_and_request_ip(): void
    {
        Carbon::setTestNow('2026-03-17 08:42:11');
        $worker = User::factory()->create();

        $this->actingAs($worker)
            ->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
            ->post(route('attendance.store'), [
                'latitud' => 19.432608,
                'longitud' => -99.133209,
                'hora_servidor' => '2000-01-01 00:00:00',
                'ip_address' => '198.51.100.1',
                'tipo' => 'salida',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $record = AttendanceRecord::query()->sole();

        $this->assertSame('entrada', $record->tipo);
        $this->assertSame('203.0.113.42', $record->ip_address);
        $this->assertSame('19.4326080', $record->latitud);
        $this->assertSame('-99.1332090', $record->longitud);
        $this->assertSame('2026-03-17 08:42:11', $record->hora_servidor->format('Y-m-d H:i:s'));
    }

    public function test_worker_can_only_register_one_entry_and_one_exit_per_day(): void
    {
        Carbon::setTestNow('2026-03-17 08:00:00');
        $worker = User::factory()->create();
        $payload = ['latitud' => 19.4, 'longitud' => -99.1];

        $this->actingAs($worker)->post(route('attendance.store'), $payload)->assertRedirect();
        Carbon::setTestNow('2026-03-17 17:00:00');
        $this->post(route('attendance.store'), $payload)->assertRedirect();
        Carbon::setTestNow('2026-03-17 17:01:00');
        $this->post(route('attendance.store'), $payload)->assertSessionHasErrors('attendance');

        $this->assertDatabaseCount('attendance_records', 2);
        $this->assertDatabaseHas('attendance_records', ['user_id' => $worker->id, 'tipo' => 'entrada']);
        $this->assertDatabaseHas('attendance_records', ['user_id' => $worker->id, 'tipo' => 'salida']);
    }

    public function test_invalid_coordinates_are_rejected(): void
    {
        $worker = User::factory()->create();

        $this->actingAs($worker)
            ->post(route('attendance.store'), ['latitud' => 91, 'longitud' => 181])
            ->assertSessionHasErrors(['latitud', 'longitud']);

        $this->assertDatabaseCount('attendance_records', 0);
    }

    public function test_admin_cannot_view_root_only_annual_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('root.reports', 'yearly'))
            ->assertForbidden();
    }

    public function test_admin_can_create_workers_but_cannot_assign_another_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'name' => 'Luis Pérez',
            'username' => 'luis_perez',
            'password' => 'secure-password-2026',
            'password_confirmation' => 'secure-password-2026',
        ];

        $this->actingAs($admin)
            ->post(route('users.store'), [...$payload, 'role' => 'root'])
            ->assertSessionHasErrors('role');

        $this->post(route('users.store'), $payload)->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['username' => 'luis_perez', 'role' => 'worker']);
    }

    public function test_last_active_root_cannot_be_disabled_or_demoted(): void
    {
        $root = User::factory()->create(['role' => 'root']);

        $this->actingAs($root)
            ->patch(route('users.toggle', $root))
            ->assertSessionHasErrors('user');

        $this->put(route('users.update', $root), [
            'name' => $root->name,
            'username' => $root->username,
            'role' => 'worker',
        ])->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $root->id, 'role' => 'root', 'is_active' => true]);
    }

    public function test_role_dashboards_render_successfully(): void
    {
        $root = User::factory()->create(['role' => 'root']);
        $this->actingAs($root)->get(route('root.dashboard'))->assertOk();
        $this->get(route('root.reports', 'yearly'))->assertOk();

        $worker = User::factory()->create(['role' => 'worker']);
        $this->actingAs($worker)->get(route('worker.dashboard'))->assertOk();
    }

    public function test_seed_creates_root_with_a_hashed_configured_password(): void
    {
        config([
            'attendance.root_name' => 'Cuenta Root',
            'attendance.root_username' => 'superadmin',
            'attendance.root_password' => 'a-strong-password-2026',
        ]);

        $this->seed(DatabaseSeeder::class);

        $root = User::query()->sole();
        $this->assertSame('root', $root->role);
        $this->assertTrue(Hash::check('a-strong-password-2026', $root->password));
    }

    public function test_admin_management_pages_are_available_only_to_admin_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.reports', 'monthly'))->assertOk();
        $this->get(route('users.index'))->assertOk();
        $this->get(route('users.create'))->assertOk();
        $this->get(route('root.dashboard'))->assertForbidden();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
