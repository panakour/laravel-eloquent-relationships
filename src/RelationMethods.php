<?php

namespace EloquentRelationships;

enum RelationMethods: string
{
    case BelongsTo = 'belongsTo';
    case BelongsToMany = 'belongsToMany';
    case HasOne = 'hasOne';
    case HasMany = 'hasMany';
    case HasManyThrough = 'hasManyThrough';
    case HasOneThrough = 'hasOneThrough';
    case MorphOne = 'morphOne';
    case MorphTo = 'morphTo';
    case MorphMany = 'morphMany';
    case MorphToMany = 'morphToMany';
    case MorphedByMany = 'morphedByMany';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
