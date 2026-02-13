<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function test_uses_professional_email_returns_true()
    {
        $user = new User([
            'email' => 'john@entreprise.com'
        ]);

        $this->assertTrue($user->usesProfessionalEmail());
    }

    public function test_uses_professional_email_returns_false()
    {
        $user = new User([
            'email' => 'john@gmail.com'
        ]);

        $this->assertFalse($user->usesProfessionalEmail());
    }
}
