<?php

namespace App\Tests\Form;

use App\Form\UploadFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UploadFormTypeTest extends TypeTestCase {
    public function testSubmitValidData(): void {
        $projectDir = getcwd();
        $formData = [
            'upload_form' => 'not file',
        ];

        $form = $this->factory->create(UploadFormType::class);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
    }

    protected function getExtensions(): array {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
