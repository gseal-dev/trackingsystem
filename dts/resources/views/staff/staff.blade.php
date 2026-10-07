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

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert" style="background: #ecfdf5; color: #065f46; border-radius: 12px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

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
