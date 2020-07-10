<?php

namespace Biigle\Tests\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Tests\LabelTest;
use Biigle\Tests\VideoTest;
use Biigle\Tests\VideoAnnotationTest;
use Biigle\Tests\VideoAnnotationLabelTest;

class VideoAnnotationLabelControllerTest extends ApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->video = VideoTest::create(['project_id' => $this->project()->id]);
    }

    public function testStore()
    {
        $annotation = VideoAnnotationTest::create(['video_id' => $this->video->id]);
        $id = $annotation->id;

        $this->doTestApiRoute('POST', "api/v1/video-annotations/{$id}/labels");

        $this->beUser();
        $this->postJson("api/v1/video-annotations/{$id}/labels", [
                'label_id' => $this->labelRoot()->id,
            ])
            ->assertStatus(403);

        $this->beEditor();
        $this->postJson("api/v1/video-annotations/{$id}/labels", [
                'label_id' => LabelTest::create()->id,
            ])
            // Label ID belong to the projects of the video.
            ->assertStatus(403);

        $this->postJson("api/v1/video-annotations/{$id}/labels", [
                'label_id' => $this->labelRoot()->id,
            ])
            ->assertSuccessful()
            ->assertJsonFragment(['label_id' => $this->labelRoot()->id]);

        $label = $annotation->labels()->first();
        $this->assertNotNull($label);
        $this->assertEquals($this->labelRoot()->id, $label->label_id);
        $this->assertEquals($this->editor()->id, $label->user_id);

        $this->postJson("api/v1/video-annotations/{$id}/labels", [
                'label_id' => $this->labelRoot()->id,
            ])
            // Label is already attached.
            ->assertStatus(422);
    }

    public function testDestroy()
    {
        $annotation = VideoAnnotationTest::create(['video_id' => $this->video->id]);
        $annotationLabel1 = VideoAnnotationLabelTest::create([
            'video_annotation_id' => $annotation->id,
            'user_id' => $this->expert()->id,
        ]);
        $annotationLabel2 = VideoAnnotationLabelTest::create([
            'video_annotation_id' => $annotation->id,
            'user_id' => $this->editor()->id,
        ]);
        $annotationLabel3 = VideoAnnotationLabelTest::create([
            'video_annotation_id' => $annotation->id,
            'user_id' => $this->editor()->id,
        ]);

        $this->doTestApiRoute('DELETE', "api/v1/video-annotation-labels/{$annotationLabel1->id}");

        $this->beUser();
        $this->deleteJson("api/v1/video-annotation-labels/{$annotationLabel1->id}")
            ->assertStatus(403);

        $this->beEditor();
        $this->deleteJson("api/v1/video-annotation-labels/{$annotationLabel1->id}")
            // Cannot detach label of other user.
            ->assertStatus(403);

        $this->deleteJson("api/v1/video-annotation-labels/{$annotationLabel3->id}")
            ->assertStatus(200);
        $this->assertNull($annotationLabel3->fresh());

        $this->beExpert();
        $this->deleteJson("api/v1/video-annotation-labels/{$annotationLabel2->id}")
            ->assertStatus(200);
        $this->assertNull($annotationLabel2->fresh());

        $this->deleteJson("api/v1/video-annotation-labels/{$annotationLabel1->id}")
            // Cannot detach the last label.
            ->assertStatus(422);
    }
}