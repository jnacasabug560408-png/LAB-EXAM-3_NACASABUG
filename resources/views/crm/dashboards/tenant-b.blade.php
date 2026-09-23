@extends('layouts.crm')

@section('title', 'Business Intelligence')
@section('page-title', 'Business Intelligence Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Total Reservations" :value="number_format($summary['total_reservations'])"
                        icon="bi-calendar-check" :href="route('reservations.index')" hint="View reservations" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Active Guests" :value="number_format($summary['active_guests'])"
                        icon="bi-people" :href="route('guests.index', ['status' => 'active'])" hint="View guests" />
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
            <x-kpi-card label="Unresolved Feedback" :value="number_format($summary['unresolved_feedback'])"
                        icon="bi-chat-left-dots" :href="route('feedback.index', ['status' => 'unresolved'])"
                        hint="Resolve feedback" />
        </div>
        @foreach($actionCounts as $status => $count)
            <div class="col-md-3">
                <x-kpi-card :label="$status.' Actions'" :value="number_format($count)" icon="bi-list-task"
                            :href="route('actions.index')" hint="Open action board" />
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Revenue — Last 14 Days</h5></div>
                <div class="card-body"><canvas id="revenueChart" height="120"></canvas></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Action Log</h5>
                    <a href="{{ route('actions.index') }}" class="btn btn-sm btn-primary">Open board</a>
                </div>
                <div class="card-body">
                    @forelse($actions as $action)
                        <div class="board-card d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $action->title }}</strong><br>
                                <small class="text-muted">{{ $action->assignee?->name ?? 'Unassigned' }}</small>
                            </div>
                            <form action="{{ route('actions.status', $action) }}" method="POST">
                                @csrf
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach(\App\Models\Action::STATUSES as $status)
                                        <option value="{{ $status }}" @selected($action->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No actions logged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: @json($revenueTrend['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueTrend['values']),
                borderColor: '#2e86de',
                backgroundColor: 'rgba(46, 134, 222, 0.15)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
