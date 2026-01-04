<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class KycDefenseController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()) {
            abort(403);
        }

        return view('ttt');
    }

    public function analyze(Request $request)
    {
        if (! $request->user()) {
            abort(403);
        }

        $data = $request->validate([
            'document' => ['required', 'file', 'image', 'max:12288'],
            'selfie' => ['nullable', 'file', 'image', 'max:12288'],
        ]);

        $analysisId = (string) Str::uuid();
        $uploadsDir = "kyc_defense_uploads/{$analysisId}";
        $artifactsDir = "kyc_defense/{$analysisId}";
        Storage::disk('local')->makeDirectory($uploadsDir);
        Storage::disk('local')->makeDirectory('kyc_defense_reports');
        Storage::disk('public')->makeDirectory($artifactsDir);
        $reportsDir = Storage::disk('local')->path('kyc_defense_reports');

        $docFile = $data['document'];
        $docName = 'document.' . $docFile->getClientOriginalExtension();
        $docPath = $docFile->storeAs($uploadsDir, $docName, 'local');
        $docFullPath = Storage::disk('local')->path($docPath);

        $selfieFullPath = null;
        if (! empty($data['selfie'])) {
            $selfieFile = $data['selfie'];
            $selfieName = 'selfie.' . $selfieFile->getClientOriginalExtension();
            $selfiePath = $selfieFile->storeAs($uploadsDir, $selfieName, 'local');
            $selfieFullPath = Storage::disk('local')->path($selfiePath);
        }

        $reportPath = $reportsDir . '/' . $analysisId . '.json';
        $artifactFullPath = Storage::disk('public')->path($artifactsDir);

        $pythonCandidates = [
            base_path('.venv/bin/python'),
            '/usr/bin/python3',
            'python3',
        ];
        $python = null;
        foreach ($pythonCandidates as $candidate) {
            if ($candidate === 'python3' || file_exists($candidate)) {
                $python = $candidate;
                break;
            }
        }

        if (! $python) {
            return response()->json([
                'message' => 'python_not_found',
                'detail' => 'Python executable was not found for analysis.',
            ], 500);
        }

        $command = [
            $python,
            '-m',
            'kyc_defense.cli',
            'analyze',
            '--doc',
            $docFullPath,
            '--out',
            $reportPath,
            '--save-artifacts',
            $artifactFullPath,
        ];

        if ($selfieFullPath) {
            $command[] = '--selfie';
            $command[] = $selfieFullPath;
        }

        $process = new Process($command, base_path());
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            return response()->json([
                'message' => 'analysis_failed',
                'detail' => trim($process->getErrorOutput() ?: $process->getOutput()),
            ], 500);
        }

        if (! file_exists($reportPath)) {
            return response()->json([
                'message' => 'report_missing',
                'detail' => 'Report file was not generated.',
            ], 500);
        }

        $report = json_decode(file_get_contents($reportPath), true);
        if (! is_array($report)) {
            return response()->json([
                'message' => 'report_invalid',
                'detail' => 'Report JSON could not be parsed.',
            ], 500);
        }

        $report['analysis_id'] = $analysisId;
        $report['artifacts'] = $report['artifacts'] ?? [];
        $report['artifacts']['ela_heatmap_url'] = $this->artifactUrl($artifactsDir, 'ela_heatmap.png');
        $report['artifacts']['doc_issue_overlay_url'] = $this->artifactUrl($artifactsDir, 'doc_issue_overlay.png');
        $report['artifacts']['selfie_preview_url'] = $this->artifactUrl($artifactsDir, 'selfie_preview.png');

        return response()->json($report);
    }

    private function artifactUrl(string $dir, string $filename): ?string
    {
        $relative = $dir . '/' . $filename;
        if (! Storage::disk('public')->exists($relative)) {
            return null;
        }

        return asset('storage/' . $relative);
    }
}
