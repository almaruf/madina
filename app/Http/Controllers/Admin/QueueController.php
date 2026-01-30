<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class QueueController extends Controller
{
    public function index()
    {
        return view('admin.queue.index');
    }

    public function jobs()
    {
        // Get pending jobs
        $jobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                return [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'attempts' => $job->attempts,
                    'reserved_at' => $job->reserved_at ? date('Y-m-d H:i:s', $job->reserved_at) : null,
                    'available_at' => date('Y-m-d H:i:s', $job->available_at),
                    'created_at' => date('Y-m-d H:i:s', $job->created_at),
                    'job_name' => $payload['displayName'] ?? 'Unknown',
                    'payload' => $payload,
                ];
            });

        // Get failed jobs
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);
                return [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'connection' => $job->connection,
                    'queue' => $job->queue,
                    'failed_at' => $job->failed_at,
                    'exception' => substr($job->exception, 0, 500) . '...',
                    'job_name' => $payload['displayName'] ?? 'Unknown',
                ];
            });

        return response()->json([
            'pending_jobs' => $jobs,
            'failed_jobs' => $failedJobs,
            'pending_count' => $jobs->count(),
            'failed_count' => $failedJobs->count(),
        ]);
    }

    public function retryFailed($id)
    {
        Artisan::call('queue:retry', ['id' => [$id]]);
        
        return response()->json([
            'message' => 'Job queued for retry',
            'output' => Artisan::output()
        ]);
    }

    public function retryAllFailed()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);
        
        return response()->json([
            'message' => 'All failed jobs queued for retry',
            'output' => Artisan::output()
        ]);
    }

    public function deleteFailed($id)
    {
        Artisan::call('queue:forget', ['id' => $id]);
        
        return response()->json([
            'message' => 'Failed job deleted',
            'output' => Artisan::output()
        ]);
    }

    public function flushFailed()
    {
        Artisan::call('queue:flush');
        
        return response()->json([
            'message' => 'All failed jobs deleted',
            'output' => Artisan::output()
        ]);
    }
}
