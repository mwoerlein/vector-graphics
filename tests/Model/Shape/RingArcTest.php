<?php
namespace VectorGraphics\Tests\Model\Shape;

use InvalidArgumentException;
use VectorGraphics\Model\Anchor;
use VectorGraphics\Model\Path\Close;
use VectorGraphics\Model\Path\CurveTo;
use VectorGraphics\Model\Path\LineTo;
use VectorGraphics\Model\Path\MoveTo;
use VectorGraphics\Model\Shape\RingArc;

/**
 * @covers VectorGraphics\Model\Shape\RingArc
 * @covers VectorGraphics\Utils\ArcUtils
 *
 * @covers VectorGraphics\Model\Shape\AbstractShape
 */
class RingArcTest extends AbstractShapeTest
{
    /**
     * @param float $x
     * @param float $y
     * @param float $inner
     * @param float $outer
     * @param float $alpha
     * @param float $angle
     *
     * @return RingArc
     */
    protected function createShape($x = 0., $y = 0., $inner = 1., $outer = 2., $alpha = 0., $angle = 360.)
    {
        return new RingArc($x, $y, $inner, $outer, $alpha, $angle);
    }
    
    /**
     */
    public function testGetter()
    {
        $arc = $this->createShape(1, 2, 3, 4, 5, 6);
        $this->assertEquals(1., $arc->getX());
        $this->assertEquals(2., $arc->getY());
        $this->assertEquals(3., $arc->getInnerRadius());
        $this->assertEquals(4., $arc->getOuterRadius());
        $this->assertEquals(5., $arc->getAlpha());
        $this->assertEquals(6., $arc->getAngle());
        
        $arc = $this->createShape(1, 2, 3, 4, 5, -6);
        $this->assertEquals(1., $arc->getX());
        $this->assertEquals(2., $arc->getY());
        $this->assertEquals(3., $arc->getInnerRadius());
        $this->assertEquals(4., $arc->getOuterRadius());
        $this->assertEquals(359., $arc->getAlpha());
        $this->assertEquals(6., $arc->getAngle());
    }
    
