<?php

namespace A17\Twill\Tests\Unit;

use Illuminate\Support\Facades\Validator;

class ValidationTest extends TestCase
{
    public function mustPass($rule, $value, $field = 'field')
    {
        return $this->validate(true, $rule, $value, $field);
    }

    public function mustFail($rule, $value, $field = 'field')
    {
        return $this->validate(false, $rule, $value, $field);
    }

    public function validate($passes, $rule, $value, $field = 'field')
    {
        $validator = Validator::make(
            [$field => $value],
            [
                $field => $rule,
            ]
        );

        $this->assertTrue($validator->passes() === $passes);

        return $validator;
    }

    public function testCanValidateAbsoluteRelativeUrls()
    {
        $this->mustPass('absolute_or_relative_url', '//relative');
        $this->mustPass('absolute_or_relative_url', 'http://absolute.com/');
        $this->mustPass('absolute_or_relative_url', 'http://abs.co/whatever');

        $this->mustFail('absolute_or_relative_url', 'noturl.com');
        $this->mustFail('absolute_or_relative_url', 'noturl');
    }

    public function testCanValidateAbsoluteSecureUrls()
    {
        $this->mustPass('relative_or_secure_url', 'https://secure.com');
        $this->mustPass('relative_or_secure_url', '//relative.com');

        $this->mustFail('relative_or_secure_url', 'http://insecure.com');
    }

    public function testCanValidateWebColors()
    {
        $this->mustPass('web_color', 'cccccc');
        $this->mustPass('web_color', 'F0F0F0');
        $this->mustPass('web_color', 'bbb');
        $this->mustPass('web_color', 'AaA');
        $this->mustPass('web_color', '0f0');

        $this->mustFail('web_color', '#F0F0F0');
        $this->mustFail('web_color', '#FFF');
        $this->mustFail('web_color', 'F0F0F');
        $this->mustFail('web_color', 'FFFF');
        $this->mustFail('web_color', '00000G');
        $this->mustFail('web_color', '00g');
    }

    public function testCanValidatePhoneNumbers()
    {
        $this->mustPass('phone_number', '+1555-1213');
        $this->mustPass('phone_number', '+800-1213');
        $this->mustPass('phone_number', '+34 12 18 13 14');

        $this->mustFail('phone_number', '+34 12 18 13 14a');
    }

    public function DISABLEDtestCanValidateBlocks()
    {
        $this->mustPass('validBlocks', [
            'text' => [
                'title' => 'Body text',
                'icon' => 'text',
                'component' => 'a17-block-wysiwyg',
            ],

            'image' => [
                'title' => 'Image',
                'icon' => 'image',
                'component' => 'a17-block-image',
            ],

            'complex' => [
                'translated' => true,
                'name' => 'subtitle_translated',
                'label' => 'Subtitle (translated)',
                'maxlength' => 250,
                'required' => true,
                'note' => 'Hint message goes here',
                'placeholder' => 'Placeholder goes here',
                'type' => 'textarea',
                'rows' => 3,
                'browsers' => ['relationName' => 'name'],
            ],
        ]);
    }
}
