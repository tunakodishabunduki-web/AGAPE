<?php

namespace Tests\Feature;

use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Fakes the 'public' disk so this test never writes real files to
        // storage/app/public — everything goes to a temporary, auto-cleaned location.
        Storage::fake('public');
    }

    // Needs the GD or Imagick PHP extension enabled (Intervention Image
    // requires one) — a near-universal default, but worth knowing if this
    // specific test fails on a minimal server build.
    public function test_admin_can_upload_and_receives_a_stored_path(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        $file = UploadedFile::fake()->image('photo.jpg', 2000, 1500);

        $response = $this->post('/api/uploads', ['file' => $file]);

        $response->assertCreated();
        $path = $response->json('path');
        $this->assertStringStartsWith('uploads/', $path);
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg');
        $this->post('/api/uploads', ['file' => $file])->assertUnauthorized();
    }

    public function test_non_image_files_are_rejected(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $this->post('/api/uploads', ['file' => $file])->assertStatus(422);
    }
}
