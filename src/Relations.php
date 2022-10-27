<?php

namespace EloquentRelationships;

use ReflectionMethod;
use SplFileObject;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Relations
{
    protected Collection $relations;

    public function __construct($model)
    {
        $this->relations = $this->initRelations($model);
    }


    public function all(): Collection
    {
        return $this->relations;
    }

    public function getByMethod($method = "belongsToMany"): Collection
    {
        return $this->relations->filter(fn($item) => $item["type"] === $method);
    }

    protected function initRelations($model)
    {
        return collect(get_class_methods($model))
            ->map(fn($method) => new ReflectionMethod($model, $method))
            ->reject(
                fn(ReflectionMethod $method) => $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() !== get_class($model)
            )
            ->filter(function (ReflectionMethod $method) {
                $file = new SplFileObject($method->getFileName());
                $file->seek($method->getStartLine() - 1);
                $code = '';
                while ($file->key() < $method->getEndLine()) {
                    $code .= $file->current();
                    $file->next();
                }

                return collect(RelationMethods::values())
                    ->contains(fn($relationMethod) => str_contains($code, '$this->'.$relationMethod.'('));
            })
            ->map(function (ReflectionMethod $method) use ($model) {
                $relation = $method->invoke($model);

                if (!$relation instanceof Relation) {
                    return null;
                }

                return [
                    'name' => $method->getName(),
                    'type' => lcfirst(Str::afterLast(get_class($relation), '\\')),
                    'related' => get_class($relation->getRelated()),
                ];
            })
            ->filter()
            ->values();
    }

}