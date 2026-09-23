<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PromotionApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Tenant A',
            'code' => 'A',
            'tier' => 'standard',
            'supports_branching' => false,
            'status' => 'active',
        ]);
    }

    protected function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'tenant_id' => $this->tenant->id,
            'role' => $role,
            'is_active' => true,
        ]);
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Holiday Special',
            'description' => 'Discount for the holidays.',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
            'reason_for_implementation' => 'Increase occupancy during the holiday season.',
        ], $overrides);
    }

    public function test_promotion_is_created_as_pending_and_stamped_with_the_current_user(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)->post(route('promotions.store'), $this->payload())->assertRedirect();

        $promotion = Promotion::withoutGlobalScopes()->firstOrFail();

        $this->assertSame('Pending', $promotion->status);
        $this->assertSame($staff->id, $promotion->implemented_by);
        $this->assertNull($promotion->approved_by);
    }

    public function test_reason_for_implementation_is_required(): void
    {
        $this->actingAs($this->user('staff'))
            ->post(route('promotions.store'), $this->payload(['reason_for_implementation' => '']))
            ->assertSessionHasErrors('reason_for_implementation');

        $this->assertSame(0, Promotion::withoutGlobalScopes()->count());
    }

    public function test_manager_can_approve_a_pending_promotion(): void
    {
        $this->actingAs($this->user('staff'))->post(route('promotions.store'), $this->payload());

        $promotion = Promotion::withoutGlobalScopes()->firstOrFail();
        $manager = $this->user('manager');

        $this->actingAs($manager)
            ->post(route('promotions.approve', $promotion), ['review_notes' => 'Looks good.'])
            ->assertRedirect();

        $promotion->refresh();

        $this->assertSame('Approved', $promotion->status);
        $this->assertSame($manager->id, $promotion->approved_by);
        $this->assertNotNull($promotion->reviewed_at);
    }

    public function test_rejection_requires_review_notes(): void
    {
        $this->actingAs($this->user('staff'))->post(route('promotions.store'), $this->payload());

        $promotion = Promotion::withoutGlobalScopes()->firstOrFail();

        $this->actingAs($this->user('manager'))
            ->post(route('promotions.reject', $promotion), [])
            ->assertSessionHasErrors('review_notes');

        $this->assertSame('Pending', $promotion->refresh()->status);
    }

    public function test_staff_cannot_approve_promotions(): void
    {
        $this->actingAs($this->user('staff'))->post(route('promotions.store'), $this->payload());

        $promotion = Promotion::withoutGlobalScopes()->firstOrFail();

        $this->actingAs($this->user('staff'))
            ->post(route('promotions.approve', $promotion))
            ->assertForbidden();

        $this->assertSame('Pending', $promotion->refresh()->status);
    }

    public function test_editing_an_approved_promotion_returns_it_to_pending(): void
    {
        $this->actingAs($this->user('staff'))->post(route('promotions.store'), $this->payload());

        $promotion = Promotion::withoutGlobalScopes()->firstOrFail();

        $this->actingAs($this->user('manager'))->post(route('promotions.approve', $promotion));

        $this->actingAs($this->user('admin'))
            ->put(route('promotions.update', $promotion), $this->payload(['title' => 'Holiday Special v2']))
            ->assertRedirect();

        $promotion->refresh();

        $this->assertSame('Pending', $promotion->status);
        $this->assertNull($promotion->approved_by);
    }
}
