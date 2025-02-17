<?php

namespace Modules\CustomField\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Subscriptions\Models\Plan;

class CustomFieldGroup extends BaseModel
{
    const ALL_FIELDS = [
        ['name' => 'Plan', 'model' => Plan::CUSTOM_FIELD_MODEL],
    ];

    public $timestamps = false;

    protected $table = 'custom_field_groups';

    public function customField(): HasMany
    {
        return $this->HasMany(CustomField::class);
    }

    public static function customFieldsDataMerge($model)
    {
        $customFields = CustomField::exportCustomFields($model);

        $customFieldsDataMerge = [];

        foreach ($customFields as $customField) {
            $customFieldsData = [
                $customField->name => [
                    'data' => $customField->name,
                    'name' => $customField->name,
                    'title' => $customField->label,
                    'visible' => $customField['visible'],
                ],
            ];

            $customFieldsDataMerge = array_merge($customFieldsDataMerge, $customFieldsData);
        }

        return $customFieldsDataMerge;
    }

    public static function columnJsonValues($model)
    {
        return json_encode(array_values(self::customFieldsDataMerge($model)));
    }

    /**
     * Get the custom field group's name.
     */
    protected function fields(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->customField->map(function ($item) {
                    if (in_array($item->type, ['select', 'radio', 'checkbox'])) {
                        $item->values = json_decode($item->values);

                        return $item;
                    }

                    return $item;
                });
            },
        );
    }
}
