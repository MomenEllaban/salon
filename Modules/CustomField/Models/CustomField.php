<?php

namespace Modules\CustomField\Models;

use App\Models\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomField extends BaseModel
{
    use HasFactory;

    protected $fillable = [];

    protected $table = 'custom_fields';

    protected $casts = [

        'required' => 'boolean',
    ];

    public function custom_fields_group()
    {
        return $this->hasMany(CustomFieldGroup::class, 'id', 'custom_field_group_id');
    }

    public static function generateUniqueSlug($label, $moduleId)
    {
        $slug = str_slug($label);
        $count = CustomField::where('name', $slug)->where('custom_field_group_id', $moduleId)->count();

        if ($count > 0) {
            $i = 1;

            while (CustomField::where('name', $slug.'-'.$i)->where('custom_field_group_id', $moduleId)->count() > 0) {
                $i++;
            }

            $slug .= '-'.$i;
        }

        return $slug;
    }

    public static function customFieldData($datatables, $model, $relation = null)
    {
        $customFields = CustomField::exportCustomFields($model);
        $customFieldNames = [];
        $customFieldsId = $customFields->pluck('id');
        $fieldData = DB::table('custom_fields_data')->where('model', $model)->whereIn('custom_field_id', $customFieldsId)->select('id', 'custom_field_id', 'model_id', 'value')->get();

        foreach ($customFields as $customField) {
            $datatables->addColumn($customField->name, function ($row) use ($fieldData, $customField, $relation) {
                $finalData = $fieldData->filter(function ($value) use ($customField, $row, $relation) {
                    return ($value->custom_field_id == $customField->id) && ($value->model_id == ($relation ? $row->{$relation}->id : $row->id));
                })->first();

                if ($customField->type == 'select') {
                    $data = $customField->values;
                    $data = json_decode($data); // string to array

                    return $finalData ? (($finalData->value >= 0 && $finalData->value != null) ? $data[$finalData->value] : '--') : '--';
                }

                if ($customField->type == 'file') {
                    return $finalData ? '<a href="'.asset_url_local_s3('custom_fields/'.$finalData->value).'" target="__blank" class="text-dark-grey">'.__('app.storageSetting.viewFile').'</a>' : '--';
                }

                return $finalData ? $finalData->value : '--';
            });

            // This will use for datatable raw column
            if ($customField->type == 'file') {
                $customFieldNames[] = $customField->name;
            }
        }

        return $customFieldNames;
    }

    public static function exportCustomFields($model)
    {
        $customFieldsGroupsId = CustomFieldGroup::where('model', $model::CUSTOM_FIELD_MODEL)->select('id')->first();

        $customFields = collect();
        if ($customFieldsGroupsId) {
            $customFields = CustomField::where('custom_field_group_id', $customFieldsGroupsId->id)->where(function ($q) {
                return $q->where('is_export', 1)->orWhere('is_view', 1);
            })->get();
        }

        return $customFields;
    }

    protected static function newFactory()
    {
        return \Modules\CustomField\Database\factories\CustomFieldFactory::new();
    }
}
