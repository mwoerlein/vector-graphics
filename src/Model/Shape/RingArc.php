<?php
namespace VectorGraphics\Model\Shape;

use InvalidArgumentException;
use VectorGraphics\Model\Anchor;
use VectorGraphics\Model\Path;
use VectorGraphics\Utils\ArcUtils;

class RingArc extends AbstractShape
{
    const ALPHA_START = 'start';
    const ALPHA_CENTRAL = 'central';
    const ALPHA_END = 'end';
    
    const RADIUS_INNER = 'inner';
    const RADIUS_MIDDLE = 'middle';
    const RADIUS_OUTER = 'outer';
    
    /** @var float */
    private $x;

    /** @var float */
    private $y;

    /** @var float */
    private $innerRadius;

    /** @var float */
    private $outerRadius;
    
    /** @var float */
    private $alpha;
    
    /** @var float */
    private $angle;
    
    /**
     * @param float $x
     * @param float $y
     * @param float $innerRadius
     * @param float $outerRadius
     * @param float $alpha
     * @param float $angle
     */
    public function __construct($x, $y, $innerRadius, $outerRadius, $alpha = 0., $angle = 360.)
    {
        if ($innerRadius <= 0.) {
            throw new InvalidArgumentException(__CLASS__ . 'has to have a positive $innerRadius');
        }
        if ($outerRadius < $innerRadius) {
            throw new InvalidArgumentException(
                '$outerRadius has to be greater or equal to $innerRadius in ' . __CLASS__
            );
        }
        parent::__construct();
        $this->x = (float) $x;
        $this->y = (float) $y;
        $this->innerRadius = (float) $innerRadius;
        $this->outerRadius = (float) $outerRadius;
        if ($angle < 0.) {
            $alpha += $angle;
            $angle = - $angle;
        }
        $this->alpha = $this->normalizeDegree((float) $alpha);
        $this->angle = $angle > 360. ? 360. : (float) $angle;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return float
     */
    public function getInnerRadius()
    {
        return $this->innerRadius;
    }

    /**
     * @return float
     */
    public function getOuterRadius()
    {
        return $this->outerRadius;
    }
    
    /**
     * @return float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }
    
    /**
     * @return float
     */
    public function getAngle()
    {
        return $this->angle;
    }
    
    /**
     * @return Path
     */
    public function getPath()
    {
        if ($this->angle === 360. && $this->alpha === 0.) {
            return $this->getFullRingPath();
        }
        return $this->getPartialRingPath();
    }
    
    /**
     * @param string $relAngle
     * @param string $relRadius
     *
     * @return Anchor
     */
    public function getAnchor($relAngle = self::ALPHA_CENTRAL, $relRadius = self::RADIUS_MIDDLE)
    {
        list ($x, $y) = ArcUtils::getPolarPoint(
            $this->getRelRadius($relRadius),
            ArcUtils::toRadian($this->normalizeDegree($this->getRelAngle($relAngle)))
        );
        return new Anchor($x + $this->getX(), $y + $this->getY(), $y, -$x);
    }
    
    private function getRelRadius($pos) {
        switch ($pos) {
            case self::RADIUS_INNER:
                return $this->innerRadius;
            case self::RADIUS_OUTER:
                return $this->outerRadius;
        }
        // default: RADIUS_MIDDLE
        return ($this->innerRadius + $this->outerRadius) / 2.;
    }
    
    private function getRelAngle($pos) {
        switch ($pos) {
            case self::ALPHA_START:
                return $this->alpha;
            case self::ALPHA_END:
                return $this->alpha + $this->angle;
        }
        // default: ALPHA_CENTRAL
        return $this->alpha + $this->angle/2.;
    }
    
    /**
     * @return Path
     */
    private function getFullRingPath()
    {
        $x = $this->getX();
        $y = $this->getY();
        $ri = $this->getInnerRadius();
        $si = $ri*4.*(sqrt(2.)-1.)/3.;
        $ro = $this->getOuterRadius();
        $so = $ro*4.*(sqrt(2.)-1.)/3.;
        return (new Path($x, $y+$ro))
            ->curveTo($x+$so, $y+$ro, $x+$ro, $y+$so, $x+$ro, $y)
            ->curveTo($x+$ro, $y-$so, $x+$so, $y-$ro, $x, $y-$ro)
            ->curveTo($x-$so, $y-$ro, $x-$ro, $y-$so, $x-$ro, $y)
            ->curveTo($x-$ro, $y+$so, $x-$so, $y+$ro, $x, $y+$ro)
            ->close()

            ->moveTo($x, $y+$ri)
            ->curveTo($x-$si, $y+$ri, $x-$ri, $y+$si, $x-$ri, $y)
            ->curveTo($x-$ri, $y-$si, $x-$si, $y-$ri, $x, $y-$ri)
            ->curveTo($x+$si, $y-$ri, $x+$ri, $y-$si, $x+$ri, $y)
            ->curveTo($x+$ri, $y+$si, $x+$si, $y+$ri, $x, $y+$ri)
            ->close();
    }
    
    
    /**
     * @return Path
     */
    private function getPartialRingPath()
    {
        $outer = $this->getOuterRadius();
        $inner = $this->getInnerRadius();
        $cx = $this->getX();
        $cy = $this->getY();
        if ($this->angle === 0.) {
            $radian = ArcUtils::toRadian($this->getAlpha());
            list($outerX, $outerY) = ArcUtils::getPolarPoint($outer, $radian);
            list($innerX, $innerY) = ArcUtils::getPolarPoint($inner, $radian);
            return (new Path($outerX + $cx, $outerY + $cy))
                ->lineTo($innerX + $cx, $innerY + $cy)
                ->close();
        }
        
        $radians = ArcUtils::getArcRadians($this->getAlpha(), $this->getAngle());
        $scale = ArcUtils::getScale($radians);
        $pos = 0;
        
        // outer arc
        list($curX, $curY) = ArcUtils::getPolarPoint($outer, $radians[$pos++]);
        $path = new Path($curX + $cx, $curY + $cy);
        while ($pos < count($radians)) {
            list ($nextX, $nextY) = ArcUtils::getPolarPoint($outer, $radians[$pos++]);
            list ($c1X, $c1Y) = ArcUtils::getBezierControl($curX, $curY, -$scale);
            list ($c2X, $c2Y) = ArcUtils::getBezierControl($nextX, $nextY, $scale);
            $path->curveTo($c1X + $cx, $c1Y + $cy, $c2X + $cx, $c2Y + $cy, $nextX + $cx, $nextY + $cy);
            $curX = $nextX; $curY = $nextY;
        }
        
        // inner arc
        list ($curX, $curY) = ArcUtils::getPolarPoint($inner, $radians[--$pos]);
        if ($this->angle === 360.) {
            // full ring
            $path->close()->moveTo($curX + $cx, $curY + $cy);
        } else {
            $path->lineTo($curX + $cx, $curY + $cy);
        }
        while ($pos > 0) {
            list ($nextX, $nextY) = ArcUtils::getPolarPoint($inner, $radians[--$pos]);
            list ($c1X, $c1Y) = ArcUtils::getBezierControl($curX, $curY, $scale);
            list ($c2X, $c2Y) = ArcUtils::getBezierControl($nextX, $nextY, -$scale);
            $path->curveTo($c1X + $cx, $c1Y + $cy, $c2X + $cx, $c2Y + $cy, $nextX + $cx, $nextY + $cy);
            $curX = $nextX; $curY = $nextY;
        }
        
        // close path
        $path->close();
        return $path;
    }
}
