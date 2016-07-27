<?php

use Dias\User;
use Dias\Role;

class UserTest extends ModelTestCase
{
    /**
     * The model class this class will test.
     */
    protected static $modelClass = Dias\User::class;

    public function testAttributes()
    {
        $this->assertNotNull($this->model->firstname);
        $this->assertNotNull($this->model->lastname);
        $this->assertNotNull($this->model->password);
        $this->assertNotNull($this->model->email);
        $this->assertNotNull($this->model->role_id);
        $this->assertNotNull($this->model->created_at);
        $this->assertNotNull($this->model->updated_at);
    }

    public function testCastsLoginAt()
    {
        $this->be($this->model);
        // make sure the login_at attribute is populated
        $this->get('/');
        $this->assertTrue($this->model->login_at instanceof \Carbon\Carbon);
    }

    public function testFirstnameRequired()
    {
        $this->model->firstname = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testLastnameRequired()
    {
        $this->model->lastname = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testPasswordRequired()
    {
        $this->model->password = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testEmailRequired()
    {
        $this->model->email = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testEmailUnique()
    {
        self::create(['email' => 'test@test.com']);
        $this->setExpectedException('Illuminate\Database\QueryException');
        self::create(['email' => 'test@test.com']);
    }

    public function testProjects()
    {
        $project = ProjectTest::create();
        $role = RoleTest::create();
        $project->addUserId($this->model->id, $role->id);

        $this->assertEquals($this->model->projects()->first()->id, $project->id);
    }

    public function testLabelTrees()
    {
        $this->assertFalse($this->model->labelTrees()->exists());
        LabelTreeTest::create()->addMember($this->model, Role::$editor);
        $this->assertTrue($this->model->labelTrees()->exists());
    }

    public function testRole()
    {
        $this->assertEquals(Role::$editor->id, $this->model->role->id);
    }

    public function testIsAdmin()
    {
        $this->assertFalse($this->model->isAdmin);
        $this->model->role()->associate(Role::$admin);
        $this->assertTrue($this->model->isAdmin);
    }

    public function testHiddenAttributes()
    {
        // API tokens mustn't show up in the JSON
        ApiTokenTest::create(['owner_id' => $this->model->id]);
        $jsonUser = json_decode((string) $this->model->fresh());
        $this->assertObjectNotHasAttribute('password', $jsonUser);
        $this->assertObjectNotHasAttribute('remember_token', $jsonUser);
        $this->assertObjectNotHasAttribute('api_tokens', $jsonUser);
    }

    public function testApiTokens()
    {
        $this->assertEmpty($this->model->apiTokens()->get());
        ApiTokenTest::create(['owner_id' => $this->model->id]);
        $this->assertNotEmpty($this->model->apiTokens()->get());
    }

    public function testCheckCanBeDeletedProjects()
    {
        $project = ProjectTest::create();
        $project->addUserId($this->model->id, Role::$guest->id);

        $this->model->checkCanBeDeleted();
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\HttpException');
        $project->creator->checkCanBeDeleted();
    }

    public function testCheckCanBeDeletedLabelTrees()
    {
        $tree = LabelTreeTest::create();
        $editor = self::create();
        $tree->addMember($editor, Role::$editor);
        $tree->addMember($this->model, Role::$admin);

        $editor->checkCanBeDeleted();
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\HttpException');
        $this->model->checkCanBeDeleted();
    }
}