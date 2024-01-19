<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Profile;
use Tests\RefreshDatabaseFast;
use Tests\TestCase;

class ProfileLikesTest extends TestCase
{
    use RefreshDatabaseFast;

    public function testUserCanGiveAndReceiveLikes(): void
    {
        /** @var Profile $giving */
        $giving = Profile::factory()->create();

        /** @var Profile $receiving */
        $receiving = Profile::factory()->create();

        $this->assertEquals(0, $giving->likesToUsers()->count());
        $this->assertEquals(0, $giving->likesFromUsers()->count());
        $this->assertEquals(0, $giving->matches()->count());
        $this->assertEquals(0, $receiving->likesFromUsers()->count());
        $this->assertEquals(0, $receiving->likesToUsers()->count());
        $this->assertEquals(0, $receiving->matches()->count());

        $giving->likesToUsers()->save($receiving);

        $this->assertEquals(1, $giving->likesToUsers()->count());
        $this->assertEquals(0, $giving->likesFromUsers()->count());
        $this->assertEquals(0, $giving->matches()->count());
        $this->assertEquals(1, $receiving->likesFromUsers()->count());
        $this->assertEquals(0, $receiving->likesToUsers()->count());
        $this->assertEquals(0, $receiving->matches()->count());

        $receiving->likesToUsers()->save($giving);

        $receiving->refresh();
        $giving->refresh();

        $this->assertEquals(1, $giving->likesToUsers()->count());
        $this->assertEquals(1, $giving->likesFromUsers()->count());
        $this->assertEquals(1, $giving->matches()->count());
        $this->assertEquals(1, $receiving->likesFromUsers()->count());
        $this->assertEquals(1, $receiving->likesToUsers()->count());
        $this->assertEquals(1, $receiving->matches()->count());
    }
}
