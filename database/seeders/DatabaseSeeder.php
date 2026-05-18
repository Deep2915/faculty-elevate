<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\Goal;
use App\Models\Setting;
use App\Models\User;
use App\Models\WellbeingSurvey;
use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Settings ───────────────────────────────────────────────────────────
        Setting::updateOrCreate(
            ['key' => 'performance_weights'],
            ['value' => ['research' => 0.4, 'teaching' => 0.4, 'innovation' => 0.2]]
        );

        // ── Admin ───────────────────────────────────────────────────────────────
        User::updateOrCreate(['email' => 'admin@facultyelevate.test'], [
            'name' => 'Platform Admin', 'password' => Hash::make('password'), 'role' => 'admin',
        ]);

        // ── HOD ─────────────────────────────────────────────────────────────────
        User::updateOrCreate(['email' => 'hod@facultyelevate.test'], [
            'name' => 'Dr. Reena Kapoor', 'password' => Hash::make('password'), 'role' => 'hod',
        ]);

        // ── Badges ───────────────────────────────────────────────────────────────
        $badgeDefs = [
            ['name' => 'Rising Star',         'slug' => 'rising-star',         'category' => 'teaching',    'xp_threshold' => 100,  'description' => 'Earned first 100 XP.'],
            ['name' => 'Research Pioneer',     'slug' => 'research-pioneer',    'category' => 'research',    'xp_threshold' => 300,  'description' => 'Published first research.'],
            ['name' => 'Innovation Leader',    'slug' => 'innovation-leader',   'category' => 'innovation',  'xp_threshold' => 500,  'description' => 'Leading the innovation curve.'],
            ['name' => 'Attendance Champion',  'slug' => 'attendance-champion', 'category' => 'attendance',  'xp_threshold' => 200,  'description' => 'Perfect attendance record.'],
            ['name' => 'Research Star',        'slug' => 'research-star',       'category' => 'research',    'xp_threshold' => 800,  'description' => 'Prolific researcher with multiple publications.'],
            ['name' => 'Pedagogy Pioneer',     'slug' => 'pedagogy-pioneer',    'category' => 'teaching',    'xp_threshold' => 1000, 'description' => 'Master educator transforming learning.'],
        ];
        foreach ($badgeDefs as $b) {
            Badge::updateOrCreate(['slug' => $b['slug']], array_merge($b, ['icon_svg' => '<svg></svg>', 'criteria' => ['xp_threshold' => $b['xp_threshold']]]));
        }

        // ── Workshops ────────────────────────────────────────────────────────────
        $workshops = [
            ['title' => 'Pedagogy 101',              'category' => 'pedagogy',        'facilitator' => 'Prof. Anil Sharma',   'status' => 'upcoming',  'xp_reward' => 100, 'capacity' => 50, 'duration_hours' => 6,  'description' => 'Fundamentals of modern pedagogy for higher education faculty.', 'days' => 10],
            ['title' => 'Digital Communication',     'category' => 'pedagogy',        'facilitator' => 'Dr. Priya Mehta',     'status' => 'upcoming',  'xp_reward' => 80,  'capacity' => 40, 'duration_hours' => 4,  'description' => 'Effective digital communication strategies for educators.', 'days' => 15],
            ['title' => 'Design Thinking Workshop',  'category' => 'design-thinking', 'facilitator' => 'Innovation Lab',      'status' => 'upcoming',  'xp_reward' => 120, 'capacity' => 60, 'duration_hours' => 8,  'description' => 'Apply design-thinking principles to improve classroom outcomes.', 'days' => 20],
            ['title' => 'EdTech Tools Masterclass',  'category' => 'edtech',          'facilitator' => 'TechEd Institute',    'status' => 'ongoing',   'xp_reward' => 150, 'capacity' => 35, 'duration_hours' => 12, 'description' => 'Master the latest EdTech tools and LMS platforms.', 'days' => 2],
            ['title' => 'Research Methodology',      'category' => 'research',        'facilitator' => 'Dr. K. Rajan',        'status' => 'completed', 'xp_reward' => 200, 'capacity' => 30, 'duration_hours' => 16, 'description' => 'Advanced research methodology and statistical analysis.', 'days' => -20],
            ['title' => 'Burnout Prevention',        'category' => 'wellbeing',       'facilitator' => 'Wellness Team',       'status' => 'upcoming',  'xp_reward' => 60,  'capacity' => 80, 'duration_hours' => 3,  'description' => 'Strategies to maintain work-life balance and prevent burnout.', 'days' => 25],
        ];
        foreach ($workshops as $w) {
            $days = $w['days'];
            unset($w['days']);
            Workshop::updateOrCreate(['title' => $w['title']], array_merge($w, [
                'schedule_date' => now()->addDays($days),
                'registered_faculty_ids' => [],
            ]));
        }

        // ── Faculty Members ──────────────────────────────────────────────────────
        $facultyData = [
            ['name' => 'Dr. Arjun Verma',    'email' => 'arjun@facultyelevate.test',    'dept' => 'Computer Science',   'desig' => 'Associate Professor', 'r' => 0.88, 't' => 0.91, 'i' => 0.75, 'xp' => 1150],
            ['name' => 'Dr. Sana Mirza',     'email' => 'sana@facultyelevate.test',     'dept' => 'Data Science',       'desig' => 'Professor',           'r' => 0.80, 't' => 0.85, 'i' => 0.70, 'xp' => 920],
            ['name' => 'Dr. Raj Patel',      'email' => 'raj@facultyelevate.test',      'dept' => 'Electronics',        'desig' => 'Assistant Professor', 'r' => 0.62, 't' => 0.70, 'i' => 0.55, 'xp' => 480],
            ['name' => 'Dr. Divya Singh',    'email' => 'divya@facultyelevate.test',    'dept' => 'Mathematics',        'desig' => 'Associate Professor', 'r' => 0.45, 't' => 0.68, 'i' => 0.40, 'xp' => 220],
            ['name' => 'Faculty Member',     'email' => 'faculty@facultyelevate.test',  'dept' => 'Computer Science',   'desig' => 'Assistant Professor', 'r' => 0.55, 't' => 0.72, 'i' => 0.49, 'xp' => 320],
        ];

        foreach ($facultyData as $fd) {
            $user = User::updateOrCreate(['email' => $fd['email']], [
                'name' => $fd['name'], 'password' => Hash::make('password'), 'role' => 'faculty',
            ]);

            $pi = ($fd['r'] * 0.4) + ($fd['t'] * 0.4) + ($fd['i'] * 0.2);
            $profile = FacultyProfile::updateOrCreate(['user_id' => (string) $user->id], [
                'department'        => $fd['dept'],
                'designation'       => $fd['desig'],
                'bio'               => "Experienced educator at {$fd['dept']} department.",
                'skills'            => ['Research', 'Teaching', 'Innovation'],
                'research_score'    => $fd['r'],
                'teaching_score'    => $fd['t'],
                'innovation_score'  => $fd['i'],
                'performance_index' => round($pi, 4),
                'xp'                => $fd['xp'],
                'level'             => max(1, intdiv($fd['xp'], 500) + 1),
                'rank'              => 0,
            ]);

            // Evaluations
            $hodUser = User::where('email', 'hod@facultyelevate.test')->first();
            foreach (['Q1-2025', 'Q2-2025', 'Annual-2025'] as $period) {
                Evaluation::updateOrCreate(
                    ['faculty_id' => (string)$user->id, 'period' => $period],
                    [
                        'evaluator_id'   => (string) ($hodUser->id ?? 'hod'),
                        'scores'         => ['research' => $fd['r'], 'teaching' => $fd['t'], 'innovation' => $fd['i']],
                        'weighted_score' => round($pi, 4),
                        'status'         => 'published',
                        'remarks'        => "Good performance observed during {$period}.",
                    ]
                );
            }

            // Achievements
            Achievement::updateOrCreate(
                ['faculty_id' => (string)$user->id, 'title' => 'Machine Learning in Education'],
                [
                    'type'           => 'publication',
                    'journal_or_body'=> 'IEEE Transactions',
                    'date'           => now()->subMonths(3)->format('Y-m-d'),
                    'proof_url'      => null,
                    'xp_awarded'     => 100,
                    'verified'       => true,
                ]
            );

            // Goals
            Goal::updateOrCreate(
                ['faculty_id' => (string)$user->id, 'title' => 'Complete Certified Online Course'],
                [
                    'description'          => 'Finish Coursera Machine Learning Specialization.',
                    'target_date'          => now()->addMonths(2)->format('Y-m-d'),
                    'milestones'           => [],
                    'completion_percentage'=> 60.0,
                    'status'               => 'active',
                ]
            );

            // Wellbeing Surveys
            for ($i = 0; $i < 3; $i++) {
                WellbeingSurvey::create([
                    'faculty_id'   => (string)$user->id,
                    'responses'    => ['workload' => rand(4,8), 'stress' => rand(3,7), 'motivation' => rand(5,9), 'support' => rand(5,9)],
                    'burnout_index'=> rand(45, 85),
                    'notes'        => null,
                    'surveyed_at'  => now()->subDays(($i+1)*7),
                ]);
            }
        }

        $this->command->info('✅ Faculty Elevate seeded! Login credentials:');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Admin',   'admin@facultyelevate.test',   'password'],
            ['HOD',     'hod@facultyelevate.test',     'password'],
            ['Faculty', 'faculty@facultyelevate.test', 'password'],
        ]);
    }
}
