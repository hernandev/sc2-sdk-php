<?php

namespace SteemConnect;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Class TestCase.
 *
 * Base test case for SteemConnect PHP SDK.
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var string Custom application name for testing.
     */
    protected $appName = 'sc2-php-sdk/0.1';

    /**
     * @var string Testing Client ID (OAuth).
     */
    protected $clientId = 'sc2-test.app';

    /**
     * @var string Testing Client Secret (OAuth).
     */
    protected $clientSecret = '0123456789abcdef0123456789abcdef0123456789abcdef';
}