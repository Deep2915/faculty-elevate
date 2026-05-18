<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\FacultyProfile;
use App\Models\User;

class GamificationService
{
    public function awardXP(User $user, int $xp, string $reason): void
    {
        $profile = FacultyProfile::firstOrCreate(
            ['user_id' => (string) $user->getKey()],
            [
                'xp' => 0,
                'level' => 1,
                'research_score' => 0.0,
                'teaching_score' => 0.0,
                'innovation_score' => 0.0,
                'performance_index' => 0.0,
            ]
        );

        $profile->xp    = ((int) $profile->xp) + $xp;
        $profile->level = max(1, intdiv((int) $profile->xp, 500) + 1);
        $profile->save();

        // Badge XP check (notifications disabled — no SQL connection)
        Badge::query()
            ->where('xp_threshold', '<=', $profile->xp)
            ->get();
        // $user->notify(new BadgeEarnedNotification(...)); // disabled: no Notifiable trait
    }
}
