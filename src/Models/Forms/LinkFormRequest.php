<?php

namespace WalkerChiu\MorphLink\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class LinkFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'morph_type'  => trans('php-morph-link::system.morph_type'),
            'morph_id'    => trans('php-morph-link::system.morph_id'),
            'type'        => trans('php-morph-link::system.type'),
            'category'    => trans('php-morph-link::system.category'),
            'serial'      => trans('php-morph-link::system.serial'),
            'target'      => trans('php-morph-link::system.target'),
            'url'         => trans('php-morph-link::system.url'),
            'extension'   => trans('php-morph-link::system.extension'),
            'order'       => trans('php-morph-link::system.order'),
            'is_enabled'  => trans('php-morph-link::system.is_enabled'),

            'name'        => trans('php-morph-link::system.name'),
            'description' => trans('php-morph-link::system.description')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'morph_type'  => 'required_with|string',
            'morph_id'    => 'required_with|integer|min:1',
            'type'        => 'required',
            'category'    => '',
            'serial'      => '',
            'target'      => 'required|string|max:10',
            'url'         => 'required|active_url',
            'extension'   => '',
            'order'       => 'nullable|numeric|min:0',
            'is_enabled'  => 'required|boolean',

            'name'        => 'required|string|max:255',
            'description' => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.morph-link.links').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.morph-link.links').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'              => trans('php-core::validation.required'),
            'id.integer'               => trans('php-core::validation.integer'),
            'id.min'                   => trans('php-core::validation.min'),
            'id.exists'                => trans('php-core::validation.exists'),
            'morph_type.required_with' => trans('php-core::validation.required_with'),
            'morph_type.string'        => trans('php-core::validation.string'),
            'morph_id.required_with'   => trans('php-core::validation.required_with'),
            'morph_id.integer'         => trans('php-core::validation.integer'),
            'morph_id.min'             => trans('php-core::validation.min'),
            'type.required'            => trans('php-core::validation.required'),
            'type.max'                 => trans('php-core::validation.max'),
            'target.required'          => trans('php-core::validation.required'),
            'target.max'               => trans('php-core::validation.max'),
            'url.required'             => trans('php-core::validation.required'),
            'url.url'                  => trans('php-core::validation.active_url'),
            'order.numeric'            => trans('php-core::validation.numeric'),
            'order.min'                => trans('php-core::validation.min'),
            'is_enabled.required'      => trans('php-core::validation.required'),
            'is_enabled.boolean'       => trans('php-core::validation.boolean'),

            'name.required' => trans('php-core::validation.required'),
            'name.string'   => trans('php-core::validation.string'),
            'name.max'      => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                isset($data['morph_type'])
                && isset($data['morph_id'])
            ) {
                if (
                    config('wk-morph-link.onoff.site-mall')
                    && !empty(config('wk-core.class.site-mall.site'))
                    && $data['morph_type'] == config('wk-core.class.site-mall.site')
                ) {
                    $result = DB::table(config('wk-core.table.site-mall.sites'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-morph-link.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['morph_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-morph-link.onoff.account')
                    && !empty(config('wk-core.class.account.profile'))
                    && $data['morph_type'] == config('wk-core.class.account.profile')
                ) {
                    $result = DB::table(config('wk-core.table.account.profiles'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                } elseif (
                    !empty(config('wk-core.class.user'))
                    && $data['morph_type'] == config('wk-core.class.user')
                ) {
                    $result = DB::table(config('wk-core.table.user'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                }
            }
        });
    }
}
