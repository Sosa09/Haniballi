<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_welcome_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Dr. Mehdi')
            ->assertSee('Haniballi');
    }

    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('doctor@haniballi.com')
            ->assertSee('patient1@test.com');
    }

    public function test_authenticated_patient_can_access_dashboard(): void
    {
        $user = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_authenticated_patient_can_access_appointments(): void
    {
        $user = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($user)->get('/appointments');

        $response->assertStatus(200);
    }

    public function test_authenticated_patient_can_access_training(): void
    {
        $user = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($user)->get('/training');

        $response->assertStatus(200)
            ->assertSee('Therapeutic Stretching');
    }

    public function test_authenticated_patient_can_access_nutrition(): void
    {
        $user = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($user)->get('/nutrition');

        $response->assertStatus(200)
            ->assertSee('Metabolic Health');
    }

    public function test_authenticated_patient_can_access_video_room(): void
    {
        $user = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::first();

        $response = $this->actingAs($user)->get('/video/'.($appointment ? $appointment->id : 1));

        $response->assertStatus(200);
    }

    public function test_doctor_can_access_admin_panel(): void
    {
        $doctor = User::where('role', 'doctor')->first();

        $response = $this->actingAs($doctor)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_users_can_exchange_webrtc_signals(): void
    {
        $doctor = User::where('role', 'doctor')->first() ?? User::factory()->create(['role' => 'doctor']);
        $patient = User::where('role', 'patient')->first() ?? User::factory()->create(['role' => 'patient']);
        $appointment = Appointment::first();

        // 1. Doctor sends WebRTC SDP Offer
        $offerResponse = $this->actingAs($doctor)->postJson('/video/'.$appointment->id.'/signal', [
            'type' => 'offer',
            'payload' => ['type' => 'offer', 'sdp' => 'v=0\r\no=- 12345 2 IN IP4 127.0.0.1...'],
        ]);

        $offerResponse->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // 2. Patient polls for incoming signals
        $pollResponse = $this->actingAs($patient)->getJson('/video/'.$appointment->id.'/signals?last_id=0');

        $pollResponse->assertStatus(200)
            ->assertJsonPath('signals.0.type', 'offer')
            ->assertJsonPath('signals.0.sender_role', 'doctor');

        // 3. Patient sends SDP Answer
        $answerResponse = $this->actingAs($patient)->postJson('/video/'.$appointment->id.'/signal', [
            'type' => 'answer',
            'payload' => ['type' => 'answer', 'sdp' => 'v=0\r\no=- 54321 2 IN IP4 127.0.0.1...'],
        ]);

        $answerResponse->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    public function test_doctor_can_save_clinical_consultation_notes(): void
    {
        $doctor = User::where('role', 'doctor')->first();
        $appointment = Appointment::first();

        $response = $this->actingAs($doctor)->postJson('/video/'.$appointment->id.'/notes', [
            'notes' => 'Patient adheres well to 16:8 fasting. Add resistance bands for scapular retraction.',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'notes' => 'Patient adheres well to 16:8 fasting. Add resistance bands for scapular retraction.',
        ]);
    }
}
