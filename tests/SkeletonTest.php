<?php

declare(strict_types=1);

namespace {vendor:no-space}\{package_name:no-space}\Tests;

use PHPUnit\Framework\TestCase;
use {vendor:no-space}\{package_name:no-space}\Skeleton;

class SkeletonTest extends TestCase
{
    /** @test */
    public function itReturnAPhrase(): void
    {
        $skeleton = new Skeleton;
        $string = 'Hello {vendor}';

        $this->assertEquals($string, $skeleton->echoPhrase($string));
    }
}
