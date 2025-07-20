<?php

namespace App\Console\Commands;

use App\Services\JobAd\JobAdService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class CompleteJobCommand
 *
 * @package App\Console\Commands
 */
class CompleteJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complete_job:check_is_job_completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completing job ads';

    /**
     * @var JobAdService
     */
    private JobAdService $jobAdService;

    /**
     * @param JobAdService $jobAdService
     */
    public function __construct(JobAdService $jobAdService)
    {
        parent::__construct();
        $this->jobAdService = $jobAdService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Completing has started');

        try {
            $this->jobAdService->dailyChangesOfJobAdStatus();
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->error($exception->getMessage());
        }

        $this->info('Completion of job ads is completed');
    }
}
