<?php

namespace EloquentRelationships\Tests;

use EloquentRelationships\RelationMethods;
use EloquentRelationships\Relations;
use EloquentRelationships\Tests\Fixtures\Models\Post;
use EloquentRelationships\Tests\Fixtures\Models\Tag;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{

    protected function setUp(): void
    {
        $db = new DB;
        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();
    }


    public function testRelations()
    {
        $relations = new Relations(new Post());
        $this->assertCount(2, $relations->all());
        $this->assertCount(1, $relations->getByMethod(RelationMethods::BelongsToMany->value));
        $this->assertCount(0, $relations->getByMethod(RelationMethods::MorphedByMany->value));

        $relations = new Relations(new Tag());
        $this->assertCount(1, $relations->all());
        $this->assertCount(1, $relations->getByMethod(RelationMethods::BelongsToMany->value));
    }
}