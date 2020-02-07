<?php

declare(strict_types=1);

namespace Talboterie\FlysystemGCPStorage\Tests;

use PHPUnit\Framework\TestCase;
use Talboterie\FlysystemGCPStorage\Skeleton;

class SkeletonTest extends TestCase
{
    /** @test */
    public function itReturnAPhrase(): void
    {
        $skeleton = new Skeleton;
        $string = 'Hello Talboterie';

        $this->assertEquals($string, $skeleton->echoPhrase($string));
    }
}
