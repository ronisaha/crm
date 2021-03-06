<?php
namespace Oro\Bundle\ContactBundle\Tests\Unit\Type;

use Oro\Bundle\ContactBundle\Form\Type\ContactSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactSelectTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContactSelectType
     */
    protected $type;

    /**
     * Setup test env
     */
    protected function setUp()
    {
        $this->type = new ContactSelectType();
    }

    public function testConfigureOptions()
    {
        /** @var OptionsResolver $resolver */
        $resolver = $this->createMock('Symfony\Component\OptionsResolver\OptionsResolver');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with($this->isType('array'));
        $this->type->configureOptions($resolver);
    }

    public function testGetParent()
    {
        $this->assertEquals(OroEntitySelectOrCreateInlineType::class, $this->type->getParent());
    }

    public function testGetName()
    {
        $this->assertEquals('oro_contact_select', $this->type->getName());
    }
}
