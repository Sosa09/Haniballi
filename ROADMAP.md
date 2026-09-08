# DEPLOYMENT & FEATURE ROADMAP

## Phase 1: Core Foundation & Interactive Mockup [COMPLETED]
* [x] Initialize Laravel 13 with Livewire v4 and Filament v5.
* [x] Implement database schema & seeders (Patients, Appointments, NutritionPlans, ExercisePrograms, VideoSessions).
* [x] Build Filament Doctor Admin Panel with 4 clinical resources.
* [x] Build Public Landing Page with responsive health aesthetic and animations.
* [x] Build Patient Dashboard with metric cards and active treatment overviews.
* [x] Build Interactive Multi-step Appointment Booking Livewire component.
* [x] Build Interactive Stretching & Mobility player with countdown timers and movement steps.
* [x] Build Interactive Telehealth Video Studio with doctor feed simulation, self-view, chat, and controls.
* [x] One-click demo credentials on `/login` for frictionless client review.
* [x] Automated test suite passing (10 tests, 16 assertions).

## Phase 2: Live Daily.co Video Pipeline Integration
* [ ] Provision Daily.co API key in `.env`.
* [ ] Implement `VideoRoomService` to automatically create expiring rooms upon appointment confirmation.
* [ ] Generate unique HMAC tokens for Doctor (Host/Owner) and Patient (Participant).
* [ ] Webhook listener for call duration tracking and attendance logging.

## Phase 3: Patient Booking Payments & Notifications
* [ ] Integrate Stripe Checkout / Cash on delivery for appointment reservations.
* [ ] Automated WhatsApp / SMS reminder notifications via Twilio or Brevo.
* [ ] Automated calendar sync (.ics export / Google Calendar link).

## Phase 4: Production Hardening & Deployment
* [ ] Switch DB driver to PostgreSQL.
* [ ] Configure Redis for cache, sessions, and queue workers.
* [ ] SSL certification, custom domain configuration (`docteurhanebalymehdi.info`).
* [ ] Deploy via Laravel Cloud / Forge / Dockerized VPS.
