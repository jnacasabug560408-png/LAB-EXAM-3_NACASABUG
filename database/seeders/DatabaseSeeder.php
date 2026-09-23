<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Branch;
use App\Models\Feedback;
use App\Models\Guest;
use App\Models\Interaction;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Master Administrator',
            'email' => 'master@innease.test',
            'password' => Hash::make('password'),
            'role' => 'master',
            'is_active' => true,
        ]);

        $tenantA = $this->tenant('Azure Bay Hotel', 'A', 'starter', false, 'Starter', 4999);
        $tenantB = $this->tenant('Bluewater Suites', 'B', 'standard', false, 'Standard', 9999);
        $tenantC = $this->tenant('Coral Group of Hotels', 'C', 'enterprise', true, 'Enterprise', 19999);

        $this->users($tenantA, 'azurebay');
        $this->users($tenantB, 'bluewater');
        $this->users($tenantC, 'coral');

        $this->branch($tenantA, 'Azure Bay Main', 'ABM', 40);

        $this->branch($tenantB, 'Bluewater Main', 'BWM', 60);

        $coralBranches = [
            $this->branch($tenantC, 'Coral Davao', 'CDV', 80),
            $this->branch($tenantC, 'Coral Cebu', 'CCB', 65),
            $this->branch($tenantC, 'Coral Manila', 'CML', 120),
        ];

        $this->operationalData($tenantA, Branch::withoutGlobalScope('tenant')->where('tenant_id', $tenantA->id)->get()->all(), 12);
        $this->operationalData($tenantB, Branch::withoutGlobalScope('tenant')->where('tenant_id', $tenantB->id)->get()->all(), 16);
        $this->operationalData($tenantC, $coralBranches, 24);
    }

    protected function tenant(string $name, string $code, string $tier, bool $branching, string $plan, float $price): Tenant
    {
        $tenant = Tenant::create([
            'name' => $name,
            'code' => $code,
            'tier' => $tier,
            'contact_email' => Str::slug($name).'@innease.test',
            'contact_phone' => '+63 900 000 '.random_int(1000, 9999),
            'description' => $name.' operates on the InnEase '.$plan.' tier.',
            'supports_branching' => $branching,
            'status' => 'active',
        ]);

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan' => $plan,
            'monthly_price' => $price,
            'started_on' => now()->subMonths(6)->toDateString(),
            'renews_on' => now()->addMonths(6)->toDateString(),
            'status' => 'active',
            'features' => match ($tier) {
                'starter' => 'Transactions, Guest Profiles, Feedback',
                'standard' => 'Transactions, Business Intelligence, Action Board',
                default => 'Transactions, Business Intelligence, Multi-branch, Promotions, Sales Reports',
            },
        ]);

        return $tenant;
    }

    protected function users(Tenant $tenant, string $slug): void
    {
        foreach (['admin', 'manager', 'staff'] as $role) {
            User::create([
                'name' => ucfirst($role).' '.$tenant->name,
                'email' => "{$role}@{$slug}.test",
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'role' => $role,
                'is_active' => true,
            ]);
        }
    }

    protected function branch(Tenant $tenant, string $name, string $code, int $rooms): Branch
    {
        return Branch::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            'code' => $code,
            'address' => $name.', Philippines',
            'manager_name' => 'Manager '.$code,
            'total_rooms' => $rooms,
            'status' => 'active',
        ]);
    }

    /**
     * @param  array<int, Branch>  $branches
     */
    protected function operationalData(Tenant $tenant, array $branches, int $guestCount): void
    {
        $staff = User::where('tenant_id', $tenant->id)->get();
        $manager = $staff->firstWhere('role', 'manager');
        $agent = $staff->firstWhere('role', 'staff');

        $firstNames = ['Maria', 'Jose', 'Ana', 'Pedro', 'Liza', 'Marco', 'Grace', 'Ramon', 'Nina', 'Carlo', 'Bea', 'Ivan'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Torres', 'Flores', 'Ramos'];

        for ($i = 0; $i < $guestCount; $i++) {
            $branch = $branches[$i % max(1, count($branches))] ?? null;

            $guest = Guest::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch?->id,
                'first_name' => $firstNames[$i % count($firstNames)],
                'last_name' => $lastNames[$i % count($lastNames)],
                'email' => strtolower($firstNames[$i % count($firstNames)].'.'.$lastNames[$i % count($lastNames)].$i).'@guest.test',
                'phone' => '+63 917 '.random_int(1000000, 9999999),
                'address' => 'Davao City, Philippines',
                'id_number' => 'ID-'.strtoupper(Str::random(6)),
                'nationality' => 'Filipino',
                'preferences' => $i % 3 === 0 ? 'Non-smoking room, high floor' : null,
                'status' => 'active',
            ]);

            $checkIn = now()->subDays(random_int(0, 20));
            $nights = random_int(1, 4);
            $status = match ($i % 5) {
                0 => 'pending',
                1 => 'confirmed',
                2 => 'checked_in',
                3 => 'checked_out',
                default => 'confirmed',
            };

            $reservation = Reservation::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch?->id,
                'guest_id' => $guest->id,
                'reference' => 'RES-'.strtoupper(Str::random(8)),
                'room_number' => (string) (100 + $i),
                'room_type' => ['standard', 'deluxe', 'suite', 'family'][$i % 4],
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkIn->copy()->addDays($nights)->toDateString(),
                'checked_in_at' => in_array($status, ['checked_in', 'checked_out'], true) ? $checkIn : null,
                'checked_out_at' => $status === 'checked_out' ? $checkIn->copy()->addDays($nights) : null,
                'adults' => random_int(1, 3),
                'children' => random_int(0, 2),
                'total_amount' => $nights * random_int(2000, 6000),
                'status' => $status,
                'notes' => null,
            ]);

            Sale::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch?->id,
                'guest_id' => $guest->id,
                'reservation_id' => $reservation->id,
                'recorded_by' => $agent?->id,
                'reference' => 'SAL-'.strtoupper(Str::random(8)),
                'description' => 'Room charge for '.$reservation->reference,
                'category' => 'room',
                'quantity' => 1,
                'unit_price' => $reservation->total_amount,
                'amount' => $reservation->total_amount,
                'payment_method' => 'card',
                'status' => 'completed',
                'sold_at' => $checkIn,
            ]);

            if ($i % 2 === 0) {
                $extra = random_int(350, 2500);

                Sale::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch?->id,
                    'guest_id' => $guest->id,
                    'reservation_id' => $reservation->id,
                    'recorded_by' => $agent?->id,
                    'reference' => 'SAL-'.strtoupper(Str::random(8)),
                    'description' => ['Restaurant dinner', 'Spa package', 'Laundry service', 'Minibar'][$i % 4],
                    'category' => ['food', 'spa', 'laundry', 'beverage'][$i % 4],
                    'quantity' => 1,
                    'unit_price' => $extra,
                    'amount' => $extra,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'sold_at' => $checkIn->copy()->addHours(6),
                ]);
            }

            if ($i % 3 === 0) {
                $rating = random_int(1, 5);

                $feedback = Feedback::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch?->id,
                    'guest_id' => $guest->id,
                    'reservation_id' => $reservation->id,
                    'rating' => $rating,
                    'category' => ['service', 'cleanliness', 'facilities', 'food', 'value'][$i % 5],
                    'comment' => $rating >= 4 ? 'Great stay, staff were very accommodating.' : 'Room was not ready on arrival and the aircon was noisy.',
                    'status' => $rating >= 4 ? 'resolved' : 'unresolved',
                    'resolution_notes' => $rating >= 4 ? 'Thanked guest for the feedback.' : null,
                ]);

                if ($rating < 4) {
                    Action::create([
                        'tenant_id' => $tenant->id,
                        'branch_id' => $branch?->id,
                        'guest_id' => $guest->id,
                        'feedback_id' => $feedback->id,
                        'assigned_to' => $agent?->id,
                        'created_by' => $manager?->id,
                        'title' => 'Follow up on feedback #'.$feedback->id,
                        'description' => $feedback->comment,
                        'type' => 'feedback-follow-up',
                        'priority' => $rating <= 2 ? 'high' : 'medium',
                        'status' => 'Open',
                        'due_date' => now()->addDays(3)->toDateString(),
                    ]);
                }
            }

            if ($i % 4 === 0) {
                Interaction::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch?->id,
                    'guest_id' => $guest->id,
                    'logged_by' => $agent?->id,
                    'type' => ['Inquiry', 'Complaint', 'Special Request'][$i % 3],
                    'channel' => ['front-desk', 'phone', 'email'][$i % 3],
                    'subject' => ['Airport transfer request', 'Late check-out request', 'Noise complaint'][$i % 3],
                    'details' => 'Guest contacted the front desk regarding their stay.',
                    'status' => $i % 2 === 0 ? 'open' : 'closed',
                ]);
            }
        }

        Action::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branches[0]->id ?? null,
            'assigned_to' => $agent?->id,
            'created_by' => $manager?->id,
            'title' => 'Room 204 aircon maintenance',
            'description' => 'Aircon unit requires servicing before the next booking.',
            'type' => 'room-maintenance',
            'priority' => 'high',
            'status' => 'In Progress',
            'due_date' => now()->addDay()->toDateString(),
        ]);

        Action::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branches[0]->id ?? null,
            'assigned_to' => $agent?->id,
            'created_by' => $manager?->id,
            'title' => 'Deep clean function hall',
            'description' => 'Post-event housekeeping.',
            'type' => 'housekeeping',
            'priority' => 'low',
            'status' => 'Resolved',
            'due_date' => now()->subDays(2)->toDateString(),
            'resolved_at' => now()->subDay(),
        ]);

        Promotion::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branches[0]->id ?? null,
            'title' => 'Summer Getaway 15% Off',
            'description' => 'Discount on all deluxe rooms for the summer season.',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'starts_on' => now()->subDays(5)->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
            'implemented_by' => $manager?->id,
            'reason_for_implementation' => 'Drive occupancy during the low season and match competitor pricing.',
            'status' => 'Pending',
        ]);

        Promotion::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branches[0]->id ?? null,
            'title' => 'Corporate Rate ₱1,000 Off',
            'description' => 'Fixed discount for corporate partners.',
            'discount_type' => 'fixed',
            'discount_value' => 1000,
            'starts_on' => now()->subDays(20)->toDateString(),
            'ends_on' => now()->addMonths(2)->toDateString(),
            'implemented_by' => $agent?->id,
            'reason_for_implementation' => 'Secure repeat bookings from long-term corporate accounts.',
            'approved_by' => $manager?->id,
            'reviewed_at' => now()->subDays(18),
            'review_notes' => 'Approved — aligned with the partnership agreement.',
            'status' => 'Approved',
        ]);
    }
}
