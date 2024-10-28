<?php

namespace WalkerChiu\MorphLink;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MorphLink\Models\Entities\Link;
use WalkerChiu\MorphLink\Models\Entities\LinkLang;

class LinkLangTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on LinkLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\MorphLink\Models\Entities\MorphLinkLang
     *
     * @return void
     */
    public function testMorphLinkLang()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-link.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-link.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-link.soft_delete', 1);

        // Give
        factory(Link::class, 2)->create();
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'description']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name']);
        factory(LinkLang::class)->create(['morph_id' => 2, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name']);
        factory(LinkLang::class)->create(['morph_id' => 2, 'morph_type' => Link::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = LinkLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = LinkLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(Link::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = LinkLang::ofCode('en_us')
                                   ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = LinkLang::ofKey('name')
                                   ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = LinkLang::ofCodeAndKey('en_us', 'name')
                                   ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = LinkLang::ofMatch('en_us', 'name', 'Hello')
                                   ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on LinkLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\MorphLink\Models\Entities\MorphLinkLang
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
        factory(Link::class, 2)->create();
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'description']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(LinkLang::class)->create(['morph_id' => 1, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name']);
        factory(LinkLang::class)->create(['morph_id' => 2, 'morph_type' => Link::class, 'code' => 'en_us', 'key' => 'name']);
        factory(LinkLang::class)->create(['morph_id' => 2, 'morph_type' => Link::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = Link::find(1);
            $lang_1   = LinkLang::find(1);
            $lang_4   = LinkLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(LinkLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = Link::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
