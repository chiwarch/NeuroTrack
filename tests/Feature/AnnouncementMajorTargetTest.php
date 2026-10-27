<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Major;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementMajorTargetTest extends TestCase
{
    use RefreshDatabase;

    public function test_major_target_is_visible_to_matching_student(): void
    {
        $rpl = Major::create(['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak']);
        $tkj = Major::create(['code' => 'TKJ', 'name' => 'Teknik Komputer Jaringan']);

        $announcement = Announcement::create([
            'title' => 'Pengumuman jurusan',
            'content' => 'Khusus siswa jurusan RPL',
            'target_type' => 'major',
            'published_at' => now(),
            'created_by' => User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ])->id,
        ]);
        $announcement->majors()->sync([$rpl->id]);

        $student = User::create([
            'name' => 'Siswa RPL',
            'email' => 'siswa@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '10001',
            'major_id' => $rpl->id,
            'grade_level' => 10,
        ]);

        $this->assertTrue(Announcement::visibleTo($student)->whereKey($announcement->id)->exists());
        $this->assertSame('Rekayasa Perangkat Lunak', $announcement->targetLabel());

        $otherStudent = User::create([
            'name' => 'Siswa TKJ',
            'email' => 'siswa-tkj@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'nis' => '10002',
            'major_id' => $tkj->id,
            'grade_level' => 10,
        ]);

        $this->assertFalse(Announcement::visibleTo($otherStudent)->whereKey($announcement->id)->exists());
    }
}
