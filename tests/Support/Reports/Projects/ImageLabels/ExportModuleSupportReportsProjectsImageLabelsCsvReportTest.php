<?php

namespace Biigle\Tests\Modules\Reports\Support\Reports\Projects\ImageLabels;

use TestCase;
use Biigle\Modules\Reports\Support\Reports\Projects\ImageLabels\CsvReportGenerator;

class CsvReportGeneratorTest extends TestCase
{
    public function testProperties()
    {
        $generator = new CsvReportGenerator;
        $this->assertEquals('CSV image label report', $generator->getName());
        $this->assertEquals('csv_image_label_report', $generator->getFilename());
    }
}
