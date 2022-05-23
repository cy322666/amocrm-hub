<?php

namespace App\Services\amoCRM\Actions;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Exceptions\AmoCRMApiErrorResponseException;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\TextCustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use App\Services\amoCRM\Client;
use Illuminate\Database\Eloquent\Model;
use Laravel\Octane\Exceptions\DdException;

class SalesforceHookSender
{
    private Model $hook;
    private AmoCRMApiClient $amoApi;

    public function __construct(Model $model, AmoCRMApiClient $amoApi)
    {
        $this->hook   = $model;
        $this->amoApi = $amoApi;
    }

    public function send() : LeadModel
    {
        $this->hook->contact_id = $this->updateOrCreateContact()->getId();
        $this->hook->lead_id    = $this->createLead()->getId();
        $this->hook->is_send    = true;
        $this->hook->save();
    }

    private function updateOrCreateContact() : ContactModel
    {
        $contact = null;

        if ($this->hook->email !== null) {

            $contact = SearchContact::searchContact($this->amoApi, $this->hook->email);
        }

        if ($contact == null && $this->hook->phone !== null) {

            $contact = SearchContact::searchContact($this->amoApi, $this->hook->phone);
        }

        if ($contact == null) {
            $contact = $this->amoApi
                ->contacts()
                ->addOne((new ContactModel())
                    ->setName($this->hook->name)
                    ->setCustomFieldsValues(
                        new CustomFieldsValuesCollection()
                    )
                );
        }

        $customFields = $contact->getCustomFieldsValues();

        if ($this->hook->phone && empty($customFields->getBy('fieldCode', 'PHONE'))) {

            SearchContact::setPhone($customFields, $this->hook->phone);
        }

        if ($this->hook->email && empty($customFields->getBy('fieldCode', 'EMAIL'))) {

            SearchContact::setEmail($customFields, $this->hook->email);
        }
//        $contact->setCreatedBy(0);
//        $contact->setUpdatedBy(0);
        try {
            return $this->amoApi
                ->contacts()
                ->updateOne($contact);

        } catch (AmoCRMApiErrorResponseException $exception) {

            dd(__METHOD__, $exception->getValidationErrors());
        }
    }

    private function createLead() : LeadModel
    {
        $lead = (new LeadModel())
            ->setName($this->hook->company)
            ->setStatusId($this->hook->status_id)
            ->setPipelineId($this->hook->pipeline_id);

        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        //avito.lead
        $fieldValue = new CheckboxCustomFieldValuesModel();
        $fieldValue->setFieldId(1155203);
        $fieldValue->setValues(
            (new CheckboxCustomFieldValueCollection())
                ->add((new CheckboxCustomFieldValueModel())
                    ->setValue(true))
        );
        $leadCustomFieldsValues->add($fieldValue);

        //avito.position
        $fieldValue = new TextCustomFieldValuesModel();
        $fieldValue->setFieldId(48261);
        $fieldValue->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())
                    ->setValue($this->hook->position))
        );
        $leadCustomFieldsValues->add($fieldValue);

        //avito.manager
        $fieldValue = new TextCustomFieldValuesModel();
        $fieldValue->setFieldId(1155205);
        $fieldValue->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())
                    ->setValue($this->hook->manager))
        );
        $leadCustomFieldsValues->add($fieldValue);

        //salesforce.id
        $fieldValue = new TextCustomFieldValuesModel();
        $fieldValue->setFieldId(1155207);
        $fieldValue->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())
                    ->setValue($this->hook->salesforce_id))
        );
        $leadCustomFieldsValues->add($fieldValue);

        //email.manager
        $fieldValue = new TextCustomFieldValuesModel();
        $fieldValue->setFieldId(1155209);
        $fieldValue->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())
                    ->setValue($this->hook->email_manager))
        );
        $leadCustomFieldsValues->add($fieldValue);

        $lead->setCustomFieldsValues($leadCustomFieldsValues);

        $lead->setTags((new TagsCollection())
            ->add(
                (new TagModel())->setName('Авито')
            ));

        try {
            $lead = $this->amoApi->leads()->addOne($lead);

            $contact = $this
                ->amoApi
                ->contacts()
                ->getOne($this->hook->contact_id);

            $links = (new LinksCollection())->add($contact);

            $this->amoApi->leads()->link($lead, $links);

            return $lead;

        } catch (AmoCRMApiException $exception) {

            dd(__METHOD__, $exception->getMessage());
        }
    }
}
