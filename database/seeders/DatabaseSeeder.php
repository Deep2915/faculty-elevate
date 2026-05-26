<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\ClassLog;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\Goal;
use App\Models\Setting;
use App\Models\StudentFeedback;
use App\Models\TimetableEntry;
use App\Models\User;
use App\Models\WellbeingSurvey;
use App\Models\Workshop;
use Carbon\Carbon;
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

        // ── Other Faculty Members ─────────────────────────────────────────────────
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
            FacultyProfile::updateOrCreate(['user_id' => (string) $user->id], [
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

        // ── Deep (deepkaurbhamber123@gmail.com) — Rich Demo Data ─────────────────
        $this->seedDeepUser();

        // ── Anonymous Student Feedbacks ───────────────────────────────────────────
        $this->seedStudentFeedbacks();

        $this->command->info('✅ Faculty Elevate seeded! Login credentials:');
        $this->command->table(['Role', 'Email', 'Password'], [
            ['Admin',   'admin@facultyelevate.test',    'password'],
            ['HOD',     'hod@facultyelevate.test',      'password'],
            ['Faculty', 'faculty@facultyelevate.test',  'password'],
            ['Deep',    'deepkaurbhamber123@gmail.com', 'password'],
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Deep's rich demo data — looks like 6+ months of active portal usage
    // ────────────────────────────────────────────────────────────────────────────
    private function seedDeepUser(): void
    {
        $hodUser = User::where('email', 'hod@facultyelevate.test')->first();

        // ── User account
        $deep = User::updateOrCreate(['email' => 'deepkaurbhamber123@gmail.com'], [
            'name'     => 'Dr. Deep Kaur',
            'password' => Hash::make('password'),
            'role'     => 'faculty',
        ]);
        $uid = (string) $deep->id;

        // ── Profile
        FacultyProfile::updateOrCreate(['user_id' => $uid], [
            'department'        => 'Computer Science',
            'designation'       => 'Assistant Professor',
            'bio'               => 'Passionate about AI, data-driven pedagogy, and student-centered learning. Six years of teaching experience with a strong research background in Machine Learning and Computer Vision.',
            'skills'            => ['Machine Learning', 'Python', 'Data Structures', 'Research', 'Pedagogy', 'Cloud Computing'],
            'research_score'    => 0.78,
            'teaching_score'    => 0.87,
            'innovation_score'  => 0.72,
            'performance_index' => round((0.78 * 0.4) + (0.87 * 0.4) + (0.72 * 0.2), 4),
            'xp'                => 1840,
            'level'             => 4,
            'rank'              => 2,
        ]);

        // ── Evaluations (5 quarters — shows growth over time)
        $evals = [
            ['period' => 'Q3-2024', 'r' => 0.68, 't' => 0.74, 'i' => 0.58, 'months_ago' => 9,  'remarks' => 'Consistent performance. Encouraged to publish more.'],
            ['period' => 'Q4-2024', 'r' => 0.72, 't' => 0.78, 'i' => 0.62, 'months_ago' => 6,  'remarks' => 'Noticeable improvement in teaching scores. Research paper submitted.'],
            ['period' => 'Q1-2025', 'r' => 0.75, 't' => 0.83, 'i' => 0.67, 'months_ago' => 3,  'remarks' => 'Great progress this quarter. Research paper accepted.'],
            ['period' => 'Q2-2025', 'r' => 0.78, 't' => 0.87, 'i' => 0.70, 'months_ago' => 1,  'remarks' => 'Excellent performance. Among top 3 in the department.'],
            ['period' => 'Annual-2025','r' => 0.78,'t' => 0.87,'i' => 0.72,'months_ago' => 0,   'remarks' => 'Outstanding annual performance. Recommended for promotion consideration.'],
        ];
        foreach ($evals as $ev) {
            $pi = ($ev['r'] * 0.4) + ($ev['t'] * 0.4) + ($ev['i'] * 0.2);
            Evaluation::updateOrCreate(
                ['faculty_id' => $uid, 'period' => $ev['period']],
                [
                    'evaluator_id'   => (string) $hodUser->id,
                    'scores'         => ['research' => $ev['r'], 'teaching' => $ev['t'], 'innovation' => $ev['i']],
                    'weighted_score' => round($pi, 4),
                    'status'         => 'published',
                    'remarks'        => $ev['remarks'],
                    'created_at'     => now()->subMonths($ev['months_ago']),
                ]
            );
        }

        // ── Achievements (varied types, spread over 8 months)
        $achievements = [
            [
                'title'          => 'Deep Learning for Image Classification',
                'type'           => 'publication',
                'journal_or_body'=> 'Springer LNCS',
                'date'           => now()->subMonths(8)->format('Y-m-d'),
                'xp_awarded'     => 200,
                'verified'       => true,
            ],
            [
                'title'          => 'Best Paper Award — ICAIET 2024',
                'type'           => 'award',
                'journal_or_body'=> 'International Conference on AI & Emerging Technologies',
                'date'           => now()->subMonths(6)->format('Y-m-d'),
                'xp_awarded'     => 300,
                'verified'       => true,
            ],
            [
                'title'          => 'Google TensorFlow Developer Certificate',
                'type'           => 'certification',
                'journal_or_body'=> 'Google',
                'date'           => now()->subMonths(5)->format('Y-m-d'),
                'xp_awarded'     => 150,
                'verified'       => true,
            ],
            [
                'title'          => 'Federated Learning for Privacy-Preserving Healthcare',
                'type'           => 'publication',
                'journal_or_body'=> 'IEEE Access',
                'date'           => now()->subMonths(3)->format('Y-m-d'),
                'xp_awarded'     => 200,
                'verified'       => true,
            ],
            [
                'title'          => 'Method for Optimised Neural Network Pruning',
                'type'           => 'patent',
                'journal_or_body'=> 'Indian Patent Office',
                'date'           => now()->subMonths(2)->format('Y-m-d'),
                'xp_awarded'     => 400,
                'verified'       => true,
            ],
            [
                'title'          => 'AWS Certified Machine Learning – Specialty',
                'type'           => 'certification',
                'journal_or_body'=> 'Amazon Web Services',
                'date'           => now()->subMonths(1)->format('Y-m-d'),
                'xp_awarded'     => 150,
                'verified'       => true,
            ],
            [
                'title'          => 'Transformer-Based Approach to Code Review Automation',
                'type'           => 'publication',
                'journal_or_body'=> 'ACM SIGSOFT',
                'date'           => now()->subWeeks(2)->format('Y-m-d'),
                'xp_awarded'     => 200,
                'verified'       => false,
            ],
        ];
        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(
                ['faculty_id' => $uid, 'title' => $ach['title']],
                array_merge($ach, ['faculty_id' => $uid, 'proof_url' => null])
            );
        }

        // ── Goals (mix of completed, active, and upcoming)
        $goals = [
            [
                'title'                 => 'Publish 3 Research Papers in 2024',
                'description'           => 'Target high-impact journals in AI/ML domain — Springer, IEEE, ACM.',
                'target_date'           => now()->subMonths(1)->format('Y-m-d'),
                'milestones'            => [
                    ['label' => 'Submit to Springer LNCS', 'done' => true],
                    ['label' => 'Submit to IEEE Access', 'done' => true],
                    ['label' => 'Submit to ACM SIGSOFT', 'done' => true],
                ],
                'completion_percentage' => 100.0,
                'status'                => 'completed',
            ],
            [
                'title'                 => 'Complete AWS ML Specialty Certification',
                'description'           => 'Obtain the AWS Certified Machine Learning – Specialty credential.',
                'target_date'           => now()->subWeeks(3)->format('Y-m-d'),
                'milestones'            => [
                    ['label' => 'Complete AWS training path', 'done' => true],
                    ['label' => 'Practice exam (score 80%+)', 'done' => true],
                    ['label' => 'Book and pass exam', 'done' => true],
                ],
                'completion_percentage' => 100.0,
                'status'                => 'completed',
            ],
            [
                'title'                 => 'Integrate AI Tools into Course Curriculum',
                'description'           => 'Redesign Data Structures & Algorithms course with AI-assisted problem sets and auto-grading.',
                'target_date'           => now()->addMonths(1)->format('Y-m-d'),
                'milestones'            => [
                    ['label' => 'Audit existing curriculum', 'done' => true],
                    ['label' => 'Design AI-assisted assignments', 'done' => true],
                    ['label' => 'Pilot with one batch', 'done' => false],
                    ['label' => 'Gather feedback and iterate', 'done' => false],
                ],
                'completion_percentage' => 55.0,
                'status'                => 'active',
            ],
            [
                'title'                 => 'File 2 Patents in 2025',
                'description'           => 'File patents on neural network optimisation and an AI-driven attendance prediction system.',
                'target_date'           => now()->addMonths(4)->format('Y-m-d'),
                'milestones'            => [
                    ['label' => 'Complete documentation for Patent 1', 'done' => true],
                    ['label' => 'File Patent 1 with IPO', 'done' => true],
                    ['label' => 'Complete documentation for Patent 2', 'done' => false],
                    ['label' => 'File Patent 2 with IPO', 'done' => false],
                ],
                'completion_percentage' => 50.0,
                'status'                => 'active',
            ],
            [
                'title'                 => 'Mentor 5 Students for National Hackathons',
                'description'           => 'Guide and coach student teams for Smart India Hackathon and other national events.',
                'target_date'           => now()->addMonths(3)->format('Y-m-d'),
                'milestones'            => [
                    ['label' => 'Identify and shortlist students', 'done' => true],
                    ['label' => 'Weekly mentoring sessions', 'done' => false],
                    ['label' => 'Submit project proposals', 'done' => false],
                ],
                'completion_percentage' => 30.0,
                'status'                => 'active',
            ],
        ];
        foreach ($goals as $goal) {
            Goal::updateOrCreate(
                ['faculty_id' => $uid, 'title' => $goal['title']],
                array_merge($goal, ['faculty_id' => $uid])
            );
        }

        // ── Timetable Entries (4 active classes per week)
        $timetableEntries = [
            [
                'subject'     => 'Machine Learning',
                'day_of_week' => 'Monday',
                'time_slot'   => '09:00 – 10:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K23RA',
                'room'        => 'LH-201',
            ],
            [
                'subject'     => 'Data Structures & Algorithms',
                'day_of_week' => 'Monday',
                'time_slot'   => '11:00 – 12:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K24RB',
                'room'        => 'LH-103',
            ],
            [
                'subject'     => 'Artificial Intelligence',
                'day_of_week' => 'Wednesday',
                'time_slot'   => '10:00 – 11:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K23RA',
                'room'        => 'LH-201',
            ],
            [
                'subject'     => 'Machine Learning',
                'day_of_week' => 'Wednesday',
                'time_slot'   => '14:00 – 15:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K23RB',
                'room'        => 'LH-302',
            ],
            [
                'subject'     => 'Data Structures & Algorithms',
                'day_of_week' => 'Thursday',
                'time_slot'   => '09:00 – 10:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K24RB',
                'room'        => 'LH-103',
            ],
            [
                'subject'     => 'Cloud Computing',
                'day_of_week' => 'Friday',
                'time_slot'   => '11:00 – 12:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'B23RA',
                'room'        => 'Computer Lab 1',
            ],
            [
                'subject'     => 'Artificial Intelligence',
                'day_of_week' => 'Friday',
                'time_slot'   => '14:00 – 15:00',
                'semester'    => 'Even 2025-26',
                'section'     => 'K23RB',
                'room'        => 'LH-302',
            ],
        ];

        $entryIds = [];
        foreach ($timetableEntries as $entry) {
            // Remove existing active entry for same faculty+day+slot to avoid conflicts
            TimetableEntry::where('faculty_id', $uid)
                ->where('day_of_week', $entry['day_of_week'])
                ->where('time_slot', $entry['time_slot'])
                ->update(['is_active' => false]);

            $te = TimetableEntry::create(array_merge($entry, [
                'faculty_id' => $uid,
                'is_active'  => true,
            ]));
            $entryIds[] = (string) $te->id;
        }

        // ── Class Logs (12 weeks of history — realistic mix of conducted/cancelled)
        // Map entry index to which days they fall on
        $dayToEntryIndexes = [
            'Monday'    => [0, 1],
            'Wednesday' => [2, 3],
            'Thursday'  => [4],
            'Friday'    => [5, 6],
        ];

        for ($week = 11; $week >= 0; $week--) {
            foreach ($dayToEntryIndexes as $dayName => $indexes) {
                // Find the date of this day in the past $week weeks
                $targetDate = Carbon::now()->subWeeks($week)->startOfWeek(Carbon::MONDAY);
                $dayOffset  = array_search($dayName, ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']);
                $classDate  = $targetDate->copy()->addDays($dayOffset);

                // Skip future dates
                if ($classDate->isFuture()) {
                    continue;
                }

                foreach ($indexes as $idx) {
                    if (!isset($entryIds[$idx])) continue;

                    // 88% conducted, 8% cancelled, 4% substituted
                    $roll = rand(1, 100);
                    $status = $roll <= 88 ? 'conducted' : ($roll <= 96 ? 'cancelled' : 'substituted');
                    $remarks = match ($status) {
                        'cancelled'    => ['Medical leave', 'Conference attendance', 'Administrative duty', 'University event'][rand(0,3)],
                        'substituted'  => 'Covered by Dr. Arjun Verma',
                        default        => null,
                    };

                    ClassLog::updateOrCreate(
                        [
                            'timetable_entry_id' => $entryIds[$idx],
                            'date'               => $classDate->copy()->startOfDay(),
                        ],
                        [
                            'faculty_id' => $uid,
                            'status'     => $status,
                            'remarks'    => $remarks,
                            'logged_by'  => $uid,
                        ]
                    );
                }
            }
        }

        // ── Wellbeing Surveys (10 check-ins over 5 months — shows improving trend)
        $wellbeingEntries = [
            ['weeks_ago' => 20, 'workload' => 8, 'stress' => 8, 'motivation' => 5, 'support' => 5, 'burnout' => 78],
            ['weeks_ago' => 17, 'workload' => 7, 'stress' => 7, 'motivation' => 6, 'support' => 6, 'burnout' => 72],
            ['weeks_ago' => 14, 'workload' => 7, 'stress' => 6, 'motivation' => 6, 'support' => 6, 'burnout' => 65],
            ['weeks_ago' => 12, 'workload' => 6, 'stress' => 6, 'motivation' => 7, 'support' => 7, 'burnout' => 60],
            ['weeks_ago' => 10, 'workload' => 6, 'stress' => 5, 'motivation' => 7, 'support' => 7, 'burnout' => 55],
            ['weeks_ago' =>  8, 'workload' => 5, 'stress' => 5, 'motivation' => 8, 'support' => 8, 'burnout' => 50],
            ['weeks_ago' =>  6, 'workload' => 5, 'stress' => 4, 'motivation' => 8, 'support' => 8, 'burnout' => 47],
            ['weeks_ago' =>  4, 'workload' => 4, 'stress' => 4, 'motivation' => 9, 'support' => 8, 'burnout' => 42],
            ['weeks_ago' =>  2, 'workload' => 4, 'stress' => 3, 'motivation' => 9, 'support' => 9, 'burnout' => 38],
            ['weeks_ago' =>  0, 'workload' => 4, 'stress' => 3, 'motivation' => 9, 'support' => 9, 'burnout' => 35],
        ];

        // Delete old wellbeing data for Deep to avoid duplicates on re-seed
        WellbeingSurvey::where('faculty_id', $uid)->delete();

        foreach ($wellbeingEntries as $wb) {
            WellbeingSurvey::create([
                'faculty_id'    => $uid,
                'responses'     => [
                    'workload'   => $wb['workload'],
                    'stress'     => $wb['stress'],
                    'motivation' => $wb['motivation'],
                    'support'    => $wb['support'],
                ],
                'burnout_index' => $wb['burnout'],
                'notes'         => null,
                'surveyed_at'   => now()->subWeeks($wb['weeks_ago']),
            ]);
        }
    }

    // ────────────────────────────────────────────────────────────────────────────
    // Anonymous Student Feedbacks — seeded dummy data for all faculty
    // ────────────────────────────────────────────────────────────────────────────
    private function seedStudentFeedbacks(): void
    {
        // Wipe existing to avoid duplicates on re-seed
        StudentFeedback::truncate();

        $allComments = [
            'positive' => [
                'Excellent teaching style! Concepts are explained with real-world examples.',
                'Very engaging sessions. The faculty makes complex topics easy to understand.',
                'Always punctual and well-prepared. One of the best professors this semester.',
                'Clear explanations, approachable, and genuinely cares about student learning.',
                'Great at breaking down difficult concepts. Really helpful during office hours.',
                'The interactive sessions make it easy to follow along and participate.',
                'I appreciate the structured approach to delivering lectures every time.',
                'Very responsive to doubts and always follows up to ensure we understand.',
                'Excellent command over the subject and always up to date with industry trends.',
                'Teaching quality has noticeably improved this semester. Keep it up!',
                'The practical examples used in class really help connect theory to reality.',
                'Assignments are well-designed and really test understanding, not just memory.',
            ],
            'neutral' => [
                'Good overall. Could benefit from more interactive activities.',
                'Decent teaching, but pace is sometimes too fast to keep up with.',
                'Content is comprehensive, but slides could be more visual.',
                'Punctuality is good, engagement could improve with more student questions.',
                'Solid fundamentals covered. Would appreciate more real-world case studies.',
                'Clear delivery, but lectures can feel monotonous at times.',
                'Good knowledge of the subject. More practice problems would help.',
                'Helpful during office hours. Classroom engagement needs improvement.',
            ],
            'constructive' => [
                'Please slow down during complex derivations — we lose track quickly.',
                'Would love more hands-on lab sessions alongside theory classes.',
                'Sometimes hard to follow when topics shift too quickly.',
                'Could be more encouraging when students ask basic questions.',
                'Audio quality in online sessions could be improved.',
            ],
        ];

        // Per-faculty feedback configuration: [email, submissions, score profile]
        $facultyConfigs = [
            [
                'email'       => 'arjun@facultyelevate.test',
                'submissions' => 14,
                'profile'     => ['clarity' => [0.82, 0.94], 'communication' => [0.80, 0.92], 'punctuality' => [0.85, 0.96], 'engagement' => [0.75, 0.90]],
                'comment_mix' => ['positive' => 8, 'neutral' => 4, 'constructive' => 2],
            ],
            [
                'email'       => 'sana@facultyelevate.test',
                'submissions' => 11,
                'profile'     => ['clarity' => [0.74, 0.88], 'communication' => [0.72, 0.86], 'punctuality' => [0.78, 0.92], 'engagement' => [0.70, 0.84]],
                'comment_mix' => ['positive' => 6, 'neutral' => 3, 'constructive' => 2],
            ],
            [
                'email'       => 'raj@facultyelevate.test',
                'submissions' => 9,
                'profile'     => ['clarity' => [0.58, 0.74], 'communication' => [0.55, 0.70], 'punctuality' => [0.62, 0.78], 'engagement' => [0.52, 0.68]],
                'comment_mix' => ['positive' => 3, 'neutral' => 4, 'constructive' => 2],
            ],
            [
                'email'       => 'divya@facultyelevate.test',
                'submissions' => 8,
                'profile'     => ['clarity' => [0.42, 0.60], 'communication' => [0.40, 0.58], 'punctuality' => [0.65, 0.80], 'engagement' => [0.38, 0.55]],
                'comment_mix' => ['positive' => 2, 'neutral' => 4, 'constructive' => 2],
            ],
            [
                'email'       => 'faculty@facultyelevate.test',
                'submissions' => 10,
                'profile'     => ['clarity' => [0.65, 0.80], 'communication' => [0.62, 0.78], 'punctuality' => [0.70, 0.85], 'engagement' => [0.60, 0.76]],
                'comment_mix' => ['positive' => 5, 'neutral' => 3, 'constructive' => 2],
            ],
            [
                'email'       => 'deepkaurbhamber123@gmail.com',
                'submissions' => 15,
                'profile'     => ['clarity' => [0.84, 0.96], 'communication' => [0.82, 0.94], 'punctuality' => [0.88, 0.98], 'engagement' => [0.80, 0.95]],
                'comment_mix' => ['positive' => 11, 'neutral' => 3, 'constructive' => 1],
            ],
        ];

        foreach ($facultyConfigs as $config) {
            $user = User::where('email', $config['email'])->first();
            if (! $user) continue;

            $uid = (string) $user->id;
            $n   = $config['submissions'];

            // Build comment pool for this faculty
            $commentPool = [];
            foreach ($config['comment_mix'] as $type => $count) {
                $pool = $allComments[$type];
                shuffle($pool);
                $commentPool = array_merge($commentPool, array_slice($pool, 0, $count));
            }
            shuffle($commentPool);

            for ($i = 0; $i < $n; $i++) {
                $daysAgo = rand(1, 90); // Spread over last 3 months

                // Generate scores within the configured range for this faculty
                $scores = [];
                foreach ($config['profile'] as $dim => [$min, $max]) {
                    $scores[$dim] = round($min + lcg_value() * ($max - $min), 2);
                }

                StudentFeedback::create([
                    'faculty_id'     => $uid,
                    'feedback_token' => null, // No token needed — directly seeded
                    'scores'         => $scores,
                    'comment'        => isset($commentPool[$i]) ? $commentPool[$i] : null,
                    'submitted_at'   => now()->subDays($daysAgo)->subHours(rand(0, 8)),
                ]);
            }
        }
    }
}
