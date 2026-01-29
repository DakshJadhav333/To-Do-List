<?php

namespace App\Tests\Unit\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class TaskTypeTest extends TypeTestCase
{
    /**
     * We enable the Validator extension so 'constraints' option works.
     * If your entity uses PHP attributes like #[Assert\NotBlank], keep enableAttributeMapping().
     * If you only set constraints in TaskType (not in entity), you can remove enableAttributeMapping().
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping() // keep if you have #[Assert\...] in your Entity
            ->getValidator();

        return [
            new PreloadedExtension([new TaskType()], []),
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'title'    => 'Test Task',
            'priority' => 'HIGH',
            // If your TaskType has other fields, include them here (e.g., 'completed' => true)
        ];

        $model = new Task();

        // Do NOT pass 'csrf_protection' here unless you also register CsrfExtension
        $form = $this->factory->create(TaskType::class, $model);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('Test Task', $model->getTitle());
        $this->assertSame('HIGH', $model->getPriority());

        $view = $form->createView();
        $this->assertArrayHasKey('title', $view->children);
        $this->assertArrayHasKey('priority', $view->children);
    }
}