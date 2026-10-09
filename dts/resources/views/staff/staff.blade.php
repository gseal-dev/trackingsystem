@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .dashboard-shell {
    width: 100%;
    min-height: calc(100vh - 100px);
    padding: clamp(1rem, 2vw, 2rem);
  }

  .dashboard-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
  }

  .staff-main-content {
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    padding: 0;
    min-height: 600px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .section-title {
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    margin: 0;
  }

  .metric-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
  }

  .metric-card::after {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 90px;
    height: 90px;
    background: radial-gradient(circle, rgba(0, 18, 83, 0.04) 0%, transparent 70%);
    border-radius: 50%;
    z-index: 0;
  }

  .metric-card-content {
    position: relative;
    z-index: 1;
  }

  .metric-icon-box {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1.25rem;
  }

  .metric-value {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    margin-bottom: 0.25rem;
  }

  .metric-sub {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.75rem;
    font-weight: 500;
  }

  .metric-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.15rem;
  }

  .metric-desc {
    font-size: 0.82rem;
    color: #64748b;
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <main class="staff-main-content" style="background: transparent; border: none; border-radius: 0; padding: 0; min-height: 600px; width: 100%;">
        <div class="section-header">
          <div>
            <h1 class="section-title">Staff Dashboard</h1>
            <p class="text-muted">System analytics and overview</p>
          </div>
        </div>

        <!-- Summary Metric Cards -->
        @php $totalDeptDocs = isset($statusCounts) ? $statusCounts->sum() : 0; @endphp
        <div class="row g-4 mb-4">
          <div class="col-md-4 col-lg-3">
            <div class="metric-card">
              <div class="metric-card-content">
                <div class="metric-icon-box" style="background: #f3e8ff; color: #9333ea;">
                  <i class="bi bi-folder-fill"></i>
                </div>
                <div class="metric-value" style="color: #9333ea;">{{ number_format($totalDeptDocs) }}</div>
                <div class="metric-sub">(100%)</div>
                <div class="metric-title">Department Documents</div>
                <div class="metric-desc">Assigned Records</div>
              </div>
            </div>
          </div>

          @if(isset($statusCounts))
            @php
              $colors = [
                ['bg' => '#ffedd5', 'color' => '#ea580c', 'icon' => 'bi-clock-history'],
                ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'bi-check-circle-fill'],
                ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'bi-exclamation-triangle-fill'],
                ['bg' => '#e0e7ff', 'color' => '#4f46e5', 'icon' => 'bi-arrow-repeat'],
              ];
              $i = 0;
            @endphp
            @foreach($statusCounts as $statusName => $count)
              @php
                $theme = $colors[$i % count($colors)];
                $pct = $totalDeptDocs > 0 ? round(($count / $totalDeptDocs) * 100, 1) : 0;
                $i++;
              @endphp
              <div class="col-md-4 col-lg-3">
                <div class="metric-card">
                  <div class="metric-card-content">
                    <div class="metric-icon-box" style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }};">
                      <i class="bi {{ $theme['icon'] }}"></i>
                    </div>
                    <div class="metric-value" style="color: {{ $theme['color'] }};">{{ number_format($count) }}</div>
                    <div class="metric-sub">({{ $pct }}%)</div>
                    <div class="metric-title">{{ $statusName }}</div>
                    <div class="metric-desc">Status Breakdown</div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>

        <!-- Staff Analytics Dashboard Graphs Section -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="p-4 border rounded-4 bg-light shadow-sm">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> Department Document Status</h5>
              <div style="height: 250px; position: relative;">
                <canvas id="staffStatusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="p-4 border rounded-4 bg-light shadow-sm">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-graph-up me-2 text-success"></i> Monthly Document Trend</h5>
              <div style="height: 250px; position: relative;">
                <canvas id="staffMonthlyChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const staffStatusCtx = document.getElementById('staffStatusChart').getContext('2d');
    new Chart(staffStatusCtx, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($statusCounts?->keys() ?? []) !!},
        datasets: [{
          data: {!! json_encode($statusCounts?->values() ?? []) !!},
          backgroundColor: ['#001253', '#ea3a14', '#16a34a', '#eab308', '#64748b']
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    const staffMonthlyCtx = document.getElementById('staffMonthlyChart').getContext('2d');
    new Chart(staffMonthlyCtx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($monthlyCounts?->keys() ?? []) !!},
        datasets: [{
          label: 'Documents',
          data: {!! json_encode($monthlyCounts?->values() ?? []) !!},
          backgroundColor: '#001253'
        }]
      },
      options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
  });
</script>
@endpush
@endsection
