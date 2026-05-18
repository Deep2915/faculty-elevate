# AGENTS.md — Faculty Elevate

## Smart Faculty Capacity Building & Performance Assessment Platform

---

## PROJECT OVERVIEW

**Faculty Elevate** is a SaaS-grade Human Capital Management (HCM) suite for educational institutions. It tracks faculty performance, delivers tailored professional development, gamifies growth, and provides actionable analytics to administrators and department heads.

---

## TECH STACK

| Layer             | Technology                            |
| ----------------- | ------------------------------------- |
| Backend Framework | Laravel 13 (MVC)                      |
| Database          | MongoDB via `mongodb/laravel-mongodb` |
| Frontend          | Blade Templates + Tailwind CSS        |
| Authentication    | Laravel Breeze                        |
| Charts            | Chart.js                              |
| PDF Reports       | barryvdh/laravel-dompdf               |
| Notifications     | Laravel Database + Mail               |
| Package Manager   | Composer + npm                        |

---

## AGENT RULES & CONSTRAINTS

### General Coding Rules

- Always follow **SOLID principles** — single responsibility per class, no fat controllers.
- All models **must** extend `MongoDB\Laravel\Eloquent\Model`, never the standard Eloquent Model.
- Use **typed properties** and **return types** on all methods.
- All business logic lives in **Service classes** (`app/Services/`), not controllers.
- Controllers are thin — they receive a request, call a service, return a response.
- Never write raw queries; use Eloquent scopes and query builder methods.
- All monetary or percentage values are stored as `float`, never `string`.
- Dates are stored as `Carbon` instances; always use `$casts` in models.

### MongoDB-Specific Rules

- Every model must have a `$collection` property explicitly defined.
- Use **embedded documents** for nested data that is always read together (e.g., `scores` inside `Evaluation`).
- Use **referenced IDs** (string ObjectId) for cross-collection relationships.
- Never use `Schema::create()` — MongoDB is schemaless; use seeders + model `$fillable` instead.
- Index heavy-query fields in `AppServiceProvider` using `Schema::connection('mongodb')`.

### Frontend Rules

- All UI uses **Tailwind CSS utility classes only** — no custom CSS files unless absolutely necessary.
- Dashboard aesthetic: **glassmorphism cards**, subtle gradients, and sidebar navigation.
- Every data table must be **responsive** and include search + sort.
- Chart.js graphs must be initialized in a `@push('scripts')` stack, never inline.
- Use **Alpine.js** for modal toggles and tab switching (already bundled with Breeze).
- Animate progress bars using Tailwind's `transition` and `duration` utilities.

### Authentication & Authorization Rules

- Use **Laravel Breeze** for the auth scaffold — do not rebuild login/register from scratch.
- Roles: `admin`, `hod`, `faculty`. Store role as a string field on the `User` model.
- Use **Laravel Gates** or a `RoleMiddleware` — never check roles directly in Blade with `if ($user->role == 'admin')` in controllers.
- Protect all routes with appropriate middleware groups: `auth`, `role:admin`, `role:hod`, `role:faculty`.

---

