<?php

namespace WalkerChiu\MorphLink\Models\Entities;

trait LinkTrait
{
    /**
     * @param String  $type
     * @param String  $category
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function links($type = null, $category = null)
    {
        return $this->morphMany(config('wk-core.class.morph-link.link'), 'morph')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            })
                    ->when($category, function ($query, $category) {
                                return $query->where('category', $category);
                            });
    }
}
