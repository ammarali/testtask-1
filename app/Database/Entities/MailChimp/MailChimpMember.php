<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $member_id;
    
    /**
     * @ORM\Column(name="mail_chimp_email_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpEmailId;
    
    /**
     * 
     * @ORM\Column(name="list_id", type="string")
     * 
     * @var string
     */
    private $listId;

    /**
     * @ORM\Column(name="mail_chimp_list_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpListId;

    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     *
     * @var string
     */
    private $status;

    /**
     * Get Member id.
     *
     * @return null|string
     */
    public function getMemberId(): ?string
    {
        return $this->member_id;
    }
    
    /**
     * Get List id.
     *
     * @return null|string
     */
    public function getListId(): ?string
    {
        return $this->listId;
    }

    /**
     * Get mailchimp List id of the Member.
     *
     * @return null|string
     */
    public function getMailChimpListId(): ?string
    {
        return $this->mailChimpListId;
    }
    
    /**
     * Get mailchimp Email id of the Member.
     *
     * @return null|string
     */
    public function getMailChimpEmailId(): ?string
    {
        return $this->mailChimpEmailId;
    }

    /**
     * Get validation rules for mailchimp member entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|email',
            'status' => 'required|string|in:subscribed,unsubscribed,cleaned,pending',
            'email_type' => 'nullable|string|in:html,text',
            'mailchimp_list_id' => 'nullable|string',
            'mail_chimp_email_id' => 'nullable|string'
        ];
    }


    /**
     * Set mailchimp list id of the Member.
     *
     * @param string $mailChimpListId
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    public function setMailChimpListId(string $mailChimpListId): MailChimpMember
    {
        $this->mailChimpListId = $mailChimpListId;

        return $this;
    }
    
    /**
     * Set mailchimp Email id of the Member.
     *
     * @param string $mailChimpEmailId
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    public function setMailChimpEmailId(string $mailChimpEmailId): MailChimpMember
    {
        $this->mailChimpEmailId = $mailChimpEmailId;
        
        return $this;
    }

    /**
     * Set Email Address.
     *
     * @param string $emailAddress
     *
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress): MailChimpMember
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }
    
    /**
     * Set Email Type.
     *
     * @param string $emailtype
     *
     * @return MailChimpMember
     */
    public function setEmailType(string $emailType): MailChimpMember
    {
        $this->emailType = $emailType;
        
        return $this;
    }

    /**
     * Set Member Status.
     *
     * @param string $status
     *
     * @return MailChimpMember
     */
    public function setstatus(string $status): MailChimpMember
    {
        $this->status = $status;
        
        return $this;
    }
    
    /**
     * Set List ID.
     *
     * @param string $listId
     *
     * @return MailChimpMember
     */
    public function setlistId(string $listId): MailChimpMember
    {
        $this->listId = $listId;
        
        return $this;
    }
    

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }
}
