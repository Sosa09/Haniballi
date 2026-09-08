# SYSTEM ARCHITECTURE: DR. MEHDI HANIBALLI PLATFORM

## 1. Executive Summary
A unified, zero-friction medical telehealth and nutritional therapy platform designed for Dr. Mehdi Haniballi and his patients. Built using a high-velocity, single-ecosystem Laravel 13 stack with Livewire v4 and Filament v5.

## 2. Core Technology Stack
* **Language & Runtime:** PHP 8.4
* **Backend Framework:** Laravel 13
* **Admin / Doctor Portal:** Filament v5 (`/admin`)
* **Reactive Client UI:** Livewire v4 (Single File Components) & Alpine.js
* **Asset Bundler & CSS:** Vite v8 + Tailwind CSS v4
* **Database Engine:** SQLite (Local/Mockup) -> PostgreSQL (Staging/Production)
* **Telehealth Pipeline:** Managed WebRTC API (Daily.co prebuilt iframe + custom client consultation studio)

## 3. System Domain Layers
### A. Doctor Admin Panel (`/admin`)
* Powered by **Filament v5** with dedicated resources:
  * `PatientResource`: Patient intake, clinical notes, allergies, anthropometric history.
  * `AppointmentResource`: Calendar scheduling, consultation type (Video / Clinic / Phone), status lifecycle.
  * `NutritionPlanResource`: Macro distribution (protein, carb, fat), caloric budgeting, structured daily meals JSON.
  * `ExerciseProgramResource`: Category-specific routines (stretching, posture, mobility), sets/reps/cadence.

### B. Patient Telehealth Portal (`/dashboard`)
* Dedicated patient interface for active treatment plans:
  * Health metrics overview (weight trajectory, BMI, calorie targets).
  * Upcoming video appointments with one-click direct access.
  * Active nutrition regimen with meal-by-meal protocol view (`/nutrition`).
  * Interactive Stretching & Mobility player with countdown timers and step progressions (`/training`).
  * Telehealth Video Consultation Studio with doctor video feed, patient self-view, audio waveform, live clinical chat, and session notes (`/video/{appointmentId}`).

### C. Public Marketing & Booking Site (`/`)
* Modern, health-focused responsive landing page showcasing Dr. Haniballi's credentials, services, patient testimonials, and interactive appointment booking workflow.

## 4. Security & Access Control
* Role-based authorization (`doctor`, `patient`, `admin`).
* `canAccessPanel(Panel $panel)` guard restricting `/admin` to verified clinical staff.
* CSRF, secure password hashing (`bcrypt`), and session-protected medical routes.
