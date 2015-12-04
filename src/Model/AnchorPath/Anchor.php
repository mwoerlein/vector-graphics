<?php
namespace VectorGraphics\Model\AnchorPath;

class Anchor
{
    /** @var float */
    public $x;
    
    /** @var float */
    public $y;
    
    /** @var float */
    public $tangentX;
    
    /** @var float */
    public $tangentY;
    
    /**
     * @param float $x
     * @param float $y
     * @param float $tx
     * @param float $ty
     */
    public function __construct($x, $y, $tx, $ty)
    {
        $this->x = $x;
        $this->y = $y;
        $this->tangentX = $tx;
        $this->tangentY = $ty;
    }
    
    /**
     * @param Anchor $anchor
     *
     * @return float
     */
    public function getDistanceTo(Anchor $anchor)
    {
        return sqrt(
            ($this->x - $anchor->x) * ($this->x - $anchor->x)
            + ($this->y - $anchor->y) * ($this->y - $anchor->y)
        );
    }
    
    /**
     * @return float
     */
    public function getRotation()
    {
        return -180. * atan2($this->tangentY, $this->tangentX) / pi();
    }
}
