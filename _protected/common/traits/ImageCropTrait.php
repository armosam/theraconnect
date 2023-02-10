<?php

namespace common\traits;

use Yii;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\AbstractImage;
use Imagine\Image\ImageInterface;
use yii\base\InvalidConfigException;

/**
 * Trait ImageCropTrait
 * @package common\helpers
 */
trait ImageCropTrait
{
    private $x;
    private $y;
    private $width;
    private $height;
    private $scale;
    private $angle;

    /** @var AbstractImage */
    private $sourceImage;

    /**
     * Gets arguments and initialize class
     * @param ImageInterface $image
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @param int $scale
     * @param int $angle
     * @throws InvalidConfigException
     */
    public function initImageCrop($image, $width, $height, $x=0, $y=0, $scale=1, $angle=0){
        if(!$image instanceof ImageInterface){
            throw new InvalidConfigException('Expected argument type is ImagineInterface.');
        }
        $this->sourceImage = $image;
        $this->width = empty($width) ? 400 : $width;
        $this->height = empty($height) ? 300 : $height;
        $this->x = $x;
        $this->y = $y;
        $this->scale = $scale;
        $this->angle = $angle;
    }

    /**
     * Gets configuration from given argument and initialize class
     * @param ImageInterface $image
     * @param string $config_name
     * @return ImageCropTrait
     * @throws InvalidConfigException
     */
    public function initImageCropFromNamedConfig($image, $config_name){
        if(!$image instanceof ImageInterface){
            throw new InvalidConfigException('Expected argument type is ImagineInterface.');
        }

        if(empty(Yii::$app->params[$config_name])){
            throw new InvalidConfigException($config_name." Configuration parameter not fount in the configuration");
        }
        $config = Yii::$app->params[$config_name];
        $this->sourceImage = $image;
        $this->x = empty($config['x']) ? 0 : $config['x'];
        $this->y = empty($config['y']) ? 0 : $config['y'];
        $this->width = empty($config['width']) ? 400 : $config['width'];
        $this->height = empty($config['height']) ? 300 : $config['height'];
        $this->scale = empty($config['scale']) ? 1 : $config['scale'];
        $this->angle = empty($config['angle']) ? 0 : $config['angle'];
        return $config;
    }

    /**
     * Auto Crops Image based on given configuration
     */
    public function cropImage()
    {
        $finalRatio = $this->width / $this->height;
        $finalBox = new Box($this->width, $this->height);

        $ratio = $this->sourceImage->getSize()->getWidth() / $this->sourceImage->getSize()->getHeight();
        $box = ($ratio > 1) ? new Box(($this->height*$ratio), $this->height) : new Box($this->height, ($this->height/$ratio));

        $widenBox = $box->widen($this->width);
        $centerWidenBoxX = (($widenBox->getWidth()/2) - ($this->width/2))<0 ? 0 : (($widenBox->getWidth()/2) - ($this->width/2));
        $centerWidenBoxY = (($widenBox->getHeight()/2) - ($this->height/2))<0 ? 0 : (($widenBox->getHeight()/2) - ($this->height/2));
        $cropWidenBoxStart = new Point($centerWidenBoxX, $centerWidenBoxY);

        $heightenBox = $box->heighten($this->height);
        $centerHeightenBoxX = (($heightenBox->getWidth()/2) - ($this->width/2))<0 ? 0 : (($heightenBox->getWidth()/2) - ($this->width/2));
        $centerHeightenBoxY = (($heightenBox->getHeight()/2) - ($this->height/2))<0 ? 0 : (($heightenBox->getHeight()/2) - ($this->height/2));
        $cropHeightenBoxStart = new Point($centerHeightenBoxX, $centerHeightenBoxY);

        if($finalRatio > 1){ // when default image is landscape
            if($ratio > 1){ // when uploaded image is landscape
                if($ratio > $finalRatio) {
                    $this->sourceImage
                        ->resize($heightenBox)
                        ->crop($cropHeightenBoxStart, $finalBox);
                } elseif($ratio < $finalRatio) {
                    $this->sourceImage
                        ->resize($widenBox)
                        ->crop($cropWidenBoxStart, $finalBox);
                } elseif ($ratio == $finalRatio) {
                    $this->sourceImage
                        ->resize($finalBox);
                }
            }elseif($ratio <= 1){ // when uploaded image is portrait or square
                $this->sourceImage
                    ->resize($widenBox)
                    ->crop($cropWidenBoxStart, $finalBox);
            }
        } else { // when default image is portrait
            if($ratio >= 1){ // when uploaded image is landscape or square
                $this->sourceImage
                    ->resize($heightenBox)
                    ->crop($cropHeightenBoxStart, $finalBox);
            }elseif($ratio < 1){ // when uploaded image is portrait
                if($ratio > $finalRatio) {
                    $this->sourceImage
                        ->resize($heightenBox)
                        ->crop($cropHeightenBoxStart, $finalBox);
                } elseif($ratio < $finalRatio) {
                    $this->sourceImage
                        ->resize($widenBox)
                        ->crop($cropWidenBoxStart, $finalBox);
                } elseif ($ratio == $finalRatio) {
                    $this->sourceImage
                        ->resize($finalBox);
                }
            }
        }
    }
} 