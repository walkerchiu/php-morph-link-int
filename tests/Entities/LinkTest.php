<?php

namespace WalkerChiu\MorphLink;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MorphLink\Models\Entities\Link;
use WalkerChiu\MorphLink\Models\Entities\LinkLang;

class LinkTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MorphLink\MorphLinkServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Link.
     *
     * For WalkerChiu\MorphLink\Models\Entities\MorphLink
     * 
     * @return void
     */
    public function testMorphLink()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-link.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-link.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-link.soft_delete', 1);

        // Give
        $record_1 = factory(Link::class)->create();
        $record_2 = factory(Link::class)->create();
        $record_3 = factory(Link::class)->create(['is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Link::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $record_2->delete();
            $records = Link::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Link::withTrashed()
                    ->find(2)
                    ->restore();
            $record_2 = Link::find(2);
            $records = Link::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, LinkLang::class);

        // Scope query on enabled records
            // When
            $records = Link::ofEnabled()
                               ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Link::ofDisabled()
                               ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
