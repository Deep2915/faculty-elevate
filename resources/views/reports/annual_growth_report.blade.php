<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Faculty Annual Growth Report – {{ $faculty->name }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', sans-serif; background: #f8fafc; color: #1e293b; font-size: 12px; line-height: 1.5; }

  /* Header */
  .header { background: linear-gradient(135deg, #4338ca 0%, #7c3aed 100%); color: white; padding: 32px 40px; }
  .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
  .brand { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
  .brand-sub { font-size: 10px; opacity: 0.75; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
  .report-badge { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); border-radius: 6px; padding: 6px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
  .faculty-name { font-size: 28px; font-weight: 800; margin-top: 20px; }
  .faculty-meta { font-size: 11px; opacity: 0.8; margin-top: 4px; }

  /* Section */
  .section { padding: 24px 40px; border-bottom: 1px solid #e2e8f0; }
  .section:last-child { border-bottom: none; }
  .section-title { font-size: 13px; font-weight: 800; color: #4338ca; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }

  /* KPI Grid */
  .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
  .kpi-box { background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
  .kpi-val { font-size: 22px; font-weight: 800; color: #4338ca; }
  .kpi-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.07em; margin-top: 4px; }

  /* Progress Bar */
  .progress-track { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 4px; }
  .progress-fill  { height: 100%; border-radius: 99px; }

  /* Score Row */
  .score-row { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
  .score-label { width: 120px; font-size: 11px; color: #475569; font-weight: 500; }
  .score-bar   { flex: 1; }
  .score-pct   { width: 44px; text-align: right; font-size: 11px; font-weight: 700; color: #4338ca; }

  /* Table */
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; border-bottom: 1px solid #e2e8f0; }
  td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #374151; vertical-align: middle; }
  tr:last-child td { border-bottom: none; }

  /* Badge */
  .badge-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
  .badge-box { background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center; }
  .badge-icon { font-size: 20px; margin-bottom: 6px; }
  .badge-name { font-size: 10px; font-weight: 700; color: #1e293b; }
  .badge-cat  { font-size: 9px; color: #64748b; margin-top: 2px; }

  /* Pill */
  .pill { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 9px; font-weight: 700; }
  .pill-green  { background: #dcfce7; color: #166534; }
  .pill-amber  { background: #fef9c3; color: #854d0e; }
  .pill-rose   { background: #fde8e8; color: #9b1c1c; }
  .pill-indigo { background: #e0e7ff; color: #3730a3; }

  /* Footer */
  .footer { background: #f1f5f9; padding: 14px 40px; font-size: 9px; color: #94a3b8; display: flex; justify-content: space-between; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
  <div class="header-top">
    <div>
      <div class="brand">Faculty Elevate</div>
      <div class="brand-sub">Smart Faculty Capacity Building Platform</div>
    </div>
    <div class="report-badge">Annual Growth Report</div>
  </div>
  <div class="faculty-name">{{ $faculty->name }}</div>
  <div class="faculty-meta">
    {{ $profile->designation ?? 'Faculty' }} · {{ $profile->department ?? '—' }} ·
    Generated: {{ $generatedAt->format('d M Y, H:i') }}
  </div>
</div>

{{-- KPI SUMMARY --}}
<div class="section">
  <div class="section-title">Performance Summary</div>
  <div class="kpi-grid">
    <div class="kpi-box">
      <div class="kpi-val">{{ number_format(($profile->performance_index ?? 0)*100, 1) }}%</div>
      <div class="kpi-lbl">Performance Index</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-val">{{ number_format($profile->xp ?? 0) }}</div>
      <div class="kpi-lbl">Total XP Earned</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-val">{{ $profile->level ?? 1 }}</div>
      <div class="kpi-lbl">Current Level</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-val">{{ $badges->count() }}</div>
      <div class="kpi-lbl">Badges Earned</div>
    </div>
  </div>
</div>

{{-- SCORE BREAKDOWN --}}
<div class="section">
  <div class="section-title">Score Breakdown</div>
  @foreach([
    'Research Score'    => $profile->research_score    ?? 0,
    'Teaching Score'    => $profile->teaching_score    ?? 0,
    'Innovation Score'  => $profile->innovation_score  ?? 0,
  ] as $label => $val)
  @php $pct = min(100, $val * 100); @endphp
  <div class="score-row">
    <div class="score-label">{{ $label }}</div>
    <div class="score-bar">
      <div class="progress-track">
        <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $pct>=80?'#10b981':($pct>=50?'#f59e0b':'#ef4444') }};"></div>
      </div>
    </div>
    <div class="score-pct">{{ number_format($pct, 0) }}%</div>
  </div>
  @endforeach
</div>

{{-- EVALUATION HISTORY --}}
@if($evaluations->isNotEmpty())
<div class="section">
  <div class="section-title">Evaluation History</div>
  <table>
    <thead><tr>
      <th>Period</th>
      <th>Research</th>
      <th>Teaching</th>
      <th>Innovation</th>
      <th>Weighted Score</th>
      <th>Status</th>
      <th>HOD Remarks</th>
    </tr></thead>
    <tbody>
      @foreach($evaluations as $eval)
      @php $ws = round(($eval->weighted_score ?? 0)*100, 1); @endphp
      <tr>
        <td><strong>{{ $eval->period }}</strong></td>
        <td>{{ round(data_get($eval,'scores.research',0)*100) }}%</td>
        <td>{{ round(data_get($eval,'scores.teaching',0)*100) }}%</td>
        <td>{{ round(data_get($eval,'scores.innovation',0)*100) }}%</td>
        <td><span class="pill {{ $ws>=80?'pill-green':($ws>=50?'pill-amber':'pill-rose') }}">{{ $ws }}%</span></td>
        <td><span class="pill pill-indigo">{{ ucfirst($eval->status) }}</span></td>
        <td style="color:#64748b; font-style:italic;">{{ Str::limit($eval->remarks ?? '—', 60) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- ACHIEVEMENTS --}}
@if($achievements->isNotEmpty())
<div class="section">
  <div class="section-title">Achievements & Publications</div>
  <table>
    <thead><tr><th>Type</th><th>Title</th><th>Journal / Body</th><th>Date</th><th>XP</th></tr></thead>
    <tbody>
      @foreach($achievements as $ach)
      <tr>
        <td><span class="pill pill-indigo">{{ ucfirst($ach->type) }}</span></td>
        <td><strong>{{ $ach->title }}</strong></td>
        <td style="color:#64748b;">{{ $ach->journal_or_body }}</td>
        <td>{{ \Carbon\Carbon::parse($ach->date)->format('M Y') }}</td>
        <td style="color:#854d0e; font-weight:700;">+{{ $ach->xp_awarded }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- BADGES --}}
@if($badges->isNotEmpty())
<div class="section">
  <div class="section-title">Badges Earned</div>
  <div class="badge-grid">
    @foreach($badges as $badge)
    <div class="badge-box">
      <div class="badge-icon">{{ ['research'=>'[R]','teaching'=>'[T]','innovation'=>'[I]','attendance'=>'[A]'][$badge->category] ?? '[B]' }}</div>
      <div class="badge-name">{{ $badge->name }}</div>
      <div class="badge-cat">{{ ucfirst($badge->category) }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- BIO --}}
@if(!empty($profile->bio))
<div class="section">
  <div class="section-title">Professional Bio</div>
  <p style="font-size:11px; color:#374151; line-height:1.7;">{{ $profile->bio }}</p>
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
  <span>Faculty Elevate – Confidential Report</span>
  <span>Generated on {{ $generatedAt->format('d M Y') }} for {{ $faculty->email }}</span>
</div>

</body>
</html>
