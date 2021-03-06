<?php

namespace BotMan\BotMan\tests\Storages;

use BotMan\BotMan\Storages\Drivers\FileStorage;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{
    /** @var FileStorage */
    protected $storage;

    protected function setUp(): void
    {
        $this->storage = new FileStorage(__DIR__.'/../Fixtures/storage');
        parent::setUp();
    }

    protected function tearDown(): void
    {
        foreach (glob(__DIR__.'/../Fixtures/storage/*.json') as $file) {
            unlink($file);
        }
    }

    /** @test */
    public function save_and_get()
    {
        $this->storage->save(['json' => 'encoded'], 'my_key');
        $data = $this->storage->get('my_key');
        $this->assertSame($data->toArray(), ['json' => 'encoded']);
    }

    /** @test */
    public function save_appends_data()
    {
        $this->storage->save(['key_one' => 'value_one'], 'my_key');
        $this->storage->save(['key_two' => 'value_two'], 'my_key');

        $data = $this->storage->get('my_key');

        $this->assertSame($data->toArray(), [
            'key_one' => 'value_one',
            'key_two' => 'value_two',
        ]);
    }

    /** @test */
    public function delete()
    {
        $this->storage->save(['json' => 'encoded'], 'my_key');
        $this->assertSame('encoded', $this->storage->get('my_key')->get('json'));

        $this->storage->delete('my_key');
        $this->assertNull($this->storage->get('my_key')->get('json'));
    }

    /** @test */
    public function all()
    {
        $this->storage->save(['json' => 'in_key_1'], 'my_key');
        $this->storage->save(['json' => 'in_key_2'], 'my_other_key');

        $all = $this->storage->all();

        $this->assertCount(2, $all);
        $this->assertSame('in_key_1', $all[0]->get('json'));
        $this->assertSame('in_key_2', $all[1]->get('json'));
    }
}