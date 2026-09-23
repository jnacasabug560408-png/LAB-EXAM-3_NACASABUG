<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Guest;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CrmSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public static function tenantPages(): array
    {
        return [
            'dashboard' => ['/'],
            'guests' => ['/guests'],
            'guest form' => ['/guests/create'],
            'reservations' => ['/reservations'],
            'reservation form' => ['/reservations/create'],
            'feedback' => ['/feedback?status=unresolved'],
            'feedback form' => ['/feedback/create'],
            'interactions' => ['/interactions'],
            'interaction form' => ['/interactions/create'],
            'sales' => ['/sales'],
            'pos form' => ['/sales/create'],
            'actions' => ['/actions'],
            'action form' => ['/actions/create'],
            'promotions' => ['/promotions'],
            'promotion form' => ['/promotions/create'],
            'daily sales report' => ['/reports/sales?period=daily'],
            'weekly sales report' => ['/reports/sales?period=weekly'],
            'monthly sales report' => ['/reports/sales?period=monthly'],
        ];
    }

    #[DataProvider('tenantPages')]
    public function test_tenant_pages_render_for_every_tenant(string $path): void
    {
        foreach (['admin@azurebay.test', 'admin@bluewater.test', 'admin@coral.test'] as $email) {
            $user = User::where('email', $email)->firstOrFail();

            $this->actingAs($user)->get($path)->assertOk();
        }
    }

    public function test_tenant_c_branch_pages_render(): void
    {
        $user = User::where('email', 'admin@coral.test')->firstOrFail();

        $this->actingAs($user)->get('/branches')->assertOk();
        $this->actingAs($user)->get('/branches/create')->assertOk();
    }

    public function test_detail_pages_render(): void
    {
        $user = User::where('email', 'admin@coral.test')->firstOrFail();

        $this->actingAs($user);

        $guest = Guest::where('tenant_id', $user->tenant_id)->firstOrFail();
        $reservation = Reservation::where('tenant_id', $user->tenant_id)->firstOrFail();
        $feedback = Feedback::where('tenant_id', $user->tenant_id)->firstOrFail();
        $promotion = Promotion::where('tenant_id', $user->tenant_id)->firstOrFail();

        $this->get("/guests/{$guest->id}")->assertOk();
        $this->get("/guests/{$guest->id}/edit")->assertOk();
        $this->get("/reservations/{$reservation->id}")->assertOk();
        $this->get("/reservations/{$reservation->id}/edit")->assertOk();
        $this->get("/feedback/{$feedback->id}")->assertOk();
        $this->get("/promotions/{$promotion->id}")->assertOk();
        $this->get("/promotions/{$promotion->id}/edit")->assertOk();
    }

    public function test_master_pages_render(): void
    {
        $master = User::where('role', 'master')->firstOrFail();

        $this->actingAs($master);

        $this->get('/')->assertOk();
        $this->get('/master/tenants')->assertOk();
        $this->get('/master/tenants/create')->assertOk();
        $this->get('/master/tenants/1')->assertOk();
        $this->get('/master/tenants/1/edit')->assertOk();
        $this->get('/master/subscriptions')->assertOk();
        $this->get('/master/subscriptions/create')->assertOk();
        $this->get('/master/subscriptions/1/edit')->assertOk();
    }

    public function test_check_in_and_check_out_flow(): void
    {
        $user = User::where('email', 'admin@azurebay.test')->firstOrFail();
        $this->actingAs($user);

        $reservation = Reservation::where('status', 'confirmed')->firstOrFail();

        $this->post("/reservations/{$reservation->id}/check-in")->assertRedirect();
        $this->assertSame('checked_in', $reservation->refresh()->status);

        $this->post("/reservations/{$reservation->id}/check-out")->assertRedirect();
        $this->assertSame('checked_out', $reservation->refresh()->status);
        $this->assertTrue($reservation->sales()->where('category', 'room')->count() >= 1);
    }
}
