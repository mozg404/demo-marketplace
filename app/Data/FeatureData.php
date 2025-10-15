<?php

namespace App\Data;

use App\Enum\FeatureType;
use App\Models\Feature;
use Spatie\LaravelData\Data;

class FeatureData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public array|null $options = null,
        public string|null $value = null,
        public string|null $value_key = null,
    ) {
    }

    public static function fromModel(Feature $feature): self
    {
        $value = null;

        if (isset($feature?->pivot->value)) {
            $value = $feature?->pivot->value;

            if ($feature->type === FeatureType::SELECT) {
                $value = $feature->options[$feature?->pivot->value];
            }
        }

        return new self(
            id: $feature->id,
            name: $feature->name,
            type: $feature->type->value,
            options: $feature->options,
            value: $value,
            value_key: $feature?->pivot?->value,
        );
    }
}
