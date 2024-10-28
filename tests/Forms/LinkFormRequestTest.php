<?php

namespace WalkerChiu\MorphLink;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\MorphLink\Models\Entities\Link;
use WalkerChiu\MorphLink\Models\Forms\LinkFormRequest;

class LinkFormRequestTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations(['--database' => 'mysql']);
        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');

        $this->request  = new LinkFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
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
     * Unit test about Authorize.
     *
     * For WalkerChiu\MorphLink\Models\Forms\LinkFormRequest
     * 
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\MorphLink\Models\Forms\LinkFormRequest
     * 
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();


        // Give
        $attributes = [
            'type'       => $faker->randomElement(['link', 'blog', 'facebook', 'instagram', 'twitter']),
            'serial'     => $faker->isbn10,
            'target'     => '_blank',
            'name'       => $faker->name,
            'url'        => $faker->url,
            'is_enabled' => 1,
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'type'       => $faker->randomElement(['link', 'blog', 'facebook', 'instagram', 'twitter']),
            'serial'     => $faker->isbn10,
            'target'     => '_blank',
            'name'       => $faker->name,
            'url'        => $faker->url,
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
