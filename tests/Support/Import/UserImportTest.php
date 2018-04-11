<?php

namespace Biigle\Tests\Modules\Sync\Support\Import;

use DB;
use File;
use TestCase;
use Exception;
use ZipArchive;
use Ramsey\Uuid\Uuid;
use Biigle\Tests\UserTest;
use Biigle\Modules\Sync\Support\Export\UserExport;
use Biigle\Modules\Sync\Support\Import\UserImport;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserImportTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->user = UserTest::create();
        $this->user->setSettings(['ab' => 'cd']);
        $this->user2 = UserTest::create();
        $export = new UserExport([$this->user->id, $this->user2->id]);
        $path = $export->getArchive();
        $this->destination = tempnam(sys_get_temp_dir(), 'user_import_test');
        // This should be a directory, not a file.
        File::delete($this->destination);

        $zip = new ZipArchive;
        $zip->open($path);
        $zip->extractTo($this->destination);
        $zip->close();
    }

    public function tearDown()
    {
        File::deleteDirectory($this->destination);
        parent::tearDown();
    }

    public function testFilesMatch()
    {
        $import = new UserImport($this->destination);

        $this->assertTrue($import->filesMatch());
        File::delete("{$this->destination}/users.json");
        $this->assertFalse($import->filesMatch());
    }

    public function testValidateFiles()
    {
        $import = new UserImport($this->destination);
        $import->validateFiles();

        $content = json_decode(File::get("{$this->destination}/users.json"), true);
        unset($content[0]['uuid']);
        File::put("{$this->destination}/users.json", json_encode($content));

        try {
            $import->validateFiles();
            $this->assertFalse(true);
        } catch (Exception $e) {
            $this->assertContains('are missing keys: uuid', $e->getMessage());
        }
    }

    public function testGetImportUsers()
    {
        $import = new UserImport($this->destination);
        $this->assertCount(2, $import->getImportUsers());
    }

    public function testGetUserImportCandidates()
    {
        $import = new UserImport($this->destination);
        $this->assertCount(0, $import->getUserImportCandidates());
        DB::table('users')->where('id', DB::table('users')->min('id'))->delete();
        $this->assertCount(1, $import->getUserImportCandidates());
    }

    public function testPerform()
    {
        $import = new UserImport($this->destination);
        DB::table('users')->delete();
        $map = $import->perform();
        $this->assertEquals(2, DB::table('users')->count());
        $user = DB::table('users')->first();
        $this->assertEquals($user->id, $map[$this->user->id]);
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function testPerformConflicts()
    {
        $import = new UserImport($this->destination);
        DB::table('users')
            ->where('id', DB::table('users')->min('id'))
            ->update(['uuid' => Uuid::uuid4()]);
        try {
            $import->perform();
            $this->assertFalse(true);
        } catch (UnprocessableEntityHttpException $e) {
            $this->assertContains('users exist according to their email address but the UUIDs do not match', $e->getMessage());
        }
    }

    public function testPerformNone()
    {
        $import = new UserImport($this->destination);
        $map = $import->perform();
        $this->assertEquals(2, DB::table('users')->count());
        $id = $this->user->id;
        $id2 = $this->user2->id;
        $this->assertEquals([$id => $id, $id2 => $id2], $map);
    }

    public function testPerformOnly()
    {
        $import = new UserImport($this->destination);
        DB::table('users')->delete();
        $map = $import->perform([$this->user->id]);
        $this->assertEquals(1, DB::table('users')->count());
        $id = DB::table('users')->first()->id;
        $this->assertEquals([$this->user->id => $id], $map);
    }
}
