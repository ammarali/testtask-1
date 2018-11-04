<?php
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\MembersController;
use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test controller returns error response when exception is thrown during create MailChimp request.
     *
     * @return void
     */
    public function testCreateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('post'));

        $this->assertMailChimpExceptionResponse($controller->create($this->getRequest(static::$MemberData), static::$MemberData['list_id']));
    }

    /**
     * Test controller returns error response when exception is thrown during remove MailChimp request.
     *
     * @return void
     */
    public function testRemoveMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('delete'));
        $member = $this->createMember(static::$MemberData);

        // If there is no list id, skip
        if (null === $member->getMemberId()) {
            self::markTestSkipped('Unable to remove, no id provided for Member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->remove($member->getMemberId()));
    }
    
    /**
     * Test controller returns error response when exception is thrown during update MailChimp request.
     *
     * @return void
     */
    public function testUpdateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('patch'));
        $member = $this->createMember(static::$MemberData);

        // If there is no Member id, skip
        if (null === $member->getMemberId()) {
            self::markTestSkipped('Unable to update, no id provided for Member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->update($this->getRequest(static::$MemberData), $member->getMemberId()));
    }
}
