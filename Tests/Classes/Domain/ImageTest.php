<?php
class Tx_Responsivetypo3_Domain_ImageTest
        extends Tx_Phpunit_TestCase
{
    /**
     * @var Tx_Responsivetypo3_Domain_Image
     */
    protected $fixture = NULL;

    public function setUp() {
        parent::setUp();

        $this->fixture = t3lib_div::makeInstance('Tx_Responsivetypo3_Domain_Image');
    }

    /**
     * @test
     */
    public function heightIsSettable() {
        $this->fixture->setTargetHeight(30);

        $this->assertEquals(30,$this->fixture->getTargetHeight());
    }

    /**
     * @test
     */
    public function heightIsCastedInteger() {
        $this->fixture->setTargetHeight('30px');

        $this->assertEquals(30,$this->fixture->getTargetHeight());
    }

    /**
     * @test
     */
    public function widthIsSettable() {
        $this->fixture = t3lib_div::makeInstance('Tx_Responsivetypo3_Domain_Image');

        $this->fixture->setTargetWidth(30);

        $this->assertEquals(30,$this->fixture->getTargetWidth());
    }

    /**
     * @test
     */
    public function widthIsCastedInteger() {
        $this->fixture->setTargetWidth('30px');

        $this->assertEquals(30,$this->fixture->getTargetWidth());
    }

    /**
     * @test
     */
    public function imagepathIsSettable() {
        $imgPath = '/uploads/media/test.jpg';
        $this->fixture->setSourcePath($imgPath);

        $this->assertEquals($imgPath, $this->fixture->getSourcePath());
    }

    /**
     * @test
     * @expectedException Tx_Responsivetypo3_Exception_Filetype
     */
    public function fileextensionMustBeImage() {
        $this->fixture->setSourcePath('testfilename.exe');
    }

    /**
     * @test
     * @expectedException Tx_Responsivetypo3_Exception_Path
     */
    public function filepathMustBeValid() {
        $this->fixture->setSourcePath('../../xxx.jpg');
    }

    /**
     * @test
     */
    public function resizemethodIstSettable() {
        $this->fixture->setResizemethod('m');
        $this->assertEquals('m', $this->fixture->getResizemethod());
    }

    /**
     * @test
     * @expectedException Tx_Responsivetypo3_Exception_Resizemethod
     */
    public function resizemethodMustBeValid() {
        $this->fixture->setResizemethod('x');
    }

    /**
     * @test
     */
    public function gifbuilderIsSettable() {
        $gifbuilder = $this->getMock('tslib_gifBuilder');

        $this->fixture->setGifbuilder($gifbuilder);

        $this->assertEquals($gifbuilder, $this->fixture->getGifbuilder());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function gifbuilderMustBeValidObject() {
        $this->fixture->setGifbuilder('test');
    }

    /**
     * @test
     */
    public function getimageReturnsImagepath() {
        $path = 'fileadmin/content/test.jpg';
        $width = 30;
        $height = 40;
        $resizeMethod = 'c';
        $returnValue = '/typo3temp/pics/resized.gif';

        $gifbuilder = $this->getMock('tslib_gifBuilder');
        $gifbuilder->expects($this->once())
                   ->method('imageMagickConvert')
                   ->with(
                        $this->equalTo($path),
                        $this->anything(),
                        $this->equalTo($width . $resizeMethod),
                        $this->equalTo($height . $resizeMethod)
                    )
                    ->will(
                        $this->returnValue(
                            array(
                                3=>$returnValue
                            )
                        )
                    );

        $this->fixture = t3lib_div::makeInstance('Tx_Responsivetypo3_Domain_Image');

        $this->fixture->disableSizeSanitization();

        $this->fixture->setSourcePath('/' . $path);
        $this->fixture->setTargetWidth($width);
        $this->fixture->setTargetHeight($height);
        $this->fixture->setResizemethod($resizeMethod);
        $this->fixture->setGifbuilder($gifbuilder);

        $this->assertEquals($returnValue, $this->fixture->getImage());
    }
}