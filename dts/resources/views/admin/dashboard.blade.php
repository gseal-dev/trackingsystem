@extends('layouts.app')

@section('title', 'Admin Dashboard')

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

  .staff-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 2rem;
    align-items: start;
  }

  .staff-sidebar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
  }

  .staff-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .staff-nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    color: #4b5563;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }

  .staff-nav-link:hover {
    background: #f3f4f6;
    color: #111827;
  }

  .staff-nav-link.active {
    background: #001253;
    color: #fff;
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

  .stat-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.5rem;
    background: #001253;
    color: #fff;
  }

  @media (max-width: 1024px) {
    .staff-layout {
      grid-template-columns: 1fr;
    }
    .staff-sidebar {
      position: static;
    }
    .staff-nav-list {
      flex-direction: row;
      overflow-x: auto;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <main class="staff-main-content" style="background: transparent; border: none; border-radius: 0; padding: 0; min-height: 600px; width: 100%;">
        <div class="section-header">
          <div>
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="text-muted">System analytics and overview</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Summary Metric Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="stat-card">
              <div class="stat-icon"><i class="bi bi-folder-fill"></i></div>
              <div>
                <div class="text-muted small fw-bold text-uppercase">Total Documents</div>
                <div class="fs-3 fw-800 text-dark">{{ $totalDocuments ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="stat-card">
              <div class="stat-icon" style="background-color: #ea3a14;"><i class="bi bi-people-fill"></i></div>
              <div>
                <div class="text-muted small fw-bold text-uppercase">Total Users</div>
                <div class="fs-3 fw-800 text-dark">{{ $totalUsers ?? 0 }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Analytics Graphs Section -->
        <div class="row g-4">
          <div class="col-md-6">
            <div class="p-4 border rounded-4 bg-light shadow-sm">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> Documents by Status</h5>
              <div style="height: 250px; position: relative;">
                <canvas id="adminStatusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="p-4 border rounded-4 bg-light shadow-sm">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-bar-chart-fill me-2 text-success"></i> Documents by Department</h5>
              <div style="height: 250px; position: relative;">
                <canvas id="adminDeptChart"></canvas>
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
    const adminStatusCtx = document.getElementById('adminStatusChart').getContext('2d');
    new Chart(adminStatusCtx, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode(isset($documentsByStatus) ? $documentsByStatus->keys() : []) !!},
        datasets: [{
          data: {!! json_encode(isset($documentsByStatus) ? $documentsByStatus->values() : []) !!},
          backgroundColor: ['#001253', '#ea3a14', '#16a34a', '#eab308', '#64748b']
        }]
      },
      options: { responsive: true, maintainAspectRatio: false }
    });

    const adminDeptCtx = document.getElementById('adminDeptChart').getContext('2d');
    new Chart(adminDeptCtx, {
      type: 'bar',
      data: {
        labels: {!! json_encode(isset($documentsByDept) ? $documentsByDept->keys() : []) !!},
        datasets: [{
          label: 'Documents',
          data: {!! json_encode(isset($documentsByDept) ? $documentsByDept->values() : []) !!},
          backgroundColor: '#001253'
        }]
      },
      options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
  });
</script>
@endpush
@endsection
