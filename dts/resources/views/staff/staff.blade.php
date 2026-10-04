@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container">
    <div class="staff-layout">
      <aside class="staff-sidebar">
        <nav class="staff-nav">
          <ul class="staff-nav-list">
            <li>
              <a href="{{ route('dashboard') }}" class="staff-nav-link active">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li>
              <a href="{{ route('staff.history') }}" class="staff-nav-link">
                <i class="bi bi-clock-history"></i>
                <span>History</span>
              </a>
            </li>
          </ul>
        </nav>
      </aside>

      <main class="staff-main-content">
        @livewire('document-list')
      </main>
    </div>
  </div>
</div>
@endsection