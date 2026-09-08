# TECHNICAL DEBT LOG

*Track deliberate architectural shortcuts taken for rapid prototype compilation. Refactor prior to production deployment.*

## 1. Local SQLite vs Production PostgreSQL
* **Current State:** SQLite is used locally for instant zero-config prototyping.
* **Refactor Plan:** Migrate to managed PostgreSQL (or Supabase/RDS) before production to support high concurrency, row-level locking, and JSON querying.

## 2. Telehealth Video Token Generation
* **Current State:** Video room component includes a high-fidelity interactive simulation studio with camera/mic controls, waveform audio, and doctor feeds, plus fallback iframe embedding.
* **Refactor Plan:** Bind to real Daily.co / Stream API keys via environment variables for live production WebRTC room creation.

## 3. Mock Authentication Bypass & Demo Buttons
* **Current State:** One-click autofill buttons are provided on `/login` to allow Dr. Haniballi and testers to easily evaluate both roles without copying credentials manually.
* **Refactor Plan:** Remove demo buttons before public production rollout.

## 4. Email / SMS Notification Transports
* **Current State:** Appointment confirmations write to local database without dispatching outbound SMS or emails.
* **Refactor Plan:** Configure queue workers and connect to transactional mail provider (Resend/Postmark/Brevo).