## DIRECTORY STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserManagementController.php
│   │   │   └── WeightConfigController.php
│   │   ├── HOD/
│   │   │   ├── EvaluationController.php
│   │   │   └── LeaderboardController.php
│   │   └── Faculty/
│   │       ├── DashboardController.php
│   │       ├── WorkshopController.php
│   │       ├── AchievementController.php
│   │       ├── GoalController.php
│   │       └── WellbeingController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── StoreEvaluationRequest.php
│       └── StoreWorkshopRequest.php
├── Models/
│   ├── User.php
│   ├── FacultyProfile.php
│   ├── Evaluation.php
│   ├── Workshop.php
│   ├── Achievement.php
│   ├── Badge.php
│   ├── Goal.php
│   └── Feedback.php
├── Services/
│   ├── PerformanceIndexService.php
│   ├── RecommendationEngineService.php
│   ├── GamificationService.php
│   └── ReportGeneratorService.php
├── Notifications/
│   ├── BadgeEarnedNotification.php
│   ├── WorkshopReminderNotification.php
│   └── EvaluationPublishedNotification.php
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          # Main auth layout
│   │   └── dashboard.blade.php    # Sidebar + topbar shell
│   ├── admin/
│   ├── hod/
│   └── faculty/
│       ├── dashboard.blade.php
│       ├── roadmap.blade.php
│       ├── leaderboard.blade.php
│       └── wellbeing.blade.php
routes/
└── web.php
```

---

## DATA MODELS (MongoDB Collections)

### `users`

```
_id, name, email, password, role (admin|hod|faculty),
department_id, avatar, email_verified_at, created_at, updated_at
```

### `faculty_profiles`

```
_id, user_id, bio, department, designation, joining_date,
skills: [string],
research_score: float,
teaching_score: float,
innovation_score: float,
performance_index: float,
xp: int,
level: int,
rank: int,
created_at, updated_at
```

### `evaluations`

```
_id, faculty_id, evaluator_id (hod), period (Q1/Q2/Annual),
scores: {
  research: float,
  teaching: float,
  innovation: float,
  student_clarity: float,
  attendance: float
},
weighted_score: float,
remarks: string,
status: (draft|published),
created_at, updated_at
```

### `workshops`

```
_id, title, description, facilitator, category, schedule_date,
duration_hours, capacity, registered_faculty_ids: [ObjectId],
xp_reward: int, status: (upcoming|ongoing|completed),
created_at, updated_at
```

### `achievements`

```
_id, faculty_id, type (publication|patent|award|certification),
title, journal_or_body, date, xp_awarded: int,
verified: bool, proof_url, created_at, updated_at
```

### `badges`

```
_id, name, slug, description, icon_svg, criteria: {},
xp_threshold: int, category (research|teaching|innovation|attendance)
```

### `goals`

```
_id, faculty_id, title, description, target_date,
milestones: [{ title, completed: bool, completed_at }],
completion_percentage: float, status (active|completed|overdue),
created_at, updated_at
```

### `feedbacks`

```
_id, faculty_id, reviewer_id, type (student|peer),
scores: { clarity: float, punctuality: float, knowledge: float },
comment: string, is_anonymous: bool, created_at
```

### `wellbeing_surveys`

```
_id, faculty_id,
responses: { workload: int, stress: int, motivation: int, support: int },
burnout_index: float, notes: string, surveyed_at
```

---

## MODULE LOGIC SPECIFICATIONS

### A. Performance Index Calculation

- Admin sets weights via `WeightConfig` (stored in a `settings` collection).
- Default: `Research = 40%, Teaching = 40%, Innovation = 20%`.
- Formula: `PI = (research_score * w_research) + (teaching_score * w_teaching) + (innovation_score * w_innovation)`
- `PerformanceIndexService::calculate(FacultyProfile $profile): float`
- Recalculate PI whenever a new `Evaluation` is published.
- Store the result back on `faculty_profiles.performance_index`.

### B. Recommendation Engine (Rule-Based)

- Implemented in `RecommendationEngineService::getRecommendations(FacultyProfile $profile): array`
- Rules (evaluated in order):
    - `student_clarity < 0.70` → suggest workshops tagged `pedagogy`
    - `innovation_score < 0.60` → suggest workshops tagged `design-thinking` or `edtech`
    - `research_score < 0.50` → suggest certifications tagged `research-methodology`
    - Missing skill in profile vs. department benchmark → flag gap + suggest resource
- Returns an array of `Workshop` and `Certification` suggestion objects.

### C. Gamification

- `GamificationService::awardXP(User $user, int $xp, string $reason): void`
    - Adds XP to `faculty_profiles.xp`
    - Checks if XP threshold crosses a new level (every 500 XP = 1 level)
    - Checks badge criteria and awards badges if met
    - Fires `BadgeEarnedNotification` if a new badge is awarded
- XP Events:
    - Workshop completed: `+50 XP`
    - Paper published: `+100 XP`
    - Patent filed: `+150 XP`
    - Goal milestone completed: `+30 XP`
    - Peer feedback given: `+10 XP`
- Leaderboard is derived by querying `faculty_profiles` sorted by `xp desc`, grouped by `department`.

### D. PDF Report Generation

- `ReportGeneratorService::generateAnnualReport(User $faculty): string` (returns PDF path)
- Uses `barryvdh/laravel-dompdf`
- Template: `resources/views/reports/annual_growth_report.blade.php`
- Sections: Profile Summary, Performance Index trend, Badges earned, HOD remarks, Goals completed, Recommendations.

### E. Wellbeing / Burnout Index

- `burnout_index = (10 - avg(workload, stress)) / 10 * 100` (higher = healthier)
- Chart.js line graph showing burnout index over the last 6 survey entries.
- Alert notification to HOD if burnout_index < 40 for two consecutive surveys.

---

## ROUTE STRUCTURE (`routes/web.php`)

```php
// Public
Route::get('/', fn() => redirect('/login'));