    /**
     * @return array[]
     */    
    public function getPathProvider()
    {
        $data = [];
        $data['full ring'] = [
            'shape' => $this->createShape(0, 0, 1, 2, 0, 360),
            'path' => [
                new MoveTo(0, 2),
                new CurveTo(1.1045694996615871, 2, 2, 1.1045694996615871, 2, 0),
                new CurveTo(2, -1.1045694996615871, 1.1045694996615871, -2, 0, -2),
                new CurveTo(-1.1045694996615871, -2, -2, -1.1045694996615871, -2, 0),
                new CurveTo(-2, 1.1045694996615871, -1.1045694996615871, 2, 0, 2),
                new Close(0, 2),
                new MoveTo(0, 1),
                new CurveTo(-0.55228474983079356, 1, -1, 0.55228474983079356, -1, 0),
                new CurveTo(-1, -0.55228474983079356, -0.55228474983079356, -1, 0, -1),
                new CurveTo(0.55228474983079356, -1, 1, -0.55228474983079356, 1, 0),
                new CurveTo(1, 0.55228474983079356, 0.55228474983079356, 1, 0, 1),
                new Close(0, 1),
            ],
            'anchors' => [
                'si' => new Anchor(0.,  1. ,  1. , 0.),
                'sm' => new Anchor(0.,  1.5,  1.5, 0.),
                'so' => new Anchor(0.,  2. ,  2. , 0.),
                'ci' => new Anchor(0., -1. , -1. , 0.),
                'cm' => new Anchor(0., -1.5, -1.5, 0.),
                'co' => new Anchor(0., -2. , -2. , 0.),
                'ei' => new Anchor(0.,  1. ,  1. , 0.),
                'em' => new Anchor(0.,  1.5,  1.5, 0.),
                'eo' => new Anchor(0.,  2. ,  2. , 0.),
            ],
        ];
        
        $data['half ring'] = [
            'shape' => $this->createShape(0, 0, 1, 2, 0, 180),
            'path' => [
                new MoveTo(0, 2),
                new CurveTo(1.1045694996615871, 2, 2, 1.1045694996615871, 2, 0),
                new CurveTo(2, -1.1045694996615871, 1.1045694996615871, -2, 0, -2),
                new LineTo(0, -1),
                new CurveTo(0.55228474983079356, -1, 1, -0.55228474983079356, 1, 0),
                new CurveTo(1, 0.55228474983079356, 0.55228474983079356, 1, 0, 1),
                new Close(0, 2),
            ],
            'anchors' => [
                'si' => new Anchor(0. ,  1. ,  1. ,  0. ),
                'sm' => new Anchor(0. ,  1.5,  1.5,  0. ),
                'so' => new Anchor(0. ,  2. ,  2. ,  0. ),
                'ci' => new Anchor(1. ,  0. ,  0. , -1. ),
                'cm' => new Anchor(1.5,  0. ,  0. , -1.5),
                'co' => new Anchor(2. ,  0. ,  0. , -2. ),
                'ei' => new Anchor(0. , -1. , -1. ,  0. ),
                'em' => new Anchor(0. , -1.5, -1.5,  0. ),
                'eo' => new Anchor(0. , -2. , -2. ,  0. ),
            ],
        ];

        $data['0/8 translated'] = [
            'shape' => $this->createShape(-2, 1, 2, 5, 15, 0),
            'path' => [
                new MoveTo(-0.7059047744873963, 5.8296291314453415),
                new LineTo(-1.4823619097949585, 2.9318516525781364),
                new Close(-0.7059047744873963, 5.8296291314453415),
            ],
            'anchors' => [
                'si' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'sm' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'so' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
                'ci' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'cm' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'co' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
                'ei' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'em' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'eo' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
            ],
        ];
        
        $data['1/8 translated'] = [
            'shape' => $this->createShape(-2, 1, 2, 5, 15, 45),
            'path' => [
                new MoveTo(-0.7059047744873963, 5.8296291314453415),
                new CurveTo(0.57499251098134252, 5.4864137382167755, 1.6670857943233326, 4.6484210885179129, 2.3301270189221928, 3.5000000000000004),
                new LineTo(-0.26794919243112281, 2),
                new CurveTo(-0.53316568227066696, 2.4593684354071654, -0.97000299560746273, 2.7945654952867103, -1.4823619097949585, 2.9318516525781364),
                new Close(-0.7059047744873963, 5.8296291314453415),
            ],
            'anchors' => [
                'si' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'sm' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'so' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
                'ci' => new Anchor(-0.78247714198255869, 2.5867066805824703, 1.5867066805824703, -1.2175228580174413),
                'cm' => new Anchor(0.13066500153052241, 3.7767366910193232, 2.7767366910193232, -2.1306650015305224),
                'co' => new Anchor(1.0438071450436031, 4.9667667014561756, 3.9667667014561756, -3.0438071450436031),
                'ei' => new Anchor(-0.26794919243112281, 2.0, 1.0000000000000002, -1.7320508075688772),
                'em' => new Anchor(1.0310889132455352, 2.7500000000000004, 1.7500000000000004, -3.0310889132455352),
                'eo' => new Anchor(2.3301270189221928, 3.5000000000000004, 2.5000000000000004, -4.3301270189221928),
            ],
        ];
    
        $data['5/8 translated'] = [
            'shape' => $this->createShape(-2, 1, 2, 5, 15, 225),
            'path' => [
                new MoveTo(-0.7059047744873963, 5.8296291314453415),
                new CurveTo(1.4800127953789337, 5.2439142838786559, 2.9999999999999991, 3.2630283924225063, 3.0, 1.0000000000000013),
                new CurveTo(3.0000000000000009, -1.2630283924225036, 1.4800127953789368, -3.2439142838786541, -0.70590477448739275, -3.8296291314453406),
                new CurveTo(-2.8918223443537223, -4.415343979012027, -5.1986128227109383, -3.4598400773233511, -6.3301270189221919, -1.5000000000000022),
                new LineTo(-3.7320508075688767, -8.8817841970012523E-16),
                new CurveTo(-3.2794451290843751, -0.78393603092934017, -2.3567289377414888, -1.1661375916048109, -1.4823619097949572, -0.93185165257813618),
                new CurveTo(-0.60799488184842509, -0.69756571355146146, 4.4408920985006262E-16, 0.094788643030998543, 0.0, 1.0000000000000007),
                new CurveTo(-2.2204460492503131E-16, 1.9052113569690026, -0.60799488184842643, 2.6975657135514624, -1.4823619097949585, 2.9318516525781364),
                new Close(-0.7059047744873963, 5.8296291314453415),
            ],
            'anchors' => [
                'si' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'sm' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'so' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
                'ci' => new Anchor(-0.41329331941752967, -0.21752285801744131, -1.2175228580174413, -1.5867066805824703),
                'cm' => new Anchor(0.77673669101932319, -1.1306650015305224, -2.1306650015305224, -2.7767366910193232),
                'co' => new Anchor(1.9667667014561756, -2.0438071450436031, -3.0438071450436031, -3.9667667014561756),
                'ei' => new Anchor(-3.7320508075688767, -8.8817841970012523E-16, -1.0000000000000009, 1.7320508075688767),
                'em' => new Anchor(-5.0310889132455348, -0.75000000000000155, -1.7500000000000016, 3.0310889132455343),
                'eo' => new Anchor(-6.3301270189221919, -1.5000000000000022, -2.5000000000000022, 4.3301270189221919),
            ],
        ];
        
        $data['8/8 translated'] = [
            'shape' => $this->createShape(-2, 1, 2, 5, 15, 360),
            'path' => [
                new MoveTo(-0.7059047744873963, 5.8296291314453415),
                new CurveTo(1.9614257421484065, 5.1149200735658891, 3.5443381893247938, 2.3732352911231986, 2.8296291314453415, -0.29409522551260414),
                new CurveTo(2.1149200735658891, -2.9614257421484069, -0.62676470887680358, -4.5443381893247947, -3.2940952255126059, -3.8296291314453406),
                new CurveTo(-5.9614257421484087, -3.1149200735658864, -7.5443381893247938, -0.37323529112319664, -6.8296291314453406, 2.2940952255126055),
                new CurveTo(-6.1149200735658873, 4.9614257421484078, -3.3732352911231969, 6.5443381893247938, -0.70590477448739453, 5.8296291314453406),
                new Close(-0.7059047744873963, 5.8296291314453415),
                new MoveTo(-1.4823619097949585, 2.9318516525781364),
                new CurveTo(-2.5492941164492788, 3.2177352757299178, -3.645968029426355, 2.5845702968593631, -3.9318516525781364, 1.5176380902050424),
                new CurveTo(-4.2177352757299182, 0.45070588355072128, -3.5845702968593631, -0.64596802942635501, -2.5176380902050424, -0.9318516525781364),
                new CurveTo(-1.4507058835507214, -1.2177352757299178, -0.35403197057364455, -0.5845702968593629, -0.068148347421863376, 0.4823619097949583),
                new CurveTo(0.2177352757299178, 1.5492941164492795, -0.41542970314063732, 2.6459680294263555, -1.4823619097949585, 2.9318516525781364),
                new Close(-1.4823619097949585, 2.9318516525781364),
            ],
            'anchors' => [
                'si' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'sm' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'so' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
                'ci' => new Anchor(-2.5176380902050406, -0.93185165257813685, -1.9318516525781368, 0.5176380902050407),
                'cm' => new Anchor(-2.9058666578588213, -2.3807403920117394, -3.3807403920117394, 0.90586665785882126),
                'co' => new Anchor(-3.2940952255126019, -3.8296291314453423, -4.8296291314453423, 1.2940952255126017),
                'ei' => new Anchor(-1.4823619097949585, 2.9318516525781364, 1.9318516525781366, -0.51763809020504148),
                'em' => new Anchor(-1.0941333421411774, 4.3807403920117389, 3.3807403920117389, -0.90586665785882259),
                'eo' => new Anchor(-0.7059047744873963, 5.8296291314453415, 4.8296291314453415, -1.2940952255126037),
            ],
        ];
        return $data;
    }
    
