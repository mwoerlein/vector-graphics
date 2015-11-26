<?php
namespace VectorGraphics\Model\Style;

abstract class AbstractColoredStyle implements StyleInterface
{
    /** @var HtmlColor */
    private $color;
    
    /** @var float */
    private $opacity = 1;
    
    /**
     * @return HtmlColor|null
     */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
     * @param HtmlColor|string|null $color
     * @param float $opacity
     *
     * @return $this
     * @throws \Exception
     */
    public function setColor($color, $opacity = 1.)
    {
        if (null === $color) {
            $this->color = null;
            $this->opacity = 0.;
        } elseif ($color instanceof HtmlColor) {
            $this->color = $color;
            $this->opacity = $opacity;
        } elseif (is_string($color)) {
            if (preg_match(HtmlColor::PATTERN_HEX, $color, $matches)) {
                $this->color = HtmlColor::byHex($color);
                $this->opacity = $opacity;
            } elseif (preg_match(HtmlColor::PATTERN_RGB, $color, $matches)) {
                $this->color = HtmlColor::rgb((int) $matches[1], (int) $matches[2], (int) $matches[3]);
                $this->opacity = $opacity;
            } elseif (preg_match(HtmlColor::PATTERN_RGBA, $color, $matches)) {
                $this->color = HtmlColor::rgb((int) $matches[1], (int) $matches[2], (int) $matches[3]);
                $this->opacity = (float) $matches[4];
            } else {
                $this->color = HtmlColor::byName($color);
                $this->opacity = $opacity;
            }
        } else {
            // TODO: cleanup exceptions
            throw new \Exception('Invalid Color: ' . get_class($color));
        }
        return $this;
    }
    
    /**
     * @return float
     */
    public function getOpacity()
    {
        return $this->opacity;
    }
    
    /**
     * @param float $opacity
     *
     * @return $this
     */
    public function setOpacity($opacity)
    {
        $this->opacity = $opacity;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->getOpacity() > 0;
    }
}