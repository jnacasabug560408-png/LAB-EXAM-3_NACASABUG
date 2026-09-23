@extends('layouts.crm')

@section('title', 'Business Intelligence')
@section('page-title', 'Group Business Intelligence')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Total Reservations" :value="number_format($summary['total_reservations'])"
                        icon="bi-calendar-check" :href="route('reservations.index')" hint="View reservations" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Total Guests" :value="number_format($summary['total_guests'])"
                        icon="bi-people" :href="route('guests.index')" hint="View guests" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Occupancy Rate" :value="$summary['occupancy_rate'].'%'"
                        icon="bi-door-open" :href="route('reservations.index', ['status' => 'checked_in'])"
                        hint="See in-house reservations" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Revenue" :value="'₱'.number_format($summary['revenue'], 2)"
                        icon="bi-cash-stack" :href="route('reports.sales')" hint="Open sales report" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Unresolved Feedback" :value="number_format($feedbackAnalytics['unresolved'])"
                        icon="bi-chat-left-dots" :href="route('feedback.index', ['status' => 'unresolved'])"
                        hint="Resolve feedback" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Average Rating" :value="$feedbackAnalytics['average'].' / 5'"
                        icon="bi-star" :href="route('feedback.index')" hint="Browse feedback" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Open Actions" :value="number_format($summary['open_actions'])"
                        icon="bi-list-task" :href="route('actions.index')" hint="Open task list" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Promotions Awaiting Approval" :value="number_format($pendingPromotions)"
                        icon="bi-megaphone" :href="route('promotions.index', ['status' => 'Pending'])"
                        hint="Review promotions" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Occupancy Trend</h5></div>
                <div class="card-body"><canvas id="occupancyChart" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Reservation Statuses</h5></div>
                <div class="card-body"><canvas id="statusChart" height="180"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Revenue — Last 14 Days</h5></div>
                <div class="card-body"><canvas id="revenueChart" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Feedback Ratings</h5></div>
                <div class="card-body"><canvas id="feedbackChart" height="180"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Operational Tasks</h5>
                    <a href="{{ route('actions.index') }}" class="btn btn-sm btn-primary">Task board</a>
                </div>
                <div class="card-body">
                    @forelse($openActions as $action)
                        <div class="board-card">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $action->title }}</strong>
                                <span class="badge {{ $action->statusBadge() }}">{{ $action->status }}</span>
                            </div>
                            <small class="text-muted">
                                {{ ucfirst(str_replace('-', ' ', $action->type)) }} ·
                                {{ $action->assignee?->name ?? 'Unassigned' }}
                            </small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No open tasks.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Latest Feedback</h5></div>
                <div class="card-body">
                    @forelse($latestFeedback as $item)
                        <div class="board-card">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $item->guest->full_name }}</strong>
                                <span>{{ str_repeat('★', $item->rating) }}</span>
                            </div>
                            <small class="text-muted">{{ Str::limit($item->comment, 90) }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No feedback yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const lineOptions = { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } };

    new Chart(document.getElementById('occupancyChart'), {
        type: 'line',
        data: {
            labels: @json($occupancyTrend['labels']),
            datasets: [{
                label: 'Occupancy %',
                data: @json($occupancyTrend['values']),
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39, 174, 96, 0.15)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: lineOptions
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: @json($revenueTrend['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueTrend['values']),
                backgroundColor: '#2e86de',
            }]
        },
        options: lineOptions
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($reservationStatuses)),
            datasets: [{
                data: @json(array_values($reservationStatuses)),
                backgroundColor: ['#f39c12', '#2e86de', '#27ae60', '#7f8c8d', '#e74c3c'],
            }]
        }
    });

    new Chart(document.getElementById('feedbackChart'), {
        type: 'bar',
        data: {
            labels: @json(array_map(fn ($r) => $r.' star', array_keys($feedbackAnalytics['by_rating']))),
            datasets: [{
                label: 'Reviews',
                data: @json(array_values($feedbackAnalytics['by_rating'])),
                backgroundColor: '#f39c12',
            }]
        },
        options: lineOptions
    });
</script>
@endsection
