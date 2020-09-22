<?php

namespace OwowAgency\LaravelNotifications\Tests;

use OwowAgency\Snapshots\MatchesSnapshots;
use OwowAgency\LaravelTestResponse\TestResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use MatchesSnapshots;

    /**
     * Create the test response instance from the given response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Testing\TestResponse
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }
}
