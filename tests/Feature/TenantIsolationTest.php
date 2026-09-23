<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function tenant(string $code, bool $branching = false): Tenant
    {
        return Tenant::create([
            'name' => "Tenant {$code}",
            'code' => $code,
            'tier' => 'standard',
            'supports_branching' => $branching,
            'status' => 'active',
        ]);
    }

    protected function user(?Tenant $tenant, string $role = 'staff'): User
    {
        return User::create([
            'name' => "{$role} user",
            'email' => $role.'-'.($tenant?->code ?? 'master').'-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant?->id,
            'role' => $role,
            'is_active' => true,
        ]);
    }

    protected function guest(Tenant $tenant, string $lastName): Guest
    {
        return Guest::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => $lastName,
            'status' => 'active',
        ]);
    }

    public function test_tenant_user_only_sees_their_own_guests(): void
    {
        $tenantA = $this->tenant('A');
        $tenantB = $this->tenant('B');

        $this->guest($tenantA, 'AlphaGuest');
        $this->guest($tenantB, 'BetaGuest');

        $response = $this->actingAs($this->user($tenantA))->get(route('guests.index'));

        $response->assertOk()
            ->assertSee('AlphaGuest')
            ->assertDontSee('BetaGuest');
    }

    public function test_tenant_user_cannot_open_another_tenants_guest(): void
    {
        $tenantA = $this->tenant('A');
        $tenantB = $this->tenant('B');

        $foreignGuest = $this->guest($tenantB, 'BetaGuest');

        $this->actingAs($this->user($tenantA))
            ->get(route('guests.show', $foreignGuest))
            ->assertNotFound();
    }

    public function test_created_records_are_stamped_with_the_active_tenant(): void
    {
        $tenantA = $this->tenant('A');

        $this->actingAs($this->user($tenantA, 'admin'))
            ->post(route('guests.store'), [
                'first_name' => 'New',
                'last_name' => 'Guest',
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertSame(
            $tenantA->id,
            Guest::withoutGlobalScopes()->where('last_name', 'Guest')->value('tenant_id')
        );
    }

    public function test_master_can_switch_tenant_perspective(): void
    {
        $tenantA = $this->tenant('A');
        $tenantB = $this->tenant('B');

        $this->guest($tenantA, 'AlphaGuest');
        $this->guest($tenantB, 'BetaGuest');

        $master = $this->user(null, 'master');

        $this->actingAs($master)
            ->post(route('context.tenant'), ['tenant_id' => $tenantB->id])
            ->assertRedirect();

        $this->actingAs($master)
            ->get(route('guests.index'))
            ->assertOk()
            ->assertSee('BetaGuest')
            ->assertDontSee('AlphaGuest');
    }

    public function test_non_master_cannot_reach_master_tenant_admin(): void
    {
        $tenantA = $this->tenant('A');

        $this->actingAs($this->user($tenantA, 'admin'))
            ->get(route('master.tenants.index'))
            ->assertForbidden();
    }

    public function test_branching_is_restricted_to_tenants_with_multi_branch_support(): void
    {
        $tenantA = $this->tenant('A');
        $tenantC = $this->tenant('C', branching: true);

        $this->actingAs($this->user($tenantA, 'admin'))
            ->get(route('branches.index'))
            ->assertForbidden();

        $this->actingAs($this->user($tenantC, 'admin'))
            ->get(route('branches.index'))
            ->assertOk();
    }
}
