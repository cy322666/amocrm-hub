<?php

namespace App\Services\amoCRM\Actions;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use Illuminate\Support\Facades\Log;

/**
 * Статический класс - реализация поиска контакта.
 * Реализуется в стратегиях бизон
 */
abstract class SearchContact
{
    public static function searchContact(AmoCRMApiClient $apiClient, string $search_query): ?ContactModel
    {
        try {
            return $apiClient->contacts()
                ->get(
                    (new ContactsFilter())->setQuery($search_query)
                )->first();

        } catch (AmoCRMApiNoContentException|AmoCRMoAuthApiException|AmoCRMApiException $exception) {

            Log::error(__METHOD__.' : '.$exception->getMessage());
        }
    }

    /**
     * @param $customFields
     * @param $phone
     * @return void
     */
    public static function setPhone(&$customFields, $phone)
    {
        $phoneField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('PHONE');

        $customFields->add($phoneField);

        $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORKDD')
                        ->setValue($phone)
                )
        );
    }

    /**
     * @param $customFields
     * @param $email
     * @return void
     */
    public static function setEmail(&$customFields, $email)
    {
        $emailField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('EMAIL');

        $customFields->add($emailField);

        $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORK')
                        ->setValue($email)
                )
        );
    }
}
