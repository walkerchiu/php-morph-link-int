<?php

namespace WalkerChiu\MorphLink\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class LinkRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;
    use ImageRepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.morph-link.link'));
    }

    /**
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(string $code, array $data, $is_enabled = null, $auto_packing = false)
    {
        $instance = $this->instance;
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                        $query->ofCurrent()
                                              ->ofCode($code);
                                    }])
                                ->whereHas('langs', function ($query) use ($code) {
                                        return $query->ofCurrent()
                                                     ->ofCode($code);
                                    })
                                ->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['morph_type']), function ($query) use ($data) {
                                                return $query->where('morph_type', $data['morph_type']);
                                            })
                                            ->unless(empty($data['morph_id']), function ($query) use ($data) {
                                                return $query->where('morph_id', $data['morph_id']);
                                            })
                                            ->unless(empty($data['type']), function ($query) use ($data) {
                                                return $query->where('type', $data['type']);
                                            })
                                            ->unless(empty($data['category']), function ($query) use ($data) {
                                                return $query->where('category', $data['category']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['target']), function ($query) use ($data) {
                                                return $query->where('target', $data['target']);
                                            })
                                            ->unless(empty($data['url']), function ($query) use ($data) {
                                                return $query->where('url', 'LIKE', $data['url']."%");
                                            })
                                            ->unless(empty($data['extension']), function ($query) use ($data) {
                                                return $query->where('extension', 'LIKE', $data['extension']."%");
                                            })
                                            ->unless(empty($data['name']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
                                                });
                                            })
                                            ->unless(empty($data['description']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'description')
                                                          ->where('value', 'LIKE', "%".$data['description']."%");
                                                });
                                            });
                                    })
                                ->orderBy('order', 'ASC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-morph-link.output_format'), config('wk-morph-link.pagination.pageName'), config('wk-morph-link.pagination.perPage'));
            $factory->setFieldsLang(['name', 'description']);
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Link           $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
    }
}