// Auth (Breeze)
require __DIR__.'/auth.php';

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', Admin\UserManagementController::class);
    Route::get('/weights', [Admin\WeightConfigController::class, 'edit'])->name('weights.edit');
    Route::put('/weights', [Admin\WeightConfigController::class, 'update'])->name('weights.update');
    Route::resource('workshops', Admin\WorkshopController::class);
    Route::resource('badges', Admin\BadgeController::class);
});

// HOD Routes
Route::middleware(['auth', 'role:hod'])->prefix('hod')->name('hod.')->group(function () {
    Route::get('/dashboard', [HOD\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('evaluations', HOD\EvaluationController::class);
    Route::get('/leaderboard', [HOD\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/faculty/{user}/report', [HOD\ReportController::class, 'download'])->name('report.download');
});

// Faculty Routes
Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [Faculty\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/roadmap', [Faculty\GoalController::class, 'roadmap'])->name('roadmap');
    Route::resource('goals', Faculty\GoalController::class);
    Route::resource('achievements', Faculty\AchievementController::class);
    Route::get('/workshops', [Faculty\WorkshopController::class, 'index'])->name('workshops.index');
    Route::post('/workshops/{workshop}/register', [Faculty\WorkshopController::class, 'register'])->name('workshops.register');
    Route::get('/leaderboard', [Faculty\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/wellbeing', [Faculty\WellbeingController::class, 'index'])->name('wellbeing');
    Route::post('/wellbeing', [Faculty\WellbeingController::class, 'store'])->name('wellbeing.store');
    Route::get('/profile', [Faculty\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [Faculty\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/report/download', [Faculty\ReportController::class, 'download'])->name('report.download');
});
```

---

## ENVIRONMENT SETUP

```bash
# 1. Create Laravel project
composer create-project laravel/laravel faculty-elevate

# 2. Install MongoDB driver
composer require mongodb/laravel-mongodb

# 3. Install Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev

# 4. Install DOMPDF
composer require barryvdh/laravel-dompdf

# 5. Publish configs
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### `.env` additions

```
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=faculty_elevate
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@facultyelevate.com"
MAIL_FROM_NAME="Faculty Elevate"
```

### `config/database.php` — add MongoDB connection

```php
'mongodb' => [
    'driver'   => 'mongodb',
    'host'     => env('DB_HOST', '127.0.0.1'),
    'port'     => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE', 'faculty_elevate'),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'options'  => ['appname' => 'FacultyElevate'],
],
```

---

## KEY CODE CONTRACTS

### `FacultyProfile` model must implement:

```php
public function calculatePerformanceScore(): float
// Fetches weights from settings collection, computes PI, saves to $this->performance_index
```

### `EvaluationController@store` must:

1. Validate via `StoreEvaluationRequest`
2. Call `PerformanceIndexService::calculate()`
3. Update `FacultyProfile`
4. Call `GamificationService` if scores improved
5. Fire `EvaluationPublishedNotification`

### `RecommendationEngineService` must return:

```php
[
  'workshops' => Workshop[],
  'certifications' => string[],
  'skill_gaps' => string[],
]
```

---

## DASHBOARD UI COMPONENTS (Blade + Tailwind)

```
dashboard.blade.php layout:
├── Sidebar (fixed, glassmorphism bg-white/10 backdrop-blur)
│   ├── Logo
│   ├── Nav links (Dashboard, Goals, Workshops, Achievements, Wellbeing, Reports)
│   └── User avatar + role badge
├── Topbar
│   ├── Page title
│   ├── Notification bell (unread count badge)
│   └── Quick XP display
└── Main content area
    ├── KPI Cards row (Total XP, Global Rank, PI Score, Pending Evaluations)
    ├── Charts row
    │   ├── Performance Trend (Line chart — Chart.js)
    │   └── Score Breakdown (Radar chart — Chart.js)
    ├── Activity Timeline (LinkedIn-style feed)
    └── Recommendations panel
```

---

## GAMIFICATION DISPLAY RULES

- **XP Bar**: Show current XP / next level threshold as an animated Tailwind progress bar.
- **Badges**: Display as SVG icon grid — grayscale if locked, colored if earned.
- **Leaderboard**: Table with rank change arrows (↑↓), avatar, name, department, XP.
- **Level**: Display as "Level 7 — Associate Researcher" with a tier label system.

Tier labels:

```
Level 1–3:   Emerging Educator
Level 4–6:   Skilled Practitioner
Level 7–10:  Associate Researcher
Level 11–15: Senior Scholar
Level 16+:   Distinguished Fellow
```

---

## NOTIFICATION TRIGGERS

| Event                     | Notification Class                | Channel         |
| ------------------------- | --------------------------------- | --------------- |
| Badge earned              | `BadgeEarnedNotification`         | database + mail |
| Evaluation published      | `EvaluationPublishedNotification` | database + mail |
| Workshop in 24h           | `WorkshopReminderNotification`    | database        |
| Burnout index < 40        | `BurnoutAlertNotification`        | mail (to HOD)   |
| Goal deadline approaching | `GoalDeadlineNotification`        | database        |

---

## DO NOT DO (Anti-patterns)

- ❌ Do not use `Illuminate\Database\Eloquent\Model` — always use `MongoDB\Laravel\Eloquent\Model`
- ❌ Do not put scoring logic in controllers — use `PerformanceIndexService`
- ❌ Do not hardcode role strings in Blade — use `@role('admin')` directive or Gate facade
- ❌ Do not skip `$fillable` on MongoDB models — mass assignment still applies
- ❌ Do not generate PDFs inline in controllers — delegate to `ReportGeneratorService`
- ❌ Do not store Chart.js data as PHP arrays in Blade — JSON-encode and pass via `@json()`
- ❌ Do not use `session()->flash()` for persistent notifications — use the Notification system

---

## TESTING CHECKLIST (per module)

- [ ] Admin can set scoring weights and PI recalculates on next evaluation save
- [ ] HOD can create, draft, and publish an evaluation; faculty is notified
- [ ] Recommendation engine returns correct workshops for sub-70% clarity score
- [ ] XP is awarded correctly and badge is triggered at threshold
- [ ] Leaderboard updates in real-time (session refresh) after XP change
- [ ] PDF report generates with all sections and is downloadable
- [ ] Wellbeing survey saves and burnout index is computed correctly
- [ ] Notification bell shows unread count and marks as read on click
