@extends('layouts.app')

@section('title', 'Staff Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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

  /* Card header shows the live date and time, centered */
  .page-card-header--center {
    justify-content: center;
    text-align: center;
    flex-wrap: wrap;
    gap: 0.25rem 0.75rem;
    font-size: 1.05rem;
    letter-spacing: 0.01em;
  }
  .page-card-header--center .clock-sep { opacity: 0.55; }

  /* Quick access buttons */
  .quick-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 0 0.9rem;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
  }
  .quick-title i { color: rgb(0, 18, 83); }

  .quick-btn {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    width: 100%;
    padding: 1rem 1.25rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    color: #0f172a;
    font-size: 1rem;
    font-weight: 700;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .quick-btn:hover {
    color: #0f172a;
    border-color: rgb(0, 18, 83);
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
  }
  .quick-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 46px;
    height: 46px;
    border-radius: 12px;
    font-size: 1.3rem;
    color: rgb(0, 18, 83);
    background: rgba(0, 18, 83, 0.08);
    transition: background 0.2s ease, color 0.2s ease;
  }
  .quick-btn:hover .quick-icon { color: #ffffff; background: rgb(0, 18, 83); }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <div class="page-card">
        <div class="page-card-header page-card-header--center">
          <span id="dashClockDate">{{ $clockDate }}</span>
          <span class="clock-sep">|</span>
          <span id="dashClockTime">{{ $clockTime }}</span>
        </div>
        <div class="section-header">
          <div>
            <p class="text-muted">System analytics and overview</p>
          </div>
        </div>

        <!-- Summary Metric Cards: all three in a single row -->
        @php $totalDeptDocs = $totalDocuments ?? 0; @endphp
        <div class="row g-4 mb-4">
          <div class="col-12 col-md-4">
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

          <div class="col-12 col-md-4">
            <div class="metric-card">
              <div class="metric-card-content">
                <div class="metric-icon-box" style="background: #e0f2fe; color: #0284c7;">
                  <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
                <div class="metric-value" style="color: #0284c7;" id="statMyUploads">{{ number_format($myUploads) }}</div>
                <div class="metric-sub">(Uploaded by you)</div>
                <div class="metric-title">My Uploads</div>
                <div class="metric-desc">Documents you uploaded</div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="metric-card">
              <div class="metric-card-content">
                <div class="metric-icon-box" style="background: #dcfce7; color: #16a34a;">
                  <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="metric-value" style="color: #16a34a;" id="statMyUploadsMonth">{{ number_format($myUploadsThisMonth) }}</div>
                <div class="metric-sub">({{ $monthLabel }})</div>
                <div class="metric-title">Uploaded This Month</div>
                <div class="metric-desc">Your uploads this month</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Access -->
        <div class="mb-4" id="quickAccess">
          <h5 class="quick-title"><i class="bi bi-lightning-charge-fill"></i> Quick Access</h5>
          <div class="row g-3">
            <div class="col-12 col-md-3">
              <a href="{{ route('staff.documents') }}" class="quick-btn" id="quickDocuments">
                <span class="quick-icon"><i class="bi bi-folder2-open"></i></span>
                <span>Documents</span>
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="{{ route('staff.document.create') }}" class="quick-btn" id="quickUpload">
                <span class="quick-icon"><i class="bi bi-file-earmark-plus"></i></span>
                <span>Upload Documents</span>
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="{{ route('staff.history') }}" class="quick-btn" id="quickHistory">
                <span class="quick-icon"><i class="bi bi-clock-history"></i></span>
                <span>History</span>
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="{{ route('staff.settings') }}" class="quick-btn" id="quickSettings">
                <span class="quick-icon"><i class="bi bi-gear-fill"></i></span>
                <span>Settings</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Chart Section -->
        <div class="p-4 border rounded-4 bg-white shadow-sm h-100 d-flex flex-column justify-content-between mb-4">
          <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-success"></i> Monthly Document Trend</h5>
          <div style="height: 170px; position: relative;">
            <canvas id="staffMonthlyChart"></canvas>
          </div>
        </div>

      </div>
    </div>
  </div>

@push('scripts')
<script>
  // Live date and time in the card header (Philippine time, like the rest of the system)
  (function () {
    const zone = @json(\App\Models\Document::DISPLAY_TIMEZONE);
    const dateEl = document.getElementById('dashClockDate');
    const timeEl = document.getElementById('dashClockTime');
    const dateFormat = new Intl.DateTimeFormat('en-US', { timeZone: zone, weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const timeFormat = new Intl.DateTimeFormat('en-US', { timeZone: zone, hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });

    function tick() {
      const now = new Date();
      dateEl.textContent = dateFormat.format(now);
      timeEl.textContent = timeFormat.format(now);
    }
    tick();
    setInterval(tick, 1000);
  })();

  document.addEventListener('DOMContentLoaded', function () {
    const staffMonthlyCtx = document.getElementById('staffMonthlyChart').getContext('2d');
    new Chart(staffMonthlyCtx, {
      type: 'pie',
      data: {
        labels: {!! json_encode($monthlyCounts?->keys() ?? []) !!},
        datasets: [{
          label: 'Documents',
          data: {!! json_encode($monthlyCounts?->values() ?? []) !!},
          backgroundColor: ['#001253', '#ea3a14', '#0284c7', '#9333ea', '#16a34a', '#eab308', '#64748b', '#cbd5e1', '#374151', '#f43f5e', '#06b6d4', '#84cc16']
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { boxWidth: 12, font: { size: 10 } }
          }
        }
      }
    });
  });
</script>
@endpush
@endsection