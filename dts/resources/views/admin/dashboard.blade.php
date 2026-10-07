@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .dashboard-shell {
    width: 100%;
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

  .metric-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
    display: flex;
    flex-direction: column;
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

  .chart-card {
    width: 100%;
    display: flex;
    flex-direction: column;
  }

  .chart-container {
    position: relative;
    flex: 1;
    width: 100%;
    min-height: 160px;
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
    <main class="staff-main-content">
      <div class="section-header">
        <div>
          <h1 class="section-title">Admin Dashboard</h1>
          <p class="text-muted">System analytics and overview</p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <!-- Equal-Height Cards Row -->
      <div class="row g-4 mb-4 align-items-stretch">
        <!-- Total Documents Card -->
        <div class="col-12 col-md-3 d-flex">
          <div class="metric-card w-100">
            <div class="metric-card-content">
              <div class="metric-icon-box" style="background: #f3e8ff; color: #9333ea;">
                <i class="bi bi-folder-fill"></i>
              </div>
              <div class="metric-value" style="color: #9333ea;">{{ number_format($totalDocuments ?? 0) }}</div>
              <div class="metric-sub">(100%)</div>
              <div class="metric-title">Total Documents</div>
              <div class="metric-desc">All Registered Records</div>
            </div>
          </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-12 col-md-3 d-flex">
          <div class="metric-card w-100">
            <div class="metric-card-content">
              <div class="metric-icon-box" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-people-fill"></i>
              </div>
              <div class="metric-value" style="color: #0284c7;">{{ number_format($totalUsers ?? 0) }}</div>
              <div class="metric-sub">(Accounts)</div>
              <div class="metric-title">Total Users</div>
              <div class="metric-desc">System Personnel</div>
            </div>
          </div>
        </div>

        <!-- Documents by Department Chart Card -->
        <div class="col-12 col-md-6 d-flex">
          <div class="p-4 border rounded-4 bg-light shadow-sm chart-card w-100">
            <h5 class="fw-bold mb-3 text-dark">
              <i class="bi bi-bar-chart-fill me-2 text-success"></i> Documents by Department
            </h5>
            <div class="chart-container">
              <canvas id="adminDeptChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
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
      options: { 
        responsive: true, 
        maintainAspectRatio: false, 
        scales: { y: { beginAtZero: true } } 
      }
    });
  });
</script>
@endpush
@endsection