<?php
declare(strict_types=1);

namespace Tests\App\Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test application creates successfully member and returns it back with id from MailChimp.
     *
     * @return void
     */
    public function testCreateMemberSuccessfully(): void
    { 
        $this->post('/mailchimp/lists', static::$listData);
        
        $list_content = \json_decode($this->response->getContent(), true);
             
        $this->assertResponseOk();
        $this->seeJson(static::$listData);
        self::assertArrayHasKey('mail_chimp_id', $list_content);
        self::assertNotNull($list_content['mail_chimp_id']);
        
        $this->createdListIds[] = $list_content['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
              
        $list_id = $list_content['list_id'];
        
        $this->post(\sprintf('/mailchimp/members/%s',$list_id), static::$MemberData);
       
        $content = \json_decode($this->response->getContent(), true);
        
        $this->assertResponseOk();
        $this->seeJson(static::$MemberData);
        self::assertArrayHasKey('mail_chimp_email_id', $content);
        self::assertNotNull($content['mail_chimp_email_id']);

        $this->createdMemberIds[] = $content['mail_chimp_email_id']; // Store MailChimp Member id for cleaning purposes
        
    }

    /**
     * Test application returns error response with errors when Member validation fails.
     *
     * @return void
     */
    public function testCreateMemberValidationFailed(): void
    {
        $this->post('/mailchimp/lists', static::$listData);
        
        $list_content = \json_decode($this->response->getContent(), true);
        
        $this->assertResponseOk();
        $this->seeJson(static::$listData);
        self::assertArrayHasKey('mail_chimp_id', $list_content);
        self::assertNotNull($list_content['mail_chimp_id']);
        
        $this->createdListIds[] = $list_content['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        
        $list_id = $list_content['list_id'];
        
        $this->post(\sprintf('/mailchimp/members/%s',$list_id));

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('Invalid data given', $content['message']);

        foreach (\array_keys(static::$MemberData) as $key) {
            if (\in_array($key, static::$notRequired, true)) {
                continue;
            }

            self::assertArrayHasKey($key, $content['errors']);
        }
    }

    /**
     * Test application returns error response when Member not found.
     *
     * @return void
     */
    public function testRemoveMemberNotFoundException(): void
    {
        $this->delete('/mailchimp/merbers/invalid-member-id');

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns empty successful response when removing existing member.
     *
     * @return void
     */
    public function testRemoveMemberSuccessfully(): void
    {
        $list_content = \json_decode($this->response->getContent(), true);
        
        $this->assertResponseOk();
        $this->seeJson(static::$listData);
        self::assertArrayHasKey('mail_chimp_id', $list_content);
        self::assertNotNull($list_content['mail_chimp_id']);
        
        $this->createdListIds[] = $list_content['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        
        $list_id = $list_content['list_id'];
        
        $this->post(\sprintf('/mailchimp/members/%s',$list_id), static::$MemberData);
        $member = \json_decode($this->response->content(), true);
        
        $this->delete(\sprintf('/mailchimp/members/%s', $member['member_id']));

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
        
        $this->delete(\sprintf('/mailchimp/lists/%s', $list_id));
        
        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
        
    }

    /**
     * Test application returns error response when Member not found.
     *
     * @return void
     */
    public function testShowMemberNotFoundException(): void
    {
        $this->get('/mailchimp/members/invalid-list-id');

        $this->assertMemberNotFoundResponse('invalid-list-id');
    }

    /**
     * Test application returns successful response with member data when requesting existing member.
     *
     * @return void
     */
    public function testShowMemberSuccessfully(): void
    {
        $member = $this->createMember(static::$MemberData);

        $this->get(\sprintf('/mailchimp/members/%s', $member->getMemberId()));
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (static::$MemberData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }

    /**
     * Test application returns error response when member not found.
     *
     * @return void
     */
    public function testUpdateMemberNotFoundException(): void
    {
        $this->put('/mailchimp/members/invalid-list-id');

        $this->assertMemberNotFoundResponse('invalid-list-id');
    }

    /**
     * Test application returns successfully response when updating existing member with updated values.
     *
     * @return void
     */
    public function testUpdateMemberSuccessfully(): void
    {
        $list_content = \json_decode($this->response->getContent(), true);
        
        $this->assertResponseOk();
        $this->seeJson(static::$listData);
        self::assertArrayHasKey('mail_chimp_id', $list_content);
        self::assertNotNull($list_content['mail_chimp_id']);
        
        $this->createdListIds[] = $list_content['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        
        $list_id = $list_content['list_id'];
        
        $this->post(\sprintf('/mailchimp/members/%s',$list_id), static::$MemberData);
        $member = \json_decode($this->response->content(), true);

        if (isset($member['mail_chimp_email_id'])) {
            $this->createdMemberIds[] = $member['mail_chimp_email_id']; // Store MailChimp Member id for cleaning purposes
        }

        $this->put(\sprintf('/mailchimp/members/%s', $member['member_id']), ['email_type' => 'text']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (\array_keys(static::$MemberData) as $key) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals('updated', $content['email_type']);
        }
    }

    /**
     * Test application returns error response with errors when member validation fails.
     *
     * @return void
     */
    public function testUpdateMemberValidationFailed(): void
    {
        $member = $this->createMember(static::$MemberData);

        $this->put(\sprintf('/mailchimp/members/%s', $member->getMemberId()), ['status' => 'invalid']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertArrayHasKey('status', $content['errors']);
        self::assertEquals('Invalid data given', $content['message']);
    }
}
