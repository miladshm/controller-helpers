<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Miladshm\ControllerHelpers\Tests\TestCase;
use function PHPUnit\Framework\assertLessThan;

class HasGetCountTest extends TestCase
{

    public function testGetCount()
    {
        $response = $this->getJson('/count/status');

        $response->assertOk();
        $response->assertJsonStructure(getConfigNames('response.field_names'));

        $prev_count = null;
        foreach ($response->json('data.count') as $key => $count) {
            if ($prev_count)
                assertLessThan($prev_count, $count, "{$count} is not less than {$prev_count}");
            $prev_count = $count;
        }
    }
}
