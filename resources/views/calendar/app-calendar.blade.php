@extends('caller.layouts.main')

@push('stylesheets')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
@endpush

@section('main-content')
  <div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="app-calendar-wrapper card">
        <div class="row g-0">
          <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar">
            <div class="p-6">
              <button class="btn btn-primary w-100 btn-toggle-sidebar" data-bs-toggle="offcanvas" data-bs-target="#addEventSidebar">
                <i class="icon-base ti tabler-plus icon-16px me-1_5"></i>Add Event
              </button>
            </div>
            <div class="p-6 pt-0">
              <div class="inline-calendar"></div>
              <hr class="mb-5 mt-4" />
              <div class="mb-3">
                <div class="form-check mb-2">
                  <input class="form-check-input select-all" type="checkbox" id="selectAll" checked />
                  <label class="form-check-label" for="selectAll">View all</label>
                </div>
              </div>
              <div class="form-check form-check-primary mb-2">
                <input class="form-check-input input-filter" type="checkbox" id="business" data-value="business" checked />
                <label class="form-check-label" for="business">Business</label>
              </div>
              <div class="form-check form-check-success mb-2">
                <input class="form-check-input input-filter" type="checkbox" id="holiday" data-value="holiday" checked />
                <label class="form-check-label" for="holiday">Holiday</label>
              </div>
              <div class="form-check form-check-danger mb-2">
                <input class="form-check-input input-filter" type="checkbox" id="personal" data-value="personal" checked />
                <label class="form-check-label" for="personal">Personal</label>
              </div>
              <div class="form-check form-check-warning mb-2">
                <input class="form-check-input input-filter" type="checkbox" id="family" data-value="family" checked />
                <label class="form-check-label" for="family">Family</label>
              </div>
              <div class="form-check form-check-info">
                <input class="form-check-input input-filter" type="checkbox" id="etc" data-value="etc" checked />
                <label class="form-check-label" for="etc">ETC</label>
              </div>
            </div>
          </div>
          <div class="col app-calendar-content">
            <div class="card-body">
              <div id="calendar"></div>
            </div>
          </div>
        </div>
        <div class="app-overlay"></div>
      </div>
    </div>
  </div>
@endsection

@push('modals')
  <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar" aria-labelledby="addEventSidebarLabel">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <form class="pt-0" id="eventForm" onsubmit="return false">
        <div class="mb-5 form-control-validation">
          <label class="form-label" for="eventTitle">Title</label>
          <input type="text" class="form-control" id="eventTitle" name="eventTitle" placeholder="Event title" />
        </div>
        <div class="mb-5 form-control-validation">
          <label class="form-label" for="eventLabel">Label</label>
          <select class="select2 form-select" id="eventLabel" name="eventLabel">
            <option data-label="primary" value="Business" selected>Business</option>
            <option data-label="success" value="Holiday">Holiday</option>
            <option data-label="danger" value="Personal">Personal</option>
            <option data-label="warning" value="Family">Family</option>
            <option data-label="info" value="ETC">ETC</option>
          </select>
        </div>
        <div class="mb-5 form-control-validation">
          <label class="form-label" for="eventStartDate">Start date</label>
          <input type="text" class="form-control" id="eventStartDate" name="eventStartDate" placeholder="Start date" />
        </div>
        <div class="mb-5 form-control-validation">
          <label class="form-label" for="eventEndDate">End date</label>
          <input type="text" class="form-control" id="eventEndDate" name="eventEndDate" placeholder="End date" />
        </div>
        <div class="mb-5">
          <div class="form-check form-switch">
            <input class="form-check-input allDay-switch" type="checkbox" id="allDay" />
            <label class="form-check-label" for="allDay">All day</label>
          </div>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventURL">Event URL</label>
          <input type="url" class="form-control" id="eventURL" placeholder="https://example.com" />
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventGuests">Guests</label>
          <select class="select2 form-select" id="eventGuests" multiple>
            <option data-avatar="1.png" value="1">John Doe</option>
            <option data-avatar="2.png" value="2">Jane Doe</option>
          </select>
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventLocation">Location</label>
          <input type="text" class="form-control" id="eventLocation" placeholder="Event location" />
        </div>
        <div class="mb-5">
          <label class="form-label" for="eventDescription">Description</label>
          <textarea class="form-control" id="eventDescription" rows="3" placeholder="Event description"></textarea>
        </div>
        <div class="d-flex justify-content-sm-between justify-content-start my-6 gap-2">
          <div class="d-flex">
            <button type="submit" id="addEventBtn" class="btn btn-primary btn-add-event me-4">Add</button>
            <button type="button" class="btn btn-label-secondary btn-cancel" data-bs-dismiss="offcanvas">Cancel</button>
          </div>
          <button type="button" class="btn btn-label-danger btn-delete-event d-none">Delete</button>
        </div>
      </form>
    </div>
  </div>
@endpush

@push('scripts')
  <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
  <script>window.assetsPath = @json(asset('assets/') . '/');</script>
  <script src="{{ asset('assets/js/app-calendar-events.js') }}"></script>
  <script src="{{ asset('assets/js/app-calendar.js') }}"></script>
@endpush