    /**
     * @dataProvider getPathProvider
     *
     * @param RingArc $arc
     * @param mixed $ignore
     * @param array[] $anchors
     */
    public function testGetAnchor($arc, $ignore, array $anchors)
    {
        $this->assertEquals($anchors, [
            'si' => $arc->getAnchor(RingArc::ALPHA_START, RingArc::RADIUS_INNER),
            'sm' => $arc->getAnchor(RingArc::ALPHA_START, RingArc::RADIUS_MIDDLE),
            'so' => $arc->getAnchor(RingArc::ALPHA_START, RingArc::RADIUS_OUTER),
            'ci' => $arc->getAnchor(RingArc::ALPHA_CENTRAL, RingArc::RADIUS_INNER),
            'cm' => $arc->getAnchor(RingArc::ALPHA_CENTRAL, RingArc::RADIUS_MIDDLE),
            'co' => $arc->getAnchor(RingArc::ALPHA_CENTRAL, RingArc::RADIUS_OUTER),
            'ei' => $arc->getAnchor(RingArc::ALPHA_END, RingArc::RADIUS_INNER),
            'em' => $arc->getAnchor(RingArc::ALPHA_END, RingArc::RADIUS_MIDDLE),
            'eo' => $arc->getAnchor(RingArc::ALPHA_END, RingArc::RADIUS_OUTER),
        ]);
    }
    
    /**
     * @return array[]
     */
    public function invalidConstructorProvider()
    {
        $data = [];
        $data['inner radius 0'] = [
            'x' => 0,
            'y' => 0,
            'innerradius' => 0,
            'outerradius' => 1,
            'alpha' => 0,
            'angle' => 1,
        ];
        $data['negativ inner radius'] = [
            'x' => 0,
            'y' => 0,
            'innerradius' => 0,
            'outerradius' => 1,
            'alpha' => 0,
            'angle' => 1,
        ];
        $data['outer radius < inner radius'] = [
            'x' => 0,
            'y' => 0,
            'innerradius' => 2,
            'outerradius' => 1,
            'alpha' => 0,
            'angle' => 1,
        ];
        return $data;
    }
    
    /**
     * @param float $x
     * @param float $y
     * @param float $innerRadius
     * @param float $outerRadius
     * @param float $alpha
     * @param float $angle
     *
     * @dataProvider invalidConstructorProvider
     */
    public function testInvalidConstructor($x, $y, $innerRadius, $outerRadius, $alpha, $angle)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->createShape($x, $y, $innerRadius, $outerRadius, $alpha, $angle);
    }
}
