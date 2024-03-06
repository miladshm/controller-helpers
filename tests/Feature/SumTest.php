<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Miladshm\ControllerHelpers\Tests\TestCase;
use function PHPUnit\Framework\assertLessThan;

class SumTest extends TestCase
{
    public function test_sum_group_by_descending()
    {
        $response = $this->getJson('sum/count?group_by=status');
        $response->assertOk();
        $response->assertJsonStructure(getConfigNames('response.field_names'));

        $prev_sum = null;
        foreach ($response->json('data.sum') as $key => $sum) {
            if ($prev_sum)
                assertLessThan($prev_sum, $sum, "{$sum} is not less than {$prev_sum}");
            $prev_sum = $sum;
        }
    }

    public function test_sum_descending()
    {
        $response = $this->getJson('sum/count');
        $response->assertOk();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
    }
}
