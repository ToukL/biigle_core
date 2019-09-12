<?php

namespace Biigle\Tests\Modules\Sync\Http\Controllers\Api\Import;

use Mockery;
use Exception;
use ApiTestCase;
use Biigle\Role;
use Biigle\Tests\LabelTreeTest;
use Illuminate\Http\UploadedFile;
use Biigle\Modules\Sync\Support\Import\ArchiveManager;
use Biigle\Modules\Sync\Support\Import\PublicLabelTreeImport;
use Biigle\Modules\Sync\Support\Export\PublicLabelTreeExport;

class PublicLabelTreeImportControllerTest extends ApiTestCase
{
    public function testStoreValidation()
    {
        $mock = Mockery::mock(ArchiveManager::class);
        $mock->shouldReceive('store')->once()->andReturn('123abc');
        $mock->shouldReceive('delete')->once()->with('123abc');
        $this->app->bind(ArchiveManager::class, function () use ($mock) {
            return $mock;
        });

        $labelTree = LabelTreeTest::create();
        $path = (new PublicLabelTreeExport([$labelTree->id]))->getArchive();

        $wrongFile = new UploadedFile($path, 'file.txt', 'text/plain', null, true);

        $this->doTestApiRoute('POST', '/api/v1/label-trees/import');

        $this->beGlobalGuest();
        $this->postJson('/api/v1/label-trees/import')->assertStatus(403);

        $this->beUser();
        $this->postJson('/api/v1/label-trees/import')->assertStatus(422);

        $this->postJson('/api/v1/label-trees/import', ['archive' => $wrongFile])
            ->assertStatus(422);
    }

    public function testStore()
    {
        $newTree = LabelTreeTest::create();
        $importMock = Mockery::mock(PublicLabelTreeImport::class);
        $importMock->shouldReceive('perform')->once()->andReturn($newTree);
        $importMock->shouldReceive('treeExists')->once()->andReturn(false);
        $managerMock = Mockery::mock(ArchiveManager::class);
        $managerMock->shouldReceive('store')->once()->andReturn('123abc');
        $managerMock->shouldReceive('get')
            ->once()
            ->with('123abc')
            ->andReturn($importMock);
        $managerMock->shouldReceive('delete')->once()->with('123abc');
        $this->app->bind(ArchiveManager::class, function () use ($managerMock) {
            return $managerMock;
        });

        $labelTree = LabelTreeTest::create();
        $path = (new PublicLabelTreeExport([$labelTree->id]))->getArchive();

        $file = new UploadedFile($path, 'label-tree.zip', 'application/zip', null, true);

        $this->beUser();
        $this->postJson('/api/v1/label-trees/import', ['archive' => $file])
            ->assertSuccessful();

        $hasMember = $newTree->members()
            ->where('id', $this->user()->id)
            ->where('label_tree_user.role_id', Role::adminId())
            ->exists();
        $this->assertTrue($hasMember);
    }

    public function testStoreValidationException()
    {
        $mock = Mockery::mock(ArchiveManager::class);
        $mock->shouldReceive('store')->once()->andThrow(Exception::class);
        $this->app->bind(ArchiveManager::class, function () use ($mock) {
            return $mock;
        });

        $labelTree = LabelTreeTest::create();
        $path = (new PublicLabelTreeExport([$labelTree->id]))->getArchive();

        $file = new UploadedFile($path, 'label-tree.zip', filesize($path), 'application/zip', null, true);

        $this->beUser();
        $this->postJson('/api/v1/label-trees/import', ['archive' => $file])
            ->assertStatus(422);
        }
}
