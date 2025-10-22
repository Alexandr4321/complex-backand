<?php

namespace App\System\Swagger\Testing;

use App\System\Swagger\Middleware\SwaggerMiddleware;
use App\System\Swagger\Services\StartTests;
use App\System\Swagger\Services\SwaggerService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    /**
     * @var SwaggerService
     */
    protected $service;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        StartTests::start();

//        $this->service = app(SwaggerService::class);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $currentTestCount = $this->getTestResultObject()->count();
        $allTestCount = $this->getTestResultObject()->topTestSuite()->count();

//        if (($currentTestCount == $allTestCount) && (!$this->hasFailed())) {
//            $this->service->saveToFile();
//        }

        parent::tearDown();
    }

    /**
     * Disabling documentation collecting on current test
     */
    public function skipDocumentationCollecting()
    {
        SwaggerMiddleware::$skipped = true;
    }
}
