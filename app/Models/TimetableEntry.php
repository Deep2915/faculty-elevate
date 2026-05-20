<?php

namespace App\Models;

class TimetableEntry extends BaseMongoModel
{
    protected $collection = 'timetable_entries';

    protected $fillable = [
        'faculty_id',
        'subject',
        'day_of_week',
        'time_slot',
        'semester',
        'section',   // e.g. "K23RK"  (Program+Batch+Section)
        'room',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Dropdown option lists ──────────────────────────────────────────────

    public static function subjectOptions(): array
    {
        return [
            // Core CS
            'Data Structures & Algorithms',
            'Operating Systems',
            'Database Management Systems',
            'Computer Networks',
            'Software Engineering',
            'Theory of Computation',
            'Compiler Design',
            'Artificial Intelligence',
            'Machine Learning',
            'Computer Architecture',
            'Discrete Mathematics',
            'Digital Logic & Design',
            'Object-Oriented Programming',
            'Web Technologies',
            'Mobile Application Development',
            'Cloud Computing',
            'Cyber Security',
            'Big Data Analytics',
            'Internet of Things',
            // Mathematics & Science
            'Mathematics I', 'Mathematics II', 'Mathematics III',
            'Engineering Mathematics',
            'Physics', 'Chemistry', 'Environmental Science',
            'Probability & Statistics',
            // Management & Soft Skills
            'Positive Psychology',
            'Professional Ethics',
            'Technical Communication',
            'Entrepreneurship & Innovation',
            'Project Management',
            'Human Resource Management',
            // Others
            'Laboratory Work',
            'Seminar',
            'Research Methodology',
        ];
    }

    public static function timeSlotOptions(): array
    {
        return [
            '08:00 – 09:00',
            '09:00 – 10:00',
            '10:00 – 11:00',
            '11:00 – 12:00',
            '12:00 – 13:00',
            '13:00 – 14:00',
            '14:00 – 15:00',
            '15:00 – 16:00',
            '16:00 – 17:00',
            '17:00 – 18:00',
        ];
    }

    public static function semesterOptions(): array
    {
        $year = date('Y');
        $next = $year + 1;
        $prev = $year - 1;
        return [
            "Even {$prev}-{$year}",
            "Odd {$year}-" . substr($next, 2),
            "Even {$year}-" . substr($next, 2),
            "Odd {$next}-" . substr($next + 1, 2),
        ];
    }

    public static function roomOptions(): array
    {
        return [
            'LH-101', 'LH-102', 'LH-103', 'LH-104', 'LH-201', 'LH-202', 'LH-203',
            'LH-301', 'LH-302', 'LH-303', 'LH-304',
            'Lab-A', 'Lab-B', 'Lab-C', 'Lab-D',
            'Seminar Hall', 'Conference Room', 'Open Auditorium',
            'Computer Lab 1', 'Computer Lab 2', 'Computer Lab 3',
        ];
    }

    /**
     * Build section options from a pattern.
     * Format: {ProgramCode}{BatchYear}{SectionCode}
     * e.g. K23RK = K(program) 23(batch 2023-27) RK(section)
     */
    public static function sectionOptions(): array
    {
        $programs = ['K', 'B', 'M', 'D', 'E', 'C'];  // K=BCA/BCS, B=BTech, M=MTech, etc.
        $batches  = ['22', '23', '24', '25'];
        $sections = ['RA', 'RB', 'RC', 'RD', 'RK', 'RL', 'RM'];

        $options = [];
        foreach ($programs as $p) {
            foreach ($batches as $b) {
                foreach ($sections as $s) {
                    $options[] = $p . $b . $s;
                }
            }
        }
        return $options;
    }
}
