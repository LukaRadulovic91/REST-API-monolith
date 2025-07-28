<?php

namespace Tests\Unit\Services;

use App\Models\JobAd;
use App\Services\JobAd\JobAdService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class JobAdServiceTest extends TestCase
{
    use WithFaker;

    protected JobAdService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->getMockBuilder(JobAdService::class)
            ->onlyMethods(['createJobAd', 'createShifts', 'changeStatus'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /** @test */
    public function it_stores_job_ad_entity_with_shifts(): void
    {
        // Arrange
        $request = new Request([
            'title' => 'Test Job',
            'shifts' => ['08:00-12:00', '13:00-17:00']
        ]);

        $jobAdMock = new JobAd(['title' => 'Test Job']);

        $this->service->expects($this->once())
            ->method('createJobAd')
            ->with($request)
            ->willReturn($jobAdMock);

        $this->service->expects($this->once())
            ->method('createShifts')
            ->with($request, $jobAdMock);

        $this->service->expects($this->once())
            ->method('changeStatus')
            ->with($request, $jobAdMock);

        // Act
        $result = $this->service->storeEntity($request);

        // Assert
        $this->assertInstanceOf(JobAd::class, $result);
        $this->assertEquals('Test Job', $result->title);
    }

    /** @test */
    public function it_stores_job_ad_entity_without_shifts(): void
    {
        // Arrange
        $request = new Request([
            'title' => 'Job Without Shifts'
        ]);

        $jobAdMock = new JobAd(['title' => 'Job Without Shifts']);

        $this->service->expects($this->once())
            ->method('createJobAd')
            ->willReturn($jobAdMock);

        $this->service->expects($this->never())
            ->method('createShifts');

        $this->service->expects($this->once())
            ->method('changeStatus')
            ->with($request, $jobAdMock);

        // Act
        $result = $this->service->storeEntity($request);

        // Assert
        $this->assertInstanceOf(JobAd::class, $result);
        $this->assertEquals('Job Without Shifts', $result->title);
    }
}
