<?php

namespace Tests\Feature\Repositories;

use App\Enums\JobAdStatus;
use App\Models\JobAd;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Client;
use App\Models\Position;
use App\Models\Shift;
use App\Repositories\API\JobAdRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobAdRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_job_ad_with_all_expected_relations_for_candidate(): void
    {
        // Arrange
        $user = User::factory()->create(['role_id' => \App\Enums\Roles::CANDIDATE]);
        $candidate = Candidate::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $client = Client::factory()->create();
        $position = Position::factory()->create();
        $jobAd = JobAd::factory()->create([
            'client_id' => $client->id,
            'position_id' => $position->id,
        ]);
        $shift = Shift::factory()->create(['job_ad_id' => $jobAd->id]);

        // Create pivot
        $candidate->jobAds()->attach($jobAd->id);

        // Act
        $repository = new JobAdRepository(new JobAd());
        $result = $repository->getJobAd($jobAd);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($jobAd->id, $result->id);
        $this->assertTrue($result->relationLoaded('client'));
        $this->assertTrue($result->relationLoaded('client.user'));
        $this->assertTrue($result->relationLoaded('position'));
        $this->assertTrue($result->relationLoaded('shifts'));
    }

    /** @test */
    public function get_job_ads_for_clients_test(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $activeJobAd = JobAd::factory()->create([
            'client_id' => $client->id,
            'job_ad_status_id' => JobAdStatus::APPROVED,
        ]);

        // Should be excluded (pending review)
        $pendingJobAd = JobAd::factory()->create([
            'client_id' => $client->id,
            'job_ad_status_id' => JobAdStatus::PENDING_REVIEW,
        ]);

        // Should be excluded (soft deleted)
        $deletedJobAd = JobAd::factory()->create([
            'client_id' => $client->id,
            'job_ad_status_id' => JobAdStatus::APPROVED,
            'deleted_at' => now(),
        ]);

        // Act
        $repository = new JobAdRepository(new JobAd());
        $result = $repository->getJobAdsForClients($user);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($activeJobAd->id, $result[0]->id);
        $this->assertEquals($client->company_name, $result[0]->company_name);
        $this->assertEquals($user->first_name, $result[0]->first_name);
    }
}
